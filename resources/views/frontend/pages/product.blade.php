@extends('frontend.layouts.app')

@section('content')
    @php
        $programs = $products ?? collect();
        $supporterLogos = array_values(array_filter(is_array($partnerImages ?? null) ? $partnerImages : []));
        $description = $description ?? 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.';
    @endphp


    @if ($programs->isNotEmpty())
        <section class="bg-slate-50">
            <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $productsTitle ?? 'Our Programs' }}</p>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Active initiatives</h2>
                    </div>
                    <p class="max-w-xl text-sm leading-6 text-slate-600">
                        Each initiative is shaped to strengthen communities and respond to real needs on the ground.
                    </p>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($programs as $program)
                        <a href="{{ route('frontend.product.detail', ['product' => $program->slug]) }}" class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-teal-900/10">
                            <div class="aspect-[4/3] bg-gradient-to-br from-teal-50 via-emerald-50 to-white">
                                @if ($program->getImageUrl())
                                    <img src="{{ $program->getImageUrl() }}" alt="{{ $program->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="flex h-full w-full items-center justify-center p-8 text-center">
                                        <div>
                                            <h3 class="mt-3 text-2xl font-semibold text-slate-900">{{ $program->title }}</h3>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="text-xl font-semibold text-slate-900">{{ $program->title }}</h3>
                                </div>
                                <p class="mt-3 text-sm leading-6 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($program->description ?? 'Programs are built around local needs and transparent support.', 140) }}
                                </p>
                                <div class="mt-5 flex items-center justify-between gap-4 text-sm">
                                    <span class="text-slate-500">Community program</span>
                                    <span class="font-semibold text-teal-700">Read more</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">{{ $partnersTitle ?? 'Our supporters' }}</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        Working with people who believe in community care
                    </h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-slate-600">
                    Supporter logos and partner marks are displayed here as the network grows.
                </p>
            </div>

            <div class="mt-8 flex flex-wrap gap-4">
                @forelse ($supporterLogos as $logo)
                    <div class="flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 shadow-sm">
                        <img src="{{ asset($logo) }}" alt="Supporter logo" loading="lazy" class="max-h-10 w-auto object-contain">
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-3 text-sm text-slate-600">
                        Supporter logos will appear here once uploaded.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
