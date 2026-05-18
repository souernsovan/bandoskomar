@extends('frontend.layouts.app')

@section('content')
    @php
        $c = $page->getPageContentForLocale();
        $galleryGroups = collect($galleryGroups ?? []);
        $gallerySummary = $gallerySummary ?? [
            'total' => 0,
            'folders' => 0,
            'formats' => [],
        ];
        $galleryFormats = collect($gallerySummary['formats'] ?? []);
        $galleryIntro = $c['gallery_intro'] ?? 'This archive is generated from the live public/images directory so every new asset can be reviewed in one place.';

        $stats = [
            ['label' => 'Images', 'value' => number_format((int) ($gallerySummary['total'] ?? 0))],
            ['label' => 'Folders', 'value' => number_format((int) ($gallerySummary['folders'] ?? 0))],
            ['label' => 'Formats', 'value' => number_format($galleryFormats->count())],
        ];
    @endphp

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <article class="rounded-[2rem] border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-teal-700">Public archive</p>
                    <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                        All website images in one place
                    </h2>
                    <p class="mt-4 max-w-3xl text-base leading-7 text-slate-600">
                        {{ $galleryIntro }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @forelse ($galleryFormats as $format)
                            <span class="rounded-full bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-slate-600 shadow-sm">
                                {{ $format }}
                            </span>
                        @empty
                            <span class="rounded-full bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-slate-600 shadow-sm">
                                Live archive
                            </span>
                        @endforelse
                    </div>
                </article>

                <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                    @foreach ($stats as $stat)
                        <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-500">{{ $stat['label'] }}</p>
                            <h3 class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ $stat['value'] }}</h3>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-50">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            @forelse ($galleryGroups as $group)
                <section class="mb-14 last:mb-0">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-orange-700">Folder</p>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $group['label'] }}</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                {{ number_format((int) $group['count']) }} asset{{ (int) $group['count'] === 1 ? '' : 's' }}
                            </p>
                        </div>
                        <div class="inline-flex rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm">
                            {{ $group['folder'] }}
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($group['images'] as $image)
                            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-teal-900/10">
                                <a href="{{ $image['url'] }}" target="_blank" rel="noreferrer" class="block aspect-[4/3] bg-slate-100">
                                    <img src="{{ $image['url'] }}" alt="{{ $image['label'] }}" class="h-full w-full object-contain p-4" loading="lazy">
                                </a>
                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h3 class="text-base font-semibold text-slate-900">{{ $image['label'] }}</h3>
                                            <p class="mt-1 text-xs uppercase tracking-[0.3em] text-slate-500">{{ $image['extension'] }}</p>
                                        </div>
                                        <a href="{{ $image['url'] }}" target="_blank" rel="noreferrer" class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700 transition hover:bg-teal-100">
                                            Open
                                        </a>
                                    </div>
                                    <p class="mt-4 break-all rounded-2xl bg-slate-50 px-4 py-3 font-mono text-xs leading-5 text-slate-500">
                                        {{ $image['path'] }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-10 text-center">
                    <h2 class="text-2xl font-bold text-slate-900">No images found</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        The gallery scans the public/images directory. Add image files there and refresh this page to see them appear.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
