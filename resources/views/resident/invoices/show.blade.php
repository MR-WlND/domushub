@extends('layouts.resident.master')

@section('title', 'Thanh toán hóa đơn – DomusHub')

@section('content')
<div class="pay-dashboard">
    <div class="pay-dashboard__header">
        <h1 class="pay-page__title">Tổng quan Hóa đơn</h1>
        <p class="pay-page__subtitle">Quản lý các khoản phí định kỳ cho căn hộ của bạn.</p>
    </div>

    @if(session('error'))
        <div class="pay-alert pay-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('success'))
        <div class="pay-alert pay-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('resident.invoices.pay') }}" id="payment-form">
        @csrf
        <div class="pay-grid">
            {{-- Cột Trái --}}
            <div class="pay-col-left">
                
                {{-- Thẻ: Danh sách hóa đơn chưa thanh toán --}}
                <div class="pay-card">
                    <div class="pay-card__header flex-between">
                        <h2 class="pay-card__title">Hóa đơn chưa thanh toán</h2>
                        @if($invoices->isNotEmpty())
                        <label class="pay-checkbox-select-all">
                            <input type="checkbox" id="select-all" checked>
                            <span class="pay-checkbox-box"><i class="fa-solid fa-check"></i></span>
                            <span class="pay-checkbox-label">Chọn tất cả</span>
                        </label>
                        @endif
                    </div>

                    @if($invoices->isEmpty())
                        <div class="pay-empty">
                            <div class="pay-empty__icon"><i class="fa-solid fa-check-circle"></i></div>
                            <h3 class="pay-empty__title">Tuyệt vời!</h3>
                            <p class="pay-empty__desc">Bạn không có khoản phí nào cần thanh toán lúc này.</p>
                        </div>
                    @else
                        <div class="pay-table-wrapper">
                            <table class="pay-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">CHỌN</th>
                                        <th>HÓA ĐƠN</th>
                                        <th>KỲ HẠN</th>
                                        <th class="text-right">SỐ TIỀN (VNĐ)</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        @php
                                            $monthStr = str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT);
                                            $yearStr = $invoice->billing_year;
                                            $serviceName = $invoice->title;
                                        @endphp
                                        <tr id="pay-row-{{ $invoice->id }}" class="pay-row {{ $invoice->status === 'overdue' ? 'row-overdue' : '' }}" onclick="toggleDetails({{ $invoice->id }})" style="cursor: pointer;">
                                            <td class="td-check" onclick="event.stopPropagation();">
                                                <label class="pay-checkbox-item">
                                                    <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}" checked class="invoice-checkbox" data-id="{{ $invoice->id }}" data-amount="{{ $invoice->total_amount - $invoice->paid_amount }}" data-name="{{ $invoice->title }}">
                                                    <span class="pay-checkbox-box"><i class="fa-solid fa-check"></i></span>
                                                </label>
                                            </td>
                                            <td class="td-service">
                                                <div class="pay-service-cell" style="display: flex; align-items: center; gap: 12px;">
                                                    <div class="desktop-only" style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                                        <i class="fa-solid fa-file-invoice"></i>
                                                    </div>
                                                    <div style="display: flex; flex-direction: column; justify-content: center;">
                                                        <div style="font-weight: 600; color: #0f172a; font-size: 1rem; margin-bottom: 2px;">
                                                            <span class="desktop-only">{{ $serviceName }}</span>
                                                            <span class="mobile-only" style="font-weight: 700; font-size: 1.05rem;">{{ str_replace('Hóa đơn ', '', $serviceName) }}</span>
                                                        </div>
                                                        <div class="desktop-only" style="color: #64748b; font-size: 0.85rem;">
                                                            Căn hộ: {{ $invoice->apartment->apartment_number ?? 'N/A' }}
                                                        </div>
                                                        <div class="mobile-only" style="color: #64748b; font-size: 0.85rem; margin-top: 2px;">
                                                            Hạn: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="desktop-only" style="font-size: 0.9rem;">
                                                <div style="color: #64748b; font-size: 0.9rem; margin-bottom: 2px;">Kỳ hạn: Tháng {{ $monthStr }}/{{ $yearStr }}</div>
                                                <div style="color: #475569; font-size: 0.85rem;">Hạn: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') }}</div>
                                            </td>
                                            <td class="mobile-only td-meta-mobile" style="display: none;">
                                                {{-- Empty grid area for mobile meta --}}
                                            </td>
                                            <td class="td-price">
                                                <div class="pay-amount-cell" style="display: flex; flex-direction: column; align-items: flex-end; text-align: right; width: 100%;">
                                                    @if($invoice->status === 'partial' || $invoice->status === 'partial_paid')
                                                        <div class="desktop-only" style="font-size: 0.85rem; color: #64748b; line-height: 1.4; margin-bottom: 8px; white-space: nowrap;">
                                                            <div>Tổng: {{ number_format($invoice->total_amount, 0, ',', '.') }}đ</div>
                                                            <div>Đã thanh toán: -{{ number_format($invoice->paid_amount, 0, ',', '.') }}đ</div>
                                                        </div>
                                                        <div class="mobile-only" style="font-size: 0.85rem; color: #64748b; margin-bottom: 2px; white-space: nowrap; text-decoration: line-through;">
                                                            {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                                                        </div>
                                                        
                                                        <div style="color: #dc2626; font-size: 1.1rem; font-weight: 700; margin-bottom: 6px; white-space: nowrap;">
                                                            <span class="desktop-only">Còn lại: </span>{{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }} đ
                                                        </div>
                                                        <span class="pay-status-badge badge-partial" style="white-space: nowrap;">THANH TOÁN 1 PHẦN</span>
                                                    @else
                                                        <div class="pay-amount-val" style="white-space: nowrap; font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                                                            {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }} đ
                                                        </div>
                                                        @if($invoice->status === 'overdue')
                                                            <span class="pay-status-badge badge-overdue" style="white-space: nowrap;">QUÁ HẠN</span>
                                                        @else
                                                            <span class="pay-status-badge badge-unpaid" style="white-space: nowrap;">CHƯA THANH TOÁN</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="desktop-only text-center text-muted">
                                                <i class="fa-solid fa-chevron-down" id="arrow-{{ $invoice->id }}" style="transition: transform 0.2s;"></i>
                                            </td>
                                            <td class="mobile-only td-chevron-mobile" style="display: none;">
                                                <div onclick="toggleDetails({{ $invoice->id }}); event.stopPropagation();" style="display: flex; flex-direction: column; align-items: flex-start; justify-content: flex-end; height: 100%; color: #00236f; font-size: 0.9rem; font-weight: 600; padding-left: 0px; cursor: pointer; position: relative; z-index: 10;" class="toggle-text-container">
                                                    <span class="toggle-text" id="toggle-text-{{ $invoice->id }}">Xem chi tiết <i class="fa-solid fa-angle-down" id="arrow-mobile-{{ $invoice->id }}" style="font-size: 0.8rem; margin-left: 4px; transition: transform 0.2s;"></i></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="details-{{ $invoice->id }}" style="display: none; background: #f8fafc;">
                                            <td colspan="5" style="padding: 16px 24px;">
                                                <table style="width: 100%; border-collapse: collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 40px; padding: 8px; border-bottom: 1px solid #e2e8f0;"></th>
                                                            <th style="text-align: left; padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; color: #64748b; font-weight: 600;">KHOẢN PHÍ</th>
                                                            <th style="text-align: right; padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; color: #64748b; font-weight: 600;">SỐ LƯỢNG</th>
                                                            <th style="text-align: right; padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; color: #64748b; font-weight: 600;">THÀNH TIỀN</th>
                                                            <th style="text-align: right; padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 0.75rem; color: #64748b; font-weight: 600;">TRẠNG THÁI</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($invoice->details as $detail)
                                                            @if($detail->amount > 0)
                                                                <tr style="{{ $detail->status === 'paid' ? 'opacity: 0.6;' : '' }}">
                                                                    <td style="padding: 12px 8px; border-bottom: 1px solid #f1f5f9; text-align: center;">
                                                                        @if($detail->status !== 'paid')
                                                                        {{-- Checkbox is always in DOM (needed by JS) but hidden visually on mobile --}}
                                                                        <input type="checkbox" name="detail_ids[]" value="{{ $detail->id }}" class="item-check child-of-{{ $invoice->id }}" data-parent="{{ $invoice->id }}" data-invoice-title="{{ $invoice->title }}" data-amount="{{ $detail->amount }}" data-name="{{ $detail->servicePrice->name ?? 'Dịch vụ' }} - T{{ $invoice->billing_month->month }}" checked onclick="event.stopPropagation();" style="cursor: pointer;">
                                                                        @endif
                                                                    </td>
                                                                    <td style="padding: 12px 8px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #334155; {{ $detail->status === 'paid' ? 'text-decoration: line-through;' : '' }}">
                                                                        {{ $detail->servicePrice->name ?? 'Dịch vụ' }}
                                                                        @if($detail->status === 'paid')
                                                                            <span class="mobile-only">(Đã thanh toán)</span>
                                                                        @endif
                                                                        @if($detail->servicePrice && $detail->servicePrice->type === 'water')
                                                                            <button type="button" class="btn-complaint" onclick="openComplaintModal({{ $invoice->id }}, '{{ $invoice->title }}', {{ $invoice->billing_month->month }}, {{ $invoice->billing_year }})" title="Khiếu nại chỉ số nước" style="margin-left: 8px; background: transparent; border: none; color: #dc2626; cursor: pointer; padding: 4px; font-size: 0.75rem; font-weight: 600;">
                                                                                <i class="fa-solid fa-circle-exclamation"></i> Khiếu nại
                                                                            </button>
                                                                        @endif
                                                                    </td>
                                                                    <td class="desktop-only" style="padding: 12px 8px; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 0.85rem; color: #334155; {{ $detail->status === 'paid' ? 'text-decoration: line-through;' : '' }}">
                                                                        {{ $detail->quantity }} {{ $detail->servicePrice->unit ?? '' }}
                                                                    </td>
                                                                    <td style="padding: 12px 8px; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 0.85rem; font-weight: 600; color: #0f172a; {{ $detail->status === 'paid' ? 'text-decoration: line-through;' : '' }}">
                                                                        {{ number_format($detail->amount, 0, ',', '.') }} đ
                                                                    </td>
                                                                    <td class="desktop-only" style="padding: 12px 8px; border-bottom: 1px solid #f1f5f9; text-align: right;">
                                                                        @if($detail->status === 'paid')
                                                                            <span style="background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem;">Đã thanh toán</span>
                                                                        @else
                                                                            <span style="background: #fef9c3; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem;">Chưa thanh toán</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="pay-card__footer">
                        <a href="{{ route('resident.invoices.history') }}" class="btn-history-link">
                            <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử giao dịch
                        </a>
                    </div>
                </div>



            </div>

            {{-- Cột Phải --}}
            <div class="pay-col-right">
                
                {{-- Top Cards (Dư nợ & Hạn chót) --}}
                @php
                    $earliestDueDate = $invoices->min('due_date');
                    $earliestDateStr = $earliestDueDate ? \Carbon\Carbon::parse($earliestDueDate)->format('d/m/Y') : 'Không có';
                @endphp
                
                {{-- Desktop Top Cards --}}
                <div class="pay-top-cards desktop-only">
                    <div class="pay-mini-card">
                        <div class="pay-mini-card__label">DƯ NỢ HIỆN TẠI</div>
                        <div class="pay-mini-card__val text-danger">{{ number_format($totalDebt, 0, ',', '.') }} <span>đ</span></div>
                    </div>
                    <div class="pay-mini-card" title="Số lượng hóa đơn bạn chưa hoàn tất thanh toán">
                        <div class="pay-mini-card__label">CHƯA THANH TOÁN</div>
                        <div class="pay-mini-card__val">{{ $invoices->count() }}</div>
                    </div>
                </div>

                {{-- Mobile Top Cards --}}
                <div class="pay-top-cards mobile-only">
                    <div class="pay-mini-card pay-mini-card--primary">
                        <div class="pay-mini-card__label">DƯ NỢ HIỆN TẠI</div>
                        <div class="pay-mini-card__val">{{ number_format($totalDebt, 0, ',', '.') }} <span>đ</span></div>
                    </div>
                    <div class="pay-mini-card pay-mini-card--outline">
                        <div class="pay-mini-card__label">CHƯA THANH TOÁN</div>
                        <div class="pay-mini-card__val" style="color: #0b1c30;">{{ $invoices->count() }}</div>
                    </div>
                </div>

                {{-- Thẻ Tổng kết thanh toán --}}
                <div class="pay-summary-card">
                    <div class="pay-summary__header">
                        <i class="fa-solid fa-receipt"></i> Tổng kết thanh toán
                    </div>

                    <div class="pay-summary__list" id="summary-list">
                        <!-- Items will be injected here via JS -->
                    </div>

                    <div class="pay-summary__subtotal flex-between">
                        <span id="summary-count-text">Tạm tính (0 mục)</span>
                        <span class="val-bold" id="summary-subtotal-val">0 đ</span>
                    </div>

                    <div class="pay-summary__total-box">
                        <div class="total-label">TỔNG TIỀN CẦN THANH TOÁN</div>
                        <div class="total-value" id="summary-total-val">0 <span>đ</span></div>
                    </div>

                    <button type="button" class="btn-pay-submit" id="pay-btn" disabled>
                        Tiến hành thanh toán <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <div class="pay-secure-text">
                        <i class="fa-solid fa-lock"></i> Thanh toán an toàn với mã hóa SSL
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- Popup Xác nhận thanh toán --}}
<div id="paymentConfirmModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:99999; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s ease;">
    <div style="background:#fff; border-radius:8px; width:100%; max-width:420px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); transform:scale(0.95); transition:transform 0.2s ease;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:1.1rem; font-weight:600; color:#1e293b; margin:0;">Xác nhận thanh toán</h3>
            <button type="button" onclick="closePaymentModal()" style="background:none; border:none; font-size:1.25rem; color:#64748b; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="padding: 24px;">
            <p style="color:#334155; font-size:0.95rem; margin:0 0 16px 0; line-height:1.5;">
                Quý khách đang thực hiện thanh toán thông qua cổng VNPay. Vui lòng kiểm tra lại số tiền trước khi tiếp tục.
            </p>
            <div style="background:#f8fafc; padding:12px 16px; border-radius:6px; border:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                <span style="color:#475569; font-size:0.9rem;">Tổng số tiền:</span>
                <strong id="modal-amount-val" style="color:#dc2626; font-size:1.15rem;">0 đ</strong>
            </div>
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; gap:12px; justify-content:flex-end; background:#f8fafc; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">
            <button type="button" onclick="closePaymentModal()" style="padding:8px 16px; border-radius:6px; border:1px solid #cbd5e1; background:#fff; color:#475569; font-weight:500; font-size:0.9rem; cursor:pointer; transition:background 0.2s;">Hủy bỏ</button>
            <button type="button" onclick="submitPaymentForm()" style="padding:8px 16px; border-radius:6px; border:none; background:#2563eb; color:#fff; font-weight:500; font-size:0.9rem; cursor:pointer; transition:background 0.2s;">Thanh toán</button>
        </div>
    </div>
