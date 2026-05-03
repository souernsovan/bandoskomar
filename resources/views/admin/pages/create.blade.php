@extends('layouts.admin')

@section('styles')
    @include('admin.pages._editor-styles')
@endsection

@section('admin_content')
<div class="page-editor-header">
    <div>
        <h1 class="page-editor-title">Create a Page</h1>
        <p class="page-editor-subtitle">Add the words visitors will see. The page address can be created automatically from the title.</p>
    </div>
    <a href="{{ route('admin.pages.index') }}" class="page-editor-back">
        <i data-lucide="arrow-left"></i> Back to Pages
    </a>
</div>

@if($errors->any())
    <div class="editor-alert error">Please check the highlighted fields and try again.</div>
@endif

<form action="{{ route('admin.pages.store') }}" method="POST" class="page-editor-layout">
    @csrf

    <div class="editor-stack">
        <section class="editor-panel">
            <h2 class="editor-section-title"><i data-lucide="file-text"></i> Page Content</h2>

            <div class="editor-grid">
                <div class="editor-field full">
                    <label class="editor-label" for="title">Page name</label>
                    <input class="editor-input" id="title" type="text" name="title" value="{{ old('title') }}" placeholder="Example: Our Partners" required>
                    <span class="editor-help">This appears as the page title in the admin area and can also be shown on the website.</span>
                    @error('title') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <div class="editor-field full">
                    <label class="editor-label" for="meta_description">Short introduction</label>
                    <textarea class="editor-textarea" id="meta_description" name="meta_description" rows="3" maxlength="255" placeholder="Write one or two sentences that explain this page.">{{ old('meta_description') }}</textarea>
                    <span class="editor-help">Visitors and search engines use this as a short summary.</span>
                    @error('meta_description') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <div class="editor-field full">
                    <label class="editor-label" for="body">Main page text</label>
                    <textarea class="editor-textarea tall" id="body" name="body" rows="12" placeholder="Write the main content here.">{{ old('body') }}</textarea>
                    <span class="editor-help">Keep paragraphs short so the page is easy to read.</span>
                    @error('body') <span class="editor-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="editor-panel">
            <h2 class="editor-section-title"><i data-lucide="search"></i> Search Details</h2>
            <div class="editor-field">
                <label class="editor-label" for="meta_keywords">Search words</label>
                <input class="editor-input" id="meta_keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="education, children, community">
                <span class="editor-help">Optional. Add a few words separated by commas.</span>
                @error('meta_keywords') <span class="editor-error">{{ $message }}</span> @enderror
            </div>
        </section>

        <section class="editor-panel sticky">
            <h2 class="editor-section-title"><i data-lucide="settings"></i> Page Setup</h2>

            <div class="editor-stack">
                <div class="editor-field">
                    <label class="editor-label" for="page_category">Menu group</label>
                    <select class="editor-select" id="page_category" name="page_category" required>
                        <option value="main" {{ old('page_category') === 'main' ? 'selected' : '' }}>Main Pages</option>
                        <option value="resources" {{ old('page_category') === 'resources' ? 'selected' : '' }}>Info & Resources</option>
                        <option value="get-involved" {{ old('page_category') === 'get-involved' ? 'selected' : '' }}>Get Involved</option>
                        <option value="donation" {{ old('page_category') === 'donation' ? 'selected' : '' }}>Donation</option>
                        <option value="contact" {{ old('page_category') === 'contact' ? 'selected' : '' }}>Contact</option>
                    </select>
                    <span class="editor-help">Choose where this page belongs in the site.</span>
                    @error('page_category') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <div class="editor-field">
                    <label class="editor-label" for="status">Visibility</label>
                    <select class="editor-select" id="status" name="status" required>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <span class="editor-help">Use Draft if the page is not ready for visitors.</span>
                    @error('status') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <details class="advanced-box">
                    <summary>Advanced options</summary>
                    <div class="editor-stack" style="margin-top: 1rem;">
                        <div class="editor-field">
                            <label class="editor-label" for="slug">Page address</label>
                            <input class="editor-input" id="slug" type="text" name="slug" value="{{ old('slug') }}" placeholder="our-partners">
                            <span class="editor-help">Optional. Leave blank to create it from the page name.</span>
                            @error('slug') <span class="editor-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="editor-field">
                            <label class="editor-label" for="icon">Menu icon</label>
                            <input class="editor-input" id="icon" type="text" name="icon" value="{{ old('icon', 'file') }}" placeholder="file">
                            <span class="editor-help">Optional icon name used inside the admin menu.</span>
                            @error('icon') <span class="editor-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </details>

                <div class="editor-actions">
                    <button type="submit" class="editor-primary-btn">
                        <i data-lucide="plus"></i> Create Page
                    </button>
                </div>
            </div>
        </section>
    </div>

</form>
@endsection
