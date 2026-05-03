@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-24 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-bk-navy via-bk-navy/80 to-bk-orange/20"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">{{ $page->content['header']['title'] ?? 'Our Programs' }}</h1>
        <p class="text-gray-300 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
            {{ $page->content['header']['description'] ?? '' }}
        </p>
    </div>
</section>

@include('pages.partials.admin-body')

<!-- Programs Grid -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 gap-24">
            @php
                // For now use the programs from Home Page as example if not specifically defined for this page
                $programs = $page->content['programs'] ?? [
                    ['title' => 'Early Childhood Care', 'description' => 'Ensuring children aged 0-5 have access to quality care and early education.', 'icon' => 'book-open'],
                    ['title' => 'Primary Education', 'description' => 'Supporting local schools to improve the quality of teaching and learning.', 'icon' => 'graduation-cap'],
                ];
            @endphp

            @foreach($programs as $index => $program)
            <div class="flex flex-col {{ $index % 2 == 0 ? 'lg:flex-row' : 'lg:flex-row-reverse' }} items-center gap-16">
                <div class="flex-1">
                    <div class="w-20 h-20 bg-bk-navy/5 rounded-[2rem] flex items-center justify-center text-bk-navy mb-8">
                        <i data-lucide="{{ $program['icon'] ?? 'star' }}" class="w-10 h-10"></i>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-bk-navy mb-6 leading-tight">{{ $program['title'] ?? '' }}</h2>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        {{ $program['description'] ?? '' }}
                    </p>
                    <ul class="space-y-4 mb-10">
                        <li class="flex items-center gap-3 text-bk-navy font-bold">
                            <i data-lucide="check-circle" class="w-5 h-5 text-bk-orange"></i>
                            Community-led initiatives
                        </li>
                        <li class="flex items-center gap-3 text-bk-navy font-bold">
                            <i data-lucide="check-circle" class="w-5 h-5 text-bk-orange"></i>
                            Sustainable impact models
                        </li>
                    </ul>
                    <a href="{{ route('contact') }}" class="inline-block bg-bk-navy text-white px-10 py-4 rounded-full font-extrabold hover:bg-bk-orange transition-all">Details & Impact</a>
                </div>
                <div class="flex-1 w-full">
                    <div class="aspect-video bg-gray-100 rounded-[3rem] overflow-hidden shadow-2xl relative group">
                        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=2022&auto=format&fit=crop" alt="Program" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-bk-navy/40 to-transparent"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
