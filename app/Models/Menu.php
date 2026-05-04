<?php

namespace App\Models;

use App\Enums\MenuLayoutType;
use App\Enums\MenuItemLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Menu extends Model
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $table = 'menus';

    public const VISIBILITY_OPTIONS = ['public', 'private', 'restricted'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'name',
        'parent_id',
        'title',
        'slug',
        'description',
        'sort_order',
        'layout_type',
        'location',
        'slider_id',
        'target_reference',
        'route',
        'icon',
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

    protected static function booted(): void
    {
        if (static::class !== self::class) {
            return;
        }

        static::addGlobalScope('root-menu', fn ($query) => $query->whereNull('parent_id'));
    }

    protected function getSlugSourceColumn(): string
    {
        return 'title';
    }

    public function items(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->withoutGlobalScope('root-menu')
            ->whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->withoutGlobalScope('root-menu');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->withoutGlobalScope('root-menu')
            ->whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Content::class, 'menu_id')
            ->withoutGlobalScope('child-content')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class, 'slider_id');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->withoutGlobalScope('root-menu')->whereNull('parent_id');
    }

    public function scopeItems(Builder $query): Builder
    {
        return $query->withoutGlobalScope('root-menu')->whereNotNull('parent_id');
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['title'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['title'] = $value;
    }

    public function normalizedLayoutType(): string
    {
        if ($this->isItem()) {
            return MenuItemLayoutType::tryFrom((string) $this->layout_type)?->value ?? MenuItemLayoutType::Default->value;
        }

        return MenuLayoutType::tryFrom((string) $this->layout_type)?->value ?? MenuLayoutType::Default->value;
    }

    protected static function normalizeSlug(?string $slug, ?string $title, string $fallback = 'item'): string
    {
        $candidate = Str::slug((string) ($slug ?: $title));

        return $candidate !== '' ? $candidate : $fallback;
    }

    public static function normalizeForPersistence(array $attributes): array
    {
        $rawLayoutType = trim((string) ($attributes['layout_type'] ?? MenuItemLayoutType::Default->value));
        $layoutType = MenuItemLayoutType::tryFrom($rawLayoutType)?->value ?? MenuItemLayoutType::Default->value;
        $route = static::normalizeNullableString($attributes['route'] ?? null);
        $targetReference = static::normalizeNullableString($attributes['target_reference'] ?? null);
        $openInWebview = static::normalizeBoolean($attributes['open_in_webview'] ?? false);
        $slug = static::normalizeSlug($attributes['slug'] ?? null, $attributes['title'] ?? null, 'menu-item');

        if ($route === null && ! static::isExternalTarget($targetReference)) {
            $route = '/menu-item/'.$slug;
        }

        return [
            ...$attributes,
            'slug' => $slug,
            'layout_type' => $layoutType,
            'route' => $route,
            'target_reference' => $targetReference,
            'open_in_webview' => $openInWebview,
        ];
    }

    public function publicPageSlug(): string
    {
        return static::normalizeSlug($this->slug, $this->title, 'menu-item-'.$this->getKey());
    }

    public function belongsToMenu(self $menu): bool
    {
        $currentParentId = $this->parent_id;

        while ($currentParentId !== null) {
            if ($currentParentId === $menu->getKey()) {
                return true;
            }

            $currentParentId = static::query()
                ->withoutGlobalScopes()
                ->whereKey($currentParentId)
                ->value('parent_id');
        }

        return false;
    }

    public function setMenuIdAttribute(?int $value): void
    {
        if (($this->attributes['parent_id'] ?? null) === null) {
            $this->attributes['parent_id'] = $value;
        }
    }

    public function isItem(): bool
    {
        return $this->parent_id !== null;
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

    private static function isExternalTarget(?string $target): bool
    {
        return filled($target) && Str::startsWith($target, ['http://', 'https://', 'mailto:', 'tel:']);
    }
}