<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWebsite;
use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCenter extends Model
{
    use BelongsToWebsite;
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $fillable = [
        'website_id',
        'name',
        'slug',
        'category_id',
        'district',
        'physical_address',
        'contact_phone',
        'contact_email',
        'service_hours',
        'summary',
        'services',
        'audience',
        'visibility',
        'is_featured',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected function getSlugSourceColumn(): string
    {
        return 'name';
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