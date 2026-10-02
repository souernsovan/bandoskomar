{{-- Alternating picture + text blocks. Expects $blocks (image/title/text); optional $alt (first block on tinted background). --}}
@foreach ($blocks as $block)
<section class="sec {{ ($loop->index % 2 === 0) === !empty($alt) ? 'alt' : '' }}">
  <div class="wrap split {{ $loop->odd ? '' : 'reverse' }}">
    @if (($block['image'] ?? '') !== '')
    <div class="photo wide"><img src="{{ \App\Support\PageContent::url($block['image']) }}" alt="{{ $block['title'] }}" loading="lazy"></div>
    @endif
    <div>
      <h2>{{ $block['title'] }}</h2>
      @foreach (array_filter(array_map('trim', explode("\n", (string) $block['text'])), 'strlen') as $paragraph)
      <p class="lead">{{ $paragraph }}</p>
      @endforeach
    </div>
  </div>
</section>
@endforeach
