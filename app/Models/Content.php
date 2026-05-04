<?php

namespace App\Models;

use App\Enums\CategoryLayoutType;
use App\Enums\ContentLayoutType;
use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use App\Models\ContentBlock;
use App\Support\MediaUrl;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Content extends Model implements HasMedia
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'contents';

    public const TYPE_OPTIONS = ['page', 'article', 'faq', 'quiz', 'service', 'referral'];

    public const STATUS_OPTIONS = ['draft', 'review', 'published', 'archived'];

    public const AUDIENCE_OPTIONS = ['general', 'adolescents', 'youth', 'providers'];

    public const VISIBILITY_OPTIONS = ['public', 'private', 'restricted'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'website_id',
        'category_id',
        'parent_id',
        'menu_id',
        'menu_item_id',
        'title',
        'name',
        'slug',
        'layout_type',
        'summary',
        'description',
        'body',
        'content_type',
        'status',
        'audience',
        'visibility',
        'featured_image_path',
        'sort_order',
        'is_active',
        'published_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        if (static::class !== self::class) {
            return;
        }

        static::addGlobalScope('child-content', fn ($query) => $query->whereNotNull('parent_id'));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->withoutGlobalScope('child-content');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->withoutGlobalScope('child-content')
            ->whereNotNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id')->withoutGlobalScope('root-menu');
    }

    public function scopeCategories(Builder $query): Builder
    {
        return $query->withoutGlobalScope('child-content')->whereNull('parent_id');
    }

    public function scopeEntries(Builder $query): Builder
    {
        return $query->withoutGlobalScope('child-content')->whereNotNull('parent_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(ContentBlock::class)->orderBy('sort_order');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
    }

    public function featuredImageUrl(): ?string
    {
        return MediaUrl::first($this, 'featured_image') ?: MediaUrl::normalize($this->featured_image_path);
    }

    public function attachmentItems(): array
    {
        return $this->getMedia('attachments')
            ->map(fn (Media $media): array => [
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => MediaUrl::fromMedia($media),
            ])
            ->values()
            ->all();
    }

    public function normalizedLayoutType(): string
    {
        if ($this->isCategory()) {
            return CategoryLayoutType::tryFrom((string) $this->layout_type)?->value ?? CategoryLayoutType::Default->value;
        }

        return ContentLayoutType::tryFrom((string) $this->layout_type)?->value ?? ContentLayoutType::Default->value;
    }

    protected static function normalizeSlug(?string $slug, ?string $title, string $fallback = 'content'): string
    {
        $candidate = str((string) ($slug ?: $title))->slug()->value();

        return $candidate !== '' ? $candidate : $fallback;
    }

    public function getCategoryIdAttribute(): ?int
    {
        return isset($this->attributes['parent_id']) ? (int) $this->attributes['parent_id'] : null;
    }

    public function setCategoryIdAttribute(?int $value): void
    {
        $this->attributes['parent_id'] = $value;
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['title'] ?? null;
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['title'] = $value;
    }

    public function getDescriptionAttribute(): ?string
    {
        return $this->attributes['summary'] ?? null;
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['summary'] = $value;
    }

    public function getMenuItemIdAttribute(): ?int
    {
        return isset($this->attributes['menu_id']) ? (int) $this->attributes['menu_id'] : null;
    }

    public function setMenuItemIdAttribute(?int $value): void
    {
        $this->attributes['menu_id'] = $value;
    }

    public function isCategory(): bool
    {
        return $this->parent_id === null;
    }
}