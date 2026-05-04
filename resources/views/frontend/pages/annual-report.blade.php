@extends('frontend.layouts.app')

@section('content')


    <section id="highlights" class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-700">Highlights</p>
                <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Key outcomes this year</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">Selected metrics and program outcomes from our annual reporting period.</p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">Students reached</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $page->content['students_reached'] ?? '5,000+' }} students supported in rural and peri-urban schools this year.</p>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">Community partners</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $page->content['partners_count'] ?? '30+' }} local partners and schools engaged across Cambodia.</p>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="mt-5 text-xl font-semibold text-slate-900">Budget allocated</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $page->content['budget'] ?? '$250,000' }} allocated to programs, materials, and teacher support.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="downloads" class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Reports & Downloads</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">Full annual reports, financial statements, and program evaluations.</p>
            </div>
            <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                <a href="#" class="rounded-full bg-orange-600 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700">Annual Report 2024 (PDF)</a>
                <a href="#" class="rounded-full border border-slate-300 bg-white px-8 py-3 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700">Financial Summary 2024</a>
                <a href="#" class="rounded-full border border-slate-300 bg-white px-8 py-3 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700">Strategic Evaluation</a>
            </div>
        </div>
    </section>
@endsection
