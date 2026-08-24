@extends('layouts.resident.master')

@section('title', 'Chi tiết hóa đơn – DomusHub')

@section('content')
<div class="invoice-detail-page">
    {{-- Header with back button --}}
    <div class="detail-header-row">
        <a href="{{ $invoice->status === 'paid' ? route('resident.invoices.history') : route('resident.invoices.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại danh sách
        </a>
        <div class="detail-actions">
            <a href="{{ route('resident.invoices.print', $invoice->id) }}" target="_blank" class="btn-top-action" style="text-decoration: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                In hóa đơn (PDF)
            </a>
        </div>
    </div>

    {{-- Main Detail Card --}}
    <div class="detail-card">
        {{-- Invoice Title & Status Banner --}}
        <div class="detail-card__header">
            <div>
                <span class="invoice-code">{{ $invoice->invoice_code }}</span>
                <h1 class="invoice-title">{{ $invoice->title }}</h1>
                <p class="invoice-subtitle">
                    Căn hộ: {{ $invoice->apartment->apartment_number ?? '—' }} 
                    ({{ optional(optional($invoice->apartment)->floor)->block->name ?? '' }})
                </p>
            </div>
            <div class="invoice-status-wrap">
                @if($invoice->status === 'paid')
                    <span class="pay-badge pay-badge--paid">Đã thanh toán</span>
                @elseif($invoice->status === 'overdue')
                    <span class="pay-badge pay-badge--overdue">Quá hạn</span>
                @else
                    <span class="pay-badge pay-badge--unpaid">Chờ thanh toán</span>
                @endif
            </div>
        </div>

        {{-- Metadata Row --}}
        <div class="detail-card__meta">
            <div class="meta-item">
                <span class="meta-label">Kỳ thanh toán</span>
                <span class="meta-val">Tháng {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</span>
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
            <h3 class="section-title">Chi tiết khoản phí</h3>
            <form id="pay-details-form" method="POST" action="{{ route('resident.invoices.pay-details') }}">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            <table class="items-table">
                <thead>
                    <tr>
                        @if($invoice->status !== 'paid')
                            <th style="width: 40px; text-align: center;">
                                <input type="checkbox" id="check-all-details" class="detail-checkbox" checked>
                            </th>
                        @endif
                        <th>Tên khoản phí</th>
                        <th class="text-right">Số lượng</th>
                        <th class="text-right">Thành tiền</th>
                        <th class="text-right">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->details as $detail)
                        <tr class="{{ $detail->status === 'paid' ? 'paid-row' : '' }}">
                            @if($invoice->status !== 'paid')
                                <td style="text-align: center;">
                                    @if($detail->status !== 'paid')
                                        <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" class="detail-checkbox item-check" data-amount="{{ $detail->amount }}" checked>
                                    @endif
                                </td>
                            @endif
                            <td data-label="Tên khoản phí">
                                <div class="item-name">{{ $detail->servicePrice->name ?? 'Dịch vụ / Phí khác' }}</div>
                                <div class="item-desc">
                                    Đơn giá: {{ number_format($detail->servicePrice->price ?? $detail->amount) }} đ
                                    @if(isset($detail->servicePrice->unit))
                                        / {{ $detail->servicePrice->unit }}
                                    @endif
                                </div>
                                @if(in_array(optional($detail->servicePrice)->type, ['water']))
                                    @php
                                        $meter = \App\Models\UtilityMeter::where('apartment_id', $invoice->apartment_id)
                                            ->where('type', $detail->servicePrice->type)
                                            ->where('record_month', $invoice->billing_month->month)
                                            ->where('record_year', $invoice->billing_year)
                                            ->first();
                                    @endphp
                                    @if($meter)
                                        <div class="item-meter-info" style="font-size: 0.8rem; color: #64748b; margin-top: 4px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                            <div>
                                                Chỉ số cũ: <strong>{{ $meter->old_value }}</strong> &nbsp;|&nbsp;
                                                Chỉ số mới: <strong>{{ $meter->new_value }}</strong> &nbsp;|&nbsp;
                                                Tiêu thụ: <strong>{{ $meter->usage_amount }}</strong> {{ $detail->servicePrice->type === 'water' ? 'm³' : 'kWh' }}
                                            </div>
                                            @if($detail->servicePrice->type === 'water')
                                                <button type="button" class="btn-complaint" onclick="sendWaterComplaint(event, '{{ route('resident.invoices.complaint-water', $invoice->id) }}')" style="background: none; border: none; color: #dc2626; font-size: 0.8rem; font-weight: 600; cursor: pointer; text-decoration: underline; padding: 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px; vertical-align: middle; display: inline-block;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>Khiếu nại chỉ số
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td data-label="Số lượng" class="text-right val-quantity">
                                {{ $detail->quantity }}
                                @if(isset($detail->servicePrice->unit))
                                    {{ $detail->servicePrice->unit }}
                                @endif
                            </td>
                            <td data-label="Thành tiền" class="text-right val-subtotal">{{ number_format($detail->amount, 0, ',', '.') }} đ</td>
                            <td data-label="Trạng thái" class="text-right">
                                @if($detail->status === 'paid')
                                    <span class="badge" style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">Đã thanh toán</span>
                                @else
                                    <span class="badge" style="background: #fef9c3; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">Chưa thanh toán</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $invoice->status !== 'paid' ? '5' : '4' }}" class="text-center text-muted">Không có thông tin chi tiết khoản phí.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        </div>

        {{-- Total Row --}}
        <div class="detail-card__total">
            <span class="total-label">Tổng cộng</span>
            <span class="total-val">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</span>
        </div>
        
        @if($invoice->status !== 'paid')
        <div class="detail-card__selected-total" style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <span class="total-label" style="font-weight: 600; color: #475569;">Đang chọn thanh toán</span>
            <span class="total-val" id="selected-total-val" style="font-size: 1.25rem; font-weight: 700; color: #0d9488;">0 đ</span>
        </div>
        @endif

        {{-- Payment History Info --}}
        <div class="detail-card__payment">
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
            @forelse($invoice->payments as $payment)
                @php 
                    // Phương thức thanh toán
                    $methodLabel = match($payment->payment_method) {
                        'vnpay'         => 'VNPay',
                        'bank_transfer' => 'Chuyển khoản ngân hàng',
                        'cash'          => 'Tiền mặt (Nộp trực tiếp)',
                        default         => ucfirst($payment->payment_method),
                    };

                    // Mã giao dịch VNPay
                    if ($payment->vnp_txn_ref) {
                        $vnpTxnNo = $payment->vnp_txn_ref;
                    } elseif (str_contains($payment->transaction_code ?? '', '|')) {
                        $vnpTxnNo = explode('|', $payment->transaction_code)[0];
                    } else {
                        $vnpTxnNo = $payment->transaction_code ?? '—';
                    }
                @endphp
                <div class="payment-transaction" style="margin-bottom: 24px;">
                    <div class="payment-info-item">
                        <span class="info-label">Mã hóa đơn hệ thống:</span>
                        <span class="info-val code-val">{{ $invoice->invoice_code }}</span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Số tiền thanh toán:</span>
                        <span class="info-val">{{ number_format($payment->amount, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Phương thức thanh toán:</span>
                        <span class="info-val">{{ $methodLabel }}</span>
                    </div>
                    <div class="payment-info-item">
                        <span class="info-label">Người nộp (Cư dân):</span>
                        <span class="info-val">{{ $payment->payer_name ?: ($invoice->apartment->owner_name ?? 'Cư dân căn hộ') }}</span>
                    </div>
                    @if($payment->recorder && $payment->recorder->name !== '—')
                    <div class="payment-info-item">
                        <span class="info-label">Người thu (Nhân viên):</span>
                        <span class="info-val">{{ $payment->recorder->name }}</span>
                    </div>
                    @endif
                    @if($payment->payment_method === 'vnpay')
                    <div class="payment-info-item">
                        <span class="info-label">Mã giao dịch đối soát (VNPay):</span>
                        <span class="info-val code-val">{{ $vnpTxnNo ?: '—' }}</span>
                    </div>
                    @endif
                    <div class="payment-info-item">
                        <span class="info-label">Thời gian thanh toán:</span>
                        <span class="info-val">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}</span>
                    </div>
                    @php
                        $displayNote = $payment->note;
                        if (empty($displayNote)) {
                            $allocatedServices = $detailAllocations[$payment->id] ?? [];
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
                    @if($payment->proof_image)
                    <div class="payment-info-item">
                        <span class="info-label">Ảnh hóa đơn (bill):</span>
                        <span class="info-val">
                            <a href="{{ asset('storage/' . $payment->proof_image) }}" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: underline;">
                                Xem ảnh hóa đơn
                            </a>
                        </span>
                    </div>
                    @endif
                </div>
            @empty
                <div style="font-size: 0.85rem; color: #64748b; padding: 15px 0; text-align: center;">
                    Chưa có giao dịch nào được ghi nhận cho hóa đơn này.
                </div>
            @endforelse
        </div>

        {{-- Pay Button (Only if Unpaid/Overdue) --}}
        @if($invoice->status !== 'paid')
            <div class="detail-card__pay-action">
                <button type="submit" class="btn-pay-now" id="btn-submit-pay">
                    Thanh toán mục đã chọn qua VNPay
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </div>
            </form>
        @endif
    </div>
</div>

@include('resident.invoices.partials.style')

@push('styles')
<style>
@media (max-width: 768px) {
    .invoice-detail-page {
        padding: 16px;
    }
    
    .detail-header-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .detail-card__header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .detail-card__meta {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    /* Transform items-table to cards */
    .items-table, .items-table tbody, .items-table tr, .items-table td {
        display: block;
        width: 100%;
    }
    
    .items-table thead {
        display: none;
    }
    
    .items-table tr {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 16px;
        padding: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    
    .items-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0 !important;
        border-bottom: 1px dashed #e2e8f0 !important;
        text-align: right;
    }
    
    .items-table td:last-child {
        border-bottom: none !important;
    }
    
    .items-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #64748b;
        text-align: left;
        margin-right: 16px;
        flex-shrink: 0;
        font-size: 0.85rem;
    }
    
    .items-table td[style*="text-align: center;"] {
        display: flex;
        justify-content: flex-start;
    }
    
    .items-table td[style*="text-align: center;"]::before {
        content: "Chọn thanh toán";
    }

    .items-table td[data-label="Tên khoản phí"] {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .items-table td[data-label="Tên khoản phí"]::before {
        margin-bottom: 8px;
    }

    .detail-card__total {
        flex-direction: column;
        gap: 8px;
        align-items: flex-end;
    }
    
    .payment-info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }
    
    .info-label {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all-details');
        const itemChecks = document.querySelectorAll('.item-check');
        const totalValEl = document.getElementById('selected-total-val');
        const btnSubmit = document.getElementById('btn-submit-pay');

        function updateTotal() {
            let total = 0;
            let checkedCount = 0;
            itemChecks.forEach(check => {
                if(check.checked) {
                    total += parseFloat(check.dataset.amount);
                    checkedCount++;
                }
            });

            if(totalValEl) {
                totalValEl.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
            }

            if(checkAll) {
                checkAll.checked = (checkedCount === itemChecks.length && itemChecks.length > 0);
            }
            
            if(btnSubmit) {
                btnSubmit.disabled = checkedCount === 0;
                btnSubmit.style.opacity = checkedCount === 0 ? '0.5' : '1';
                btnSubmit.style.cursor = checkedCount === 0 ? 'not-allowed' : 'pointer';
            }
        }

        if(checkAll) {
            checkAll.addEventListener('change', function() {
                itemChecks.forEach(check => {
                    check.checked = this.checked;
                });
                updateTotal();
            });
        }

        itemChecks.forEach(check => {
            check.addEventListener('change', updateTotal);
        });

        // Initialize total
        updateTotal();
    });

    async function sendWaterComplaint(event, url) {
        event.preventDefault();
        if (!confirm('Bạn có chắc chắn muốn gửi khiếu nại về chỉ số nước này tới Ban Quản Lý không?')) {
            return;
        }
        
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Đang gửi...';
        
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                btn.innerHTML = 'Đã gửi khiếu nại';
                btn.style.color = '#16a34a';
                btn.style.textDecoration = 'none';
                btn.onclick = null;
            } else {
                alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                btn.disabled = false;
                btn.innerHTML = originalContent;
            }
        } catch(err) {
            console.error(err);
            alert('Không thể kết nối máy chủ.');
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    }
</script>
@endpush

@endsection
