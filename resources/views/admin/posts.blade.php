@extends('layouts.admin')

@section('admin_content')
<div class="post-management">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Content › <span style="color: var(--primary); font-weight: 600;">Post Management</span></div>
            <h1 style="font-size: 1.8rem; color: var(--text-main);">Post Management</h1>
            <p style="color: var(--text-muted);">Review, edit, and organize all published and draft articles.</p>
        </div>
        <button class="btn-primary"><span>➕</span> Create New Post</button>
    </div>

    <div class="stats-grid">
        <div class="card stat-card">
            <div class="stat-header">
                <div class="stat-icon">📄</div>
                <div class="stat-change up">+12%</div>
            </div>
            <div class="stat-value">1,284</div>
            <div class="stat-label">Total Posts</div>
        </div>
        <div class="card stat-card">
            <div class="stat-header">
                <div class="stat-icon">✅</div>
                <div class="stat-change" style="color: var(--text-muted);">Stable</div>
            </div>
            <div class="stat-value">1,102</div>
            <div class="stat-label">Published</div>
        </div>
        <div class="card stat-card">
            <div class="stat-header">
                <div class="stat-icon">✏️</div>
                <div class="stat-change up">+2 new</div>
            </div>
            <div class="stat-value">182</div>
            <div class="stat-label">Drafts</div>
        </div>
        <div class="card stat-card">
            <div class="stat-header">
                <div class="stat-icon">👁️</div>
                <div class="stat-change up">+24k</div>
            </div>
            <div class="stat-value">4.8k</div>
            <div class="stat-label">Avg. Engagement</div>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div style="display: flex; gap: 1rem;">
                <button style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border); background: white; font-size: 0.85rem;">
                    <span>📅</span> All Status
                </button>
                <button style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border); background: white; font-size: 0.85rem;">
                    <span>🕒</span> All Time
                </button>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span style="font-size: 0.85rem; color: var(--text-muted);">Bulk Actions:</span>
                <button style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid var(--border); background: white; font-size: 0.85rem; font-weight: 600;">🔄 Update Status</button>
                <button style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #fee2e2; background: #fff1f1; color: #ef4444; font-size: 0.85rem; font-weight: 600;">🗑️ Delete Selection</button>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border); text-align: left;">
                    <th style="padding: 1rem;"><input type="checkbox"></th>
                    <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Post Details</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Author</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Category</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Date</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 1rem;"><input type="checkbox"></td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <img src="{{ $post['image'] }}" style="width: 60px; height: 45px; border-radius: 8px; object-fit: cover;">
                            <div style="display: flex; flex-direction: column;">
                                <span style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);">{{ $post['title'] }}</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">slug: {{ $post['slug'] }}</span>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700;">
                                {{ substr($post['author'], 0, 1) }}{{ substr(explode(' ', $post['author'])[1] ?? '', 0, 1) }}
                            </div>
                            <span style="font-size: 0.9rem;">{{ $post['author'] }}</span>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.2rem 0.5rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; background: #f3f4f6; color: #4b5563; border: 1px solid var(--border);">{{ $post['category'] }}</span>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; flex-direction: column;">
                            <span style="font-size: 0.85rem; font-weight: 500;">{{ $post['date'] }}</span>
                            <span style="font-size: 0.7rem; color: var(--text-muted);">10:45 AM</span>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; color: {{ $post['status'] == 'Published' ? '#10b981' : '#6b7280' }};">
                            <span style="font-size: 0.6rem;">●</span> {{ $post['status'] }}
                        </div>
                    </td>
                    <td style="padding: 1rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <button style="background: transparent; border: none; cursor: pointer; color: var(--text-muted);">✏️</button>
                            <button style="background: transparent; border: none; cursor: pointer; color: var(--text-muted);">🗑️</button>
                            <button style="background: transparent; border: none; cursor: pointer; color: var(--text-muted);">⋮</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding: 1rem 0;">
            <span style="font-size: 0.85rem; color: var(--text-muted);">Showing 1 to 4 of 1,284 entries</span>
            <div style="display: flex; gap: 0.25rem;">
                <button style="padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid var(--border); background: white;">‹</button>
                <button style="padding: 0.4rem 0.8rem; border-radius: 6px; border: none; background: var(--primary); color: white; font-weight: 600;">1</button>
                <button style="padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid var(--border); background: white;">2</button>
                <button style="padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid var(--border); background: white;">3</button>
                <span style="padding: 0.4rem;">...</span>
                <button style="padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid var(--border); background: white;">321</button>
                <button style="padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid var(--border); background: white;">›</button>
            </div>
        </div>
    </div>
</div>
@endsection
