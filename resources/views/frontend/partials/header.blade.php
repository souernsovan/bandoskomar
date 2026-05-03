@php
    $currentSlug = $page->slug ?? null;
    $headerPageCollection = collect($headerPages ?? []);

    $menuPages = $headerPageCollection->filter(
        fn ($menuPage) => $menuPage->getMenuGroup() !== 'hidden'
    );
    $mainPages = $menuPages->filter(fn ($menuPage) => $menuPage->getMenuGroup() === 'main')->values();
    $resourceLinks = $menuPages->filter(fn ($menuPage) => $menuPage->getMenuGroup() === 'resources')->values();
    $getInvolvedLinks = $menuPages->filter(fn ($menuPage) => $menuPage->getMenuGroup() === 'involved')->values();
    $morePages = $menuPages->filter(fn ($menuPage) => $menuPage->getMenuGroup() === 'more')->values();

    $isResources = $resourceLinks->contains(fn ($resource) => $currentSlug === $resource->slug);
    $isInvolved = $getInvolvedLinks->contains(fn ($involved) => $currentSlug === $involved->slug);
    $isMorePages = $morePages->contains(fn ($menuPage) => $currentSlug === $menuPage->slug);
    $isContact = request()->routeIs('frontend.contact') || $currentSlug === 'contact';
    $isDonate = request()->routeIs('frontend.donate') || $currentSlug === 'donate';
@endphp

<header class="fe-header">
    <div class="fe-header-inner fe-max-width">
        <a href="{{ route('frontend.home') }}" class="fe-header-logo" aria-label="{{ $siteName ?? config('app.name') }}">
            <img src="{{ asset($siteIconPath ?? \App\Models\SiteSetting::siteIconPath()) }}" alt="{{ $siteName ?? config('app.name') }}" class="fe-header-logo-img">
        </a>

        <button type="button" class="fe-header-menu-toggle" id="feHeaderMenuToggle" aria-label="Toggle menu" aria-expanded="false">
            <span class="fe-header-menu-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>

        <div class="fe-header-nav-overlay" id="feHeaderNavOverlay" aria-hidden="true"></div>

        <nav class="fe-header-nav" id="feHeaderNav" aria-label="Primary navigation">
            <button type="button" class="fe-header-nav-close" id="feHeaderNavClose" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            @foreach ($mainPages as $menuPage)
                <a href="{{ $menuPage->url }}" class="fe-header-nav-link {{ $currentSlug === $menuPage->slug ? 'is-active' : '' }}" data-fe-close-on-nav-click>
                    {{ $menuPage->getTitleForLocale() }}
                </a>
            @endforeach

            @if ($resourceLinks->isNotEmpty())
            <div class="fe-header-dropdown {{ $isResources ? 'is-active' : '' }}" data-fe-header-dropdown>
                <button
                    type="button"
                    class="fe-header-nav-link fe-header-nav-button {{ $isResources ? 'is-active' : '' }}"
                    data-fe-header-dropdown-trigger
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-controls="feHeaderResourcesMenu"
                >
                    <span>Info &amp; Resources</span>
                    <svg class="fe-header-nav-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div class="fe-header-dropdown-menu" id="feHeaderResourcesMenu" data-fe-header-dropdown-menu role="menu" aria-hidden="true">
                    @foreach ($resourceLinks as $resource)
                        <a href="{{ $resource->url }}" class="fe-header-dropdown-link {{ $currentSlug === $resource->slug ? 'is-active' : '' }}" role="menuitem" data-fe-close-on-nav-click>
                            {{ $resource->getTitleForLocale() }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($getInvolvedLinks->isNotEmpty())
            <div class="fe-header-dropdown {{ $isInvolved ? 'is-active' : '' }}" data-fe-header-dropdown>
                <button
                    type="button"
                    class="fe-header-nav-link fe-header-nav-button {{ $isInvolved ? 'is-active' : '' }}"
                    data-fe-header-dropdown-trigger
                    aria-haspopup="true"
                    aria-expanded="false"
                    aria-controls="feHeaderInvolvedMenu"
                >
                    <span>Get Involved</span>
                    <svg class="fe-header-nav-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div class="fe-header-dropdown-menu" id="feHeaderInvolvedMenu" data-fe-header-dropdown-menu role="menu" aria-hidden="true">
                    @foreach ($getInvolvedLinks as $involved)
                        <a href="{{ $involved->url }}" class="fe-header-dropdown-link {{ $currentSlug === $involved->slug ? 'is-active' : '' }}" role="menuitem" data-fe-close-on-nav-click>
                            {{ $involved->getTitleForLocale() }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($morePages->isNotEmpty())
                <div class="fe-header-dropdown {{ $isMorePages ? 'is-active' : '' }}" data-fe-header-dropdown>
                    <button
                        type="button"
                        class="fe-header-nav-link fe-header-nav-button {{ $isMorePages ? 'is-active' : '' }}"
                        data-fe-header-dropdown-trigger
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-controls="feHeaderMorePagesMenu"
                    >
                        <span>More Pages</span>
                        <svg class="fe-header-nav-chevron" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div class="fe-header-dropdown-menu" id="feHeaderMorePagesMenu" data-fe-header-dropdown-menu role="menu" aria-hidden="true">
                        @foreach ($morePages as $menuPage)
                            <a href="{{ $menuPage->url }}" class="fe-header-dropdown-link {{ $currentSlug === $menuPage->slug ? 'is-active' : '' }}" role="menuitem" data-fe-close-on-nav-click>
                                {{ $menuPage->getTitleForLocale() }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="{{ route('frontend.contact') }}" class="fe-header-nav-link {{ $isContact ? 'is-active' : '' }}" data-fe-close-on-nav-click>
                Contact
            </a>

            <a href="{{ route('frontend.donate') }}" class="fe-header-donate-btn {{ $isDonate ? 'is-active' : '' }}" data-fe-close-on-nav-click>
                Donate
            </a>
        </nav>
    </div>
</header>
