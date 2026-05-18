@extends('frontend.layouts.app')

@section('content')
    @php
        $pageTitle = $page->getTitleForLocale();
        $pageContent = trim((string) ($page->getContentForLocale() ?? ''));
        $pageSlug = $page->slug ?? '';
        $eyebrow = \Illuminate\Support\Str::headline($pageSlug ?: 'community update');
        $leadText = 'This page is being built with the same care we bring to our community work.';

        if ($pageSlug === 'contact') {
            $eyebrow = 'Contact';
            $leadText = 'Reach our team for partnerships, volunteering, support, and practical questions.';
        } elseif ($pageSlug === 'donate') {
            $eyebrow = 'Support';
            $leadText = 'Your support helps us reach more families and communities with practical care.';
        } elseif ($pageSlug === 'history') {
            $eyebrow = 'Our story';
            $leadText = 'Read how the organization started, grew, and stayed rooted in community partnership.';
        } elseif ($pageSlug === 'jobs-announcement') {
            $leadText = 'See current openings, team values, and future opportunities to join the mission.';
        } elseif ($pageSlug === 'annual-report') {
            $leadText = 'Review our annual reports, program summaries, and stewardship highlights.';
        } elseif ($pageSlug === 'strategic-plan') {
            $leadText = 'Learn about the priorities and long-term goals guiding our next chapter.';
        } elseif ($pageSlug === 'partner') {
            $leadText = 'We welcome people and organizations who want to collaborate on lasting impact.';
        } elseif ($pageSlug === 'volunteer') {
            $leadText = 'Find simple ways to give your time, skills, and care where they matter most.';
        } elseif ($pageSlug === 'image-gallery' || $pageSlug === 'image') {
            $leadText = 'Browse photos, logos, and visual assets from the public website archive.';
        } elseif ($pageSlug === 'video') {
            $leadText = 'Watch stories and updates from the communities we serve, shared in their own voices.';
        }

        $quickLinks = [
            ['label' => 'Programs', 'href' => route('frontend.product')],
            ['label' => 'Donate', 'href' => route('frontend.donate')],
            ['label' => 'Contact', 'href' => route('frontend.contact')],
        ];

        $highlights = [
            [
                'eyebrow' => 'Programs',
                'title' => 'Practical help for real community needs',
                'description' => 'We support education, family resilience, and emergency response with a focus on long-term value.',
            ],
            [
                'eyebrow' => 'Transparency',
                'title' => 'Clear stewardship and honest reporting',
                'description' => 'Donors and partners can see how support is used, what changes, and where the next needs are.',
            ],
            [
                'eyebrow' => 'Get involved',
                'title' => 'Donate, volunteer, or partner with us',
                'description' => 'Small actions add up when they are part of a sustained community effort.',
            ],
        ];
    @endphp

    <section class="relative overflow-hidden bg-[#1f2a72] text-white">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(249,115,22,0.22),transparent_35%),radial-gradient(circle_at_bottom_left,rgba(255,255,255,0.08),transparent_25%)]"></div>
        <div class="relative mx-auto max-w-6xl px-6 py-16 lg:px-8 lg:py-20">
            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-orange-300">
                {{ $eyebrow }}
            </p>
            <h1 class="mt-4 max-w-4xl text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                {{ $pageTitle }}
            </h1>
            <p class="mt-6 max-w-3xl text-lg leading-8 text-blue-100">
                {{ $leadText }}
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('frontend.product') }}" class="rounded-full bg-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600">
                    View Programs
                </a>
                <a href="{{ route('frontend.contact') }}" class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                    Contact us
                </a>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @foreach ($quickLinks as $quickLink)
                    <a href="{{ $quickLink['href'] }}" class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                        {{ $quickLink['label'] }}
                    </a>
                @endforeach
            </div>

            <article class="mt-10 rounded-[2rem] bg-white/10 p-6 shadow-2xl shadow-slate-950/20 ring-1 ring-white/10 backdrop-blur-sm">
                <div class="space-y-4 text-sm leading-7 text-blue-50 whitespace-pre-line">
                    {{ $pageContent ?: 'More information will be shared here soon.' }}
                </div>
            </article>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($highlights as $highlight)
                    <article class="rounded-3xl border border-slate-200 bg-slate-50 p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $highlight['eyebrow'] }}</p>
                        <h2 class="mt-4 text-2xl font-bold text-slate-900">{{ $highlight['title'] }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $highlight['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @include('frontend.partials.page-enrichments', ['page' => $page])
@endsection
