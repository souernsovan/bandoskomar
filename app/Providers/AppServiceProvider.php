<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\User;
use App\Support\PageContent;
use Illuminate\Foundation\Console\ServeCommand;
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
        // `php artisan serve` only passes listed variables to the PHP server, and the check is
        // case-sensitive. PowerShell names it "SystemRoot"; without it Windows cannot open the
        // port ("Failed to listen on 127.0.0.1:9000 (reason: ?)").
        ServeCommand::$passthroughVariables[] = 'SystemRoot';
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

        // Header, footer and shared blocks are edited in the hidden "site" page.
        View::composer('frontend.*', function ($view) {
            $view->with('site', PageContent::site());
        });

        View::composer('frontend.layouts.app', function ($view) {
            $view->with('headerPages', Page::getForMenu());
        });
    }
}
