<?php

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Website extends Model
{
    use GeneratesUniqueSlug;
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function getSlugSourceColumn(): string
    {
        return 'name';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_websites')
            ->withPivot(['role', 'is_owner'])
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(UserWebsite::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ContentCategory::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ServiceCenter::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(AppSetting::class);
    }
}
