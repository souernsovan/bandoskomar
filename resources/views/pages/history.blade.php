@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-6 tracking-tight">{{ $page->content['header']['title'] ?? 'Our History' }}</h1>
        <p class="text-gray-300 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            {{ $page->content['header']['description'] ?? '' }}
        </p>
    </div>
</section>

<!-- Timeline Section -->
<section class="py-24 bg-white relative">
    <!-- Vertical Line -->
    <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-1 bg-gray-100 z-0"></div>

    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="space-y-24">
            @if(isset($page->content['timeline']))
                @foreach($page->content['timeline'] as $index => $item)
                @if(isset($item['year']) && $item['year'])
                <div class="flex flex-col {{ $index % 2 == 0 ? 'md:flex-row' : 'md:flex-row-reverse' }} items-center gap-8 md:gap-16">
                    <div class="flex-1 text-center {{ $index % 2 == 0 ? 'md:text-right' : 'md:text-left' }}">
                        <span class="inline-block px-6 py-2 bg-bk-orange text-white font-black text-2xl rounded-full mb-4 shadow-lg shadow-bk-orange/30">{{ $item['year'] }}</span>
                        <h3 class="text-2xl md:text-3xl font-black text-bk-navy mb-4">{{ $item['title'] ?? '' }}</h3>
                        <p class="text-gray-600 leading-relaxed max-w-lg {{ $index % 2 == 0 ? 'ml-auto' : 'mr-auto' }}">
                            {{ $item['description'] ?? '' }}
                        </p>
                    </div>
                    
                    <!-- Center Point -->
                    <div class="w-12 h-12 bg-white border-4 border-bk-navy rounded-full z-20 flex items-center justify-center">
                        <div class="w-4 h-4 bg-bk-orange rounded-full animate-pulse"></div>
                    </div>

                    <div class="flex-1 hidden md:block">
                        <div class="bg-gray-50 p-8 rounded-[2rem] border border-gray-100 opacity-40 hover:opacity-100 transition-opacity">
                            <i data-lucide="history" class="w-12 h-12 text-bk-navy/10"></i>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            @endif
        </div>
    </div>
</section>
@endsection
