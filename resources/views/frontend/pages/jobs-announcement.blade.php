@extends('frontend.layouts.app')

@php
    use App\Support\PageContent;

    $jobs = $c->list('jobs');
    $openJobs = array_filter($jobs, fn ($job) => $job['status'] !== 'closed');
    // Re-open the form after a submit without JavaScript.
    $status = session('form_status');
    $reopen = (is_array($status) && ($status['form'] ?? null) === 'job') || old('_form') === 'job';
@endphp

@section('content')
@include('frontend.partials.banner', ['bannerImage' => $c->get('banner_image'), 'bannerTitle' => $c->get('banner_title')])

<section class="sec">
  <div class="wrap">
    <div class="head"><h2>{{ $c->get('intro_title') }}</h2><p class="lead">{{ $c->get('intro_text') }}</p></div>
    @if (empty($jobs) && $c->has('no_jobs_text'))
    <p class="notice show" style="background:var(--sky);color:var(--navy)">{{ $c->get('no_jobs_text') }}</p>
    @endif
    <div class="stack">
      @foreach ($jobs as $job)
      @php $closed = $job['status'] === 'closed'; @endphp
      <div class="card job">
        <div><span class="tag {{ $closed ? 'closed' : '' }}">{{ $closed ? $c->get('closed_label') : $c->get('open_label') }}</span><h3>{{ $job['title'] }}</h3>
        <div class="meta">
          @if ($job['location'] !== '')<span>@include('frontend.partials.icon', ['name' => 'pin']) {{ $job['location'] }}</span>@endif
          @if ($job['type'] !== '')<span>@include('frontend.partials.icon', ['name' => 'clock']) {{ $job['type'] }}</span>@endif
          @if ($job['deadline'] !== '')<span>@include('frontend.partials.icon', ['name' => 'calendar']) {{ $job['deadline'] }}</span>@endif
        </div></div>
        <div class="job-actions">
          @if ($job['pdf'] !== '')
          <a class="btn btn-line" href="{{ PageContent::url($job['pdf']) }}" target="_blank" rel="noopener">{{ $c->get('details_button') }}</a>
          @endif
          @if ($closed)
          <span class="btn btn-line" aria-disabled="true">{{ $c->get('closed_label') }}</span>
          @else
          <a class="btn btn-orange" href="#apply" data-modal-open="apply" data-position="{{ $job['title'] }}">{{ $c->get('apply_button') }}</a>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

@if (count($openJobs))
<div class="modal {{ $reopen ? 'open' : '' }}" id="apply" role="dialog" aria-modal="true" aria-labelledby="apply-title">
  <div class="modal-box">
    <a href="#" class="modal-x" data-modal-close aria-label="Close">✕</a>
    <h2 id="apply-title">{{ $c->get('apply_title') }}</h2>
    <p class="lead">{{ $c->get('apply_text') }}</p>
    <form class="form" action="{{ route('frontend.form.submit', 'job') }}" method="post" enctype="multipart/form-data"
      data-ajax data-notice="job-notice" data-error="{{ $site->get('form_error') }}">
      @include('frontend.partials.form-hidden', ['form' => 'job'])
      <div><label for="jpos">{{ $c->get('label_position') }}</label><select id="jpos" name="position" required><option value="">{{ $c->get('position_placeholder') }}</option>
        @foreach ($openJobs as $job)
        <option @selected(old('position') === $job['title'])>{{ $job['title'] }}</option>
        @endforeach
      </select></div>
      <div class="row"><div><label for="jn">{{ $c->get('label_name') }}</label><input id="jn" name="name" value="{{ old('name') }}" required maxlength="255"></div><div><label for="je">{{ $c->get('label_email') }}</label><input id="je" name="email" type="email" value="{{ old('email') }}" required maxlength="255"></div></div>
      <div class="row"><div><label for="jp">{{ $c->get('label_phone') }}</label><input id="jp" name="phone" type="tel" value="{{ old('phone') }}" maxlength="50"></div>
      <div><label for="jcv">{{ $c->get('label_cv') }}</label><input id="jcv" name="cv" type="file" accept=".pdf,application/pdf"></div></div>
      <div><label for="jm">{{ $c->get('label_letter') }}</label><textarea id="jm" name="message" maxlength="5000">{{ old('message') }}</textarea></div>
      <button class="btn btn-orange" type="submit">{{ $c->get('submit_label') }}</button>
      @include('frontend.partials.form-notice', ['form' => 'job', 'id' => 'job-notice'])
    </form>
  </div>
</div>
@endif
@endsection
