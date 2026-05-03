@extends('layouts.admin')

@section('styles')
<style>
    :root {
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border: #e2e8f0;
    }
    .page-header { margin-bottom: 2rem; }
    .page-title { font-size: 1.75rem; font-weight: 800; color: var(--text-main); }
    .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin-top: 0.25rem; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        transition: box-shadow 0.2s;
        text-decoration: none;
    }
    .stat-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon svg { width: 24px; height: 24px; }
    .icon-blue { background: #eff6ff; color: #2563eb; }
    .icon-green { background: #f0fdf4; color: #16a34a; }
    .icon-orange { background: #fff7ed; color: #ea580c; }
    .icon-purple { background: #faf5ff; color: #9333ea; }

    .stat-info { flex: 1; }
    .stat-value { font-size: 2rem; font-weight: 800; color: var(--text-main); line-height: 1; }
    .stat-label { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.35rem; }
    .stat-badge { font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 0.2rem; margin-top: 0.5rem; }
    .badge-up { background: #dcfce7; color: #15803d; }
    .badge-down { background: #fee2e2; color: #b91c1c; }
    .badge-neutral { background: #f1f5f9; color: #475569; }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }

    .card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }
    .card-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    .card-title { font-weight: 700; font-size: 1rem; color: var(--text-main); }
    .card-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem; }
    .card-body { padding: 1.5rem; }
    .card-link { font-size: 0.85rem; font-weight: 600; color: #f68b1e; text-decoration: none; display: flex; align-items: center; gap: 0.25rem; }
    .card-link:hover { text-decoration: underline; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
    .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #fafafa; }

    .post-title { font-weight: 600; font-size: 0.9rem; color: var(--text-main); margin-bottom: 0.2rem; }
    .post-meta { font-size: 0.75rem; color: var(--text-muted); }

    .status-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .status-published { background: #dcfce7; color: #15803d; }
    .status-draft { background: #f1f5f9; color: #475569; }

    .cat-pill { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }

    .btn-action { background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 6px; color: var(--text-muted); transition: all 0.15s; display: inline-flex; }
    .btn-action:hover { background: #f1f5f9; color: var(--text-main); }

    .donor-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9; }
    .donor-row:last-child { border-bottom: none; }
    .avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: white; flex-shrink: 0; }
    .donor-name { font-weight: 600; font-size: 0.9rem; }
    .donor-count { font-size: 0.75rem; color: var(--text-muted); }
    .donor-amount { margin-left: auto; font-weight: 700; font-size: 0.9rem; color: var(--text-main); }

    .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
    .quick-action-btn {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 0.5rem; padding: 1.25rem; border-radius: 12px;
        border: 1.5px dashed var(--border); text-decoration: none;
        color: var(--text-muted); font-size: 0.8rem; font-weight: 600;
        transition: all 0.2s; text-align: center;
    }
    .quick-action-btn:hover { border-color: #f68b1e; color: #f68b1e; background: #fff7ed; }
    .quick-action-btn svg { width: 22px; height: 22px; }
</style>
@endsection

@section('admin_content')
<div class="page-header">
    <h1 class="page-title">Dashboard Overview</h1>
    <p class="page-subtitle">Welcome back, Administrator. Here's what's happening with Bandos Komar today.</p>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <a href="{{ route('admin.users.index') }}" class="stat-card">
        <div class="stat-icon icon-blue"><i data-lucide="users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_users']['value'] }}</div>
            <div class="stat-label">Total Users</div>
            <span class="stat-badge {{ $stats['total_users']['up'] === null ? 'badge-neutral' : ($stats['total_users']['up'] ? 'badge-up' : 'badge-down') }}">
                @if($stats['total_users']['up'] !== null)
                    <i data-lucide="{{ $stats['total_users']['up'] ? 'trending-up' : 'trending-down' }}" style="width:12px;height:12px;"></i>
                @endif
                {{ $stats['total_users']['change'] }}
            </span>
        </div>
    </a>
    <a href="{{ route('admin.posts.index') }}" class="stat-card">
        <div class="stat-icon icon-green"><i data-lucide="file-text"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_posts']['value'] }}</div>
            <div class="stat-label">Total Posts</div>
            <span class="stat-badge {{ $stats['total_posts']['up'] === null ? 'badge-neutral' : ($stats['total_posts']['up'] ? 'badge-up' : 'badge-down') }}">
                @if($stats['total_posts']['up'] !== null)
                    <i data-lucide="{{ $stats['total_posts']['up'] ? 'trending-up' : 'trending-down' }}" style="width:12px;height:12px;"></i>
                @endif
                {{ $stats['total_posts']['change'] }}
            </span>
        </div>
    </a>
    <a href="{{ route('admin.donations.index') }}" class="stat-card">
        <div class="stat-icon icon-orange"><i data-lucide="dollar-sign"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total_donations']['value'] }}</div>
            <div class="stat-label">Total Donations</div>
            <span class="stat-badge {{ $stats['total_donations']['up'] === null ? 'badge-neutral' : ($stats['total_donations']['up'] ? 'badge-up' : 'badge-down') }}">
                {{ $stats['total_donations']['change'] }}
            </span>
        </div>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="stat-card">
        <div class="stat-icon icon-purple"><i data-lucide="folder"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['active_categories']['value'] }}</div>
            <div class="stat-label">Categories</div>
            <span class="stat-badge badge-neutral">{{ $stats['active_categories']['change'] }}</span>
        </div>
    </a>
</div>

{{-- Main content grid --}}
<div class="content-grid">
    {{-- Recent Posts --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Recent Posts</div>
                <div class="card-subtitle">Latest articles and news</div>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="card-link">View All <i data-lucide="arrow-right" style="width:14px;height:14px;"></i></a>
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
                @forelse($recent_posts as $post)
                <tr>
                    <td>
                        <div class="post-title">{{ $post['title'] }}</div>
                        <div class="post-meta">By {{ $post['author'] }}</div>
                    </td>
                    <td><span class="cat-pill">{{ $post['category'] }}</span></td>
                    <td>
                        <span class="status-badge {{ $post['status'] === 'Published' ? 'status-published' : 'status-draft' }}">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                            {{ $post['status'] }}
                        </span>
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">{{ $post['date'] }}</td>
                    <td style="text-align:right;">
                        <a href="{{ route('admin.posts.edit', $post['id']) }}" class="btn-action"><i data-lucide="edit-3" style="width:16px;height:16px;"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 1.5rem; color: var(--text-muted); text-align: center;">No posts yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem;">
        {{-- Top Donors --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Top Donors</div>
                    <div class="card-subtitle">Most generous contributors</div>
                </div>
                <a href="{{ route('admin.donations.index') }}" class="card-link">View All <i data-lucide="arrow-right" style="width:14px;height:14px;"></i></a>
            </div>
            <div class="card-body">
                @php $colors = ['#f68b1e','#2563eb','#16a34a','#9333ea','#dc2626']; @endphp
                @forelse($top_donors as $i => $donor)
                <div class="donor-row">
                    <div class="avatar" style="background:{{ $colors[$i % count($colors)] }};">{{ $donor['initials'] }}</div>
                    <div>
                        <div class="donor-name">{{ $donor['name'] }}</div>
                        <div class="donor-count">{{ $donor['donations'] }}</div>
                    </div>
                    <div class="donor-amount">{{ $donor['amount'] }}</div>
                </div>
                @empty
                <div style="color: var(--text-muted); text-align: center; padding: 1rem 0;">No completed donations yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="{{ route('admin.posts.create') }}" class="quick-action-btn">
                        <i data-lucide="plus-circle"></i> New Post
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="quick-action-btn">
                        <i data-lucide="folder-plus"></i> New Category
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="quick-action-btn">
                        <i data-lucide="user-plus"></i> Add User
                    </a>
                    <a href="{{ route('admin.donations.index') }}" class="quick-action-btn">
                        <i data-lucide="heart-handshake"></i> Donations
                    </a>
                </div>
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
