@extends('frontend.layouts.app')

@section('content')
    @php
        $backUrl = route('frontend.product');
    @endphp

    <article class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
            <p class="mb-8">
                <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-sm font-semibold text-teal-700 hover:text-teal-900">
                    <span aria-hidden="true">←</span>
                    Back to programs
                </a>
            </p>

            <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-5">
                    <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-50 shadow-xl shadow-teal-900/5">
                        @if ($product->getImageUrl())
                            <img
                                src="{{ $product->getImageUrl() }}"
                                alt="{{ $product->title }}"
                                class="h-full w-full object-cover"
                                loading="eager"
                                width="640"
                                height="640"
                            >
                        @else
                            <div class="flex aspect-square items-center justify-center p-10 text-center">
                                <div>
                                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900">
                                        {{ $product->title }}
                                    </h1>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">Program detail</p>
                    <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
                        {{ $product->title }}
                    </h1>

                    @if (!empty($product->description))
                        <div class="mt-8 space-y-4 text-base leading-7 text-slate-600">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    @else
                        <p class="mt-8 text-base leading-7 text-slate-600">
                            This program is being prepared and will be updated with more details soon.
                        </p>
                    @endif

                    <div class="mt-10 rounded-3xl border border-slate-200 bg-slate-50 p-6">
                        <h2 class="text-lg font-semibold text-slate-900">How this helps</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Each program is designed to support real needs in the community, with a focus on dignity,
                            transparency, and measurable impact.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection
