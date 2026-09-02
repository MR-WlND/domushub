@extends('layouts.admin.master')

@section('page_title', 'Biểu giá dịch vụ')

@section('content')
<div class="sp-page">

    {{-- Header --}}
    <div class="sp-header">
        <div>
            <h1 class="sp-title">Biểu giá dịch vụ</h1>
            <p class="sp-subtitle">Quản lý và cấu hình các loại phí dịch vụ chung cư</p>
        </div>
        <button class="sp-btn sp-btn--primary" onclick="toggleForm()">
            <i class="fas fa-plus" style="margin-right: 4px;"></i> Thêm đơn giá
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="sp-alert sp-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sp-alert sp-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Form thêm mới --}}
    <div class="sp-form-card" id="addForm" style="display:none">
        <h3 class="sp-form-title">Thêm đơn giá mới</h3>
        <form method="POST" action="{{ portal_route('service-prices.store') }}">
            @csrf
            <div class="sp-form-grid">
                <div class="sp-field">
                    <label>Loại dịch vụ <span class="sp-required">*</span></label>
                    <select name="type" required onchange="toggleAddVehicleType(this)">
                        <option value="">-- Chọn loại --</option>
                        <option value="water"       {{ old('type')=='water'?'selected':'' }}>Nước</option>
                        <option value="parking_fee" {{ old('type')=='parking_fee'?'selected':'' }}>Phí quản lý xe</option>
                        <option value="internet"    {{ old('type')=='internet'?'selected':'' }}>Internet</option>
                        <option value="service"     {{ old('type')=='service'?'selected':'' }}>Dịch vụ khác</option>
                        <option value="other"       {{ old('type')=='other'?'selected':'' }}>Khác</option>
                    </select>
                    @error('type')<span class="sp-error">{{ $message }}</span>@enderror
                </div>
                <div class="sp-field" id="addVehicleTypeField" style="display: {{ old('type') == 'parking_fee' ? 'flex' : 'none' }};">
                    <label>Áp dụng cho loại xe <span class="sp-required">*</span></label>
                    <select name="vehicle_type">
                        <option value="">-- Chọn xe --</option>
                        <option value="car" {{ old('vehicle_type')=='car'?'selected':'' }}>Ô tô</option>
                        <option value="motorbike" {{ old('vehicle_type')=='motorbike'?'selected':'' }}>Xe máy</option>
                        <option value="electric_bike" {{ old('vehicle_type')=='electric_bike'?'selected':'' }}>Xe điện</option>
                    </select>
                    @error('vehicle_type')<span class="sp-error">{{ $message }}</span>@enderror
                </div>
                <div class="sp-field">
                    <label>Tên dịch vụ <span class="sp-required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Giá nước sinh hoạt" required maxlength="100">
                    @error('name')<span class="sp-error">{{ $message }}</span>@enderror
                </div>
                <div class="sp-field">
                    <label>Đơn giá (đ) <span class="sp-required">*</span></label>
                    <input type="number" name="unit_price" value="{{ old('unit_price', 0) }}" min="0" step="100" required placeholder="0">
                    @error('unit_price')<span class="sp-error">{{ $message }}</span>@enderror
                </div>
                <div class="sp-field sp-field--wide">
                    <label>Mô tả</label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Ghi chú về đơn giá này..." maxlength="500">
                </div>
            </div>
            <div class="sp-form-actions">
                <button type="submit" class="sp-btn sp-btn--primary">Lưu đơn giá</button>
                <button type="button" class="sp-btn sp-btn--ghost" onclick="toggleForm()">Hủy</button>
            </div>
        </form>
    </div>

    {{-- Thống kê nhanh --}}
    <div class="sp-stats">
        @php
            $typeLabels = [
                'water'          => 'Nước',
                'parking_fee'    => 'Phí gửi xe',
                'internet'       => 'Internet',
                'service'        => 'Dịch vụ',
                'other'          => 'Khác',
            ];
            $vehicleLabels = [
                'car' => 'Ô tô',
                'motorbike' => 'Xe máy',
                'electric_bike' => 'Xe điện',
            ];
            $grouped = $servicePrices->groupBy('type');
            $icons = [
                'water' => 'fas fa-tint icon-water',
                'parking_fee' => 'fas fa-car icon-parking',
                'internet' => 'fas fa-wifi icon-internet',
                'service' => 'fas fa-concierge-bell icon-service',
                'other' => 'fas fa-box icon-other',
            ];
        @endphp
        <div class="sp-stats-grid">
            @foreach($grouped as $type => $items)
            <div class="sp-stat-widget">
                <div class="sp-stat-info">
                    <span class="sp-stat-label">{{ $typeLabels[$type] ?? $type }}</span>
                    <span class="sp-stat-value">{{ $items->count() }} mục</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bảng biểu giá --}}
    <div class="sp-card">
        <div class="sp-card-header">
            <h2 class="sp-card-title">Danh sách đơn giá dịch vụ</h2>
            <span class="sp-card-count">{{ $servicePrices->count() }} mục đơn giá</span>
        </div>
        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Loại</th>
                        <th>Tên dịch vụ</th>
                        <th>Đơn giá</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th style="width:140px; text-align:right">Hành động</th>
                    </tr>
                </thead>
            <tbody>
                @forelse($servicePrices as $sp)
                <tr id="row-{{ $sp->id }}">
                    {{-- View mode --}}
                    <td class="view-{{ $sp->id }}">
                        <span class="sp-type-badge sp-type--{{ $sp->type }}">
                            {{ $typeLabels[$sp->type] ?? $sp->type }}
                            @if($sp->type === 'parking_fee' && $sp->vehicle_type)
                                ({{ $vehicleLabels[$sp->vehicle_type] ?? $sp->vehicle_type }})
                            @endif
                        </span>
                    </td>
                    <td class="view-{{ $sp->id }} sp-name">{{ $sp->name }}</td>
                    <td class="view-{{ $sp->id }} sp-price">{{ number_format($sp->unit_price) }}đ</td>
                    <td class="view-{{ $sp->id }} sp-desc">{{ $sp->description ?? '—' }}</td>
                    <td class="view-{{ $sp->id }}">
                        @if($sp->status === 'active')
                            <span class="status-pill status-active">
                                <span class="dot"></span> Đang dùng
                            </span>
                        @else
                            <span class="status-pill status-inactive">
                                <span class="dot"></span> Tạm dừng
                            </span>
                        @endif
                    </td>
                    <td class="view-{{ $sp->id }}" style="text-align:right">
                        <div class="sp-actions" style="justify-content: flex-end;">
                            <button class="sp-btn sp-btn--sm sp-btn--edit" onclick="openEdit({{ $sp->id }})" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ portal_route('service-prices.destroy', $sp->id) }}"
                                  onsubmit="return confirm('Xoá đơn giá {{ $sp->name }}?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="sp-btn sp-btn--sm sp-btn--danger" title="Xoá">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                    {{-- Edit mode (ẩn mặc định) --}}
                    <td colspan="6" class="edit-{{ $sp->id }}" style="display:none; padding:16px; background:#f8fafc;">
                        <form method="POST" action="{{ portal_route('service-prices.update', $sp->id) }}">
                            @csrf @method('PUT')
                            <div class="sp-form-grid">
                                <div class="sp-field">
                                    <label>Loại dịch vụ</label>
                                    <select name="type" required onchange="toggleEditVehicleType(this, {{ $sp->id }})">
                                        <option value="water"       {{ $sp->type=='water'?'selected':'' }}>Nước</option>
                                        <option value="parking_fee" {{ $sp->type=='parking_fee'?'selected':'' }}>Phí quản lý xe</option>
                                        <option value="internet"    {{ $sp->type=='internet'?'selected':'' }}>Internet</option>
                                        <option value="service"     {{ $sp->type=='service'?'selected':'' }}>Dịch vụ khác</option>
                                        <option value="other"       {{ $sp->type=='other'?'selected':'' }}>Khác</option>
                                    </select>
                                </div>
                                <div class="sp-field editVehicleTypeField-{{ $sp->id }}" style="display: {{ $sp->type == 'parking_fee' ? 'flex' : 'none' }};">
                                    <label>Áp dụng cho loại xe <span class="sp-required">*</span></label>
                                    <select name="vehicle_type">
                                        <option value="">-- Chọn xe --</option>
                                        <option value="car" {{ $sp->vehicle_type=='car'?'selected':'' }}>Ô tô</option>
                                        <option value="motorbike" {{ $sp->vehicle_type=='motorbike'?'selected':'' }}>Xe máy</option>
                                        <option value="electric_bike" {{ $sp->vehicle_type=='electric_bike'?'selected':'' }}>Xe điện</option>
                                    </select>
                                </div>
                                <div class="sp-field">
                                    <label>Tên dịch vụ</label>
                                    <input type="text" name="name" value="{{ $sp->name }}" required maxlength="100">
                                </div>
                                <div class="sp-field">
                                    <label>Đơn giá (đ)</label>
                                    <input type="number" name="unit_price" value="{{ $sp->unit_price }}" min="0" step="100" required>
                                </div>
                                <div class="sp-field">
                                    <label>Trạng thái</label>
                                    <select name="status" required>
                                        <option value="active"  {{ $sp->status=='active'?'selected':'' }}>Đang dùng</option>
                                        <option value="pending" {{ $sp->status=='pending'?'selected':'' }}>Tạm dừng</option>
                                        <option value="banned"  {{ $sp->status=='banned'?'selected':'' }}>Vô hiệu</option>
                                    </select>
                                </div>
                                <div class="sp-field sp-field--wide">
                                    <label>Mô tả</label>
                                    <input type="text" name="description" value="{{ $sp->description }}" maxlength="500">
                                </div>
                            </div>
                            <div class="sp-form-actions">
                                <button type="submit" class="sp-btn sp-btn--primary sp-btn--sm">Lưu</button>
                                <button type="button" class="sp-btn sp-btn--ghost sp-btn--sm" onclick="closeEdit({{ $sp->id }})">Hủy</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6"><div class="sp-empty">Chưa có đơn giá nào. Hãy thêm mới.</div></td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<style>
