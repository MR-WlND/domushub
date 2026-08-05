@extends('layouts.admin.master')

@section('page_title', 'Thu tiền thủ công')

@push('styles')
<style>
/* ===== Layout Wrapper ===== */
.mp-wrap {
    padding: 24px 28px;
    background: #fff;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

/* ===== Page Header ===== */
.mp-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 22px;
}
.mp-header__title {
    font-size: 22px;
    font-weight: 700;
    color: #1a2236;
    margin: 0 0 4px;
}
.mp-header__sub {
    font-size: 13px;
    color: #7a8399;
    margin: 0;
}

/* ===== Card Base ===== */
.mp-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8ecf1;
    padding: 20px 22px;
    margin-bottom: 18px;
}

/* ===== Top Row ===== */
.mp-top-row {
    margin-bottom: 18px;
}
.mp-resident-card {
    margin-bottom: 0;
}

/* ===== Alert ===== */
.mp-alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: 500;
}
.mp-alert--success {
    background: #e8f5e9;
    color: #2e7d32;
    border-left: 4px solid #43a047;
}
.mp-alert--error {
    background: #fdecea;
    color: #c62828;
    border-left: 4px solid #e53935;
}

/* Xóa CSS cũ của ô tìm kiếm */

/* ===== Resident Card ===== */
.mp-resident-info {
    display: flex;
    align-items: center;
    gap: 16px;
}
.mp-resident-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    background: #e8ecf1;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    color: #a0abc0;
    border: 2px solid #e0e7ff;
}
.mp-resident-avatar img {
    width: 60px; height: 60px;
    border-radius: 50%; object-fit: cover;
}
.mp-resident-name {
    font-size: 18px;
    font-weight: 700;
    color: #1a2236;
    margin: 0 0 4px;
}
.mp-resident-role {
    font-size: 13px;
    color: #7a8399;
    margin: 0 0 8px;
}
.mp-resident-apt-badge {
    display: inline-block;
    background: #eff6ff;
    color: #2563eb;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    padding: 3px 10px;
    border: 1px solid #bfdbfe;
}
.mp-resident-contacts {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 8px;
    font-size: 13px;
    color: #3d4a60;
}
.mp-resident-contacts span { display: flex; align-items: center; gap: 6px; }
.mp-resident-contacts i { color: #3b82f6; width: 14px; }

/* ===== Invoices Table ===== */
.mp-invoices-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}
.mp-invoices-header__icon {
    width: 30px; height: 30px;
    border-radius: 7px;
    background: #eff6ff;
    display: flex; align-items: center; justify-content: center;
    color: #3b82f6;
    font-size: 14px;
}
.mp-invoices-header__title {
    font-size: 15px;
    font-weight: 700;
    color: #1a2236;
    margin: 0;
}
.mp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.mp-table th {
    background: #f8faff;
    color: #7a8399;
    font-weight: 600;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 10px 12px;
    border-bottom: 2px solid #e8ecf1;
    text-align: left;
}
.mp-table td {
    padding: 13px 12px;
    border-bottom: 1px solid #f0f2f7;
    color: #1a2236;
    vertical-align: middle;
}
.mp-table tr:last-child td { border-bottom: none; }
.mp-table tr:hover td { background: #f8faff; }
.mp-table input[type="checkbox"] {
    width: 16px; height: 16px;
    accent-color: #3b82f6;
    cursor: pointer;
}
.mp-invoice-code { font-weight: 600; color: #2563eb; text-decoration: none; display: inline-flex; align-items: center; }
.mp-invoice-code:hover { text-decoration: underline; color: #1d4ed8; }
.mp-invoice-desc { font-size: 12px; color: #a0abc0; margin-top: 2px; }
.mp-amount { font-weight: 700; text-align: right; font-size: 14px; }

/* ===== Status Badges ===== */
.mp-badge {
    display: inline-block;
    padding: 3px 11px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.mp-badge--overdue   { background: #fff1f0; color: #e53935; border: 1px solid #ffcdd2; }
.mp-badge--unpaid    { background: #fffde7; color: #f59e0b; border: 1px solid #ffe082; }
.mp-badge--partial   { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }

/* ===== Payment Form ===== */
.mp-payment-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.mp-payment-title {
    font-size: 15px;
    font-weight: 700;
    color: #1a2236;
    margin: 0;
}
.mp-payment-total-label { font-size: 13px; color: #7a8399; }
.mp-payment-total-value {
    font-size: 17px;
    font-weight: 700;
    color: #2563eb;
}
.mp-payment-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}
.mp-method-label, .mp-field-label {
    font-size: 13px;
    font-weight: 600;
    color: #3d4a60;
    margin-bottom: 10px;
    display: block;
}
.mp-method-group {
    display: flex;
    gap: 12px;
}
.mp-method-btn {
    flex: 1;
    border: 1.5px solid #d1d9e6;
    background: #fff;
    border-radius: 10px;
    padding: 12px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #3d4a60;
    transition: all .2s;
}
.mp-method-btn i { font-size: 20px; color: #a0abc0; }
.mp-method-btn:hover { border-color: #3b82f6; color: #2563eb; }
.mp-method-btn:hover i { color: #3b82f6; }
.mp-method-btn.active {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #2563eb;
}
.mp-method-btn.active i { color: #3b82f6; }
.mp-textarea, .mp-input {
    width: 100%;
    border: 1.5px solid #d1d9e6;
    border-radius: 8px;
    font-size: 13.5px;
    color: #1a2236;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.mp-textarea {
    height: 80px;
    padding: 10px 12px;
    resize: vertical;
}
.mp-input { padding: 0 14px; height: 44px; }
.mp-textarea:focus, .mp-input:focus { border-color: #3b82f6; }
.mp-field-hint { font-size: 11.5px; color: #a0abc0; margin-top: 5px; }
.mp-amount-field { margin-top: 16px; }
.mp-btn-row {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}
.mp-btn {
    padding: 10px 22px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .2s;
}
.mp-btn--outline {
    background: #fff;
    border: 1.5px solid #d1d9e6;
    color: #3d4a60;
}
.mp-btn--outline:hover { border-color: #3b82f6; color: #2563eb; }
.mp-btn--primary {
    background: #2563eb;
    color: #fff;
}
.mp-btn--primary:hover { background: #1d4ed8; }

/* Photo Upload Box */
.mp-photo-box {
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    background: #f8fafc;
    padding: 6px;
    transition: all 0.2s ease;
    cursor: pointer;
}
.mp-photo-box:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}
</style>
@endpush


@section('content')
<div class="mp-wrap">

    {{-- Page Header --}}
    <div class="mp-header">
        <div>
            <h1 class="mp-header__title">Thanh toán cho căn hộ: {{ $apartment->apartment_number }}</h1>
            <p class="mp-header__sub">Ghi nhận thanh toán các hóa đơn còn nợ của căn hộ này.</p>
        </div>
        <div>
            <a href="{{ portal_route('invoices.index') }}" class="mp-btn mp-btn--outline" style="text-decoration:none;">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mp-alert mp-alert--success" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div>{{ session('success') }}</div>
            @if(session('print_receipt_url'))
                <a href="{{ session('print_receipt_url') }}" target="_blank" class="mp-btn mp-btn--outline" style="padding:4px 12px; font-size:13px; background:#fff; text-decoration:none;">
                    <i class="fas fa-print"></i> Mở phiếu thu vừa in
                </a>
            @endif
        </div>
    @endif
    @if(session('error'))
        <div class="mp-alert mp-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Thông tin cư dân --}}
    <div class="mp-top-row">
        <div class="mp-card mp-resident-card" id="residentCard">
            <div class="mp-resident-info">
                <div class="mp-resident-avatar" id="residentAvatar">
                    @if($ownerUser && $ownerUser->avatar)
                        <img src="{{ asset('storage/' . $ownerUser->avatar) }}" alt="avatar" />
                    @else
                        @php
                            $initials = $ownerUser ? strtoupper(substr(explode(' ', trim($ownerUser->name))[count(explode(' ', trim($ownerUser->name)))-1] ?? '?', 0, 1)) : '?';
                        @endphp
                        <span style="font-size:22px;font-weight:700;color:#2563eb;">{{ $initials }}</span>
                    @endif
                </div>
                <div>
                    <p class="mp-resident-name" id="residentName">{{ $ownerUser->name ?? '---' }}</p>
                    <p class="mp-resident-role">Chủ hộ</p>
                    <span class="mp-resident-apt-badge" id="residentApt">Căn hộ {{ $apartment->apartment_code ?? '---' }}</span>
                </div>
                <div class="mp-resident-contacts" style="margin-left: auto; text-align: right; margin-top: 0;">
                    <span style="justify-content: flex-end;"><i class="fas fa-phone"></i><span id="residentPhone">{{ $ownerUser->phone ?? '---' }}</span></span>
                    <span style="justify-content: flex-end;"><i class="fas fa-envelope"></i><span id="residentEmail">{{ $ownerUser->email ?? '---' }}</span></span>
                </div>
            </div>
        </div>

    </div>


    {{-- Danh sách hóa đơn chưa thanh toán --}}
    <div class="mp-card" id="invoicesSection">
        <div class="mp-invoices-header">
            <div class="mp-invoices-header__icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h2 class="mp-invoices-header__title">Danh sách hóa đơn chưa thanh toán</h2>
        </div>
        
        @if($invoices->isEmpty())
            <div id="noInvoicesMsg" style="text-align:center; padding:30px; color:#a0abc0; font-size:14px;">
                <i class="fas fa-check-circle" style="font-size:28px; color:#43a047; display:block; margin-bottom:8px;"></i>
                Căn hộ này không có hóa đơn chưa thanh toán.
            </div>
        @else
            <table class="mp-table" id="invoicesTable">
                <thead>
                    <tr>
                        <th style="width:40px;">
                            <input type="checkbox" id="checkAll" title="Chọn tất cả" checked />
                        </th>
                        <th>Mã HĐ</th>
                        <th>Kỳ hóa đơn</th>
                        <th>Hạn thanh toán</th>
                        <th>Trạng thái</th>
                        <th style="text-align:right;">Tổng tiền HĐ</th>
                        <th style="text-align:right;">Đã thu</th>
                        <th style="text-align:right; color:#e53935;">Còn phải thu</th>
                    </tr>
                </thead>
                <tbody id="invoicesTbody">
                    @foreach($invoices as $inv)
                        @php
                            $badgeClass = $inv->status === 'overdue' ? 'mp-badge--overdue' : ($inv->status === 'partial_paid' ? 'mp-badge--partial' : 'mp-badge--unpaid');
                            $monthStr = $inv->billing_month;
                            if (is_string($monthStr) && str_contains($monthStr, 'T')) {
                                $monthStr = str_pad(Carbon\Carbon::parse($monthStr)->month, 2, '0', STR_PAD_LEFT);
                            } else {
                                $monthStr = str_pad($monthStr, 2, '0', STR_PAD_LEFT);
                            }
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="inv-chk" data-id="{{ $inv->id }}" data-amount="{{ $inv->formatted_remaining }}" checked /></td>
                            <td>
                                <a href="{{ $inv->show_url }}" target="_blank" class="mp-invoice-code" title="Xem chi tiết hóa đơn">
                                    {{ $inv->invoice_code }} <i class="fas fa-external-link-alt" style="font-size:11px; margin-left:3px;"></i>
                                </a>
                                @php
                                    $feesList = explode(', ', $inv->formatted_description);
                                    $feesCount = count($feesList);
                                @endphp
                                @if($feesCount > 1)
                                    <div class="mp-invoice-desc" style="margin-top: 4px;">
                                        <span style="font-weight: 600; color: #475569;">{{ $feesCount }} khoản phí</span>
                                        <button type="button" style="background:none; border:none; color:#3b82f6; font-size:12px; cursor:pointer; padding:0; margin-left:6px;" onclick="this.nextElementSibling.style.display='block'; this.style.display='none';">▶ Xem chi tiết</button>
                                        <div style="display:none; margin-top:6px; background:#f8fafc; padding:8px 10px; border-radius:6px; border:1px solid #e2e8f0;">
                                            <ul style="padding-left:14px; margin:0 0 6px 0; color:#64748b; font-size:12px; line-height:1.5;">
                                                @foreach($feesList as $fee)
                                                    <li>{{ $fee }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" style="background:none; border:none; color:#ef4444; font-size:12px; cursor:pointer; padding:0;" onclick="this.parentElement.style.display='none'; this.parentElement.previousElementSibling.style.display='inline';">▲ Thu gọn</button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mp-invoice-desc">{{ $inv->formatted_description }}</div>
                                @endif
                            </td>
                            <td>Tháng {{ $monthStr }}/{{ $inv->billing_year }}</td>
                            <td>{{ $inv->due_date ? Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '---' }}</td>
                            <td><span class="mp-badge {{ $badgeClass }}">{{ $inv->formatted_status_label }}</span></td>
                            <td style="text-align:right; font-weight:500;">{{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                            <td style="text-align:right; font-weight:500; color:#16a34a;">{{ number_format($inv->paid_amount, 0, ',', '.') }}</td>
                            <td style="text-align:right; font-weight:700; color:#e53935;" class="mp-amount">{{ number_format($inv->formatted_remaining, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($invoices->isNotEmpty())
    {{-- Xử lý thanh toán --}}
    <div class="mp-card" id="paymentSection">
        <form method="POST" action="{{ portal_route('manual-payment.process') }}" id="paymentForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="apartment_id" id="hiddenApartmentId" value="{{ $apartment->id }}" />

            {{-- Header row --}}
            <div class="mp-payment-title-row">
                <h3 class="mp-payment-title">Xử lý thanh toán</h3>
                <div style="text-align: right;">
                    <div style="font-size: 13.5px; color: #475569; font-weight: 600; margin-bottom: 2px;" id="selectedCountLabel">Đã chọn 0 hóa đơn</div>
                    <span class="mp-payment-total-label">Tổng tiền cần thanh toán: </span>
                    <span class="mp-payment-total-value" id="totalDisplay">0 VNĐ</span>
                </div>
            </div>

            {{-- Body: left = phương thức + số tiền, right = ghi chú --}}
            <div class="mp-payment-body">
                <div>
                    {{-- Phương thức thanh toán --}}
                    <label class="mp-method-label">Phương thức thanh toán</label>
                    <div class="mp-method-group" id="methodGroup">
                        <button type="button" class="mp-method-btn active" data-method="cash">
                            <i class="fas fa-money-bill-wave"></i>
                            Tiền mặt
                        </button>
                        <button type="button" class="mp-method-btn" data-method="bank_transfer">
                            <i class="fas fa-university"></i>
                            Chuyển khoản
                        </button>
                    </div>
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="cash" />

                    {{-- Số tiền thực thu --}}
                    <div class="mp-amount-field">
                        <label class="mp-field-label" for="amountReceived">Số tiền thực thu (VNĐ)</label>
                        <input type="text"
                               id="amountReceived"
                               name="amount_received"
                               class="mp-input"
                               placeholder="0"
                               autocomplete="off" />
                        
                        <div id="paymentPreview" style="margin-top: 10px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 13.5px; display: none;">
                            <div style="margin-bottom: 6px; font-weight: 700; color: #334155; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Sau thanh toán</div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: #64748b; font-weight: 500;">Đã thu:</span>
                                <span id="previewPaid" style="font-weight: 700; color: #16a34a;">0 VNĐ</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b; font-weight: 500;">Còn nợ:</span>
                                <span id="previewDebt" style="font-weight: 700; color: #ef4444;">0 VNĐ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    {{-- Ghi chú --}}
                    <label class="mp-field-label" for="paymentNote">Ghi chú (Tùy chọn)</label>
                    <textarea id="paymentNote"
                              name="note"
                              class="mp-textarea"
                              placeholder="Nhập ghi chú hoặc mã giao dịch ngân hàng..."
                              style="height: 60px;"></textarea>

                    {{-- Ảnh minh chứng --}}
                    <div style="margin-top: 10px;">
                        <label class="mp-field-label">
                            <i class="fas fa-image" style="margin-right:4px; color:#3b82f6;"></i> Ảnh minh chứng
                        </label>
                        <div class="mp-photo-box" id="photoDropArea" onclick="document.getElementById('proofImageInput').click();">
                            <input type="file" name="proof_image" id="proofImageInput" accept="image/*" style="display:none;" onchange="handlePhotoSelect(this)" />
                            <div id="photoPlaceholder" style="text-align:center; padding:8px; cursor:pointer;">
                                <i class="fas fa-upload" style="font-size:18px; color:#3b82f6; margin-bottom:4px;"></i>
                                <div style="font-size:12px; font-weight:600; color:#3b82f6;">Tải lên ảnh minh chứng</div>
                            </div>
                            <div id="photoPreviewWrap" style="display:none; position:relative; text-align:center;">
                                <img id="photoPreview" src="" alt="Preview" style="max-height:80px; border-radius:6px; border:1px solid #cbd5e1; object-fit:contain;" />
                                <button type="button" onclick="event.stopPropagation(); removePhoto();" style="position:absolute; top:-6px; right:-6px; background:#ef4444; color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; font-size:11px; line-height:1;">&times;</button>
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="mp-btn-row" style="margin-top: 14px; display:flex; flex-direction:column; gap:14px; align-items:flex-start;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14.5px; font-weight:500; color:#3d4a60; user-select:none;">
                            <input type="checkbox" name="action" value="print" style="width:18px; height:18px; accent-color:#3b82f6; cursor:pointer;" checked />
                            In phiếu thu sau khi thanh toán
                        </label>
                        <button type="submit" class="mp-btn mp-btn--primary" id="btnConfirm" style="width:100%; padding:14px; font-size:15.5px; text-transform:uppercase;">
                            Xác nhận thanh toán
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endif

</div>

{{-- Confirm Modal --}}
<div id="confirmPaymentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter: blur(2px);">
    <div style="background:#fff; width:380px; border-radius:12px; padding:24px; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <i class="fas fa-question-circle" style="font-size:48px; color:#3b82f6; margin-bottom:16px;"></i>
        <h3 style="margin:0 0 8px; font-size:18px; color:#1e293b;">Xác nhận thu tiền</h3>
        <p style="margin:0 0 24px; font-size:14.5px; color:#475569; line-height:1.6;">
            Bạn xác nhận thu <br/>
            <strong id="confirmAmountTxt" style="font-size:24px; color:#2563eb;">0 VNĐ</strong> <br/>
            cho <strong>Căn hộ {{ $apartment->apartment_number ?? '' }}</strong>?
        </p>
        <div style="display:flex; gap:12px;">
            <button type="button" onclick="closeConfirmModal()" style="flex:1; padding:12px; border-radius:8px; border:1px solid #cbd5e1; background:#f8fafc; color:#475569; font-size:15px; font-weight:600; cursor:pointer;">Hủy bỏ</button>
            <button type="button" onclick="submitConfirmedForm()" style="flex:1; padding:12px; border-radius:8px; border:none; background:#16a34a; color:#fff; font-size:15px; font-weight:600; cursor:pointer;">Xác nhận</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    recalcTotal();

    // Checkbox logic
    document.querySelectorAll('.inv-chk').forEach(chk => {
        chk.addEventListener('change', recalcTotal);
    });

    // Check all
    const checkAllBtn = document.getElementById('checkAll');
    if (checkAllBtn) {
        checkAllBtn.addEventListener('change', function() {
            document.querySelectorAll('.inv-chk').forEach(c => { c.checked = this.checked; });
            recalcTotal();
        });
    }
});

// --- Recalculate Total ---
let currentTotalDebt = 0;

function recalcTotal() {
    currentTotalDebt = 0;
    const selectedIds = [];
    document.querySelectorAll('.inv-chk:checked').forEach(c => {
        currentTotalDebt += parseFloat(c.dataset.amount || 0);
        selectedIds.push(c.dataset.id);
    });

    document.getElementById('selectedCountLabel').textContent = 'Đã chọn ' + selectedIds.length + ' hóa đơn';
    document.getElementById('totalDisplay').textContent = Number(currentTotalDebt).toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('amountReceived').value = Number(currentTotalDebt).toLocaleString('vi-VN');

    // Cập nhật hidden inputs hóa đơn được chọn
    document.querySelectorAll('input[name="invoice_ids[]"]').forEach(e => e.remove());
    const form = document.getElementById('paymentForm');
    selectedIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'invoice_ids[]';
        inp.value = id;
        form.appendChild(inp);
    });
    
    updatePaymentPreview();
}

// --- Payment Method Selection ---
document.querySelectorAll('.mp-method-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.mp-method-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('paymentMethodInput').value = this.dataset.method;
    });
});

// --- Format amount input & Preview ---
document.getElementById('amountReceived').addEventListener('input', function () {
    let raw = this.value.replace(/[^\d]/g, '');
    let amount = parseInt(raw) || 0;
    
    if (amount > currentTotalDebt) {
        amount = currentTotalDebt;
        raw = amount.toString();
        alert('Không được vượt quá số tiền còn nợ (' + Number(currentTotalDebt).toLocaleString('vi-VN') + ' VNĐ).');
    }
    
    if (raw) {
        this.value = Number(raw).toLocaleString('vi-VN');
    } else {
        this.value = '';
    }
    
    updatePaymentPreview();
});

function updatePaymentPreview() {
    const rawAmount = document.getElementById('amountReceived').value.replace(/[^\d]/g, '');
    const amount = parseInt(rawAmount) || 0;
    const previewBox = document.getElementById('paymentPreview');
    
    if (amount > 0 && amount <= currentTotalDebt) {
        previewBox.style.display = 'block';
        document.getElementById('previewPaid').textContent = Number(amount).toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('previewDebt').textContent = Number(currentTotalDebt - amount).toLocaleString('vi-VN') + ' VNĐ';
    } else {
        previewBox.style.display = 'none';
    }
}

// --- Form Validation before Submit ---
let isConfirmed = false;

document.getElementById('paymentForm').addEventListener('submit', function (e) {
    if (isConfirmed) return; // Nếu đã xác nhận thì cho phép form submit
    
    e.preventDefault();
    const checked = document.querySelectorAll('.inv-chk:checked');
    if (checked.length === 0) {
        alert('Vui lòng chọn ít nhất một hóa đơn để thanh toán.');
        return;
    }
    const rawAmount = document.getElementById('amountReceived').value.replace(/[^\d]/g, '');
    if (!rawAmount || parseInt(rawAmount) <= 0) {
        alert('Vui lòng nhập số tiền thực thu hợp lệ.');
        return;
    }
    // Chuẩn hóa amount_received về số nguyên
    document.getElementById('amountReceived').value = rawAmount;
    
    // Hiển thị modal xác nhận
    document.getElementById('confirmAmountTxt').textContent = Number(rawAmount).toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('confirmPaymentModal').style.display = 'flex';
});

function closeConfirmModal() {
    document.getElementById('confirmPaymentModal').style.display = 'none';
}

function submitConfirmedForm() {
    isConfirmed = true;
    document.getElementById('paymentForm').submit();
}

// --- Photo Upload / Camera handlers ---
function handlePhotoSelect(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPlaceholder').style.display = 'none';
            document.getElementById('photoPreviewWrap').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removePhoto() {
    document.getElementById('proofImageInput').value = '';
    document.getElementById('photoPreview').src = '';
    document.getElementById('photoPlaceholder').style.display = 'block';
    document.getElementById('photoPreviewWrap').style.display = 'none';
}

@if(session('print_receipt_url'))
    window.open("{{ session('print_receipt_url') }}", "_blank");
@endif
</script>
@endpush


