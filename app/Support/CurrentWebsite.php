<?php

namespace App\Support;

use App\Models\Website;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CurrentWebsite
{
    public function website(): ?Website
    {
        if (! Schema::hasTable('websites') || ! Schema::hasTable('users')) {
            return null;
        }

        $user = Auth::user();

        if ($user === null) {
            return $this->resolvePublicWebsite();
        }

        $currentWebsite = $user->currentWebsite;

        if ($currentWebsite !== null) {
            return $currentWebsite;
        }

        $fallback = $user->websites()->where('is_active', true)->orderBy('name')->first();

        if ($fallback !== null) {
            $user->switchToWebsite($fallback);
        }

        return $fallback;
    }

    public function id(): ?int
    {
        return $this->website()?->getKey();
    }

    private function resolvePublicWebsite(): ?Website
    {
        $host = Str::lower((string) Request::getHost());

        $website = null;

        if ($host !== '') {
            $website = Website::query()
                ->where('is_active', true)
                ->where('domain', $host)
                ->first();

            if ($website === null) {
                $website = Website::query()
                    ->where('is_active', true)
                    ->get()
                    ->first(function (Website $candidate) use ($host): bool {
                        $slug = Str::lower((string) $candidate->slug);

                        if ($slug === '') {
                            return false;
                        }

                        return preg_match('/(^|[.-])'.preg_quote($slug, '/').'($|[.-])/', $host) === 1;
                    });
            }
        }

        if ($website === null) {
            $website = Website::query()->whereKey(1)->where('is_active', true)->first();
        }

        return $website ?? Website::query()->where('is_active', true)->orderBy('id')->first();
    }
}
