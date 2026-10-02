@php use App\Support\PageContent; @endphp
<footer>
  <div class="wrap foot-grid">
    <div>
      <div class="foot-logo"><img src="{{ $site->media('logo') }}" alt="{{ $site->get('site_title') }}" loading="lazy"></div>
      <p>{{ $site->get('footer_about') }}</p>
    </div>
    @foreach ([1, 2, 3] as $column)
    <div>
      <h4>{{ $site->get("footer_col{$column}_title") }}</h4>
      <ul>
        @foreach ($site->list("footer_col{$column}_links") as $link)
        <li><a href="{{ PageContent::href($link['url']) }}">{{ $link['label'] }}</a></li>
        @endforeach
      </ul>
    </div>
    @endforeach
    <div class="foot-contact">
      <h4>{{ $site->get('footer_contact_title') }}</h4>
      @if ($site->has('address'))<p>{{ $site->get('address') }}</p>@endif
      @if ($site->has('email'))<p><a href="mailto:{{ $site->get('email') }}">{{ $site->get('email') }}</a></p>@endif
      @if ($site->has('phone'))<p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $site->get('phone')) }}">{{ $site->get('phone') }}</a></p>@endif
      @include('frontend.partials.social')
      <a class="btn btn-orange" href="{{ route('frontend.donate') }}">{{ $site->get('donate_label') }}</a>
    </div>
  </div>
  <p class="copy">{{ $site->get('copyright') }}</p>
</footer>
