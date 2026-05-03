@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();
        $pageTitle = $page->getTitleForLocale();

        $heroBadge = $c['hero_badge'] ?? 'Support our work';
        $heroTitle = $c['hero_title'] ?? 'Make a Difference Today';
        $heroSubtitle = $c['hero_subtitle'] ?? 'Your generosity creates lasting change';
        $heroDescription = $c['hero_description'] ?? 'Every contribution, no matter the size, helps us deliver practical support to communities in need. Join us in building a better future for families across Southeast Asia.';

        $impactTitle = $c['impact_title'] ?? 'Where your support goes';
        $impactSubtitle = $c['impact_subtitle'] ?? 'We are committed to transparency and accountable stewardship';

        $ctaTitle = $c['cta_title'] ?? 'Ready to make an impact?';
        $ctaDescription = $c['cta_description'] ?? 'Your support helps us reach more families with food, education, health care, and emergency relief. Partner with us today.';
        $donateButton = $c['donate_button'] ?? 'Donate Now';
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
                    <a href="{{ route('frontend.product') }}" class="px-10 py-4 rounded-full font-extrabold text-lg shadow-lg transition-all text-center" style="background: #F68B1E; color: #ffffff;" onmouseover="this.style.background='#e07a1a';this.style.transform='scale(1.05)'" onmouseout="this.style.background='#F68B1E';this.style.transform='scale(1)'">{{ $donateButton }}</a>
                    <a href="{{ route('frontend.about-us') }}" class="px-10 py-4 rounded-full font-extrabold text-lg border-2 transition-all text-center" style="background: transparent; color: #F68B1E; border-color: #F68B1E;" onmouseover="this.style.background='#F68B1E';this.style.color='#ffffff'" onmouseout="this.style.background='transparent';this.style.color='#F68B1E'">Our work</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-24">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $impactSubtitle }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $impactTitle }}
                </h2>
            </div>

            <div class="mt-16 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @php
                    $impactAreas = $c['impact_areas'] ?? [
                        [
                            'icon' => '🍚',
                            'title' => 'Meals & Essentials',
                            'description' => 'Providing nutritious food, hygiene supplies, and basic necessities to families facing hardship.',
                        ],
                        [
                            'icon' => '🎓',
                            'title' => 'Education Support',
                            'description' => 'Funding school supplies, scholarships, and learning resources for children and youth.',
                        ],
                        [
                            'icon' => '🏥',
                            'title' => 'Health Outreach',
                            'description' => 'Supporting basic health checks, wellness education, and access to care.',
                        ],
                        [
                            'icon' => '🆘',
                            'title' => 'Emergency Relief',
                            'description' => 'Rapid response to floods, displacement, and urgent community crises.',
                        ],
                        [
                            'icon' => '🤝',
                            'title' => 'Volunteer Care',
                            'description' => 'Equipping local volunteers with resources and training to serve effectively.',
                        ],
                        [
                            'icon' => '📊',
                            'title' => 'Transparent Reporting',
                            'description' => 'Clear updates so donors see exactly how their support makes a difference.',
                        ],
                    ];
                @endphp

                @foreach ($impactAreas as $area)
                    <article class="rounded-3xl border border-slate-200 bg-white p-8 shadow-lg shadow-teal-900/5">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-50 text-4xl">
                            {{ $area['icon'] }}
                        </div>
                        <h3 class="mt-5 text-xl font-bold text-slate-900">{{ $area['title'] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            {{ $area['description'] }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-teal-50 to-emerald-50 p-8 md:p-12 shadow-xl shadow-teal-900/5">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">Why give</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        Your support reaches communities directly
                    </h2>
                    <p class="mt-6 text-base leading-7 text-slate-600">
                        We keep overhead low and focus on practical, community-led programs. Every dollar you give goes toward real needs—food, school supplies, health outreach, and emergency relief—with transparent reporting so you can see the impact.
                    </p>

                    <div class="mt-10 grid gap-6 sm:grid-cols-3">
                        @php
                            $trustStats = $c['trust_stats'] ?? [
                                ['value' => '95%+', 'label' => 'Funds go to programs'],
                                ['value' => '36+', 'label' => 'Years trusted'],
                                ['value' => 'Local', 'label' => 'Community-led'],
                            ];
                        @endphp
                        @foreach ($trustStats as $stat)
                            <div class="rounded-2xl bg-white p-5 text-center shadow-sm border border-slate-100">
                                <div class="text-3xl font-extrabold text-teal-700">{{ $stat['value'] }}</div>
                                <div class="mt-1 text-sm text-slate-600">{{ $stat['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        <p class="text-sm text-slate-600">
                            <strong>Ways to give:</strong> Bank transfer, credit/debit card, or in-kind donations. All contributions are tax-deductible where applicable.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-teal-700 text-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-100">{{ $ctaTitle }}</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ $ctaDescription }}
                </h2>
            </div>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="mailto:donate@bandoskomar.org" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-teal-800 transition hover:bg-teal-50">
                    {{ $donateButton }}
                </a>
                <a href="{{ route('frontend.contact') }}" class="rounded-full border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                    Contact us
                </a>
            </div>
        </div>
    </section>
@endsection
