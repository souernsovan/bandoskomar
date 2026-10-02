{{-- Inner page banner. Expects $page; optional $bannerImage, $bannerTitle. --}}
@php
    $title = ($bannerTitle ?? '') !== '' ? $bannerTitle : $page->getTitleForLocale();
    $image = \App\Support\PageContent::cssUrl($bannerImage ?? '');
@endphp
<section class="banner" @if ($image !== '') style="background-image:url('{{ $image }}')" @endif>
  <div class="wrap">
    <h1>{{ $title }}</h1>
    <p class="crumbs"><a href="{{ route('frontend.home') }}">{{ $site->get('home_label') }}</a> / {{ $page->getTitleForLocale() }}</p>
  </div>
</section>
