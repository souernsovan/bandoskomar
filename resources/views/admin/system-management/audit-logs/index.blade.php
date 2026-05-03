@extends('admin.layouts.app')

@section('title', 'Audit Log')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Audit Log</h2>
        <p>View all user activity and changes within the system</p>
    </div>
</div>

<!-- Audit Log Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-header-left">
            <form action="{{ route('system-management.audit-logs.index') }}" method="GET" class="per-page-form">
                @foreach(request()->except('per_page') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="per_page">Per page</label>
                <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
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
            <form action="{{ route('system-management.audit-logs.index') }}" method="GET" class="table-search-form">
                @foreach(request()->except('search') as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <div class="table-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="filters-panel" id="filtersPanel">
        <form action="{{ route('system-management.audit-logs.index') }}" method="GET" class="filters-form">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif

            <div class="filters-grid grid-3">
                <div class="filter-group">
                    <label for="filter_user">User</label>
                    <select name="user_id" id="filter_user" class="filter-select">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter_action">Action</label>
                    <select name="action" id="filter_action" class="filter-select">
                        <option value="">All Actions</option>
                        @foreach(\App\Models\AuditLog::actions() as $key => $label)
                            <option value="{{ $key }}" {{ request('action') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter_module">Module</label>
                    <select name="module" id="filter_module" class="filter-select">
                        <option value="">All Modules</option>
                        @foreach(\App\Models\AuditLog::modules() as $key => $label)
                            <option value="{{ $key }}" {{ request('module') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="filter_date_from">Date From</label>
                    <input type="date" name="date_from" id="filter_date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>

                <div class="filter-group">
                    <label for="filter_date_to">Date To</label>
                    <input type="date" name="date_to" id="filter_date_to" class="filter-input" value="{{ request('date_to') }}">
                </div>
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn btn-info">Apply Filters</button>
                <a href="{{ route('system-management.audit-logs.index', ['per_page' => request('per_page')]) }}" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="th-number">#</th>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Object Changed</th>
                    <th>IP Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogRows ?? $auditLogs as $index => $row)
                    @php
                        $isGroup = is_array($row);
                        $logs = $isGroup ? $row : [$row];
                        $firstLog = $logs[0];
                        $groupLabel = $isGroup ? \App\Models\AuditLog::getBatchGroupLabel($logs) : null;
                    @endphp
                    <tr>
                        <td class="td-number">{{ $auditLogs->firstItem() + $index }}</td>
                        <td>
                            <span class="datetime date-cell" data-iso="{{ $firstLog->created_at->toIso8601String() }}" data-seconds="true">{{ $firstLog->created_at->format('M d, Y H:i:s') }}</span>
                        </td>
                        <td>
                            @if($firstLog->user)
                                <div class="user-cell">
                                    <img src="{{ $firstLog->user->getAvatarUrl() }}" alt="{{ $firstLog->user->name }}" class="user-avatar-img">
                                    <div class="user-info">
                                        <div class="user-name">{{ $firstLog->user->name }}</div>
                                        <div class="user-email">{{ $firstLog->user->email }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-action-{{ $firstLog->action }}">
                                {{ $firstLog->getActionLabel() }}
                            </span>
                        </td>
                        <td>{{ $firstLog->getModuleLabel() }}</td>
                        <td>
                            @if($isGroup)
                                <span class="audit-group-label" title="{{ implode('; ', array_map(fn($l) => $l->object_changed ?? '', $logs)) }}">
                                    {{ $groupLabel }}
                                </span>
                            @else
                                {{ $firstLog->object_changed ?? '—' }}
                            @endif
                        </td>
                        <td><code class="text-sm">{{ $firstLog->ip_address ?? '—' }}</code></td>
                        <td>
                            <div class="action-buttons">
                                @if(auth()->user()->isSystem() || auth()->user()->can('audit_logs.view'))
                                <a href="{{ route('system-management.audit-logs.show', $firstLog) }}{{ $isGroup ? '?batch=1' : '' }}" class="btn-icon btn-icon-view" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                                <h3>No audit log entries found</h3>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($auditLogs->hasPages())
        <div class="table-footer">
            <div class="table-info">
                Showing {{ number_format($auditLogs->firstItem()) }} to {{ number_format($auditLogs->lastItem()) }} of {{ number_format($auditLogs->total()) }} entries
            </div>
            <div class="pagination-wrapper">
                {{ $auditLogs->withQueryString()->links('admin.vendor.pagination.custom') }}
            </div>
        </div>
    @endif
</div>
@endsection
