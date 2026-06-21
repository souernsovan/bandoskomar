
@php
    $multiple = $multiple ?? false;
    $pathOnly = $pathOnly ?? false;
    $sharedImageKey = $sharedImageKey ?? null;
    $keepImagesName = $keepImagesName ?? 'homepage_keep_partner_images';
    $fullCard = $fullCard ?? false;
    $oldName = $oldName ?? ($pathName ?? null);
    $id = 'hp_' . preg_replace('/[^a-z0-9]/', '_', $name ?? $pathName ?? 'img');
    $wrapperClass = $multiple ? 'homepage-partners-upload' : 'homepage-image-upload';
    if ($fullCard && !$multiple) {
        $wrapperClass .= ' homepage-image-upload--full-card';
    }
@endphp
<div class="form-group" @if($sharedImageKey) data-shared-image="{{ $sharedImageKey }}" @endif>
    <label class="form-label">{{ $label }}</label>
    @if (!$multiple && isset($pathName))
    <input type="text" name="{{ $pathName }}" class="form-input homepage-image-path-input @error($pathName) error @enderror"
        value="{{ old($oldName ?? $pathName, $pathValue ?? '') }}" placeholder="{{ $uploadPath ?? 'images/home/...' }}"
        data-path-input @if($sharedImageKey) data-shared-image="{{ $sharedImageKey }}" @endif
        style="display: none;">
    @endif
    @if ($pathOnly)
    {{-- Path-only mode (non-EN locale): show preview + remove button (upload in EN tab) --}}
    <div class="image-upload-wrapper {{ $wrapperClass }}" data-upload-id="{{ $id }}">
        <div class="image-drop-zone {{ !empty($currentImageUrl) ? 'has-file' : '' }}" data-path-only-zone>
            <div class="image-preview {{ !empty($currentImageUrl) ? 'has-image' : '' }}" data-preview>
                @if(!empty($currentImageUrl))
                    <div class="image-preview-item">
                        <img src="{{ asset($currentImageUrl) }}" alt="Current">
                        <div class="image-preview-info">
                            <span class="image-name">Current Image</span>
                            <span class="image-size">Uploaded</span>
                        </div>
                    </div>
                    <button type="button" class="remove-image-btn" data-path-only-remove
                        @if($sharedImageKey) data-shared-image="{{ $sharedImageKey }}" @endif
                        title="Remove image (applies to all languages)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @else
                    <div class="drop-zone-content">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="drop-zone-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25z" />
                        </svg>
                        <span class="drop-zone-text">Drag & drop an image here</span>
                        <span class="drop-zone-subtext">or click to browse (edit in English tab)</span>
                        <span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="image-upload-wrapper {{ $wrapperClass }}" data-upload-id="{{ $id }}" @if($multiple) data-partners-upload data-partners-input-name="{{ $name }}" @if(!empty($stagedPurpose)) data-staged-purpose="{{ $stagedPurpose }}" @endif @endif>
        <div class="image-drop-zone {{ !$multiple && !empty($currentImageUrl) ? 'has-file' : '' }}" @if($multiple) data-partners-drop-zone @else data-drop-zone @endif>
            <input type="file" name="{{ $name }}" class="image-file-input"
                @if($multiple) data-partners-file-input multiple @else data-file-input @endif
                accept="image/jpeg,image/png,image/gif,image/webp">
            <div class="drop-zone-content" @if($multiple) data-partners-empty-state @endif @if(!$multiple && !empty($currentImageUrl)) style="display: none;" @endif>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="drop-zone-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                <span class="drop-zone-text">{{ $multiple ? 'Drag & drop images here' : 'Drag & drop an image here' }}</span>
                <span class="drop-zone-subtext">or click to browse</span>
                <span class="drop-zone-hint">{{ $multiple ? 'Multiple images • JPEG, PNG, GIF, WebP • Max 10MB each' : 'JPEG, PNG, GIF, WebP • Max 10MB' }}</span>
            </div>
            @if ($multiple)
            <div class="image-preview partners-preview-grid" data-partners-preview style="display: none;"></div>
            @else
            <div class="image-preview {{ !empty($currentImageUrl) ? 'has-image' : '' }}" data-preview>
                @if(!empty($currentImageUrl))
                    <div class="image-preview-item">
                        <img src="{{ asset($currentImageUrl) }}" alt="Current">
                        <div class="image-preview-info @if($fullCard) image-preview-info--compact @endif">
                            <span class="image-name">Current Image</span>
                            <span class="image-size">Uploaded</span>
                        </div>
                    </div>
                @endif
            </div>
            <button type="button" class="remove-image-btn" data-remove-btn @if($sharedImageKey) data-shared-image="{{ $sharedImageKey }}" @endif @if(empty($currentImageUrl)) style="display: none;" @endif title="Remove image">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            @endif
        </div>
        <div class="image-error" @if($multiple) data-partners-error @else data-error @endif style="display: none;"></div>
    </div>
    @endif
    @if(isset($hint) && $hint)
        <small class="form-hint">{{ $hint }}</small>
    @endif
    @if(!$multiple && isset($pathName))
        @error($pathName)
            <span class="form-error">{{ $message }}</span>
        @enderror
    @endif
