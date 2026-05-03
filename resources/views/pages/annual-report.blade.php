@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">{{ $page->content['header']['title'] ?? 'Annual Reports' }}</h1>
        <p class="text-gray-300 text-lg md:text-xl max-w-2xl leading-relaxed">
            {{ $page->content['header']['description'] ?? '' }}
        </p>
    </div>
</section>

@include('pages.partials.admin-body')

<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if(isset($page->content['reports']))
                @foreach($page->content['reports'] as $report)
                <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100 hover:shadow-xl transition-all group">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-bk-orange mb-6 shadow-sm">
                        <i data-lucide="file-text" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-bk-navy mb-2">{{ $report['title'] ?? '' }}</h3>
                    <span class="block text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Year: {{ $report['year'] ?? '' }}</span>
                    <a href="{{ !empty($report['link']) && $report['link'] !== '#' ? $report['link'] : route('contact') }}" class="inline-flex items-center gap-2 text-bk-navy font-bold hover:text-bk-orange transition-colors">
                        Download Report <i data-lucide="download" class="w-4 h-4"></i>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>
@endsection