</div>

{{-- Popup Khiếu nại chỉ số nước --}}
<div id="complaintModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:99999; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s ease;">
    <div style="background:#fff; border-radius:8px; width:100%; max-width:420px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); transform:scale(0.95); transition:transform 0.2s ease;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:1.1rem; font-weight:600; color:#1e293b; margin:0;">Khiếu nại chỉ số nước</h3>
            <button type="button" onclick="closeComplaintModal()" style="background:none; border:none; font-size:1.25rem; color:#64748b; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('resident.invoices.complain-water') }}" id="complaint-form">
            @csrf
            <input type="hidden" name="invoice_id" id="complaint_invoice_id">
            <div style="padding: 24px;">
                <p style="color:#334155; font-size:0.95rem; margin:0 0 16px 0; line-height:1.5;">
                    Vui lòng cung cấp lý do bạn thấy chỉ số nước <strong id="complaint_invoice_title"></strong> chưa chính xác. Yêu cầu của bạn sẽ được gửi tới Ban quản lý.
                </p>
                <div style="margin-bottom: 12px;">
                    <textarea name="complaint_reason" rows="4" placeholder="Nhập lý do chi tiết..." required style="width:100%; border:1px solid #cbd5e1; border-radius:6px; padding:10px; font-family:inherit; font-size:0.95rem; resize:vertical;"></textarea>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid #e2e8f0; display:flex; gap:12px; justify-content:flex-end; background:#f8fafc; border-bottom-left-radius:8px; border-bottom-right-radius:8px;">
                <button type="button" onclick="closeComplaintModal()" style="padding:8px 16px; border-radius:6px; border:1px solid #cbd5e1; background:#fff; color:#475569; font-weight:500; font-size:0.9rem; cursor:pointer; transition:background 0.2s;">Hủy bỏ</button>
                <button type="submit" style="padding:8px 16px; border-radius:6px; border:none; background:#dc2626; color:#fff; font-weight:500; font-size:0.9rem; cursor:pointer; transition:background 0.2s;">Gửi khiếu nại</button>
            </div>
        </form>
    </div>
