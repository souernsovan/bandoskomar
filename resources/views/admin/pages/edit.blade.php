@extends('layouts.admin')

@section('styles')
    @include('admin.pages._editor-styles')
@endsection

@php
    $content = $page->content ?? [];
    $liveRoutes = [
        'home' => route('home'),
        'about-us' => route('about'),
        'history' => route('history'),
        'our-program' => route('programs'),
        'annual-report' => route('resources.annual-report'),
        'publication' => route('resources.publication'),
        'photo-gallery' => route('resources.photo-gallery'),
        'video-center' => route('resources.video-center'),
        'support-us' => route('get-involved.support-us'),
        'sponsor-child' => route('get-involved.sponsor-child'),
        'ways-to-give' => route('get-involved.ways-to-give'),
        'career' => route('get-involved.career'),
        'donate' => route('donate'),
        'contact' => route('contact'),
    ];
@endphp

@section('admin_content')
<div class="page-editor-header">
    <div>
        <h1 class="page-editor-title">Edit {{ $page->title }}</h1>
        <p class="page-editor-subtitle">Update the public page content. Fields are grouped by the part of the page visitors see.</p>
    </div>
    <a href="{{ route('admin.pages.index') }}" class="page-editor-back">
        <i data-lucide="arrow-left"></i> Back to Pages
    </a>
</div>

@if(session('success'))
    <div class="editor-alert success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="editor-alert error">Please check the highlighted fields and try again.</div>
@endif

