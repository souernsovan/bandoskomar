<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Page;
use App\Services\AuditLogService;
use App\Support\PageLocales;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('route_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = in_array($request->input('per_page', 25), [25, 50, 100]) ? $request->input('per_page', 25) : 25;
        $pages = $query->orderBy('sort_order')->orderBy('title')->paginate($perPage);

        return view('admin.system-management.pages.index', compact('pages', 'perPage'));
    }

    public function create()
    {
        $nextSortOrder = (Page::max('sort_order') ?? 0) + 1;

        return view('admin.system-management.pages.create', compact('nextSortOrder'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'title' => $request->input('translations.en.title') ?? $request->input('title'),
            'content' => $request->input('translations.en.content') ?? $request->input('content'),
        ]);

        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'banner_title' => ['nullable', 'string', 'max:255'],
            'banner_description' => ['nullable', 'string'],
            'background_image' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'menu_group' => ['nullable', 'string', Rule::in(array_keys(Page::MENU_GROUP_LABELS))],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'og_type' => ['nullable', 'string', 'max:50'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'structured_data' => ['nullable', 'string'],
            'sitemap_include' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1', Rule::unique('pages', 'sort_order')],
        ], [
            'sort_order.unique' => 'This order number is already used by another page. Please choose a different order number.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sitemap_include'] = $request->boolean('sitemap_include');
        $validated['menu_group'] = $validated['menu_group'] ?? 'more';

        $validated['og_tags'] = array_filter([
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
            'og_type' => $validated['og_type'] ?? 'website',
        ]);

        unset($validated['og_title'], $validated['og_description'], $validated['og_image'], $validated['og_type']);

        if (!empty($validated['structured_data'])) {
            $decoded = json_decode($validated['structured_data'], true);
            $validated['structured_data'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $validated['structured_data'] = null;
        }

        $validated['translations'] = $this->buildTranslationsFromRequest($request);
        $validated['title'] = $validated['translations']['en']['title'] ?? $validated['title'];
        $validated['content'] = $validated['translations']['en']['content'] ?? $validated['content'] ?? null;
        $bannerFields = $this->extractBannerFields($request);
        $bannerFields['background_image'] = $this->storeBannerBackgroundImage(
            $request,
            $bannerFields['background_image'] ?? ''
        );
        if (!empty($bannerFields)) {
            $validated['page_content'] = $bannerFields;
        }

        $page = Page::create($validated);

        $newData = [
            'title' => $page->title,
            'slug' => $page->slug,
            'meta_title' => $page->meta_title,
            'translations' => $page->translations,
        ];
        AuditLogService::logCreate(AuditLog::MODULE_PAGE, $page->title, $newData);

        return redirect()->route('system-management.pages.index')->with('success', 'Page created successfully.');
    }

    public function show(Page $page)
    {
        return view('admin.system-management.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        $pageType = $page->getPageType();
        $data = ['page' => $page, 'pageType' => $pageType];
        $data['locales'] = PageLocales::labels();
        $data['translations'] = $this->getTranslationsForEdit($page);
        $data['pageContentByLocale'] = $this->getPageContentByLocale($page, $pageType);
        $bannerLocaleData = $page->getPageContentForLocale('en');
        $data['bannerTitle'] = $bannerLocaleData['banner_title'] ?? '';
        $data['bannerDescription'] = $bannerLocaleData['banner_description'] ?? '';
        $data['bannerBackgroundImage'] = $bannerLocaleData['background_image'] ?? '';

        if ($pageType === 'platform') {
            $data['platform_feature_icons'] = config('platform_feature_icons.icons', []);
            $data['pageContentByLocale'] = $this->normalizePlatformSharedImages($data['pageContentByLocale']);
            foreach (PageLocales::all() as $locale) {
                $data['pageContentByLocale'][$locale]['features'] = $this->normalizePlatformFeatures(
                    $data['pageContentByLocale'][$locale]['features'] ?? []
                );
            }
        } elseif ($pageType === 'about-us') {
            $data['pageContentByLocale'] = $this->normalizeAboutUsSharedImages($data['pageContentByLocale']);
            foreach (PageLocales::all() as $locale) {
                $data['pageContentByLocale'][$locale]['solution_cards'] = $this->normalizeAboutUsSolutionCards(
                    $data['pageContentByLocale'][$locale]['solution_cards'] ?? []
                );
                $data['pageContentByLocale'][$locale]['interest_cards'] = $this->normalizeAboutUsInterestCards(
                    $data['pageContentByLocale'][$locale]['interest_cards'] ?? []
                );
            }
        } elseif ($pageType === 'product') {
            $data['pageContentByLocale'] = $this->normalizeProductSharedImages($data['pageContentByLocale']);
            foreach (PageLocales::all() as $locale) {
                $data['pageContentByLocale'][$locale]['feature_cards'] = $this->normalizeProductFeatureCards(
                    $data['pageContentByLocale'][$locale]['feature_cards'] ?? []
                );
            }
            $data['product_partner_images'] = $this->normalizePartnerImages(
                $data['pageContentByLocale']['en']['partner_images'] ?? $data['pageContentByLocale'][array_key_first($data['pageContentByLocale'])]['partner_images'] ?? []
            );
        } elseif ($pageType === 'partner') {
            $data['pageContentByLocale'] = $this->normalizePartnerSharedImages($data['pageContentByLocale']);
            foreach (PageLocales::all() as $locale) {
                $data['pageContentByLocale'][$locale]['partner_features'] = $this->normalizePartnerFeatures(
                    $data['pageContentByLocale'][$locale]['partner_features'] ?? []
                );
            }
            $data['partner_page_images'] = $this->normalizePartnerImages(
                $data['pageContentByLocale']['en']['partner_images'] ?? $data['pageContentByLocale'][array_key_first($data['pageContentByLocale'])]['partner_images'] ?? []
            );
        } elseif ($pageType === 'contact') {
            // Contact uses the shared locale data editor below; no extra assets needed here.
        } elseif ($pageType !== 'home') {
            $data['page_content_json'] = $page->page_content
                ? json_encode($page->page_content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                : '{}';
        }
        if ($pageType === 'home') {
            $c = $page->getPageContentForLocale('en');
            $data = array_merge($data, [
                'homepage_hero_headline' => $c['hero_headline'] ?? '',
                'homepage_hero_description' => $c['hero_description'] ?? '',
                'homepage_hero_image' => $c['hero_image'] ?? '',
                'homepage_company_title' => $c['company_title'] ?? 'Our Mission',
                'homepage_company_description' => $c['company_description'] ?? '',
                'homepage_company_logo' => $c['company_logo'] ?? '',
                'homepage_value_prop_1_title' => $c['value_prop_1_title'] ?? '',
                'homepage_value_prop_1_desc' => $c['value_prop_1_desc'] ?? '',
                'homepage_value_prop_2_title' => $c['value_prop_2_title'] ?? '',
                'homepage_value_prop_2_desc' => $c['value_prop_2_desc'] ?? '',
                'homepage_value_prop_3_title' => $c['value_prop_3_title'] ?? '',
                'homepage_value_prop_3_desc' => $c['value_prop_3_desc'] ?? '',
                'homepage_capabilities_image' => $c['capabilities_image'] ?? '',
                'homepage_marketing_title' => $c['marketing_title'] ?? '',
                'homepage_marketing_description' => $c['marketing_description'] ?? '',
                'homepage_marketing_image' => $c['marketing_image'] ?? '',
                'homepage_mobile_title' => $c['mobile_title'] ?? '',
                'homepage_mobile_image' => $c['mobile_image'] ?? '',
                'homepage_mobile_bg' => $c['mobile_bg'] ?? '',
                'homepage_impact_feature_label' => $c['impact_feature_label'] ?? 'What we focus on',
                'homepage_impact_feature_1_title' => $c['impact_feature_1_title'] ?? 'Community updates',
                'homepage_impact_feature_1_desc' => $c['impact_feature_1_desc'] ?? 'Short, clear updates that show what is happening on the ground.',
                'homepage_impact_feature_2_title' => $c['impact_feature_2_title'] ?? 'Transparent reporting',
                'homepage_impact_feature_2_desc' => $c['impact_feature_2_desc'] ?? 'Simple reporting that helps supporters understand the results.',
                'homepage_impact_feature_3_title' => $c['impact_feature_3_title'] ?? 'Direct response',
                'homepage_impact_feature_3_desc' => $c['impact_feature_3_desc'] ?? 'Fast action when families need practical help the most.',
                'homepage_trust_subtitle' => $c['trust_subtitle'] ?? 'Built on trust',
                'homepage_trust_title' => $c['trust_title'] ?? 'Support that feels local, practical, and accountable.',
                'homepage_trust_description' => $c['trust_description'] ?? 'NGO work is strongest when it stays close to the people it serves. We listen first, respond with simple action, and keep donors and partners informed along the way.',
                'homepage_trust_step_1_title' => $c['trust_step_1_title'] ?? 'Listen to the community',
                'homepage_trust_step_1_desc' => $c['trust_step_1_desc'] ?? 'We work with local families, schools, and leaders to understand what matters most.',
                'homepage_trust_step_2_title' => $c['trust_step_2_title'] ?? 'Act with purpose',
                'homepage_trust_step_2_desc' => $c['trust_step_2_desc'] ?? 'Every program is designed to be useful, visible, and easy to support.',
                'homepage_trust_step_3_title' => $c['trust_step_3_title'] ?? 'Show the outcome',
                'homepage_trust_step_3_desc' => $c['trust_step_3_desc'] ?? 'We keep the story transparent so people can see the impact of their help.',
                'homepage_trust_image' => $c['trust_image'] ?? '',
                'homepage_trust_quote_label' => $c['trust_quote_label'] ?? 'Why it matters',
                'homepage_trust_quote_text' => $c['trust_quote_text'] ?? 'Small, clear actions build trust, and trust makes long-term community support possible.',
                'homepage_style_title' => $c['style_title'] ?? 'Featured programs',
                'homepage_styles' => is_array($c['styles'] ?? null) ? $c['styles'] : [],
                'homepage_partners_title' => $c['partners_title'] ?? 'Our supporters',
                'homepage_partner_images' => $this->normalizePartnerImages($c['partner_images'] ?? []),
            ]);
        } elseif ($pageType === 'partner') {
            $c = $page->getPageContentForLocale('en');
            $data = array_merge($data, [
                'partner_page_title' => $c['partners_title'] ?? 'Our supporters',
                'partner_page_description' => $c['partners_description'] ?? 'Partners and organizations helping grow practical education and community support.',
                'partner_page_feature_image' => $c['partner_feature_image'] ?? '',
                'partner_page_features' => $this->normalizePartnerFeatures($c['partner_features'] ?? []),
                'partner_page_images' => $this->normalizePartnerImages($c['partner_images'] ?? []),
            ]);
        }

        return view('admin.system-management.pages.edit', $data);
    }

    private function getTranslationsForEdit(Page $page): array
    {
        $translations = $page->translations ?? [];
        $result = [];
        foreach (PageLocales::all() as $locale) {
            $result[$locale] = [
                'title' => $translations[$locale]['title'] ?? ($locale === 'en' ? $page->title : ''),
                'content' => $translations[$locale]['content'] ?? ($locale === 'en' ? $page->content : ''),
            ];
        }
        if (empty($page->translations) && $page->title) {
            $result['en']['title'] = $page->title;
            $result['en']['content'] = $page->content ?? '';
        }
        return $result;
    }

    private function getPageContentByLocale(Page $page, ?string $pageType = null): array
    {
        $pageType = $pageType ?? $page->getPageType();
        $pc = $page->page_content ?? [];
        $locales = PageLocales::all();
        $isLocaleKeyed = false;
        foreach (array_keys($pc) as $key) {
            if (in_array($key, $locales, true)) {
                $isLocaleKeyed = true;
                break;
            }
        }
        $result = [];
        if ($isLocaleKeyed) {
            foreach ($locales as $locale) {
                $result[$locale] = $pc[$locale] ?? [];
            }
            if ($pageType === 'home') {
                $result = $this->normalizeHomepageSharedImages($result);
            }
        } else {
            foreach ($locales as $locale) {
                $result[$locale] = $locale === 'en' ? $pc : [];
            }
        }
        return $result;
    }

    /** Ensure platform image fields are shared across locales (first non-empty, EN first) */
    private function normalizePlatformSharedImages(array $pageContentByLocale): array
    {
        $imageKeys = ['choose_col_1_image'];
        $locales = PageLocales::all();
        $canonical = [];
        foreach ($imageKeys as $key) {
            foreach ($locales as $locale) {
                $val = $pageContentByLocale[$locale][$key] ?? null;
                if (!empty($val)) {
                    $canonical[$key] = $val;
                    break;
                }
            }
            $canonical[$key] = $canonical[$key] ?? '';
        }

        $sliderImages = [];
        foreach ($locales as $locale) {
            $arr = $pageContentByLocale[$locale]['platform_slider_images'] ?? null;
            if (is_array($arr)) {
                $sliderImages = $this->normalizePlatformSliderImages($arr);
                if (!empty($sliderImages)) {
                    break;
                }
            }
        }
        if (empty($sliderImages)) {
            foreach ($locales as $locale) {
                $single = $pageContentByLocale[$locale]['platform_image'] ?? '';
                if (!empty($single)) {
                    $sliderImages = [trim((string) $single)];
                    break;
                }
            }
        }
        if (empty($sliderImages)) {
            $sliderImages = [];
        }

        foreach ($locales as $locale) {
            foreach ($imageKeys as $key) {
                $pageContentByLocale[$locale][$key] = $canonical[$key];
            }
            $pageContentByLocale[$locale]['platform_slider_images'] = $sliderImages;
            $pageContentByLocale[$locale]['platform_image'] = $sliderImages[0] ?? '';
        }
        return $pageContentByLocale;
    }

    /** @param mixed $value */
    private function normalizePlatformSliderImages($value): array
    {
        if (!is_array($value)) {
            $s = is_string($value) ? trim($value) : '';

            return $s !== '' ? [$s] : [];
        }
        $out = [];
        foreach ($value as $item) {
            $s = is_string($item) ? trim($item) : '';
            if ($s !== '') {
                $out[] = $s;
            }
        }

        return $out;
    }

    /** Ensure about-us image fields are shared across locales (first non-empty, EN first) */
    private function normalizeAboutUsSharedImages(array $pageContentByLocale): array
    {
        $imageKeys = ['different_image', 'promise_image'];
        for ($i = 1; $i <= 3; $i++) {
            $imageKeys[] = "solution_{$i}_image";
        }
        for ($i = 1; $i <= 6; $i++) {
            $imageKeys[] = "interest_{$i}_image";
        }
        $locales = PageLocales::all();
        $canonical = [];
        foreach ($imageKeys as $key) {
            foreach ($locales as $locale) {
                $val = $pageContentByLocale[$locale][$key] ?? null;
                if (!empty($val)) {
                    $canonical[$key] = $val;
                    break;
                }
            }
            $canonical[$key] = $canonical[$key] ?? '';
        }
        foreach ($locales as $locale) {
            foreach ($imageKeys as $key) {
                $pageContentByLocale[$locale][$key] = $canonical[$key];
            }
        }

        $stripImages = [];
        foreach ($locales as $locale) {
            $val = $pageContentByLocale[$locale]['about_strip_images'] ?? [];
            if (is_array($val)) {
                $stripImages = array_values(array_filter(array_map('strval', $val)));
            } elseif (is_string($val) && trim($val) !== '') {
                $stripImages = [trim($val)];
            }
            if (!empty($stripImages)) {
                break;
            }
        }
        foreach ($locales as $locale) {
            $pageContentByLocale[$locale]['about_strip_images'] = $stripImages;
        }
        return $pageContentByLocale;
    }

    /** Ensure product page partner_images are shared across locales (same as homepage) */
    private function normalizeProductSharedImages(array $pageContentByLocale): array
    {
        $locales = PageLocales::all();
        $partnerImages = [];
        foreach ($locales as $locale) {
            $arr = $pageContentByLocale[$locale]['partner_images'] ?? [];
            if (is_array($arr) && !empty($arr)) {
                $partnerImages = $this->normalizePartnerImages($arr);
                break;
            }
        }
        foreach ($locales as $locale) {
            $pageContentByLocale[$locale]['partner_images'] = $partnerImages ?: ($pageContentByLocale[$locale]['partner_images'] ?? []);
        }
        return $pageContentByLocale;
    }

    private function normalizePartnerSharedImages(array $pageContentByLocale): array
    {
        $locales = PageLocales::all();
        $partnerImages = [];
        foreach ($locales as $locale) {
            $arr = $pageContentByLocale[$locale]['partner_images'] ?? [];
            if (is_array($arr) && !empty($arr)) {
                $partnerImages = $this->normalizePartnerImages($arr);
                break;
            }
        }
        foreach ($locales as $locale) {
            $pageContentByLocale[$locale]['partner_images'] = $partnerImages ?: ($pageContentByLocale[$locale]['partner_images'] ?? []);
        }
        return $pageContentByLocale;
    }

    private function normalizeProductFeatureCards(array $value): array
    {
        $defaults = [
            ['title' => 'Education', 'description' => 'School support, learning, and access.', 'image' => ''],
            ['title' => 'Health', 'description' => 'Outreach, referrals, and basic care.', 'image' => ''],
            ['title' => 'Relief', 'description' => 'Rapid support during urgent hardship.', 'image' => ''],
            ['title' => 'Partnership', 'description' => 'Local work with shared accountability.', 'image' => ''],
        ];

        $result = [];
        for ($i = 0; $i < 4; $i++) {
            $item = $value[$i] ?? $defaults[$i];
            $result[] = [
                'title' => is_array($item) ? ($item['title'] ?? $defaults[$i]['title']) : $defaults[$i]['title'],
                'description' => is_array($item) ? ($item['description'] ?? $defaults[$i]['description']) : $defaults[$i]['description'],
                'image' => is_array($item) ? ($item['image'] ?? $defaults[$i]['image']) : $defaults[$i]['image'],
            ];
        }

        return $result;
    }

    private function normalizePartnerFeatures(array $value): array
    {
        $defaults = [
            ['title' => 'Funding & grants', 'description' => 'Support programs, materials, teacher stipends, and infrastructure projects.'],
            ['title' => 'In-kind expertise', 'description' => 'Offer training, curriculum resources, monitoring & evaluation, or tech support.'],
        ];

        $result = [];
        for ($i = 0; $i < 2; $i++) {
            $item = $value[$i] ?? $defaults[$i];
            $result[] = [
                'title' => is_array($item) ? ($item['title'] ?? $defaults[$i]['title']) : $defaults[$i]['title'],
                'description' => is_array($item) ? ($item['description'] ?? $defaults[$i]['description']) : $defaults[$i]['description'],
            ];
        }

        return $result;
    }

    /** Ensure homepage image fields are shared across locales (first non-empty, EN first) */
    private function normalizeHomepageSharedImages(array $pageContentByLocale): array
    {
        $imageKeys = ['hero_image', 'company_logo', 'capabilities_image', 'marketing_image', 'mobile_image', 'mobile_bg', 'trust_image'];
        $locales = PageLocales::all();
        $canonical = [];
        foreach ($imageKeys as $key) {
            foreach ($locales as $locale) {
                $val = $pageContentByLocale[$locale][$key] ?? null;
                if (!empty($val)) {
                    $canonical[$key] = $val;
                    break;
                }
            }
            $canonical[$key] = $canonical[$key] ?? '';
        }
        $partnerImages = [];
        foreach ($locales as $locale) {
            $arr = $pageContentByLocale[$locale]['partner_images'] ?? [];
            if (is_array($arr) && !empty($arr)) {
                $partnerImages = $this->normalizePartnerImages($arr);
                break;
            }
        }
        foreach ($locales as $locale) {
            foreach ($imageKeys as $key) {
                $pageContentByLocale[$locale][$key] = $canonical[$key];
            }
            $pageContentByLocale[$locale]['partner_images'] = $partnerImages ?: ($pageContentByLocale[$locale]['partner_images'] ?? []);
        }
        return $pageContentByLocale;
    }

    private function buildTranslationsFromRequest(Request $request): array
    {
        $translations = [];
        foreach (PageLocales::all() as $locale) {
            $title = $request->input("translations.{$locale}.title") ?? $request->input("title");
            $content = $request->input("translations.{$locale}.content") ?? $request->input("content");
            $translations[$locale] = [
                'title' => $title ?? '',
                'content' => $content ?? '',
            ];
        }
        return $translations;
    }

    private function normalizeAboutUsSolutionCards(array $value): array
    {
        $defaults = [
            ['title' => 'Education support', 'description' => 'Scholarships, school supplies, and learning support for children and young people.', 'image' => ''],
            ['title' => 'Health outreach', 'description' => 'Wellness visits, family care, and community health education.', 'image' => ''],
            ['title' => 'Emergency relief', 'description' => 'Fast support for families facing crisis, displacement, or urgent hardship.', 'image' => ''],
        ];
        $result = [];
        for ($i = 0; $i < 3; $i++) {
            $item = $value[$i] ?? $defaults[$i];
            $result[] = [
                'title' => is_array($item) ? ($item['title'] ?? $defaults[$i]['title']) : $defaults[$i]['title'],
                'description' => is_array($item) ? ($item['description'] ?? $defaults[$i]['description']) : $defaults[$i]['description'],
                'image' => is_array($item) ? ($item['image'] ?? $defaults[$i]['image']) : $defaults[$i]['image'],
            ];
        }
        return $result;
    }

    private function normalizeAboutUsInterestCards(array $value): array
    {
        $defaults = [
            ['title' => 'Meals and essentials', 'description' => 'Helping families access what they need most, when they need it most.', 'image' => ''],
            ['title' => 'Youth mentoring', 'description' => 'Guidance, encouragement, and opportunities for young people to grow.', 'image' => ''],
            ['title' => 'Reports and transparency', 'description' => 'Clear reporting so supporters can see how the work makes a difference.', 'image' => ''],
            ['title' => 'Community partnerships', 'description' => 'Working side by side with local organizations to strengthen support.', 'image' => ''],
            ['title' => 'Volunteer care', 'description' => 'Equipping volunteers with simple, effective ways to help.', 'image' => ''],
            ['title' => 'Ongoing support', 'description' => 'Stay connected through updates, needs, and opportunities to serve.', 'image' => ''],
        ];
        $result = [];
        for ($i = 0; $i < 6; $i++) {
            $item = $value[$i] ?? $defaults[$i];
            $result[] = [
                'title' => is_array($item) ? ($item['title'] ?? $defaults[$i]['title']) : $defaults[$i]['title'],
                'description' => is_array($item) ? ($item['description'] ?? $defaults[$i]['description']) : $defaults[$i]['description'],
                'image' => is_array($item) ? ($item['image'] ?? $defaults[$i]['image']) : $defaults[$i]['image'],
            ];
        }
        return $result;
    }

    private function normalizePartnerImages($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        return $value ? [$value] : [];
    }

    private function normalizePlatformFeatures($value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $validIconKeys = array_keys(config('platform_feature_icons.icons', []));
        $defaultIcon = $validIconKeys[0] ?? 'icon_1';
        $result = [];
        foreach (array_values($value) as $item) {
            $title = '';
            if (is_array($item)) {
                $title = trim((string) ($item['title'] ?? ''));
            } elseif (is_string($item)) {
                $title = trim($item);
            }
            if ($title === '') {
                continue;
            }
            $color = is_array($item) ? ($item['color'] ?? 'blue') : 'blue';
            $color = in_array($color, ['blue', 'purple', 'green', 'red'], true) ? $color : 'blue';
            $icon = is_array($item) ? ($item['icon'] ?? $defaultIcon) : $defaultIcon;
            $icon = in_array($icon, $validIconKeys, true) ? $icon : $defaultIcon;
            $result[] = [
                'title' => $title,
                'color' => $color,
                'icon' => $icon,
            ];
        }

        return $result;
    }

    private function extractBannerFields(Request $request): array
    {
        return [
            'banner_title' => trim((string) $request->input('banner_title', '')),
            'banner_description' => trim((string) $request->input('banner_description', '')),
            'background_image' => trim((string) $request->input('background_image', '')),
        ];
    }

    private function storeBannerBackgroundImage(Request $request, ?string $fallback = ''): string
    {
        $uploadPath = 'images/banners';
        $path = public_path($uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $file = $request->file('background_image_file');
        if ($file?->isValid()) {
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $name);
            return $uploadPath . '/' . $name;
        }

        return trim((string) $fallback);
    }

    private function mergeBannerFieldsIntoPageContent(array $content, array $bannerFields): array
    {
        $bannerFields = array_filter($bannerFields, static fn ($value) => is_string($value) ? trim($value) !== '' : !empty($value));
        if (empty($bannerFields)) {
            return $content;
        }

        $locales = PageLocales::all();
        $hasLocaleKeys = false;
        foreach (array_keys($content) as $key) {
            if (in_array($key, $locales, true)) {
                $hasLocaleKeys = true;
                break;
            }
        }

        if ($hasLocaleKeys) {
            foreach ($locales as $locale) {
                if (isset($content[$locale]) && is_array($content[$locale])) {
                    $content[$locale] = array_merge($content[$locale], $bannerFields);
                }
            }
            return $content;
        }

        return array_merge($content, $bannerFields);
    }

    public function update(Request $request, Page $page)
    {
        $request->attributes->set('audit_batch_id', Str::uuid()->toString());

        $request->merge([
            'title' => $request->input('translations.en.title') ?? $request->input('title'),
            'content' => $request->input('translations.en.content') ?? $request->input('content'),
        ]);

        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug,' . $page->id],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'banner_title' => ['nullable', 'string', 'max:255'],
            'banner_description' => ['nullable', 'string'],
            'background_image' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'menu_group' => ['nullable', 'string', Rule::in(array_keys(Page::MENU_GROUP_LABELS))],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'og_type' => ['nullable', 'string', 'max:50'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'structured_data' => ['nullable', 'string'],
            'sitemap_include' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:1', Rule::unique('pages', 'sort_order')->ignore($page->id)],
        ], [
            'sort_order.unique' => 'This order number is already used by another page. Please choose a different order number.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sitemap_include'] = $request->boolean('sitemap_include');
        $validated['menu_group'] = $validated['menu_group'] ?? ($page->menu_group ?? 'more');

        $validated['translations'] = $this->buildTranslationsFromRequest($request);
        $validated['title'] = $validated['translations']['en']['title'] ?? $validated['title'];
        $validated['content'] = $validated['translations']['en']['content'] ?? $validated['content'] ?? null;
        $bannerFields = $this->extractBannerFields($request);

        $originalSlug = $page->slug;
        $pageType = $page->getPageType();
        // Use page type (from content structure) so we don't overwrite special page content
        // when slug was changed (e.g. home → homes) - otherwise we'd build from empty
        // page_content_locale and lose all data
        if (!in_array($pageType ?? $originalSlug, ['home', 'platform', 'about-us', 'product', 'contact'])) {
            $pageContentByLocale = $request->input('page_content_locale', []);
            $built = is_array($page->page_content ?? null) ? $page->page_content : [];
            foreach (PageLocales::all() as $locale) {
                if (!array_key_exists($locale, $pageContentByLocale)) {
                    continue;
                }
                $json = $pageContentByLocale[$locale] ?? null;
                $decoded = is_string($json) ? json_decode($json, true) : null;
                $built[$locale] = is_array($decoded) ? $decoded : [];
            }
            $validated['page_content'] = $this->mergeBannerFieldsIntoPageContent($built, $bannerFields);
        }

        $validated['og_tags'] = array_filter([
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
            'og_type' => $validated['og_type'] ?? 'website',
        ]);

        unset($validated['og_title'], $validated['og_description'], $validated['og_image'], $validated['og_type']);

        if (!empty($validated['structured_data'])) {
            $decoded = json_decode($validated['structured_data'], true);
            $validated['structured_data'] = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        } else {
            $validated['structured_data'] = null;
        }

        $oldData = [
            'title' => $page->title,
            'slug' => $page->slug,
            'meta_title' => $page->meta_title,
            'translations' => $page->translations,
        ];
        if (array_key_exists('page_content', $validated)) {
            $oldData['page_content'] = $page->page_content;
        }
        $page->update($validated);
        $newData = [
            'title' => $page->title,
            'slug' => $page->slug,
            'meta_title' => $page->meta_title,
            'translations' => $validated['translations'] ?? $page->translations,
        ];
        if (array_key_exists('page_content', $validated)) {
            $newData['page_content'] = $validated['page_content'];
        }
        AuditLogService::logEdit(AuditLog::MODULE_PAGE, $page->title, $oldData, $newData);

        // Use page type (from content structure) so section data is saved even when slug was changed
        // (e.g. home → homes). If we used only $originalSlug, a page with slug "homes" but home-type
        // content would not trigger saveHomepageSections, and we'd overwrite with empty page_content.
        $effectiveType = $pageType ?? $originalSlug;
        if ($effectiveType === 'home') {
            $this->saveHomepageSections($request, $page);
        } elseif ($effectiveType === 'platform') {
            $this->savePlatformSections($request, $page);
        } elseif ($effectiveType === 'about-us') {
            $this->saveAboutUsSections($request, $page);
        } elseif ($effectiveType === 'product') {
            $this->saveProductSections($request, $page);
        } elseif ($effectiveType === 'partner') {
            $this->savePartnerSections($request, $page);
        } elseif ($effectiveType === 'contact') {
            $this->saveContactSections($request, $page);
        }

        return redirect()->route('system-management.pages.index')->with('success', 'Page updated successfully.');
    }

    private function saveHomepageSections(Request $request, Page $page): void
    {
        $uploadPath = 'images/homepage';
        $path = public_path($uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $sections = $request->input('homepage_sections', []);
        $content = [];
        $bannerFields = $this->extractBannerFields($request);

        $saveImage = function ($file, ?string $fallback) use ($uploadPath) {
            if ($file?->isValid()) {
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $name);
                return $uploadPath . '/' . $name;
            }
            return $fallback ?? '';
        };

        $imageKeys = ['hero_image', 'company_logo', 'capabilities_image', 'marketing_image', 'mobile_image', 'mobile_bg', 'trust_image'];
        $sharedImages = [];
        foreach ($imageKeys as $key) {
            $file = null;
            $pathVal = '';
            foreach (PageLocales::all() as $locale) {
                $f = $request->file("homepage_sections.{$locale}.{$key}_file");
                $p = $sections[$locale][$key] ?? '';
                if ($f?->isValid()) {
                    $file = $f;
                    break;
                }
                if (!empty($p)) {
                    $pathVal = $p;
                }
            }
            $sharedImages[$key] = $saveImage($file, $pathVal ?: null);
        }

        $keep = $request->input('homepage_keep_partner_images', []) ?? [];
        $existingPartner = $this->normalizePartnerImages($page->getPageContentForLocale('en')['partner_images'] ?? []);
        $currentPartner = array_values(array_intersect($existingPartner, is_array($keep) ? $keep : [$keep]));
        if ($request->hasFile('homepage_partner_image_files')) {
            $seen = [];
            foreach ($request->file('homepage_partner_image_files') as $file) {
                if ($file?->isValid()) {
                    $key = $file->getClientOriginalName() . '-' . $file->getSize();
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;
                    $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($uploadPath), $name);
                    $currentPartner[] = $uploadPath . '/' . $name;
                }
            }
        }
        foreach ($this->validatedStagedPaths($request, 'homepage_staged_partner_paths', 'images/homepage') as $path) {
            $currentPartner[] = $path;
        }

        $globalStyles = $sections['_global']['styles'] ?? [];
        $savedStyles = [];
        foreach ($globalStyles as $si => $style) {
            $styleImage = $saveImage(
                $request->file("homepage_sections._global.style_{$si}_image_file"),
                $style['image'] ?? null
            );
            $colors = [];
            foreach (($style['colors'] ?? []) as $ci => $color) {
                $colorImage = $saveImage(
                    $request->file("homepage_sections._global.style_{$si}_color_{$ci}_image_file"),
                    $color['image'] ?? null
                );
                $colors[] = [
                    'name' => trim($color['name'] ?? ''),
                    'hex' => strtolower(trim($color['hex'] ?? '#6366f1')),
                    'image' => $colorImage,
                ];
            }
            $savedStyles[] = [
                'image' => $styleImage,
                'title' => trim($style['title'] ?? ''),
                'description' => trim($style['description'] ?? ''),
                'colors' => $colors,
            ];
        }

        foreach (PageLocales::all() as $locale) {
            $s = $sections[$locale] ?? [];
            $content[$locale] = [
                'banner_title' => $bannerFields['banner_title'] ?? '',
                'banner_description' => $bannerFields['banner_description'] ?? '',
                'hero_headline' => $s['hero_headline'] ?? '',
                'hero_description' => $s['hero_description'] ?? '',
                'hero_image' => $sharedImages['hero_image'],
                'company_title' => $s['company_title'] ?? '',
                'company_description' => $s['company_description'] ?? '',
                'company_logo' => $sharedImages['company_logo'],
                'value_prop_1_title' => $s['value_prop_1_title'] ?? '',
                'value_prop_1_desc' => $s['value_prop_1_desc'] ?? '',
                'value_prop_2_title' => $s['value_prop_2_title'] ?? '',
                'value_prop_2_desc' => $s['value_prop_2_desc'] ?? '',
                'value_prop_3_title' => $s['value_prop_3_title'] ?? '',
                'value_prop_3_desc' => $s['value_prop_3_desc'] ?? '',
                'capabilities_image' => $sharedImages['capabilities_image'],
                'marketing_title' => $s['marketing_title'] ?? '',
                'marketing_description' => $s['marketing_description'] ?? '',
                'marketing_image' => $sharedImages['marketing_image'],
                'mobile_title' => $s['mobile_title'] ?? '',
                'mobile_image' => $sharedImages['mobile_image'],
                'mobile_bg' => $sharedImages['mobile_bg'],
                'impact_feature_label' => $s['impact_feature_label'] ?? '',
                'impact_feature_1_title' => $s['impact_feature_1_title'] ?? '',
                'impact_feature_1_desc' => $s['impact_feature_1_desc'] ?? '',
                'impact_feature_2_title' => $s['impact_feature_2_title'] ?? '',
                'impact_feature_2_desc' => $s['impact_feature_2_desc'] ?? '',
                'impact_feature_3_title' => $s['impact_feature_3_title'] ?? '',
                'impact_feature_3_desc' => $s['impact_feature_3_desc'] ?? '',
                'trust_subtitle' => $s['trust_subtitle'] ?? '',
                'trust_title' => $s['trust_title'] ?? '',
                'trust_description' => $s['trust_description'] ?? '',
                'trust_step_1_title' => $s['trust_step_1_title'] ?? '',
                'trust_step_1_desc' => $s['trust_step_1_desc'] ?? '',
                'trust_step_2_title' => $s['trust_step_2_title'] ?? '',
                'trust_step_2_desc' => $s['trust_step_2_desc'] ?? '',
                'trust_step_3_title' => $s['trust_step_3_title'] ?? '',
                'trust_step_3_desc' => $s['trust_step_3_desc'] ?? '',
                'trust_image' => $sharedImages['trust_image'],
                'trust_quote_label' => $s['trust_quote_label'] ?? '',
                'trust_quote_text' => $s['trust_quote_text'] ?? '',
                'style_title' => $s['style_title'] ?? '',
                'color_choice_title' => $s['color_choice_title'] ?? 'COLOR CHOICE',
                'styles' => $savedStyles,
                'partners_title' => $s['partners_title'] ?? '',
                'partner_images' => $currentPartner,
            ];
        }

        $oldEnHome = $page->getPageContentForLocale('en');
        foreach (['hero_image', 'company_logo', 'capabilities_image', 'marketing_image', 'mobile_image', 'mobile_bg', 'trust_image'] as $key) {
            $this->deleteRemovedPageImages(
                !empty($oldEnHome[$key]) ? [(string) $oldEnHome[$key]] : [],
                !empty($sharedImages[$key]) ? [(string) $sharedImages[$key]] : [],
                'images/homepage'
            );
        }
        $this->deleteRemovedPageImages(
            $this->collectHomepageStyleImagePaths($oldEnHome['styles'] ?? []),
            $this->collectHomepageStyleImagePaths($savedStyles),
            'images/homepage'
        );
        $this->deleteRemovedPageImages($existingPartner, $currentPartner, 'images/homepage');
        $oldContent = $page->page_content ?? [];
        $page->update(['page_content' => $content]);
        AuditLogService::logEdit(AuditLog::MODULE_PAGE, $page->title . ' (Homepage sections)', ['page_content' => $oldContent], ['page_content' => $content]);
    }

    private function savePlatformSections(Request $request, Page $page): void
    {
        $uploadPath = 'images/platform';
        $path = public_path($uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $sections = $request->input('platform_sections', []);
        $validIconKeys = array_keys(config('platform_feature_icons.icons', []));
        $defaultIcon = $validIconKeys[0] ?? 'icon_1';
        $bannerFields = $this->extractBannerFields($request);

        $saveImage = function ($file, ?string $fallback) use ($uploadPath) {
            if ($file?->isValid()) {
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $name);
                return $uploadPath . '/' . $name;
            }
            return $fallback ?? '';
        };

        $imageKeys = ['choose_col_1_image'];
        $sharedImages = [];
        foreach ($imageKeys as $key) {
            $file = null;
            $pathVal = '';
            foreach (PageLocales::all() as $locale) {
                $f = $request->file("platform_sections.{$locale}.{$key}_file");
                $p = $sections[$locale][$key] ?? '';
                if ($f?->isValid()) {
                    $file = $f;
                    break;
                }
                if (!empty($p)) {
                    $pathVal = $p;
                }
            }
            $sharedImages[$key] = $saveImage($file, $pathVal ?: null);
        }

        $keep = $request->input('platform_keep_slider_images', []) ?? [];
        $existingSlider = $this->normalizePlatformSliderImages(
            $page->getPageContentForLocale('en')['platform_slider_images'] ?? []
        );
        if (empty($existingSlider)) {
            $legacy = $page->getPageContentForLocale('en')['platform_image'] ?? '';
            if (!empty($legacy)) {
                $existingSlider = [trim((string) $legacy)];
            }
        }
        $currentSlider = array_values(array_intersect($existingSlider, is_array($keep) ? $keep : [$keep]));
        if ($request->hasFile('platform_slider_image_files')) {
            $seen = [];
            foreach ($request->file('platform_slider_image_files') as $file) {
                if ($file?->isValid()) {
                    $key = $file->getClientOriginalName() . '-' . $file->getSize();
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;
                    $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($uploadPath), $name);
                    $currentSlider[] = $uploadPath . '/' . $name;
                }
            }
        }
        foreach ($this->validatedStagedPaths($request, 'platform_staged_slider_paths', 'images/platform') as $path) {
            $currentSlider[] = $path;
        }
        if (empty($currentSlider)) {
            $currentSlider = [];
        }

        $this->deleteRemovedPageImages($existingSlider, $currentSlider, 'images/platform');
        $content = [];
        foreach (PageLocales::all() as $locale) {
            $s = $sections[$locale] ?? [];
            $rawFeatures = $s['features'] ?? [];
            if (!is_array($rawFeatures)) {
                $rawFeatures = [];
            }
            $features = [];
            foreach (array_values($rawFeatures) as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $title = trim((string) ($row['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $color = $row['color'] ?? 'blue';
                $icon = $row['icon'] ?? $defaultIcon;
                $features[] = [
                    'title' => $title,
                    'color' => in_array($color, ['blue', 'purple', 'green', 'red'], true) ? $color : 'blue',
                    'icon' => in_array($icon, $validIconKeys, true) ? $icon : $defaultIcon,
                ];
                if (count($features) >= 100) {
                    break;
                }
            }
            $content[$locale] = [
                'banner_title' => $bannerFields['banner_title'] ?? '',
                'banner_description' => $bannerFields['banner_description'] ?? '',
                'profile_title' => $s['profile_title'] ?? '',
                'profile_tagline' => $s['profile_tagline'] ?? '',
                'platform_slider_images' => $currentSlider,
                'platform_image' => $currentSlider[0] ?? '',
                'features_title' => $s['features_title'] ?? '',
                'features_subtitle' => $s['features_subtitle'] ?? '',
                'features' => $features,
                'choose_title' => $s['choose_title'] ?? '',
                'choose_col_1_text' => $s['choose_col_1_text'] ?? '',
                'choose_col_1_image' => $sharedImages['choose_col_1_image'],
                'choose_col_2_text' => $s['choose_col_2_text'] ?? '',
                'choose_col_2_value' => $s['choose_col_2_value'] ?? '',
                'choose_col_3_text' => $s['choose_col_3_text'] ?? '',
                'choose_col_3_value' => $s['choose_col_3_value'] ?? '',
            ];
        }

        $oldEnPlatform = $page->getPageContentForLocale('en');
        $this->deleteRemovedPageImages(
            !empty($oldEnPlatform['choose_col_1_image']) ? [(string) $oldEnPlatform['choose_col_1_image']] : [],
            !empty($sharedImages['choose_col_1_image']) ? [(string) $sharedImages['choose_col_1_image']] : [],
            'images/platform'
        );

        $oldContent = $page->page_content ?? [];
        $page->update(['page_content' => $content]);
        AuditLogService::logEdit(AuditLog::MODULE_PAGE, $page->title . ' (Platform sections)', ['page_content' => $oldContent], ['page_content' => $content]);
    }

    private function saveAboutUsSections(Request $request, Page $page): void
    {
        $uploadPath = 'images/about-us';
        $path = public_path($uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $sections = $request->input('about_us_sections', []);
        $validSolKeys = array_keys(config('about_us_icons.solution_icons', []));
        $validIntKeys = array_keys(config('about_us_icons.interest_icons', []));
        $bannerFields = $this->extractBannerFields($request);
        $bannerFields['background_image'] = $this->storeBannerBackgroundImage(
            $request,
            $bannerFields['background_image'] ?? ($page->getPageContentForLocale('en')['background_image'] ?? '')
        );

        $saveImage = function ($file, ?string $fallback) use ($uploadPath) {
            if ($file?->isValid()) {
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $name);
                return $uploadPath . '/' . $name;
            }
            return $fallback ?? '';
        };

        $imageKeys = ['different_image', 'promise_image'];
        for ($i = 1; $i <= 3; $i++) {
            $imageKeys[] = "solution_{$i}_image";
        }
        for ($i = 1; $i <= 6; $i++) {
            $imageKeys[] = "interest_{$i}_image";
        }
        $sharedImages = [];
        foreach ($imageKeys as $key) {
            $file = null;
            $pathVal = '';
            foreach (PageLocales::all() as $locale) {
                $f = $request->file("about_us_sections.{$locale}.{$key}_file");
                $p = $sections[$locale][$key] ?? '';
                if ($f?->isValid()) {
                    $file = $f;
                    break;
                }
                if (!empty($p)) {
                    $pathVal = $p;
                }
            }
            $sharedImages[$key] = $saveImage($file, $pathVal ?: null);
        }

        $existingStripImages = array_values(array_filter(array_map('strval', (array) ($page->getPageContentForLocale('en')['about_strip_images'] ?? []))));
        $keptStripImages = array_values(array_filter(array_map('strval', (array) $request->input('about_keep_strip_images', []))));
        $stagedStripImages = $this->validatedStagedPaths($request, 'about_staged_strip_paths', 'images/about-us');
        $directStripImages = [];
        foreach (PageLocales::all() as $locale) {
            $files = $request->file("about_us_sections.{$locale}.about_strip_image_files", []);
            if (!is_array($files)) {
                $files = $files ? [$files] : [];
            }
            foreach ($files as $file) {
                if ($file?->isValid()) {
                    $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($uploadPath), $name);
                    $directStripImages[] = $uploadPath . '/' . $name;
                }
            }
        }
        $directStripImages = array_values(array_unique($directStripImages));
        $currentStripImages = $stagedStripImages;
        if (!empty($keptStripImages)) {
            $currentStripImages = array_values(array_unique(array_merge(
                array_values(array_intersect($existingStripImages, $keptStripImages)),
                $stagedStripImages,
                $directStripImages
            )));
        } elseif (!empty($directStripImages) || !empty($stagedStripImages)) {
            $currentStripImages = array_values(array_unique(array_merge($stagedStripImages, $directStripImages)));
        } elseif (empty($stagedStripImages) && empty($directStripImages) && !empty($existingStripImages)) {
            $currentStripImages = $existingStripImages;
        }

        $content = [];
        foreach (PageLocales::all() as $locale) {
            $s = $sections[$locale] ?? [];
            $solCards = [];
            for ($i = 1; $i <= 3; $i++) {
                $icon = $s["solution_{$i}_icon"] ?? "sol_{$i}";
                $solCards[] = [
                    'title' => $s["solution_{$i}_title"] ?? '',
                    'description' => $s["solution_{$i}_description"] ?? '',
                    'icon' => in_array($icon, $validSolKeys, true) ? $icon : ($validSolKeys[$i - 1] ?? 'sol_1'),
                ];
            }
            $intCards = [];
            for ($i = 1; $i <= 6; $i++) {
                $imageKey = "interest_{$i}_image";
                $intCards[] = [
                    'title' => $s["interest_{$i}_title"] ?? '',
                    'description' => $s["interest_{$i}_description"] ?? '',
                    'image' => $sharedImages[$imageKey] ?? ($s[$imageKey] ?? ''),
                ];
            }
            $solCards = [];
            for ($i = 1; $i <= 3; $i++) {
                $imageKey = "solution_{$i}_image";
                $solCards[] = [
                    'title' => $s["solution_{$i}_title"] ?? '',
                    'description' => $s["solution_{$i}_description"] ?? '',
                    'image' => $sharedImages[$imageKey] ?? ($s[$imageKey] ?? ''),
                ];
            }
            $content[$locale] = [
                'banner_title' => $bannerFields['banner_title'] ?? '',
                'banner_description' => $bannerFields['banner_description'] ?? '',
                'background_image' => $bannerFields['background_image'] ?? '',
                'results_subtitle' => $s['results_subtitle'] ?? '',
                'results_title' => $s['results_title'] ?? '',
                'results_description' => $s['results_description'] ?? '',
                'different_subtitle' => $s['different_subtitle'] ?? '',
                'different_title' => $s['different_title'] ?? '',
                'different_description' => $s['different_description'] ?? '',
                'different_check' => $s['different_check'] ?? '',
                'different_image' => $sharedImages['different_image'],
                'about_strip_images' => $currentStripImages,
                'mission_1_title' => $s['mission_1_title'] ?? '',
                'mission_1_description' => $s['mission_1_description'] ?? '',
                'mission_2_title' => $s['mission_2_title'] ?? '',
                'mission_2_description' => $s['mission_2_description'] ?? '',
                'mission_3_title' => $s['mission_3_title'] ?? '',
                'mission_3_description' => $s['mission_3_description'] ?? '',
                'promise_subtitle' => $s['promise_subtitle'] ?? '',
                'promise_title' => $s['promise_title'] ?? '',
                'promise_description' => $s['promise_description'] ?? '',
                'promise_check' => $s['promise_check'] ?? '',
                'promise_image' => $sharedImages['promise_image'],
                'solutions_subtitle' => $s['solutions_subtitle'] ?? '',
                'solutions_title' => $s['solutions_title'] ?? '',
                'solutions_description' => $s['solutions_description'] ?? '',
                'solution_cards' => $this->normalizeAboutUsSolutionCards($solCards),
                'interests_title' => $s['interests_title'] ?? '',
                'interest_cards' => $this->normalizeAboutUsInterestCards($intCards),
                'ready_title' => $s['ready_title'] ?? '',
                'ready_description' => $s['ready_description'] ?? '',
            ];
        }

        $oldEnAbout = $page->getPageContentForLocale('en');
        foreach (['different_image', 'promise_image'] as $key) {
            $this->deleteRemovedPageImages(
                !empty($oldEnAbout[$key]) ? [(string) $oldEnAbout[$key]] : [],
                !empty($sharedImages[$key]) ? [(string) $sharedImages[$key]] : [],
                'images/about-us'
            );
        }
        $this->deleteRemovedPageImages(
            !empty($oldEnAbout['about_strip_images']) && is_array($oldEnAbout['about_strip_images']) ? array_map('strval', $oldEnAbout['about_strip_images']) : [],
            $currentStripImages,
            'images/about-us'
        );

        $oldContent = $page->page_content ?? [];
        $page->update(['page_content' => $content]);
        AuditLogService::logEdit(AuditLog::MODULE_PAGE, $page->title . ' (About Us sections)', ['page_content' => $oldContent], ['page_content' => $content]);
    }

    private function saveProductSections(Request $request, Page $page): void
    {
        $uploadPath = 'images/homepage';
        $featureUploadPath = 'images/product-features';
        foreach ([$uploadPath, $featureUploadPath] as $dir) {
            $path = public_path($dir);
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }

        $sections = $request->input('product_sections', []);
        if (!is_array($sections)) {
            $sections = [];
        }
        $keep = $request->input('product_keep_partner_images', []) ?? [];
        $existingPartner = $this->normalizePartnerImages($page->getPageContentForLocale('en')['partner_images'] ?? []);
        $currentPartner = array_values(array_intersect($existingPartner, is_array($keep) ? $keep : [$keep]));
        $bannerFields = $this->extractBannerFields($request);
        $bannerFields['background_image'] = $this->storeBannerBackgroundImage(
            $request,
            $bannerFields['background_image'] ?? ($page->getPageContentForLocale('en')['background_image'] ?? '')
        );
        $saveImage = function ($file, ?string $fallback, string $path) {
            if ($file?->isValid()) {
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($path), $name);
                return $path . '/' . $name;
            }
            return trim((string) $fallback);
        };
        if ($request->hasFile('product_partner_image_files')) {
            $seen = [];
            foreach ($request->file('product_partner_image_files') as $file) {
                if ($file?->isValid()) {
                    $key = $file->getClientOriginalName() . '-' . $file->getSize();
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;
                    $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($uploadPath), $name);
                    $currentPartner[] = $uploadPath . '/' . $name;
                }
            }
        }
        foreach ($this->validatedStagedPaths($request, 'product_staged_partner_paths', 'images/homepage') as $path) {
            $currentPartner[] = $path;
        }

        $currentFeatureImages = [];
        for ($i = 0; $i < 4; $i++) {
            $file = null;
            $pathVal = '';
            foreach (PageLocales::all() as $locale) {
                $f = $request->file("product_sections.{$locale}.feature_cards.{$i}.image_file");
                $localeFeatureCards = $sections[$locale]['feature_cards'] ?? [];
                $p = $localeFeatureCards[$i]['image'] ?? '';
                if ($f?->isValid()) {
                    $file = $f;
                    break;
                }
                if (!empty($p)) {
                    $pathVal = $p;
                }
            }
            $currentFeatureImages[$i] = $saveImage($file, $pathVal ?: null, $featureUploadPath);
        }

        $this->deleteRemovedPageImages($existingPartner, $currentPartner, 'images/homepage');
        $content = [];
        foreach (PageLocales::all() as $locale) {
            $s = $sections[$locale] ?? [];
            $featureCards = $this->normalizeProductFeatureCards($s['feature_cards'] ?? []);
            foreach ($featureCards as $index => $card) {
                $featureCards[$index]['image'] = $currentFeatureImages[$index] ?? ($card['image'] ?? '');
            }
            $content[$locale] = [
                'banner_title' => $bannerFields['banner_title'] ?? '',
                'banner_description' => $bannerFields['banner_description'] ?? '',
                'background_image' => $bannerFields['background_image'] ?? '',
                'description' => $s['description'] ?? '',
                'products_title' => $s['products_title'] ?? 'Our Programs',
                'feature_section_title' => trim((string) ($s['feature_section_title'] ?? '')) ?: 'Impact areas',
                'feature_section_description' => trim((string) ($s['feature_section_description'] ?? '')) ?: 'Programs are designed around practical action, local accountability, and visible results.',
                'feature_cards' => $featureCards,
                'partners_title' => $s['partners_title'] ?? 'Our supporters',
                'partner_images' => $currentPartner,
            ];
        }

        $oldEnProduct = $page->getPageContentForLocale('en');
        $this->deleteRemovedPageImages(
            !empty($oldEnProduct['background_image']) ? [(string) $oldEnProduct['background_image']] : [],
            !empty($bannerFields['background_image']) ? [(string) $bannerFields['background_image']] : [],
            'images/banners'
        );
        $oldFeatureImages = [];
        foreach (($oldEnProduct['feature_cards'] ?? []) as $card) {
            if (!empty($card['image'])) {
                $oldFeatureImages[] = (string) $card['image'];
            }
        }
        $this->deleteRemovedPageImages(
            $oldFeatureImages,
            array_values(array_filter($currentFeatureImages)),
            'images/product-features'
        );

        $oldContent = $page->page_content ?? [];
        $page->update(['page_content' => $content]);
        AuditLogService::logEdit(AuditLog::MODULE_PAGE, $page->title . ' (Product sections)', ['page_content' => $oldContent], ['page_content' => $content]);
    }

    private function savePartnerSections(Request $request, Page $page): void
    {
        $uploadPath = 'images/partners';
        $path = public_path($uploadPath);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $sections = $request->input('partner_sections', []);
        if (!is_array($sections)) {
            $sections = [];
        }

        $bannerFields = $this->extractBannerFields($request);
        $bannerFields['background_image'] = $this->storeBannerBackgroundImage(
            $request,
            $bannerFields['background_image'] ?? ($page->getPageContentForLocale('en')['background_image'] ?? '')
        );

        $keep = $request->input('partner_keep_images', []) ?? [];
        $existingPartner = $this->normalizePartnerImages($page->getPageContentForLocale('en')['partner_images'] ?? []);
        $currentPartner = array_values(array_intersect($existingPartner, is_array($keep) ? $keep : [$keep]));

        $heroImageFallback = $page->getPageContentForLocale('en')['partner_feature_image'] ?? '';
        $heroImage = $heroImageFallback;
        foreach (PageLocales::all() as $locale) {
            $heroImageFile = $request->file("partner_sections.{$locale}.partner_feature_image_file");
            if ($heroImageFile?->isValid()) {
                $name = Str::slug(pathinfo($heroImageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $heroImageFile->getClientOriginalExtension();
                $heroImageFile->move(public_path($uploadPath), $name);
                $heroImage = $uploadPath . '/' . $name;
                break;
            }
            if (!empty($sections[$locale]['partner_feature_image'] ?? null)) {
                $heroImage = trim((string) $sections[$locale]['partner_feature_image']);
            }
        }

        $saveImage = function ($file, ?string $fallback) use ($uploadPath) {
            if ($file?->isValid()) {
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $name);
                return $uploadPath . '/' . $name;
            }
            return trim((string) $fallback);
        };

        if ($request->hasFile('partner_image_files')) {
            $seen = [];
            foreach ($request->file('partner_image_files') as $file) {
                if ($file?->isValid()) {
                    $key = $file->getClientOriginalName() . '-' . $file->getSize();
                    if (isset($seen[$key])) {
                        continue;
                    }
                    $seen[$key] = true;
                    $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path($uploadPath), $name);
                    $currentPartner[] = $uploadPath . '/' . $name;
                }
            }
        }

        foreach ($this->validatedStagedPaths($request, 'partner_staged_image_paths', 'images/partners') as $path) {
            $currentPartner[] = $path;
        }

        $this->deleteRemovedPageImages($existingPartner, $currentPartner, 'images/partners');
        $oldHeroImage = $page->getPageContentForLocale('en')['partner_feature_image'] ?? '';
        if (!empty($oldHeroImage) && $oldHeroImage !== $heroImage) {
            $this->deleteRemovedPageImages([(string) $oldHeroImage], [(string) $heroImage], 'images/partners');
        }

        $content = [];
        foreach (PageLocales::all() as $locale) {
            $s = $sections[$locale] ?? [];
            $partnerFeatures = $this->normalizePartnerFeatures($s['partner_features'] ?? []);
            $content[$locale] = [
                'banner_title' => $bannerFields['banner_title'] ?? '',
                'banner_description' => $bannerFields['banner_description'] ?? '',
                'background_image' => $bannerFields['background_image'] ?? '',
                'partners_title' => $s['partners_title'] ?? 'How you can partner',
                'partners_description' => $s['partners_description'] ?? 'We work with schools, donors, corporations, and NGOs to support education programs.',
                'partner_feature_image' => $heroImage,
                'partner_features' => $partnerFeatures,
                'supporters_title' => $s['supporters_title'] ?? 'Our supporters',
                'supporters_description' => $s['supporters_description'] ?? 'Partners and organizations helping grow practical education and community support.',
                'partner_images' => $currentPartner,
            ];
        }

        $oldEnPartner = $page->getPageContentForLocale('en');
        $this->deleteRemovedPageImages(
            !empty($oldEnPartner['background_image']) ? [(string) $oldEnPartner['background_image']] : [],
            !empty($bannerFields['background_image']) ? [(string) $bannerFields['background_image']] : [],
            'images/banners'
        );

        $oldContent = $page->page_content ?? [];
        $page->update(['page_content' => $content]);
        AuditLogService::logEdit(AuditLog::MODULE_PAGE, $page->title . ' (Partner sections)', ['page_content' => $oldContent], ['page_content' => $content]);
    }

    private function saveContactSections(Request $request, Page $page): void
    {
        $sections = $request->input('contact_sections', []);
        if (!is_array($sections)) {
            $sections = [];
        }
        $bannerFields = $this->extractBannerFields($request);

        $defaults = [
            'page_title' => 'Contact Us',
            'page_intro' => "We're here to help. Feel free to reach out through any of the channels below or send us a message directly.",
            'contact_info_title' => 'Contact Information',
            'confirm_open' => 'Open this contact method?',
            'address' => '123 Main Street, Phnom Penh, Cambodia',
            'phone' => '+855 23 123 456',
            'email' => 'info@bandoskomar.org',
            'office_hours' => 'Monday-Friday, 8:00-17:00 (ICT)',
            'form_title' => 'Send Us a Message',
            'form_subtitle' => 'Fill out the form below and we will get back to you as soon as possible.',
            'success_message' => 'Thank you! Your message has been sent and we will respond shortly.',
            'target_email' => 'info@bandoskomar.org',
            'labels' => [
                'full_name' => 'Full Name',
                'email_address' => 'Email Address',
                'message' => 'Message',
                'send_message' => 'Send Message',
            ],
            'placeholders' => [
                'full_name' => 'Your full name',
                'email_address' => 'you@example.com',
                'message' => 'How can we help you?',
            ],
            'messages' => [
                'no_methods' => 'No contact methods are available yet.',
            ],
        ];

        $content = [];
        $methodDefinitions = [
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'whatsapp', 'label' => 'WhatsApp'],
            ['key' => 'telegram', 'label' => 'Telegram'],
            ['key' => 'signal', 'label' => 'Signal'],
            ['key' => 'teams', 'label' => 'Microsoft Teams'],
            ['key' => 'wechat', 'label' => 'WeChat'],
        ];

        foreach (PageLocales::all() as $locale) {
            $s = is_array($sections[$locale] ?? null) ? $sections[$locale] : [];
            $labels = is_array($s['labels'] ?? null) ? $s['labels'] : [];
            $placeholders = is_array($s['placeholders'] ?? null) ? $s['placeholders'] : [];
            $messages = is_array($s['messages'] ?? null) ? $s['messages'] : [];
            $rawMethods = [];
            foreach (is_array($s['contact_methods'] ?? null) ? $s['contact_methods'] : [] as $row) {
                if (is_array($row) && !empty($row['key'])) {
                    $rawMethods[strtolower((string) $row['key'])] = $row;
                }
            }

            $methods = [];
            foreach ($methodDefinitions as $definition) {
                $row = $rawMethods[$definition['key']] ?? [];
                if (!is_array($row)) {
                    $row = [];
                }

                $enabled = filter_var($row['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                $enabled = $enabled ?? false;
                $value = trim((string) ($row['value'] ?? ''));
                $url = trim((string) ($row['url'] ?? ''));
                $resolvedUrl = $this->buildContactMethodUrl($definition['key'], $value, $url);

                $methods[] = [
                    'key' => $definition['key'],
                    'label' => $definition['label'],
                    'value' => $value,
                    'url' => $resolvedUrl,
                    'enabled' => $enabled,
                ];
            }

            $content[$locale] = [
                'banner_title' => $bannerFields['banner_title'] ?? '',
                'banner_description' => $bannerFields['banner_description'] ?? '',
                'page_title' => trim((string) ($s['page_title'] ?? '')) ?: $defaults['page_title'],
                'page_intro' => trim((string) ($s['page_intro'] ?? '')) ?: $defaults['page_intro'],
                'contact_info_title' => trim((string) ($s['contact_info_title'] ?? '')) ?: $defaults['contact_info_title'],
                'confirm_open' => trim((string) ($s['confirm_open'] ?? '')) ?: $defaults['confirm_open'],
                'address' => trim((string) ($s['address'] ?? '')) ?: $defaults['address'],
                'phone' => trim((string) ($s['phone'] ?? '')) ?: $defaults['phone'],
                'email' => trim((string) ($s['email'] ?? '')) ?: $defaults['email'],
                'office_hours' => trim((string) ($s['office_hours'] ?? '')) ?: $defaults['office_hours'],
                'form_title' => trim((string) ($s['form_title'] ?? '')) ?: $defaults['form_title'],
                'form_subtitle' => trim((string) ($s['form_subtitle'] ?? '')) ?: $defaults['form_subtitle'],
                'success_message' => trim((string) ($s['success_message'] ?? '')) ?: $defaults['success_message'],
                'target_email' => trim((string) ($s['target_email'] ?? '')) ?: $defaults['target_email'],
                'labels' => [
                    'full_name' => trim((string) ($labels['full_name'] ?? '')) ?: $defaults['labels']['full_name'],
                    'email_address' => trim((string) ($labels['email_address'] ?? '')) ?: $defaults['labels']['email_address'],
                    'message' => trim((string) ($labels['message'] ?? '')) ?: $defaults['labels']['message'],
                    'send_message' => trim((string) ($labels['send_message'] ?? '')) ?: $defaults['labels']['send_message'],
                ],
                'placeholders' => [
                    'full_name' => trim((string) ($placeholders['full_name'] ?? '')) ?: $defaults['placeholders']['full_name'],
                    'email_address' => trim((string) ($placeholders['email_address'] ?? '')) ?: $defaults['placeholders']['email_address'],
                    'message' => trim((string) ($placeholders['message'] ?? '')) ?: $defaults['placeholders']['message'],
                ],
                'messages' => [
                    'no_methods' => trim((string) ($messages['no_methods'] ?? '')) ?: $defaults['messages']['no_methods'],
                ],
                'contact_methods' => $methods,
            ];
        }

        $oldContent = $page->page_content ?? [];
        $page->update(['page_content' => $content]);
        AuditLogService::logEdit(
            AuditLog::MODULE_PAGE,
            $page->title . ' (Contact sections)',
            ['page_content' => $oldContent],
            ['page_content' => $content]
        );
    }

    private function buildContactMethodUrl(string $key, string $value, string $url = ''): string
    {
        $key = strtolower(trim($key));
        $value = trim($value);
        $url = trim($url);

        if ($url !== '') {
            return $url;
        }

        if ($value === '') {
            return '';
        }

        return match ($key) {
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $value : '',
            'phone' => 'tel:' . preg_replace('/[^0-9+]/', '', $value),
            'whatsapp' => 'https://wa.me/' . preg_replace('/\D+/', '', $value),
            'telegram' => 'https://t.me/' . ltrim(preg_replace('#^https?://t\.me/#i', '', $value), '@'),
            'signal' => 'https://signal.me/#p/' . preg_replace('/\s+/', '', $value),
            'teams' => Str::startsWith($value, ['http://', 'https://']) ? $value : 'https://teams.microsoft.com/l/chat/0/0?users=' . urlencode($value),
            'wechat' => Str::startsWith($value, ['http://', 'https://']) ? $value : 'weixin://dl/chat?'.urlencode($value),
            'website', 'link', 'custom' => filter_var($value, FILTER_VALIDATE_URL) ? $value : '',
            default => filter_var($value, FILTER_VALIDATE_URL) ? $value : '',
        };
    }

    /**
     * Single-file staged upload for multi-image page fields (avoids one huge multipart save).
     */
    public function stagedMediaUpload(Request $request, Page $page)
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'image', 'max:10240'],
            'purpose' => ['required', 'string', 'in:homepage_partner,platform_slider,product_partner,about_strip'],
        ]);

        $uploadPath = match ($validated['purpose']) {
            'homepage_partner', 'product_partner' => 'images/homepage',
            'platform_slider' => 'images/platform',
            'about_strip' => 'images/about-us',
        };

        $dir = public_path($uploadPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $request->file('file');
        if (!$file?->isValid()) {
            return response()->json(['message' => 'Invalid file upload.'], 422);
        }

        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return response()->json(['path' => $uploadPath . '/' . $name]);
    }

    /**
     * @return list<string>
     */
    private function validatedStagedPaths(Request $request, string $field, string $allowedPrefix): array
    {
        $raw = $request->input($field, []);
        if (!is_array($raw)) {
            $raw = $raw !== null && $raw !== '' ? [$raw] : [];
        }
        $out = [];
        foreach ($raw as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }
            if ($this->isValidStagedPublicPath($path, $allowedPrefix)) {
                $out[] = $path;
            }
        }

        return array_values(array_unique($out));
    }

    private function isValidStagedPublicPath(string $path, string $allowedPrefix): bool
    {
        $path = str_replace('\\', '/', trim($path));
        if (str_contains($path, '..')) {
            return false;
        }
        $allowedPrefix = rtrim(str_replace('\\', '/', $allowedPrefix), '/') . '/';
        if (!str_starts_with($path, $allowedPrefix)) {
            return false;
        }

        return is_file(public_path($path));
    }

    /**
     * @return list<string>
     */
    private function collectHomepageStyleImagePaths(array $styles): array
    {
        $paths = [];
        foreach ($styles as $style) {
            if (!is_array($style)) {
                continue;
            }
            $img = trim((string) ($style['image'] ?? ''));
            if ($img !== '') {
                $paths[] = $img;
            }
            foreach ($style['colors'] ?? [] as $c) {
                if (!is_array($c)) {
                    continue;
                }
                $ci = trim((string) ($c['image'] ?? ''));
                if ($ci !== '') {
                    $paths[] = $ci;
                }
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * Delete image files that were removed from the list (no longer kept). Only deletes under allowedPrefix.
     */
    private function deleteRemovedPageImages(array $oldPaths, array $newPaths, string $allowedPrefix): void
    {
        $allowedPrefix = rtrim(str_replace('\\', '/', $allowedPrefix), '/') . '/';
        $kept = array_flip(array_map('strval', $newPaths));

        foreach ($oldPaths as $path) {
            $path = str_replace('\\', '/', trim((string) $path));
            if ($path === '' || isset($kept[$path])) {
                continue;
            }
            if (str_contains($path, '..') || !str_starts_with($path, $allowedPrefix)) {
                continue;
            }
            $full = public_path($path);
            if (is_file($full)) {
                @unlink($full);
            }
        }
    }

    public function destroy(Page $page)
    {
        $title = $page->title;
        $page->delete();

        AuditLogService::logDelete(AuditLog::MODULE_PAGE, $title);

        return redirect()->route('system-management.pages.index')->with('success', 'Page deleted successfully.');
    }
}
