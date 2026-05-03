@extends('admin.layouts.app')

@section('title', 'Add Program')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Add Program</h2>
            <p>Create a new community program</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-section">
                <h4 class="form-section-title">Basic Information</h4>
                <div class="form-grid form-grid-3">
                    <div class="form-group">
                        <label for="title" class="form-label">Title <span class="form-required">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Enter program title" class="form-input @error('title') error @enderror">
                        @error('title')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <p class="form-hint">Give this program a clear, descriptive name</p>
                    </div>
                    <div class="form-group">
                        <label for="slug" class="form-label">URL Slug (Optional)</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Auto-generated from title" class="form-input @error('slug') error @enderror">
                        @error('slug')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <p class="form-hint">Leave blank to auto-generate from the title</p>
                    </div>
                    <div class="form-group">
                        <label for="status" class="form-label">Status <span class="form-required">*</span></label>
                        <select name="status" id="status" required class="form-input @error('status') error @enderror">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active - Visible to public</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive - Hidden from public</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="form-section-title">Program Details</h4>
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="5" class="form-input form-textarea @error('description') error @enderror" placeholder="Describe this program, its goals, and how it helps the community...">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <p class="form-hint">Provide details about what this program does and who it serves</p>
                </div>
            </div>

            <div class="form-section">
                <h4 class="form-section-title">Program Image (Optional)</h4>
                <div class="form-group full-width">
                    <label class="form-label">Upload Image</label>
                    <div class="image-upload-wrapper">
                        <div class="image-drop-zone" id="imageDropZone">
                            <input type="file" id="imageInput" name="image" class="image-file-input" accept="image/jpeg,image/png,image/gif,image/webp">
                            <div class="drop-zone-content">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="drop-zone-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="drop-zone-text">Drag & drop image here</span>
                                <span class="drop-zone-subtext">or click to browse files</span>
                                <span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>
                            </div>
                            <div class="image-preview" id="imagePreview"></div>
                            <button type="button" class="remove-image-btn" id="removeImage" style="display: none;" title="Remove image">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="image-error" id="imageError" style="display: none;"></div>
                    </div>
                    @error('image')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <p class="form-hint">Add an image to make this program more engaging. Recommended size: 1200x800px</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Create Program
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
