@php
    $bannerTitleValue = old('banner_title', $bannerTitle ?? '');
    $bannerDescriptionValue = old('banner_description', $bannerDescription ?? '');
    $bannerBackgroundImageValue = old('background_image', $bannerBackgroundImage ?? '');
@endphp

<div class="edit-page-section">
    <h3 class="edit-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
        Page Banner
    </h3>

    <div class="form-grid">
        <div class="form-group full-width">
            <label for="banner_title" class="form-label">Banner Title</label>
            <input type="text" name="banner_title" id="banner_title" value="{{ $bannerTitleValue }}"
                class="form-input" placeholder="A short banner title">
            <small class="form-hint">Shown at the top of the public page banner.</small>
        </div>

        <div class="form-group full-width">
            <label for="banner_description" class="form-label">Banner Description</label>
            <textarea name="banner_description" id="banner_description" rows="3" class="form-input form-textarea"
                placeholder="A short banner description">{{ $bannerDescriptionValue }}</textarea>
            <small class="form-hint">Keep it concise and relevant to the page.</small>
        </div>

        <div class="form-group full-width">
            @include('admin.system-management.pages.partials.homepage-image-upload', [
                'name' => 'background_image_file',
                'pathName' => 'background_image',
                'label' => 'Banner Image',
                'pathValue' => $bannerBackgroundImageValue,
                'currentImageUrl' => $bannerBackgroundImageValue,
                'uploadPath' => 'images/banners',
                'sharedImageKey' => 'page_banner_background',
                'hint' => 'Shown behind the page banner text on the public website.',
            ])
        </div>
    </div>
</div>
