@extends('layouts.admin')

@section('styles')
<style>
    :root { --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --primary: #f68b1e; }

    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); }
    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem; }

    .card { background: white; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
    .card-header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); }
    .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
    .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #fafafa; }

    .status-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-published { background: #dcfce7; color: #15803d; }
    .status-draft { background: #f1f5f9; color: #475569; }

    .cat-pill { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

    .btn-primary { background: var(--primary); color: white; border: none; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-primary:hover { background: #d97706; transform: translateY(-1px); }

    .btn-action { background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 6px; color: var(--text-muted); transition: all 0.15s; display: inline-flex; text-decoration: none; }
    .btn-action:hover { background: #f1f5f9; color: var(--text-main); }
    .btn-action.danger:hover { background: #fee2e2; color: #dc2626; }

    .alert-success { padding: 0.9rem 1.25rem; background: #dcfce7; color: #15803d; border-radius: 10px; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; border: 1px solid #a7f3d0; font-size: 0.9rem; }

    .filters-bar { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
    .search-input { display: flex; align-items: center; gap: 0.5rem; border: 1px solid var(--border); border-radius: 10px; padding: 0.5rem 1rem; background: white; }
    .search-input input { border: none; outline: none; font-size: 0.9rem; width: 220px; }
    .filter-btn { padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border); background: white; font-size: 0.85rem; font-weight: 600; cursor: pointer; color: var(--text-muted); transition: all 0.15s; }
    .filter-btn.active, .filter-btn:hover { background: var(--primary); color: white; border-color: var(--primary); }

    .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-muted); }
    .empty-state svg { width: 48px; height: 48px; margin: 0 auto 1rem; opacity: 0.3; display: block; }
    .empty-state p { font-weight: 600; }

    .pagination-wrap { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }
</style>
@endsection

@section('admin_content')
<div class="page-header">
    <div>
        <h1 class="page-title">Posts & Articles</h1>
        <p class="page-subtitle">Manage your published articles and draft posts.</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="btn-primary">
        <i data-lucide="plus" style="width:18px;height:18px;"></i> New Post
    </a>
</div>

@if(session('success'))
<div class="alert-success">
    <i data-lucide="check-circle" style="width:18px;height:18px;"></i> {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <span class="card-title">All Posts <span style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-left:0.5rem;">{{ $posts->total() }} total</span></span>
        <div class="filters-bar">
            <div class="search-input">
                <i data-lucide="search" style="width:16px;height:16px;color:#94a3b8;"></i>
                <input type="text" placeholder="Search posts..." id="postSearch">
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Post</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr>
                <td>
                    <div style="font-weight:600;font-size:0.9rem;color:var(--text-main);">{{ $post->title }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">By {{ $post->user->name ?? 'Admin' }}</div>
                </td>
                <td>
                    <span class="cat-pill">{{ $post->category->name ?? 'Uncategorized' }}</span>
                </td>
                <td>
                    <span class="status-badge {{ $post->status === 'published' ? 'status-published' : 'status-draft' }}">
                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                        {{ ucfirst($post->status) }}
                    </span>
                </td>
                <td style="font-size:0.8rem;color:var(--text-muted);">{{ $post->created_at->format('M d, Y') }}</td>
                <td style="text-align:right;">
                    <div style="display:flex;gap:0.25rem;justify-content:flex-end;">
                        <a href="{{ route('posts.show', $post) }}" class="btn-action" title="View on Site" target="_blank">
                            <i data-lucide="external-link" style="width:16px;height:16px;"></i>
                        </a>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn-action" title="Edit">
                            <i data-lucide="edit-3" style="width:16px;height:16px;"></i>
                        </a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action danger" title="Delete">
                                <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i data-lucide="file-x" style="width:48px;height:48px;display:block;margin:0 auto 1rem;opacity:0.3;"></i>
                        <p>No posts yet. <a href="{{ route('admin.posts.create') }}" style="color:var(--primary);">Create your first post →</a></p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $posts->links() }}</div>
</div>
@endsection

@section('scripts')
<script>
    lucide.createIcons();
    document.getElementById('postSearch').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.data-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endsection
