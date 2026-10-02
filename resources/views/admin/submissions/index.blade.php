@extends('admin.layouts.app')

@php
    use App\Models\FormSubmission;

    $title = FormSubmission::typeLabel($type);
    $subjectLabel = FormSubmission::TYPES[$type][2];
    $keep = fn (array $except = []) => collect(request()->only(['search', 'status', 'from', 'to', 'per_page']))
        ->except($except)->filter(fn ($v) => $v !== null && $v !== '');
@endphp

@section('title', $title)

@section('content')
    @include('admin.submissions.partials.styles')
    @include('admin.submissions.partials.alerts')

    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $title }}</h2>
            <p>Sent from the website {{ strtolower(FormSubmission::typeLabel($type, false)) }} form</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.submissions.index', ['type' => $type] + $keep()->all() + ['export' => 'csv']) }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    <div class="sub-chips">
        <a href="{{ route('admin.submissions.index', ['type' => $type] + $keep(['status'])->all()) }}" class="sub-chip {{ request('status') ? '' : 'active' }}">
            All <span>{{ $statusCounts->sum() }}</span>
        </a>
        @foreach ($statuses as $value => $label)
            <a href="{{ route('admin.submissions.index', ['type' => $type, 'status' => $value] + $keep(['status'])->all()) }}"
                class="sub-chip {{ request('status') === $value ? 'active' : '' }}">
                {{ $label }} <span>{{ $statusCounts[$value] ?? 0 }}</span>
            </a>
        @endforeach
        @if ($totalAmount !== null)
            <span class="sub-total">Total in this list: <strong>${{ number_format($totalAmount, 2) }}</strong></span>
        @endif
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-header-left">
                <form action="{{ route('admin.submissions.index', $type) }}" method="GET" class="per-page-form">
                    @foreach ($keep(['per_page']) as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page">Per page</label>
                    <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                        @foreach ([25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected($perPage == $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </form>
                <button type="button" class="btn-filter" onclick="toggleFilters()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    <span>Filters</span>
                </button>
            </div>
            <div class="table-header-right">
                <form action="{{ route('admin.submissions.index', $type) }}" method="GET" class="table-search-form">
                    @foreach ($keep(['search']) as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                    <div class="table-search-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="text" name="search" placeholder="Search name, email, phone..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>

        <div class="filters-panel {{ request()->hasAny(['from', 'to']) ? 'open' : '' }}" id="filtersPanel">
            <form action="{{ route('admin.submissions.index', $type) }}" method="GET" class="filters-form">
                @foreach ($keep(['status', 'from', 'to']) as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <div class="filters-grid grid-3">
                    <div class="filter-group">
                        <label for="filter_status">Status</label>
                        <select name="status" id="filter_status" class="filter-select">
                            <option value="">All</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter_from">From</label>
                        <input type="date" name="from" id="filter_from" class="filter-select" value="{{ request('from') }}">
                    </div>
                    <div class="filter-group">
                        <label for="filter_to">To</label>
                        <input type="date" name="to" id="filter_to" class="filter-select" value="{{ request('to') }}">
                    </div>
                </div>
                <div class="filters-actions">
                    <button type="submit" class="btn btn-info">Apply Filters</button>
                    <a href="{{ route('admin.submissions.index', $type) }}" class="btn btn-secondary">Clear Filters</a>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="th-number">#</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Contact</th>
                        @if ($type === 'donate')<th>Amount</th>@endif
                        <th>{{ $subjectLabel }}</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $index => $submission)
                        <tr class="{{ $submission->status === 'new' ? 'sub-row-new' : '' }}">
                            <td class="td-number">{{ $submissions->firstItem() + $index }}</td>
                            <td>{{ $submission->created_at?->format('M d, Y') }}<br><small class="sub-muted">{{ $submission->created_at?->format('H:i') }}</small></td>
                            <td><strong>{{ $submission->name }}</strong></td>
                            <td>
                                <a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a>
                                @if ($submission->phone)<br><small class="sub-muted">{{ $submission->phone }}</small>@endif
                            </td>
                            @if ($type === 'donate')<td><strong>${{ number_format((float) $submission->amount, 2) }}</strong></td>@endif
                            <td>
                                {{ \Illuminate\Support\Str::limit($submission->subject ?: $submission->message ?: '—', 50) }}
                                @if ($submission->attachment_path)<br><small class="sub-muted">📎 CV attached</small>@endif
                            </td>
                            <td><span class="sub-badge sub-badge-{{ $submission->status }}">{{ $submission->statusLabel() }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.submissions.show', [$type, $submission]) }}" class="btn-icon btn-icon-view" title="Open">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    @can('submissions.delete')
                                        <form action="{{ route('admin.submissions.destroy', [$type, $submission]) }}" method="POST" class="inline-form"
                                            data-confirm-delete="Delete this {{ strtolower(FormSubmission::typeLabel($type, false)) }}?"
                                            data-delete-title="Delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $type === 'donate' ? 8 : 7 }}">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                                    </svg>
                                    <h3>Nothing here yet</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($submissions->hasPages())
            <div class="table-footer">
                <div class="table-info">
                    Showing {{ $submissions->firstItem() }} to {{ $submissions->lastItem() }} of {{ $submissions->total() }}
                </div>
                <div class="pagination-wrapper">
                    {{ $submissions->withQueryString()->links('admin.vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>
@endsection
