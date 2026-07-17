@extends('layouts.admin.master')

@section('page_title', 'Tạo Hóa đơn lẻ')

@section('content')
<div class="batch-page">

    {{-- Header --}}
    <div class="batch-header">
        <div>
            <p class="batch-eyebrow">Tài chính</p>
            <h1 class="batch-title">Tạo hóa đơn lẻ</h1>
            <p class="batch-sub">Phát hành hóa đơn cho một căn hộ cụ thể theo loại phí và kỳ thanh toán</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="batch-btn batch-btn--ghost">Danh sách hóa đơn</a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="batch-alert batch-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="batch-alert batch-alert--error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="batch-alert batch-alert--error">
            @foreach($errors->all() as $e)
                <div>• {{ $e }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.invoices.store') }}">
        @csrf
        <div class="batch-layout">

            {{-- LEFT COLUMN --}}
            <div class="batch-config">

                {{-- Thông số chung --}}
                <div class="batch-section">
                    <h3 class="batch-section-title">Thông số chung</h3>
                    <div class="batch-fields">
                        <div class="batch-field">
                            <label>Căn hộ <span class="batch-req">*</span></label>
                            <select name="apartment_id" required>
                                <option value="">-- Chọn căn hộ --</option>
                                @foreach($apartments as $apt)
                                    <option value="{{ $apt->id }}" {{ old('apartment_id') == $apt->id ? 'selected' : '' }}>
                                        {{ optional($apt->floor->block)->name ?? '' }} – Tầng {{ optional($apt->floor)->floor_number ?? '' }} – Phòng {{ $apt->apartment_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="batch-field">
                            <label>Tháng phí <span class="batch-req">*</span></label>
                            <input type="month" name="billing_month" id="billing_month"
                                   value="{{ old('billing_month', now()->format('Y-m')) }}" required
                                   onchange="updateTitle()">
                        </div>
                    </div>
                    <div class="batch-fields" style="margin-top:14px">
                        <div class="batch-field">
                            <label>Hạn thanh toán <span class="batch-req">*</span></label>
                            <input type="date" name="due_date"
                                   value="{{ old('due_date', now()->addDays(20)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="batch-field">
                            <label>Loại phí <span class="batch-req">*</span></label>
                            <select name="type" required onchange="autoTitle(this.value)">
                                <option value="">-- Chọn loại phí --</option>
                                <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>Tiền điện</option>
                                <option value="water" {{ old('type') == 'water' ? 'selected' : '' }}>Tiền nước</option>
                                <option value="management_fee" {{ old('type') == 'management_fee' ? 'selected' : '' }}>Phí quản lý</option>
                                <option value="parking" {{ old('type') == 'parking' ? 'selected' : '' }}>Phí gửi xe</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Phí khác</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Chi tiết --}}
                <div class="batch-section">
                    <h3 class="batch-section-title">Chi tiết hóa đơn</h3>
                    <div class="batch-fields">
                        <div class="batch-field">
                            <label>Tiêu đề <span class="batch-req">*</span></label>
                            <input type="text" name="title" id="titleInput"
                                   value="{{ old('title') }}" required maxlength="150"
                                   placeholder="VD: Tiền điện tháng 07/2026">
                        </div>
                        <div class="batch-field">
                            <label>Số tiền (VNĐ) <span class="batch-req">*</span></label>
                            <input type="number" name="amount" id="amountInput"
                                   value="{{ old('amount') }}"
                                   min="0" step="1000" required placeholder="0"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                </div>

                {{-- Ghi chú --}}
                <div class="batch-section">
                    <h3 class="batch-section-title">Ghi chú</h3>
                    <div class="batch-field">
                        <label>Nội dung ghi chú (tùy chọn)</label>
                        <textarea name="note" rows="3" maxlength="500"
                                  placeholder="Ghi chú thêm cho hóa đơn này..."
                                  style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:0.875rem;background:#f8fafc;outline:none;resize:vertical;font-family:inherit;transition:border-color .15s"
                                  onfocus="this.style.borderColor='#3b82f6';this.style.background='#fff'"
                                  onblur="this.style.borderColor='#cbd5e1';this.style.background='#f8fafc'">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Preview --}}
            <div class="batch-preview">
                <div class="batch-section">
                    <h3 class="batch-section-title">Xem trước hóa đơn</h3>
                    <div class="batch-est-grid">
                        <div class="batch-est-card">
                            <span class="batch-est-label">Loại phí</span>
                            <span class="batch-est-val" id="preview_type">--</span>
                        </div>
                        <div class="batch-est-card">
                            <span class="batch-est-label">Kỳ phát hành</span>
                            <span class="batch-est-val" id="preview_period">--</span>
                        </div>
                        <div class="batch-est-card batch-est-card--primary">
                            <span class="batch-est-label">Số tiền</span>
                            <span class="batch-est-val" id="preview_amount">0đ</span>
                        </div>
                    </div>
                </div>

                <div class="batch-section">
                    <h3 class="batch-section-title">Lưu ý</h3>
                    <p class="batch-hint" style="margin:0">Hóa đơn sau khi phát hành sẽ được gửi thông báo tự động đến cư dân. Hãy kiểm tra kỹ thông tin trước khi xác nhận.</p>

                    <div style="margin-top:16px;display:flex;flex-direction:column;gap:10px">
                        <div style="display:flex;align-items:center;gap:10px;font-size:0.82rem;color:#334155">
                            <span style="width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#15803d;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">1</span>
                            Chọn căn hộ và kỳ thanh toán
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;font-size:0.82rem;color:#334155">
                            <span style="width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#15803d;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">2</span>
                            Chọn loại phí và nhập số tiền
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;font-size:0.82rem;color:#334155">
                            <span style="width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#15803d;font-size:0.7rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">3</span>
                            Kiểm tra tiêu đề và xác nhận
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit bar --}}
        <div class="batch-submit-bar">
            <div class="batch-submit-info">
                Hóa đơn lẻ dành cho các khoản phát sinh ngoài kỳ xuất hàng loạt.
            </div>
            <button type="submit" class="batch-btn batch-btn--primary">
                Phát hành hóa đơn
            </button>
        </div>
    </form>
