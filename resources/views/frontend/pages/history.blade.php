@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();

        $milestoneTitle = $c['milestone_title'] ?? 'Key milestones';
        $milestoneSubtitle = $c['milestone_subtitle'] ?? 'Our journey in pictures and stories';

        $valuesTitle = $c['values_title'] ?? 'What drives us';
        $valuesSubtitle = $c['values_subtitle'] ?? 'The principles that guide every decision';
        $valuesDescription = $c['values_description'] ?? 'Our work is rooted in respect, transparency, and a deep belief that communities know best what they need.';

        $journeyTitle = $c['journey_title'] ?? 'Our journey continues';
        $journeyDescription = $c['journey_description'] ?? 'We are always looking for partners, donors, and volunteers who share our vision for thriving communities.';
    @endphp


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
