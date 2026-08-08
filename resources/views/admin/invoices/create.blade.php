@extends('layouts.admin.master')

@section('page_title', 'Tạo Hóa đơn lẻ')

@push('styles')
<style>
    :root {
        --c-bg: #f8fafc;
        --c-primary: #1e3a8a;
        --c-primary-light: #2563eb;
        --c-text-main: #1e293b;
        --c-text-muted: #64748b;
        --c-border: #e2e8f0;
        --c-blue-text: #3b82f6;
        --c-danger: #ef4444;
    }

    body {
        background-color: var(--c-bg);
    }

    .invoice-layout {
        padding: 24px 0 60px 0;
        font-family: 'Inter', sans-serif;
    }

    /* Top Header */
    .top-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }
    .top-header-title {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .breadcrumb {
        font-size: 0.85rem;
        color: var(--c-text-main);
        font-weight: 500;
    }
    .breadcrumb span {
        color: var(--c-primary);
        font-weight: 600;
    }
    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--c-primary);
        margin: 0;
    }
    .top-header-actions {
        display: flex;
        gap: 12px;
    }
    .btn-top {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
    }
    .btn-cancel {
        background: #fff;
        border: 1px solid var(--c-border);
        color: var(--c-text-main);
    }
    .btn-cancel:hover { background: #f1f5f9; }
    .btn-publish {
        background: var(--c-primary);
        color: #fff;
        gap: 8px;
    }
    .btn-publish:hover { background: #172554; }

    .inv-grid { 
        display: grid; 
        grid-template-columns: 1fr 380px; 
        gap: 24px; 
        align-items: start; 
    }
    @media (max-width: 1024px) { .inv-grid { grid-template-columns: 1fr; } }

    /* Clean Card */
    .clean-card {
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .clean-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--c-border);
    }
    .clean-card-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        color: var(--c-primary);
        font-size: 1.1rem;
    }
    .clean-card-body {
        padding: 24px;
    }

    /* Form Inputs */
    .form-group { margin-bottom: 24px; }
    .form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--c-text-main);
        margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        font-size: 0.95rem;
        color: var(--c-text-main);
        background: #fff;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--c-primary-light);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

    /* Toggle Buttons */
    .toggle-group {
        display: flex;
        background: #f1f5f9;
        border-radius: 8px;
        padding: 4px;
    }
    .toggle-btn {
        flex: 1;
        text-align: center;
        padding: 8px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--c-text-muted);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
    }
    .toggle-btn.active {
        background: #fff;
        color: var(--c-primary-light);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Custom Table */
    .clean-table { width: 100%; border-collapse: collapse; text-align: left; }
    .clean-table th { 
        padding: 12px 16px; 
        font-weight: 600; 
        background: #f8fafc; 
        color: var(--c-text-muted); 
        font-size: 0.75rem; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .clean-table td { 
        padding: 16px; 
        border-bottom: 1px solid var(--c-border); 
        vertical-align: middle; 
    }
    
    .table-input {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 0.95rem;
        color: var(--c-text-main);
        padding: 4px 0;
    }
    .table-input:focus { outline: none; border-bottom: 1px solid var(--c-primary-light); }
    .table-input.amount { font-weight: 700; }
    .table-input.note { font-style: italic; color: var(--c-text-muted); font-size: 0.9rem; }
    
    .type-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--c-border);
        border-radius: 6px;
        font-size: 0.9rem;
        color: var(--c-text-main);
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s;
    }
    .type-select:focus { outline: none; border-color: var(--c-primary-light); background: #fff; }

    .btn-add-row {
        background: none;
        border: none;
        color: var(--c-primary);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-delete {
        color: var(--c-danger);
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
    }
    .btn-delete:hover { background: #fee2e2; }

    /* Summary Section */
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 16px;
        font-size: 0.95rem;
        color: var(--c-text-muted);
    }
    .summary-row .val { color: var(--c-text-main); font-weight: 500; }
    
    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 24px 0;
        padding-top: 24px;
        border-top: 1px solid var(--c-border);
    }
    .summary-total .lbl { font-size: 1.1rem; font-weight: 700; color: var(--c-text-main); }
    .summary-total .val {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--c-primary);
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        line-height: 1.2;
    }
    .summary-total .currency { font-size: 0.9rem; color: var(--c-text-muted); font-weight: 600; }

    /* Checkboxes */
    .checkbox-list { margin-bottom: 24px; }
    .checkbox-list-title { font-size: 0.85rem; font-weight: 700; color: var(--c-text-main); margin-bottom: 12px; }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 0.9rem;
        color: var(--c-text-main);
        cursor: pointer;
    }
    .checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--c-primary);
        cursor: pointer;
    }
    .checkbox-item svg { color: var(--c-text-muted); }

    /* Action Button */
    .btn-submit {
        width: 100%;
        background: var(--c-primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 14px;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #172554; }

    /* Info Boxes */
    .info-box-green {
        background: #f0fdf4;
        border: 1px solid #dcfce7;
        border-radius: 8px;
        padding: 16px;
        margin-top: 16px;
        display: flex;
        gap: 12px;
        color: #166534;
        font-size: 0.85rem;
        line-height: 1.5;
    }
    .info-box-blue {
        background: #eff6ff;
        border-radius: 12px;
        padding: 20px;
        margin-top: 24px;
    }
    .info-box-blue .title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: var(--c-primary);
        margin-bottom: 8px;
    }
    .info-box-blue p {
        font-size: 0.9rem;
        color: #1e3a8a;
        margin: 0 0 16px 0;
        line-height: 1.5;
    }
    .info-box-blue a {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--c-primary-light);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .info-box-blue a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="invoice-layout">
    
    @if($errors->any())
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; gap: 12px; align-items: flex-start;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2" style="flex-shrink:0; margin-top:2px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <div>
            <div style="font-weight: 600; color: #991b1b; margin-bottom: 4px;">Vui lòng kiểm tra lại thông tin:</div>
            <ul style="margin: 0; padding-left: 20px; color: #b91c1c; font-size: 0.9rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ portal_route('invoices.store') }}" id="invoiceForm" onsubmit="return validateForm()">
        @csrf
        <!-- Top Header as in Image -->
        <div class="top-header">
            <div class="top-header-title">
                <div class="breadcrumb">Hóa đơn / <span>Tạo hóa đơn lẻ</span></div>
                <h1 class="page-title">Tạo hóa đơn lẻ</h1>
            </div>
            <div class="top-header-actions">
                <a href="{{ portal_route('invoices.index') }}" class="btn-top btn-cancel">Hủy bỏ</a>
                <button type="submit" class="btn-top btn-publish">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm-3.611-1.73 4.339-2.76z"/></svg>
                    Phát hành hóa đơn
                </button>
            </div>
        </div>

        <div class="inv-grid">
            
            <!-- LEFT COLUMN -->
            <div class="left-col">
                
                <!-- Card 1: Thông tin khách hàng & Hóa đơn -->
                <div class="clean-card">
                    <div class="clean-card-header" style="border-bottom: none; padding-bottom: 0;">
                        <div class="clean-card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                            Thông tin khách hàng & Hóa đơn
                        </div>
                    </div>
                    <div class="clean-card-body">
                        <div class="grid-3">
                            <div class="form-group">
                                <label>Tòa nhà</label>
                                <select id="block_select" class="form-input" onchange="filterFloors()">
                                    <option value="">Chọn tòa nhà</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tầng</label>
                                <select id="floor_select" class="form-input" onchange="filterApartments()" disabled>
                                    <option value="">Chọn tầng</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Căn hộ <span class="text-danger">*</span></label>
                                <select name="apartment_id" id="apartment_select" class="form-input" required>
                                    <option value="">Chọn căn hộ</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tiêu đề hóa đơn <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="Ví dụ: Phí phát sinh căn hộ A-1205" class="form-input" required maxlength="150">
                        </div>
                        
                        <div class="grid-2">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Tháng ghi nhận <span class="text-danger">*</span></label>
                                <input type="month" name="billing_month" id="billing_month" value="{{ old('billing_month', now()->format('Y-m')) }}" class="form-input" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Ngày đến hạn <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" class="form-input" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Chi tiết khoản phí -->
                <div class="clean-card">
                    <div class="clean-card-header">
                        <div class="clean-card-title" style="color: var(--c-text-main);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 1A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 13.5 1h-11ZM2 2.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11ZM4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5Zm0 3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5Zm0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5Z"/></svg>
                            Chi tiết khoản phí
                        </div>
                        <button type="button" class="btn-add-row" onclick="addCustomFeeRow()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                            Thêm khoản phí
                        </button>
                    </div>
                    <div class="clean-card-body" style="padding: 0;">
                        <table class="clean-table" id="customFeesTable">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">TÊN KHOẢN PHÍ</th>
                                    <th style="width: 20%;">LOẠI PHÍ</th>
                                    <th style="width: 20%;">SỐ TIỀN (VNĐ)</th>
                                    <th style="width: 20%;">GHI CHÚ</th>
                                    <th style="width: 5%; text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody id="customFeesBody">
                                <!-- Dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="right-col">
                <div class="clean-card">
                    <div class="clean-card-body">
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--c-text-main); margin: 0 0 24px 0;">Tổng quan hóa đơn</h3>
                        
                        <div class="summary-row">
                            <span>Tạm tính</span>
                            <span class="val" id="summarySubtotal">0 VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span>Thuế (0%)</span>
                            <span class="val">0 VNĐ</span>
                        </div>
                        
                        <div class="summary-total">
                            <span class="lbl">Tổng cộng</span>
                            <span class="val">
                                <span id="summaryTotalValue">0</span>
                                <span class="currency">VNĐ</span>
                            </span>
                        </div>

                        <div class="info-box-green">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
                            Hóa đơn này sẽ tự động được gạch nợ khi cư dân thanh toán qua cổng điện tử.
                        </div>
                    </div>
                </div>

                <div class="info-box-blue">
                    <div class="title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/></svg>
                        Cần hỗ trợ?
                    </div>
                    <p>Xem hướng dẫn về cách tạo hóa đơn định kỳ hoặc hóa đơn dịch vụ hàng loạt.</p>
                    <a href="#">
                        Xem tài liệu HDSD
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/><path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
@php
$apartmentsList = $apartments->map(function($apt) {
    return [
        'id' => $apt->id,
        'number' => $apt->apartment_number,
        'floor_id' => $apt->floor_id,
        'floor_number' => optional($apt->floor)->floor_number,
        'block_id' => optional(optional($apt->floor)->block)->id,
        'block_name' => optional(optional($apt->floor)->block)->name,
    ];
});
@endphp
<script>
const apartmentsData = @json($apartmentsList);

