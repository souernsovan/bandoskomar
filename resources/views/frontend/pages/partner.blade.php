@extends('frontend.layouts.app')
@section('content')

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">How you can partner</h2>
            <p class="mt-4 text-base leading-7 text-slate-600">We work with schools, donors, corporations, and NGOs to support education programs.</p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Funding & Grants</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Support programs, materials, teacher stipends, and infrastructure projects.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">In-kind & Expertise</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Offer training, curriculum resources, monitoring & evaluation, or tech support.</p>
            </article>
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('frontend.contact') }}" class="rounded-full bg-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700">Contact us to discuss a partnership</a>
        </div>
    </div>
</section>
@endsection
