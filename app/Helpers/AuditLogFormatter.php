<?php

namespace App\Helpers;

use App\Support\PageLocales;

class AuditLogFormatter
{
    /** Human-friendly labels for technical keys */
    private static array $keyLabels = [
        'permissions' => 'Permissions',
        'profile_title' => 'Profile title',
        'profile_tagline' => 'Profile tagline',
        'platform_image' => 'Platform image',
        'platform_slider_images' => 'Platform slider images',
        'hero_headline' => 'Hero headline',
        'hero_description' => 'Hero description',
        'hero_image' => 'Hero image',
        'company_title' => 'Company title',
        'company_description' => 'Company description',
        'company_logo' => 'Company logo',
        'value_prop_1_title' => 'Value prop 1 title',
        'value_prop_1_desc' => 'Value prop 1 description',
        'value_prop_2_title' => 'Value prop 2 title',
        'value_prop_2_desc' => 'Value prop 2 description',
        'value_prop_3_title' => 'Value prop 3 title',
        'value_prop_3_desc' => 'Value prop 3 description',
        'capabilities_image' => 'Capabilities image',
        'marketing_title' => 'Marketing title',
        'marketing_description' => 'Marketing description',
        'marketing_image' => 'Marketing image',
        'mobile_title' => 'Mobile section title',
        'mobile_image' => 'Mobile image',
        'mobile_bg' => 'Mobile background',
        'style_title' => 'Style section title',
        'partners_title' => 'Partners section title',
        'features_title' => 'Features heading',
        'features_subtitle' => 'Features subtitle',
        'choose_title' => '"Why Choose" section title',
        'choose_col_1_text' => 'Column 1 text',
        'choose_col_1_image' => 'Column 1 image',
        'choose_col_2_text' => 'Column 2 label',
        'choose_col_2_value' => 'Column 2 value',
        'choose_col_3_text' => 'Column 3 label',
        'choose_col_3_value' => 'Column 3 value',
        'results_subtitle' => 'Results subtitle',
        'results_title' => 'Results heading',
        'results_description' => 'Results description',
        'different_subtitle' => 'Different section subtitle',
        'different_title' => 'Different section heading',
        'different_description' => 'Different section description',
        'different_check' => 'Different section highlight',
        'different_image' => 'Different section image',
        'promise_subtitle' => 'Promise section subtitle',
        'promise_title' => 'Promise section heading',
        'promise_description' => 'Promise section description',
        'promise_check' => 'Promise section highlight',
        'promise_image' => 'Promise section image',
        'solutions_subtitle' => 'Solutions subtitle',
        'solutions_title' => 'Solutions heading',
        'solutions_description' => 'Solutions description',
        'interests_title' => 'Interests heading',
        'ready_title' => 'CTA heading',
        'features' => 'Platform features list',
        'partner_images' => 'Partner logos',
        'styles' => 'Style Showcase',
        'color_choice_title' => 'Color choice title',
    ];

    /** Keys that store image paths (single path or array of paths) */
    private static array $imageKeys = [
        'platform_image', 'platform_slider_images', 'choose_col_1_image', 'hero_image', 'company_logo',
        'capabilities_image', 'marketing_image', 'mobile_image', 'mobile_bg',
        'different_image', 'promise_image', 'og_image', 'image',
        'partner_images',
    ];

    public static function isImageKey(string $key): bool
    {
        if (in_array($key, self::$imageKeys, true)) {
            return true;
        }
        return str_ends_with($key, '_image') || str_ends_with($key, '_images') || str_ends_with($key, '_logo') || str_ends_with($key, '_bg');
    }

    /**
     * Check if a value looks like an image path.
     */
    public static function isImagePath($value): bool
    {
        if (!is_string($value) || $value === '') {
            return false;
        }
        $lower = strtolower($value);
        return str_starts_with($value, 'images/') || str_starts_with($value, '/') || str_starts_with($value, 'http')
            || preg_match('/\.(jpg|jpeg|png|gif|webp|svg)(\?|$)/i', $lower);
    }

