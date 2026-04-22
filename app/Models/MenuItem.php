<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWebsite;
use App\Models\ContentCategory;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use BelongsToWebsite;
    use HasFactory;

    public const TYPE_OPTIONS = [
        'content',
        'category',
        'external_url',
        'internal_route',
        'quiz',
        'faq',
        'service_locator',
        'webview_page',
    ];

    public const VISIBILITY_OPTIONS = ['public', 'private', 'restricted'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'menu_id',
        'parent_id',
        'title',
        'type',
        'target_reference',
        'route',
        'icon',
        'sort_order',
        'visibility',
        'open_in_webview',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'open_in_webview' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ContentCategory::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public static function normalizeForPersistence(array $attributes): array
    {
        $type = trim((string) ($attributes['type'] ?? ''));
        $route = static::normalizeNullableString($attributes['route'] ?? null);
        $targetReference = static::normalizeNullableString($attributes['target_reference'] ?? null);
        $openInWebview = static::normalizeBoolean($attributes['open_in_webview'] ?? false);

        if ($route === null && $type !== 'external_url') {
            $title = $attributes['title'] ?? '';
            $slug = Str::slug($title);
            $menuItemName = $slug !== '' ? $slug : 'item';
            $route = '/menu-item/' . $menuItemName;
        }

        if (static::shouldUseWebviewPageType($type, $route, $targetReference, $openInWebview)) {
            $type = 'webview_page';
            $openInWebview = true;
        }

        return [
            ...$attributes,
            'type' => $type,
            'route' => $route,
            'target_reference' => $targetReference,
            'open_in_webview' => $openInWebview,
        ];
    }

    public function publicPageSlug(): string
    {
        $slug = Str::slug($this->title);

        return $slug !== '' ? $slug : 'menu-item-'.$this->getKey();
    }

    private static function shouldUseWebviewPageType(string $type, ?string $route, ?string $targetReference, bool $openInWebview): bool
    {
        if ($type === 'webview_page') {
            return true;
        }

        if ($type !== 'internal_route') {
            return false;
        }

        if ($route !== null && preg_match('#^/?menu-pages(?:/|$)#', $route) === 1) {
            return true;
        }

        if ($route !== null && preg_match('#^/?menu-item(?:/|$)#', $route) === 1) {
            return true;
        }

        return $openInWebview && ($route === null || $targetReference !== null);
    }

    private static function normalizeNullableString(mixed $value): ?string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private static function normalizeBoolean(mixed $value): bool
    {
        return match (true) {
            is_bool($value) => $value,
            is_int($value) => $value === 1,
            default => in_array(Str::lower(trim((string) $value)), ['1', 'true', 'on', 'yes'], true),
        };
    }
}