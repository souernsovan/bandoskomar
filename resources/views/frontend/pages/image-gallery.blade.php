@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

@php
    $images = $c->list('images');
    $usedCategories = array_filter(array_unique(array_column($images, 'category')), 'strlen');
@endphp

<section class="sec">
  <div class="wrap">
    <h2>{{ $c->get('title') }}</h2>
    @if (count($usedCategories) > 1)
    <div class="filters">
      <button class="active" data-filter="all">{{ $c->get('all_label') }}</button>
      @foreach (['education', 'lifeskills', 'community'] as $category)
        @if (in_array($category, $usedCategories, true))
        <button data-filter="{{ $category }}">{{ $c->get($category . '_label') }}</button>
        @endif
      @endforeach
    </div>
    @endif
    @include('frontend.partials.gallery-grid', ['images' => $images])
  </div>
</section>
<div class="lightbox" role="dialog" aria-modal="true"><button class="close" aria-label="Close">✕</button><img alt=""></div>
@endsection
