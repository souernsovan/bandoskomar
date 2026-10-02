@extends('frontend.layouts.app')

@php use App\Support\PageContent; @endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap">
    <div class="head"><h2>{{ $c->get('intro_title') }}</h2>@if ($c->has('intro_text'))<p class="lead">{{ $c->get('intro_text') }}</p>@endif</div>
    <div class="grid-4 partners">
      @foreach ($c->list('partners') as $partner)
      <div class="card partner">
        @if ($partner['logo'] !== '')
        <img class="logo-img" src="{{ PageContent::url($partner['logo']) }}" alt="{{ $partner['title'] }}" loading="lazy">
        @else
        <div class="mono">{{ $partner['mono'] !== '' ? $partner['mono'] : PageContent::initials($partner['title']) }}</div>
        @endif
        <h3>@if ($partner['url'] !== '')<a href="{{ PageContent::href($partner['url']) }}" target="_blank" rel="noopener">{{ $partner['title'] }}</a>@else {{ $partner['title'] }} @endif</h3>
        @if ($partner['text'] !== '')<p>{{ $partner['text'] }}</p>@endif
      </div>
      @endforeach
    </div>
    @if ($c->has('button_label'))
    <div class="center mt"><a class="btn btn-orange" href="{{ $c->link('button_url') }}">{{ $c->get('button_label') }}</a></div>
    @endif
  </div>
</section>

@if (count($photos = $c->list('activity_images')))
<section class="sec alt">
  <div class="wrap">
    <h2>{{ $c->get('activity_title') }}</h2>
    @include('frontend.partials.gallery-grid', ['images' => $photos])
  </div>
</section>
<div class="lightbox" role="dialog" aria-modal="true"><button class="close" aria-label="Close">✕</button><img alt=""></div>
@endif
@endsection
