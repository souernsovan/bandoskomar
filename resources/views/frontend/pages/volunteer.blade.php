@extends('frontend.layouts.app')

@php use App\Support\PageContent; @endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap split wide-left">
    <div>
      <h2>{{ $c->get('intro_title') }}</h2>
      <p class="lead">{{ $c->get('intro_text') }}</p>
      @if (count($points = $c->list('intro_points')))
      <ul class="checks mt">
        @foreach ($points as $point)
        <li>{{ $point['text'] }}</li>
        @endforeach
      </ul>
      @endif
    </div>
    @if ($c->has('intro_image'))
    <div class="photo tall"><img src="{{ $c->media('intro_image') }}" alt=""></div>
    @endif
  </div>
</section>

@if (count($volunteers = $c->list('volunteers')))
<section class="sec alt">
  <div class="wrap">
    <div class="head center">
      <h2>{{ $c->get('team_title') }}</h2>
      <p class="lead">{{ $c->get('team_text') }}</p>
    </div>
    @if (count($categories = $c->list('categories')))
    <div class="filters vol-filters" style="justify-content:center">
      <button class="active" data-vfilter="all">{{ $c->get('all_label') }}</button>
      @foreach ($categories as $category)
      <button data-vfilter="{{ $category['key'] }}">{{ $category['label'] }}</button>
      @endforeach
    </div>
    @endif
    <div class="grid-3 vol-grid">
      @foreach ($volunteers as $volunteer)
      <article class="vol" data-role="{{ $volunteer['category'] }}">
        <div class="vol-top"><div class="avatar">
          @if ($volunteer['photo'] !== '')
          <img src="{{ PageContent::url($volunteer['photo']) }}" alt="{{ $volunteer['name'] }}" loading="lazy">
          @else
          {{ PageContent::initials($volunteer['name']) }}
          @endif
        </div>
          <div><h3>{{ $volunteer['name'] }}</h3>@if ($volunteer['role'] !== '')<span class="role">{{ $volunteer['role'] }}</span>@endif</div></div>
        <div class="vol-meta">
          @if ($volunteer['location'] !== '')<span>@include('frontend.partials.icon', ['name' => 'pin']) {{ $volunteer['location'] }}</span>@endif
          @if ($volunteer['duration'] !== '')<span>@include('frontend.partials.icon', ['name' => 'timer']) {{ $volunteer['duration'] }}</span>@endif
        </div>
        <p class="vol-desc">{{ $volunteer['text'] }}</p>
        <button class="read-more" type="button" data-more="{{ $c->get('read_more') }}" data-less="{{ $c->get('show_less') }}">{{ $c->get('read_more') }}</button>
      </article>
      @endforeach
    </div>
  </div>
</section>
@endif

@if (count($steps = $c->list('steps')))
<section class="sec">
  <div class="wrap">
    <div class="head center"><h2>{{ $c->get('steps_title') }}</h2></div>
    <div class="steps">
      @foreach ($steps as $step)
      <div class="step"><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></div>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="sec alt" id="apply">
  <div class="wrap split">
    <div><h2>{{ $c->get('apply_title') }}</h2><p class="lead">{{ $c->get('apply_text') }}</p></div>
    <form class="card form" action="{{ route('frontend.form.submit', 'volunteer') }}" method="post"
      data-ajax data-notice="vol-notice" data-error="{{ $site->get('form_error') }}">
      @include('frontend.partials.form-hidden', ['form' => 'volunteer'])
      <div class="row"><div><label for="vn">{{ $c->get('label_name') }}</label><input id="vn" name="name" value="{{ old('name') }}" required maxlength="255"></div><div><label for="ve">{{ $c->get('label_email') }}</label><input id="ve" name="email" type="email" value="{{ old('email') }}" required maxlength="255"></div></div>
      <div class="row"><div><label for="vp">{{ $c->get('label_phone') }}</label><input id="vp" name="phone" type="tel" value="{{ old('phone') }}" maxlength="50"></div>
      <div><label for="vt">{{ $c->get('label_availability') }}</label><select id="vt" name="availability">
        @foreach ($c->list('availability_options') as $option)
        <option @selected(old('availability') === $option['text'])>{{ $option['text'] }}</option>
        @endforeach
      </select></div></div>
      <div><label for="vs">{{ $c->get('label_interest') }}</label><select id="vs" name="interest">
        @foreach ($c->list('interest_options') as $option)
        <option @selected(old('interest') === $option['text'])>{{ $option['text'] }}</option>
        @endforeach
      </select></div>
      <div><label for="vm">{{ $c->get('label_message') }}</label><textarea id="vm" name="message" maxlength="5000">{{ old('message') }}</textarea></div>
      <button class="btn btn-orange" type="submit">{{ $c->get('submit_label') }}</button>
      @include('frontend.partials.form-notice', ['form' => 'volunteer', 'id' => 'vol-notice'])
    </form>
  </div>
</section>
@endsection