</div>

@include('resident.invoices.partials.style')

@include('resident.invoices.partials.script')
<script>
    function toggleDetails(id) {
        const payRow = document.getElementById('pay-row-' + id);
        const row = document.getElementById('details-' + id);
        const arrow = document.getElementById('arrow-' + id);
        const textMobile = document.getElementById('toggle-text-' + id);
        
        if (row.style.display === 'none') {
            row.style.display = 'table-row';
            if (payRow) payRow.classList.add('is-expanded');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
            if (textMobile) textMobile.innerHTML = 'Thu gọn <i class="fa-solid fa-angle-up" style="font-size: 0.8rem; margin-left: 4px; transition: transform 0.2s;"></i>';
        } else {
            row.style.display = 'none';
            if (payRow) payRow.classList.remove('is-expanded');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
            if (textMobile) textMobile.innerHTML = 'Xem chi tiết <i class="fa-solid fa-angle-down" style="font-size: 0.8rem; margin-left: 4px; transition: transform 0.2s;"></i>';
        }
    }

    // Modal Logic
    document.getElementById('pay-btn').addEventListener('click', function() {
        const totalText = document.getElementById('summary-total-val').textContent;
        document.getElementById('modal-amount-val').innerHTML = totalText;
        
        const modal = document.getElementById('paymentConfirmModal');
        const modalContent = modal.querySelector('div');
        
        modal.style.display = 'flex';
        // Trigger reflow for animation
        void modal.offsetWidth;
        modal.style.opacity = '1';
        modalContent.style.transform = 'scale(1) translateY(0)';
    });

    window.closePaymentModal = function() {
        const modal = document.getElementById('paymentConfirmModal');
        const modalContent = modal.querySelector('div');
        
        modal.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    };

    window.submitPaymentForm = function() {
        document.getElementById('payment-form').submit();
    };

    // Complaint Modal Logic
    window.openComplaintModal = function(invoiceId, title, month, year) {
        document.getElementById('complaint_invoice_id').value = invoiceId;
        document.getElementById('complaint_invoice_title').innerText = title;
        
        const modal = document.getElementById('complaintModal');
        const modalContent = modal.querySelector('div');
        
        modal.style.display = 'flex';
        // Trigger reflow for animation
        void modal.offsetWidth;
        modal.style.opacity = '1';
        modalContent.style.transform = 'scale(1) translateY(0)';
    };

    window.closeComplaintModal = function() {
        const modal = document.getElementById('complaintModal');
        const modalContent = modal.querySelector('div');
        
        modal.style.opacity = '0';
        modalContent.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    };
</script>
@endsection
