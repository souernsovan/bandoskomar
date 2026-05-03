@extends('admin.layouts.app')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Page</h2>
            <p>{{ $page->title }}</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('system-management.pages.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        @php
            $stagedUploadPageTypes = ['home', 'platform', 'product'];
            $useStagedMultiUpload = in_array($pageType ?? $page->slug ?? '', $stagedUploadPageTypes, true);
        @endphp
        <form action="{{ route('system-management.pages.update', $page) }}" method="POST"
            @if ($useStagedMultiUpload) data-staged-upload-url="{{ route('system-management.pages.staged-media', $page, false) }}" @endif
            @if (in_array($pageType ?? $page->slug ?? '', ['home', 'platform', 'about-us', 'product'])) enctype="multipart/form-data" @endif>
            @csrf
            @method('PUT')

            <div class="edit-form-body">
                <div class="edit-page-section mb-0 pb-0">
                    <label class="form-label" style="margin-bottom: 0.75rem; display: block;">Title &amp; Content
                        (multi-language)</label>
                </div>
                <div class="edit-page-lang-tabs-sticky">
                    @include('components.language-tabs', ['active' => 'en', 'sticky' => true])
                </div>

                <div class="edit-page-section">
                    <h3 class="edit-section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Basic Information
                    </h3>
                    <div class="form-grid form-grid-3">
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}" id="lang-panel-{{ $code }}"
                                role="tabpanel">
                                <div class="form-group">
                                    <label for="translations_{{ $code }}_title" class="form-label">Title
                                        ({{ $info['name'] }})
                                        <span class="form-required">*</span></label>
                                    <input type="text" name="translations[{{ $code }}][title]"
                                        id="translations_{{ $code }}_title"
                                        value="{{ old("translations.{$code}.title", $translations[$code]['title'] ?? '') }}"
                                        class="form-input @error('title') error @enderror"
                                        {{ $code === 'en' ? 'required' : '' }}>
                                    @error('title')
                                        <span class="form-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                        <div class="form-group">
                            <label for="slug" class="form-label">Slug <span class="form-required">*</span></label>
                            @php $slugLocked = in_array($pageType ?? $page->slug ?? '', ['home', 'platform', 'about-us', 'product']); @endphp
                            @if ($slugLocked)
                                <input type="hidden" name="slug" value="{{ old('slug', $page->slug) }}">
                                <input type="text" id="slug" value="{{ old('slug', $page->slug) }}"
                                    disabled class="form-input" readonly>
                                <small class="form-hint">Slug is fixed for this page type.</small>
                            @else
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}"
                                    required class="form-input @error('slug') error @enderror">
                            @endif
                            @error('slug')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="route_name" class="form-label">Route Name</label>
                            <input type="text" name="route_name" id="route_name"
                                value="{{ old('route_name', $page->route_name) }}"
                                class="form-input @error('route_name') error @enderror"
                                placeholder="e.g. frontend.page or frontend.contact">
                            <small class="form-hint">Optional. Use a named frontend route or leave blank to use the page slug.</small>
                            @error('route_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="menu_group" class="form-label">Menu Placement</label>
                            <select name="menu_group" id="menu_group" class="form-input @error('menu_group') error @enderror">
                                @foreach (\App\Models\Page::MENU_GROUP_LABELS as $value => $label)
                                    <option value="{{ $value }}" {{ old('menu_group', $page->menu_group ?? $page->getMenuGroup()) === $value ? 'selected' : '' }}>
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
                            <label for="sort_order" class="form-label">Sort Order <span
                                    class="form-required">*</span></label>
                            <input type="number" name="sort_order" id="sort_order"
                                value="{{ old('sort_order', $page->sort_order) }}" min="1"
                                class="form-input @error('sort_order') error @enderror"
                                placeholder="Display order (1, 2, 3...)">
                            <small class="form-hint">Must be unique. If taken, choose another number.</small>
                            @error('sort_order')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                                Active
                            </label>
                            <small class="form-hint">Uncheck to hide the page from the public website.</small>
                        </div>
                    </div>
                    {{-- SEO Settings --}}
                    <div class="edit-page-section">
                        <h3 class="edit-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            SEO Settings
                        </h3>
                        <div class="form-grid form-grid-2">
                            <div class="form-group">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" id="meta_title"
                                    value="{{ old('meta_title', $page->meta_title) }}"
                                    class="form-input @error('meta_title') error @enderror" maxlength="60"
                                    placeholder="Recommended 50–60 chars">
                                @error('meta_title')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="canonical_url" class="form-label">Canonical URL</label>
                                <input type="url" name="canonical_url" id="canonical_url"
                                    value="{{ old('canonical_url', $page->canonical_url) }}"
                                    class="form-input @error('canonical_url') error @enderror"
                                    placeholder="https://example.com/page">
                                @error('canonical_url')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group full-width">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="2"
                                    class="form-input form-textarea @error('meta_description') error @enderror" maxlength="160"
                                    placeholder="Recommended 150–160 characters">{{ old('meta_description', $page->meta_description) }}</textarea>
                                @error('meta_description')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            
                        </div>
                    </div>

                    {{-- Open Graph (OG) Tags --}}
                    <div class="edit-page-section">
                        <h3 class="edit-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Open Graph (OG) Tags
                        </h3>
                        @php $ogTags = $page->og_tags ?? []; @endphp
                        <div class="form-grid form-grid-2">
                            <div class="form-group">
                                <label for="og_title" class="form-label">OG Title</label>
                                <input type="text" name="og_title" id="og_title"
                                    value="{{ old('og_title', $ogTags['og_title'] ?? '') }}"
                                    class="form-input @error('og_title') error @enderror"
                                    placeholder="Defaults to meta title">
                                @error('og_title')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="og_description" class="form-label">OG Description</label>
                                <input type="text" name="og_description" id="og_description"
                                    value="{{ old('og_description', $ogTags['og_description'] ?? '') }}"
                                    class="form-input @error('og_description') error @enderror"
                                    placeholder="Defaults to meta description">
                                @error('og_description')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="og_image" class="form-label">OG Image URL</label>
                                <input type="url" name="og_image" id="og_image"
                                    value="{{ old('og_image', $ogTags['og_image'] ?? '') }}"
                                    class="form-input @error('og_image') error @enderror"
                                    placeholder="https://example.com/image.jpg">
                                @error('og_image')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="og_type" class="form-label">OG Type</label>
                                <select name="og_type" id="og_type"
                                    class="form-input @error('og_type') error @enderror">
                                    <option value="website"
                                        {{ old('og_type', $ogTags['og_type'] ?? 'website') == 'website' ? 'selected' : '' }}>
                                        website</option>
                                    <option value="article"
                                        {{ old('og_type', $ogTags['og_type'] ?? '') == 'article' ? 'selected' : '' }}>
                                        article</option>
                                </select>
                                @error('og_type')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="edit-page-section">
                        <h3 class="edit-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                            </svg>
                            Structured Data (JSON-LD)
                        </h3>
                        <div class="form-group">
                            <textarea name="structured_data" id="structured_data" rows="8"
                                class="form-input form-textarea font-mono @error('structured_data') error @enderror"
                                placeholder='{"@@context":"https://schema.org","@@type":"WebPage","name":"..."}'>{{ old('structured_data', $page->structured_data ? json_encode($page->structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                            <small class="form-hint">Valid JSON-LD for rich snippets in search results</small>
                            @error('structured_data')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="edit-page-section">
                        <div class="form-group">
                            <label class="form-label">Sitemap</label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="sitemap_include" value="1"
                                    {{ old('sitemap_include', $page->sitemap_include) ? 'checked' : '' }}>
                                Include in sitemap
                            </label>
                        </div>
                    </div>
                </div>

                @if (!in_array($pageType ?? $page->slug ?? '', ['home', 'platform', 'about-us', 'product']))
                    <div class="edit-page-section">
                        <h3 class="edit-section-title">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Page Content (JSON)
                        </h3>
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            @php $pcLocale = $pageContentByLocale[$code] ?? []; @endphp
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}"
                                id="lang-panel-content-{{ $code }}" role="tabpanel">
                                <div class="form-grid mt-2">
                                    <div class="form-group full-width">
                                        <label for="page_content_{{ $code }}" class="form-label">Page Content -
                                            {{ $info['name'] }} (JSON)</label>
                                        <textarea name="page_content_locale[{{ $code }}]" id="page_content_{{ $code }}" rows="10"
                                            class="form-input form-textarea font-mono @error('page_content') error @enderror"
                                            placeholder='{"key": "value", ...}'>{{ old("page_content_locale.{$code}", json_encode($pcLocale, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                                        <small class="form-hint">Structured content as JSON for
                                            {{ $info['name'] }}.</small>
                                        @error('page_content')
                                            <span class="form-error">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (($pageType ?? $page->slug) === 'home')
                    <div class="edit-page-section">
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}"
                                id="lang-panel-home-{{ $code }}" role="tabpanel">
                                @include('admin.system-management.pages.partials.homepage-sections', [
                                    'locale' => $code,
                                    'localeName' => $info['name'] ?? $code,
                                    'localeData' => $pageContentByLocale[$code] ?? [],
                                ])
                            </div>
                        @endforeach
                        @include('admin.system-management.pages.partials.homepage-styles', [
                            'homepage_styles' => $homepage_styles ?? [],
                        ])
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            @php
                                $pLocaleData = $pageContentByLocale[$code] ?? [];
                                $pN = function ($key) use ($code) {
                                    return "homepage_sections[{$code}][{$key}]";
                                };
                                $pV = function ($key, $default = '') use ($pLocaleData) {
                                    return $pLocaleData[$key] ?? $default;
                                };
                            @endphp
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}"
                                id="lang-panel-partners-{{ $code }}" role="tabpanel">
                                <div class="homepage-section-card">
                                    <h4 class="homepage-section-heading"><span class="homepage-section-num">7</span> Our
                                        Supporters ({{ $info['name'] ?? $code }})</h4>
                                    <div class="form-grid">
                                        <div class="form-group full-width">
                                            <label class="form-label">Supporters Title</label>
                                            <input type="text" name="{{ $pN('partners_title') }}" class="form-input"
                                                value="{{ old($pN('partners_title'), $pV('partners_title', 'Our supporters')) }}">
                                        </div>
                                        <div class="form-group full-width">
                                            @include(
                                                'admin.system-management.pages.partials.homepage-image-upload',
                                                [
                                                    'name' => 'homepage_partner_image_files[]',
                                                    'label' => 'Add Supporter Logos',
                                                    'multiple' => true,
                                                    'keepImagesName' => 'homepage_keep_partner_images',
                                                    'stagedPurpose' => 'homepage_partner',
                                                    'currentImages' => $pV(
                                                        'partner_images',
                                                        $homepage_partner_images ?? []),
                                                    'hint' => 'Upload or remove. Changes apply to all languages.',
                                                ]
                                            )
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif (($pageType ?? $page->slug) === 'platform')
                    <div class="edit-page-section">
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}"
                                id="lang-panel-platform-{{ $code }}" role="tabpanel">
                                @include('admin.system-management.pages.partials.platform-sections', [
                                    'locale' => $code,
                                    'localeName' => $info['name'] ?? $code,
                                    'localeData' => $pageContentByLocale[$code] ?? [],
                                ])
                            </div>
                        @endforeach
                    </div>
                @elseif (($pageType ?? $page->slug) === 'about-us')
                    <div class="edit-page-section">
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}"
                                id="lang-panel-about-{{ $code }}" role="tabpanel">
                                @include('admin.system-management.pages.partials.about-us-sections', [
                                    'locale' => $code,
                                    'localeName' => $info['name'] ?? $code,
                                    'localeData' => $pageContentByLocale[$code] ?? [],
                                ])
                            </div>
                        @endforeach
                    </div>
                @elseif (($pageType ?? $page->slug) === 'product')
                    <div class="edit-page-section">
                        @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                            <div class="lang-panel {{ $loop->first ? 'active' : '' }}"
                                id="lang-panel-product-{{ $code }}" role="tabpanel">
                                @include('admin.system-management.pages.partials.product-sections', [
                                    'locale' => $code,
                                    'localeName' => $info['name'] ?? $code,
                                    'localeData' => $pageContentByLocale[$code] ?? [],
                                    'product_partner_images' => $product_partner_images ?? [],
                                ])
                            </div>
                        @endforeach
                    </div>
                @endif



                <div class="form-actions">
                    <button type="submit" class="btn btn-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        Update
                    </button>
                    <a href="{{ route('system-management.pages.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>{{-- /edit-form-body --}}

        </form>
    </div>
@endsection
