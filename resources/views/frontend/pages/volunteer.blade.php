@extends('frontend.layouts.app')
@section('content')

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Ways to get involved</h2>
            <p class="mt-4 text-base leading-7 text-slate-600">Short-term and long-term opportunities to support programs and events.</p>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Local support</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Assist with tutoring, classroom support, or organizing community workshops.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Events & drives</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Help plan and run donation drives, school events, or fundraising campaigns.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Skill-based</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Offer expertise (teaching, translation, IT, design) to support operations.</p>
            </article>
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-slate-900">Remote</h3>
                <p class="mt-2 text-sm leading-6 text-slate-600">Contribute online (content, grant writing, mentoring) from anywhere.</p>
            </article>
        </div>
        <div class="mt-8 text-center">
            <a href="{{ route('frontend.contact') }}" class="rounded-full bg-orange-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-orange-600/20 transition hover:bg-orange-700">Register interest or ask questions</a>
        </div>
    </div>
</section>
@endsection
