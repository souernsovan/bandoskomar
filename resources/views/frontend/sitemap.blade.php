{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($pages as $page)
    <url>
        <loc>{{ $page->url }}</loc>
        <lastmod>{{ $page->updated_at->toW3cString() }}</lastmod>
        <changefreq>{{ $page->slug === 'home' ? 'daily' : 'weekly' }}</changefreq>
        <priority>{{ $page->slug === 'home' ? '1.0' : '0.8' }}</priority>
    </url>
@endforeach
</urlset>
