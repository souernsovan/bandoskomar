{{-- Clickable photo grid (opens the lightbox). Expects $images with image/category/caption. --}}
<div class="gallery">
  @foreach ($images as $item)
  @continue(($item['image'] ?? '') === '')
  @php $alt = ($item['caption'] ?? '') !== '' ? $item['caption'] : $site->get('site_title'); @endphp
  <button data-cat="{{ $item['category'] ?? '' }}"><img src="{{ \App\Support\PageContent::url($item['image']) }}" alt="{{ $alt }}" loading="lazy"></button>
  @endforeach
</div>
