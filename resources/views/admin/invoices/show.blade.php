@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Hóa đơn ' . $invoice->invoice_code)

@section('content')
<div class="invoice-detail-page">
    {{-- Header with back button --}}
    <div class="detail-header-row">
        <a href="{{ route('admin.invoices.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại danh sách
        </a>
        <div class="detail-actions">
            <button type="button" class="btn-top-action" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                In hóa đơn
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="pay-alert pay-alert--success" style="margin-bottom: 20px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <div>{{ session('success') }}</div>
    </div>
    @endif
    @if(session('error'))
    <div class="pay-alert pay-alert--error" style="margin-bottom: 20px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>{{ session('error') }}</div>
    </div>
    @endif

    {{-- Main Detail Card --}}
    <div class="detail-card">
        {{-- Invoice Title & Status Banner --}}
        <div class="detail-card__header">
            <div>
                <span class="invoice-code">#{{ $invoice->invoice_code }}</span>
                <h1 class="invoice-title">{{ $invoice->title }}</h1>
                <p class="invoice-subtitle">
                    Căn hộ: {{ $invoice->apartment->apartment_number ?? '—' }} 
                    ({{ optional(optional($invoice->apartment)->floor)->name ?? 'Tầng ' . optional(optional($invoice->apartment)->floor)->floor_number }} - {{ optional(optional(optional($invoice->apartment)->floor)->block)->name ?? '' }})
                </p>
                <p class="invoice-subtitle" style="margin-top: 4px;">
                    Người tạo: <strong>{{ optional($invoice->creator)->name ?? 'Hệ thống' }}</strong>
                </p>
            </div>
            <div class="invoice-status-wrap">
                <span class="pay-badge pay-badge--{{ $invoice->status === 'paid' ? 'paid' : ($invoice->status === 'partial' ? 'partial' : ($invoice->status === 'overdue' ? 'overdue' : 'unpaid')) }}">
                    {{ \App\Models\Invoice::statusLabel($invoice->status) }}
                </span>
            </div>
        </div>

        {{-- Metadata Row --}}
        <div class="detail-card__meta">
            <div class="meta-item">
                <span class="meta-label">Kỳ thanh toán</span>
                <span class="meta-val">Tháng {{ str_pad($invoice->billing_month->month ?? $invoice->billing_month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Ngày phát hành</span>
                <span class="meta-val">{{ $invoice->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Hạn thanh toán</span>
                <span class="meta-val">{{ $invoice->due_date->format('d/m/Y') }}</span>
            </div>
        </div>



        {{-- Line Items Table --}}
        <div class="detail-card__items">
            <h3 class="section-title">Chi tiết dịch vụ</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Tên dịch vụ</th>
                        <th>Loại phí</th>
                        <th class="text-right">Số lượng / Chỉ số</th>
                        <th class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->details as $detail)
                        <tr>
                            <td>
                                <div class="item-name">{{ $detail->servicePrice->name ?? 'Dịch vụ / Phí khác' }}</div>
                                <div class="item-desc">
                                    Đơn giá: {{ number_format($detail->servicePrice->price ?? $detail->amount) }} đ
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $detail->servicePrice->type ?? 'other' }}">
                                    {{ \App\Models\Invoice::typeLabel($detail->servicePrice->type ?? 'other') }}
                                </span>
                            </td>
                            <td class="text-right val-quantity">
                                {{ $detail->quantity }}
                            </td>
                            <td class="text-right val-subtotal">{{ number_format($detail->amount, 0, ',', '.') }} đ</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Không có thông tin chi tiết dịch vụ.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total Row --}}
        <div class="detail-card__total">
            <span class="total-label">Tổng cộng</span>
            <span class="total-val">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</span>
        </div>

        {{-- Lịch sử thanh toán --}}
        @if($invoice->payments->isNotEmpty())
        <div class="detail-card__payment" style="border-bottom: 1px solid var(--color-background, #f8f9ff);">
            <h3 class="section-title">Thông tin giao dịch</h3>
            @foreach($invoice->payments as $pm)
                @php
                    $methodLabel = match($pm->payment_method) {
                        'vnpay'         => 'VNPay',
                        'bank_transfer' => 'Chuyển khoản ngân hàng',
                        'cash'          => 'Tiền mặt (Nộp trực tiếp)',
                        default         => ucfirst($pm->payment_method),
                    };

                    if ($pm->vnp_txn_ref) {
                        $vnpTxnNo = $pm->vnp_txn_ref;
                    } elseif (str_contains($pm->transaction_code ?? '', '|')) {
                        $vnpTxnNo = explode('|', $pm->transaction_code)[0];
                    } else {
                        $vnpTxnNo = $pm->transaction_code ?? '—';
                    }
                @endphp
                <div class="payment-grid" style="margin-bottom: 20px; border-bottom: 1px dashed var(--color-outline-soft); padding-bottom: 20px; {{ $pm->is_refunded ? 'opacity:.65;' : '' }}">
                    <div class="payment-info-item">
                        <span class="info-label">Mã hóa đơn hệ thống:</span>
                        <span class="info-val code-val">{{ $invoice->invoice_code }}</span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Số tiền thanh toán:</span>
                        <span class="info-val" style="{{ $pm->is_refunded ? 'text-decoration:line-through;color:#757682' : '' }}">
                            {{ number_format($pm->amount) }} đ
                        </span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Phương thức thanh toán:</span>
                        <span class="info-val">{{ $methodLabel }}</span>
                    </div>
                    @if($pm->payment_method === 'vnpay')
                    <div class="payment-info-item">
                        <span class="info-label">Mã giao dịch đối soát (VNPay):</span>
                        <span class="info-val code-val">{{ $vnpTxnNo ?: '—' }}</span>
                    </div>
                    @endif
                    <div class="payment-info-item">
                        <span class="info-label">Thời gian thanh toán:</span>
                        <span class="info-val">{{ $pm->paid_at ? $pm->paid_at->format('d/m/Y H:i') : '—' }}</span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Trạng thái:</span>
                        <span class="info-val">
                            @if($pm->is_refunded)
                                <span class="pay-badge" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">🔄 Đã hủy</span>
                                @if($pm->refund_note)
                                <div style="font-size:11px;color:#757682;margin-top:4px;">{{ $pm->refund_note }}</div>
                                @endif
                            @else
                                <span class="pay-badge pay-badge--paid">Thành công</span>
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Form ghi nhận thanh toán thủ công --}}
        @if($invoice->status !== 'paid')
        <div class="detail-card__pay-action" style="flex-direction: column; align-items: stretch; background-color: #f8f9ff; padding: 24px;">
            <h3 class="section-title" style="margin-bottom: 8px; display: flex; align-items: center; gap: 6px; font-size: 1.05rem;">
                💵 Ghi nhận Thanh toán Thủ công
            </h3>
            <p style="font-size: 0.85rem; color: var(--color-text-secondary, #444651); margin-bottom: 20px;">
                Ghi nhận khi cư dân nộp tiền mặt hoặc chuyển khoản.
                @if($invoice->status === 'partial')
                Còn thiếu <strong style="color:var(--color-error, #ba1a1a);">{{ number_format($invoice->remaining_amount) }}đ</strong>.
                @endif
            </p>

            <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
                @csrf
                @method('PATCH')
                <div class="payment-form-grid">
                    {{-- Số tiền --}}
                    <div class="form-group">
                        <label class="form-label" for="amount">Số tiền thu</label>
                        <input
                            type="number"
                            name="amount"
                            id="amount"
                            class="form-input"
                            value="{{ old('amount', $invoice->remaining_amount ?: $invoice->total_amount) }}"
                            min="1"
                            max="{{ $invoice->remaining_amount ?: $invoice->total_amount }}"
                            step="1"
                            required>
                        @error('amount')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Phương thức --}}
                    <div class="form-group">
                        <label class="form-label" for="payment_method">Phương thức</label>
                        <select name="payment_method" id="payment_method" class="form-input">
                            <option value="transfer">🏦 Chuyển khoản ngân hàng</option>
                            <option value="cash">💵 Tiền mặt</option>
                            <option value="other">💳 Khác</option>
                        </select>
                    </div>
                </div>

                {{-- Ghi chú --}}
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="note">Ghi chú / Mã tham chiếu <span style="font-weight:400;text-transform:none;color:#757682;">(tuỳ chọn)</span></label>
                    <input
                        type="text"
                        name="note"
                        id="note"
                        class="form-input"
                        placeholder="VD: Chuyển khoản số GD #123456, hoặc ghi chú khác..."
                        value="{{ old('note') }}">
                </div>

                <div style="text-align: right;">
                    <button type="submit" class="btn-pay-now">
                        ✔ Xác nhận Thanh toán
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

