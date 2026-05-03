@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-20 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-bk-orange opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="max-w-3xl">
            <span class="text-bk-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ $page->content['header']['badge'] ?? 'About Us' }}</span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">{{ $page->content['header']['title'] ?? 'Our Mission & Vision' }}</h1>
            <p class="text-gray-300 text-lg md:text-xl leading-relaxed">
                {{ $page->content['header']['description'] ?? '' }}
            </p>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="relative">
                <div class="absolute -top-10 -left-10 w-32 h-32 bg-bk-orange/10 rounded-full z-0"></div>
                <img src="{{ $page->content['mission']['image'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop' }}" alt="Impact" class="rounded-[3rem] shadow-2xl relative z-10 w-full h-[500px] object-cover">
                @if(isset($page->content['mission']['quote']))
                <div class="absolute -bottom-6 -right-6 bg-bk-orange p-8 rounded-3xl shadow-xl z-20 text-white max-w-[240px]">
                    <p class="text-sm font-bold opacity-80 mb-1 italic">"{{ $page->content['mission']['quote'] }}"</p>
                </div>
                @endif
            </div>

            <div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-bk-navy mb-8 leading-tight">{{ $page->content['mission']['title'] ?? 'Transforming Education' }}</h2>
                <div class="space-y-6 text-gray-600 text-lg leading-relaxed">
                    @php 
                        $description = $page->content['mission']['description'] ?? '';
                        $paragraphs = explode("\n", $description);
                    @endphp
                    @foreach($paragraphs as $para)
                        @if(trim($para))
                        <p>{{ trim($para) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
@if(isset($page->content['values']['items']))
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl md:text-5xl font-extrabold text-bk-navy mb-6 tracking-tight">{{ $page->content['values']['title'] ?? 'Our Core Values' }}</h2>
            <div class="w-20 h-1.5 bg-bk-orange mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            @foreach($page->content['values']['items'] as $item)
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-xl transition-all group">
                <div class="w-14 h-14 bg-bk-navy/5 rounded-2xl flex items-center justify-center text-bk-navy mb-6 group-hover:bg-bk-navy group-hover:text-white transition-all">
                    <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-extrabold text-bk-navy mb-4">{{ $item['title'] ?? '' }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $item['description'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
