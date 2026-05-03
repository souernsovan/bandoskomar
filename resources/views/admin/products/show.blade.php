@extends('admin.layouts.app')

@section('title', $product->title)

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Program Details</h2>
            <p>View program information</p>
        </div>

        <div class="page-header-right">
            @can('products.edit')
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
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
                    <path d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $product->title }}</h3>
                <p>{{ $product->slug }}</p>
            </div>
        </div>

        <div class="detail-body">
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-5">
                    <div class="detail-item">
                        <label>Title</label>
                        <p>{{ $product->title }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Slug</label>
                        <p><code>{{ $product->slug }}</code></p>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="badge status-{{ $product->status }}">{{ ucfirst($product->status) }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Partner</label>
                        <span class="badge text-indigo bg-indigo">{{ $product->provider?->name ?? '-' }}</span>
                    </div>
                   
                    @if($product->description)
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <label>Description</label>
                        <p>{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="detail-section" bis_skin_checked="1">
                <h4 class="detail-section-title">Image</h4>
                <div class="detail-item" bis_skin_checked="1">
                    <div class="detail-image-preview" bis_skin_checked="1">
                        @if ($product->getImageUrl())
                            <a href="{{ $product->getImageUrl() }}" target="_blank">
                                <img src="{{ $product->getImageUrl() }}" alt="{{ $product->title }}">
                            </a>
                        @else
                            <div style="min-height: 180px; display: flex; align-items: center; justify-content: center; border-radius: 18px; background: #f8fafc; color: #64748b; font-weight: 600;">
                                No image uploaded for this program.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <p>{{ $product->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <p>{{ $product->updated_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Program ID</label>
                        <span class="uuid-cell">{{ $product->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
