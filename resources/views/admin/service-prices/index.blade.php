@extends('layouts.admin.master')

@section('page_title', 'Biểu giá dịch vụ')

@section('content')
<div class="sp-page">

    {{-- Header --}}
    <div class="sp-header">
        <div>
            <p class="sp-eyebrow">Tài chính</p>
            <h1 class="sp-title">Biểu giá dịch vụ</h1>
        </div>
        <button class="sp-btn sp-btn--primary" onclick="toggleForm()">Thêm đơn giá</button>
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
        @endphp
        @foreach($grouped as $type => $items)
        <div class="sp-stat-chip">
            <span>{{ $typeLabels[$type] ?? $type }}</span>
            <span class="sp-stat-count">{{ $items->count() }}</span>
        </div>
        @endforeach
    </div>

    {{-- Bảng biểu giá --}}
    <div class="sp-card">
        <div class="sp-card-header">
            <span>{{ $servicePrices->count() }} mục đơn giá</span>
        </div>
        <table class="sp-table">
            <thead>
                <tr>
                    <th>Loại</th>
                    <th>Tên dịch vụ</th>
                    <th>Đơn giá</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                    <th style="width:120px"></th>
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
                            <span class="sp-badge sp-badge--active">Đang dùng</span>
                        @else
                            <span class="sp-badge sp-badge--inactive">Tạm dừng</span>
                        @endif
                    </td>
                    <td class="view-{{ $sp->id }}">
                        <div class="sp-actions">
                            <button class="sp-btn sp-btn--sm sp-btn--edit" onclick="openEdit({{ $sp->id }})">Sửa</button>
                            <form method="POST" action="{{ portal_route('service-prices.destroy', $sp->id) }}"
                                  onsubmit="return confirm('Xoá đơn giá {{ $sp->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="sp-btn sp-btn--sm sp-btn--danger">Xoá</button>
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

<style>
.sp-page { max-width: 1100px; margin: 0 auto; padding: 24px 20px; }

.sp-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
.sp-eyebrow { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.sp-title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 0; }

.sp-alert { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; margin-bottom: 16px; }
.sp-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.sp-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

.sp-form-card { background: #fff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 20px 24px; margin-bottom: 20px; }
.sp-form-title { font-size: 1rem; font-weight: 700; color: #1e40af; margin: 0 0 16px; }
.sp-form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }
.sp-field { display: flex; flex-direction: column; gap: 4px; }
.sp-field--wide { grid-column: 1 / -1; }
.sp-field label { font-size: 0.78rem; font-weight: 600; color: #64748b; }
.sp-field input, .sp-field select {
    padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px;
    font-size: 0.875rem; color: #1e293b; background: #f8fafc; outline: none;
}
.sp-field input:focus, .sp-field select:focus { border-color: #3b82f6; background: #fff; }
.sp-required { color: #ef4444; }
.sp-error { font-size: 0.75rem; color: #dc2626; }
.sp-form-actions { display: flex; gap: 10px; margin-top: 16px; padding-top: 14px; border-top: 1px solid #e2e8f0; }

.sp-stats { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
.sp-stat-chip {
    display: flex; align-items: center; gap: 6px;
    background: #fff; border: 1px solid #e2e8f0; border-radius: 20px;
    padding: 5px 12px; font-size: 0.8rem; color: #475569; font-weight: 500;
}
.sp-stat-count { background: #e2e8f0; color: #334155; border-radius: 10px; padding: 1px 7px; font-size: 0.72rem; font-weight: 700; }

.sp-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.sp-card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155;
}
.sp-table { width: 100%; border-collapse: collapse; }
.sp-table th {
    text-align: left; padding: 10px 14px; font-size: 0.72rem; font-weight: 700;
    color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;
    border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.sp-table td { padding: 12px 14px; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.sp-table tr:hover td { background: #fafbff; }

.sp-type-badge { font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 5px; background: #f1f5f9; color: #475569; white-space: nowrap; }
.sp-type--water       { background: #eff6ff; color: #1e40af; }
.sp-type--management_fee { background: #f0fdf4; color: #166534; }
.sp-type--parking_fee { background: #fff7ed; color: #c2410c; }
.sp-type--internet    { background: #f5f3ff; color: #5b21b6; }
.sp-type--service     { background: #fff1f2; color: #be123c; }

.sp-name  { font-weight: 600; color: #0f172a; }
.sp-price { font-weight: 700; color: #2563eb; font-family: monospace; font-size: 0.9rem; }
.sp-desc  { color: #94a3b8; font-size: 0.8rem; max-width: 250px; }

.sp-badge { font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; }
.sp-badge--active   { background: #dcfce7; color: #15803d; }
.sp-badge--inactive { background: #f1f5f9; color: #64748b; }

.sp-actions { display: flex; gap: 6px; align-items: center; }

.sp-btn {
    padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;
    cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
}
.sp-btn--primary { background: #2563eb; color: #fff; }
.sp-btn--primary:hover { background: #1d4ed8; }
.sp-btn--ghost { background: none; color: #ef4444; border: 1px solid #fecaca; }
.sp-btn--ghost:hover { background: #fef2f2; }
.sp-btn--edit   { background: #f1f5f9; color: #475569; padding: 5px 9px; border: 1px solid #e2e8f0; }
.sp-btn--edit:hover { background: #e2e8f0; }
.sp-btn--danger { background: #fee2e2; color: #b91c1c; padding: 5px 9px; border: 1px solid #fecaca; }
.sp-btn--danger:hover { background: #fecaca; }
.sp-btn--sm { padding: 5px 12px; font-size: 0.78rem; }

.sp-empty { text-align: center; padding: 48px 20px; color: #94a3b8; font-size: 0.95rem; }
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
