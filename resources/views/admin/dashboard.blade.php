@extends('admin.layouts.app')

@section('title', 'Dashboard')

@php
    use App\Models\FormSubmission;

    $money = fn ($v) => '$' . number_format((float) $v, 2);
@endphp

@section('content')
@include('admin.submissions.partials.styles')
<style>
    .dash-filter { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 20px; }
    .dash-seg { display: inline-flex; border: 1px solid var(--border, #e2e8f0); border-radius: 10px; overflow: hidden; }
    .dash-seg a { padding: 8px 16px; font-size: 14px; font-weight: 500; color: inherit; text-decoration: none; }
    .dash-seg a + a { border-left: 1px solid var(--border, #e2e8f0); }
    .dash-seg a.active { background: #1E2A6B; color: #fff; }
    .dash-filter form { display: flex; gap: 8px; align-items: center; }
    .dash-filter input, .dash-filter select { padding: 7px 10px; border-radius: 8px; border: 1px solid var(--border, #e2e8f0); background: transparent; color: inherit; font: inherit; }
    .dash-range { margin-left: auto; font-weight: 600; }
    .dash-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 20px; }
    .dash-card { border: 1px solid var(--border, #e2e8f0); border-radius: 14px; padding: 18px; }
    .dash-card span { display: block; font-size: 13px; color: var(--text-secondary, #64748b); }
    .dash-card strong { display: block; font-size: 26px; margin-top: 4px; }
    .dash-card.accent { background: #FFF4EA; border-color: #F7D3AE; color: #1A1F3A; }
    .dash-card.accent span { color: #8A4A0A; }
    .dash-charts { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px; }
    .dash-panel { border: 1px solid var(--border, #e2e8f0); border-radius: 14px; padding: 20px; min-width: 0; }
    .dash-panel h3 { font-size: 16px; font-weight: 700; margin: 0 0 14px; }
    .dash-chart-box { position: relative; height: 320px; }
    .dash-empty { display: grid; place-items: center; height: 100%; color: var(--text-secondary, #64748b); text-align: center; }
    .dash-quick { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
    .dash-quick a { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border: 1px solid var(--border, #e2e8f0); border-radius: 12px; color: inherit; text-decoration: none; }
    .dash-quick a:hover { border-color: #F7861F; }
    @media (max-width: 1100px) { .dash-charts { grid-template-columns: 1fr; } .dash-range { margin-left: 0; width: 100%; } }
</style>

<div class="welcome-banner" style="margin-bottom: 24px;">
    <div class="welcome-content">
        <h2 class="welcome-title">Welcome back, {{ Auth::user()->name ?? 'Admin' }}! 🎉</h2>
    </div>
</div>

@can('submissions.view')
    {{-- New messages waiting --}}
    <div class="dash-panel" style="margin-bottom:20px">
        <h3>Waiting for you</h3>
        <div class="dash-quick">
            @foreach (FormSubmission::TYPES as $type => $labels)
                <a href="{{ route('admin.submissions.index', ['type' => $type, 'status' => 'new']) }}">
                    <span>{{ $labels[0] }}</span>
                    <span class="sub-badge {{ ($newCounts[$type] ?? 0) > 0 ? 'sub-badge-new' : '' }}">{{ $newCounts[$type] ?? 0 }} new</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Donations --}}
    <div class="dash-filter">
        <div class="dash-seg">
            @foreach (['week' => 'Week', 'month' => 'Month', 'year' => 'Year', 'all' => 'Year by year'] as $value => $label)
                <a href="{{ route('dashboard', ['period' => $value]) }}" class="{{ $period === $value ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>
        @if ($period === 'month')
            <form method="GET" action="{{ route('dashboard') }}">
                <input type="hidden" name="period" value="month">
                <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()">
            </form>
        @elseif ($period === 'year')
            <form method="GET" action="{{ route('dashboard') }}">
                <input type="hidden" name="period" value="year">
                <select name="year" onchange="this.form.submit()">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        @endif
        <span class="dash-range">Donations · {{ $rangeLabel }}</span>
    </div>

    <div class="dash-cards">
        <div class="dash-card accent"><span>Total pledged</span><strong>{{ $money($stats['total']) }}</strong></div>
        <div class="dash-card"><span>Number of donations</span><strong>{{ $stats['count'] }}</strong></div>
        <div class="dash-card"><span>Average donation</span><strong>{{ $money($stats['average']) }}</strong></div>
        <div class="dash-card"><span>Marked as received</span><strong>{{ $money($stats['received']) }}</strong></div>
        <div class="dash-card"><span>All-time total</span><strong>{{ $money($stats['allTime']) }}</strong></div>
    </div>

    <div class="dash-charts">
        <div class="dash-panel">
            <h3>Donations over time</h3>
            <div class="dash-chart-box">
                @if ($stats['count'])
                    <canvas id="donationBar" aria-label="Donations over time"></canvas>
                @else
                    <div class="dash-empty">No donations in this period yet.</div>
                @endif
            </div>
        </div>
        <div class="dash-panel">
            <h3>Donations by amount</h3>
            <div class="dash-chart-box">
                @if ($stats['count'])
                    <canvas id="donationPie" aria-label="Donations by amount"></canvas>
                @else
                    <div class="dash-empty">No donations in this period yet.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-header-left"><strong>Latest donations · {{ $rangeLabel }}</strong></div>
            <div class="table-header-right">
                <a href="{{ route('admin.submissions.index', 'donate') }}" class="btn btn-secondary">See all donations</a>
            </div>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Amount</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($recentDonations as $donation)
                        <tr>
                            <td>{{ $donation->created_at->format('M d, Y') }}</td>
                            <td>{{ $donation->name }}</td>
                            <td>{{ $donation->email }}</td>
                            <td><strong>{{ $money($donation->amount) }}</strong></td>
                            <td><span class="sub-badge sub-badge-{{ $donation->status }}">{{ FormSubmission::statusesFor('donate')[$donation->status] ?? $donation->status }}</span></td>
                            <td><a href="{{ route('admin.submissions.show', ['donate', $donation->id]) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><h3>No donations in this period yet</h3></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endcan
@endsection

@push('scripts')
@can('submissions.view')
@if ($stats['count'])
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(() => {
    const data = @json($chart);
    const dark = document.documentElement.classList.contains('dark');
    const text = dark ? '#e4e4e7' : '#334155';
    const grid = dark ? 'rgba(255,255,255,.08)' : 'rgba(15,23,42,.08)';
    const money = v => '$' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    Chart.defaults.color = text;
    Chart.defaults.font.family = 'Plus Jakarta Sans, system-ui, sans-serif';

    new Chart(document.getElementById('donationBar'), {
        data: {
            labels: data.labels,
            datasets: [
                { type: 'bar', label: 'Amount (USD)', data: data.amounts, backgroundColor: '#F7861F', borderRadius: 6, yAxisID: 'y', order: 2 },
                { type: 'line', label: 'Number of donations', data: data.counts, borderColor: '#1E2A6B', backgroundColor: '#1E2A6B',
                  tension: .3, pointRadius: 3, yAxisID: 'y1', order: 1 },
            ],
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { tooltip: { callbacks: { label: c => c.dataset.yAxisID === 'y' ? ' ' + money(c.raw) : ' ' + c.raw + ' donation(s)' } } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: grid }, ticks: { callback: money } },
                y1: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: { precision: 0 } },
            },
        },
    });

    new Chart(document.getElementById('donationPie'), {
        type: 'doughnut',
        data: {
            labels: data.pieLabels,
            datasets: [{ data: data.pieAmounts, backgroundColor: ['#FFD3A8', '#F7861F', '#DD6F0B', '#3B1FA8', '#1E2A6B'], borderWidth: 0 }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '55%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: { callbacks: { label: c => ' ' + money(c.raw) + ' · ' + data.pieCounts[c.dataIndex] + ' donation(s)' } },
            },
        },
    });
})();
</script>
@endif
@endcan
@endpush
