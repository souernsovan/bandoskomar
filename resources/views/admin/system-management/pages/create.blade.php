@extends('admin.layouts.app')

@section('title', 'Create Website Page')

@section('content')
        <div class="page-header">
        <div class="page-header-left">
            <h2>Create Website Page</h2>
            <p>Create a new website page with SEO and routing settings</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('system-management.pages.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.pages.store') }}" method="POST">
            @csrf
            @php $createLocales = \App\Support\PageLocales::labels(); @endphp

            <div class="edit-page-section mb-0 pb-0">
                <label class="form-label" style="margin-bottom: 0.75rem; display: block;">Title &amp; Content (multi-language)</label>
                @include('components.language-tabs', ['active' => 'en'])
            </div>

            <div class="edit-page-section">
                <h3 class="edit-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Basic Information
                </h3>
                <div class="form-grid form-grid-3">
                    @foreach($createLocales as $code => $info)
                        <div class="lang-panel {{ $loop->first ? 'active' : '' }}" id="lang-panel-create-title-{{ $code }}" style="{{ $loop->first ? '' : 'display:none;' }}" role="tabpanel">
                            <div class="form-group">
                                <label for="translations_{{ $code }}_title" class="form-label">Title ({{ $info['name'] }}) <span class="form-required">*</span></label>
                                <input type="text" name="translations[{{ $code }}][title]" id="translations_{{ $code }}_title"
                                    value="{{ old("translations.{$code}.title") }}"
                                    class="form-input @error('title') error @enderror" {{ $code === 'en' ? 'required' : '' }} placeholder="e.g. Our Mission">
                                @error('title')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label for="slug" class="form-label">Slug <span class="form-required">*</span></label>
                            <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required
                            class="form-input @error('slug') error @enderror" placeholder="e.g. mission">
                        @error('slug')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="route_name" class="form-label">Route Name</label>
                        <input type="text" name="route_name" id="route_name" value="{{ old('route_name') }}"
                            class="form-input @error('route_name') error @enderror" placeholder="e.g. frontend.page or frontend.contact">
                        <small class="form-hint">Optional. Use a named frontend route or leave blank to use the page slug.</small>
                        @error('route_name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="menu_group" class="form-label">Menu Placement</label>
                        <select name="menu_group" id="menu_group" class="form-input @error('menu_group') error @enderror">
                            @foreach (\App\Models\Page::MENU_GROUP_LABELS as $value => $label)
                                <option value="{{ $value }}" {{ old('menu_group', 'more') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-hint">Main page = top navbar. Dropdown pages appear inside the menu groups.</small>
                        @error('menu_group')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="sort_order" class="form-label">Sort Order <span class="form-required">*</span></label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $nextSortOrder ?? 1) }}" min="1"
                            class="form-input @error('sort_order') error @enderror" placeholder="Display order (1, 2, 3...)">
                        <small class="form-hint">Must be unique. Starts from 1.</small>
                        @error('sort_order')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            Active
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sitemap</label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="sitemap_include" value="1" {{ old('sitemap_include', true) ? 'checked' : '' }}>
                            Include in sitemap
                        </label>
                    </div>
                </div>
            </div>

            <div class="edit-page-section">
                <h3 class="edit-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    Page Content
                </h3>
                @foreach($createLocales as $code => $info)
                    <div class="lang-panel {{ $loop->first ? 'active' : '' }}" id="lang-panel-create-{{ $code }}" style="{{ $loop->first ? '' : 'display:none;' }}" role="tabpanel">
                        <div class="form-group">
                            <label for="translations_{{ $code }}_content" class="form-label">Content ({{ $info['name'] }})</label>
                            <textarea name="translations[{{ $code }}][content]" id="translations_{{ $code }}_content" rows="6"
                                class="form-input form-textarea @error('content') error @enderror" placeholder="Page content in {{ $info['name'] }}">{{ old("translations.{$code}.content") }}</textarea>
                            @error('content')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="edit-page-section">
                <h3 class="edit-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    SEO Settings
                </h3>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}"
                            class="form-input @error('meta_title') error @enderror" maxlength="60" placeholder="Recommended 50–60 chars">
                        @error('meta_title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" rows="2"
                            class="form-input form-textarea @error('meta_description') error @enderror" maxlength="160" placeholder="Recommended 150–160 characters">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-group-span-2">
                        <label for="canonical_url" class="form-label">Canonical URL</label>
                        <input type="url" name="canonical_url" id="canonical_url" value="{{ old('canonical_url') }}"
                            class="form-input @error('canonical_url') error @enderror" placeholder="https://example.com/page">
                        @error('canonical_url')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="edit-page-section">
                <h3 class="edit-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    Open Graph (OG) Tags
                </h3>
                <div class="form-grid form-grid-2">
                    <div class="form-group">
                        <label for="og_title" class="form-label">OG Title</label>
                        <input type="text" name="og_title" id="og_title" value="{{ old('og_title') }}"
                            class="form-input @error('og_title') error @enderror" placeholder="Defaults to meta title">
                        @error('og_title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="og_description" class="form-label">OG Description</label>
                        <input type="text" name="og_description" id="og_description" value="{{ old('og_description') }}"
                            class="form-input @error('og_description') error @enderror" placeholder="Defaults to meta description">
                        @error('og_description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="og_image" class="form-label">OG Image URL</label>
                        <input type="url" name="og_image" id="og_image" value="{{ old('og_image') }}"
                            class="form-input @error('og_image') error @enderror" placeholder="https://example.com/image.jpg">
                        @error('og_image')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="og_type" class="form-label">OG Type</label>
                        <select name="og_type" id="og_type" class="form-input @error('og_type') error @enderror">
                            <option value="website" {{ old('og_type', 'website') == 'website' ? 'selected' : '' }}>website</option>
                            <option value="article" {{ old('og_type') == 'article' ? 'selected' : '' }}>article</option>
                        </select>
                        @error('og_type')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="edit-page-section">
                <h3 class="edit-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                    </svg>
                    Structured Data (JSON-LD)
                </h3>
                <div class="form-group">
                    <textarea name="structured_data" id="structured_data" rows="8"
                        class="form-input form-textarea font-mono @error('structured_data') error @enderror" placeholder='{"@@context":"https://schema.org","@@type":"WebPage","name":"..."}'>{{ old('structured_data') }}</textarea>
                    <small class="form-hint">Valid JSON-LD for rich snippets in search results</small>
                    @error('structured_data')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Create
                </button>
                <a href="{{ route('system-management.pages.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection
