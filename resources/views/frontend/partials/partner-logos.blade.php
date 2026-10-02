{{-- Partner logo strip, using the partners listed on the Partner page. Optional $title. --}}
@php
    $partnerContent = \App\Support\PageContent::forSlug('partner');
    $logos = array_filter($partnerContent->list('partners'), fn ($p) => $p['logo'] !== '');
@endphp
@if (count($logos))
<section class="sec">
  <div class="wrap">
    @if (!empty($title))<div class="head center"><h2>{{ $title }}</h2></div>@endif
    <div class="logo-strip">
      @foreach ($logos as $partner)
      <a href="{{ route('frontend.page', 'partner') }}" title="{{ $partner['title'] }}"><img src="{{ \App\Support\PageContent::url($partner['logo']) }}" alt="{{ $partner['title'] }}" loading="lazy"></a>
      @endforeach
    </div>
  </div>
</section>
@endif
