<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = $siteName ?? \App\Models\SiteSetting::get('site_name', config('app.name'));
        $page = $page ?? null;
        $currentLocale = app()->getLocale();
        $metaTitle = $page ? ($page->meta_title ?? $page->getTitleForLocale()) : null;
        $metaDescription = $page?->meta_description ?? \App\Models\SiteSetting::get('site_description', '');
        $canonical = $page?->canonical_url ?? url()->current();
        $ogTags = $page?->og_tags ?? [];
    @endphp

    <title>{{ $metaTitle ? $metaTitle . ' - ' . $siteName : $siteName }}</title>
    <meta name="description" content="{{ $metaDescription ?? '' }}">
    <meta name="robots" content="index, follow">

    <link rel="icon" href="{{ asset($siteIconPath ?? \App\Models\SiteSetting::siteIconPath()) }}" type="{{ $siteIconMimeType ?? \App\Models\SiteSetting::siteIconMimeType() }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', $currentLocale) }}">
    <meta property="og:title" content="{{ $ogTags['og_title'] ?? $metaTitle ?? $siteName }}">
    <meta property="og:description" content="{{ $ogTags['og_description'] ?? $metaDescription ?? '' }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:type" content="{{ $ogTags['og_type'] ?? 'website' }}">
    @if(!empty($ogTags['og_image']))
    <meta property="og:image" content="{{ $ogTags['og_image'] }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="{{ !empty($ogTags['og_image']) ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $ogTags['og_title'] ?? $metaTitle ?? $siteName }}">
    <meta name="twitter:description" content="{{ $ogTags['og_description'] ?? $metaDescription ?? '' }}">
    @if(!empty($ogTags['og_image']))
    <meta name="twitter:image" content="{{ $ogTags['og_image'] }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Structured Data (JSON-LD) --}}
    @if($page && $page->structured_data && is_array($page->structured_data ?? []) && count($page->structured_data))
    <script type="application/ld+json">{!! json_encode($page->structured_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

     @vite(['resources/css/frontend/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="fe-body">
    @include('frontend.partials.header')
    <main class="fe-main">
        @php
            $showPageBanner = false;

            if (isset($page) && $page) {
                if (property_exists($page, 'show_banner')) {
                    $showPageBanner = (bool) $page->show_banner;
                } elseif (method_exists($page, 'shouldShowBanner')) {
                    $showPageBanner = (bool) $page->shouldShowBanner();
                }
            }
        @endphp

        @if ($showPageBanner)
            @include('frontend.partials.page-banner', ['page' => $page])
        @endif

        @yield('content')

        @if ($showPageBanner)
            @include('frontend.partials.page-enrichments', ['page' => $page])
        @endif
    </main>
    @include('frontend.partials.footer')
    @stack('scripts')
</body>
</html>
