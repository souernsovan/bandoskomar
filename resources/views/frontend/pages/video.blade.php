@extends('frontend.layouts.app')

@php use App\Support\PageContent; @endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap">
    <div class="head"><h2>{{ $c->get('intro_title') }}</h2><p class="lead">{{ $c->get('intro_text') }}</p></div>
    <div class="grid-3">
      @foreach ($c->list('videos') as $video)
      <div><button class="video" data-src="{{ PageContent::embedUrl($video['video_url']) }}" data-title="{{ $video['title'] }}" data-empty="{{ $c->get('no_video_text') }}">
        @if ($video['thumbnail'] !== '')<img src="{{ PageContent::url($video['thumbnail']) }}" alt="" loading="lazy">@endif<span class="play">▶</span></button>
        <p class="video-title">{{ $video['title'] }}</p></div>
      @endforeach
    </div>
    @if ($site->has('youtube_url') && $c->has('channel_label'))
    <div class="center mt"><a class="btn btn-navy" href="{{ $site->link('youtube_url') }}" target="_blank" rel="noopener">@include('frontend.partials.icon', ['name' => 'youtube']) {{ $c->get('channel_label') }}</a></div>
    @endif
  </div>
</section>
<div class="lightbox video-modal" role="dialog" aria-modal="true"><button class="close" aria-label="Close">✕</button><div class="modal-video"></div></div>
@endsection
