<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    protected static function bootGeneratesUniqueSlug(): void
    {
        static::saving(function ($model): void {
            $slugColumn = $model->getSlugColumn();

            if (filled($model->{$slugColumn})) {
                return;
            }

            $sourceColumn = $model->getSlugSourceColumn();
            $sourceValue = $model->{$sourceColumn} ?: Str::random(8);

            $model->{$slugColumn} = $model->generateUniqueSlugFrom($sourceValue, $slugColumn);
        });
    }

    protected function getSlugColumn(): string
    {
        return 'slug';
    }

    protected function getSlugSourceColumn(): string
    {
        return 'title';
    }

    protected function generateUniqueSlugFrom(string $sourceValue, string $slugColumn): string
    {
        $baseSlug = Str::slug($sourceValue);
        $baseSlug = $baseSlug !== '' ? $baseSlug : Str::lower(Str::random(8));
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->where($slugColumn, $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}