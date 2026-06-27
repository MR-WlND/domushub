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
            <button type="button" class="btn-top-action" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                In hóa đơn
            </button>
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
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Tên khoản phí</th>
                        <th class="text-right">Số lượng</th>
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
                                    @if(isset($detail->servicePrice->unit))
                                        / {{ $detail->servicePrice->unit }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-right val-quantity">
                                {{ $detail->quantity }}
                                @if(isset($detail->servicePrice->unit))
                                    {{ $detail->servicePrice->unit }}
                                @endif
                            </td>
                            <td class="text-right val-subtotal">{{ number_format($detail->amount, 0, ',', '.') }} đ</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Không có thông tin chi tiết khoản phí.</td>
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
                <form method="POST" action="{{ route('resident.invoices.pay') }}">
                    @csrf
                    <input type="hidden" name="invoice_ids[]" value="{{ $invoice->id }}">
                    <button type="submit" class="btn-pay-now">
                        Thanh toán ngay qua VNPay
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@include('resident.invoices.partials.style')
@endsection
