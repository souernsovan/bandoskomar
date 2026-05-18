<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Route;

class Page extends Model
{
    use HasFactory, HasUuids;

    public const MENU_GROUP_LABELS = [
        'main' => 'Main page',
        'resources' => 'Info & Resources dropdown',
        'involved' => 'Get Involved dropdown',
        'more' => 'More pages dropdown',
        'hidden' => 'Hidden',
    ];

    protected $table = 'pages';

    protected $fillable = [
        'slug',
        'title',
        'content',
        'translations',
        'page_content',
        'route_name',
        'menu_group',
        'is_active',
        'meta_title',
        'meta_description',
        'og_tags',
        'canonical_url',
        'structured_data',
        'sitemap_include',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sitemap_include' => 'boolean',
        'og_tags' => 'array',
        'structured_data' => 'array',
        'translations' => 'array',
        'page_content' => 'array',
    ];

    /**
     * Get title for locale (with fallback to en, then legacy title column).
     */
    public function getTitleForLocale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $locale = \App\Support\PageLocales::isValid($locale) ? $locale : 'en';

        $translations = $this->translations ?? [];
        if (isset($translations[$locale]['title']) && $translations[$locale]['title'] !== '') {
            return $translations[$locale]['title'];
        }
        if (isset($translations['en']['title']) && $translations['en']['title'] !== '') {
            return $translations['en']['title'];
        }
        return $this->title ?? '';
    }

    /**
     * Get content for locale (with fallback).
     */
    public function getContentForLocale(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $locale = \App\Support\PageLocales::isValid($locale) ? $locale : 'en';

        $translations = $this->translations ?? [];
        if (isset($translations[$locale]['content']) && $translations[$locale]['content'] !== '') {
            return $translations[$locale]['content'];
        }
        if (isset($translations['en']['content']) && $translations['en']['content'] !== '') {
            return $translations['en']['content'];
        }
        return $this->content;
    }

    /**
     * Get page_content for locale. Structure: page_content = { en: {...}, km: {...}, ... }.
     * Legacy: flat structure is treated as 'en'.
     */
    public function getPageContentForLocale(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $locale = \App\Support\PageLocales::isValid($locale) ? $locale : 'en';

        $pc = $this->page_content ?? [];
        $locales = \App\Support\PageLocales::all();

        if (isset($pc[$locale]) && is_array($pc[$locale])) {
            return $pc[$locale];
        }
        if (isset($pc['en']) && is_array($pc['en'])) {
            return $pc['en'];
        }
        foreach ($locales as $l) {
            if (isset($pc[$l]) && is_array($pc[$l])) {
                return $pc[$l];
            }
        }
        return is_array($pc) && !$this->isPageContentLocaleKeyed($pc) ? $pc : [];
    }

    /**
     * Get the configured navigation group for the page.
     * Falls back to the legacy slug-based grouping so existing pages keep their placement.
     */
    public function getMenuGroup(): string
    {
        $group = is_string($this->menu_group ?? null) ? trim($this->menu_group) : '';
        if ($group !== '') {
            return $group;
        }

        return match ($this->slug) {
            'home', 'platform', 'about-us', 'product', 'history' => 'main',
            'jobs-announcement', 'annual-report', 'strategic-plan', 'partner' => 'resources',
            'volunteer', 'image-gallery', 'video' => 'involved',
            'image' => 'hidden',
            'contact', 'donate' => 'hidden',
            default => 'more',
        };
    }

    /**
     * Human-friendly label for the navigation group.
     */
    public function getMenuGroupLabel(): string
    {
        return self::MENU_GROUP_LABELS[$this->getMenuGroup()] ?? ucfirst($this->getMenuGroup());
    }

    /**
     * Determine whether the shared frontend banner should be rendered.
     */
    public function shouldShowBanner(): bool
    {
        return in_array($this->slug, [
            'platform',
            'about-us',
            'product',
            'history',
            'contact',
            'donate',
            'partner',
            'volunteer',
            'image-gallery',
            'video-stories',
            'annual-report',
            'strategic-plan',
            'jobs-announcement',
        ], true);
    }

    private function isPageContentLocaleKeyed(array $pc): bool
    {
        $locales = \App\Support\PageLocales::all();
        foreach (array_keys($pc) as $key) {
            if (in_array($key, $locales, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the "page type" for special pages (home, platform, about-us, product, contact).
     * Used so that changing the slug does not change which edit form or save logic is used.
     * Prefer slug; if slug was changed, infer from page_content structure.
     */
    public function getPageType(): ?string
    {
        $special = ['home', 'platform', 'about-us', 'product', 'contact'];
        if (in_array($this->slug, $special, true)) {
            return $this->slug;
        }
        $pc = $this->page_content ?? [];
        $first = null;
        if (isset($pc['en']) && is_array($pc['en'])) {
            $first = $pc['en'];
        } else {
            foreach ($pc as $v) {
                if (is_array($v)) {
                    $first = $v;
                    break;
                }
            }
        }
        if (!$first) {
            return null;
        }
        if (array_key_exists('hero_headline', $first)) {
            return 'home';
        }
        if (array_key_exists('features', $first) && is_array($first['features'])) {
            return 'platform';
        }
        if (array_key_exists('solution_cards', $first)) {
            return 'about-us';
        }
        if (array_key_exists('products_title', $first) || array_key_exists('description', $first)) {
            return 'product';
        }
        if (
            array_key_exists('contact_methods', $first)
            || array_key_exists('contact_info_title', $first)
            || array_key_exists('confirm_open', $first)
            || array_key_exists('page_intro', $first)
            || array_key_exists('form_title', $first)
            || array_key_exists('target_email', $first)
            || array_key_exists('address', $first)
        ) {
            return 'contact';
        }
        return null;
    }

    /**
     * Get page by slug.
     */
    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Get the page used as the frontend homepage.
     * Prefer slug "home"; if missing (e.g. slug was changed), use first active page with home-type content.
     */
    public static function getHomePage(): ?self
    {
        $page = static::getBySlug('home');
        if ($page !== null) {
            return $page;
        }
        /** @var \Illuminate\Database\Eloquent\Collection<int, static> $pages */
        $pages = static::where('is_active', true)->orderBy('sort_order')->get();
        foreach ($pages as $p) {
            if ($p->getPageType() === 'home') {
                return $p;
            }
        }
        return null;
    }

    /**
     * Get pages for sitemap.
     */
    public static function getForSitemap()
    {
        return static::where('is_active', true)
            ->where('sitemap_include', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get pages for header navigation menu.
     */
    public static function getForMenu()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the URL for this page.
     * Honor an explicit frontend route mapping first, then fall back to page type/slug.
     */
    public function getUrlAttribute(): string
    {
        $routeName = is_string($this->route_name ?? null) ? trim($this->route_name) : '';

        if ($routeName !== '') {
            try {
                if ($routeName === 'frontend.page') {
                    return route($routeName, ['slug' => $this->slug]);
                }

                if (Route::has($routeName)) {
                    return route($routeName);
                }
            } catch (\Throwable $e) {
                // Fall back to the legacy page-type routing below.
            }
        }

        $type = $this->getPageType() ?? $this->slug;
        return match ($type) {
            'home' => route('frontend.home'),
            'platform' => route('frontend.platform'),
            'history' => route('frontend.history'),
            'product' => route('frontend.product'),
            'about-us' => route('frontend.about-us'),
            'contact' => route('frontend.contact'),
            'donate' => route('frontend.donate'),
            default => route('frontend.page', ['slug' => $this->slug]),
        };
    }
}
 
