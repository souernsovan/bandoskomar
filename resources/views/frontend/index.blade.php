@extends('frontend.layouts.app')

@php use App\Support\PageContent; @endphp

@section('content')
<section class="hero">
  @foreach ($c->list('hero_slides') as $slide)
  <div class="slide {{ $loop->first ? 'active' : '' }}"><img src="{{ PageContent::url($slide['image']) }}" alt="" @unless ($loop->first) loading="lazy" @endunless></div>
  @endforeach
  <div class="hero-text"><div class="wrap"><div class="inner">
    <h1>@if ($c->has('hero_kicker'))<span class="kicker">{{ $c->get('hero_kicker') }}</span>@endif{{ $c->get('hero_title') }}</h1>
    @if ($c->has('hero_text'))<p>{{ $c->html('hero_text') }}</p>@endif
    <div class="hero-actions">
      <a class="btn btn-orange" href="{{ route('frontend.donate') }}"><span class="heart">♥</span> {{ $site->get('donate_label') }}</a>
      @if ($c->has('hero_button_2_label'))
      <a class="btn btn-light" href="{{ $c->link('hero_button_2_url') }}">{{ $c->get('hero_button_2_label') }}</a>
      @endif
    </div>
  </div></div></div>
  <button class="arrow prev" aria-label="Previous slide">❮</button>
  <button class="arrow next" aria-label="Next slide">❯</button>
  <div class="dots"></div>
</section>

@if (count($stats = $c->list('stats')))
<section class="stats">
  <div class="wrap">
    @foreach ($stats as $stat)
    @php $plain = filter_var($stat['plain'], FILTER_VALIDATE_BOOLEAN); @endphp
    <div><strong data-count="{{ (float) $stat['number'] }}" @if ($plain) data-plain="1" @endif data-suffix="{{ $stat['suffix'] }}">{{ $plain ? $stat['number'] : number_format((float) $stat['number']) }}{{ $stat['suffix'] }}</strong><span>{{ $stat['label'] }}</span></div>
    @endforeach
  </div>
</section>
@endif

<section class="sec problem">
  <div class="wrap split">
    <figure class="photo tall"><img src="{{ $c->media('problem_image') }}" alt="" loading="lazy">@if ($c->has('problem_caption'))<figcaption>{{ $c->get('problem_caption') }}</figcaption>@endif</figure>
    <div>
      <h2>{{ $c->get('problem_title') }}</h2>
      <p class="lead">{{ $c->html('problem_text') }}</p>
      @if (count($points = $c->list('problem_points')))
      <ul class="need-list">
        @foreach ($points as $point)
        <li><b>{{ $loop->iteration }}</b><span>{{ $point['text'] }}</span></li>
        @endforeach
      </ul>
      @endif
      @if ($c->has('problem_button'))
      <a class="btn btn-orange mt" href="{{ route('frontend.donate') }}">{{ $c->get('problem_button') }}</a>
      @endif
    </div>
  </div>
</section>

@if (count($gifts = $c->list('gifts')))
<section class="sec">
  <div class="wrap">
    <div class="head center">
      <h2>{{ $c->get('gifts_title') }}</h2>
      <p class="lead">{{ $c->get('gifts_text') }}</p>
    </div>
    <div class="grid-4">
      @foreach ($gifts as $gift)
      @php $featured = filter_var($gift['featured'], FILTER_VALIDATE_BOOLEAN); @endphp
      <div class="gift {{ $featured ? 'featured' : '' }}">
        @if ($gift['image'] !== '')<img src="{{ PageContent::url($gift['image']) }}" alt="" loading="lazy">@endif
        <div class="body">
          @if ($featured && $gift['badge'] !== '')<span class="badge">{{ $gift['badge'] }}</span>@endif
          <span class="amt">{{ PageContent::money($gift['amount']) }}</span>
          <p>{{ $gift['text'] }}</p>
          <a class="btn {{ $featured ? 'btn-orange' : 'btn-line' }}" href="{{ route('frontend.donate', ['amount' => $gift['amount']]) }}">{{ $gift['button'] }}</a>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="sec alt">
  <div class="wrap {{ $c->get('show_campaign') ? 'split wide-left' : '' }}">
    <div>
      <h2>{{ $c->get('about_title') }}</h2>
      <p class="lead">{{ $c->html('about_text') }}</p>
      @if ($c->has('about_button'))
      <a class="btn btn-line mt" href="{{ $c->link('about_button_url') }}">{{ $c->get('about_button') }}</a>
      @endif
    </div>
    @if ($c->get('show_campaign'))
      @include('frontend.partials.campaign', ['button' => true])
    @endif
  </div>
</section>

<section class="sec">
  <div class="wrap split">
    <div class="photo wide"><img src="{{ $c->media('context_image') }}" alt="" loading="lazy"></div>
    <div><h2>{{ $c->get('context_title') }}</h2><ul class="checks">
      @foreach ($c->list('context_points') as $point)
      <li>{{ $point['text'] }}</li>
      @endforeach
    </ul></div>
  </div>
</section>

<section class="sec alt">
  <div class="wrap split">
    <div><h2>{{ $c->get('selection_title') }}</h2><p class="lead">{{ $c->html('selection_text') }}</p>
      @if ($c->has('selection_quote'))<p class="quote mt">{{ $c->get('selection_quote') }}</p>@endif</div>
    <div class="collage">
      @foreach (array_slice($c->list('selection_images'), 0, 3) as $image)
      <img src="{{ PageContent::url($image['image']) }}" alt="" loading="lazy">
      @endforeach
    </div>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="head" style="max-width:720px">
      <h2>{{ $c->get('skills_title') }}</h2>
      <p class="lead">{{ $c->html('skills_text') }}</p>
    </div>
    @include('frontend.partials.tiles', ['tiles' => $c->list('skills_tiles')])
  </div>
</section>

@if (count($activities = $c->list('activities_images')))
<section class="sec alt">
  <div class="wrap">
    <h2>{{ $c->get('activities_title') }}</h2>
    @include('frontend.partials.gallery-grid', ['images' => $activities])
    @if ($c->has('activities_button'))
    <div class="center mt"><a class="btn btn-navy" href="{{ $c->link('activities_button_url') }}">{{ $c->get('activities_button') }}</a></div>
    @endif
  </div>
</section>
@endif

<section class="final-cta" style="background-image:url('{{ PageContent::cssUrl($c->get('cta_image')) }}')">
  <div class="wrap">
    <h2>{{ $c->get('cta_title') }}</h2>
    <p>{{ $c->get('cta_text') }}</p>
    <a class="btn btn-orange" href="{{ route('frontend.donate') }}"><span class="heart">♥</span> {{ $c->get('cta_button') }}</a>
    <div class="trust mt" style="color:#C6CCEA">
      @if ($c->has('cta_trust_1'))<span>@include('frontend.partials.icon', ['name' => 'mail']) {{ $c->get('cta_trust_1') }}</span>@endif
      @if ($c->has('cta_trust_2'))<span>@include('frontend.partials.icon', ['name' => 'chart']) {{ $c->get('cta_trust_2') }}</span>@endif
    </div>
  </div>
</section>
<div class="lightbox" role="dialog" aria-modal="true"><button class="close" aria-label="Close">✕</button><img alt=""></div>
@endsection
