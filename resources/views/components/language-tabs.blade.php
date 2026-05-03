@php
    $locales = \App\Support\PageLocales::labels();
    $active = $active ?? 'en';
    $groupId = $groupId ?? 'main';
    $sticky = $sticky ?? false;
@endphp
<div class="lang-tabs {{ $sticky ? 'lang-tabs-sticky' : '' }}" role="tablist" data-lang-tabs="{{ $groupId }}">
    @foreach($locales as $code => $info)
        <button type="button"
            class="lang-tab {{ $active === $code ? 'active' : '' }}"
            role="tab"
            aria-selected="{{ $active === $code ? 'true' : 'false' }}"
            data-lang="{{ $code }}">
            <span class="lang-tab-flag">{{ $info['flag'] }}</span>
            <span class="lang-tab-name">{{ $info['name'] }}</span>
        </button>
    @endforeach
</div>
