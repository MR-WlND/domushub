@push('styles')
<style>
    body.resident-page {
        background-color: #f9fafb;
    }

    .pay-dashboard {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 40px;
        font-family: var(--font-family, 'Inter', sans-serif);
    }

    .pay-dashboard__header {
        /* using flex gap instead */
    }

    .pay-page__title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--color-primary, #00236f);
        margin: 0 0 6px 0;
    }

    .pay-page__subtitle {
        font-size: 0.9rem;
        color: var(--color-text-secondary, #444651);
        margin: 0;
    }

    /* Alerts */
    .pay-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: var(--radius, 8px);
        margin-bottom: 24px;
        font-size: 0.95rem;
        font-weight: 500;
    }
    .pay-alert--success { background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .pay-alert--error { background-color: #fef2f2; border: 1px solid #fecaca; color: var(--color-error, #ba1a1a); }

    /* Grid Layout */
    .pay-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        align-items: start;
    }

    /* Cards */
    .pay-card {
        background: var(--color-card, #fff);
        border: 1px solid #f1f5f9;
        border-radius: var(--radius-lg, 12px);
        box-shadow: 0px 4px 20px rgba(0, 35, 111, 0.03);
        overflow: hidden;
    }

    .pay-card__header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .flex-between {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pay-card__title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
        margin: 0;
    }

    /* Checkbox */
    .pay-checkbox-select-all, .pay-checkbox-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }

    .pay-checkbox-select-all input, .pay-checkbox-item input {
        display: none;
    }

    .pay-checkbox-box {
        width: 18px;
        height: 18px;
        border: 2px solid var(--color-primary, #00236f);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        transition: 0.2s;
    }

    .pay-checkbox-select-all input:checked ~ .pay-checkbox-box, 
    .pay-checkbox-item input:checked ~ .pay-checkbox-box {
        background-color: var(--color-primary, #00236f);
        color: #fff;
    }

    .pay-checkbox-box i {
        font-size: 11px;
    }

    .pay-checkbox-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--color-text, #0b1c30);
    }

    /* Table */
    .pay-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    
    .pay-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pay-table th {
        text-align: left;
        padding: 14px 24px;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-outline, #757682);
        background-color: var(--color-card, #fff);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #f1f5f9;
    }

    .pay-table td {
        padding: 16px 24px;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--color-text, #0b1c30);
    }
    
    .pay-row:last-child td {
        border-bottom: none;
    }
    
    .pay-row.row-overdue {
        background-color: #fffaf9;
    }
    
    /* Service Cell */
    .pay-service-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pay-icon-square {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .pay-icon-text {
        color: var(--color-outline, #757682);
        font-size: 16px;
    }

    .badge-unpaid { background-color: #e2e8f0; color: #64748b; border: 1px solid #cbd5e1; font-weight: 600; padding: 4px 10px; font-size: 0.7rem; }
    .badge-paid { background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; font-weight: 600; padding: 4px 10px; font-size: 0.7rem; }
    .badge-overdue { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; font-weight: 600; padding: 4px 10px; font-size: 0.7rem; }
    .badge-partial { background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; font-weight: 600; padding: 4px 10px; font-size: 0.7rem; }

    .icon-internet { background-color: #ffe4e6; color: #e11d48; }
    .icon-management { background-color: #e0f2fe; color: #0284c7; }
    .icon-electric { background-color: #fef9c3; color: #ca8a04; }
    .icon-water { background-color: #e0f2fe; color: #0284c7; }
    .icon-default { background-color: #f1f5f9; color: #64748b; }

    .pay-service-name {
        font-weight: 600;
        color: var(--color-text, #0b1c30);
        text-decoration: none;
        display: block;
        margin-bottom: 4px;
    }
    .pay-service-name:hover { color: var(--color-primary, #00236f); }

    .pay-service-sub {
        font-size: 0.8rem;
        color: var(--color-text-secondary, #444651);
    }

    .text-right { text-align: right !important; }
    .text-danger { color: #ef4444 !important; }
    .text-success { color: #10b981 !important; }
    .text-muted { color: var(--color-outline, #757682) !important; font-weight: 400; }
    .mt-4 { margin-top: 24px; }
    .ms-1 { margin-left: 4px; }
    .p-4 { padding: 24px; }
    
    /* Amount Cell */
    .pay-amount-cell {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 4px;
    }

    .pay-amount-val {
        font-size: 1.05rem;
        font-weight: 700;
    }

    .pay-status-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 3px 6px;
        border-radius: 4px;
        letter-spacing: 0.03em;
    }
    
    .pay-status-text {
        font-size: 0.75rem;
        font-weight: 600;
    }


    .pay-card__footer {
        padding: 16px 24px;
        background: transparent;
    }

    .btn-history-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--color-primary, #00236f);
        text-decoration: none;
        font-weight: 600;
        padding: 8px 16px;
        background: transparent;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-history-link:hover { background-color: #e0e7ff; }

    /* Right Column Top Cards */
    .pay-top-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .pay-mini-card {
        background: var(--color-card, #fff);
        border: 1px solid #f1f5f9;
        border-radius: var(--radius-md, 12px);
        padding: 16px;
        text-align: center;
        box-shadow: 0px 4px 20px rgba(0, 35, 111, 0.03);
    }
    
    .pay-mini-card__label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-outline, #757682);
        margin-bottom: 8px;
        letter-spacing: 0.05em;
    }

    .pay-mini-card__val {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--color-text, #0b1c30);
    }
    .pay-mini-card__val span { font-size: 0.9rem; }
    
    .pay-mini-card--primary {
        background: #00236f;
        border: none;
    }
    .pay-mini-card--primary .pay-mini-card__label,
    .pay-mini-card--primary .pay-mini-card__val {
        color: #fff !important;
    }
    .pay-mini-card--outline {
        background: #fff;
        border: 1px solid #e2e8f0;
    }
    .pay-mini-card--outline .pay-mini-card__label {
        color: #64748b;
    }

    /* Summary Card */
    .pay-summary-card {
        background: var(--color-card, #fff);
        border: 1px solid #f1f5f9;
        border-radius: var(--radius-lg, 12px);
        padding: 24px;
        position: sticky;
        top: 24px;
        box-shadow: 0px 4px 20px rgba(0, 35, 111, 0.03);
    }

    .pay-summary__header {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }
    .pay-summary__header i { color: var(--color-primary, #00236f); }

    .pay-summary__list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
        min-height: 100px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--color-text-secondary, #444651);
    }
    
    .summary-item.is-overdue { color: #ef4444; }

    .summary-item .item-val {
        font-weight: 700;
        color: var(--color-text, #0b1c30);
    }
    .summary-item.is-overdue .item-val { color: #ef4444; }

    .pay-summary__subtotal {
        padding-top: 16px;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.95rem;
        color: var(--color-text-secondary, #444651);
        margin-bottom: 24px;
    }
    .val-bold { font-weight: 700; color: var(--color-text, #0b1c30); }

    .pay-summary__total-box {
        background-color: #f8fafc;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 24px;
    }

    .pay-summary__total-box .total-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-outline, #757682);
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }

    .pay-summary__total-box .total-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--color-primary, #00236f);
    }
    .pay-summary__total-box .total-value span { font-size: 1.1rem; }

    .btn-pay-submit {
        width: 100%;
        background-color: #0b1c30;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 16px;
        font-size: 1.05rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        transition: 0.2s;
    }
    .btn-pay-submit:hover:not(:disabled) {
        background-color: var(--color-primary, #00236f);
    }
    .btn-pay-submit:disabled {
        background-color: var(--color-outline-soft, #c5c5d3);
        cursor: not-allowed;
    }

    .pay-secure-text {
        text-align: center;
        font-size: 0.8rem;
        color: var(--color-outline, #757682);
        margin-top: 16px;
    }
    .pay-secure-text i { margin-right: 4px; }

    /* Empty state */
    .pay-empty {
        text-align: center;
        padding: 40px 20px;
    }
    .pay-empty__icon {
        font-size: 40px;
        color: #10b981;
        margin-bottom: 16px;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .pay-grid { grid-template-columns: 1fr; }
    }

.invoice-detail-page {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 20px;
        font-family: var(--font-family, 'Inter', sans-serif);
    }

    .detail-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--color-primary, #00236f);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: color var(--transition-fast, 0.2s);
    }

    .btn-back:hover {
        color: #1d4ed8;
    }

    .btn-top-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: var(--color-card, #fff);
        border: 1px solid var(--color-outline-soft, #c5c5d3);
        border-radius: var(--radius, 8px);
        padding: 8px 16px;
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--color-text-secondary, #444651);
        cursor: pointer;
        transition: all var(--transition-fast, 0.2s) ease;
    }

    .btn-top-action:hover {
        background-color: var(--color-surface-low, #eff4ff);
        border-color: var(--color-outline, #757682);
    }

    /* Main Card Layout */
    .detail-card {
        background-color: var(--color-card, #fff);
        border: 1px solid var(--color-outline-soft, #c5c5d3);
        border-radius: var(--radius-md, 12px);
        box-shadow: var(--color-shadow, 0px 4px 12px rgba(30,58,138,.05));
        overflow: hidden;
    }

    .detail-card__header {
        padding: 24px;
        border-bottom: 1px solid var(--color-background, #f8f9ff);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
    }

    .invoice-code {
        font-family: monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--color-primary, #00236f);
        background-color: var(--color-surface-low, #eff4ff);
        padding: 4px 8px;
        border-radius: var(--radius-sm, 4px);
    }

    .invoice-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
        margin: 8px 0 4px 0;
    }

    .invoice-subtitle {
        font-size: 0.9rem;
        color: var(--color-text-secondary, #444651);
        margin: 0;
    }

    /* Status Badges */
    .pay-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: var(--radius-sm, 4px);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .pay-badge--paid {
        background-color: #e6fcf5;
        color: #0ca678;
    }

    .pay-badge--unpaid {
        background-color: var(--color-surface-low, #eff4ff);
        color: var(--color-primary, #00236f);
    }

    .pay-badge--overdue {
        background-color: #fff5f5;
        color: var(--color-error, #ba1a1a);
    }

    /* Metadata Panel */
    .detail-card__meta {
        background-color: var(--color-background, #f8f9ff);
        padding: 18px 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        border-bottom: 1px solid var(--color-outline-soft, #c5c5d3);
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .meta-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--color-outline, #757682);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .meta-val {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
    }

    /* Items Section */
    .detail-card__items {
        padding: 24px;
        border-bottom: 1px solid var(--color-background, #f8f9ff);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--color-primary, #00236f);
        margin: 0 0 16px 0;
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table th {
        text-align: left;
        padding: 10px 0;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--color-outline, #757682);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--color-outline-soft, #c5c5d3);
    }

    .items-table td {
        padding: 14px 0;
        border-bottom: 1px solid var(--color-background, #f8f9ff);
        vertical-align: top;
    }

    .items-table tr:last-child td {
        border-bottom: none;
    }

    .item-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--color-text, #0b1c30);
        margin-bottom: 3px;
    }

    .item-desc {
        font-size: 0.8rem;
        color: var(--color-text-secondary, #444651);
    }

    .val-quantity {
        font-size: 0.9rem;
        color: var(--color-text-secondary, #444651);
    }

    .val-subtotal {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
    }

    .text-right, 
    .items-table th.text-right, 
    .items-table td.text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: var(--color-outline, #757682);
    }

    /* Total block */
    .detail-card__total {
        padding: 20px 24px;
        background-color: var(--color-surface-low, #eff4ff);
        border-bottom: 1px solid var(--color-outline-soft, #c5c5d3);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total-label {
        font-size: 1rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
    }

    .total-val {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--color-primary, #00236f);
    }

    /* Payment details block */
    .detail-card__payment {
        padding: 24px;
        background-color: var(--color-card, #fff);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .payment-transaction {
        border: 1px solid var(--color-outline-soft, #c5c5d3);
        border-radius: var(--radius-md, 12px);
        padding: 24px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .payment-info-item {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        font-size: 0.92rem;
        gap: 16px;
    }

    .info-label {
        color: var(--color-text-secondary, #444651);
        font-weight: 500;
        width: auto;
        flex-shrink: 0;
    }

    .info-val {
        color: var(--color-text, #0b1c30);
        font-weight: 500;
        text-align: right;
    }
    
    .desktop-label { display: none; }
    .mobile-label { display: inline; }

    /* On tablets and larger, use a true 4-column grid for perfect vertical alignment */
    @media (min-width: 992px) {
        .payment-transaction {
            display: grid;
            grid-template-columns: max-content 1fr max-content 1fr;
            column-gap: 24px;
            row-gap: 16px;
            align-items: center;
        }
        .payment-info-item {
            display: contents;
        }
        .info-val {
            text-align: left;
            justify-self: start;
        }
        .desktop-label { display: inline; }
        .mobile-label { display: none; }
        
        /* The 'Nội dung' row spans the remaining columns */
        .payment-info-item.full-width .info-label {
            grid-column: 1 / 2;
        }
        .payment-info-item.full-width .info-val {
            grid-column: 2 / -1;
        }
    }

    .code-val {
        font-family: monospace;
        color: var(--color-primary, #00236f);
        font-size: 0.95rem;
        background-color: var(--color-surface-low, #eff4ff);
        padding: 2px 8px;
        border-radius: var(--radius-sm, 4px);
    }

    /* Action buttons below card */
    .detail-card__pay-action {
        padding: 24px;
        display: flex;
        justify-content: flex-end;
        background-color: var(--color-card, #fff);
    }

    .btn-pay-now {
        background-color: var(--color-secondary, #006c49);
        color: var(--color-on-secondary, #fff);
        border: none;
        border-radius: var(--radius, 8px);
        padding: 12px 28px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all var(--transition-fast, 0.2s) ease;
        box-shadow: 0 2px 8px rgba(0, 108, 73, 0.2);
    }

    .btn-pay-now:hover {
        background-color: #005539;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 108, 73, 0.3);
    }

    .btn-pay-now:active {
        transform: translateY(0);
    }

    /* Print styling */
    @media print {
        body * {
            visibility: hidden;
        }
        .detail-card, .detail-card * {
            visibility: visible;
        }
        .detail-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
        }
        .detail-card__pay-action {
            display: none;
        }
    }

.inv-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
}

.inv-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.inv-eyebrow {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin: 0 0 4px 0;
    font-weight: 600;
}

.inv-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

/* ALERTS */
.inv-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.95rem;
    font-weight: 500;
}

.inv-alert--success {
    background-color: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.inv-alert--error {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* FILTERS */
.inv-filter-section {
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.inv-filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.inv-filter-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #475569;
}

.inv-filter-select {
    padding: 6px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #1e293b;
    outline: none;
    cursor: pointer;
    background-color: #f8fafc;
}

.inv-filter-select:focus {
    border-color: #3b82f6;
}

/* EMPTY STATE */
.inv-empty {
    text-align: center;
    padding: 50px 20px;
    background-color: #fff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    margin-top: 10px;
}

.inv-empty__icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background-color: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px auto;
}

.inv-empty__title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 6px 0;
}

.inv-empty__desc {
    font-size: 0.9rem;
    color: #64748b;
    max-width: 400px;
    margin: 0 auto;
}

/* CARD LIST */
.inv-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.inv-card {
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 16px 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.inv-card__accent {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
}

/* Accents by status */
.inv-card--paid .inv-card__accent { background-color: #10b981; }
.inv-card--unpaid .inv-card__accent { background-color: #f59e0b; }
.inv-card--overdue .inv-card__accent { background-color: #ef4444; }

.inv-card__body {
    flex: 1;
    min-width: 0;
}

.inv-card__title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 4px 0;
}

.inv-card__subtitle {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0 0 8px 0;
}

.inv-card__details {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}

.inv-card__detail-badge {
    font-size: 0.75rem;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.inv-card__meta {
    display: flex;
    gap: 15px;
}

.inv-meta-item {
    font-size: 0.8rem;
    color: #64748b;
}

/* RIGHT SECTION */
.inv-card__right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    margin-left: 20px;
    flex-shrink: 0;
}

.inv-card__price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
}

.inv-card__price--pending {
    color: #dc2626;
}

.inv-card__status-box {
    margin-bottom: 2px;
}

.inv-status {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 4px;
    display: inline-block;
}

.inv-status--paid {
    background-color: #dcfce7;
    color: #15803d;
}

.inv-status--unpaid {
    background-color: #fef3c7;
    color: #b45309;
}

.inv-status--overdue {
    background-color: #fee2e2;
    color: #b91c1c;
}

/* BUTTONS */
.inv-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    outline: none;
}

.inv-btn--primary {
    background-color: #2563eb;
    color: #fff;
}

.inv-btn--primary:hover {
    background-color: #1d4ed8;
}

/* PAGINATION */
.inv-pagination {
    margin-top: 25px;
    display: flex;
    justify-content: center;
}

.mobile-only { display: none !important; }

/* ==================================================== */
/* MOBILE RESPONSIVE (CARD LAYOUT & STICKY BOTTOM BAR) */
/* ==================================================== */
@media (max-width: 1024px) {
    .desktop-only { display: none !important; }
    .mobile-only { display: block !important; }
    span.mobile-only, i.mobile-only { display: inline-block !important; }
    div.mobile-flex { display: flex !important; }

    .pay-grid {
        display: flex;
        flex-direction: column;
    }
    .pay-col-left {
        order: 2;
        width: 100%;
    }
    .pay-col-right {
        order: 1;
        width: 100%;
        display: flex;
        flex-direction: column;
    }
    
    /* Top Cards Layout */
    .pay-top-cards.mobile-only {
        order: -1;
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }
    .pay-top-cards .pay-mini-card {
        border-radius: 12px !important;
    }
    .pay-mini-card {
        text-align: center;
        padding: 16px 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border-radius: 12px;
    }
    .pay-mini-card__label {
        font-size: 0.7rem !important;
        margin-bottom: 6px;
    }
    .pay-mini-card__val {
        font-size: 1.25rem !important;
        justify-content: center;
    }

    /* Transform Table to Cards */
    .pay-card {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
    }
    .pay-card__header {
        padding: 0 0 16px 0 !important;
        border: none !important;
    }
    .pay-card__title {
        font-size: 1.05rem !important;
    }
    
    .pay-table-wrapper {
        background: transparent !important;
        overflow: visible !important;
    }
    .pay-table {
        display: block;
    }
    .pay-table thead {
        display: none;
    }
    .pay-table tbody {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .pay-row {
        display: grid !important;
        grid-template-columns: 36px 1fr auto;
        grid-template-rows: auto auto;
        grid-template-areas: 
            "check service price"
            ". toggle price";
        gap: 12px 8px;
        align-items: start;
        background: #fff;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border: 1px solid #f1f5f9;
    }
    .pay-row > td {
        padding: 0 !important;
        border: none !important;
        display: block;
    }
    
    /* Card Cells Layout with Grid Areas */
    .pay-row > td.td-check { 
        grid-area: check; 
        padding-top: 4px !important;
    }
    .pay-row > td.td-service { 
        grid-area: service; 
        min-width: 0; /* allow truncation if needed */
    }
    .pay-service-cell {
        flex-direction: row !important;
        align-items: flex-start !important;
    }
    .pay-row > td.td-meta-mobile { 
        grid-area: meta;
        margin-top: 0 !important;
        padding-left: 0 !important;
    }
    .pay-row > td.td-price { 
        grid-area: price;
        margin-top: 0 !important;
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .pay-row > td.td-chevron-mobile { 
        grid-area: toggle;
        position: static !important;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-top: 4px !important;
    }
    .pay-row > td.td-chevron-mobile .toggle-text-container {
        margin-top: 0 !important;
    }
    
    .pay-row.is-expanded {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: none !important;
    }
    .pay-row + tr[id^="details-"] {
        margin-top: -16px;
        width: 100%;
        display: block; /* Ensure it takes full width as flex item */
    }
    .pay-row + tr[id^="details-"] > td {
        padding: 0 !important;
        width: 100%;
        display: block;
    }
    .pay-row + tr[id^="details-"] table {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-top: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border-radius: 0 0 12px 12px;
        margin-top: 0;
        padding: 0 16px 16px 16px;
        display: block;
    }
    .pay-row + tr[id^="details-"] table::before {
        content: "";
        display: block;
        width: 100%;
        height: 1px;
        background: #e2e8f0;
        margin-bottom: 12px;
    }
    .pay-row + tr[id^="details-"] thead th:nth-child(3),
    .pay-row + tr[id^="details-"] thead th:nth-child(5) {
        display: none;
    }
    .pay-row + tr[id^="details-"] thead {
        display: none;
    }
    .pay-row + tr[id^="details-"] tbody {
        display: flex;
        flex-direction: column;
        gap: 8px; /* Reduced gap since no borders */
    }
    .pay-row + tr[id^="details-"] tr {
        display: flex;
        width: 100%;
        border-bottom: none; /* Removed dashed border */
        padding: 8px 0; /* Adjusted padding */
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .pay-row + tr[id^="details-"] td {
        padding: 0 !important;
        border: none !important;
    }
    .pay-row + tr[id^="details-"] td:nth-child(2) {
        flex: 1;
    }
    .pay-row + tr[id^="details-"] td:nth-child(4) {
        margin-left: auto;
        text-align: right;
    }
    
    /* Fixed Bottom Bar */
    .pay-summary-card {
        position: fixed !important;
        top: auto !important;
        bottom: 0 !important;
        left: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        background: #fff !important;
        border-radius: 20px 20px 0 0 !important;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.1) !important;
        z-index: 9999 !important;
        padding: 20px 24px !important;
    }
    .pay-summary__header, .pay-summary__list, .pay-summary__total-box {
        display: none !important;
    }
    .pay-summary__subtotal {
        margin-bottom: 16px !important;
        font-size: 1.05rem !important;
    }
    .pay-summary__subtotal #summary-subtotal-val {
        font-size: 1.35rem !important;
        font-weight: 800 !important;
        color: var(--color-text, #0b1c30) !important;
    }
    .btn-pay-submit {
        border-radius: 8px !important;
        padding: 14px 20px !important;
        width: 100% !important;
    }
    
    .pay-dashboard {
        padding-bottom: 180px;
    }
}
</style>
@endpush