    /**
     * Format image path(s) as HTML img element(s).
     */
    public static function formatImage($value, bool $thumb = false): string
    {
        if (is_array($value)) {
            $imgs = [];
            foreach ($value as $path) {
                if (is_string($path) && self::isImagePath($path)) {
                    $url = str_starts_with($path, 'http') ? $path : url($path);
                    $cls = $thumb ? 'audit-img audit-img-thumb' : 'audit-img';
                    $imgs[] = '<img src="' . e($url) . '" alt="" class="' . $cls . '">';
                }
            }
            if (empty($imgs)) {
                return '<span class="text-muted">(empty)</span>';
            }
            return '<div class="audit-imgs">' . implode(' ', $imgs) . '</div>';
        }

        if (is_string($value) && self::isImagePath($value)) {
            $url = str_starts_with($value, 'http') ? $value : url($value);
            $cls = $thumb ? 'audit-img audit-img-thumb' : 'audit-img';
            return '<img src="' . e($url) . '" alt="" class="' . $cls . '">';
        }

        return '';
    }

    public static function getHumanLabel(string $key): string
    {
        return self::$keyLabels[$key] ?? str_replace('_', ' ', ucfirst($key));
    }

    /**
     * Format an audit log value for human-readable display based on the key.
     * Returns ['formatted' => true, 'html' => '...'] when custom formatting applies,
     * or ['formatted' => false] to use default JSON/text display.
     */
    public static function formatValue(string $key, $value): array
    {
        if ($value === null) {
            return ['formatted' => false];
        }

        // Image fields: display as img elements
        if (self::isImageKey($key)) {
            if (is_string($value) && self::isImagePath($value)) {
                return ['formatted' => true, 'html' => self::formatImage($value)];
            }
            if (is_string($value) && trim($value) === '') {
                return ['formatted' => true, 'html' => '<span class="text-muted">(no image)</span>'];
            }
            if (($key === 'partner_images' || $key === 'partner_image') && is_array($value)) {
                return ['formatted' => true, 'html' => self::formatImage($value, true)];
            }
        }

        // Platform features: [{title, color, icon}, ...]
        if ($key === 'features' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatPlatformFeatures($value),
            ];
        }

