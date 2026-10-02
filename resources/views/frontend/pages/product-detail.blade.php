@extends('frontend.layouts.app')

@section('content')
@php $programs = \App\Models\Page::getBySlug('product'); @endphp
<section class="banner" @if ($product->getImageUrl()) style="background-image:url('{{ \App\Support\PageContent::cssUrl($product->getImageUrl()) }}')" @endif>
  <div class="wrap">
    <h1>{{ $product->title }}</h1>
    <p class="crumbs"><a href="{{ route('frontend.home') }}">{{ $site->get('home_label') }}</a>
      @if ($programs) / <a href="{{ route('frontend.product') }}">{{ $programs->getTitleForLocale() }}</a>@endif
      / {{ $product->title }}</p>
  </div>
</section>

<section class="sec">
  <div class="wrap split wide-left" style="align-items:start">
    <div class="prose">{!! $product->description !!}</div>
    @if ($product->getImageUrl())
    <div class="photo wide"><img src="{{ $product->getImageUrl() }}" alt="{{ $product->title }}"></div>
    @endif
  </div>
</section>

@include('frontend.partials.give')
@endsection
