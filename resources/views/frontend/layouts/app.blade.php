@php
    use App\Support\PageContent;

    $page = $page ?? null;
    $currentLocale = app()->getLocale();
    $siteTitle = $site->get('site_title');
    $pageTitle = $page ? $page->getTitleForLocale() : null;
    $metaTitle = ($page->meta_title ?? null) ?: ($pageTitle ? $pageTitle . ' | ' . $siteTitle : $siteTitle);
    $metaDescription = ($page->meta_description ?? null) ?: $site->get('meta_description');
    $canonical = ($page->canonical_url ?? null) ?: url()->current();
    $ogTags = $page->og_tags ?? [];
    $structuredData = $page->structured_data ?? null;
    $isDonatePage = ($page->slug ?? null) === 'donate';
    $assetVersion = fn (string $path) => asset($path) . '?v=' . (@filemtime(public_path($path)) ?: '1');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="theme-color" content="#1E2A6B">
<link rel="icon" href="{{ asset(\App\Models\SiteSetting::siteIconPath()) }}" type="{{ \App\Models\SiteSetting::siteIconMimeType() }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:site_name" content="{{ $siteTitle }}">
<meta property="og:locale" content="{{ str_replace('-', '_', $currentLocale) }}">
<meta property="og:title" content="{{ $ogTags['og_title'] ?? $metaTitle }}">
<meta property="og:description" content="{{ $ogTags['og_description'] ?? $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:type" content="{{ $ogTags['og_type'] ?? 'website' }}">
@if (!empty($ogTags['og_image']))
<meta property="og:image" content="{{ $ogTags['og_image'] }}">
@endif
<meta name="twitter:card" content="{{ !empty($ogTags['og_image']) ? 'summary_large_image' : 'summary' }}">
@if (is_array($structuredData) && count($structuredData))
<script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ $assetVersion('assets/css/style.css') }}">
@stack('head')
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
@include('frontend.partials.header')
<main id="main">
@yield('content')
</main>
@include('frontend.partials.footer')

<a class="float-donate" href="{{ route('frontend.donate') }}" aria-label="{{ $site->get('donate_label') }}"><span class="heart">♥</span><span class="fd-label">{{ $site->get('donate_label') }}</span></a>

@if ($site->get('popup_enabled') && !$isDonatePage)
<div class="popup" role="dialog" aria-modal="true" aria-label="{{ $site->get('donate_label') }}" data-delay="{{ (float) $site->get('popup_delay') }}">
  <div class="box">
    <button class="x" aria-label="Close">✕</button>
    @if ($site->has('popup_image'))
    <img src="{{ $site->media('popup_image') }}" alt="" loading="lazy">
    @endif
    <div class="in">
      <h3>{{ $site->get('popup_title') }}</h3>
      <p>{{ $site->get('popup_text') }}</p>
      @if (count($popupAmounts = $site->list('popup_amounts')))
      <div class="row-btns">
        @foreach ($popupAmounts as $item)
        <a href="{{ route('frontend.donate', ['amount' => $item['amount']]) }}">{{ PageContent::money($item['amount']) }}</a>
        @endforeach
      </div>
      @endif
      <a class="btn btn-orange" href="{{ route('frontend.donate') }}" style="width:100%">{{ $site->get('donate_label') }}</a>
      <button class="later">{{ $site->get('popup_later') }}</button>
    </div>
  </div>
</div>
@endif

<script src="{{ $assetVersion('assets/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
