@if(!empty($page->content['body']))
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
        <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed">
            {!! nl2br(e($page->content['body'])) !!}
        </div>
    </div>
</section>
@endif
