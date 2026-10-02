@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

@php
    $details = [
        ['icon' => 'pin', 'label' => $c->get('label_address'), 'value' => $site->get('address'), 'href' => null],
        ['icon' => 'mail', 'label' => $c->get('label_email'), 'value' => $site->get('email'), 'href' => 'mailto:' . $site->get('email')],
        ['icon' => 'phone', 'label' => $c->get('label_phone'), 'value' => $site->get('phone'), 'href' => 'tel:' . preg_replace('/[^0-9+]/', '', $site->get('phone'))],
        ['icon' => 'clock', 'label' => $c->get('label_hours'), 'value' => $site->get('office_hours'), 'href' => null],
    ];
@endphp

<section class="sec">
  <div class="wrap split">
    <div>
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->get('intro_text') }}</p>
      <ul class="info-list mt">
        @foreach ($details as $detail)
        @continue(trim($detail['value']) === '')
        <li><div class="icon">@include('frontend.partials.icon', ['name' => $detail['icon']])</div><div><strong>{{ $detail['label'] }}</strong>
          @if ($detail['href'])<a href="{{ $detail['href'] }}">{{ $detail['value'] }}</a>@else {{ $detail['value'] }} @endif
        </div></li>
        @endforeach
      </ul>
      @if ($site->has('facebook_url') || $site->has('youtube_url'))
      <p class="mt" style="color:var(--muted)">{{ $c->get('social_title') }}</p>
      @include('frontend.partials.social')
      @endif
    </div>
    <form class="card form" action="{{ route('frontend.contact.send') }}" method="post"
      data-ajax data-notice="contact-notice" data-error="{{ $site->get('form_error') }}">
      @include('frontend.partials.form-hidden', ['form' => 'contact'])
      <div class="row"><div><label for="cn">{{ $c->get('label_name') }}</label><input id="cn" name="name" value="{{ old('name') }}" required maxlength="255"></div><div><label for="ce">{{ $c->get('label_form_email') }}</label><input id="ce" name="email" type="email" value="{{ old('email') }}" required maxlength="255"></div></div>
      @if (count($subjects = $c->list('subjects')))
      <div><label for="cs">{{ $c->get('label_subject') }}</label><select id="cs" name="subject">
        @foreach ($subjects as $subject)
        <option @selected(old('subject') === $subject['text'])>{{ $subject['text'] }}</option>
        @endforeach
      </select></div>
      @endif
      <div><label for="cm">{{ $c->get('label_message') }}</label><textarea id="cm" name="message" required maxlength="5000">{{ old('message') }}</textarea></div>
      <button class="btn btn-orange" type="submit">{{ $c->get('submit_label') }}</button>
      @include('frontend.partials.form-notice', ['form' => 'contact', 'id' => 'contact-notice'])
    </form>
  </div>
</section>

@if (count($offices = array_filter($c->list('offices'), fn ($o) => $o['map_url'] !== '')))
<section class="sec alt">
  <div class="wrap">
    <div class="head center"><h2>{{ $c->get('location_title') }}</h2>@if ($c->has('location_text'))<p class="lead">{{ $c->get('location_text') }}</p>@endif</div>
    <div class="grid-{{ count($offices) > 1 ? 2 : 1 }} offices">
      @foreach ($offices as $office)
      <div><h3>@include('frontend.partials.icon', ['name' => 'pin']) {{ $office['name'] }}</h3>
        <iframe class="map" title="{{ $office['name'] }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="{{ \App\Support\PageContent::href($office['map_url']) }}"></iframe></div>
      @endforeach
    </div>
  </div>
</section>
@endif
@endsection
