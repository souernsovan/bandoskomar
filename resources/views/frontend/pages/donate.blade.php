@extends('frontend.layouts.app')

@php
    use App\Support\PageContent;

    $amounts = $c->list('amounts');
    $defaultAmount = (string) (old('amount') ?: (float) $c->get('default_amount'));
    $presetAmounts = array_map(fn ($a) => (string) (float) $a['amount'], $amounts);
    $isPreset = in_array($defaultAmount, $presetAmounts, true);
@endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

@if (count($causes = $c->list('causes')))
<section class="sec">
  <div class="wrap">
    <div class="head center"><h2>{{ $c->get('causes_title') }}</h2></div>
    @include('frontend.partials.tiles', ['tiles' => $causes, 'cols' => 3])
  </div>
</section>
@endif

<section class="sec alt">
  <div class="wrap split" style="align-items:start">
    <div>
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->html('intro_text') }}</p>
      @if (count($impact = $c->list('impact')))
      <ul class="impact mt">
        @foreach ($impact as $item)
        <li><strong>{{ PageContent::money($item['amount']) }}</strong><span>{{ $item['text'] }}</span></li>
        @endforeach
      </ul>
      @endif
      @if ($c->get('show_campaign'))
      <div class="mt">@include('frontend.partials.campaign')</div>
      @endif
      @if ($c->has('quote'))
      <p class="quote mt">{{ $c->get('quote') }}</p>
      @endif
    </div>

    <form class="card form" id="donate-form" action="{{ route('frontend.form.submit', 'donate') }}" method="post"
      data-ajax data-notice="donate-notice" data-validate="bkDonateValid" data-default="{{ $defaultAmount }}"
      data-error="{{ $site->get('form_error') }}" data-no-amount="{{ $c->get('no_amount_message') }}">
      @include('frontend.partials.form-hidden', ['form' => 'donate'])
      <h3>{{ $c->get('form_title') }}</h3>
      @if ($c->has('form_text'))
      <p style="color:var(--muted);font-size:16px;margin-top:-8px">{{ $c->get('form_text') }}</p>
      @endif
      @if (count($amounts))
      <div><label>{{ $c->get('label_amount') }}</label>
        <div class="amounts">
          @foreach ($amounts as $item)
          @php $value = (string) (float) $item['amount']; @endphp
          <button type="button" class="{{ $value === $defaultAmount ? 'active' : '' }}" data-amount="{{ $value }}">{{ PageContent::money($item['amount']) }}</button>
          @endforeach
        </div></div>
      @endif
      <div><label for="custom-amount">{{ $c->get('label_custom') }}</label><input id="custom-amount" type="number" min="1" step="1" placeholder="e.g. 30" value="{{ $isPreset ? '' : $defaultAmount }}"></div>
      <input type="hidden" name="amount" id="amount-field" value="{{ $defaultAmount }}">
      <div class="row"><div><label for="dn">{{ $c->get('label_name') }}</label><input id="dn" name="name" value="{{ old('name') }}" required maxlength="255"></div><div><label for="de">{{ $c->get('label_email') }}</label><input id="de" name="email" type="email" value="{{ old('email') }}" required maxlength="255"></div></div>
      <div><label for="dp">{{ $c->get('label_phone') }}</label><input id="dp" name="phone" type="tel" value="{{ old('phone') }}" maxlength="50"></div>
      <div><label for="dm">{{ $c->get('label_message') }}</label><textarea id="dm" name="message" maxlength="5000" placeholder="{{ $c->get('message_placeholder') }}">{{ old('message') }}</textarea></div>
      <div class="total"><span>{{ $c->get('total_label') }}</span><span id="total-amount">{{ PageContent::money($defaultAmount) }}</span></div>
      <button class="btn btn-orange" type="submit" style="font-size:18px;padding:15px"><span class="heart">♥</span> {{ $c->get('submit_label') }}</button>
      @include('frontend.partials.form-notice', ['form' => 'donate', 'id' => 'donate-notice'])
      <div class="trust">
        @if ($c->has('trust_1'))<span>@include('frontend.partials.icon', ['name' => 'mail']) {{ $c->get('trust_1') }}</span>@endif
        @if ($c->has('trust_2'))<span>@include('frontend.partials.icon', ['name' => 'chart']) <a href="{{ $c->link('trust_2_url') }}" style="text-decoration:underline">{{ $c->get('trust_2') }}</a></span>@endif
      </div>
    </form>
  </div>
</section>
@endsection
