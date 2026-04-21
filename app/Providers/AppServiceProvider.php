<?php

namespace App\Providers;

use App\Support\PublicNavigation;
use App\Support\PublicSiteConfig;
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
            $navigation = app(PublicNavigation::class);
            $siteConfig = app(PublicSiteConfig::class)->data();

            $view->with([
                'siteMenus' => $navigation->menus(),
                'siteNavigation' => $navigation->items(),
                'siteMiniBarLinks' => $navigation->quickLinks(),
                'publicSite' => $siteConfig,
            ]);
        });
    }
}
