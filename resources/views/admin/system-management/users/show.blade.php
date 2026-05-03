@extends('admin.layouts.app')

@section('title', 'User Details')

@section('breadcrumb')
    <a href="{{ route('system-management.users.index') }}" class="breadcrumb-item">Users</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">User Details</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>User Details</h2>
            <p>View user details</p>
        </div>

        <div class="page-header-right">
            @can('users.edit')
                <a href="{{ route('system-management.users.edit', $user) }}" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Edit
                </a>
            @endcan
            <a href="{{ route('system-management.users.index') }}" class="btn btn-secondary">
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
            <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="detail-avatar-img">
            <div class="detail-header-info">
                <h3>{{ $user->name }}</h3>
                <p>{{ $user->email }}</p>
            </div>
        </div>

        <div class="detail-body">
            <div class="detail-section">
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Full Name</label>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Email Address</label>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="detail-item">
                        <label>Role</label>
                        @php
                            $roleBadge = in_array($user->role, ['system', 'admin', 'staff', 'user'])
                                ? "role-{$user->role}"
                                : 'role-default';
                        @endphp
                        <span class="badge {{ $roleBadge }}">{{ $user->getRoleDisplayName() }}</span>
                    </div>

                    <div class="detail-item">
                        <label>Status</label>
                        @if ($user->isActive())
                            <span class="badge status-active">Active</span>
                        @else
                            <span class="badge status-inactive">Inactive</span>
                        @endif
                    </div>
                    <div class="detail-item">
                        <label>Protection Status</label>
                        @if ($user->isSystem())
                            <span class="badge badge-protected">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                                Protected
                            </span>
                        @else
                            <span class="badge text-blue bg-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                                    </path>
                                </svg>
                                Standard user
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">
                    Metadata
                </h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <p class="datetime" data-iso="{{ $user->created_at->toIso8601String() }}">
                            {{ $user->created_at->format('F d, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <p class="datetime" data-iso="{{ $user->updated_at->toIso8601String() }}">
                            {{ $user->updated_at->format('F d, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <div class="detail-item">
                        <label>User ID</label>
                        <span class="uuid-cell">{{ $user->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
