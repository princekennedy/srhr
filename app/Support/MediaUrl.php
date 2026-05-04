<?php

namespace App\Support;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaUrl
{
    public static function first(HasMedia $model, string $collection): ?string
    {
        return static::fromMedia($model->getFirstMedia($collection));
    }

    public static function fromMedia(?Media $media): ?string
    {
        return $media === null ? null : static::normalize($media->getUrl());
    }

    public static function normalize(?string $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $trimmed = trim($url);

        if ($trimmed === '') {
            return null;
        }

        if (str_starts_with($trimmed, '/storage/')) {
            return $trimmed;
        }

        $parts = parse_url($trimmed);
        $path = $parts['path'] ?? null;

        if (! is_string($path) || ! str_starts_with($path, '/storage/')) {
            return $trimmed;
        }

        $query = isset($parts['query']) ? '?'.$parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';

        return $path.$query.$fragment;
    }
}