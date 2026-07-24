@extends('layouts.admin.master')

@section('page_title', 'Tạo Hóa đơn')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Tạo Hóa đơn mới</h1>
        <p class="page-subtitle">Điền thông tin để phát hành hóa đơn cho căn hộ</p>
    </div>
    <a href="{{ portal_route('invoices.index') }}" class="btn btn-outline">← Quay lại</a>
</div>

<div style="max-width:680px">
<div class="table-card" style="padding:28px">

@if($errors->any())
<div style="background:var(--red-50);border:1px solid #fca5a5;border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:20px">
    <div style="font-weight:600;color:var(--red-600);margin-bottom:6px">Vui lòng kiểm tra lại:</div>
    <ul style="margin:0;padding-left:18px;color:var(--red-600);font-size:13px">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ portal_route('invoices.store') }}">
    @csrf

    @php
        $inputStyle = "width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:var(--radius-xs);font-family:inherit;font-size:14px;color:var(--text-primary);outline:none;transition:border-color .2s;background:#fff";
        $labelStyle = "display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:6px";
    @endphp

    <div style="display:grid;gap:20px">

        <div>
            <label style="{{ $labelStyle }}">Căn hộ <span style="color:var(--red-500)">*</span></label>
            <select name="apartment_id" required
                    style="{{ $inputStyle }}"
                    onfocus="this.style.borderColor='var(--brand-400)'"
                    onblur="this.style.borderColor='var(--border)'">
                <option value="">-- Chọn căn hộ --</option>
                @foreach($apartments as $apt)
                    <option value="{{ $apt->id }}" {{ old('apartment_id')==$apt->id?'selected':'' }}>
                        {{ optional($apt->floor->block)->name ?? '' }}
                        – Tầng {{ optional($apt->floor)->floor_number ?? '' }}
                        – Phòng {{ $apt->apartment_number }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div>
                <label style="{{ $labelStyle }}">Loại phí <span style="color:var(--red-500)">*</span></label>
                <select name="type" required
                        style="{{ $inputStyle }}"
                        onfocus="this.style.borderColor='var(--brand-400)'"
                        onblur="this.style.borderColor='var(--border)'"
                        onchange="autoTitle(this.value)">
                    <option value="">-- Chọn loại --</option>
                    <option value="electricity" {{ old('type')=='electricity'?'selected':'' }}>Tiền điện</option>
                    <option value="water"       {{ old('type')=='water'?'selected':'' }}>Tiền nước</option>
                    <option value="management_fee" {{ old('type')=='management_fee'?'selected':'' }}>🏢 Phí quản lý</option>
                    <option value="motorbike"   {{ old('type')=='motorbike'?'selected':'' }}>🏍️ Phí gửi xe máy</option>
                    <option value="car"         {{ old('type')=='car'?'selected':'' }}>🚗 Phí gửi ô tô</option>
                    <option value="bicycle"     {{ old('type')=='bicycle'?'selected':'' }}>🚲 Phí gửi xe đạp</option>
                    <option value="electric_bike" {{ old('type')=='electric_bike'?'selected':'' }}>🛵 Phí gửi xe điện</option>
                    <option value="other"       {{ old('type')=='other'?'selected':'' }}>📦 Phí khác</option>
                </select>
            </div>

            <div>
                <label style="{{ $labelStyle }}">Tháng phí <span style="color:var(--red-500)">*</span></label>
                <input type="month" name="billing_month" id="billing_month"
                       value="{{ old('billing_month', now()->format('Y-m')) }}" required
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='var(--brand-400)'"
                       onblur="this.style.borderColor='var(--border)'"
                       onchange="updateTitle()">
            </div>
        </div>

        <div>
            <label style="{{ $labelStyle }}">Tiêu đề hóa đơn <span style="color:var(--red-500)">*</span></label>
            <input type="text" name="title" id="titleInput"
                   value="{{ old('title') }}" required maxlength="150"
                   placeholder="VD: Tiền điện tháng 05/2026"
                   style="{{ $inputStyle }}"
                   onfocus="this.style.borderColor='var(--brand-400)'"
                   onblur="this.style.borderColor='var(--border)'">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div>
                <label style="{{ $labelStyle }}">Số tiền (đ) <span style="color:var(--red-500)">*</span></label>
                <input type="number" name="amount" value="{{ old('amount') }}"
                       min="0" step="1000" required placeholder="0"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='var(--brand-400)'"
                       onblur="this.style.borderColor='var(--border)'">
            </div>

            <div>
                <label style="{{ $labelStyle }}">Hạn thanh toán <span style="color:var(--red-500)">*</span></label>
                <input type="date" name="due_date"
                       value="{{ old('due_date', now()->addDays(20)->format('Y-m-d')) }}" required
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='var(--brand-400)'"
                       onblur="this.style.borderColor='var(--border)'">
            </div>
        </div>

        <div>
            <label style="{{ $labelStyle }}">Ghi chú</label>
            <textarea name="note" rows="3" maxlength="500"
                      placeholder="Ghi chú thêm (nếu có)..."
                      style="{{ $inputStyle }};resize:vertical"
                      onfocus="this.style.borderColor='var(--brand-400)'"
                      onblur="this.style.borderColor='var(--border)'">{{ old('note') }}</textarea>
        </div>

        <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border)">
            <button type="submit" class="btn btn-primary">🚀 Phát hành hóa đơn</button>
            <a href="{{ portal_route('invoices.index') }}" class="btn btn-outline">Hủy</a>
        </div>

    </div>
</form>
</div>
</div>

@endsection

@push('scripts')
<script>
const typeNames = {
    electricity: 'Tiền điện', water: 'Tiền nước',
    management_fee: 'Phí quản lý',
    motorbike: 'Phí gửi xe máy', car: 'Phí gửi ô tô',
    bicycle: 'Phí gửi xe đạp', electric_bike: 'Phí gửi xe điện',
    other: 'Phí khác'
};
let currentType = '';

function autoTitle(type) {
    currentType = type;
    updateTitle();
}

function updateTitle() {
    if (!currentType) return;
    const monthInput = document.getElementById('billing_month').value;
    if (!monthInput) return;
    const [y, m] = monthInput.split('-');
    document.getElementById('titleInput').value = `${typeNames[currentType]} tháng ${m}/${y}`;
}
</script>
@endpush