</div>

<style>
    /* Reuse all batch- styles from batch.blade.php */
    .batch-page { max-width:1200px; margin:0 auto; padding:24px 20px; }
    .batch-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; }
    .batch-eyebrow { font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em; color:#64748b; margin:0 0 4px; font-weight:600; }
    .batch-title { font-size:1.6rem; font-weight:700; color:#0f172a; margin:0 0 4px; }
    .batch-sub { font-size:0.875rem; color:#64748b; margin:0; }
    .batch-alert { padding:12px 16px; border-radius:8px; font-size:0.875rem; font-weight:500; margin-bottom:16px; }
    .batch-alert--success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .batch-alert--error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }
    .batch-layout { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
    @media(max-width:860px){ .batch-layout{ grid-template-columns:1fr; } }
    .batch-section { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:18px 20px; margin-bottom:16px; }
    .batch-section:last-child { margin-bottom:0; }
    .batch-section-title { font-size:0.9rem; font-weight:700; color:#1e293b; margin:0 0 14px; }
    .batch-hint { font-size:0.8rem; color:#64748b; }
    .batch-fields { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .batch-field { display:flex; flex-direction:column; gap:5px; }
    .batch-field label { font-size:0.78rem; font-weight:600; color:#64748b; }
    .batch-field input, .batch-field select, .batch-field textarea { padding:8px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:0.875rem; background:#f8fafc; outline:none; font-family:inherit; transition:border-color .15s, background .15s; }
    .batch-field input:focus, .batch-field select:focus, .batch-field textarea:focus { border-color:#3b82f6; background:#fff; }
    .batch-req { color:#ef4444; }
    .batch-est-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .batch-est-card { border:1px solid #e2e8f0; border-radius:8px; padding:12px; background:#f8fafc; }
    .batch-est-card--primary { border-color:#bae6fd; background:#f0f9ff; }
    .batch-est-card--primary .batch-est-val { color:#0369a1; }
    .batch-est-label { display:block; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.04em; color:#64748b; font-weight:600; margin-bottom:4px; }
    .batch-est-val { display:block; font-size:1.1rem; font-weight:800; color:#0f172a; }
    .batch-submit-bar { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; gap:16px; }
    .batch-submit-info { font-size:0.85rem; color:#b45309; background:#fef3c7; padding:8px 14px; border-radius:6px; }
    .batch-btn { padding:9px 18px; border-radius:7px; font-size:0.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .batch-btn--primary { background:#2563eb; color:#fff; }
    .batch-btn--primary:hover { background:#1d4ed8; }
    .batch-btn--ghost { background:none; color:#475569; border:1px solid #e2e8f0; }
    .batch-btn--ghost:hover { background:#f8fafc; }
</style>
@endsection

@push('scripts')
<script>
const typeNames = {
    electricity: 'Tiền điện',
    water: 'Tiền nước',
    management_fee: 'Phí quản lý',
    parking: 'Phí gửi xe',
    other: 'Phí khác'
};
let currentType = '{{ old("type", "") }}';

function autoTitle(type) {
    currentType = type;
    document.getElementById('preview_type').innerText = typeNames[type] || '--';
    updateTitle();
}

function updateTitle() {
    if (!currentType) return;
    const monthInput = document.getElementById('billing_month').value;
    if (!monthInput) return;
    const [y, m] = monthInput.split('-');
    document.getElementById('titleInput').value = typeNames[currentType] + ' tháng ' + m + '/' + y;
    document.getElementById('preview_period').innerText = 'Tháng ' + m + '/' + y;
}

function updatePreview() {
    const val = parseInt(document.getElementById('amountInput').value) || 0;
    document.getElementById('preview_amount').innerText = val.toLocaleString('vi-VN') + 'đ';
}

document.addEventListener('DOMContentLoaded', function() {
    // Init preview
    const monthInput = document.getElementById('billing_month').value;
    if (monthInput) {
        const [y, m] = monthInput.split('-');
        document.getElementById('preview_period').innerText = 'Tháng ' + m + '/' + y;
    }
    if (currentType) {
        document.getElementById('preview_type').innerText = typeNames[currentType] || '--';
    }
    const amountInput = document.getElementById('amountInput');
    if (amountInput && amountInput.value) {
        updatePreview();
    }
});
</script>
@endpush
