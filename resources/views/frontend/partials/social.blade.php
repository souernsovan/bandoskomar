{{-- Facebook / YouTube links from the "site" page. --}}
@if ($site->has('facebook_url') || $site->has('youtube_url'))
<p class="social">
  @if ($site->has('facebook_url'))<a href="{{ $site->link('facebook_url') }}" target="_blank" rel="noopener" aria-label="Facebook">@include('frontend.partials.icon', ['name' => 'facebook'])</a>@endif
  @if ($site->has('youtube_url'))<a href="{{ $site->link('youtube_url') }}" target="_blank" rel="noopener" aria-label="YouTube">@include('frontend.partials.icon', ['name' => 'youtube'])</a>@endif
</p>
@endif