// Build Blocks
const blockSelect = document.getElementById('block_select');
const floorSelect = document.getElementById('floor_select');
const aptSelect = document.getElementById('apartment_select');

let blocksMap = {};
apartmentsData.forEach(a => {
    if (a.block_id) blocksMap[a.block_id] = a.block_name;
});
Object.keys(blocksMap).forEach(id => {
    blockSelect.add(new Option(blocksMap[id], id));
});

function filterFloors() {
    floorSelect.innerHTML = '<option value="">Chọn tầng</option>';
    aptSelect.innerHTML = '<option value="">Chọn căn hộ</option>';
    floorSelect.disabled = true;
    
    if (blockSelect.value) {
        let floorsMap = {};
        apartmentsData.filter(a => a.block_id == blockSelect.value).forEach(a => {
            if (a.floor_id) floorsMap[a.floor_id] = 'Tầng ' + a.floor_number;
        });
        Object.keys(floorsMap).forEach(id => {
            floorSelect.add(new Option(floorsMap[id], id));
        });
        floorSelect.disabled = false;
    }
}

function filterApartments() {
    aptSelect.innerHTML = '<option value="">Chọn căn hộ</option>';
    
    if (floorSelect.value) {
        apartmentsData.filter(a => a.floor_id == floorSelect.value).forEach(a => {
            aptSelect.add(new Option(a.number, a.id));
        });
    }
}

