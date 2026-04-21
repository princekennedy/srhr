<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use App\Models\ContentBlock;
use App\Models\ContentCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

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
        'title',
        'slug',
        'summary',
        'body',
        'content_type',
        'category_id',
        'status',
        'audience',
        'visibility',
        'featured_image_path',
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
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
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
}