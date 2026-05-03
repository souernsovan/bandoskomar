@extends('layouts.app')

@section('content')
<section class="bg-bk-navy py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight">
            {{ $page->content['header']['title'] ?? $page->title }}
        </h1>
        @if(!empty($page->content['header']['description']))
            <p class="text-gray-300 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                {{ $page->content['header']['description'] }}
            </p>
        @endif
    </div>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
        <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed">
            @if(!empty($page->content['body']))
                {!! nl2br(e($page->content['body'])) !!}
            @else
                <p>This page is being prepared.</p>
            @endif
        </div>
    </div>
</section>
@endsection
