@extends('layouts.app')

@section('content')
<!-- Article Header -->
<section class="bg-bk-navy pt-32 pb-20 relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-bk-orange opacity-5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="flex items-center justify-center gap-4 mb-8">
                <span class="bg-bk-orange text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-bk-orange/20">
                    {{ $post->category->name ?? 'Update' }}
                </span>
                <span class="text-gray-400 text-xs font-bold uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-bk-orange"></i>
                    {{ $post->created_at->format('M d, Y') }}
                </span>
            </div>
            <h1 class="text-4xl md:text-6xl font-black text-white mb-8 leading-tight tracking-tight">
                {{ $post->title }}
            </h1>
            <div class="flex items-center justify-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-white font-bold border border-white/10">
                    {{ substr($post->user->name ?? 'A', 0, 1) }}
                </div>
                <div class="text-left">
                    <p class="text-white font-bold text-sm">{{ $post->user->name ?? 'Admin' }}</p>
                    <p class="text-gray-400 text-xs">Author</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<article class="py-24 bg-white relative">
    <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto">
            <!-- Featured Image Placeholder -->
            <div class="w-full h-[400px] bg-gray-50 rounded-[3rem] mb-16 flex items-center justify-center text-gray-200 border border-gray-100 shadow-inner group overflow-hidden">
                <i data-lucide="image" class="w-32 h-32 group-hover:scale-110 transition-transform duration-700"></i>
            </div>

            <!-- Content -->
            <div class="prose prose-lg prose-gray max-w-none prose-headings:text-bk-navy prose-headings:font-black prose-p:text-gray-600 prose-p:leading-relaxed prose-strong:text-bk-navy prose-blockquote:border-bk-orange prose-blockquote:bg-gray-50 prose-blockquote:py-2 prose-blockquote:px-8 prose-blockquote:rounded-r-2xl prose-img:rounded-3xl shadow-none">
                {!! nl2br(e($post->content)) !!}
            </div>

            <!-- Tags/Footer -->
            <div class="mt-16 pt-10 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-bold text-gray-400 uppercase tracking-widest">Share this story:</span>
                    <div class="flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-bk-navy hover:bg-bk-navy hover:text-white transition-all"><i data-lucide="facebook" class="w-4 h-4"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-bk-navy hover:bg-bk-navy hover:text-white transition-all"><i data-lucide="twitter" class="w-4 h-4"></i></a>
                        <a href="{{ request()->fullUrl() }}" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-bk-navy hover:bg-bk-navy hover:text-white transition-all"><i data-lucide="link" class="w-4 h-4"></i></a>
                    </div>
                </div>
                <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-3 rounded-full font-bold text-sm uppercase tracking-wider hover:bg-bk-orange transition-all shadow-lg">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to News
                </a>
            </div>
        </div>
    </div>
</article>

<!-- Recent Posts / Related -->
<section class="py-24 bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-4 md:px-6 text-center">
        <h2 class="text-3xl font-black text-bk-navy mb-12">More Stories to Read</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Sample Related Posts -->
            @foreach(\App\Models\Post::where('id', '!=', $post->id)->where('status', 'published')->take(3)->get() as $related)
            <a href="{{ route('posts.show', $related) }}" class="group bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all text-left">
                <div class="flex items-center gap-3 text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                    <i data-lucide="calendar" class="w-3 h-3 text-bk-orange"></i>
                    {{ $related->created_at->format('M d, Y') }}
                </div>
                <h3 class="text-lg font-black text-bk-navy mb-4 group-hover:text-bk-orange transition-colors line-clamp-2">
                    {{ $related->title }}
                </h3>
                <span class="inline-flex items-center gap-1 text-bk-navy font-bold text-xs uppercase tracking-wider">
                    Read Story <i data-lucide="chevron-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
