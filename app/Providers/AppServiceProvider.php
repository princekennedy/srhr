<?php

namespace App\Providers;

use App\Support\PublicNavigation;
use App\Support\PublicSiteConfig;
use Throwable;
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
            try {
                $navigation = app(PublicNavigation::class);
                $siteConfig = app(PublicSiteConfig::class)->data();

                $payload = [
                    'siteMenus' => $navigation->menus(),
                    'siteNavigation' => $navigation->items(),
                    'siteMiniBarLinks' => $navigation->quickLinks(),
                    'publicSite' => $siteConfig,
                ];
            } catch (Throwable $exception) {
                report($exception);

                $payload = [
                    'siteMenus' => collect(),
                    'siteNavigation' => collect(),
                    'siteMiniBarLinks' => collect(),
                    'publicSite' => [
                        'brand' => [
                            'name' => config('app.name', 'SRHR Connect'),
                            'kicker' => 'SRHR Platform',
                            'message' => 'Trusted SRHR guidance and support access in one place.',
                            'strapline' => 'Trusted SRHR guidance and support access in one place.',
                        ],
                        'homepage' => ['slides' => []],
                        'support' => [
                            'phone' => null,
                            'phone_href' => null,
                            'email' => null,
                            'email_href' => null,
                        ],
                        'theme' => [
                            'accent' => '#009bde',
                            'accent_strong' => '#007fb6',
                            'accent_soft' => 'rgba(0, 155, 222, 0.12)',
                            'warm' => '#00658f',
                            'warm_soft' => 'rgba(0, 101, 143, 0.12)',
                            'default_mode' => 'light',
                        ],
                        'features' => [
                            'registration_required' => false,
                        ],
                    ],
                ];
            }

            $view->with($payload);
        });
    }
}