<style>
    /* CSS được chuyển thể từ resident.invoices.partials.style */
    :root {
        --color-primary: #00236f;
        --color-secondary: #006c49;
        --color-text: #0b1c30;
        --color-text-secondary: #444651;
        --color-background: #f8f9ff;
        --color-surface-low: #eff4ff;
        --color-card: #ffffff;
        --color-outline: #757682;
        --color-outline-soft: #c5c5d3;
        --color-error: #ba1a1a;
        --radius: 8px;
        --radius-sm: 4px;
        --radius-md: 12px;
        --transition-fast: 0.2s;
    }

    .invoice-detail-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 10px 0;
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
        color: var(--color-primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: color var(--transition-fast);
    }
    .btn-back:hover { color: #1d4ed8; }

    .btn-top-action {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: var(--color-card);
        border: 1px solid var(--color-outline-soft);
        border-radius: var(--radius);
        padding: 8px 16px;
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--color-text-secondary);
        cursor: pointer;
        transition: all var(--transition-fast) ease;
    }
    .btn-top-action:hover {
        background-color: var(--color-surface-low);
        border-color: var(--color-outline);
    }

    .pay-alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: var(--radius);
        font-size: 0.95rem;
        font-weight: 500;
    }
    .pay-alert--success { background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .pay-alert--error { background-color: #fef2f2; border: 1px solid #fecaca; color: var(--color-error); }

    /* Main Card Layout */
    .detail-card {
        background-color: var(--color-card);
        border: 1px solid var(--color-outline-soft);
        border-radius: var(--radius-md);
        box-shadow: 0px 4px 12px rgba(30,58,138,.05);
        overflow: hidden;
    }

    .detail-card__header {
        padding: 24px;
        border-bottom: 1px solid var(--color-background);
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
        color: var(--color-primary);
        background-color: var(--color-surface-low);
        padding: 4px 8px;
        border-radius: var(--radius-sm);
    }

    .invoice-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--color-text);
        margin: 8px 0 4px 0;
    }

    .invoice-subtitle {
        font-size: 0.9rem;
        color: var(--color-text-secondary);
        margin: 0;
    }

    /* Status Badges */
    .pay-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: var(--radius-sm);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-block;
    }
    .pay-badge--paid { background-color: #e6fcf5; color: #0ca678; }
    .pay-badge--unpaid { background-color: var(--color-surface-low); color: var(--color-primary); }
    .pay-badge--partial { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .pay-badge--overdue { background-color: #fff5f5; color: var(--color-error); }

    /* Metadata Panel */
    .detail-card__meta {
        background-color: var(--color-background);
        padding: 18px 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
        border-bottom: 1px solid var(--color-outline-soft);
    }
    .meta-item { display: flex; flex-direction: column; gap: 4px; }
    .meta-label { font-size: 0.75rem; font-weight: 600; color: var(--color-outline); text-transform: uppercase; letter-spacing: 0.04em; }
    .meta-val { font-size: 0.95rem; font-weight: 700; color: var(--color-text); }

    /* Items Section */
    .detail-card__items { padding: 24px; border-bottom: 1px solid var(--color-background); }
    .section-title { font-size: 1.1rem; font-weight: 700; color: var(--color-primary); margin: 0 0 16px 0; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th {
        text-align: left; padding: 10px 0; font-size: 0.78rem; font-weight: 600; color: var(--color-outline);
        text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--color-outline-soft);
    }
    .items-table td { padding: 14px 0; border-bottom: 1px dashed var(--color-outline-soft); vertical-align: middle; }
    .items-table tr:last-child td { border-bottom: none; }
    .item-name { font-size: 0.95rem; font-weight: 600; color: var(--color-text); margin-bottom: 3px; }
    .item-desc { font-size: 0.8rem; color: var(--color-text-secondary); }
    .val-quantity { font-size: 0.9rem; color: var(--color-text-secondary); }
    .val-subtotal { font-size: 0.95rem; font-weight: 700; color: var(--color-text); }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .text-muted { color: var(--color-outline); }

    /* Total block */
    .detail-card__total {
        padding: 20px 24px; background-color: var(--color-surface-low);
        border-bottom: 1px solid var(--color-outline-soft);
        display: flex; justify-content: space-between; align-items: center;
    }
    .total-label { font-size: 1rem; font-weight: 700; color: var(--color-text); }
    .total-val { font-size: 1.4rem; font-weight: 800; color: var(--color-primary); }

    /* Progress bar */
    .progress-wrap { background: #e5e7eb; border-radius: 99px; height: 8px; overflow: hidden; margin: 8px 0 4px; }
    .progress-bar { height: 100%; border-radius: 99px; background: #0ca678; transition: width .4s ease; }
    .progress-bar.partial { background: #e67700; }

    /* Forms */
    .payment-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 0.8rem; font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: 0.04em; }
    .form-input {
        padding: 10px 14px; border: 1px solid var(--color-outline-soft); border-radius: var(--radius);
        font-size: 0.95rem; color: var(--color-text); outline: none; transition: border-color 0.2s;
    }
    .form-input:focus { border-color: var(--color-primary); box-shadow: 0 0 0 2px var(--color-surface-low); }
    .form-error { font-size: 0.8rem; color: var(--color-error); }

    .btn-pay-now {
        background-color: var(--color-secondary); color: #fff; border: none;
        border-radius: var(--radius); padding: 12px 24px; font-size: 0.95rem; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(0, 108, 73, 0.2);
    }
    .btn-pay-now:hover { background-color: #005539; transform: translateY(-1px); }

    .btn-sm-danger {
        background-color: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;
        border-radius: var(--radius-sm); padding: 4px 10px; font-size: 0.75rem; font-weight: 600; cursor: pointer;
    }

    .badge { font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; font-weight: 600; }
    .badge-other { background: #f1f3f5; color: #495057; }

    .recorder-badge {
        display: inline-flex; align-items: center; gap: 4px; font-size: 0.8rem; font-weight: 500;
        color: #1d4ed8; background: #eff6ff; border-radius: 99px; padding: 2px 8px;
    }

    /* Payment details block */
    .detail-card__payment {
        padding: 24px;
        background-color: var(--color-card, #fff);
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .payment-transaction {
        border: 1px solid var(--color-outline-soft);
        border-radius: var(--radius-md);
        padding: 20px;
        background-color: var(--color-background);
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .payment-info-item {
        display: flex;
        align-items: center;
        font-size: 0.92rem;
        border-bottom: 1px dashed var(--color-outline-soft);
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
    /* ============================================================
       PRINT STYLES
       ============================================================ */
    @media print {
        /* Ẩn hoàn toàn sidebar, header, và các nút điều hướng */
        .dashboard-sidebar,
        .sidebar-overlay,
        #dashboardSidebar,
        .dashboard-main > header,
        .dashboard-main > .dashboard-header,
        [class*="dashboard-header"],
        .detail-header-row,
        .detail-actions,
        .btn-back,
        .btn-top-action,
        .btn-pay-now,
        .btn-sm-danger,
        .detail-card__pay-action {
            display: none !important;
        }

        /* Reset layout: bỏ sidebar khỏi grid/flex */
        body, .dashboard-page {
            background: #fff !important;
        }
        .dashboard-shell {
            display: block !important;
        }
        .dashboard-main {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .dashboard-content {
            padding: 0 !important;
        }

        /* Trang in full-width */
        .invoice-detail-page {
            max-width: 100% !important;
            padding: 0 !important;
        }

        /* Bỏ box-shadow, border-radius khi in */
        .detail-card {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
            border-radius: 0 !important;
        }

        /* Bảng Chi tiết dịch vụ: căn chỉnh cột cố định */
        .items-table {
            table-layout: fixed !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .items-table th,
        .items-table td {
            padding: 8px 6px !important;
            border-bottom: 1px solid #ddd !important;
            word-wrap: break-word !important;
            overflow: hidden !important;
        }
        /* Tên dịch vụ: rộng nhất */
        .items-table th:nth-child(1),
        .items-table td:nth-child(1) { width: 42% !important; }
        /* Loại phí */
        .items-table th:nth-child(2),
        .items-table td:nth-child(2) { width: 20% !important; }
        /* Số lượng */
        .items-table th:nth-child(3),
        .items-table td:nth-child(3) { width: 18% !important; text-align: right !important; }
        /* Thành tiền */
        .items-table th:nth-child(4),
        .items-table td:nth-child(4) { width: 20% !important; text-align: right !important; }

        /* Badge không cần border-radius khi in */
        .pay-badge, .badge {
            border: 1px solid #aaa !important;
            background: #f5f5f5 !important;
            color: #000 !important;
        }

        /* Ẩn phần lịch sử thanh toán và form khi in */
        .detail-card__payment {
            display: none !important;
        }

        /* Không xuống trang giữa card */
        .detail-card { page-break-inside: avoid; }
        .detail-card__items { page-break-inside: auto; }
    }
</style>
@endsection

