<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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
        SymfonyRequest::setTrustedProxies(
            ['*'], // Trust all proxies
            SymfonyRequest::HEADER_X_FORWARDED_FOR |
            SymfonyRequest::HEADER_X_FORWARDED_HOST |
            SymfonyRequest::HEADER_X_FORWARDED_PORT |
            SymfonyRequest::HEADER_X_FORWARDED_PROTO
        );

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::before(function (?User $user, string $ability) {
            if ($user && $user->isSystem()) {
                return true;
            }
        });

        View::composer('frontend.layouts.app', function ($view) {
            $view->with([
                'headerPages' => Page::getForMenu(),
                'productCategories' => Category::where('status', 'active')->orderBy('name')->get(),
                'siteName' => \App\Models\SiteSetting::get('site_name', config('app.name')),
                'siteLogoPath' => \App\Models\SiteSetting::siteLogoPath(),
                'siteIconPath' => \App\Models\SiteSetting::siteIconPath(),
                'siteIconMimeType' => \App\Models\SiteSetting::siteIconMimeType(),
            ]);
        });
    }
}
