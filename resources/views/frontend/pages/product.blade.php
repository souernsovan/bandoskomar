@extends('frontend.layouts.app')

@php
    use App\Support\PageContent;

    $topics = collect($c->list('program_topics'))->groupBy(fn ($t) => strtoupper(trim($t['program'])));
@endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap">
    <div class="head center">
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->get('intro_text') }}</p>
    </div>
    @if (count($cards = $c->list('cards')))
    <div class="grid-3">
      @include('frontend.partials.cards', ['cards' => $cards])
    </div>
    @endif
  </div>
</section>

@foreach ($c->list('programs') as $program)
<section class="sec {{ $loop->odd ? 'alt' : '' }}" id="{{ \Illuminate\Support\Str::slug($program['code'] ?: $program['title']) }}">
  <div class="wrap">
    <div class="head" style="max-width:820px">
      <h2>{{ $program['title'] }}</h2>
      <p class="lead">{{ $program['goal'] }}</p>
    </div>
    @php $programTopics = $topics->get(strtoupper(trim($program['code'])), collect()); @endphp
    @if ($programTopics->isNotEmpty())
    <div class="grid-{{ $programTopics->count() % 3 === 0 ? 3 : 2 }} topics">
      @foreach ($programTopics as $topic)
      <article class="card topic">
        @if ($topic['image'] !== '')<img src="{{ PageContent::url($topic['image']) }}" alt="{{ $topic['title'] }}" loading="lazy">@endif
        <h3>{{ $topic['title'] }}</h3>
        @php $lines = array_values(array_filter(array_map('trim', explode("\n", (string) $topic['text'])), 'strlen')); @endphp
        @if (count($lines) > 1)
        <ul class="topic-points">@foreach ($lines as $line)<li>{{ $line }}</li>@endforeach</ul>
        @elseif (count($lines))
        <p>{{ $lines[0] }}</p>
        @endif
      </article>
      @endforeach
    </div>
    @endif
  </div>
</section>
@endforeach

<section class="sec">
  <div class="wrap">
    <div class="head" style="max-width:720px">
      <h2>{{ $c->get('skills_title') }}</h2>
      <p class="lead">{{ $c->html('skills_text') }}</p>
    </div>
    @include('frontend.partials.tiles', ['tiles' => $c->list('skills_tiles')])
  </div>
</section>

<section class="sec alt">
  <div class="wrap">
    <div class="split" style="margin-bottom:56px">
      <div><h2>{{ $c->get('selection_title') }}</h2><p class="lead">{{ $c->html('selection_text') }}</p></div>
      <div class="collage">
        @foreach (array_slice($c->list('selection_images'), 0, 3) as $image)
        <img src="{{ PageContent::url($image['image']) }}" alt="" loading="lazy">
        @endforeach
      </div>
    </div>
    <div class="steps">
      @foreach ($c->list('steps') as $step)
      <div class="step"><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></div>
      @endforeach
    </div>
  </div>
</section>

@if ($c->get('show_projects') && isset($products) && $products->isNotEmpty())
<section class="sec projects">
  <div class="wrap">
    <div class="head"><h2>{{ $c->get('projects_title') }}</h2></div>
    <div class="grid-3">
      @foreach ($products as $product)
      <a class="tile" href="{{ route('frontend.product.detail', $product->slug) }}">
        @if ($product->getImageUrl())<img src="{{ $product->getImageUrl() }}" alt="{{ $product->title }}" loading="lazy">@endif
        <h3>{{ $product->title }}</h3>
        <p>{{ \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 140) }}</p>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@if ($c->get('show_give'))
  @include('frontend.partials.give')
@endif
@endsection
