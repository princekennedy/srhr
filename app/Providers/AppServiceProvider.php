<?php

namespace App\Providers;

use App\Support\PublicNavigation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.site', function ($view): void {
            $view->with('siteNavigation', app(PublicNavigation::class)->items());
        });
    }
}
