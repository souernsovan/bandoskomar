@php
    $globalLocale = '_global';
    $existingStyles = $homepage_styles ?? [];
    if (!is_array($existingStyles)) {
        $existingStyles = [];
    }

    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $n = function ($key) use ($locale) {
        return "homepage_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };

@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">6</span> Program Showcase <span id="styleShowcaseLangLabel">(English)</span></h4>

    <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Program Section Title</label>
            @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                <div class="lang-panel {{ $loop->first ? 'active' : '' }}" id="lang-panel-style-title-{{ $code }}" role="tabpanel">
                    <input type="text" name="homepage_sections[{{ $code }}][style_title]" class="form-input"
                        value="{{ old("homepage_sections.{$code}.style_title", $pageContentByLocale[$code]['style_title'] ?? 'Featured programs') }}">
                </div>
            @endforeach
        </div>
        <div class="form-group">
            <label class="form-label">Section Tagline</label>
            @foreach ($locales ?? \App\Support\PageLocales::labels() as $code => $info)
                <div class="lang-panel {{ $loop->first ? 'active' : '' }}" id="lang-panel-color-choice-title-{{ $code }}" role="tabpanel">
                    <input type="text" name="homepage_sections[{{ $code }}][color_choice_title]" class="form-input"
                        value="{{ old("homepage_sections.{$code}.color_choice_title", $pageContentByLocale[$code]['color_choice_title'] ?? 'Program areas') }}">
                </div>
            @endforeach
        </div>
    </div>

    <div class="style-items-manager" id="styleItemsManager__global" data-locale="_global">
        <div class="style-items-list" id="styleItemsList__global">
            @forelse($existingStyles as $si => $style)
                <div class="style-item-card is-collapsed" data-style-index="{{ $si }}">
                    <div class="style-item-header" data-toggle-style>
                        <span class="style-item-number">STYLE {{ str_pad($si + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="style-item-header-actions">
                            <button type="button" class="style-item-toggle-btn" data-toggle-style-btn title="Expand / Collapse">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <button type="button" class="style-item-delete-btn" data-delete-style title="Remove this style">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="style-item-body">
                        <div class="form-group" data-shared-image="style_{{ $si }}_image">
                            @include('admin.system-management.pages.partials.homepage-image-upload', [
                                'name' => "homepage_sections[_global][style_{$si}_image_file]",
                                'pathName' => "homepage_sections[_global][styles][{$si}][image]",
                                'label' => 'Program Background Image',
                                'pathValue' => $style['image'] ?? '',
                                'currentImageUrl' => $style['image'] ?? '',
                                'pathOnly' => false,
                                'uploadPath' => 'images/homepage',
                                'sharedImageKey' => "style_{$si}_image",
                            ])
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Program Colors</label>
                            <div class="style-color-items" data-color-list data-style-index="{{ $si }}"
                                data-locale="_global">
                                @foreach ($style['colors'] ?? [] as $ci => $color)
                                    @php
                                        $cName = is_array($color) ? $color['name'] ?? '' : (string) $color;
                                        $cHex = is_array($color) ? $color['hex'] ?? '#6366f1' : '#6366f1';
                                        $cImage = is_array($color) ? $color['image'] ?? '' : '';
                                    @endphp
                                    <div class="style-color-entry" data-color-index="{{ $ci }}">
                                        <div class="style-color-entry-header">
                                            <span class="style-color-entry-num">{{ $ci + 1 }}</span>
                                            <button type="button" class="style-color-entry-delete" data-delete-color
                                                title="Remove color">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    width="14" height="14">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="style-color-entry-body style-color-entry-body--horizontal">
                                            <div class="style-color-entry-left">
                                                <div class="style-color-field">
                                                    <label class="form-label form-label-sm">Color Name</label>
                                                    <input type="text" class="form-input form-input-sm"
                                                        name="homepage_sections[_global][styles][{{ $si }}][colors][{{ $ci }}][name]"
                                                        value="{{ $cName }}"
                                                        placeholder="e.g. Dark Emerald Green">
                                                </div>
                                                <div class="style-color-field">
                                                    <label class="form-label form-label-sm">Hex Color</label>
                                                    <div class="style-color-picker-wrap">
                                                        <input type="color" class="style-color-picker-input"
                                                            name="homepage_sections[_global][styles][{{ $si }}][colors][{{ $ci }}][hex]"
                                                            value="{{ $cHex }}">
                                                        <input type="text"
                                                            class="form-input form-input-sm style-color-hex-text"
                                                            value="{{ $cHex }}" placeholder="#000000"
                                                            data-hex-text>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="style-color-entry-right">
                                                @include(
                                                    'admin.system-management.pages.partials.homepage-image-upload',
                                                    [
                                                        'name' => "homepage_sections[_global][style_{$si}_color_{$ci}_image_file]",
                                                        'pathName' => "homepage_sections[_global][styles][{$si}][colors][{$ci}][image]",
                                                        'label' => 'Preview Image',
                                                        'pathValue' => $cImage,
                                                        'currentImageUrl' => $cImage,
                                                        'pathOnly' => false,
                                                        'uploadPath' => 'images/homepage',
                                                        'sharedImageKey' => "style_{$si}_color_{$ci}_image",
                                                    ]
                                                )
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline style-add-color-btn" data-add-color
                                data-style-index="{{ $si }}" data-locale="_global">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" width="14" height="14">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Color
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="style-items-empty" id="styleItemsEmpty__global">No program cards added yet. Click "Add Program Card" to create one.</p>
            @endforelse
        </div>
        <button type="button" class="btn btn-outline style-add-btn" data-add-style data-locale="_global">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Program Card
        </button>
    </div>
</div>
