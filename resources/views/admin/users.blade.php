@extends('layouts.admin')

@section('styles')
<style>
    :root { --text-main: #1e293b; --text-muted: #64748b; --border: #e2e8f0; --primary: #f68b1e; }

    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; }
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

    .user-info { display: flex; align-items: center; gap: 0.85rem; }
    .user-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: #f1f5f9; }
    .user-initials { width: 36px; height: 36px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }

    .role-badge { padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; background: #f1f5f9; color: #475569; }
    .role-admin { background: #eff6ff; color: #1d4ed8; }
    .role-editor { background: #f0fdf4; color: #15803d; }

    .status-indicator { display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; }
    .status-active { color: #10b981; }
    .status-suspended { color: #ef4444; }

    .btn-primary { background: var(--primary); color: white; border: none; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.9rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.2s; }
    .btn-primary:hover { background: #d97706; transform: translateY(-1px); }

    .btn-action { background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 6px; color: var(--text-muted); transition: all 0.15s; display: inline-flex; }
    .btn-action:hover { background: #f1f5f9; color: var(--text-main); }
</style>
@endsection

@section('admin_content')
<div class="page-header">
    <div>
        <h1 class="page-title">User Accounts</h1>
        <p class="page-subtitle">Review and manage administrative access across the platform.</p>
    </div>
    <button class="btn-primary">
        <i data-lucide="user-plus" style="width:18px;height:18px;"></i> Add New User
    </button>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">System Users <span style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-left:0.5rem;">{{ count($users) }} total</span></span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Active</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <div class="user-info">
                        @if($user['avatar'])
                            <img src="{{ $user['avatar'] }}" class="user-avatar" alt="{{ $user['name'] }}">
                        @else
                            <div class="user-initials">{{ $user['initials'] }}</div>
                        @endif
                        <div style="font-weight:600;color:var(--text-main);font-size:0.9rem;">{{ $user['name'] }}</div>
                    </div>
                </td>
                <td style="font-size:0.85rem;color:var(--text-muted);">{{ $user['email'] }}</td>
                <td>
                    <span class="role-badge {{ $user['role'] == 'Admin' ? 'role-admin' : ($user['role'] == 'Editor' ? 'role-editor' : '') }}">
                        {{ $user['role'] }}
                    </span>
                </td>
                <td>
                    <div class="status-indicator {{ $user['status'] == 'Active' ? 'status-active' : 'status-suspended' }}">
                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;"></span>
                        {{ $user['status'] }}
                    </div>
                </td>
                <td style="font-size:0.85rem;color:var(--text-muted);">{{ $user['last_active'] }}</td>
                <td style="text-align:right;">
                    <div style="display:flex;gap:0.25rem;justify-content:flex-end;">
                        <button class="btn-action" title="Edit Profile"><i data-lucide="edit-3" style="width:16px;height:16px;"></i></button>
                        <button class="btn-action" title="Manage Permissions"><i data-lucide="shield" style="width:16px;height:16px;"></i></button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    lucide.createIcons();
</script>
@endsection
