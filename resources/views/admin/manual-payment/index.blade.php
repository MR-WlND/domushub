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
        <div class="mp-card mp-search-card"></div>

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
