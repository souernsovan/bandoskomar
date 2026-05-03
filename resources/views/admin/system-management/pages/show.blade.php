@extends('admin.layouts.app')

@section('title', $page->title)

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Website Page Details</h2>
            <p>{{ $page->title }}</p>
        </div>
        <div class="page-header-right">
            @can('pages.edit')
            <a href="{{ route('system-management.pages.edit', $page) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('system-management.pages.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <div class="banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $page->title }}</h3>
                <p><code>{{ $page->slug }}</code></p>
            </div>
        </div>

        <div class="detail-body">
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Title</label>
                        <p>{{ $page->title }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Slug</label>
                        <p><code>{{ $page->slug }}</code></p>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="badge {{ $page->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $page->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if($page->route_name)
                    <div class="detail-item">
                        <label>Route Name</label>
                        <p><code>{{ $page->route_name }}</code></p>
                    </div>
                    @endif
                    <div class="detail-item">
                        <label>Menu Placement</label>
                        <span class="badge {{ $page->getMenuGroup() === 'main' ? 'status-active' : ($page->getMenuGroup() === 'hidden' ? 'status-inactive' : 'role-staff') }}">
                            {{ $page->getMenuGroupLabel() }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Frontend URL</label>
                        <p><a href="{{ $page->url }}" target="_blank" rel="noopener">{{ $page->url }}</a></p>
                    </div>
                    <div class="detail-item">
                        <label>Sitemap</label>
                        <p>{{ $page->sitemap_include ? 'Included' : 'Excluded' }}</p>
                    </div>
                    @if($page->content)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <label>Content</label>
                        <div class="meta-content">{!! nl2br(e(Str::limit($page->content, 500))) !!}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">SEO Settings</h4>
                <div class="detail-grid form-grid-3">
                    @if($page->meta_title)
                    <div class="detail-item">
                        <label>Meta Title</label>
                        <p>{{ $page->meta_title }}</p>
                    </div>
                    @endif
                    @if($page->meta_description)
                    <div class="detail-item" style="grid-column: span 2;">
                        <label>Meta Description</label>
                        <p>{{ $page->meta_description }}</p>
                    </div>
                    @endif
                    @if($page->canonical_url)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <label>Canonical URL</label>
                        <p><a href="{{ $page->canonical_url }}" target="_blank" rel="noopener">{{ $page->canonical_url }}</a></p>
                    </div>
                    @endif
                    @if($page->og_tags && count(array_filter($page->og_tags)))
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <label>Open Graph Tags</label>
                        <div class="detail-grid form-grid-2">
                            @if(!empty($page->og_tags['og_title']))
                            <div class="detail-item">
                                <label>OG Title</label>
                                <p>{{ $page->og_tags['og_title'] }}</p>
                            </div>
                            @endif
                            @if(!empty($page->og_tags['og_image']))
                            <div class="detail-item">
                                <label>OG Image</label>
                                <p><a href="{{ $page->og_tags['og_image'] }}" target="_blank" rel="noopener">{{ Str::limit($page->og_tags['og_image'], 50) }}</a></p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if($page->structured_data && count($page->structured_data))
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <label>Structured Data (JSON-LD)</label>
                        <pre class="meta-json">{{ json_encode($page->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                    @endif
                    @php $hasSeo = $page->meta_title || $page->meta_description || $page->canonical_url || ($page->og_tags && count(array_filter($page->og_tags))) || ($page->structured_data && is_array($page->structured_data) && count($page->structured_data)); @endphp
                    @if(!$hasSeo)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <p class="text-muted">No SEO settings configured for this page.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <p>{{ $page->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <p>{{ $page->updated_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Page ID</label>
                        <span class="uuid-cell">{{ $page->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
