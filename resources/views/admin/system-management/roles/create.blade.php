@extends('admin.layouts.app')

@section('title', 'Create Role')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Create New Role</h2>
            <p>Create a new role and configure permissions</p>
        </div>
        <div class="page-header-right">
            <a href="{{ route('system-management.roles.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.roles.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="name" class="form-label">Role Name <span class="form-required">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    placeholder="e.g. staff, manager, viewer"
                    class="form-input @error('name') error @enderror">
                <span class="form-hint">Use lowercase letters, numbers, and underscores only (e.g. staff, content_manager)</span>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            @foreach ($permissionGroups as $groupName => $permissions)
                @php
                    $groupSlug = preg_replace('/[^a-z0-9]/', '-', $groupName);
                    $permKeys = array_keys($permissions);
                    $allChecked = count(array_intersect($permKeys, $rolePermissions)) === count($permKeys);
                @endphp
                <fieldset class="permission-fieldset" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border: 1px solid var(--border, #e2e8f0); border-radius: 8px;">
                    <legend class="permission-legend" style="font-weight: 600; padding: 0 0.5rem; font-size: 1rem;">
                        {{ ucfirst(str_replace('_', ' ', $groupName)) }}
                    </legend>
                    <label class="check-all-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0.75rem; color: var(--info, #0d6efd);">
                        <input type="checkbox" class="permission-check-all" data-group="{{ $groupSlug }}"
                            {{ $allChecked ? 'checked' : '' }}>
                        <span>Check all</span>
                    </label>
                    <div class="permission-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem;">
                        @foreach ($permissions as $permKey => $permLabel)
                            <label class="checkbox-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="permissions[]" value="{{ $permKey }}" class="permission-item" data-group="{{ $groupSlug }}"
                                    {{ in_array($permKey, $rolePermissions) ? 'checked' : '' }}>
                                <span>{{ $permLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <div class="form-actions" style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border, #e2e8f0);">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add
                </button>
                <a href="{{ route('system-management.roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection
