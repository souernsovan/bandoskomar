@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-20 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-bk-orange opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <div class="max-w-3xl mx-auto">
            <span class="text-bk-orange font-bold uppercase tracking-widest text-sm mb-4 block">{{ $page->content['header']['badge'] ?? 'Make an Impact' }}</span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">{{ $page->content['header']['title'] ?? 'Support Our Mission' }}</h1>
            <p class="text-gray-300 text-lg md:text-xl leading-relaxed">
                {{ $page->content['header']['description'] ?? 'Your generosity empowers children and transforms communities. Choose how you want to make a difference today.' }}
            </p>
        </div>
    </div>
</section>

@include('pages.partials.admin-body')

<!-- Donation Options -->
<section class="py-24 bg-white relative">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 max-w-6xl mx-auto">
            
            <!-- Option 1: General Donation -->
            <div class="bg-white rounded-[2.5rem] shadow-lg border border-gray-100 overflow-hidden group hover:-translate-y-2 transition-transform duration-300">
                <div class="h-48 bg-bk-navy/5 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-bk-navy opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i data-lucide="arrow-right" class="w-12 h-12 text-white transform translate-x-4 group-hover:translate-x-0 transition-transform"></i>
                    </div>
                    <i data-lucide="wallet" class="w-20 h-20 text-bk-navy group-hover:scale-110 transition-transform duration-500"></i>
                </div>
                <div class="p-10 text-center">
                    <h3 class="text-2xl font-black text-bk-navy mb-4">General Donation</h3>
                    <p class="text-gray-500 mb-8">Make a one-time or recurring donation to support our most urgent needs and core programs.</p>
                    <a href="{{ route('get-involved.support-us') }}" class="block w-full bg-bk-orange text-white py-4 rounded-xl font-bold uppercase tracking-wider hover:bg-orange-600 transition-colors shadow-md">Donate Now</a>
                </div>
            </div>

            <!-- Option 2: Sponsor a Child -->
            <div class="bg-white rounded-[2.5rem] shadow-lg border border-gray-100 overflow-hidden group hover:-translate-y-2 transition-transform duration-300 relative transform md:-translate-y-6">
                <!-- Highlight badge -->
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 bg-bk-orange text-white px-6 py-1.5 rounded-b-xl text-xs font-bold uppercase tracking-widest z-10">Most Impactful</div>
                
                <div class="h-48 bg-bk-orange/10 flex items-center justify-center relative overflow-hidden mt-6">
                    <div class="absolute inset-0 bg-bk-orange opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i data-lucide="arrow-right" class="w-12 h-12 text-white transform translate-x-4 group-hover:translate-x-0 transition-transform"></i>
                    </div>
                    <i data-lucide="user-plus" class="w-20 h-20 text-bk-orange group-hover:scale-110 transition-transform duration-500"></i>
                </div>
                <div class="p-10 text-center">
                    <h3 class="text-2xl font-black text-bk-navy mb-4">Sponsor a Child</h3>
                    <p class="text-gray-500 mb-8">Provide ongoing support to a specific child, ensuring they have access to education and health resources.</p>
                    <a href="{{ route('get-involved.sponsor-child') }}" class="block w-full bg-bk-navy text-white py-4 rounded-xl font-bold uppercase tracking-wider hover:bg-blue-900 transition-colors shadow-md">Learn More</a>
                </div>
            </div>

            <!-- Option 3: Other Ways to Give -->
            <div class="bg-white rounded-[2.5rem] shadow-lg border border-gray-100 overflow-hidden group hover:-translate-y-2 transition-transform duration-300">
                <div class="h-48 bg-gray-50 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gray-200 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <i data-lucide="arrow-right" class="w-12 h-12 text-gray-700 transform translate-x-4 group-hover:translate-x-0 transition-transform"></i>
                    </div>
                    <i data-lucide="gift" class="w-20 h-20 text-gray-400 group-hover:scale-110 transition-transform duration-500"></i>
                </div>
                <div class="p-10 text-center">
                    <h3 class="text-2xl font-black text-bk-navy mb-4">Other Ways to Give</h3>
                    <p class="text-gray-500 mb-8">Discover alternative ways to contribute, including corporate partnerships, legacy gifts, and in-kind donations.</p>
                    <a href="{{ route('get-involved.ways-to-give') }}" class="block w-full bg-gray-100 text-bk-navy py-4 rounded-xl font-bold uppercase tracking-wider hover:bg-gray-200 transition-colors shadow-sm border border-gray-200">Explore Options</a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Secure Trust Banner -->
<section class="py-12 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 text-center">
        <div class="flex flex-col md:flex-row items-center justify-center gap-6">
            <i data-lucide="shield-check" class="w-10 h-10 text-green-500"></i>
            <p class="text-gray-600 font-medium text-lg">
                Your donations are secure and <strong class="text-bk-navy">tax-deductible</strong>. We ensure 100% transparency in how your funds are used.
            </p>
        </div>
    </div>
</section>
@endsection