        // About Us solution_cards: [{title, description, icon}, ...]
        if ($key === 'solution_cards' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatCardList($value, 'title', 'description', 'icon'),
            ];
        }

        // About Us interest_cards: [{title, description, icon}, ...]
        if ($key === 'interest_cards' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatCardList($value, 'title', 'description', 'icon'),
            ];
        }

        // Style Showcase: [{image, colors: [{name, hex, image}, ...]}, ...]
        if ($key === 'styles' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatStyles($value),
            ];
        }

        // page_content: may be locale-keyed { en: {...}, km: {...} } or flat
        if ($key === 'page_content' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatPageContent($value),
            ];
        }

        // translations: { en: { title, content }, km: { title, content }, ... }
        if ($key === 'translations' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatTranslations($value),
            ];
        }

        // Permissions diff: {added: [...]} or {removed: [...]} or plain array
        if ($key === 'permissions' && is_array($value)) {
            return [
                'formatted' => true,
                'html' => self::formatPermissions($value),
            ];
        }

        return ['formatted' => false];
    }

    /** Get display name for locale code */
    public static function getLocaleLabel(string $locale): string
    {
        $labels = PageLocales::labels();
        return $labels[$locale]['name'] ?? strtoupper($locale);
    }

    /** Format translations (title/content per locale) for readable display */
    private static function formatTranslations(array $translations): string
    {
        $out = [];
        foreach ($translations as $locale => $data) {
            if (!is_array($data)) {
                continue;
            }
            $name = self::getLocaleLabel($locale);
            $title = trim($data['title'] ?? '');
            $content = trim($data['content'] ?? '');
            $lines = [];
            if ($title !== '') {
                $lines[] = '<div class="audit-translation-item"><strong>Title:</strong> ' . e($title) . '</div>';
            }
            if ($content !== '') {
                $preview = mb_strlen($content) > 200 ? mb_substr($content, 0, 200) . '…' : $content;
                $lines[] = '<div class="audit-translation-item audit-translation-content"><strong>Content:</strong> ' . e($preview) . '</div>';
            }
            if (empty($lines)) {
                $lines[] = '<span class="text-muted">(empty)</span>';
            }
            $out[] = '<div class="audit-locale-block"><span class="audit-locale-badge" data-locale="' . e($locale) . '">' . e($name) . '</span>' . implode('', $lines) . '</div>';
        }
        if (empty($out)) {
            return '<span class="text-muted">(no translations)</span>';
        }
        return '<div class="audit-translations-formatted">' . implode('', $out) . '</div>';
    }

    private static function formatPlatformFeatures(array $items): string
    {
        if (empty($items)) {
            return '<span class="text-muted">(empty)</span>';
        }

        $lines = [];
        foreach (array_values($items) as $i => $item) {
            if (!is_array($item)) {
                $lines[] = '<span class="audit-feature-num">' . ($i + 1) . '.</span> ' . e((string) $item);
                continue;
            }
            $title = trim($item['title'] ?? '');
            $lines[] = '<span class="audit-feature-num">' . ($i + 1) . '.</span> ' . ($title ? e($title) : '<span class="text-muted">(empty)</span>');
        }

        return '<ul class="audit-list audit-list-features">' . implode('', array_map(fn ($l) => '<li>' . $l . '</li>', $lines)) . '</ul>';
    }

    private static function formatStyles(array $styles): string
    {
        if (empty($styles)) {
            return '<span class="text-muted">(no styles)</span>';
        }

        $out = [];
        foreach ($styles as $si => $style) {
            $num = str_pad((int) $si + 1, 2, '0', STR_PAD_LEFT);

            if ($style === null) {
                $out[] = '<div class="audit-style-card"><div class="audit-style-header">Style ' . $num . ' <span style="color:#ef4444;font-size:0.7rem;font-weight:400;">removed</span></div></div>';
                continue;
            }
            if (!is_array($style)) {
                continue;
            }

            $html = '<div class="audit-style-card">';
            $html .= '<div class="audit-style-header">Style ' . $num . '</div>';

            if (array_key_exists('image', $style)) {
                $img = $style['image'] ?? '';
                if (is_string($img) && self::isImagePath($img)) {
                    $url = str_starts_with($img, 'http') ? $img : url($img);
                    $html .= '<div class="audit-style-field"><span class="audit-style-field-label">Image</span> ';
                    $html .= '<img src="' . e($url) . '" alt="" class="audit-img audit-img-thumb"></div>';
                } else {
                    $html .= '<div class="audit-style-field"><span class="audit-style-field-label">Image</span> <span class="text-muted">—</span></div>';
                }
            }

            $colors = $style['colors'] ?? [];
            if (is_array($colors) && !empty($colors)) {
                $html .= '<div class="audit-style-colors">';
                foreach ($colors as $ci => $color) {
                    if ($color === null) {
                        $html .= '<div class="audit-color-entry audit-color-removed">Color ' . ((int) $ci + 1) . ' <span style="color:#ef4444;">removed</span></div>';
                        continue;
                    }
                    if (!is_array($color)) {
                        continue;
                    }
                    $name = $color['name'] ?? '';
                    $hex = $color['hex'] ?? '';
                    $cImg = $color['image'] ?? '';

                    $html .= '<div class="audit-color-entry">';
                    if ($hex) {
                        $html .= '<span class="audit-color-swatch" style="background:' . e($hex) . '"></span>';
                    }
                    $html .= '<span class="audit-color-name">' . e($name ?: '—') . '</span>';
                    if ($hex) {
                        $html .= '<span class="audit-color-hex">' . e($hex) . '</span>';
                    }
                    if (is_string($cImg) && self::isImagePath($cImg)) {
                        $cUrl = str_starts_with($cImg, 'http') ? $cImg : url($cImg);
                        $html .= '<img src="' . e($cUrl) . '" alt="" class="audit-img audit-img-thumb-sm">';
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';
            }

            $html .= '</div>';
            $out[] = $html;
        }

        return '<div class="audit-styles-formatted">' . implode('', $out) . '</div>';
    }

    private static function formatCardList(array $items, string $titleKey, string $descKey, string $iconKey): string
    {
        if (empty($items)) {
            return '<span class="text-muted">(empty)</span>';
        }

        $lines = [];
        foreach (array_values($items) as $i => $item) {
            if (!is_array($item)) {
                $lines[] = '<span class="audit-feature-num">' . ($i + 1) . '.</span> ' . e((string) $item);
                continue;
            }
            $title = trim($item[$titleKey] ?? '');
            $desc = trim($item[$descKey] ?? '');
            $line = '<span class="audit-feature-num">' . ($i + 1) . '.</span> ';
            $line .= $title ? '<strong>' . e($title) . '</strong>' : '<span class="text-muted">(no title)</span>';
            if ($desc) {
                $line .= '<div class="audit-card-desc">' . e($desc) . '</div>';
            }
            $lines[] = $line;
        }

        return '<ul class="audit-list audit-list-cards">' . implode('', array_map(fn ($l) => '<li>' . $l . '</li>', $lines)) . '</ul>';
    }

    /**
     * Format permissions for audit log display.
     * Handles both diff format {added:[...]} / {removed:[...]} and plain arrays (legacy).
     */
    private static function formatPermissions(array $value): string
    {
        $permissionLabels = collect(config('permissions', []))
            ->flatMap(fn ($group) => $group)
            ->all();

        $formatList = function (array $items, string $badgeClass) use ($permissionLabels): string {
            if (empty($items)) {
                return '<span class="text-muted">None</span>';
            }
            $out = '<div class="audit-permission-list">';
            foreach ($items as $perm) {
                $label = $permissionLabels[$perm] ?? str_replace(['.', '_'], ' ', ucfirst($perm));
                $out .= '<span class="audit-permission-badge ' . $badgeClass . '">' . e($label) . '</span>';
            }
            $out .= '</div>';
            return $out;
        };

        // New diff format: {added: [...]} or {removed: [...]}
        if (isset($value['added'])) {
            $items = is_array($value['added']) ? $value['added'] : [];
            if (empty($items)) {
                return '<span class="text-muted">No permissions added</span>';
            }
            return '<div class="audit-permission-section">'
                . '<span class="audit-permission-section-label audit-permission-added-label">Added</span>'
                . $formatList($items, 'audit-permission-added')
                . '</div>';
        }

        if (isset($value['removed'])) {
            $items = is_array($value['removed']) ? $value['removed'] : [];
            if (empty($items)) {
                return '<span class="text-muted">No permissions removed</span>';
            }
            return '<div class="audit-permission-section">'
                . '<span class="audit-permission-section-label audit-permission-removed-label">Removed</span>'
                . $formatList($items, 'audit-permission-removed')
                . '</div>';
        }

        // Legacy: plain array of permission keys
        return $formatList($value, 'audit-permission-badge-default');
    }

    private static function formatPageContent(array $content): string
    {
        $locales = PageLocales::all();
        $isLocaleKeyed = false;
        foreach (array_keys($content) as $key) {
            if (in_array($key, $locales, true) || $key === '_global') {
                $isLocaleKeyed = true;
                break;
            }
        }

        if ($isLocaleKeyed) {
            return self::formatLocaleKeyedPageContent($content);
        }

        return self::formatSingleLocalePageContent($content);
    }

    /** Format page_content when structured as { en: {...}, km: {...}, _global: {...} } */
    private static function formatLocaleKeyedPageContent(array $content): string
    {
        $out = [];
        $order = ['_global' => 0];
        $i = 1;
        foreach (PageLocales::all() as $l) {
            $order[$l] = $i++;
        }
        uksort($content, fn ($a, $b) => ($order[$a] ?? 999) <=> ($order[$b] ?? 999));
        foreach ($content as $locale => $localeContent) {
            if (!is_array($localeContent)) {
                continue;
            }
            if ($locale === '_global') {
                $name = 'Global content';
                $formatted = self::formatSingleLocalePageContent($localeContent);
                $out[] = '<div class="audit-locale-group audit-locale-global"><div class="audit-locale-header"></div><div class="audit-locale-body">' . $formatted . '</div></div>';
                continue;
            }
            $name = self::getLocaleLabel($locale);
            $formatted = self::formatSingleLocalePageContent($localeContent);
            $out[] = '<div class="audit-locale-group"><div class="audit-locale-header"><span class="audit-locale-badge">' . e($name) . ' (' . e($locale) . ')</span></div><div class="audit-locale-body">' . $formatted . '</div></div>';
        }
        if (empty($out)) {
            return '<pre class="comparison-json">' . e(json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) . '</pre>';
        }
        return '<div class="audit-content-multilang">' . implode('', $out) . '</div>';
    }

    /** Format a single locale's page content (flat structure) */
    private static function formatSingleLocalePageContent(array $content): string
    {
        $sections = [];

        if (isset($content['features']) && is_array($content['features'])) {
            $sections[] = '<div class="audit-section"><span class="audit-section-label">Platform features list</span>' . self::formatPlatformFeatures($content['features']) . '</div>';
        }
        if (isset($content['solution_cards']) && is_array($content['solution_cards'])) {
            $sections[] = '<div class="audit-section"><span class="audit-section-label">Solution cards</span>' . self::formatCardList($content['solution_cards'], 'title', 'description', 'icon') . '</div>';
        }
        if (isset($content['interest_cards']) && is_array($content['interest_cards'])) {
            $sections[] = '<div class="audit-section"><span class="audit-section-label">Interest cards</span>' . self::formatCardList($content['interest_cards'], 'title', 'description', 'icon') . '</div>';
        }

        // Styles (array of style objects with colors)
        if (isset($content['styles']) && is_array($content['styles'])) {
            $sections[] = '<div class="audit-section"><span class="audit-section-label">' . e(self::getHumanLabel('styles')) . '</span>' . self::formatStyles($content['styles']) . '</div>';
        }

        // Partner images (array of paths)
        if (isset($content['partner_images']) && is_array($content['partner_images'])) {
            $sections[] = '<div class="audit-section"><span class="audit-section-label">' . e(self::getHumanLabel('partner_images')) . '</span>' . self::formatImage($content['partner_images'], true) . '</div>';
        }

        // Platform slider (array of paths)
        if (isset($content['platform_slider_images']) && is_array($content['platform_slider_images'])) {
            $sections[] = '<div class="audit-section"><span class="audit-section-label">' . e(self::getHumanLabel('platform_slider_images')) . '</span>' . self::formatImage($content['platform_slider_images'], true) . '</div>';
        }

        // Simple key-value pairs (including images)
        $skip = ['features', 'solution_cards', 'interest_cards', 'partner_images', 'platform_slider_images', 'styles'];
        foreach ($content as $k => $v) {
            if (in_array($k, $skip, true)) {
                continue;
            }
            if (is_scalar($v) && (string) $v !== '') {
                if (self::isImageKey($k) && self::isImagePath((string) $v)) {
                    $sections[] = '<div class="audit-section"><span class="audit-section-label">' . e(self::getHumanLabel($k)) . '</span>' . self::formatImage((string) $v) . '</div>';
                } else {
                    $sections[] = '<div class="audit-section"><span class="audit-section-label">' . e(self::getHumanLabel($k)) . '</span><span class="audit-section-value">' . e((string) $v) . '</span></div>';
                }
            }
        }

        if (empty($sections)) {
            return '<pre class="comparison-json audit-json-compact">' . e(json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) . '</pre>';
        }

        return '<div class="audit-content-formatted">' . implode('', $sections) . '</div>';
    }
}
