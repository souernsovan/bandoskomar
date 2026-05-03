@extends('admin.layouts.app')

@section('title', 'Role: ' . ucfirst($role->name))

@section('breadcrumb')
    <a href="{{ route('system-management.roles.index') }}" class="breadcrumb-item">Roles & Permissions</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ ucfirst($role->name) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Role Details</h2>
            <p>View role and its assigned permissions</p>
        </div>

        <div class="page-header-right">
            @can('roles.edit')
            <a href="{{ route('system-management.roles.edit', $role->name) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            @endcan
            <a href="{{ route('system-management.roles.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-header">
            <div class="banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ ucfirst($role->name) }}</h3>
                <p>{{ $role->permissions->count() }} permission{{ $role->permissions->count() !== 1 ? 's' : '' }} assigned</p>
            </div>
        </div>

        <div class="detail-body">
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Role name</label>
                        <p><span class="badge role-{{ $role->name }}">{{ ucfirst($role->name) }}</span></p>
                    </div>
                    <div class="detail-item">
                        <label>Guard</label>
                        <p><code>{{ $role->guard_name }}</code></p>
                    </div>
                    <div class="detail-item">
                        <label>Permissions count</label>
                        <p>{{ $role->permissions->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">Assigned Permissions</h4>
                @if ($role->permissions->isEmpty())
                    <p class="text-muted">No permissions assigned to this role.</p>
                @else
                    @foreach ($permissionGroups as $groupKey => $groupPermissions)
                        @php
                            $assignedInGroup = array_intersect(array_keys($groupPermissions), $rolePermissionNames);
                        @endphp
                        @if (!empty($assignedInGroup))
                            <div class="permission-group-block" style="margin-bottom: 1.5rem;">
                                <h5 class="detail-section-subtitle" style="font-size: 0.95rem; margin-bottom: 0.75rem; color: #4b5563;">
                                    {{ ucfirst(str_replace('_', ' ', $groupKey)) }}
                                </h5>
                                <ul class="permission-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                    @foreach ($assignedInGroup as $permKey)
                                        <li>
                                            <span class="badge text-blue bg-blue">{{ $groupPermissions[$permKey] ?? $permKey }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <p>{{ $role->created_at?->format('F d, Y \a\t h:i A') ?? '-' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <p>{{ $role->updated_at?->format('F d, Y \a\t h:i A') ?? '-' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Role ID</label>
                        <span class="uuid-cell">{{ $role->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
