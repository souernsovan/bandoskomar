<style>
    .sc-editor .lang-panel:not(.active) { display: none; }
    .sc-note { background: rgba(247, 134, 31, .1); border-left: 3px solid #F7861F; padding: 10px 14px; border-radius: 8px; margin: 0 0 16px; font-size: 14px; }
    .sc-errors { background: rgba(220, 38, 38, .08); border: 1px solid rgba(220, 38, 38, .35); color: #b91c1c; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 14px; }
    .sc-section { border: 1px solid var(--border, #e2e8f0); border-radius: 12px; margin-bottom: 14px; overflow: hidden; }
    .sc-section > summary { cursor: pointer; padding: 14px 18px; font-weight: 600; list-style: none; display: flex; justify-content: space-between; align-items: center; }
    .sc-section > summary::-webkit-details-marker, .sc-item > summary::-webkit-details-marker { display: none; }
    .sc-section > summary::after { content: "▾"; transition: transform .2s; opacity: .6; }
    .sc-section:not([open]) > summary::after { transform: rotate(-90deg); }
    .sc-section-body { padding: 4px 18px 18px; display: grid; gap: 4px; }
    .sc-field { margin-bottom: 10px; }
    .sc-field .form-textarea { min-height: 0; }
    .sc-media { display: grid; grid-template-columns: 120px 1fr; gap: 12px; align-items: start; }
    .sc-media-preview { width: 120px; height: 84px; border-radius: 8px; border: 1px dashed var(--border, #e2e8f0); display: grid; place-items: center; overflow: hidden; font-size: 12px; color: var(--text-secondary, #64748b); text-align: center; }
    .sc-media-preview img { width: 100%; height: 100%; object-fit: cover; }
    .sc-media-inputs { display: grid; gap: 8px; min-width: 0; }
    .sc-media-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; font-size: 13px; }
    .sc-media-actions input[type=file] { max-width: 100%; }
    .sc-btn-sm { padding: 6px 12px !important; font-size: 13px !important; }
    .sc-list { border-top: 1px dashed var(--border, #e2e8f0); padding-top: 12px; margin: 6px 0 12px; }
    .sc-list-head { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .sc-list-head .form-label { margin: 0; }
    .sc-list-count { font-size: 12px; background: var(--border, #e2e8f0); border-radius: 999px; padding: 1px 9px; }
    .sc-items { display: grid; gap: 8px; }
    .sc-item { border: 1px solid var(--border, #e2e8f0); border-radius: 10px; }
    .sc-item > summary { cursor: pointer; list-style: none; display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 8px 12px; }
    .sc-item-title { display: flex; align-items: center; gap: 8px; min-width: 0; font-weight: 600; font-size: 14px; }
    .sc-item-thumb { width: 34px; height: 26px; object-fit: cover; border-radius: 4px; }
    .sc-item-summary { font-weight: 400; color: var(--text-secondary, #64748b); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sc-item-tools { display: flex; gap: 4px; flex-shrink: 0; }
    .sc-icon-btn { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--border, #e2e8f0); background: transparent; color: inherit; cursor: pointer; }
    .sc-icon-btn:hover { background: rgba(100, 116, 139, .12); }
    .sc-danger:hover { background: rgba(220, 38, 38, .12); color: #dc2626; }
    .sc-item-body { padding: 4px 12px 12px; display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 0 14px; }
    .sc-item-body .sc-field-textarea, .sc-item-body .sc-field-rich, .sc-item-body .sc-field-image, .sc-item-body .sc-field-file { grid-column: 1 / -1; }
    .sc-list-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
    .sc-bulk { cursor: pointer; }
    @media (max-width: 640px) { .sc-media { grid-template-columns: 1fr; } }
</style>
