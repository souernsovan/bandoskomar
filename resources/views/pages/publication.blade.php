@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">{{ $page->content['header']['title'] ?? 'Publications' }}</h1>
        <p class="text-gray-300 text-lg md:text-xl max-w-2xl leading-relaxed">
            {{ $page->content['header']['description'] ?? '' }}
        </p>
    </div>
</section>

@include('pages.partials.admin-body')

<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="space-y-6">
            @if(isset($page->content['items']))
                @foreach($page->content['items'] as $item)
                <div class="flex items-center justify-between p-8 bg-gray-50 rounded-3xl border border-gray-100 hover:bg-white hover:shadow-lg transition-all group">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 bg-bk-navy/5 rounded-xl flex items-center justify-center text-bk-navy group-hover:bg-bk-navy group-hover:text-white transition-all">
                            <i data-lucide="book" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-bk-navy mb-1">{{ $item['title'] ?? '' }}</h3>
                            <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">{{ $item['type'] ?? 'PDF' }}</span>
                        </div>
                    </div>
                    <a href="{{ !empty($item['link']) && $item['link'] !== '#' ? $item['link'] : route('contact') }}" class="bg-white border-2 border-bk-navy/10 text-bk-navy px-8 py-3 rounded-full font-extrabold hover:bg-bk-navy hover:text-white transition-all">
                        View Publication
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>
@endsection
