{{-- Icon cards. Expects $cards with icon/title/text. Place inside a grid. --}}
@foreach ($cards as $card)
<div class="card"><div class="icon">@include('frontend.partials.icon', ['name' => $card['icon']])</div><h3>{{ $card['title'] }}</h3><p>{{ $card['text'] }}</p></div>
@endforeach
