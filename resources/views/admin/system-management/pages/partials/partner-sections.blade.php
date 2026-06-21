<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a8.25 8.25 0 1 0-8.25-8.25A8.25 8.25 0 0 0 12 21Z" />
    </svg>
    Partner Page Sections ({{ $localeName }})
</h3>

@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $n = function ($key) use ($locale) {
        return "partner_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d) {
        return $d[$key] ?? $default;
    };
@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">1</span> Partner hero ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Hero title</label>
            <input type="text" name="{{ $n('partners_title') }}" class="form-input"
                value="{{ old($n('partners_title'), $v('partners_title', 'How you can partner')) }}"
                placeholder="How you can partner">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Hero description</label>
            <textarea name="{{ $n('partners_description') }}" rows="3" class="form-input form-textarea"
                placeholder="A short description for the partner page">{{ old($n('partners_description'), $v('partners_description', 'We work with schools, donors, corporations, and NGOs to support education programs.')) }}</textarea>
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => "partner_sections[{$locale}][partner_feature_image_file]",
                'pathName' => "partner_sections[{$locale}][partner_feature_image]",
                'oldName' => "partner_sections.$locale.partner_feature_image",
                'label' => 'Hero image',
                'pathValue' => $v('partner_feature_image', ''),
                'currentImageUrl' => $v('partner_feature_image', ''),
                'uploadPath' => 'images/partners',
                'sharedImageKey' => 'partner_feature_image',
                'hint' => 'Shown on the right side of the Partner page hero.',
            ])
        </div>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">2</span> Partner feature cards ({{ $localeName }})</h4>
    <div class="form-grid">
        @php
            $partnerFeatures = $v('partner_features', [
                ['title' => 'Funding & grants', 'description' => 'Support programs, materials, teacher stipends, and infrastructure projects.'],
                ['title' => 'In-kind expertise', 'description' => 'Offer training, curriculum resources, monitoring & evaluation, or tech support.'],
            ]);
        @endphp
        @foreach ($partnerFeatures as $i => $card)
            @php
                $featureTitleOldKey = "partner_sections.$locale.partner_features.$i.title";
                $featureDescriptionOldKey = "partner_sections.$locale.partner_features.$i.description";
            @endphp
            <div class="homepage-section-card" style="margin-top: 0;">
                <h4 class="homepage-section-heading"><span class="homepage-section-num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span> Feature card {{ $i + 1 }}</h4>
                <div class="form-group full-width">
                    <label class="form-label">Title</label>
                    <input type="text" name="{{ $n('partner_features') }}[{{ $i }}][title]" class="form-input"
                        value="{{ old($featureTitleOldKey, $card['title'] ?? '') }}">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Description</label>
                    <textarea name="{{ $n('partner_features') }}[{{ $i }}][description]" rows="3" class="form-input form-textarea">{{ old($featureDescriptionOldKey, $card['description'] ?? '') }}</textarea>
                </div>
            </div>
        @endforeach
        <div class="form-group full-width">
            <label class="form-label">Supporters section title</label>
            <input type="text" name="{{ $n('supporters_title') }}" class="form-input"
                value="{{ old($n('supporters_title'), $v('supporters_title', 'Our supporters')) }}"
                placeholder="Our supporters">
        </div>
        <div class="form-group full-width">
            <label class="form-label">Supporters section description</label>
            <textarea name="{{ $n('supporters_description') }}" rows="3" class="form-input form-textarea"
                placeholder="A short description for the supporters strip">{{ old($n('supporters_description'), $v('supporters_description', 'Partners and organizations helping grow practical education and community support.')) }}</textarea>
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => 'partner_image_files[]',
                'label' => 'Add Supporter Logos',
                'multiple' => true,
                'keepImagesName' => 'partner_keep_images',
                'stagedPurpose' => 'partner_page',
                'currentImages' => $v('partner_images', $partner_page_images ?? []),
                'hint' => 'Upload or remove. Changes apply to all languages.',
            ])
        </div>
    </div>
</div>
