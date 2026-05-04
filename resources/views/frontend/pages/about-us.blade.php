@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();

        $resultsSubtitle = $c['results_subtitle'] ?? 'Community impact';
        $resultsTitle = $c['results_title'] ?? 'How do we deliver meaningful results?';
        $resultsDescription = $c['results_description'] ?? 'We build practical, transparent programs that focus on long-term support for people and communities.';

        $differentSubtitle = $c['different_subtitle'] ?? 'Why we are different';
        $differentTitle = $c['different_title'] ?? 'We work with people, not for them.';
        $differentDescription = $c['different_description'] ?? 'Our approach is collaborative, local, and rooted in dignity. We listen first and act with care.';
        $differentCheck = $c['different_check'] ?? 'Community-led support';
        $differentImage = $c['different_image'] ?? '';

        $promiseSubtitle = $c['promise_subtitle'] ?? 'Our promise';
        $promiseTitle = $c['promise_title'] ?? 'We stay accountable to every family and donor.';
        $promiseDescription = $c['promise_description'] ?? 'We keep our work simple, transparent, and focused on the real needs that matter most.';
        $promiseCheck = $c['promise_check'] ?? 'Transparent reporting';
        $promiseImage = $c['promise_image'] ?? '';

        $solutionsSubtitle = $c['solutions_subtitle'] ?? 'Our approach';
        $solutionsTitle = $c['solutions_title'] ?? 'Programs designed for lasting change';
        $solutionsDescription = $c['solutions_description'] ?? 'Each initiative is shaped to respond to community needs with practical support and local partnership.';
        $solIcons = config('about_us_icons.solution_icons', []);
        $solutionCards = $c['solution_cards'] ?? [
            ['title' => 'Education support', 'description' => 'Scholarships, school supplies, and learning support for children and youth.', 'icon' => 'sol_1'],
            ['title' => 'Health outreach', 'description' => 'Health education, basic care, and referrals that make support easier to access.', 'icon' => 'sol_2'],
            ['title' => 'Emergency relief', 'description' => 'Rapid help for families facing crisis, displacement, or urgent hardship.', 'icon' => 'sol_3'],
        ];

        $interestsTitle = $c['interests_title'] ?? 'Where support matters most';
        $intIcons = config('about_us_icons.interest_icons', []);
        $interestCards = $c['interest_cards'] ?? [
            ['title' => 'Meals and essentials', 'description' => 'Helping families access the things they need most, when they need them most.', 'icon' => 'int_1'],
            ['title' => 'Youth mentoring', 'description' => 'Guidance, encouragement, and opportunities for young people to grow.', 'icon' => 'int_2'],
            ['title' => 'Reports and transparency', 'description' => 'Clear reporting so supporters can see how the work is making a difference.', 'icon' => 'int_3'],
            ['title' => 'Community partnerships', 'description' => 'Working side by side with local organizations to make support stronger.', 'icon' => 'int_4'],
            ['title' => 'Volunteer care', 'description' => 'Equipping volunteers with simple, effective ways to help.', 'icon' => 'int_5'],
            ['title' => 'Ongoing support', 'description' => 'Stay connected through regular updates, needs, and opportunities to serve.', 'icon' => 'int_6'],
        ];

        $readyTitle = $c['ready_title'] ?? 'Ready to help build stronger communities?';
    @endphp

    <div class="bg-white">
       

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $resultsSubtitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        {{ $resultsTitle }}
                    </h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        {{ $resultsDescription }}
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-slate-50">
            <div class="mx-auto grid max-w-7xl gap-8 px-6 py-16 lg:grid-cols-2 lg:px-8">
                <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8 shadow-lg shadow-teal-900/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $differentSubtitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $differentTitle }}</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        {{ $differentDescription }}
                    </p>
                    <div class="mt-6 inline-flex items-center gap-3 rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-800">
                        <span aria-hidden="true">✓</span>
                        {{ $differentCheck }}
                    </div>
                    @if (!empty($differentImage))
                        <img src="{{ asset($differentImage) }}" alt="{{ $differentTitle }}" class="mt-8 w-full rounded-3xl object-cover">
                    @else
                        <div class="mt-8 rounded-3xl bg-slate-50 p-6 text-sm leading-6 text-slate-600">
                            Community-first work means the people who know the need best help shape the solution.
                        </div>
                    @endif
                </article>

                <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-8 shadow-lg shadow-teal-900/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $promiseSubtitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ $promiseTitle }}</h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        {{ $promiseDescription }}
                    </p>
                    <div class="mt-6 inline-flex items-center gap-3 rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-800">
                        <span aria-hidden="true">✓</span>
                        {{ $promiseCheck }}
                    </div>
                    @if (!empty($promiseImage))
                        <img src="{{ asset($promiseImage) }}" alt="{{ $promiseTitle }}" class="mt-8 w-full rounded-3xl object-cover">
                    @else
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-900">Stewardship</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">We keep support focused and transparent.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-900">Consistency</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">We stay present beyond one-off campaigns.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-900">Respect</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Every family is treated with dignity.</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-900">Partnership</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">We collaborate with local leaders and donors.</p>
                            </div>
                        </div>
                    @endif
                </article>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $solutionsSubtitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        {{ $solutionsTitle }}
                    </h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        {{ $solutionsDescription }}
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach ($solutionCards as $card)
                        @php
                            $iconKey = $card['icon'] ?? 'sol_1';
                            $iconSvg = $solIcons[$iconKey]['svg'] ?? '';
                        @endphp
                        <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-teal-700 shadow-sm">
                                {!! $iconSvg !!}
                            </div>
                            <h3 class="mt-5 text-xl font-semibold text-slate-900">{{ $card['title'] ?? '' }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $interestsTitle }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        We focus on the needs that matter most.
                    </h2>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($interestCards as $card)
                        @php
                            $iconKey = $card['icon'] ?? 'int_1';
                            $iconSvg = $intIcons[$iconKey]['svg'] ?? '';
                        @endphp
                        <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                                {!! $iconSvg !!}
                            </div>
                            <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ $card['title'] ?? '' }}</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="rounded-[2rem] bg-teal-700 px-8 py-10 text-white shadow-2xl shadow-teal-900/15">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-100">Next step</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                        {{ $readyTitle }}
                    </h2>
                    <p class="mt-4 max-w-3xl text-sm leading-6 text-teal-50">
                        Partner with us to help children, families, and neighbors thrive.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('frontend.product') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
                            View programs
                        </a>
                        <a href="mailto:info@example.org" class="rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            Contact us
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
