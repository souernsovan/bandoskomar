@extends('layouts.admin')

@section('styles')
<style>
    :root { --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --primary: #f68b1e; }

    .page-header { margin-bottom: 2rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); }
    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem; }

    .card { background: white; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
    .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #fafafa; }

    .btn-primary { background: var(--primary); color: white; border: none; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.2s; width: 100%; }
    .btn-primary:hover { background: #d97706; transform: translateY(-1px); }

    .btn-action { background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 6px; color: var(--text-muted); transition: all 0.15s; display: inline-flex; text-decoration: none; }
    .btn-action:hover { background: #f1f5f9; color: var(--text-main); }
    .btn-action.danger:hover { background: #fee2e2; color: #dc2626; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: var(--text-main); }
    .form-control { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid var(--border); border-radius: 10px; font-size: 0.95rem; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.15s; outline: none; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(246,139,30,0.1); }

    .alert-success { padding: 0.9rem 1.25rem; background: #dcfce7; color: #15803d; border-radius: 10px; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid #a7f3d0; font-size: 0.9rem; }

    .management-layout { display: grid; grid-template-columns: 350px 1fr; gap: 2rem; }
    @media (max-width: 1024px) { .management-layout { grid-template-columns: 1fr; } }
</style>
@endsection

@section('admin_content')
<div class="page-header">
    <h1 class="page-title">Categories</h1>
    <p class="page-subtitle">Organize your site content with powerful category tags.</p>
</div>

@if(session('success'))
<div class="alert-success">
    <i data-lucide="check-circle" style="width:18px;height:18px;"></i> {{ session('success') }}
</div>
@endif

<div class="management-layout">
    {{-- Add Category Form --}}
    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">Add New Category</span></div>
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Health Outreach" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Briefly describe this category..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i data-lucide="plus-circle" style="width:18px;height:18px;"></i> Add Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Categories List --}}
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">All Categories <span style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-left:0.5rem;">{{ $categories->total() }} total</span></span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--text-main);">{{ $category->name }}</div>
                            @if($category->description)
                            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.2rem;">{{ Str::limit($category->description, 50) }}</div>
                            @endif
                        </td>
                        <td><code style="background:#f1f5f9;padding:0.2rem 0.4rem;border-radius:4px;font-size:0.85rem;color:var(--text-muted);">{{ $category->slug }}</code></td>
                        <td style="text-align:right;">
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category? This will affect posts using it.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action danger" title="Delete">
                                    <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding:3rem;text-align:center;color:var(--text-muted);">No categories defined yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--border);">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    lucide.createIcons();
</script>
@endsection
