<?php

namespace App\Models\Concerns;

use App\Models\Website;
use App\Support\CurrentWebsite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

trait BelongsToWebsite
{
    public static function bootBelongsToWebsite(): void
    {
        static::creating(function ($model): void {
            if (filled($model->website_id)) {
                return;
            }

            $websiteId = app(CurrentWebsite::class)->id();

            if ($websiteId !== null) {
                $model->website_id = $websiteId;
            }
        });

        static::addGlobalScope('website', function (Builder $builder): void {
            $model = $builder->getModel();
            $table = $model->getTable();

            if (! Schema::hasColumn($table, 'website_id')) {
                return;
            }

            $websiteId = app(CurrentWebsite::class)->id();

            if ($websiteId !== null) {
                $builder->where($table.'.website_id', $websiteId);
            }
        });
    }

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
