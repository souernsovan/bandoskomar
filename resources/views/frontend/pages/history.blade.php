@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();
        $pageTitle = $page->getTitleForLocale();

        $heroBadge = $c['hero_badge'] ?? 'Our story';
        $heroTitle = $c['hero_title'] ?? 'History of Impact';
        $heroSubtitle = $c['hero_subtitle'] ?? 'Building communities since 1989';
        $heroDescription = $c['hero_description'] ?? 'For over three decades, we have worked hand-in-hand with communities to create lasting change through education, health, and relief programs.';

        $milestoneTitle = $c['milestone_title'] ?? 'Key milestones';
        $milestoneSubtitle = $c['milestone_subtitle'] ?? 'Our journey in pictures and stories';

        $valuesTitle = $c['values_title'] ?? 'What drives us';
        $valuesSubtitle = $c['values_subtitle'] ?? 'The principles that guide every decision';
        $valuesDescription = $c['values_description'] ?? 'Our work is rooted in respect, transparency, and a deep belief that communities know best what they need.';

        $journeyTitle = $c['journey_title'] ?? 'Our journey continues';
        $journeyDescription = $c['journey_description'] ?? 'We are always looking for partners, donors, and volunteers who share our vision for thriving communities.';
    @endphp

    <section class="relative h-[80vh] min-h-[600px] flex items-center overflow-hidden" style="--hero-bk-navy: #1E2D53; --hero-bk-orange: #F68B1E;">
        <!-- Hero Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1497486751825-1233686d5d80?q=80&w=1600&auto=format&fit=crop" alt="Hero Background" class="w-full h-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(to right, rgba(30,45,83,0.9) 0%, rgba(30,45,83,0.5) 60%, transparent 100%);"></div>
        </div>

        <div class="container mx-auto px-4 md:px-6 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-block px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-6 animate-bounce" style="background: rgba(246,139,30,0.2); border: 1px solid rgba(246,139,30,0.3); color: #F68B1E;">
                    {{ $heroBadge }}
                </div>
                <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-[1.1] mb-6 tracking-tight" style="color: #ffffff;">
                    {{ $heroTitle }} <br>
                    <span style="color: #F68B1E;">{{ $heroSubtitle }}</span>
                </h1>
                <p class="text-lg md:text-xl mb-10 max-w-2xl leading-relaxed" style="color: rgba(255,255,255,0.85);">
                    {{ $heroDescription }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('frontend.product') }}" class="px-10 py-4 rounded-full font-extrabold text-lg shadow-lg transition-all text-center" style="background: #F68B1E; color: #ffffff;" onmouseover="this.style.background='#e07a1a';this.style.transform='scale(1.05)'" onmouseout="this.style.background='#F68B1E';this.style.transform='scale(1)'">Our Programs</a>
                    <a href="{{ route('frontend.contact') }}" class="px-10 py-4 rounded-full font-extrabold text-lg border-2 transition-all text-center" style="background: transparent; color: #F68B1E; border-color: #F68B1E;" onmouseover="this.style.background='#F68B1E';this.style.color='#ffffff'" onmouseout="this.style.background='transparent';this.style.color='#F68B1E'">Contact us</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-24">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $milestoneSubtitle }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $milestoneTitle }}
                </h2>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @php
                    $milestones = $c['milestones'] ?? [
                        ['year' => '1989', 'title' => 'Founded', 'description' => 'Bandos Komar began with a small group of local volunteers dedicated to improving education in rural Cambodia.'],
                        ['year' => '1995', 'title' => 'First school build', 'description' => 'Completed our first primary school construction, serving over 200 students.'],
                        ['year' => '2005', 'title' => 'Health program launch', 'description' => 'Expanded into community health outreach with mobile clinics and health education.'],
                        ['year' => '2015', 'title' => 'Relief response', 'description' => 'Established emergency relief networks to support families during floods and crises.'],
                        ['year' => '2020', 'title' => 'Digital learning', 'description' => 'Introduced remote learning tools and digital resources for continued education.'],
                        ['year' => '2024', 'title' => 'Looking forward', 'description' => 'Continuing to grow local partnerships and expand sustainable community programs.'],
                    ];
                @endphp

                @foreach ($milestones as $milestone)
                    <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-teal-900/5 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <span class="text-9xl font-extrabold">{{ $milestone['year'] }}</span>
                        </div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-4" style="background: #F68B1E; color: #ffffff;">
                            {{ $milestone['year'] }}
                        </span>
                        <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $milestone['title'] }}</h3>
                        <p class="mt-4 text-sm leading-6 text-slate-600">
                            {{ $milestone['description'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-5">
                    <span class="inline-flex items-center rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.3em] text-teal-700">
                        {{ $valuesSubtitle }}
                    </span>
                    <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                        {{ $valuesTitle }}
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        {{ $valuesDescription }}
                    </p>

                    <div class="mt-8 space-y-4">
                        @php
                            $values = $c['values'] ?? [
                                ['icon' => '🤝', 'title' => 'Community-led', 'text' => 'We listen first and let local needs shape our programs.'],
                                ['icon' => '💎', 'title' => 'Transparency', 'text' => 'Every donation and effort is accounted for with clear reporting.'],
                                ['icon' => '🌱', 'title' => 'Sustainability', 'text' => 'We build for long-term impact, not short-term visibility.'],
                            ];
                        @endphp
                        @foreach ($values as $value)
                            <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-2xl">{{ $value['icon'] }}</span>
                                <div>
                                    <h4 class="text-lg font-semibold text-slate-900">{{ $value['title'] }}</h4>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $value['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-xl shadow-teal-900/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-teal-700">Legacy</p>
                        <h3 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">36+ years of community partnership</h3>
                        <p class="mt-6 text-base leading-7 text-slate-600">
                            From a handful of passionate volunteers to a network spanning multiple provinces, our growth has always been rooted in local relationships and measurable impact.
                        </p>

                        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                            @php
                                $stats = $c['stats'] ?? [
                                    ['value' => '36+', 'label' => 'Years active'],
                                    ['value' => '50+', 'label' => 'Communities served'],
                                    ['value' => '10K+', 'label' => 'Children supported'],
                                    ['value' => '200+', 'label' => 'Schools built'],
                                ];
                            @endphp
                            @foreach ($stats as $stat)
                                <div class="rounded-2xl bg-teal-50 p-5 text-center">
                                    <div class="text-3xl font-extrabold text-teal-700">{{ $stat['value'] }}</div>
                                    <div class="mt-1 text-sm text-slate-600">{{ $stat['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-teal-700 text-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-100">{{ $journeyTitle }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ $journeyDescription }}
                </h2>
            </div>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('frontend.contact') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
                    Get in touch
                </a>
                <a href="{{ route('frontend.donate') }}" class="rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                    Support our work
                </a>
            </div>
        </div>
    </section>
@endsection
