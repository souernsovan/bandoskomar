<section class="give">
  <div class="wrap">
    <div><h2>{{ $site->get('give_title') }}</h2>
    @if ($site->has('give_text'))<p style="opacity:.85;margin-top:10px">{{ $site->get('give_text') }}</p>@endif</div>
    <a class="btn btn-orange" href="{{ route('frontend.donate') }}"><span class="heart">♥</span> {{ $site->get('donate_label') }}</a>
  </div>
</section>