let rowCount = 0;
function addCustomFeeRow() {
    const tbody = document.getElementById('customFeesBody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="text" name="custom_fees[${rowCount}][name]" class="table-input" placeholder="Ví dụ: Làm lại thẻ" required>
        </td>
        <td>
            <select name="custom_fees[${rowCount}][type]" class="type-select">
                <option value="service">Dịch vụ</option>
                <option value="compensation">Bồi thường</option>
                <option value="penalty">Phạt</option>
                <option value="card_reissue">Làm lại thẻ</option>
                <option value="other">Khác</option>
            </select>
        </td>
        <td>
            <input type="number" name="custom_fees[${rowCount}][amount]" class="table-input amount amount-input" placeholder="0" min="0" required oninput="calcTotal()">
        </td>
        <td>
            <input type="text" name="custom_fees[${rowCount}][note]" class="table-input note" placeholder="Nhập ghi chú...">
        </td>
        <td style="text-align: center;">
            <button type="button" class="btn-delete" onclick="removeRow(this)" title="Xóa">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    rowCount++;
    calcTotal();
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calcTotal();
}

function calcTotal() {
    let total = 0;
    const inputs = document.querySelectorAll('.amount-input');
    inputs.forEach(input => {
        let val = parseFloat(input.value);
        if (!isNaN(val) && val > 0) total += val;
    });
    
    let formatted = total.toLocaleString('vi-VN');
    document.getElementById('summarySubtotal').textContent = formatted + ' VNĐ';
    document.getElementById('summaryTotalValue').textContent = formatted;
}

function validateForm() {
    const tbody = document.getElementById('customFeesBody');
    if (tbody.children.length === 0) {
        alert("Vui lòng thêm ít nhất một khoản phí.");
        return false;
    }
    return true;
}

// Init one row by default
window.addEventListener('DOMContentLoaded', () => {
    addCustomFeeRow();
});
</script>
@endpush
