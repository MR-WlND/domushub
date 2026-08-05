@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Hóa đơn ' . $invoice->invoice_code)

@section('content')
<div class="invoice-detail-page">
    {{-- Header with back button --}}
    <div class="detail-header-row">
        <a href="{{ portal_route('invoices.apartment', $invoice->apartment_id) }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại danh sách
        </a>
        <div class="detail-actions" style="display:flex;gap:10px;">
            @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                <a href="{{ portal_route('invoices.edit', $invoice) }}" class="btn-top-action" style="background:#eff6ff;color:#2563eb;border-color:#bfdbfe;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Chỉnh sửa
                </a>
            @endif
            <a href="{{ portal_route('invoices.print', $invoice) }}" target="_blank" class="btn-top-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                In hóa đơn
            </a>
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
                <span class="pay-badge pay-badge--{{ in_array($invoice->status, ['paid']) ? 'paid' : ($invoice->status === 'partial_paid' ? 'partial' : ($invoice->status === 'overdue' ? 'overdue' : ($invoice->status === 'cancelled' ? 'cancelled' : 'unpaid'))) }}">
                    {{ \App\Models\Invoice::statusLabel($invoice->status) }}
                </span>
            </div>
        </div>

        {{-- Metadata Row --}}
        <div class="detail-card__meta">
            <div class="meta-item">
                <span class="meta-label">Kỳ thanh toán</span>
                <span class="meta-val">Tháng {{ str_pad($invoice->getRawOriginal('billing_month') ?: ($invoice->billing_month->month ?? 1), 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</span>
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
                        <th width="40%">Tên dịch vụ</th>
                        <th width="20%" class="text-center">Loại phí</th>
                        <th width="15%" class="text-center">Số lượng</th>
                        <th width="25%" class="text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->details as $detail)
                        <tr>
                            <td>
                                <div class="item-name">
                                    {{ $detail->servicePrice->name ?? ($detail->note ?? 'Phí phát sinh khác') }}
                                </div>
                                <div class="item-desc">
                                    Đơn giá: {{ number_format($detail->amount / max(1, $detail->quantity)) }} đ
                                </div>
                                @if(in_array(optional($detail->servicePrice)->type, ['water']))
                                    @php
                                        $meter = \App\Models\UtilityMeter::where('apartment_id', $invoice->apartment_id)
                                                                ->where('record_month', $invoice->getRawOriginal('billing_month'))
                                             ->where('record_year', $invoice->billing_year)
                                             ->first();
                                    @endphp
                                    @if($meter)
                                        <div class="item-meter-info" style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">
                                            Chỉ số cũ: <strong>{{ $meter->old_value }}</strong> &nbsp;|&nbsp;
                                            Chỉ số mới: <strong>{{ $meter->new_value }}</strong> &nbsp;|&nbsp;
                                            Tiêu thụ: <strong>{{ $meter->usage_amount }}</strong> {{ $detail->servicePrice->type === 'water' ? 'm³' : 'kWh' }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $detail->servicePrice->type ?? 'other' }}">
                                    {{ \App\Models\Invoice::typeLabel($detail->servicePrice->type ?? 'other') }}
                                </span>
                            </td>
                            <td class="text-center val-quantity">
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
        @php
            $currentAmount = $invoice->current_amount > 0 
                ? $invoice->current_amount 
                : ($invoice->details->sum('amount') > 0 ? $invoice->details->sum('amount') : $invoice->total_amount);
                
            $alreadyPaid = (float) $invoice->paid_amount;
            $remainingThisBill = max(0, $currentAmount - $alreadyPaid);
            
            $realPreviousDebt = \App\Models\Invoice::where('apartment_id', $invoice->apartment_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($invoice) {
                    $q->where('billing_year', '<', $invoice->billing_year)
                      ->orWhere(function ($q2) use ($invoice) {
                          $q2->where('billing_year', $invoice->billing_year)
                             ->where('billing_month', '<', $invoice->getRawOriginal('billing_month'));
                      });
                })->get()->sum(function($inv) {
                    return max(0, $inv->total_amount - $inv->paid_amount);
                });
        @endphp
        <div class="detail-card__summary">
            <div class="summary-item">
                <span class="summary-label">Tổng hóa đơn kỳ này</span>
                <span class="summary-val">{{ number_format($currentAmount, 0, ',', '.') }} đ</span>
            </div>
            @if($alreadyPaid > 0)
            <div class="summary-item" style="color: #16a34a;">
                <span class="summary-label">Đã thanh toán</span>
                <span class="summary-val">- {{ number_format($alreadyPaid, 0, ',', '.') }} đ</span>
            </div>
            @endif
            <div class="summary-item total-due" style="border-top: 1px dashed #cbd5e1; margin-top: 6px; padding-top: 8px;">
                <span class="summary-label" style="font-weight: 700;">Còn phải thanh toán</span>
                <span class="summary-val" style="color: {{ $remainingThisBill > 0 ? '#dc2626' : '#16a34a' }}; font-weight: 800; font-size: 1.1rem;">
                    {{ number_format($remainingThisBill, 0, ',', '.') }} đ
                </span>
            </div>
            @if($realPreviousDebt > 0)
            <div class="summary-item" style="margin-top:8px; padding-top:8px; border-top:1px solid #f1f5f9; color:#64748b; font-size:0.85rem;">
                <span class="summary-label">Nợ đọng các kỳ trước</span>
                <span class="summary-val" style="color:#d97706; font-weight:600;">{{ number_format($realPreviousDebt, 0, ',', '.') }} đ</span>
            </div>
            @endif
        </div>

        {{-- Lịch sử thanh toán --}}
        <div class="detail-card__payment" style="border-bottom: 1px solid var(--color-background, #f8f9ff);">
            <h3 class="section-title">Lịch sử giao dịch</h3>
            @php
                $details = $invoice->details()->orderBy('id')->get();
                $payments = $invoice->payments()->orderBy('id')->get();
                $detailAllocations = [];
                $detailPaidState = [];
                
                foreach ($details as $d) {
                    $detailPaidState[$d->id] = [
                        'name' => $d->servicePrice->name ?? 'Dịch vụ',
                        'amount' => (float)$d->amount,
                        'remaining' => (float)$d->amount
                    ];
                }
                
                foreach ($payments as $p) {
                    if ($p->status === 'refunded') continue;
                    
                    $pAmount = (float)$p->amount;
                    $coveredServices = [];
                    
                    foreach ($detailPaidState as $dId => &$dState) {
                        if ($pAmount <= 0) break;
                        if ($dState['remaining'] <= 0) continue;
                        
                        $allocation = min($pAmount, $dState['remaining']);
                        $dState['remaining'] -= $allocation;
                        $pAmount -= $allocation;
                        
                        $coveredServices[] = $dState['name'];
                    }
                    
                    $detailAllocations[$p->id] = array_unique($coveredServices);
                }
            @endphp
            @forelse($invoice->payments as $pm)
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
                <div class="payment-transaction" style="margin-bottom: 24px; {{ $pm->is_refunded ? 'opacity:.65;' : '' }}">
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
                    <div class="payment-info-item">
                        <span class="info-label">Người nộp (Cư dân):</span>
                        <span class="info-val">{{ $pm->payer_name ?: ($invoice->apartment->owner_name ?? 'Cư dân căn hộ') }}</span>
                    </div>
                    @if($pm->recorder)
                    <div class="payment-info-item">
                        <span class="info-label">Người thu (Nhân viên):</span>
                        <span class="info-val">{{ $pm->recorder->name }}</span>
                    </div>
                    @endif
                    @if($pm->payment_method === 'vnpay' || $pm->payment_method === 'bank_transfer' || $vnpTxnNo !== '—')
                    <div class="payment-info-item">
                        <span class="info-label">Mã giao dịch đối soát:</span>
                        <span class="info-val code-val">{{ $vnpTxnNo ?: '—' }}</span>
                    </div>
                    @endif
                    <div class="payment-info-item">
                        <span class="info-label">Thời gian thanh toán:</span>
                        <span class="info-val">{{ $pm->paid_at ? $pm->paid_at->format('d/m/Y H:i') : '—' }}</span>
                    </div>
                    @php
                        $displayNote = $pm->note;
                        if (empty($displayNote)) {
                            $allocatedServices = $detailAllocations[$pm->id] ?? [];
                            if (!empty($allocatedServices)) {
                                $displayNote = 'Thanh toán dịch vụ: ' . implode(', ', $allocatedServices);
                            } else {
                                $displayNote = 'Thanh toán hóa đơn';
                            }
                        }
                    @endphp
                    <div class="payment-info-item">
                        <span class="info-label">Nội dung / Dịch vụ:</span>
                        <span class="info-val" style="color: #0d9488;">{{ $displayNote }}</span>
                    </div>
                    @if($pm->proof_image)
                    <div class="payment-info-item">
                        <span class="info-label">Ảnh hóa đơn (bill):</span>
                        <span class="info-val">
                            <a href="{{ asset('storage/' . $pm->proof_image) }}" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: underline;">
                                Xem ảnh hóa đơn
                            </a>
                        </span>
                    </div>
                    @endif
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
            @empty
                <div style="font-size: 0.85rem; color: #64748b; padding: 15px 0; text-align: center;">
                    Chưa có giao dịch nào được ghi nhận cho hóa đơn này.
                </div>
            @endforelse
        </div>

        {{-- Trang chi tiết hóa đơn (Chế độ chỉ xem chi tiết) --}}
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
    .text-right { text-align: right !important; }
    .text-center { text-align: center !important; }
    .text-muted { color: var(--color-outline); }

    /* Summary block */
    .detail-card__summary {
        padding: 20px 24px; background-color: var(--color-surface-low);
        border-bottom: 1px solid var(--color-outline-soft);
        display: flex; flex-direction: column; gap: 12px;
    }
    .summary-item { display: flex; justify-content: space-between; align-items: center; }
    .summary-label { font-size: 0.95rem; font-weight: 600; color: var(--color-text-secondary); }
    .summary-val { font-size: 1.05rem; font-weight: 700; color: var(--color-text); }
    
    .summary-item.total-due { margin-top: 8px; padding-top: 12px; border-top: 1px dashed var(--color-outline-soft); }
    .summary-item.total-due .summary-label { font-size: 1.05rem; font-weight: 700; color: var(--color-text); text-transform: uppercase; }
    .summary-item.total-due .summary-val { font-size: 1.4rem; font-weight: 800; color: var(--color-primary); }

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
        .items-table td:nth-child(1) { width: 35% !important; }
        /* Loại phí */
        .items-table th:nth-child(2),
        .items-table td:nth-child(2) { width: 15% !important; }
        /* Số lượng */
        .items-table th:nth-child(3),
        .items-table td:nth-child(3) { width: 15% !important; text-align: right !important; }
        /* Thành tiền */
        .items-table th:nth-child(4),
        .items-table td:nth-child(4) { width: 15% !important; text-align: right !important; }
        /* Trạng thái */
        .items-table th:nth-child(5),
        .items-table td:nth-child(5) { width: 20% !important; }
        /* Thao tác (ẩn khi in) */
        .items-table th:nth-child(6),
        .items-table td:nth-child(6) { display: none !important; }

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

    /* Modal Overlay and Content Styles */
    .modal-overlay {
        display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: #fff; border-radius: 12px; width: 100%; max-width: 440px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: hidden;
        text-align: left;
    }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 20px; border-bottom: 1px solid #e2e8f0;
    }
    .modal-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
    .modal-close {
        background: none; border: none; font-size: 1.4rem; cursor: pointer;
        color: #94a3b8; line-height: 1; padding: 0 4px;
    }
    .modal-close:hover { color: #475569; }
    .modal-body { padding: 20px; }
    .modal-footer {
        display: flex; justify-content: flex-end; gap: 10px;
        padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;
    }
    .form-field { margin-bottom: 15px; }
    .form-field label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 6px; text-transform: uppercase; }
</style>

{{-- Detail Payment Modal --}}
<div class="modal-overlay" id="detailPaymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thu tiền dịch vụ lẻ</h3>
            <button class="modal-close" onclick="closeDetailPaymentModal()">&times;</button>
        </div>
        <form id="detailPaymentForm" method="POST" action="" enctype="multipart/form-data" onsubmit="return confirmDetailPayment(event);">
            @csrf
            <div class="modal-body">
                <p id="modalDetailInfo" style="font-size: 13px; color: #64748b; margin-bottom: 16px; font-weight: 500;"></p>

                <div class="form-field">
                    <label for="modal_detail_payment_method">Phương thức thanh toán</label>
                    <select name="payment_method" id="modal_detail_payment_method" class="form-input" style="width:100%">
                        <option value="transfer">🏦 Chuyển khoản ngân hàng</option>
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="other">💳 Khác</option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="modal_detail_transaction_code">ID giao dịch ngân hàng <span style="font-weight:400;text-transform:none;color:#757682;">(nếu chuyển khoản)</span></label>
                    <input type="text" name="transaction_code" id="modal_detail_transaction_code" class="form-input" style="width:100%" placeholder="Mã giao dịch đối soát...">
                </div>

                <div class="form-field">
                    <label for="modal_detail_payer_name">Người nộp tiền (tùy chọn)</label>
                    <input type="text" name="payer_name" id="modal_detail_payer_name" class="form-input" style="width:100%" placeholder="Tên người thanh toán..." value="{{ $invoice->apartment->owner_name ?? '' }}">
                </div>

                <div class="form-field">
                    <label for="modal_detail_proof_image">Minh chứng thanh toán (Ảnh chụp bill / Biên lai)</label>
                    <div class="custom-file-upload-box" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; background: #fff; display: flex; align-items: center; justify-content: space-between; gap: 10px; min-height: 42px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                        <span id="file_name_label_modal_detail_proof_image" style="color: #64748b; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 55%; font-weight: 500;">Chưa chọn tệp nào</span>
                        <div style="display: flex; gap: 6px; flex-shrink: 0;">
                            <button type="button" onclick="document.getElementById('modal_detail_proof_image').click()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                📁 Chọn tệp
                            </button>
                            <button type="button" onclick="startCamera('modal_detail_proof_image')" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                📸 Mở Camera
                            </button>
                        </div>
                    </div>
                    <input type="file" name="proof_image" id="modal_detail_proof_image" class="form-input" style="display: none;" accept="image/*" capture="environment" onchange="updateFileNameLabel(this, 'modal_detail_proof_image')">

                    <div class="camera-wrapper" style="margin-top: 10px;">
                        <div id="camera_container_modal_detail_proof_image" style="display:none; margin-top:10px; position:relative; background:#000; border-radius:8px; overflow:hidden;">
                            <video id="video_modal_detail_proof_image" autoplay playsinline style="width:100%; max-height:250px; object-fit:cover; display:block;"></video>
                            <div style="position:absolute; bottom:10px; left:50%; transform:translateX(-50%); display:flex; gap:8px; z-index:10;">
                                <button type="button" onclick="captureSnapshot('modal_detail_proof_image')" style="background:#10b981; color:#fff; border:none; padding:6px 14px; border-radius:20px; font-weight:600; cursor:pointer; font-size:0.8rem;">📸 Chụp</button>
                                <button type="button" onclick="stopCamera('modal_detail_proof_image')" style="background:#ef4444; color:#fff; border:none; padding:6px 14px; border-radius:20px; font-weight:600; cursor:pointer; font-size:0.8rem;">✕ Đóng</button>
                            </div>
                        </div>
                        <div id="preview_container_modal_detail_proof_image" style="display:none; margin-top:10px; position:relative; max-width: 150px;">
                            <img id="img_preview_modal_detail_proof_image" src="" style="width:100%; border-radius:6px; border:1px solid #cbd5e1; display:block;">
                            <button type="button" onclick="removeCapturedPhoto('modal_detail_proof_image')" style="position:absolute; top:-5px; right:-5px; background:#ef4444; color:#fff; border:none; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; cursor:pointer; padding:0;">×</button>
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label for="modal_detail_note">Ghi chú (tùy chọn)</label>
                    <input type="text" name="note" id="modal_detail_note" class="form-input" style="width:100%" placeholder="Nhập ghi chú...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="inv-admin__btn" onclick="closeDetailPaymentModal()" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0; padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Hủy</button>
                <button type="submit" class="btn-pay-now" style="background:#10b981; border: none; padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; color:#fff; cursor: pointer;">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Global variable to keep track of camera streams
    const activeStreams = {};

    function updateFileNameLabel(input, inputId) {
        const label = document.getElementById('file_name_label_' + inputId);
        if (input.files && input.files.length > 0) {
            label.textContent = input.files[0].name;
            label.style.color = '#0f172a';
        } else {
            label.textContent = 'Chưa chọn tệp nào';
            label.style.color = '#64748b';
        }
    }

    async function startCamera(inputId) {
        // Stop any active stream for this input first
        stopCamera(inputId);

        const video = document.getElementById('video_' + inputId);
        const container = document.getElementById('camera_container_' + inputId);
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            activeStreams[inputId] = stream;
            video.srcObject = stream;
            container.style.display = 'block';
        } catch (err) {
            alert('Không thể truy cập camera. Vui lòng cấp quyền camera cho trang web hoặc chọn ảnh từ thiết bị.');
            console.error('Error accessing camera: ', err);
        }
    }

    function stopCamera(inputId) {
        if (activeStreams[inputId]) {
            activeStreams[inputId].getTracks().forEach(track => track.stop());
            delete activeStreams[inputId];
        }
        const container = document.getElementById('camera_container_' + inputId);
        if (container) {
            container.style.display = 'none';
        }
        const video = document.getElementById('video_' + inputId);
        if (video) {
            video.srcObject = null;
        }
    }

    function captureSnapshot(inputId) {
        const video = document.getElementById('video_' + inputId);
        if (!video || !video.srcObject) return;

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            if (!blob) return;

            // Create File from Blob and put it in input
            const file = new File([blob], "captured_bill.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById(inputId).files = dataTransfer.files;

            // Show preview image
            const previewImg = document.getElementById('img_preview_' + inputId);
            const previewContainer = document.getElementById('preview_container_' + inputId);
            previewImg.src = URL.createObjectURL(blob);
            previewContainer.style.display = 'block';

            // Update custom file label
            const label = document.getElementById('file_name_label_' + inputId);
            if (label) {
                label.textContent = 'captured_bill.jpg (Chụp từ Camera)';
                label.style.color = '#0f172a';
            }

            // Stop camera
            stopCamera(inputId);
        }, 'image/jpeg', 0.8);
    }

    function removeCapturedPhoto(inputId) {
        document.getElementById(inputId).value = '';
        const label = document.getElementById('file_name_label_' + inputId);
        if (label) {
            label.textContent = 'Chưa chọn tệp nào';
            label.style.color = '#64748b';
        }
        const previewContainer = document.getElementById('preview_container_' + inputId);
        if (previewContainer) {
            previewContainer.style.display = 'none';
        }
        const previewImg = document.getElementById('img_preview_' + inputId);
        if (previewImg) {
            previewImg.src = '';
        }
    }

    let currentDetailAmount = 0;
    let currentDetailServiceName = '';

    const invoiceApartment = "Căn {{ $invoice->apartment->apartment_number ?? '—' }} ({{ optional(optional($invoice->apartment)->floor)->block->name ?? '' }})";
    const invoiceBillingPeriod = "Tháng {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}";

    function confirmManualPayment(event) {
        const amountVal = document.getElementById('amount').value;
        const methodSelect = document.getElementById('payment_method');
        const methodVal = methodSelect.options[methodSelect.selectedIndex].text;
        const payerVal = document.getElementById('payer_name').value || '{{ $invoice->apartment->owner_name ?? 'Cư dân' }}';
        
        const formattedAmount = Number(amountVal).toLocaleString('vi-VN') + ' đ';

        const msg = `Vui lòng xác nhận thông tin thanh toán:\n\n` +
                    `• Căn hộ: ${invoiceApartment}\n` +
                    `• Kỳ hóa đơn: ${invoiceBillingPeriod}\n` +
                    `• Số tiền thu: ${formattedAmount}\n` +
                    `• Phương thức: ${methodVal}\n` +
                    `• Người nộp tiền: ${payerVal}\n\n` +
                    `Bạn có chắc chắn muốn ghi nhận thanh toán này?`;
                    
        return confirm(msg);
    }

    function confirmDetailPayment(event) {
        const methodSelect = document.getElementById('modal_detail_payment_method');
        const methodVal = methodSelect.options[methodSelect.selectedIndex].text;
        const payerVal = document.getElementById('modal_detail_payer_name').value || '{{ $invoice->apartment->owner_name ?? 'Cư dân' }}';
        
        const formattedAmount = Number(currentDetailAmount).toLocaleString('vi-VN') + ' đ';

        const msg = `Vui lòng xác nhận thông tin thanh toán dịch vụ lẻ:\n\n` +
                    `• Căn hộ: ${invoiceApartment}\n` +
                    `• Kỳ hóa đơn: ${invoiceBillingPeriod}\n` +
                    `• Dịch vụ: ${currentDetailServiceName}\n` +
                    `• Số tiền thu: ${formattedAmount}\n` +
                    `• Phương thức: ${methodVal}\n` +
                    `• Người nộp tiền: ${payerVal}\n\n` +
                    `Bạn có chắc chắn muốn ghi nhận thanh toán lẻ này?`;
                    
        return confirm(msg);
    }

    function openDetailPaymentModal(detailId, serviceName, amount) {
        const modal = document.getElementById('detailPaymentModal');
        const form = document.getElementById('detailPaymentForm');
        const info = document.getElementById('modalDetailInfo');

        currentDetailAmount = amount;
        currentDetailServiceName = serviceName;

        form.action = `/admin/invoices/details/${detailId}/mark-paid`;
        info.innerHTML = `Dịch vụ: <strong>${serviceName}</strong><br>Số tiền cần thu: <span style="color:#2563eb;font-weight:700">${Number(amount).toLocaleString('vi-VN')} đ</span>`;

        modal.classList.add('active');
    }

    function closeDetailPaymentModal() {
        stopCamera('modal_detail_proof_image');
        document.getElementById('detailPaymentModal').classList.remove('active');
    }

    // Close detail modal on click overlay
    document.getElementById('detailPaymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailPaymentModal();
        }
    });
</script>
@endsection

