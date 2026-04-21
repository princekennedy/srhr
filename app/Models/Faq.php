<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $fillable = [
        'website_id',
        'question',
        'slug',
        'answer',
        'category_id',
        'audience',
        'visibility',
        'sort_order',
        'is_published',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    protected function getSlugSourceColumn(): string
    {
        return 'question';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
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