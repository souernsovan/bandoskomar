@php
    $currentSlug = $page->slug ?? null;
    $allPages = collect($headerPages ?? []);
    // Contact and Donate have fixed places in the menu, wherever they are grouped.
    $menuPages = $allPages->reject(fn ($p) => in_array($p->slug, ['contact', 'donate', \App\Support\ContentSchema::SITE], true)
        || $p->getMenuGroup() === 'hidden');
    $group = fn (string $name) => $menuPages->filter(fn ($p) => $p->getMenuGroup() === $name)->values();
    $mainPages = $group('main');
    $dropdowns = [
        ['label' => $site->get('resources_label'), 'pages' => $group('resources')],
        ['label' => $site->get('involved_label'), 'pages' => $group('involved')],
    ];
    $morePages = $group('more');
    $contactPage = $allPages->firstWhere('slug', 'contact');
    $isActive = fn ($p) => $currentSlug === $p->slug;
    $logo = $site->media('logo');
    $languages = [
        'en' => ['name' => 'English', 'flag' => 'gb'],
        'km' => ['name' => 'ខ្មែរ', 'long' => 'ខ្មែរ (Khmer)', 'flag' => 'kh'],
    ];
    $currentLang = $languages[app()->getLocale()] ?? $languages['en'];
    $flag = fn ($code) => 'https://flagcdn.com/w20/' . $code . '.png';
@endphp
<header>
  <div class="wrap nav">
    <a class="logo" href="{{ route('frontend.home') }}"><img src="{{ $logo }}" alt="{{ $site->get('site_title') }}"></a>
    <button class="burger" aria-label="Open menu" aria-expanded="false" aria-controls="drawer"><span></span><span></span><span></span></button>
    <nav class="drawer" id="drawer" aria-label="Main">
      <div class="drawer-head">
        <a class="logo" href="{{ route('frontend.home') }}"><img src="{{ $logo }}" alt="{{ $site->get('site_title') }}"></a>
        <button class="drawer-close" aria-label="Close menu">✕</button>
      </div>
      <ul class="menu">
        @foreach ($mainPages as $menuPage)
        <li><a href="{{ $menuPage->url }}" class="{{ $isActive($menuPage) ? 'active' : '' }}">{{ $menuPage->getTitleForLocale() }}</a></li>
        @endforeach
        @foreach ($dropdowns as $dropdown)
          @continue($dropdown['pages']->isEmpty())
          <li class="has-sub"><button class="{{ $dropdown['pages']->contains($isActive) ? 'active' : '' }}"><span>{{ $dropdown['label'] }}</span> <span class="caret">▼</span></button><ul class="sub">
            @foreach ($dropdown['pages'] as $menuPage)
            <li><a href="{{ $menuPage->url }}" class="{{ $isActive($menuPage) ? 'active' : '' }}">{{ $menuPage->getTitleForLocale() }}</a></li>
            @endforeach
          </ul></li>
        @endforeach
        @foreach ($morePages as $menuPage)
        <li><a href="{{ $menuPage->url }}" class="{{ $isActive($menuPage) ? 'active' : '' }}">{{ $menuPage->getTitleForLocale() }}</a></li>
        @endforeach
        @if ($contactPage)
        <li><a href="{{ route('frontend.contact') }}" class="{{ $isActive($contactPage) ? 'active' : '' }}">{{ $contactPage->getTitleForLocale() }}</a></li>
        @endif
        <li><a href="{{ route('frontend.donate') }}" class="nav-donate {{ $currentSlug === 'donate' ? 'active' : '' }}">{{ $site->get('donate_label') }}</a></li>
        <li class="has-sub lang"><button class="lang-current"><img src="{{ $flag($currentLang['flag']) }}" alt=""> {{ $currentLang['name'] }} <span class="caret">▼</span></button>
          <ul class="sub">
            @foreach ($languages as $code => $lang)
            <li><a href="{{ route('locale.switch', $code) }}" lang="{{ $code }}"><img src="{{ $flag($lang['flag']) }}" alt=""> {{ $lang['long'] ?? $lang['name'] }}</a></li>
            @endforeach
          </ul>
        </li>
      </ul>
      <div class="drawer-foot">
        <div class="seg" role="group" aria-label="Language">
          @foreach ($languages as $code => $lang)
          <a href="{{ route('locale.switch', $code) }}" lang="{{ $code }}" class="{{ app()->getLocale() === $code ? 'on' : '' }}"><img src="{{ $flag($lang['flag']) }}" alt=""> {{ $lang['name'] }}</a>
          @endforeach
        </div>
        <a class="btn btn-orange" href="{{ route('frontend.donate') }}"><span class="heart">♥</span> {{ $site->get('donate_label') }}</a>
        <p class="drawer-contact">{{ $site->get('email') }}<br>{{ $site->get('phone') }}</p>
      </div>
    </nav>
  </div>
  <div class="overlay"></div>
</header>
