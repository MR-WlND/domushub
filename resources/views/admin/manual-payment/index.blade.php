@extends('layouts.admin.master')

@section('page_title', 'Thu tiền thủ công')

@push('styles')
<style>
/* ===== Layout Wrapper ===== */
.mp-wrap {
    padding: 24px 28px;
    background: #f4f6f9;
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

/* ===== Top Row (2 cột) ===== */
.mp-top-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}
.mp-search-card,
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

/* ===== Search Box ===== */
.mp-search-label {
    font-size: 14px;
    font-weight: 600;
    color: #3d4a60;
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 12px;
}
.mp-search-label i { color: #3b82f6; }
.mp-search-input-wrap {
    position: relative;
}
.mp-search-input {
    width: 100%;
    height: 44px;
    border: 1.5px solid #d1d9e6;
    border-radius: 8px;
    padding: 0 46px 0 14px;
    font-size: 14px;
    color: #1a2236;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.mp-search-input:focus { border-color: #3b82f6; }
.mp-search-btn {
    position: absolute;
    right: 0; top: 0;
    width: 44px; height: 44px;
    background: #3b82f6;
    border: none;
    border-radius: 0 8px 8px 0;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.mp-search-btn:hover { background: #2563eb; }
.mp-search-hint {
    font-size: 12px;
    color: #a0abc0;
    margin-top: 7px;
}
.mp-search-error {
    font-size: 13px;
    color: #e53935;
    margin-top: 8px;
    display: none;
}

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
.mp-invoice-code { font-weight: 600; color: #2563eb; }
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
</style>
@endpush


@section('content')
<div class="mp-wrap">

    {{-- Page Header --}}
    <div class="mp-header">
        <div>
            <h1 class="mp-header__title">Thu tiền thủ công</h1>
            <p class="mp-header__sub">Tra cứu và ghi nhận thanh toán trực tiếp từ cư dân.</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mp-alert mp-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mp-alert mp-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Top Row: Tra cứu + Thông tin cư dân --}}
    <div class="mp-top-row">
        {{-- Left: Ô tìm kiếm --}}
        <div class="mp-card mp-search-card">
            <div class="mp-search-label">
                <i class="fas fa-search"></i>
                Tra cứu
            </div>
            <p style="font-size:13px;color:#7a8399;margin:0 0 14px;">Mã căn hộ hoặc Tên chủ hộ</p>
            <div class="mp-search-input-wrap">
                <input type="text"
                       id="searchInput"
                       class="mp-search-input"
                       placeholder="Ví dụ: A1-1205 hoặc Nguyễn Văn An"
                       autocomplete="off" />
                <button type="button" class="mp-search-btn" id="searchBtn" title="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <p class="mp-search-hint">Nhập mã căn hộ, tên, số điện thoại hoặc email của chủ hộ.</p>
            <div class="mp-search-error" id="searchError"></div>
        </div>

        {{-- Right: Card thông tin cư dân --}}
        <div class="mp-card mp-resident-card" id="residentCard" style="display:none;"></div>
    </div>

    {{-- Danh sách hóa đơn chưa thanh toán --}}
    <div class="mp-card" id="invoicesSection" style="display:none;">
        <div class="mp-invoices-header">
            <div class="mp-invoices-header__icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h2 class="mp-invoices-header__title">Danh sách hóa đơn chưa thanh toán</h2>
        </div>
        <table class="mp-table" id="invoicesTable"></table>
    </div>

    {{-- Xử lý thanh toán --}}
    <div class="mp-card" id="paymentSection" style="display:none;">
        <div class="mp-payment-form"></div>
    </div>

</div>
@endsection
