@extends('frontend.layouts.app')
@section('content')

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Goal 1: Access</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Expand reach into rural schools and underserved communities by 2028.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Goal 2: Quality</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Improve teacher training and learning materials to raise student outcomes.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Goal 3: Sustainability</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Build local capacity and community-led governance for long-term impact.</p>
            </article>
        </div>
    </div>
</section>
@endsection
