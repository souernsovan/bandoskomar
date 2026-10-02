@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap split wide-left">
    <div>
      <h2>{{ $c->get('intro_title') }}</h2>
      @foreach ($c->paragraphs('intro_text') as $paragraph)
      <p class="lead">{{ $paragraph }}</p>
      @endforeach
      @if ($c->has('plan_pdf'))
      <a class="btn btn-navy mt" href="{{ $c->media('plan_pdf') }}" target="_blank" rel="noopener">{{ $c->get('download_label') }}</a>
      @endif
    </div>
    @if ($c->has('intro_image'))
    <div class="photo wide"><img src="{{ $c->media('intro_image') }}" alt=""></div>
    @endif
  </div>
</section>

@include('frontend.partials.story', ['blocks' => $c->list('blocks'), 'alt' => true])

@if (count($goals = $c->list('goals')))
<section class="sec">
  <div class="wrap">
    <div class="head center"><h2>{{ $c->get('goals_title') }}</h2></div>
    <div class="grid-{{ count($goals) === 4 ? 4 : 3 }}">
      @include('frontend.partials.cards', ['cards' => $goals])
    </div>
  </div>
</section>
@endif

@if (count($photos = $c->list('photos')))
<section class="sec alt">
  <div class="wrap">@include('frontend.partials.gallery-grid', ['images' => $photos])</div>
</section>
<div class="lightbox" role="dialog" aria-modal="true"><button class="close" aria-label="Close">✕</button><img alt=""></div>
@endif

@if ($c->get('show_give'))
  @include('frontend.partials.give')
@endif
@endsection
