<?php

namespace Tests\Feature;

use App\Models\ContentCategory;
use App\Models\Menu;
use App\Models\User;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsManagementTest extends TestCase
{
    use RefreshDatabase;

    private function signIn(): User
    {
        $user = User::factory()->create();
        $permissions = [
            'cms.access',
            'cms.manage.categories',
            'cms.manage.contents',
            'cms.manage.faqs',
            'cms.manage.quizzes',
            'cms.manage.services',
            'cms.manage.menus',
            'cms.manage.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions($permissions);

        $user->assignRole($adminRole);

        $this->actingAs($user);

        return $user;
    }

    public function test_cms_dashboard_loads(): void
    {
        $this->signIn();

        $response = $this->get(route('cms.dashboard'));

        $response->assertOk();
        $response->assertSee('Delivery dashboard');
    }

    public function test_all_cms_module_pages_load(): void
    {
        $this->signIn();

        $this->get(route('cms.categories.index'))->assertOk();
        $this->get(route('cms.contents.index'))->assertOk();
        $this->get(route('cms.faqs.index'))->assertOk();
        $this->get(route('cms.quizzes.index'))->assertOk();
        $this->get(route('cms.services.index'))->assertOk();
        $this->get(route('cms.menus.index'))->assertOk();
        $this->get(route('cms.settings.index'))->assertOk();
    }

    public function test_can_create_category_and_content_entries(): void
    {
        $this->signIn();

        $this->post(route('cms.categories.store'), [
            'name' => 'Adolescent Health',
            'description' => 'Content focused on adolescent SRHR topics.',
            'sort_order' => 1,
            'is_active' => 1,
        ])->assertRedirect(route('cms.categories.index'));

        $category = ContentCategory::query()->firstOrFail();

        $this->assertDatabaseHas('content_categories', [
            'name' => 'Adolescent Health',
            'slug' => 'adolescent-health',
        ]);

        $this->post(route('cms.contents.store'), [
            'title' => 'Understanding consent',
            'summary' => 'Introductory consent guidance for young people.',
            'body' => 'Consent is clear, informed, and ongoing.',
            'content_type' => 'page',
            'category_id' => $category->id,
            'status' => 'published',
            'audience' => 'youth',
            'visibility' => 'public',
        ])->assertRedirect(route('cms.contents.index'));

        $this->assertDatabaseHas('contents', [
            'title' => 'Understanding consent',
            'slug' => 'understanding-consent',
            'category_id' => $category->id,
            'status' => 'published',
        ]);
    }

    public function test_can_create_menu_and_menu_item(): void
    {
        $this->signIn();

        $this->post(route('cms.menus.store'), [
            'name' => 'Home Navigation',
            'location' => 'home-primary',
            'description' => 'Main mobile navigation menu.',
            'is_active' => 1,
        ])->assertRedirect();

        $menu = Menu::query()->firstOrFail();

        $this->assertDatabaseHas('menus', [
            'name' => 'Home Navigation',
            'slug' => 'home-navigation',
        ]);

        $this->post(route('cms.menus.items.store', $menu), [
            'title' => 'Get Help Now',
            'type' => 'internal_route',
            'route' => '/help-now',
            'sort_order' => 1,
            'visibility' => 'public',
            'is_active' => 1,
        ])->assertRedirect(route('cms.menus.edit', $menu));

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'title' => 'Get Help Now',
            'type' => 'internal_route',
            'route' => '/help-now',
        ]);
    }

    public function test_regular_user_cannot_access_cms(): void
    {
        $this->seed(CmsSeeder::class);

        $user = User::query()->where('email', 'user@srhr.test')->firstOrFail();
        $this->actingAs($user);

        $this->get(route('cms.dashboard'))->assertRedirect(route('home'));
        $this->get(route('cms.contents.index'))->assertRedirect(route('home'));
        $this->get(route('cms.contents.create'))->assertRedirect(route('home'));
        $this->post(route('cms.contents.store'), [
            'title' => 'Blocked content',
            'summary' => 'Blocked summary',
            'body' => 'Blocked body',
            'content_type' => 'page',
            'status' => 'draft',
            'audience' => 'general',
            'visibility' => 'public',
        ])->assertRedirect(route('home'));
        $this->get(route('cms.menus.create'))->assertRedirect(route('home'));
    }
}