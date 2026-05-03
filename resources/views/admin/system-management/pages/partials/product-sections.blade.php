<h3 class="edit-section-title">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
    </svg>
    Programs Page Sections
</h3>
@php
    $locale = $locale ?? 'en';
    $localeName = $localeName ?? $locale;
    $d = $localeData ?? [];
    $defaultDescription = 'Our programs support communities through education, health, relief, and local empowerment. Each initiative is designed to create practical, measurable impact.';
    $n = function ($key) use ($locale) {
        return "product_sections[{$locale}][{$key}]";
    };
    $v = function ($key, $default = '') use ($d, $defaultDescription) {
        if ($key === 'description') {
            return $d[$key] ?? $defaultDescription;
        }
        return $d[$key] ?? $default;
    };
@endphp

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">1</span> Description ({{ $localeName }})</h4>
    <div class="form-group full-width">
        <label class="form-label">Programs Page Description</label>
        <textarea name="{{ $n('description') }}" rows="6"
            class="form-input form-textarea @error('product_sections.*.description') error @enderror">{{ old($n('description'), $v('description')) }}</textarea>
        <small class="form-hint">Main intro text displayed above the product grid.</small>
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">2</span> Programs ({{ $localeName }})</h4>
    <div class="form-group full-width">
        <label for="product_sections_{{ $locale }}_products_title" class="form-label">Programs Section Title</label>
        <input type="text" name="{{ $n('products_title') }}" id="product_sections_{{ $locale }}_products_title"
            class="form-input @error('product_sections.*.products_title') error @enderror"
            value="{{ old($n('products_title'), $v('products_title', 'Our Programs')) }}"
            placeholder="Our Programs">
        <small class="form-hint">Title displayed above the program grid for this language.</small>
        @error('product_sections.*.products_title')
            <span class="form-error">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="homepage-section-card">
    <h4 class="homepage-section-heading"><span class="homepage-section-num">3</span> Supporters ({{ $localeName }})</h4>
    <div class="form-grid">
        <div class="form-group full-width">
            <label class="form-label">Supporters Title</label>
            <input type="text" name="{{ $n('partners_title') }}" class="form-input"
                value="{{ old($n('partners_title'), $v('partners_title', 'Our supporters')) }}">
        </div>
        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => 'product_partner_image_files[]',
                'label' => 'Add Supporter Logos',
                'multiple' => true,
                'keepImagesName' => 'product_keep_partner_images',
                'stagedPurpose' => 'product_partner',
                'currentImages' => $v('partner_images', $product_partner_images ?? []),
                'hint' => 'Upload or remove. Changes apply to all languages.',
            ])
        </div>
    </div>
</div>
