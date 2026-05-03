<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
    </svg>
    About Us Page Sections ({{ $localeName }})
</h3>

@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $n = function ($key) use ($locale) {
        return "about_us_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };
    $solIcons = $about_us_solution_icons ?? config('about_us_icons.solution_icons', []);
    $intIcons = $about_us_interest_icons ?? config('about_us_icons.interest_icons', []);
    $solCards = $d['solution_cards'] ?? [];
    $intCards = $d['interest_cards'] ?? [];
    $defaultSolTitles = ['Education support', 'Health outreach', 'Emergency relief'];
@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">1</span> Impact Results ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Subtitle</label>
            <input type="text" name="{{ $n('results_subtitle') }}" class="form-input"
                value="{{ old($n('results_subtitle'), $v('results_subtitle', 'Community impact')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Title</label>
            <input type="text" name="{{ $n('results_title') }}" class="form-input"
                value="{{ old($n('results_title'), $v('results_title', 'How do we deliver meaningful results?')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Description</label>
            <textarea name="{{ $n('results_description') }}" rows="4" class="form-input form-textarea">{{ old($n('results_description'), $v('results_description', 'We build practical, transparent programs that focus on long-term support for people and communities.')) }}</textarea>
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">2</span> Why We Are Different ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Subtitle</label>
            <input type="text" name="{{ $n('different_subtitle') }}" class="form-input"
                value="{{ old($n('different_subtitle'), $v('different_subtitle', 'Why we are different')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Title</label>
            <input type="text" name="{{ $n('different_title') }}" class="form-input"
                value="{{ old($n('different_title'), $v('different_title', 'We work with people, not for them.')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Description</label>
            <textarea name="{{ $n('different_description') }}" rows="3" class="form-input form-textarea">{{ old($n('different_description'), $v('different_description', 'Our approach is collaborative, local, and rooted in dignity. We listen first and act with care.')) }}</textarea>
        </div>
        <div class="form-group full-width">
            <label class="form-label">Checkmark Text</label>
            <input type="text" name="{{ $n('different_check') }}" class="form-input"
                value="{{ old($n('different_check'), $v('different_check', 'Community-led support')) }}">
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "about_us_sections[{$locale}][different_image_file]",
                'pathName' => $n('different_image'),
                'label' => 'Image',
                'pathValue' => $v('different_image', ''),
                'currentImageUrl' => $v('different_image'),
                'uploadPath' => 'images/about-us',
                'sharedImageKey' => 'about_different_image',
            ])
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">3</span> Our Promise ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Subtitle</label>
            <input type="text" name="{{ $n('promise_subtitle') }}" class="form-input"
                value="{{ old($n('promise_subtitle'), $v('promise_subtitle', 'Our Promise')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Title</label>
            <input type="text" name="{{ $n('promise_title') }}" class="form-input"
                value="{{ old($n('promise_title'), $v('promise_title', 'We stay accountable to every family and donor.')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Description</label>
            <textarea name="{{ $n('promise_description') }}" rows="4" class="form-input form-textarea">{{ old($n('promise_description'), $v('promise_description', 'We keep our work simple, transparent, and focused on the real needs that matter most.')) }}</textarea>
        </div>
        <div class="form-group full-width">
            <label class="form-label">Checkmark Text</label>
            <input type="text" name="{{ $n('promise_check') }}" class="form-input"
                value="{{ old($n('promise_check'), $v('promise_check', 'Transparent reporting')) }}">
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "about_us_sections[{$locale}][promise_image_file]",
                'pathName' => $n('promise_image'),
                'label' => 'Image',
                'pathValue' => $v('promise_image', ''),
                'currentImageUrl' => $v('promise_image'),
                'uploadPath' => 'images/about-us',
                'sharedImageKey' => 'about_promise_image',
            ])
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">4</span> Our Approach ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Subtitle</label>
            <input type="text" name="{{ $n('solutions_subtitle') }}" class="form-input"
                value="{{ old($n('solutions_subtitle'), $v('solutions_subtitle', 'Our Solutions')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Title</label>
            <input type="text" name="{{ $n('solutions_title') }}" class="form-input"
                value="{{ old($n('solutions_title'), $v('solutions_title', 'Programs designed for lasting change')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Description</label>
            <textarea name="{{ $n('solutions_description') }}" rows="2" class="form-input form-textarea">{{ old($n('solutions_description'), $v('solutions_description', 'Each initiative is shaped to respond to community needs with practical support and local partnership.')) }}</textarea>
        </div>
    </div>
    <div class="form-grid form-grid-2 mt-4">
        @foreach ([1, 2, 3] as $i)
            @php $solCard = $solCards[$i - 1] ?? []; @endphp
            <div class="form-group">
                <label class="form-label">Approach {{ $i }} Title</label>
                <input type="text" name="{{ $n('solution_' . $i . '_title') }}" class="form-input"
                    value="{{ old($n('solution_' . $i . '_title'), $solCard['title'] ?? $defaultSolTitles[$i - 1]) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Approach {{ $i }} Icon</label>
                <select name="{{ $n('solution_' . $i . '_icon') }}" class="form-input">
                    @foreach ($solIcons as $key => $opt)
                        <option value="{{ $key }}" {{ ($solCard['icon'] ?? 'sol_' . $i) === $key ? 'selected' : '' }}>{{ $opt['label'] ?? $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Approach {{ $i }} Description</label>
                <textarea name="{{ $n('solution_' . $i . '_description') }}" rows="2" class="form-input form-textarea">{{ old($n('solution_' . $i . '_description'), $solCard['description'] ?? '') }}</textarea>
            </div>
        @endforeach
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">5</span> Where Support Matters ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Section Title</label>
            <input type="text" name="{{ $n('interests_title') }}" class="form-input"
                value="{{ old($n('interests_title'), $v('interests_title', 'Where support matters most')) }}">
        </div>
    </div>
    <div class="form-grid form-grid-2 mt-4">
        @foreach ([1, 2, 3, 4, 5, 6] as $i)
            @php $card = $intCards[$i - 1] ?? []; @endphp
            <div class="form-group">
                <label class="form-label">Need {{ $i }} Title</label>
                <input type="text" name="{{ $n('interest_' . $i . '_title') }}" class="form-input"
                    value="{{ old($n('interest_' . $i . '_title'), $card['title'] ?? '') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Need {{ $i }} Icon</label>
                <select name="{{ $n('interest_' . $i . '_icon') }}" class="form-input">
                    @foreach ($intIcons as $key => $opt)
                        <option value="{{ $key }}" {{ ($card['icon'] ?? 'int_' . $i) === $key ? 'selected' : '' }}>{{ $opt['label'] ?? $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group full-width">
                <label class="form-label">Need {{ $i }} Description</label>
                <textarea name="{{ $n('interest_' . $i . '_description') }}" rows="2" class="form-input form-textarea">{{ old($n('interest_' . $i . '_description'), $card['description'] ?? '') }}</textarea>
            </div>
        @endforeach
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">6</span> Ready Section ({{ $localeName }})</h4>
    <div class="form-group full-width">
        <label class="form-label">Title</label>
        <input type="text" name="{{ $n('ready_title') }}" class="form-input"
            value="{{ old($n('ready_title'), $v('ready_title', 'Ready to help build stronger communities?')) }}">
    </div>
</div>
