<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_loads(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('A safer front door for SRHR information');
    }

    public function test_guest_can_view_auth_pages(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Log in');
        $this->get(route('register'))->assertOk()->assertSee('Create account');
    }

    public function test_guest_can_access_public_srhr_pages_without_login(): void
    {
        $this->seed(CmsSeeder::class);

        $this->get(route('public.categories.index'))->assertOk()->assertSee('Browse published SRHR topics');
        $this->get(route('public.contents.index'))->assertOk()->assertSee('Published content from the mobile app');
        $this->get(route('public.faqs.index'))->assertOk()->assertSee('Trusted answers to common SRHR questions');
        $this->get(route('public.quizzes.index'))->assertOk()->assertSee('Interactive public quizzes');
        $this->get(route('public.services.index'))->assertOk()->assertSee('Find youth-friendly services and referral points');
    }

    public function test_public_header_uses_admin_managed_menu_items_with_dropdowns(): void
    {
        $this->seed(CmsSeeder::class);

        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();

        $parent = $menu->items()->create([
            'title' => 'Resources',
            'type' => 'internal_route',
            'route' => '/content',
            'sort_order' => 30,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]);

        $menu->items()->create([
            'parent_id' => $parent->id,
            'title' => 'Clinic Directory',
            'type' => 'service_locator',
            'sort_order' => 31,
            'visibility' => 'public',
            'open_in_webview' => false,
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Resources');
        $response->assertSee('Clinic Directory');
    }

    public function test_user_can_register_without_cms_access(): void
    {
        $this->seed(CmsSeeder::class);

        $response = $this->post(route('register'), [
            'name' => 'Platform User',
            'email' => 'user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'user@example.com']);
        $this->assertFalse(auth()->user()?->canAccessCms() ?? true);
    }

    public function test_guest_is_redirected_from_cms_dashboard(): void
    {
        $this->get(route('cms.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_user_can_log_in(): void
    {
        $this->seed(CmsSeeder::class);

        $user = User::factory()->create([
            'email' => 'reviewer@example.com',
            'password' => 'password',
        ]);

        $adminRole = Role::query()->where('name', 'admin')->where('guard_name', 'web')->firstOrFail();
        $user->assignRole($adminRole);

        $response = $this->post(route('login'), [
            'email' => 'reviewer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('cms.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_content_form_uses_ckeditor_for_rich_text_editing(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('cms.contents.create'));

        $response->assertOk();
        $response->assertSee('data-ckeditor-field="content-body"', false);
        $response->assertSee('cdn.ckeditor.com/ckeditor5', false);
        $response->assertSee('ClassicEditor', false);
    }

    public function test_public_content_page_renders_saved_rich_text(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $category = ContentCategory::query()->firstOrFail();

        $content = Content::query()->create([
            'title' => 'Rich Text Public Page',
            'slug' => 'rich-text-public-page',
            'summary' => 'A rich text rendering check.',
            'body' => '<h2>Formatted heading</h2><p><strong>Public body</strong> with <a href="https://example.com">a link</a>.</p>',
            'content_type' => 'page',
            'category_id' => $category->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'published_at' => Carbon::now(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $response = $this->get(route('public.contents.show', $content));

        $response->assertOk();
        $response->assertSee('<h2>Formatted heading</h2>', false);
        $response->assertSee('<strong>Public body</strong>', false);
    }
}