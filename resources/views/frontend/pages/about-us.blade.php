@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap split wide-left">
    <div>
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->html('intro_text') }}</p>
    </div>
    @if ($c->has('intro_image'))
    <div class="photo tall"><img src="{{ $c->media('intro_image') }}" alt="{{ $c->get('intro_title') }}"></div>
    @endif
  </div>
</section>

@if (count($cards = $c->list('cards')))
<section class="sec alt">
  <div class="wrap">
    @if ($c->has('foundation_title'))<div class="head center">
      <h2>{{ $c->get('foundation_title') }}</h2>
    </div>@endif
    <div class="grid-{{ count($cards) === 2 ? 2 : 3 }}">
      @include('frontend.partials.cards', ['cards' => $cards])
    </div>
  </div>
</section>
@endif

@if (count($values = $c->list('values')))
<section class="sec">
  <div class="wrap">
    <div class="head center">
      <h2>{{ $c->get('values_title') }}</h2>@if ($c->has('values_text'))<p class="lead">{{ $c->get('values_text') }}</p>@endif
    </div>
    <div class="grid-3">
      @foreach ($values as $value)
      <div class="card">
        <h3>{{ $value['title'] }}</h3>
        <p>{{ $value['text'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@include('frontend.partials.story', ['blocks' => $c->list('story'), 'alt' => true])

<section class="sec">
  <div class="wrap split">
    @if ($c->has('context_image'))
    <div class="photo wide"><img src="{{ $c->media('context_image') }}" alt="" loading="lazy"></div>
    @endif
    <div>
      <h2>{{ $c->get('context_title') }}</h2>
      <ul class="checks">
        @foreach ($c->list('context_points') as $point)
        <li>{{ $point['text'] }}</li>
        @endforeach
      </ul>
    </div>
  </div>
</section>

@if ($c->has('goal_title') || count($c->list('goal_tiles')))
<section class="sec alt">
  <div class="wrap">
    <div class="head center">
      <h2>{{ $c->get('goal_title') }}</h2>@if ($c->has('goal_text'))<p class="lead">{{ $c->get('goal_text') }}</p>@endif
    </div>
    @include('frontend.partials.tiles', ['tiles' => $c->list('goal_tiles'), 'cols' => 3])
  </div>
</section>
@endif

@if ($c->get('show_partners'))
@include('frontend.partials.partner-logos', ['title' => $c->get('partners_title')])
@endif

@if ($c->get('show_give'))
@include('frontend.partials.give')
@endif
@endsection