@php
    $bannerPage = $page ?? null;
    $bannerPageContent = $bannerPage && method_exists($bannerPage, 'getPageContentForLocale') ? $bannerPage->getPageContentForLocale() : [];
    $bannerPageSlug = $bannerPage ? ($bannerPage->slug ?? '') : '';

    $bannerDefaults = config('frontend_banner.defaults', []);
    $bannerPageDefaults = config("frontend_banner.pages.{$bannerPageSlug}", []);
    $bannerData = array_replace_recursive($bannerDefaults, $bannerPageDefaults);

    $bannerHero = is_array(data_get($bannerPageContent, 'hero')) ? data_get($bannerPageContent, 'hero') : [];

    $bannerBadge = $bannerBadge
        ?? data_get($bannerHero, 'badge')
        ?? data_get($bannerPageContent, 'hero_badge')
        ?? ($bannerData['badge'] ?? null)
        ?? ($bannerPage && method_exists($bannerPage, 'getTitleForLocale') ? $bannerPage->getTitleForLocale() : '');

    $bannerTitle = $bannerTitle
        ?? data_get($bannerHero, 'title')
        ?? data_get($bannerPageContent, 'hero_title')
        ?? ($bannerData['title'] ?? null)
        ?? ($bannerPage && method_exists($bannerPage, 'getTitleForLocale') ? $bannerPage->getTitleForLocale() : '');

    $bannerSubtitle = $bannerSubtitle
        ?? data_get($bannerHero, 'subtitle')
        ?? data_get($bannerPageContent, 'hero_subtitle')
        ?? ($bannerData['subtitle'] ?? null)
        ?? '';

    $bannerDescription = $bannerDescription
        ?? data_get($bannerHero, 'description')
        ?? data_get($bannerPageContent, 'hero_description')
        ?? ($bannerData['description'] ?? null)
        ?? '';

    $bannerPrimary = $bannerPrimary ?? ($bannerData['primary'] ?? null);
    $bannerSecondary = $bannerSecondary ?? ($bannerData['secondary'] ?? null);

    $resolveActionHref = function ($action): ?string {
        if (!is_array($action) || empty($action)) {
            return null;
        }

        if (!empty($action['href'])) {
            return $action['href'];
        }

        if (!empty($action['route']) && \Illuminate\Support\Facades\Route::has($action['route'])) {
            return route($action['route'], $action['params'] ?? []);
        }

        return null;
    };

    $bannerPrimaryLabel = $bannerPrimaryLabel ?? data_get($bannerPrimary, 'label');
    $bannerPrimaryHref = $bannerPrimaryHref ?? $resolveActionHref($bannerPrimary);
    $bannerSecondaryLabel = $bannerSecondaryLabel ?? data_get($bannerSecondary, 'label');
    $bannerSecondaryHref = $bannerSecondaryHref ?? $resolveActionHref($bannerSecondary);

    $bannerBackgroundImage = $bannerBackgroundImage ?? ($bannerData['background_image'] ?? '');
    $bannerBackgroundImageUrl = $bannerBackgroundImage;
    if ($bannerBackgroundImageUrl !== '' && !\Illuminate\Support\Str::startsWith($bannerBackgroundImageUrl, ['http://', 'https://', '//'])) {
        $bannerBackgroundImageUrl = asset($bannerBackgroundImageUrl);
    }

    $bannerAccentColor = $bannerAccentColor ?? ($bannerData['accent_color'] ?? '#F68B1E');
    $bannerNavyColor = $bannerNavyColor ?? ($bannerData['navy_color'] ?? '#1E2D53');
@endphp

<section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden" style="--hero-bk-navy: {{ $bannerNavyColor }}; --hero-bk-orange: {{ $bannerAccentColor }};">
    <div class="absolute inset-0 z-0">
        @if ($bannerBackgroundImageUrl !== '')
            <img src="{{ $bannerBackgroundImageUrl }}" alt="{{ $bannerTitle }}" class="w-full h-full object-cover">
        @endif
        <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(30,45,83,0.9) 0%, rgba(30,45,83,0.5) 60%, transparent 100%);"></div>
    </div>

    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="max-w-3xl">
            @if ($bannerBadge !== '')
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 animate-bounce" style="background: rgba(246,139,30,0.2); border: 1px solid rgba(246,139,30,0.3); color: {{ $bannerAccentColor }};">
                    {{ $bannerBadge }}
                </div>
            @endif
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-[1.1] mb-6 tracking-tight" style="color: #ffffff;">
                {{ $bannerTitle }}
                @if ($bannerSubtitle !== '')
                    <br>
                    <span style="color: {{ $bannerAccentColor }};">{{ $bannerSubtitle }}</span>
                @endif
            </h1>
            @if ($bannerDescription !== '')
                <p class="text-lg md:text-xl mb-10 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.85);">
                    {{ $bannerDescription }}
                </p>
            @endif

            @if ($bannerPrimaryHref || $bannerSecondaryHref)
                <div class="flex flex-col sm:flex-row gap-4">
                    @if ($bannerPrimaryHref)
                        <a href="{{ $bannerPrimaryHref }}" class="px-10 py-4 rounded-full font-extrabold text-lg shadow-lg transition-all text-center" style="background: {{ $bannerAccentColor }}; color: #ffffff;" onmouseover="this.style.background='#e07a1a';this.style.transform='scale(1.05)'" onmouseout="this.style.background='{{ $bannerAccentColor }}';this.style.transform='scale(1)'">
                            {{ $bannerPrimaryLabel ?? 'Learn More' }}
                        </a>
                    @endif

                    @if ($bannerSecondaryHref)
                        <a href="{{ $bannerSecondaryHref }}" class="px-10 py-4 rounded-full font-extrabold text-lg border-2 transition-all text-center" style="background: transparent; color: {{ $bannerAccentColor }}; border-color: {{ $bannerAccentColor }};" onmouseover="this.style.background='{{ $bannerAccentColor }}';this.style.color='#ffffff'" onmouseout="this.style.background='transparent';this.style.color='{{ $bannerAccentColor }}'">
                            {{ $bannerSecondaryLabel ?? 'Learn More' }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
