@extends('frontend.layouts.app')
@section('content')

<section class="border-y border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2">
            @for($i=1;$i<=4;$i++)
                <article class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm flex gap-4">
                    <div class="aspect-video w-40 flex-shrink-0 rounded-xl bg-slate-200 flex items-center justify-center text-slate-400 text-sm">Video {{ $i }}</div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Story {{ $i }}: Title here</h3>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Short description of this video story and its impact.</p>
                    </div>
                </article>
            @endfor
        </div>
    </div>
</section>
@endsection
