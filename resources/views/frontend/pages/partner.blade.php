@extends('frontend.layouts.app')
@section('content')
    @php
        $featuredImage = $partnerFeatureImage ?? 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1600&auto=format&fit=crop';
        $featuredImage = preg_match('#^https?://#i', trim((string) $featuredImage))
            ? trim((string) $featuredImage)
            : asset(ltrim(trim((string) $featuredImage), '/'));
        $partnerFeatures = $partnerFeatures ?? [
            ['title' => 'Funding & grants', 'description' => 'Support programs, materials, teacher stipends, and infrastructure projects.'],
            ['title' => 'In-kind expertise', 'description' => 'Offer training, curriculum resources, monitoring & evaluation, or tech support.'],
        ];
    @endphp

<section class="relative overflow-hidden border-y border-slate-200 bg-slate-50">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(246,139,30,0.08),transparent_28%),radial-gradient(circle_at_bottom_right,rgba(30,45,83,0.05),transparent_24%)]"></div>
    <div class="relative mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.05fr_.95fr] lg:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.45em] text-teal-700">Partnerships</p>
                <h2 class="mt-4 max-w-2xl text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                    {{ $partnerTitle ?? 'How you can partner' }}
                </h2>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-600">
                    {{ $partnerDescription ?? 'We work with schools, donors, corporations, and NGOs to support education programs.' }}
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('frontend.contact') }}" class="rounded-full bg-[#F97316] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700">
                        Contact us
                    </a>
                    <a href="{{ route('frontend.product') }}" class="rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-900 transition hover:border-slate-400 hover:bg-slate-50">
                        See programs
                    </a>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    @foreach ($partnerFeatures as $index => $feature)
                        <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full {{ $index === 0 ? 'bg-orange-500/10 text-orange-600' : 'bg-teal-700/10 text-teal-700' }} text-lg font-black">
                                    {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <h3 class="text-lg font-bold text-slate-900">{{ $feature['title'] ?? '' }}</h3>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-slate-600">
                                {{ $feature['description'] ?? '' }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-6 -top-6 h-28 w-28 rounded-full bg-orange-500/10 blur-2xl"></div>
                <div class="overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">
                    <div class="relative aspect-[4/4.5]">
                        <img src="{{ $featuredImage }}" alt="Partnership feature" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/10 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                            <p class="text-xs font-semibold uppercase tracking-[0.45em] text-orange-300">Why partner</p>
                            <h3 class="mt-3 max-w-md text-2xl font-black leading-tight">
                                Local partnerships keep support practical, visible, and accountable.
                            </h3>
                            <p class="mt-3 max-w-lg text-sm leading-6 text-white/85">
                                Community work is strongest when resources, expertise, and trust move together.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-8 left-6 right-6 rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-2xl shadow-slate-900/10">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-orange-500">Next step</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">Tell us how you want to contribute, and we’ll shape the partnership around it.</p>
                        </div>
                        <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-full bg-teal-700 text-white md:flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M13 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('frontend.partials.supporters-strip', [
    'supporterLogos' => is_array($partnerImages ?? null) ? $partnerImages : [],
    'supportersTitle' => $supportersTitle ?? 'Our supporters',
    'supportersDescription' => $supportersDescription ?? 'Partners and organizations helping grow practical education and community support.',
])

@endsection
