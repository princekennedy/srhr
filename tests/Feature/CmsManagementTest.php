<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Content;
use App\Models\Menu;
use App\Models\Slider;
use App\Models\User;
use App\Models\Website;
use Database\Seeders\CmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            'cms.manage.sliders',
            'cms.manage.menus',
            'cms.manage.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions($permissions);

        $user->assignRole($adminRole);
        $website = Website::query()->create([
            'name' => 'Test Website',
            'slug' => 'test-website',
            'is_active' => true,
            'created_by' => $user->id,
        ]);
        $user->websites()->attach($website, [
            'role' => 'owner',
            'is_owner' => true,
        ]);
        $user->forceFill(['current_website_id' => $website->id])->save();

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

        $this->get(route('cms.websites.index'))->assertOk();
        $this->get(route('cms.contents.index'))->assertOk();
        $this->get(route('cms.sliders.index'))->assertOk();
        $this->get(route('cms.menus.index'))->assertOk();
        $this->get(route('cms.settings.index'))->assertOk();
    }

    public function test_can_create_category_and_content_entries(): void
    {
        $this->signIn();

        $this->post(route('cms.contents.store', ['kind' => 'category']), [
            'name' => 'Adolescent Health',
            'description' => 'Content focused on adolescent SRHR topics.',
            'layout_type' => 'default',
            'sort_order' => 1,
            'visibility' => 'public',
            'is_active' => 1,
        ])->assertRedirect(route('cms.contents.index'));

        $category = Content::query()->categories()->firstOrFail();

        $this->assertDatabaseHas('contents', [
            'title' => 'Adolescent Health',
            'slug' => 'adolescent-health',
            'parent_id' => null,
            'content_type' => 'category',
        ]);

        $this->post(route('cms.contents.store'), [
            'title' => 'Understanding consent',
            'summary' => 'Introductory consent guidance for young people.',
            'body' => 'Consent is clear, informed, and ongoing.',
            'layout_type' => 'default',
            'content_type' => 'page',
            'category_id' => $category->id,
            'status' => 'published',
            'audience' => 'youth',
            'visibility' => 'public',
        ])->assertRedirect(route('cms.contents.index'));

        $this->assertDatabaseHas('contents', [
            'title' => 'Understanding consent',
            'slug' => 'understanding-consent',
            'parent_id' => $category->id,
            'status' => 'published',
        ]);
    }

    public function test_category_layout_preview_renders_with_paginated_contents(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $website = Website::query()->firstOrFail();
        $category = Content::query()->categories()->firstOrFail();

        Content::query()->create([
            'website_id' => $website->id,
            'title' => 'Preview Category Content',
            'slug' => 'preview-category-content',
            'summary' => 'Content created for category layout preview regression coverage.',
            'body' => 'Preview body content.',
            'content_type' => 'article',
            'parent_id' => $category->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'published_at' => now(),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('cms.layout-preview', [
            'section' => 'categories',
            'layout' => 'minimal',
            'category_id' => $category->id,
        ]));

        $response->assertOk();
        $response->assertSee('Preview Category Content');
    }

    public function test_content_featured_image_is_stored_in_spatie_with_relative_storage_url(): void
    {
        $this->signIn();

        $this->post(route('cms.contents.store', ['kind' => 'category']), [
            'name' => 'Media Category',
            'description' => 'Category for media upload coverage.',
            'layout_type' => 'default',
            'sort_order' => 1,
            'visibility' => 'public',
            'is_active' => 1,
        ])->assertRedirect(route('cms.contents.index'));

        $category = Content::query()->categories()->firstOrFail();

        $this->post(route('cms.contents.store'), [
            'title' => 'Content With Featured Image',
            'summary' => 'Summary',
            'body' => 'Body',
            'layout_type' => 'default',
            'content_type' => 'page',
            'category_id' => $category->id,
            'status' => 'published',
            'audience' => 'general',
            'visibility' => 'public',
            'featured_image_upload' => UploadedFile::fake()->image('featured.jpg', 1200, 800),
        ])->assertRedirect(route('cms.contents.index'));

        $content = Content::query()->where('title', 'Content With Featured Image')->firstOrFail();

        $this->assertNotNull($content->getFirstMedia('featured_image'));
        $this->assertNull($content->featured_image_path);
        $this->assertStringStartsWith('/storage/', (string) $content->featuredImageUrl());
    }

    public function test_can_create_menu_and_menu_item(): void
    {
        $this->signIn();

        $this->post(route('cms.menus.store'), [
            'name' => 'Home Navigation',
            'layout_type' => 'default',
            'location' => 'home-primary',
            'description' => 'Main mobile navigation menu.',
            'visibility' => 'public',
            'is_active' => 1,
        ])->assertRedirect();

        $menu = Menu::query()->firstOrFail();

        $this->assertDatabaseHas('menus', [
            'title' => 'Home Navigation',
            'slug' => 'home-navigation',
            'parent_id' => null,
        ]);

        $this->post(route('cms.menus.items.store', $menu), [
            'title' => 'Get Help Now',
            'layout_type' => 'default',
            'route' => '/help-now',
            'sort_order' => 1,
            'visibility' => 'public',
            'is_active' => 1,
        ])->assertRedirect(route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children']));

        $this->assertDatabaseHas('menus', [
            'parent_id' => $menu->id,
            'title' => 'Get Help Now',
            'layout_type' => 'default',
            'route' => '/help-now',
        ]);
    }

    public function test_menu_item_save_generates_dynamic_route_when_missing(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $this->actingAs($admin);

        $menu = Menu::query()->where('location', 'public-primary')->firstOrFail();

        $this->post(route('cms.menus.items.store', $menu), [
            'title' => 'Ask an Expert',
            'layout_type' => 'default',
            'target_reference' => 'content:12',
            'sort_order' => 1,
            'visibility' => 'public',
            'open_in_webview' => 1,
            'is_active' => 1,
        ])->assertRedirect(route('cms.menus.edit', ['menu' => $menu, 'tab' => 'children']));

        $this->assertDatabaseHas('menus', [
            'parent_id' => $menu->id,
            'title' => 'Ask an Expert',
            'layout_type' => 'default',
            'target_reference' => 'content:12',
            'route' => '/menu-item/ask-an-expert',
            'open_in_webview' => true,
        ]);
    }

    public function test_admin_can_create_slider_entry(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $this->actingAs($admin);

        $this->post(route('cms.sliders.store'), [
            'title' => 'Trusted support when you need it most',
            'kicker' => 'Always available',
            'layout_type' => 'default',
            'caption' => 'Give users a clear path to services and reliable information from the first screen.',
            'primary_button_text' => 'Find Support',
            'primary_button_link' => '#support',
            'secondary_button_text' => 'Learn More',
            'secondary_button_link' => '#features',
            'sort_order' => 9,
            'is_active' => 1,
            'image_upload' => UploadedFile::fake()->image('slide.jpg', 1600, 900),
        ])->assertRedirect(route('cms.sliders.index'));

        $this->assertDatabaseHas('sliders', [
            'title' => 'Trusted support when you need it most',
            'kicker' => 'Always available',
            'sort_order' => 9,
            'is_active' => true,
        ]);

        $slider = Slider::query()->where('title', 'Trusted support when you need it most')->firstOrFail();

        $this->assertNotNull($slider->getFirstMedia('slide_image'));
        $this->assertStringStartsWith('/storage/', (string) $slider->imageUrl());
    }

    public function test_slider_item_image_is_stored_in_spatie_with_relative_storage_url(): void
    {
        $this->seed(CmsSeeder::class);

        $admin = User::query()->where('email', 'admin@srhr.test')->firstOrFail();
        $this->actingAs($admin);

        $this->post(route('cms.sliders.store'), [
            'title' => 'Slider With Item Image',
            'kicker' => 'Items',
            'layout_type' => 'default',
            'caption' => 'Root caption.',
            'sort_order' => 10,
            'is_active' => 1,
            'items' => [[
                'title' => 'First Item',
                'caption' => 'Item caption',
                'sort_order' => 0,
                'is_active' => 1,
                'image_upload' => UploadedFile::fake()->image('item.jpg', 1200, 800),
            ]],
        ])->assertRedirect(route('cms.sliders.index'));

        $slider = Slider::query()->where('title', 'Slider With Item Image')->firstOrFail();
        $item = $slider->items()->firstOrFail();

        $this->assertNotNull($item->getFirstMedia('slide_image'));
        $this->assertStringStartsWith('/storage/', (string) $item->imageUrl());
    }

    public function test_uploaded_settings_use_spatie_media_and_relative_storage_urls(): void
    {
        $user = $this->signIn();
        $website = Website::query()->where('created_by', $user->id)->firstOrFail();

        $setting = AppSetting::query()->create([
            'website_id' => $website->id,
            'key' => 'hero_slide_1_image',
            'label' => 'Hero slide 1 image',
            'value' => null,
            'group' => 'branding',
            'input_type' => 'upload',
            'description' => 'Hero image',
            'is_public' => true,
        ]);

        $this->put(route('cms.settings.update'), [
            'settings' => [
                'hero_slide_1_image' => null,
            ],
            'setting_uploads' => [
                'hero_slide_1_image' => UploadedFile::fake()->image('hero-setting.jpg', 1600, 900),
            ],
        ])->assertRedirect(route('cms.settings.index'));

        $setting->refresh();

        $this->assertNotNull($setting->getFirstMedia('setting_asset'));
        $this->assertNull($setting->value);
        $this->assertStringStartsWith('/storage/', (string) $setting->settingAssetUrl());
        $this->assertSame($setting->settingAssetUrl(), $setting->resolvedValue());
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