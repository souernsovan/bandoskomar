<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
    </svg>
    Mission Page Sections ({{ $localeName }})
</h3>

@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $n = function ($key) use ($locale) {
        return "platform_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };
    $platformFeatureIcons = $platform_feature_icons ?? config('platform_feature_icons.icons', []);
    $platformFeatures = $d['features'] ?? [];
    if (!is_array($platformFeatures)) {
        $platformFeatures = [];
    }
    $platformFeatures = array_values($platformFeatures);
    $defaultIconKey = array_key_first($platformFeatureIcons) ?? 'icon_1';
    if (count($platformFeatures) === 0) {
        $platformFeatures[] = ['title' => '', 'color' => 'blue', 'icon' => $defaultIconKey];
    }
    $platformSliderImages = $d['platform_slider_images'] ?? [];
    if (!is_array($platformSliderImages)) {
        $platformSliderImages = [];
    }
    if (empty($platformSliderImages) && !empty($d['platform_image'])) {
        $platformSliderImages = [$d['platform_image']];
    }
@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">1</span> Mission Overview ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Mission Title</label>
            <input type="text" name="{{ $n('profile_title') }}"
                class="form-input @error('platform_sections.*.profile_title') error @enderror"
                value="{{ old($n('profile_title'), $v('profile_title', 'Our Mission')) }}">
            @error('platform_sections.*.profile_title')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group full-width">
            <label class="form-label">Mission Tagline</label>
            <input type="text" name="{{ $n('profile_tagline') }}" class="form-input"
                value="{{ old($n('profile_tagline'), $v('profile_tagline', 'We work with communities to deliver care, education, and relief.')) }}">
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">2</span> Mission Gallery ({{ $localeName }})</h4>
    <p class="form-hint mb-3">Upload images that help tell the mission story. Remove items you no longer want to feature.</p>
    @include('admin.system-management.pages.partials.homepage-image-upload', [
        'name' => 'platform_slider_image_files[]',
        'label' => 'Add mission gallery images',
        'multiple' => true,
        'keepImagesName' => 'platform_keep_slider_images',
        'stagedPurpose' => 'platform_slider',
        'currentImages' => $platformSliderImages,
        'currentListLabel' => 'Current mission images',
        'hint' => 'JPEG, PNG, GIF, WebP • Max 10MB each. Order is preserved left-to-right.',
    ])
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">3</span> Program Pillars ({{ $localeName }})</h4>
    <p class="form-hint mb-3">Add or remove program pillars for this language. Rows with an empty title are ignored when saving.</p>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Program Title</label>
            <input type="text" name="{{ $n('features_title') }}" class="form-input"
                value="{{ old($n('features_title'), $v('features_title', 'What we do')) }}">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Program Subtitle</label>
            <textarea name="{{ $n('features_subtitle') }}" rows="2" class="form-input form-textarea">{{ old($n('features_subtitle'), $v('features_subtitle', 'We focus on education, care, and relief for communities.')) }}</textarea>
        </div>
    </div>
    <div id="platformFeaturesList_{{ $locale }}" class="platform-features-list" data-platform-features-list
        data-locale="{{ $locale }}">
        @foreach ($platformFeatures as $i => $feature)
            @php
                $title = is_array($feature) ? ($feature['title'] ?? '') : '';
                $color = is_array($feature) ? ($feature['color'] ?? 'blue') : 'blue';
                $icon = is_array($feature) ? ($feature['icon'] ?? $defaultIconKey) : $defaultIconKey;
            @endphp
            @include('admin.system-management.pages.partials.platform-feature-row', [
                'locale' => $locale,
                'index' => $i,
                'title' => $title,
                'color' => $color,
                'icon' => $icon,
                'platformFeatureIcons' => $platformFeatureIcons,
            ])
        @endforeach
    </div>
        <button type="button" class="btn btn-outline mt-3" data-add-platform-feature data-locale="{{ $locale }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            width="18" height="18" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Add pillar
    </button>
    <template id="platformFeatureRowTpl_{{ $locale }}">
        @include('admin.system-management.pages.partials.platform-feature-row', [
            'locale' => $locale,
            'index' => '__INDEX__',
            'title' => '',
            'color' => 'blue',
            'icon' => $defaultIconKey,
            'platformFeatureIcons' => $platformFeatureIcons,
        ])
    </template>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">4</span> Why Support Us ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Section Title</label>
            <input type="text" name="{{ $n('choose_title') }}" class="form-input"
                value="{{ old($n('choose_title'), $v('choose_title', 'Why support our work?')) }}">
        </div>
    </div>
    <div class="form-grid form-grid-3 mt-4">
        <div class="form-group">
            <label class="form-label">Column 1 Text</label>
            <input type="text" name="{{ $n('choose_col_1_text') }}" class="form-input"
                value="{{ old($n('choose_col_1_text'), $v('choose_col_1_text', 'Supported by local partners')) }}">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "platform_sections[{$locale}][choose_col_1_image_file]",
                'pathName' => $n('choose_col_1_image'),
                'label' => 'Column 1 Image',
                'pathValue' => $v('choose_col_1_image', ''),
                'currentImageUrl' => $v('choose_col_1_image'),
                'uploadPath' => 'images/platform',
                'sharedImageKey' => 'platform_choose_col_1_image',
            ])
        </div>
        <div class="form-group">
            <label class="form-label">Column 2 Text</label>
            <input type="text" name="{{ $n('choose_col_2_text') }}" class="form-input"
                value="{{ old($n('choose_col_2_text'), $v('choose_col_2_text', 'Community focus')) }}">
            <label class="form-label mt-2">Column 2 Value</label>
            <input type="text" name="{{ $n('choose_col_2_value') }}" class="form-input"
                value="{{ old($n('choose_col_2_value'), $v('choose_col_2_value', '24/7')) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Column 3 Text</label>
            <input type="text" name="{{ $n('choose_col_3_text') }}" class="form-input"
                value="{{ old($n('choose_col_3_text'), $v('choose_col_3_text', 'Program areas')) }}">
            <label class="form-label mt-2">Column 3 Value</label>
            <input type="text" name="{{ $n('choose_col_3_value') }}" class="form-input"
                value="{{ old($n('choose_col_3_value'), $v('choose_col_3_value', '3+')) }}">
        </div>
    </div>
</div>
