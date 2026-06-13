<style>
.pay-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px 20px;
        font-family: var(--font-family, 'Inter', sans-serif);
    }

    .pay-header-row {
        margin-bottom: 25px;
    }

    .pay-page__title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--color-primary, #00236f);
        margin: 0 0 8px 0;
    }

    .pay-page__subtitle {
        font-size: 0.95rem;
        color: var(--color-text-secondary, #444651);
        margin: 0;
        line-height: 1.5;
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

    .pay-alert--success {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    .pay-alert--error {
        background-color: #fef2f2;
        border: 1px solid #fecaca;
        color: var(--color-error, #ba1a1a);
    }

    /* Empty State */
    .pay-empty {
        text-align: center;
        padding: 60px 20px;
        background-color: var(--color-card, #fff);
        border: 1px dashed var(--color-outline-soft, #c5c5d3);
        border-radius: var(--radius-md, 12px);
    }

    .pay-empty__icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background-color: var(--color-surface-low, #eff4ff);
        color: var(--color-success, #10b981);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px auto;
    }

    .pay-empty__title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--color-text, #0b1c30);
        margin: 0 0 6px 0;
    }

    .pay-empty__desc {
        font-size: 0.95rem;
        color: var(--color-text-secondary, #444651);
        max-width: 400px;
        margin: 0 auto;
    }

    /* White Main Container */
    .pay-card-container {
        background: var(--color-card, #fff);
        border: 1px solid var(--color-outline-soft, #c5c5d3);
        border-radius: var(--radius-md, 12px);
        box-shadow: var(--color-shadow, 0px 4px 12px rgba(30,58,138,.05));
        overflow: hidden;
        margin-bottom: 24px;
    }

    /* Select All Header */
    .pay-select-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--color-background, #f8f9ff);
    }

    .pay-header-month {
        font-size: 0.9rem;
        color: var(--color-text-secondary, #444651);
        font-weight: 500;
    }

    /* Checkbox Styling */
    .pay-checkbox {
        position: relative;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        user-select: none;
        gap: 12px;
    }

    .pay-checkbox input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .pay-checkbox__box {
        width: 22px;
        height: 22px;
        min-width: 22px;
        background-color: #fff;
        border: 2px solid var(--color-outline-soft, #c5c5d3);
        border-radius: var(--radius-sm, 4px);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-fast, 0.2s);
    }

    .pay-checkbox__box svg {
        width: 12px;
        height: 12px;
        opacity: 0;
        transition: opacity 0.15s;
    }

    .pay-checkbox input:checked ~ .pay-checkbox__box {
        background-color: var(--color-primary, #00236f);
        border-color: var(--color-primary, #00236f);
    }

    .pay-checkbox input:checked ~ .pay-checkbox__box svg {
        opacity: 1;
    }

    .pay-checkbox__label {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-text, #0b1c30);
    }

    .pay-checkbox--item {
        margin: 0;
    }

    /* Invoice List */
    .pay-list {
        display: flex;
        flex-direction: column;
    }

    .pay-item {
        display: flex;
        align-items: center;
        padding: 16px 24px;
        border-bottom: 1px solid var(--color-background, #f8f9ff);
        transition: background-color var(--transition-fast, 0.15s);
    }

    .pay-item:hover {
        background-color: var(--color-surface-low, #eff4ff);
    }

    .pay-item:last-child {
        border-bottom: none;
    }

    /* Icon circles styling */
    .pay-item__icon {
        margin-left: 14px;
        margin-right: 16px;
        flex-shrink: 0;
    }

    .pay-item__icon span {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-full, 50%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon--electric {
        color: #0ca678;
        background-color: #e6fcf5;
    }

    .icon--water {
        color: #1c7ed6;
        background-color: #e7f5ff;
    }

    .icon--management {
        color: var(--color-text-secondary, #444651);
        background-color: #f1f3f5;
    }

    .icon--parking {
        color: #099268;
        background-color: #e3faf2;
    }

    .icon--default {
        color: var(--color-text-secondary, #444651);
        background-color: #f1f3f5;
    }

    /* Info Column */
    .pay-item__info {
        flex: 1;
        min-width: 0;
    }

    .pay-item__name {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--color-text, #0b1c30);
        margin: 0 0 4px 0;
    }

    .pay-item__meta {
        font-size: 0.82rem;
        color: var(--color-text-secondary, #444651);
        margin: 0;
    }

    /* Right hand side of item (Amount & Status) */
    .pay-item__right-side {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        margin-right: 18px;
        flex-shrink: 0;
    }

    .pay-item__amount {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--color-text, #0b1c30);
    }

    /* Status Badges */
    .pay-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: var(--radius-sm, 4px);
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .pay-badge--unpaid {
        background-color: var(--color-surface-low, #eff4ff);
        color: var(--color-primary, #00236f);
    }

    .pay-badge--overdue {
        background-color: #fff5f5;
        color: var(--color-error, #ba1a1a);
    }

    /* Row Arrow chevron */
    .pay-item__arrow {
        color: var(--color-outline-soft, #c5c5d3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 12px;
    }

    .pay-item__detail-link {
        color: var(--color-outline, #757682);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        border-radius: 50%;
        transition: all var(--transition-fast, 0.2s) ease;
        text-decoration: none;
    }

    .pay-item__detail-link:hover {
        background-color: var(--color-surface-low, #eff4ff);
        color: var(--color-primary, #00236f);
    }

    /* Unchecked row opacity styling */
    .pay-item.unchecked {
        opacity: 0.55;
    }

    .pay-item.unchecked .pay-item__amount {
        text-decoration: line-through;
        color: var(--color-outline, #757682);
    }

    /* Bottom Summary Card */
    .pay-footer-summary {
        background-color: var(--color-surface-low, #eff4ff);
        border-top: 1px solid var(--color-outline-soft, #c5c5d3);
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .pay-footer-summary__count {
        font-size: 0.95rem;
        color: var(--color-text, #0b1c30);
    }

    .pay-footer-summary__count strong {
        color: var(--color-primary, #00236f);
        font-weight: 700;
    }

    .pay-footer-summary__total {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .pay-total-label {
        font-size: 0.65rem;
        font-weight: 700;
        color: var(--color-outline, #757682);
        letter-spacing: 0.08em;
        margin-bottom: 4px;
    }

    .pay-total-val {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--color-primary, #00236f);
    }

    /* Submit Pay Button */
    .pay-footer-summary__btn {
        flex-shrink: 0;
    }

    .pay-submit-btn {
        background-color: var(--color-secondary, #006c49);
        color: var(--color-on-secondary, #fff);
        border: none;
        border-radius: var(--radius, 8px);
        padding: 12px 24px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all var(--transition-fast, 0.2s) ease;
        box-shadow: 0 2px 8px rgba(0, 108, 73, 0.2);
    }

    .pay-submit-btn:hover {
        background-color: #005539;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 108, 73, 0.3);
    }

    .pay-submit-btn:active {
        transform: translateY(0);
    }

    .pay-submit-btn:disabled {
        background-color: var(--color-outline-soft, #c5c5d3);
        color: var(--color-outline, #757682);
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* Bottom History Link Section */
    .pay-history-link-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }

    .pay-history-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--color-primary, #00236f);
        text-decoration: none;
        transition: color var(--transition-fast, 0.15s) ease;
    }

    .pay-history-link:hover {
        color: #1d4ed8;
    }

    .pay-history-link svg {
        color: var(--color-primary, #00236f);
    }

    .pay-history-link .chevron-right {
        margin-left: 2px;
        font-family: monospace;
    }

    /* Responsive */
    @media (max-width: 680px) {
        .pay-header-row {
            flex-direction: column;
            gap: 14px;
        }

        .pay-actions-top {
            width: 100%;
        }

        .btn-top-action {
            flex: 1;
            justify-content: center;
        }

        .pay-item {
            padding: 14px 16px;
        }

        .pay-item__icon {
            margin-left: 8px;
            margin-right: 10px;
        }

        .pay-footer-summary {
            flex-direction: column;
            align-items: stretch;
            padding: 18px 16px;
            text-align: center;
        }

        .pay-footer-summary__total {
            align-items: center;
            margin: 8px 0;
        }

        .pay-submit-btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Print styles */
    @media print {
        body * {
            visibility: hidden;
        }
        .pay-card-container, .pay-card-container * {
            visibility: visible;
        }
        .pay-card-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
            box-shadow: none;
        }
        .pay-footer-summary__btn {
            display: none;
        }
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
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .payment-info-item {
        display: flex;
        align-items: center;
        font-size: 0.92rem;
        border-bottom: 1px dashed var(--color-background, #f8f9ff);
        padding-bottom: 8px;
    }

    .payment-info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-label {
        color: var(--color-text-secondary, #444651);
        font-weight: 500;
        width: 240px;
        flex-shrink: 0;
    }

    .info-val {
        color: var(--color-text, #0b1c30);
        font-weight: 600;
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

</style>