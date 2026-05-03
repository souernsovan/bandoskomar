@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="bg-bk-navy py-20 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-bk-orange opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="max-w-3xl">
            <span class="text-bk-orange font-bold uppercase tracking-widest text-sm mb-4 block">Our Stories</span>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">Latest News & Updates</h1>
            <p class="text-gray-300 text-lg md:text-xl leading-relaxed">
                Stay updated with our latest workshops, community impact stories, and upcoming events.
            </p>
        </div>
    </div>
</section>

<!-- Posts Grid -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($posts as $post)
            <div class="group bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col h-full">
                <!-- Post Thumbnail -->
                <div class="h-64 overflow-hidden relative">
                    @php
                        $bgColors = ['bg-bk-navy', 'bg-bk-orange', 'bg-bk-blue'];
                        $bgColor = $bgColors[$loop->index % count($bgColors)];
                    @endphp
                    <div class="w-full h-full {{ $bgColor }} flex items-center justify-center text-white/20 group-hover:scale-110 transition-transform duration-700">
                        <i data-lucide="image" class="w-20 h-20"></i>
                    </div>
                    <div class="absolute top-6 left-6">
                        <span class="bg-white/90 backdrop-blur-sm text-bk-navy px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                            {{ $post->category->name ?? 'Update' }}
                        </span>
                    </div>
                </div>

                <!-- Post Content -->
                <div class="p-10 flex flex-col flex-1">
                    <div class="flex items-center gap-3 text-gray-400 text-xs font-bold uppercase tracking-widest mb-4">
                        <i data-lucide="calendar" class="w-4 h-4 text-bk-orange"></i>
                        {{ $post->created_at->format('M d, Y') }}
                    </div>
                    <h2 class="text-2xl font-black text-bk-navy mb-4 leading-tight group-hover:text-bk-orange transition-colors">
                        {{ $post->title }}
                    </h2>
                    <p class="text-gray-500 text-sm leading-relaxed mb-8 flex-1">
                        {{ Str::limit($post->content, 120) }}
                    </p>
                    <div class="pt-6 border-t border-gray-50 mt-auto">
                        <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center gap-2 text-bk-navy font-black text-sm uppercase tracking-wider group/link">
                            Read Full Story 
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover/link:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-gray-300">
                    <i data-lucide="file-x" class="w-10 h-10"></i>
                </div>
                <h3 class="text-2xl font-bold text-bk-navy mb-2">No Stories Yet</h3>
                <p class="text-gray-500">We're currently writing some amazing stories. Check back soon!</p>
            </div>
            @endforelse
        </div>

        <div class="mt-16 flex justify-center">
            {{ $posts->links() }}
        </div>
    </div>
</section>

<!-- Newsletter CTA -->
<section class="py-24 bg-gray-50">
    <div class="container mx-auto px-4 md:px-6">
        <div class="bg-white rounded-[3rem] p-12 md:p-20 shadow-xl border border-gray-100 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="max-w-xl">
                <h2 class="text-3xl md:text-4xl font-black text-bk-navy mb-4">Stay in the Loop</h2>
                <p class="text-gray-500 text-lg leading-relaxed">
                    Subscribe to our newsletter to receive the latest news, success stories, and impact reports directly in your inbox.
                </p>
            </div>
            <div class="w-full max-w-md">
                <form class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="Your email address" class="flex-1 px-6 py-4 rounded-full bg-gray-50 border border-gray-200 focus:outline-none focus:border-bk-orange text-sm font-medium">
                    <button type="submit" class="bg-bk-navy text-white px-8 py-4 rounded-full font-bold text-sm uppercase tracking-wider hover:bg-bk-orange transition-all">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
