@extends('layouts.admin')

@section('styles')
<style>
    :root { --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --primary: #f68b1e; }

    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); }
    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem; }

    .back-link { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: var(--text-muted); text-decoration: none; font-weight: 600; margin-bottom: 1.5rem; }
    .back-link:hover { color: var(--primary); }

    .form-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    @media (max-width: 1024px) { .form-grid { grid-template-columns: 1fr; } }

    .card { background: white; border: 1px solid var(--border); border-radius: 16px; }
    .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); }
    .card-body { padding: 1.5rem; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main); }
    .form-label span { color: #ef4444; margin-left: 0.2rem; }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border); border-radius: 10px; font-size: 0.95rem; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.15s; outline: none; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(246,139,30,0.1); }
    textarea.form-control { resize: vertical; min-height: 280px; line-height: 1.6; }

    .btn-primary { background: var(--primary); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; transition: all 0.2s; }
    .btn-primary:hover { background: #d97706; transform: translateY(-1px); }
    .btn-secondary { background: white; color: var(--text-muted); border: 1.5px solid var(--border); padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; text-decoration: none; transition: all 0.15s; margin-top: 0.75rem; }
    .btn-secondary:hover { background: #f8fafc; }

    .upload-zone { border: 2px dashed var(--border); border-radius: 12px; padding: 2rem; text-align: center; color: var(--text-muted); cursor: pointer; transition: all 0.2s; }
    .upload-zone:hover { border-color: var(--primary); color: var(--primary); background: #fff7ed; }
    .upload-zone svg { margin: 0 auto 0.75rem; display: block; width: 36px; height: 36px; }

    .error-msg { color: #dc2626; font-size: 0.8rem; margin-top: 0.35rem; font-weight: 500; }
</style>
@endsection

@section('admin_content')
<a href="{{ route('admin.posts.index') }}" class="back-link">
    <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back to Posts
</a>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Post</h1>
        <p class="page-subtitle">Update your existing article or news content.</p>
    </div>
</div>

<form action="{{ route('admin.posts.update', $post) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-grid">
        {{-- Main content --}}
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <div class="card">
                <div class="card-header"><span class="card-title">Post Content</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Post Title <span>*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Enter post title..." value="{{ old('title', $post->title) }}" required>
                        @error('title') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Content <span>*</span></label>
                        <textarea name="content" class="form-control" placeholder="Write your post content here..." required>{{ old('content', $post->content) }}</textarea>
                        @error('content') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">
            <div class="card">
                <div class="card-header"><span class="card-title">Update Actions</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Status <span>*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category <span>*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="save" style="width:16px;height:16px;"></i> Update Post
                    </button>
                    <a href="{{ route('admin.posts.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><span class="card-title">Featured Image</span></div>
                <div class="card-body">
                    <div class="upload-zone">
                        <i data-lucide="image"></i>
                        <p style="font-size:0.85rem;font-weight:600;">Update image</p>
                        <p style="font-size:0.75rem;margin-top:0.25rem;">PNG, JPG up to 5MB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    lucide.createIcons();
</script>
@endsection