<form action="{{ route('admin.pages.update', $page) }}" method="POST" class="page-editor-layout">
    @csrf
    @method('PUT')

    <div class="editor-stack">
        <section class="editor-panel">
            <h2 class="editor-section-title"><i data-lucide="file-text"></i> Basic Page Text</h2>
            <div class="editor-grid">
                <div class="editor-field full">
                    <label class="editor-label" for="title">Page name</label>
                    <input class="editor-input" id="title" type="text" name="title" value="{{ old('title', $page->title) }}" required>
                    @error('title') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <div class="editor-field">
                    <label class="editor-label" for="header_title">Page headline</label>
                    <input class="editor-input" id="header_title" type="text" name="content[header][title]" value="{{ old('content.header.title', data_get($content, 'header.title')) }}" placeholder="Headline visitors see">
                </div>

                <div class="editor-field">
                    <label class="editor-label" for="header_badge">Small label</label>
                    <input class="editor-input" id="header_badge" type="text" name="content[header][badge]" value="{{ old('content.header.badge', data_get($content, 'header.badge')) }}" placeholder="Optional">
                </div>

                <div class="editor-field full">
                    <label class="editor-label" for="header_description">Short introduction</label>
                    <textarea class="editor-textarea" id="header_description" name="content[header][description]" rows="3" placeholder="A short summary shown near the top of the page.">{{ old('content.header.description', data_get($content, 'header.description')) }}</textarea>
                </div>
            </div>
        </section>

        @if($page->slug === 'home')
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="image"></i> Home Hero</h2>
                <div class="editor-grid">
                    <div class="editor-field">
                        <label class="editor-label">Small label above headline</label>
                        <input class="editor-input" type="text" name="content[hero][badge]" value="{{ old('content.hero.badge', data_get($content, 'hero.badge')) }}">
                    </div>
                    <div class="editor-field">
                        <label class="editor-label">Photo URL</label>
                        <input class="editor-input" type="text" name="content[hero][image]" value="{{ old('content.hero.image', data_get($content, 'hero.image')) }}">
                    </div>
                    <div class="editor-field">
                        <label class="editor-label">Big headline</label>
                        <input class="editor-input" type="text" name="content[hero][title]" value="{{ old('content.hero.title', data_get($content, 'hero.title')) }}">
                    </div>
                    <div class="editor-field">
                        <label class="editor-label">Colored headline line</label>
                        <input class="editor-input" type="text" name="content[hero][subtitle]" value="{{ old('content.hero.subtitle', data_get($content, 'hero.subtitle')) }}">
                    </div>
                    <div class="editor-field full">
                        <label class="editor-label">Hero description</label>
                        <textarea class="editor-textarea" name="content[hero][description]" rows="3">{{ old('content.hero.description', data_get($content, 'hero.description')) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="bar-chart-2"></i> Impact Numbers</h2>
                <div class="repeat-list">
                    @for($i = 0; $i < 4; $i++)
                        <div class="repeat-item">
                            <div class="repeat-item-title">Number {{ $i + 1 }}</div>
                            <div class="editor-grid">
                                <div class="editor-field">
                                    <label class="editor-label">Value</label>
                                    <input class="editor-input" type="text" name="content[stats][items][{{ $i }}][value]" value="{{ old("content.stats.items.$i.value", data_get($content, "stats.items.$i.value")) }}">
                                </div>
                                <div class="editor-field">
                                    <label class="editor-label">Label</label>
                                    <input class="editor-input" type="text" name="content[stats][items][{{ $i }}][label]" value="{{ old("content.stats.items.$i.label", data_get($content, "stats.items.$i.label")) }}">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </section>

            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="grid-2x2"></i> Program Cards</h2>
                <div class="repeat-list">
                    @for($i = 0; $i < 4; $i++)
                        <div class="repeat-item">
                            <div class="repeat-item-title">Program {{ $i + 1 }}</div>
                            <div class="editor-grid">
                                <div class="editor-field">
                                    <label class="editor-label">Program name</label>
                                    <input class="editor-input" type="text" name="content[programs][{{ $i }}][title]" value="{{ old("content.programs.$i.title", data_get($content, "programs.$i.title")) }}">
                                </div>
                                <div class="editor-field">
                                    <label class="editor-label">Icon name</label>
                                    <input class="editor-input" type="text" name="content[programs][{{ $i }}][icon]" value="{{ old("content.programs.$i.icon", data_get($content, "programs.$i.icon")) }}">
                                </div>
                                <div class="editor-field full">
                                    <label class="editor-label">Description</label>
                                    <textarea class="editor-textarea" name="content[programs][{{ $i }}][description]" rows="2">{{ old("content.programs.$i.description", data_get($content, "programs.$i.description")) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </section>
        @elseif($page->slug === 'about-us')
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="heart"></i> Mission Section</h2>
                <div class="editor-grid">
                    <div class="editor-field">
                        <label class="editor-label">Mission headline</label>
                        <input class="editor-input" type="text" name="content[mission][title]" value="{{ old('content.mission.title', data_get($content, 'mission.title')) }}">
                    </div>
                    <div class="editor-field">
                        <label class="editor-label">Photo URL</label>
                        <input class="editor-input" type="text" name="content[mission][image]" value="{{ old('content.mission.image', data_get($content, 'mission.image')) }}">
                    </div>
                    <div class="editor-field full">
                        <label class="editor-label">Mission text</label>
                        <textarea class="editor-textarea" name="content[mission][description]" rows="5">{{ old('content.mission.description', data_get($content, 'mission.description')) }}</textarea>
                    </div>
                    <div class="editor-field full">
                        <label class="editor-label">Quote</label>
                        <input class="editor-input" type="text" name="content[mission][quote]" value="{{ old('content.mission.quote', data_get($content, 'mission.quote')) }}">
                    </div>
                </div>
            </section>
        @elseif($page->slug === 'history')
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="history"></i> Timeline</h2>
                <div class="repeat-list">
                    @for($i = 0; $i < 5; $i++)
                        <div class="repeat-item">
                            <div class="repeat-item-title">Timeline item {{ $i + 1 }}</div>
                            <div class="editor-grid">
                                <div class="editor-field">
                                    <label class="editor-label">Year</label>
                                    <input class="editor-input" type="text" name="content[timeline][{{ $i }}][year]" value="{{ old("content.timeline.$i.year", data_get($content, "timeline.$i.year")) }}">
                                </div>
                                <div class="editor-field">
                                    <label class="editor-label">Event title</label>
                                    <input class="editor-input" type="text" name="content[timeline][{{ $i }}][title]" value="{{ old("content.timeline.$i.title", data_get($content, "timeline.$i.title")) }}">
                                </div>
                                <div class="editor-field full">
                                    <label class="editor-label">Event story</label>
                                    <textarea class="editor-textarea" name="content[timeline][{{ $i }}][description]" rows="2">{{ old("content.timeline.$i.description", data_get($content, "timeline.$i.description")) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </section>
        @elseif($page->slug === 'annual-report')
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="files"></i> Reports</h2>
                <div class="repeat-list">
                    @for($i = 0; $i < 5; $i++)
                        <div class="repeat-item">
                            <div class="repeat-item-title">Report {{ $i + 1 }}</div>
                            <div class="editor-grid">
                                <div class="editor-field">
                                    <label class="editor-label">Report title</label>
                                    <input class="editor-input" type="text" name="content[reports][{{ $i }}][title]" value="{{ old("content.reports.$i.title", data_get($content, "reports.$i.title")) }}">
                                </div>
                                <div class="editor-field">
                                    <label class="editor-label">Year</label>
                                    <input class="editor-input" type="text" name="content[reports][{{ $i }}][year]" value="{{ old("content.reports.$i.year", data_get($content, "reports.$i.year")) }}">
                                </div>
                                <div class="editor-field full">
                                    <label class="editor-label">Report file link</label>
                                    <input class="editor-input" type="text" name="content[reports][{{ $i }}][link]" value="{{ old("content.reports.$i.link", data_get($content, "reports.$i.link")) }}">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </section>
        @elseif($page->slug === 'publication')
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="book-open"></i> Publications</h2>
                <div class="repeat-list">
                    @for($i = 0; $i < 5; $i++)
                        <div class="repeat-item">
                            <div class="repeat-item-title">Publication {{ $i + 1 }}</div>
                            <div class="editor-grid">
                                <div class="editor-field">
                                    <label class="editor-label">Publication title</label>
                                    <input class="editor-input" type="text" name="content[items][{{ $i }}][title]" value="{{ old("content.items.$i.title", data_get($content, "items.$i.title")) }}">
                                </div>
                                <div class="editor-field">
                                    <label class="editor-label">File or page link</label>
                                    <input class="editor-input" type="text" name="content[items][{{ $i }}][link]" value="{{ old("content.items.$i.link", data_get($content, "items.$i.link")) }}">
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </section>
        @elseif($page->slug === 'contact')
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="mail"></i> Contact Details</h2>
                <div class="editor-grid">
                    <div class="editor-field full">
                        <label class="editor-label">Address</label>
                        <input class="editor-input" type="text" name="content[info][address]" value="{{ old('content.info.address', data_get($content, 'info.address')) }}">
                    </div>
                    <div class="editor-field">
                        <label class="editor-label">Email</label>
                        <input class="editor-input" type="email" name="content[info][email]" value="{{ old('content.info.email', data_get($content, 'info.email')) }}">
                    </div>
                    <div class="editor-field">
                        <label class="editor-label">Phone</label>
                        <input class="editor-input" type="text" name="content[info][phone]" value="{{ old('content.info.phone', data_get($content, 'info.phone')) }}">
                    </div>
                    <div class="editor-field full">
                        <label class="editor-label">Map link</label>
                        <input class="editor-input" type="text" name="content[info][map_embed]" value="{{ old('content.info.map_embed', data_get($content, 'info.map_embed')) }}">
                    </div>
                </div>
            </section>
        @else
            <section class="editor-panel">
                <h2 class="editor-section-title"><i data-lucide="align-left"></i> Main Content</h2>
                <div class="editor-field">
                    <label class="editor-label">Page body</label>
                    <textarea class="editor-textarea tall" name="content[body]" rows="12" placeholder="Write the main content here.">{{ old('content.body', data_get($content, 'body')) }}</textarea>
                    <span class="editor-help">Use this for simple text pages. Special layout sections stay saved in the background.</span>
                </div>
            </section>
        @endif

        <section class="editor-panel">
            <h2 class="editor-section-title"><i data-lucide="search"></i> Search Details</h2>
            <div class="editor-grid">
                <div class="editor-field full">
                    <label class="editor-label" for="meta_description">Short search description</label>
                    <textarea class="editor-textarea" id="meta_description" name="meta_description" rows="3" maxlength="255">{{ old('meta_description', $page->meta_description) }}</textarea>
                    @error('meta_description') <span class="editor-error">{{ $message }}</span> @enderror
                </div>
                <div class="editor-field full">
                    <label class="editor-label" for="meta_keywords">Search words</label>
                    <input class="editor-input" id="meta_keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}" placeholder="education, children, cambodia">
                    @error('meta_keywords') <span class="editor-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="editor-panel sticky">
            <h2 class="editor-section-title"><i data-lucide="settings"></i> Publish</h2>

            <div class="editor-stack">
                <div class="editor-field">
                    <label class="editor-label" for="status">Visibility</label>
                    <select class="editor-select" id="status" name="status" required>
                        <option value="published" {{ old('status', $page->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    <span class="editor-help">Choose Draft when changes are not ready for visitors.</span>
                    @error('status') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <div class="editor-field">
                    <label class="editor-label" for="page_category">Menu group</label>
                    <select class="editor-select" id="page_category" name="page_category" required>
                        <option value="main" {{ old('page_category', $page->page_category) === 'main' ? 'selected' : '' }}>Main Pages</option>
                        <option value="resources" {{ old('page_category', $page->page_category) === 'resources' ? 'selected' : '' }}>Info & Resources</option>
                        <option value="get-involved" {{ old('page_category', $page->page_category) === 'get-involved' ? 'selected' : '' }}>Get Involved</option>
                        <option value="donation" {{ old('page_category', $page->page_category) === 'donation' ? 'selected' : '' }}>Donation</option>
                        <option value="contact" {{ old('page_category', $page->page_category) === 'contact' ? 'selected' : '' }}>Contact</option>
                    </select>
                    @error('page_category') <span class="editor-error">{{ $message }}</span> @enderror
                </div>

                <details class="advanced-box">
                    <summary>Advanced options</summary>
                    <div class="editor-stack" style="margin-top: 1rem;">
                        <div class="editor-field">
                            <label class="editor-label" for="slug">Page address</label>
                            <input class="editor-input" id="slug" type="text" name="slug" value="{{ old('slug', $page->slug) }}" required>
                            <span class="editor-help">Changing this can change the page link.</span>
                            @error('slug') <span class="editor-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="editor-field">
                            <label class="editor-label" for="icon">Menu icon</label>
                            <input class="editor-input" id="icon" type="text" name="icon" value="{{ old('icon', $page->icon) }}">
                            @error('icon') <span class="editor-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </details>

                <div class="editor-help">
                    Last updated {{ $page->updated_at->format('M d, Y') }}.
                </div>

                <div class="editor-actions">
                    <a href="{{ $liveRoutes[$page->slug] ?? route('pages.custom', $page) }}" target="_blank" class="editor-secondary-btn">
                        <i data-lucide="external-link"></i> View Live Page
                    </a>
                    <button type="submit" class="editor-primary-btn">
                        <i data-lucide="save"></i> Save Changes
                    </button>
                </div>
            </div>
        </section>
    </div>

</form>
@endsection
