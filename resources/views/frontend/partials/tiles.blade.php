{{-- Image tiles. Expects $tiles; optional $cols (3 or 4, default 4). --}}
<div class="grid-{{ $cols ?? 4 }}">
  @foreach ($tiles as $tile)
  <div class="tile">
    @if ($tile['image'] !== '')<img src="{{ \App\Support\PageContent::url($tile['image']) }}" alt="{{ $tile['title'] }}" loading="lazy">@endif
    <h3>{{ $tile['title'] }}</h3>
    @if ($tile['text'] !== '')<p>{!! nl2br(e($tile['text'])) !!}</p>@endif
  </div>
  @endforeach
</div>
