@extends('frontend.layouts.app')

@section('content')
    @php
        $programs = $products ?? collect();
        $supporterLogos = array_values(array_filter(is_array($partnerImages ?? null) ? $partnerImages : []));
        $pageTitle = $productsTitle ?? 'Our Programs';
        $intro = $description ?? 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.';
        $featureSectionTitle = $featureSectionTitle ?? 'Impact areas';
        $featureSectionDescription = $featureSectionDescription ?? 'Programs are designed around practical action, local accountability, and visible results.';
        $featureCards = $featureCards ?? [
            ['title' => 'Education', 'description' => 'School support, learning, and access.', 'image' => ''],
            ['title' => 'Health', 'description' => 'Outreach, referrals, and basic care.', 'image' => ''],
            ['title' => 'Relief', 'description' => 'Rapid support during urgent hardship.', 'image' => ''],
            ['title' => 'Partnership', 'description' => 'Local work with shared accountability.', 'image' => ''],
        ];
        $programCount = $programs->count();
        $impactCards = array_slice(array_values($featureCards), 0, 4);
        $resolveImageUrl = static function (?string $image): string {
            $image = trim((string) $image);
            if ($image === '') {
                return '';
            }
            if (preg_match('#^https?://#i', $image)) {
                return $image;
            }
            return asset(ltrim($image, '/'));
        };
    @endphp

    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[1.08fr_.92fr] lg:items-start">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $pageTitle }}</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                        Practical programs for real community needs
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600">
                        {{ $intro }}
                    </p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        @foreach (array_slice($impactCards, 0, 2) as $index => $card)
                            @php
                                $image = $resolveImageUrl($card['image'] ?? '');
                                $title = $card['title'] ?? '';
                                $description = $card['description'] ?? '';
                            @endphp
                            <article class="group overflow-hidden border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                                    @if (!empty($image))
                                        <img src="{{ $image }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 h-36 w-36 rounded-full bg-orange-500/10 blur-3xl"></div>
                    <div class="px-6 pt-6">
                        <div class="inline-flex rounded-full bg-[#F97316] px-4 py-1.5 text-xs font-bold uppercase tracking-[0.35em] text-white">
                            {{ $featureSectionTitle }}
                        </div>
                        <p class="mt-3 max-w-lg text-sm leading-6 text-slate-600">
                            {{ $featureSectionDescription }}
                        </p>
                    </div>

                    <div class="grid gap-4 p-6 pt-5 sm:grid-cols-2">
                        @foreach (array_slice($impactCards, 2) as $index => $card)
                            @php
                                $image = $resolveImageUrl($card['image'] ?? '');
                                $title = $card['title'] ?? '';
                                $description = $card['description'] ?? '';
                            @endphp
                            <article class="group overflow-hidden border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                                    @if (!empty($image))
                                        <img src="{{ $image }}" alt="{{ $title }}" class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                                    @else
                                        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(30,45,83,0.08),rgba(249,115,22,0.06)),radial-gradient(circle_at_top_right,rgba(255,255,255,0.95),transparent_35%)]"></div>
                                    @endif
                                    {{-- <div class="absolute inset-x-0 top-0 flex items-center justify-between px-4 pt-4">
                                        <span class="inline-flex items-center rounded-full bg-slate-900/75 px-3 py-1 text-[0.62rem] font-bold uppercase tracking-[0.35em] text-white backdrop-blur-sm">
                                            {{ $featureSectionTitle }}
                                        </span>
                                     
                                    </div> --}}
                                </div>
                                <div class="p-5">
                                    <h3 class="text-lg font-black text-slate-900">{{ $title }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-14 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">Active initiatives</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Programs currently in focus</h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-slate-600">
                    Each initiative is built to stay practical, visible, and rooted in the needs of the people we serve.
                </p>
            </div>

            @if ($programs->isNotEmpty())
                <div class="mt-10 grid gap-4 md:grid-cols-3 xl:grid-cols-4">
                    @foreach ($programs as $program)
                        <a href="{{ route('frontend.product.detail', ['product' => $program->slug]) }}" class="group overflow-hidden border border-slate-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-teal-900/10">
                            <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-teal-50 via-emerald-50 to-white">
                                @if ($program->getImageUrl())
                                    <img src="{{ $program->getImageUrl() }}" alt="{{ $program->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="flex h-full w-full items-center justify-center p-8 text-center">
                                        <div>
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-teal-700 text-white shadow-lg shadow-teal-900/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="m3 17 6-6 4 4 8-8"></path>
                                                </svg>
                                            </div>
                                            <h3 class="mt-4 text-2xl font-semibold text-slate-900">{{ $program->title }}</h3>
                                        </div>
                                    </div>
                                @endif

                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/65 via-slate-950/5 to-transparent p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/80">Community program</p>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $program->title }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($program->description ?? 'Programs are built around local needs and transparent support.', 140) }}
                                </p>
                                <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                                    <span class="text-slate-500">Tap to learn more</span>
                                    <span class="font-semibold text-teal-700">Read more</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="mt-10 rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-12 text-center shadow-sm">
                    <p class="text-lg font-semibold text-slate-900">No active programs yet</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Add a program in the admin panel and it will appear here automatically.
                    </p>
                </div>
            @endif
        </div>
    </section>

    @include('frontend.partials.supporters-strip', [
        'supporterLogos' => $supporterLogos,
        'supportersTitle' => $partnersTitle ?? 'Our supporters',
        'supportersDescription' => 'Organizations and partners who help make practical community support possible.',
    ])

    <section class="bg-[#f97316] text-white">
        <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1.1fr_.9fr] lg:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-white/80">Next step</p>
                    <h2 class="mt-4 max-w-2xl text-3xl font-black tracking-tight sm:text-4xl">
                        Support the work, share the story, or ask how to get involved.
                    </h2>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/90">
                        Programs have the strongest impact when communities, donors, and volunteers move together.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 lg:justify-end">
                    <a href="{{ route('frontend.contact') }}" class="rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-900 transition hover:scale-[1.02]">
                        Contact us
                    </a>
                    <a href="{{ route('frontend.donate') }}" class="rounded-full border border-white/30 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                        Donate now
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
