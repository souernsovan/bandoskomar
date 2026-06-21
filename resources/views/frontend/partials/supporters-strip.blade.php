@php
    $supporterLogos = array_values(array_filter(is_array($supporterLogos ?? null) ? $supporterLogos : []));
    $supportersTitle = $supportersTitle ?? 'Our supporters';
    $supportersDescription = $supportersDescription ?? 'Organizations and partners who help make practical community support possible.';
    $supportersEmptyText = $supportersEmptyText ?? 'Supporter logos will appear here once uploaded.';
    $resolveSupporterUrl = static function (?string $path): string {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        return asset(ltrim($path, '/'));
    };
@endphp

<section class="bg-white py-12 sm:py-14 w-full">
    <div class="w-full px-0 sm:px-0 lg:px-0">
        <div class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-teal-700">{{ $supportersTitle }}</p>
            <h2 class="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                Trusted organizations walking with us
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                {{ $supportersDescription }}
            </p>
        </div>

        <div class="mt-8 relative overflow-hidden">
            <div class="pointer-events-none absolute inset-y-0 left-0 w-20 bg-gradient-to-r from-white to-transparent z-10"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 w-20 bg-gradient-to-l from-white to-transparent z-10"></div>
            @if (!empty($supporterLogos))
                @php
                    $marqueeLogos = array_merge($supporterLogos, $supporterLogos);
                @endphp
                <div class="supporters-marquee flex w-max items-center gap-4 py-2">
                    @foreach ($marqueeLogos as $logo)
                        <div class="flex h-20 w-48 flex-none items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 shadow-[0_8px_24px_rgba(15,23,42,0.04)]">
                            <img src="{{ $resolveSupporterUrl($logo) }}" alt="Supporter logo" loading="lazy" class="max-h-12 w-full object-contain">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex min-h-[6rem] items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 text-sm text-slate-600">
                    {{ $supportersEmptyText }}
                </div>
            @endif
        </div>
    </div>
</section>

@once
    <style>
        .supporters-marquee {
            animation: supporters-marquee 26s linear infinite;
            will-change: transform;
        }

        .supporters-marquee:hover {
            animation-play-state: paused;
        }

        @keyframes supporters-marquee {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-50%);
            }
        }
    </style>
@endonce
