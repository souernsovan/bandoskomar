@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap">
    <div class="head">
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->get('intro_text') }}</p>
    </div>
    <div class="grid-4">
      @foreach ($c->list('reports') as $report)
      <div class="card report">
        <div class="cover"><span>{{ $c->get('cover_label') }}</span><strong>{{ $report['year'] }}</strong></div>
        <h3>{{ $report['title'] }}</h3>
        <p>{{ $report['text'] }}</p>
        @if ($report['pdf'] !== '')
        <a class="btn btn-navy" href="{{ \App\Support\PageContent::url($report['pdf']) }}" target="_blank" rel="noopener">{{ $c->get('download_label') }}</a>
        @else
        <span class="btn btn-navy" aria-disabled="true">{{ $c->get('soon_label') }}</span>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</section>

@if ($c->get('show_give'))
@include('frontend.partials.give')
@endif
@endsection