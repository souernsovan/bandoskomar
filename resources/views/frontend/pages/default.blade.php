{{-- Pages created in the admin without a fixed design: banner + the page's own content. --}}
@extends('frontend.layouts.app')

@php
$legacy = $page->getPageContentForLocale();
$content = $page->getContentForLocale();
@endphp

@section('content')
@include('frontend.partials.banner', [
'bannerImage' => $legacy['background_image'] ?? '',
'bannerTitle' => $legacy['banner_title'] ?? '',
])

<section class="sec">
  <div class="wrap">
    @if (!empty($legacy['banner_description']))
    <p class="lead" style="margin-bottom:28px">{{ $legacy['banner_description'] }}</p>
    @endif
    @if (filled($content))
    <div class="prose">{!! $content !!}</div>
    @endif
  </div>
</section>

@include('frontend.partials.give')
@endsection