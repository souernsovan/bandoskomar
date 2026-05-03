@extends('layouts.admin')

@section('admin_content')
<style>
    .delete-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.45);
    }

    .delete-modal-backdrop.is-open {
        display: flex;
    }

    .delete-modal {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.24);
        padding: 1.5rem;
    }

    .delete-modal-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #fef2f2;
        color: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .delete-modal-title {
        margin: 0 0 0.35rem;
        color: #1e293b;
        font-size: 1.15rem;
        font-weight: 800;
    }

    .delete-modal-text {
        margin: 0;
        color: #64748b;
        line-height: 1.55;
        font-size: 0.95rem;
    }

    .delete-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 1.35rem;
    }

    .delete-modal-cancel,
    .delete-modal-confirm {
        border: none;
        border-radius: 10px;
        padding: 0.7rem 1rem;
        font-weight: 800;
        cursor: pointer;
    }

    .delete-modal-cancel {
        background: #f1f5f9;
        color: #334155;
    }

    .delete-modal-confirm {
        background: #dc2626;
        color: #ffffff;
    }
</style>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1e293b;">Page Management</h1>
        <p style="color: #64748b; font-weight: 500;">Manage the content of your website pages</p>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <select id="pageCategoryFilter" style="padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; background: white; color: #1e293b; font-weight: 700; min-width: 190px;">
            <option value="all">All Pages</option>
            <option value="main">Main Pages</option>
            <option value="resources">Info & Resources</option>
            <option value="get-involved">Get Involved</option>
            <option value="donation">Donation</option>
            <option value="contact">Contact</option>
        </select>
        <a href="{{ route('admin.pages.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; background: var(--primary); color: white; border-radius: 10px; text-decoration: none; font-weight: 800;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Create Page
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; color: #059669; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
        <i data-lucide="check-circle"></i> {{ session('success') }}
    </div>
@endif

<div style="background: white; border-radius: 20px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="padding: 1.25rem 1.5rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase;">Page Title</th>
                <th style="padding: 1.25rem 1.5rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase;">Category</th>
                <th style="padding: 1.25rem 1.5rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase;">Slug</th>
                <th style="padding: 1.25rem 1.5rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                <th style="padding: 1.25rem 1.5rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase;">Last Updated</th>
                <th style="padding: 1.25rem 1.5rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
            @php
                $categoryLabels = [
                    'main' => 'Main Pages',
                    'resources' => 'Info & Resources',
                    'get-involved' => 'Get Involved',
                    'donation' => 'Donation',
                    'contact' => 'Contact',
                ];
                $categoryKey = $page->page_category ?? 'main';
                $categoryLabel = $categoryLabels[$categoryKey] ?? 'Main Pages';
            @endphp
            <tr data-page-category="{{ $categoryKey }}" style="border-bottom: 1px solid #e2e8f0; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1.25rem 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 35px; height: 35px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                            <i data-lucide="{{ $page->icon ?? 'file' }}" style="width: 18px; height: 18px;"></i>
                        </div>
                        <span style="font-weight: 700; color: #1e293b;">{{ $page->title }}</span>
                    </div>
                </td>
                <td style="padding: 1.25rem 1.5rem;">
                    <span style="padding: 0.4rem 0.8rem; border-radius: 999px; background: #fff7ed; color: #c2410c; font-size: 0.75rem; font-weight: 800;">
                        {{ $categoryLabel }}
                    </span>
                </td>
                <td style="padding: 1.25rem 1.5rem; color: #64748b; font-family: monospace; font-size: 0.9rem;">/{{ $page->slug }}</td>
                <td style="padding: 1.25rem 1.5rem;">
                    <span style="padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; {{ $page->status === 'published' ? 'background: #ecfdf5; color: #059669;' : 'background: #f1f5f9; color: #64748b;' }}">
                        {{ ucfirst($page->status) }}
                    </span>
                </td>
                <td style="padding: 1.25rem 1.5rem; color: #64748b; font-size: 0.9rem;">{{ $page->updated_at->format('M d, Y') }}</td>
                <td style="padding: 1.25rem 1.5rem; text-align: right;">
                    <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                        <a href="{{ route('admin.pages.edit', $page) }}" title="Edit page" aria-label="Edit {{ $page->title }}" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #1e293b; text-decoration: none; border-radius: 10px; transition: all 0.2s;">
                            <i data-lucide="edit-3" style="width: 17px; height: 17px;"></i>
                        </a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="delete-page-btn" data-page-title="{{ $page->title }}" title="Delete page" aria-label="Delete {{ $page->title }}" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: #fef2f2; color: #dc2626; border: none; border-radius: 10px; cursor: pointer; transition: all 0.2s;">
                                <i data-lucide="trash-2" style="width: 17px; height: 17px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="delete-modal-backdrop" id="deletePageModal" aria-hidden="true">
    <div class="delete-modal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="delete-modal-icon">
            <i data-lucide="trash-2" style="width: 22px; height: 22px;"></i>
        </div>
        <h2 class="delete-modal-title" id="deleteModalTitle">Delete page?</h2>
        <p class="delete-modal-text">
            Are you sure you want to delete <strong id="deletePageName">this page</strong>? This action cannot be undone.
        </p>
        <div class="delete-modal-actions">
            <button type="button" class="delete-modal-cancel" id="cancelDeletePage">Cancel</button>
            <button type="button" class="delete-modal-confirm" id="confirmDeletePage">Delete</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filter = document.getElementById('pageCategoryFilter');
        const rows = document.querySelectorAll('[data-page-category]');
        const deleteModal = document.getElementById('deletePageModal');
        const deletePageName = document.getElementById('deletePageName');
        const cancelDeletePage = document.getElementById('cancelDeletePage');
        const confirmDeletePage = document.getElementById('confirmDeletePage');
        let pendingDeleteForm = null;

        filter.addEventListener('change', () => {
            rows.forEach((row) => {
                row.style.display = filter.value === 'all' || row.dataset.pageCategory === filter.value ? '' : 'none';
            });
        });

        document.querySelectorAll('.delete-page-btn').forEach((button) => {
            button.addEventListener('click', () => {
                pendingDeleteForm = button.closest('form');
                deletePageName.textContent = button.dataset.pageTitle || 'this page';
                deleteModal.classList.add('is-open');
                deleteModal.setAttribute('aria-hidden', 'false');
            });
        });

        const closeDeleteModal = () => {
            deleteModal.classList.remove('is-open');
            deleteModal.setAttribute('aria-hidden', 'true');
            pendingDeleteForm = null;
        };

        cancelDeletePage.addEventListener('click', closeDeleteModal);
        deleteModal.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        confirmDeletePage.addEventListener('click', () => {
            if (pendingDeleteForm) {
                pendingDeleteForm.submit();
            }
        });
    });
</script>
@endsection
