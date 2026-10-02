{{-- Campaign progress box. Optional $button (bool). --}}
@php
    $raised = (float) $site->get('campaign_raised');
    $goal = (float) $site->get('campaign_goal');
    $percent = $goal > 0 ? (int) min(100, round($raised / $goal * 100)) : 0;
@endphp
<div class="campaign">
  <h3>{{ $site->get('campaign_title') }}</h3>
  <p style="color:var(--muted)">{{ $site->get('campaign_text') }}</p>
  <div class="bar"><i data-pct="{{ $percent }}"></i></div>
  <div class="bar-meta">
    <span><strong>{{ \App\Support\PageContent::money($raised) }}</strong> {{ $site->get('campaign_raised_label') }}</span>
    <span>{{ $site->get('campaign_goal_label') }} <strong>{{ \App\Support\PageContent::money($goal) }}</strong></span>
    <span>{{ $percent }}% {{ $site->get('campaign_reached_label') }}</span>
  </div>
  @if (!empty($button))
  <a class="btn btn-orange mt" href="{{ route('frontend.donate') }}" style="width:100%">{{ $site->get('donate_label') }}</a>
  @endif
</div>
