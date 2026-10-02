@extends('frontend.layouts.app')

@php use App\Support\PageContent; @endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap">
    <div class="head center">
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->html('intro_text') }}</p>
    </div>
    <div class="timeline">
      @foreach ($c->list('timeline') as $event)
      <div class="t-item">
        @if ($event['year'] !== '')<span class="t-year">{{ $event['year'] }}</span>@endif
        <h3>{{ $event['title'] }}</h3>
        <div class="t-body">
          @if ($event['image'] !== '')<img src="{{ PageContent::url($event['image']) }}" alt="{{ $event['title'] }}" loading="lazy">@endif
          <p>{{ $event['text'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

@if (count($stats = $c->list('legacy_stats')))
<section class="sec alt">
  <div class="wrap">
    <div class="head center"><h2>{{ $c->get('legacy_title') }}</h2>@if ($c->has('legacy_text'))<p class="lead">{{ $c->get('legacy_text') }}</p>@endif</div>
    <div class="grid-{{ count($stats) === 2 ? 2 : 3 }} legacy">
      @foreach ($stats as $stat)
      <div class="card center"><strong class="legacy-num">{{ $stat['number'] }}</strong><h3>{{ $stat['label'] }}</h3><p>{{ $stat['text'] }}</p></div>
      @endforeach
    </div>
  </div>
</section>
@endif

@if (count($photos = $c->list('gallery')))
<section class="sec">
  <div class="wrap">@include('frontend.partials.gallery-grid', ['images' => $photos])</div>
</section>
<div class="lightbox" role="dialog" aria-modal="true"><button class="close" aria-label="Close">✕</button><img alt=""></div>
@endif

@if ($c->get('show_give'))
  @include('frontend.partials.give')
@endif
@endsection
