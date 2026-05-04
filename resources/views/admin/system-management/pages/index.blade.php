@extends('admin.layouts.app')

@section('title', 'Website Pages')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span class="alert-content">{{ session('success') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span class="alert-content">{{ session('error') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <div class="page-header">
        <div class="page-header-left">
            <h2>Website Pages</h2>
            <p>Manage website pages, route mappings, and SEO settings</p>
        </div>
        <div class="page-header-right">
            @can('pages.edit')
            <a href="{{ route('system-management.pages.create') }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add
            </a>
            @endcan
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-header-left">
                <form action="{{ route('system-management.pages.index') }}" method="GET" class="per-page-form">
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if (request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <label for="per_page">Per page</label>
                    <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
            <div class="table-header-right">
                <form action="{{ route('system-management.pages.index') }}" method="GET" class="table-search-form">
                    @if (request('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
                    <div class="table-search-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="th-number">#</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Menu</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $index => $page)
                        <tr>
                            <td class="td-number">{{ $pages->firstItem() + $index }}</td>
                            <td>{{ $page->title }}</td>
                            <td><code>{{ $page->slug }}</code></td>
                           
                            @php
                                $menuGroup = $page->getMenuGroup();
                                $menuBadgeClass = $menuGroup === 'main' ? 'status-active' : ($menuGroup === 'hidden' ? 'status-inactive' : 'role-staff');
                            @endphp
                            <td>
                                <span class="badge {{ $menuBadgeClass }}">{{ $page->getMenuGroupLabel() }}</span>
                            </td>
                            <td><span class="badge role-staff">{{ $page->sort_order }}</span></td>
                            <td>
                                <span class="badge {{ $page->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $page->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @can('pages.view')
                                    <a href="{{ route('system-management.pages.show', $page) }}" class="btn-icon btn-icon-view" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('pages.edit')
                                    <a href="{{ route('system-management.pages.edit', $page) }}" class="btn-icon btn-icon-edit" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    @endcan
                                    @can('pages.edit')
                                    <form action="{{ route('system-management.pages.destroy', $page) }}" method="POST" class="inline-form"
                                        data-confirm-delete="Are you sure you want to delete this page?"
                                        data-delete-title="Delete Page">
                                        @csrf
                                        {{-- @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button> --}}
                                    </form>
                                    @endcan
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
                                    <h3>No pages found</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pages->hasPages())
            <div class="table-footer">
                <div class="table-info">
                    Showing {{ $pages->firstItem() }} to {{ $pages->lastItem() }} of {{ $pages->total() }} pages
                </div>
                <div class="pagination-wrapper">
                    {{ $pages->withQueryString()->links('admin.vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>
@endsection