.sp-page { display: flex; flex-direction: column; gap: 20px; }

.sp-header { display: flex; justify-content: space-between; align-items: flex-start; }
.sp-eyebrow { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; color: #0b57d0; margin: 0 0 6px; font-weight: 700; }
.sp-title { font-size: 28px; font-weight: 700; color: #00236f; margin: 0; line-height: 1.2; }
.sp-subtitle { font-size: 15px; color: #64748b; margin: 8px 0 0 0; font-weight: 400; }

.sp-alert { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;}
.sp-alert--success { background: #e6f4ea; border: 1px solid #b7dfc1; color: #137333; }
.sp-alert--error   { background: #fce8e6; border: 1px solid #f4b8b2; color: #ba1a1a; }

.sp-form-card { background: #fff; border: 1px solid #eef2f6; border-radius: 10px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0, 35, 111, 0.04); }
.sp-form-title { font-size: 16px; font-weight: 700; color: #0b1c30; margin: 0 0 20px; }
.sp-form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
.sp-field { display: flex; flex-direction: column; gap: 6px; }
.sp-field--wide { grid-column: 1 / -1; }
.sp-field label { font-size: 13px; font-weight: 700; color: #334155; }
.sp-field input, .sp-field select {
    padding: 0 14px; height: 44px; border: 1px solid #d9e2f2; border-radius: 8px;
    font-size: 14px; color: #0b1c30; background: #f8f9ff; outline: none; transition: all 0.2s;
}
.sp-field input:focus, .sp-field select:focus { border-color: #0b57d0; background: #fff; box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.08); }
.sp-required { color: #dc2626; margin-left: 2px; }
.sp-error { font-size: 12px; color: #dc2626; margin-top: 2px; }
.sp-form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9; }

/* Stats grid */
.sp-stats-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;
}
.sp-stat-widget {
    background: #fff; border: 1px solid #eef2f6; border-radius: 12px; padding: 16px;
    display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 10px rgba(0, 35, 111, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.sp-stat-widget:hover {
    transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0, 35, 111, 0.08);
}
.sp-stat-icon {
    width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.sp-stat-info { display: flex; flex-direction: column; }
.sp-stat-label { font-size: 13px; color: #64748b; font-weight: 600; }
.sp-stat-value { font-size: 18px; font-weight: 700; color: #0b1c30; line-height: 1.2; margin-top: 4px; }

.icon-water { background: #eff6ff; color: #2563eb; }
.icon-internet { background: #f5f3ff; color: #7c3aed; }
.icon-parking { background: #fff7ed; color: #ea580c; }
.icon-service { background: #fff1f2; color: #e11d48; }
.icon-other { background: #f1f5f9; color: #475569; }

.sp-card { background: #fff; border: 1px solid #eef2f6; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 35, 111, 0.04); }
.sp-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 24px; border-bottom: 1px solid #f1f5f9;
}
.sp-card-title { margin: 0; font-size: 16px; font-weight: 700; color: #0b1c30; }
.sp-card-count { font-size: 13px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 3px 10px; border-radius: 10px; }

.sp-table-wrap { width: 100%; overflow-x: auto; }
.sp-table { width: 100%; border-collapse: collapse; min-width: 700px; }
.sp-table th {
    text-align: left; padding: 15px 20px; font-size: 11px; font-weight: 700;
    color: #475569; text-transform: uppercase; letter-spacing: 0.08em;
    border-bottom: 1px solid #f1f5f9; background: #f4f7fe; white-space: nowrap;
}
.sp-table td { padding: 15px 20px; font-size: 14px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.sp-table tr:hover td { background: #fafbff; }

.sp-type-badge { font-size: 12px; font-weight: 600; padding: 5px 12px; border-radius: 6px; background: #f1f5f9; color: #475569; white-space: nowrap; display: inline-block; }
.sp-type--water       { background: #eff6ff; color: #1e40af; border: 1px dashed #bfdbfe; }
.sp-type--management_fee { background: #f0fdf4; color: #166534; border: 1px dashed #bbf7d0; }
.sp-type--parking_fee { background: #fff7ed; color: #c2410c; border: 1px dashed #fed7aa; }
.sp-type--internet    { background: #f5f3ff; color: #5b21b6; border: 1px dashed #ddd6fe; }
.sp-type--service     { background: #fff1f2; color: #be123c; border: 1px dashed #fecdd3; }

.sp-name  { font-weight: 600; color: #0b1c30; }
.sp-price { font-weight: 700; color: #00236f; font-family: 'Monaco', 'Consolas', monospace; font-size: 15px; }
.sp-desc  { color: #64748b; font-size: 13px; max-width: 280px; line-height: 1.4; }

.status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
.status-pill .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.status-active { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.status-active .dot { background: #10b981; }
.status-inactive { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.status-inactive .dot { background: #ef4444; }

.sp-actions { display: flex; gap: 8px; align-items: center; }

.sp-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    border-radius: 8px; font-size: 14px; font-weight: 600;
    cursor: pointer; border: none; text-decoration: none; transition: all 0.2s ease;
}
.sp-btn--primary {
    background: #00236f; color: #fff;
    padding: 0 20px; height: 42px;
}
.sp-btn--primary:hover { background: #00123f; }
.sp-btn--ghost { background: transparent; color: #475569; border: 1px solid #e2e8f0; padding: 0 16px; height: 42px; }
.sp-btn--ghost:hover { background: #f1f5f9; color: #0b1c30; }
.sp-btn--edit   { background: transparent; color: #0b57d0; width: 34px; height: 34px; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 13px; }
.sp-btn--edit:hover { background: #eff6ff; }
.sp-btn--danger { background: transparent; color: #dc2626; width: 34px; height: 34px; border: 1px solid #fecaca; border-radius: 8px; font-size: 13px; }
.sp-btn--danger:hover { background: #fef2f2; }
.sp-btn--sm { padding: 0; }

.sp-empty { text-align: center; padding: 56px 24px; color: #94a3b8; font-size: 15px; display: flex; flex-direction: column; align-items: center; gap: 12px; }
</style>

@push('scripts')
<script>
function toggleForm() {
    const f = document.getElementById('addForm');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
    if (f.style.display === 'block') f.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function openEdit(id) {
    // Ẩn view mode
    document.querySelectorAll(`.view-${id}`).forEach(el => el.style.display = 'none');
    // Hiện edit mode
    document.querySelectorAll(`.edit-${id}`).forEach(el => el.style.display = 'table-cell');
}

function closeEdit(id) {
    document.querySelectorAll(`.view-${id}`).forEach(el => el.style.display = '');
    document.querySelectorAll(`.edit-${id}`).forEach(el => el.style.display = 'none');
}

function toggleAddVehicleType(sel) {
    const field = document.getElementById('addVehicleTypeField');
    const input = field.querySelector('select');
    if(sel.value === 'parking_fee') {
        field.style.display = 'flex';
        input.required = true;
    } else {
        field.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

function toggleEditVehicleType(sel, id) {
    const field = document.querySelector('.editVehicleTypeField-' + id);
    const input = field.querySelector('select');
    if(sel.value === 'parking_fee') {
        field.style.display = 'flex';
        input.required = true;
    } else {
        field.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}
</script>
@endpush

@endsection
