@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();
        $missionTitle = $c['profile_title'] ?? 'Our Mission';
        $missionTagline = $c['profile_tagline'] ?? 'We work alongside communities to deliver education, care, and relief with dignity.';

        $platformSliderImages = $c['platform_slider_images'] ?? null;
        if (!is_array($platformSliderImages) || empty($platformSliderImages)) {
            $legacy = $c['platform_image'] ?? '';
            $platformSliderImages = $legacy ? [$legacy] : [];
        }
        $platformSliderImages = array_values(array_filter($platformSliderImages, fn ($p) => is_string($p) && $p !== ''));

        $featuresTitle = $c['features_title'] ?? 'What we do';
        $featuresSubtitle = $c['features_subtitle'] ?? 'We invest in practical programs that address real needs, not short-term optics.';
        $featureIcons = config('platform_feature_icons.icons', []);
        $defaultIconSvg = $featureIcons['icon_1']['svg'] ?? '';
        $defaultFeaturesList = [
            ['title' => 'Education support for children and youth.', 'color' => 'blue', 'icon' => 'icon_3'],
            ['title' => 'Health outreach and family care.', 'color' => 'green', 'icon' => 'icon_11'],
            ['title' => 'Emergency relief when crisis hits.', 'color' => 'red', 'icon' => 'icon_5'],
            ['title' => 'Community partnerships with local leaders.', 'color' => 'purple', 'icon' => 'icon_7'],
            ['title' => 'Transparent reporting for donors.', 'color' => 'blue', 'icon' => 'icon_2'],
            ['title' => 'Volunteer coordination and training.', 'color' => 'green', 'icon' => 'icon_8'],
        ];
        if (!array_key_exists('features', $c) || !is_array($c['features'])) {
            $features = $defaultFeaturesList;
        } else {
            $features = [];
            foreach ($c['features'] as $feature) {
                if (is_array($feature) && trim((string) ($feature['title'] ?? '')) !== '') {
                    $features[] = $feature;
                }
            }
        }

        $chooseTitle = $c['choose_title'] ?? 'Why support our work?';
        $chooseCol1Text = $c['choose_col_1_text'] ?? 'Supported by local partners';
        $chooseCol1Image = $c['choose_col_1_image'] ?? '';
        $chooseCol2Text = $c['choose_col_2_text'] ?? 'Community focus';
        $chooseCol2Value = $c['choose_col_2_value'] ?? '24/7';
        $chooseCol3Text = $c['choose_col_3_text'] ?? 'Program areas';
        $chooseCol3Value = $c['choose_col_3_value'] ?? '3+';
    @endphp

    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-24">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-6">
                    <span class="inline-flex items-center rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-teal-700">
                        Mission
                    </span>
                    <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                        {{ $missionTitle }}
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        {{ $missionTagline }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('frontend.product') }}" class="rounded-full bg-teal-700 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-700/20 transition hover:bg-teal-800">
                            View programs
                        </a>
                        <a href="{{ route('frontend.about-us') }}" class="rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-teal-300 hover:text-teal-700">
                            Learn more
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    @if (!empty($platformSliderImages))
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach (array_slice($platformSliderImages, 0, 4) as $idx => $slidePath)
                                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-lg shadow-teal-900/5 {{ $idx === 0 ? 'sm:col-span-2' : '' }}">
                                    <img class="h-full w-full object-cover" src="{{ asset($slidePath) }}" alt="{{ $missionTitle }} {{ $idx + 1 }}" loading="{{ $idx === 0 ? 'eager' : 'lazy' }}">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-xl shadow-teal-900/5">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-teal-700">Our commitments</p>
                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-900">Accountability</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">Clear goals, open reporting, and practical stewardship.</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-900">Local leadership</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">Programs are shaped with people closest to the need.</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-900">Long-term support</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">We build relationships that continue beyond a single event.</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-900">Shared impact</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">Volunteers, donors, and communities moving together.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $featuresTitle }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $featuresSubtitle }}
                </h2>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($features as $feature)
                    @php
                        $color = is_array($feature) && isset($feature['color']) ? $feature['color'] : 'blue';
                        $iconKey = is_array($feature) && isset($feature['icon']) ? $feature['icon'] : 'icon_1';
                        $iconSvg = $featureIcons[$iconKey]['svg'] ?? $defaultIconSvg;
                    @endphp
                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-teal-700 shadow-sm">
                            {!! $iconSvg !!}
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $feature['title'] ?? '' }}</h3>
                        @if (!empty($feature['description']))
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $feature['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">Support</p>
                    <h3 class="mt-4 text-2xl font-bold text-slate-900">{{ $chooseCol1Text }}</h3>
                    @if (!empty($chooseCol1Image))
                        <img src="{{ asset($chooseCol1Image) }}" alt="{{ $chooseCol1Text }}" class="mt-6 w-full rounded-2xl object-cover">
                    @else
                        <div class="mt-6 rounded-2xl bg-teal-50 p-6 text-sm leading-6 text-teal-900">
                            Local volunteers and donors help us keep support close to the community.
                        </div>
                    @endif
                </article>

                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $chooseCol2Text }}</p>
                    <div class="mt-6 text-6xl font-extrabold tracking-tight text-slate-900">{{ $chooseCol2Value }}</div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">
                        We stay responsive because community needs do not keep business hours.
                    </p>
                </article>

                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $chooseCol3Text }}</p>
                    <div class="mt-6 text-6xl font-extrabold tracking-tight text-slate-900">{{ $chooseCol3Value }}</div>
                    <p class="mt-4 text-sm leading-6 text-slate-600">
                        Focus areas are set so we can deliver practical help where it is needed most.
                    </p>
                </article>
            </div>
        </div>
    </section>
@endsection
