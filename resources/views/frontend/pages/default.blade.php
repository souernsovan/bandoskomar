@extends('frontend.layouts.app')

@section('content')
    @php
        $pageTitle = $page->getTitleForLocale();
        $pageContent = trim((string) ($page->getContentForLocale() ?? ''));
        $pageSlug = $page->slug ?? '';
        $eyebrow = \Illuminate\Support\Str::headline($pageSlug ?: 'community update');
        $leadText = 'Updates and details for this page will appear below.';

        if ($pageSlug === 'contact') {
            $eyebrow = 'Contact';
            $leadText = 'Reach our team for partnerships, volunteering, and support.';
        } elseif ($pageSlug === 'donate') {
            $eyebrow = 'Support';
            $leadText = 'Your support helps us reach more families and communities.';
        } elseif ($pageSlug === 'history') {
            $eyebrow = 'Our story';
            $leadText = 'Read how the organization started and how the work has grown.';
        } elseif ($pageSlug === 'jobs-announcement') {
            $leadText = 'See current openings and future opportunities to join the team.';
        } elseif ($pageSlug === 'annual-report') {
            $leadText = 'Review our annual reports and impact summaries.';
        } elseif ($pageSlug === 'strategic-plan') {
            $leadText = 'Learn about the priorities and long-term goals guiding our work.';
        } elseif ($pageSlug === 'partner') {
            $leadText = 'We welcome people and organizations who want to collaborate with us.';
        } elseif ($pageSlug === 'volunteer') {
            $leadText = 'Find simple ways to give your time, skills, and care.';
        } elseif ($pageSlug === 'image') {
            $leadText = 'Browse photos and visual stories from our community programs.';
        } elseif ($pageSlug === 'video') {
            $leadText = 'Watch stories and updates from the communities we serve.';
        }
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

            <article class="mt-10 rounded-[2rem] bg-white/10 p-6 shadow-2xl shadow-slate-950/20 ring-1 ring-white/10 backdrop-blur-sm">
                <div class="space-y-4 text-sm leading-7 text-blue-50 whitespace-pre-line">
                    {{ $pageContent ?: 'More information will be shared here soon.' }}
                </div>
            </article>
        </div>
    </section>
@endsection
