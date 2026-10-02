<style>
    .sub-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 16px;
    }

    .sub-chip {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        padding: 7px 14px;
        border-radius: 999px;
        border: 1px solid var(--border, #e2e8f0);
        font-size: 14px;
        font-weight: 500;
        color: inherit;
        text-decoration: none;
    }

    .sub-chip span {
        font-size: 12px;
        background: rgba(100, 116, 139, .15);
        border-radius: 999px;
        padding: 1px 8px;
    }

    .sub-chip.active {
        background: #1E2A6B;
        border-color: #1E2A6B;
        color: #fff;
    }

    .sub-chip.active span {
        background: rgba(255, 255, 255, .2);
    }

    .sub-total {
        margin-left: auto;
        font-size: 14px;
    }

    .sub-muted {
        color: var(--text-secondary, #64748b);
    }

    .sub-row-new td:first-child {
        box-shadow: inset 3px 0 0 #F7861F;
    }

    .sub-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(100, 116, 139, .15);
        white-space: nowrap;
    }

    .sub-badge-new {
        background: #FFF1E3;
        color: #C2620A;
    }

    .sub-badge-received,
    .sub-badge-hired,
    .sub-badge-accepted,
    .sub-badge-replied {
        background: #E7F5EC;
        color: #1D6B3A;
    }

    .sub-badge-shortlisted,
    .sub-badge-reviewing,
    .sub-badge-contacted {
        background: #E8EDFB;
        color: #1E2A6B;
    }

    .sub-badge-rejected,
    .sub-badge-cancelled,
    .sub-badge-declined {
        background: #F6E6E6;
        color: #9A2C2C;
    }

    .sub-detail {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 20px;
        align-items: start;
    }

    .sub-fields {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px 24px;
    }

    .sub-fields div {
        display: grid;
        gap: 2px;
    }

    .sub-fields span {
        font-size: 13px;
        color: var(--text-secondary, #64748b);
    }

    .sub-fields strong {
        font-weight: 600;
        word-break: break-word;
    }

    .sub-heading {
        font-size: 16px;
        font-weight: 700;
        margin: 24px 0 10px;
    }

    .sub-message {
        background: rgba(100, 116, 139, .08);
        border-radius: 10px;
        padding: 16px;
        line-height: 1.7;
        white-space: normal;
    }

    .sub-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    @media (max-width: 960px) {
        .sub-detail {
            grid-template-columns: 1fr;
        }
    }
</style>