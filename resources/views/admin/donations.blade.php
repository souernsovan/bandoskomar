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
    .pagination-wrap { padding: 1rem 1.5rem; border-top: 1px solid var(--border); }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); background: #f8fafc; border-bottom: 1px solid var(--border); }
    .data-table td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #fafafa; }

    .donor-info { display: flex; align-items: center; gap: 0.85rem; }
    .donor-initials { width: 36px; height: 36px; border-radius: 10px; background: #fff7ed; color: #ea580c; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; border: 1px solid #fed7aa; }

    .status-badge { padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; }
    .status-completed { background: #dcfce7; color: #15803d; }
    .status-pending { background: #fffbeb; color: #b45309; }
    .status-failed { background: #fee2e2; color: #b91c1c; }

    .amount-display { font-weight: 800; font-size: 0.95rem; color: #1e293b; }
    
    .btn-action { background: none; border: none; cursor: pointer; padding: 0.35rem; border-radius: 6px; color: var(--text-muted); transition: all 0.15s; display: inline-flex; }
    .btn-action:hover { background: #f1f5f9; color: var(--text-main); }
</style>
@endsection

@section('admin_content')
<div class="page-header">
    <div>
        <h1 class="page-title">Donations Log</h1>
        <p class="page-subtitle">Detailed tracking of all financial contributions to Bandos Komar projects.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Recent Contributions <span style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-left:0.5rem;">{{ $donations->total() }} entries</span></span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Donor</th>
                <th>Project/Allocation</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
            <tr>
                <td>
                    <div class="donor-info">
                        <div class="donor-initials">{{ $donation['initials'] }}</div>
                        <div>
                            <div style="font-weight:600;color:var(--text-main);font-size:0.9rem;">{{ $donation['donor'] }}</div>
                            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem;">{{ $donation['email'] }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:0.85rem;color:var(--text-muted);font-weight:600;">{{ $donation['project'] }}</td>
                <td>
                    <div class="amount-display">{{ $donation['amount'] }}</div>
                </td>
                <td style="font-size:0.85rem;color:var(--text-muted);">{{ $donation['date'] }}</td>
                <td>
                    <span class="status-badge {{ strtolower($donation['status']) == 'completed' ? 'status-completed' : (strtolower($donation['status']) == 'pending' ? 'status-pending' : 'status-failed') }}">
                        {{ $donation['status'] }}
                    </span>
                </td>
                <td style="text-align:right;">
                    <div style="display:flex;gap:0.25rem;justify-content:flex-end;">
                        <button class="btn-action" title="View Transaction Detail"><i data-lucide="external-link" style="width:16px;height:16px;"></i></button>
                        <button class="btn-action" title="Print Receipt"><i data-lucide="printer" style="width:16px;height:16px;"></i></button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted); font-weight: 600;">
                    No donations found yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($donations->hasPages())
        <div class="pagination-wrap">
            {{ $donations->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    lucide.createIcons();
</script>
@endsection
