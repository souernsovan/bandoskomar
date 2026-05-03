<style>
    .page-editor-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .page-editor-title {
        margin: 0;
        color: #1e293b;
        font-size: 1.75rem;
        font-weight: 800;
    }

    .page-editor-subtitle {
        margin: 0.35rem 0 0;
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .page-editor-back {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #475569;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .page-editor-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        align-items: start;
    }

    .editor-stack {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .editor-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.35rem;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .editor-panel.sticky {
        position: static;
    }

    .editor-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0 1rem;
        color: #1e293b;
        font-size: 1rem;
        font-weight: 800;
    }

    .editor-section-title i {
        width: 18px;
        height: 18px;
        color: var(--primary);
    }

    .editor-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .editor-field {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .editor-field.full {
        grid-column: 1 / -1;
    }

    .editor-label {
        color: #334155;
        font-size: 0.85rem;
        font-weight: 800;
    }

    .editor-help {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.45;
    }

    .editor-input,
    .editor-textarea,
    .editor-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0.75rem 0.85rem;
        color: #1e293b;
        background: #ffffff;
        font: inherit;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .editor-input:focus,
    .editor-textarea:focus,
    .editor-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(246, 139, 30, 0.14);
    }

    .editor-textarea {
        min-height: 120px;
        resize: vertical;
        line-height: 1.6;
    }

    .editor-textarea.tall {
        min-height: 220px;
    }

    .repeat-list {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .repeat-item {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .repeat-item-title {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
    }

    .advanced-box {
        border-top: 1px solid #e2e8f0;
        margin-top: 1rem;
        padding-top: 1rem;
    }

    .advanced-box summary {
        cursor: pointer;
        color: #475569;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .editor-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .editor-primary-btn,
    .editor-secondary-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        border-radius: 10px;
        padding: 0.78rem 1rem;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .editor-primary-btn {
        border: none;
        background: var(--primary);
        color: #ffffff;
    }

    .editor-secondary-btn {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
    }

    .editor-alert {
        margin-bottom: 1rem;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .editor-alert.error {
        background: #fef2f2;
        color: #b91c1c;
    }

    .editor-alert.success {
        background: #ecfdf5;
        color: #047857;
    }

    .editor-error {
        color: #dc2626;
        font-size: 0.8rem;
        font-weight: 700;
    }

    @media (max-width: 640px) {
        .page-editor-header {
            flex-direction: column;
        }

        .editor-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
