@extends('layouts.admin.master')

@section('page_title', 'Thu tiền thủ công')

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