</div>

@if ($fullCard)
@once
<style>
    .homepage-image-upload--full-card .image-drop-zone {
        border-radius: 24px;
        overflow: hidden;
        min-height: 64px;
        padding: 0;
        background: #f8fafc;
    }

    .homepage-image-upload--full-card .image-drop-zone.has-file {
        min-height: 240px;
    }

    .homepage-image-upload--full-card .drop-zone-content {
        min-height: 64px;
        padding: 0.35rem 0.6rem;
        justify-content: center;
        text-align: center;
    }

    .homepage-image-upload--full-card .image-drop-zone.has-file .drop-zone-content {
        min-height: 240px;
    }

    .homepage-image-upload--full-card .image-preview {
        display: block;
        width: 100%;
        min-height: 240px;
    }

    .homepage-image-upload--full-card .image-preview-item {
        position: relative;
        display: block;
        width: 100%;
        min-height: 240px;
        height: 100%;
        padding: 0;
        border: 0;
        border-radius: 0;
        overflow: hidden;
    }

    .homepage-image-upload--full-card .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        min-height: 240px;
        border-radius: 0;
    }

    .homepage-image-upload--full-card .image-preview-info {
        display: none !important;
    }

    .homepage-image-upload--full-card .image-preview-item img {
        width: 100% !important;
        height: 100% !important;
    }

    .homepage-image-upload--full-card .drop-zone-icon {
        width: 16px;
        height: 16px;
    }

    .homepage-image-upload--full-card .drop-zone-text {
        font-size: 10px;
    }

    .homepage-image-upload--full-card .drop-zone-subtext {
        font-size: 8px;
    }

    .homepage-image-upload--full-card .drop-zone-hint {
        font-size: 7px;
    }

    .homepage-image-upload--full-card .image-preview-item,
    .homepage-image-upload--full-card .image-preview {
        border-radius: 0 !important;
    }

    .homepage-image-upload--full-card .remove-image-btn {
        top: 1rem;
        right: 1rem;
        z-index: 5;
    }
</style>
@endonce
@endif

@php
    $showCurrentPartners = $multiple && isset($currentImages) && is_array($currentImages) && count($currentImages) > 0;
    $currentListLabel = $currentListLabel ?? 'Current Supporter Logos';
@endphp
@if ($showCurrentPartners)
<div class="form-group mt-4">
    <label class="form-label">{{ $currentListLabel }}</label>
    <div class="partners-current-list flex flex-col gap-3">
        @foreach ($currentImages as $img)
            <div class="image-preview-item partners-current-item">
                <img src="{{ asset($img) }}" alt="Supporter logo">
                <div class="image-preview-info">
                    <span class="image-name">Current Image</span>
                    <span class="image-size">Uploaded</span>
                </div>
                <input type="checkbox" name="{{ $keepImagesName }}[]" value="{{ $img }}" checked class="partner-keep-checkbox sr-only">
                <button type="button" class="remove-image-btn partner-remove-btn" title="Remove from list" data-partner-remove>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endforeach
    </div>
</div>
@endif
