<?php

namespace App\Providers;

use App\Models\Page;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.app', function ($view) {
            $pages = collect();

            if (Schema::hasTable('pages')) {
                $knownRoutes = [
                    'home' => 'home',
                    'about-us' => 'about',
                    'history' => 'history',
                    'our-program' => 'programs',
                    'annual-report' => 'resources.annual-report',
                    'publication' => 'resources.publication',
                    'photo-gallery' => 'resources.photo-gallery',
                    'video-center' => 'resources.video-center',
                    'support-us' => 'get-involved.support-us',
                    'sponsor-child' => 'get-involved.sponsor-child',
                    'ways-to-give' => 'get-involved.ways-to-give',
                    'career' => 'get-involved.career',
                    'donate' => 'donate',
                    'contact' => 'contact',
                ];

                $pages = Page::query()
                    ->where('status', 'published')
                    ->orderBy('id')
                    ->get()
                    ->map(function (Page $page) use ($knownRoutes) {
                        $routeName = $knownRoutes[$page->slug] ?? null;
                        $url = $routeName ? route($routeName) : route('pages.custom', $page);

                        return [
                            'title' => $page->title,
                            'slug' => $page->slug,
                            'category' => $page->page_category ?? 'main',
                            'url' => $url,
                            'active' => request()->url() === $url,
                        ];
                    });
            }

            $view->with([
                'frontendNavPages' => $pages->groupBy('category'),
                'frontendMobilePages' => $pages,
                'frontendDonatePage' => $pages->firstWhere('category', 'donation'),
            ]);
        });
    }
}
