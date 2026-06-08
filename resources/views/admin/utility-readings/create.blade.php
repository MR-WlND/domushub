@extends('layouts.admin.master')

@section('page_title', 'Ghi chỉ số đơn lẻ – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>📝 Ghi chỉ số đơn lẻ</h1>
        <p>Nhập chỉ số mới cho một căn hộ</p>
    </div>
    <a href="{{ route('admin.utility-readings.index') }}" class="util-btn util-btn--outline">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Quay lại danh sách
    </a>
</div>

{{-- ── Error Alert ─────────────────────────────────── --}}
@if (isset($errors) && $errors->any())
    <div class="util-alert--danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── Form Card ───────────────────────────────────── --}}
<div class="util-form-card" style="max-width: 760px;">

    <div class="form-section-header">
        <div class="section-number">1</div>
        <h4>Thông tin căn hộ</h4>
    </div>

    <form action="{{ route('admin.utility-readings.store') }}" method="POST" id="createForm" enctype="multipart/form-data">
        @csrf

        <div class="util-form-grid-2">
            {{-- Chọn Tòa nhà --}}
            <div class="util-form-group">
                <label class="util-form-label">Block / Tòa nhà <span style="color:#ef4444">*</span></label>
                <select id="block_select" class="util-form-input" required>
                    <option value="">— Chọn tòa nhà —</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}">{{ $block->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Chọn Tầng --}}
            <div class="util-form-group">
                <label class="util-form-label">Tầng <span style="color:#ef4444">*</span></label>
                <select id="floor_select" class="util-form-input" required disabled>
                    <option value="">— Chọn tầng —</option>
                </select>
            </div>

            {{-- Căn hộ --}}
            <div class="util-form-group" style="grid-column: span 2;">
                <label class="util-form-label">Căn hộ <span style="color:#ef4444">*</span></label>
                <select name="apartment_id" id="apartment_id"
                    class="util-form-input {{ $errors->has('apartment_id') ? 'util-form-input--error' : '' }}" required disabled>
                    <option value="">— Chọn căn hộ —</option>
                </select>
                @error('apartment_id')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-section-header" style="margin-top: 20px;">
            <div class="section-number">2</div>
            <h4>Thông tin chỉ số</h4>
        </div>

        <div class="util-form-grid-2">
            {{-- Loại --}}
            <div class="util-form-group">
                <label class="util-form-label">Loại <span style="color:#ef4444">*</span></label>
                <select name="type" id="type"
                    class="util-form-input {{ $errors->has('type') ? 'util-form-input--error' : '' }}" required>
                    <option value="">— Chọn loại —</option>
                    <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>⚡ Điện</option>
                    <option value="water" {{ old('type') == 'water' ? 'selected' : '' }}>💧 Nước</option>
                </select>
                @error('type')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tháng/Năm --}}
            <div class="util-form-group">
                <label class="util-form-label">Kỳ ghi <span style="color:#ef4444">*</span></label>
                <div style="display: flex; gap: 8px;">
                    <input type="number" name="record_month" id="record_month"
                        value="{{ old('record_month', now()->month) }}" min="1" max="12"
                        class="util-form-input {{ $errors->has('record_month') ? 'util-form-input--error' : '' }}"
                        placeholder="Tháng" required style="flex: 1;">
                    <input type="number" name="record_year" id="record_year"
                        value="{{ old('record_year', now()->year) }}" min="2020" max="2100"
                        class="util-form-input {{ $errors->has('record_year') ? 'util-form-input--error' : '' }}"
                        placeholder="Năm" required style="flex: 1.5;">
                </div>
            </div>
        </div>

        {{-- Chỉ số --}}
        <div class="util-form-grid-3" style="margin-top: 0;">
            @if(auth()->user()->role !== 'technician')
            <div class="util-form-group">
                <label class="util-form-label">Chỉ số cũ (tự động)</label>
                <input type="number" id="old_value_display" class="util-form-input util-form-input--readonly"
                    value="0" readonly>
                <p class="util-form-hint">Lấy tự động từ kỳ trước</p>
            </div>
            @else
            <input type="hidden" id="old_value_display" value="0">
            @endif

            <div class="util-form-group" style="{{ auth()->user()->role === 'technician' ? 'grid-column: span 3;' : '' }}">
                <label class="util-form-label">Chỉ số mới <span style="color:#ef4444">*</span></label>
                <input type="number" name="new_value" id="new_value"
                    value="{{ old('new_value') }}" min="0"
                    class="util-form-input {{ $errors->has('new_value') ? 'util-form-input--error' : '' }}"
                    placeholder="Nhập chỉ số mới" required>
                @error('new_value')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            @if(auth()->user()->role !== 'technician')
            <div class="util-form-group">
                <label class="util-form-label">Tiêu thụ (tự tính)</label>
                <input type="text" id="usage_display" class="util-form-input util-form-input--readonly"
                    value="0" readonly style="font-weight: 700; color: #00236f;">
                <p class="util-form-hint">= Chỉ số mới – Chỉ số cũ</p>
            </div>
            @else
            <input type="hidden" id="usage_display" value="0">
            @endif

            {{-- Cờ Thay công tơ mới --}}
            <div class="util-form-group" style="grid-column: span 3; margin-top: 10px; margin-bottom: 10px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #1e293b;">
                    <input type="checkbox" name="is_reset" id="is_reset" value="1" {{ old('is_reset') ? 'checked' : '' }} style="width: 16px; height: 16px; accent-color: #00236f; cursor: pointer;">
                    🔄 Thay công tơ mới (Chỉ số cũ sẽ đặt về 0 cho kỳ này)
                </label>
            </div>
        </div>

        {{-- Ảnh công tơ minh chứng --}}
        <div class="util-form-group" style="margin-top: 15px; margin-bottom: 20px;">
            <label class="util-form-label">Ảnh minh chứng công tơ</label>
            <input type="file" name="image_proof" id="image_proof" accept="image/*" class="util-form-input">
            <p class="util-form-hint">Hỗ trợ các file định dạng ảnh (JPG, PNG, WebP) tối đa 4MB.</p>
            <div id="image_preview_container" style="margin-top: 10px; display: none;">
                <img id="image_preview" src="" alt="Xem trước ảnh công tơ" style="max-width: 240px; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            </div>
        </div>

        {{-- Actions --}}
        <div class="util-form-actions">
            <button type="submit" class="util-btn util-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Lưu chỉ số
            </button>
            <a href="{{ route('admin.utility-readings.index') }}" class="util-btn util-btn--outline">Hủy</a>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const blockSelect  = document.getElementById('block_select');
    const floorSelect  = document.getElementById('floor_select');
    const apartmentId  = document.getElementById('apartment_id');
    const type         = document.getElementById('type');
    const recordMonth  = document.getElementById('record_month');
    const recordYear   = document.getElementById('record_year');
    const oldValueDisp = document.getElementById('old_value_display');
    const newValueInp  = document.getElementById('new_value');
    const usageDisp    = document.getElementById('usage_display');
    const isResetCb    = document.getElementById('is_reset');

    // Dữ liệu được truyền từ Laravel
    const floorsData = @json($floors);
    const apartmentsData = @json($apartments);

    let fetchedOldValue = 0;

    function populateFloors(blockId) {
        floorSelect.innerHTML = '<option value="">— Chọn tầng —</option>';
        apartmentId.innerHTML = '<option value="">— Chọn căn hộ —</option>';
        apartmentId.disabled = true;

        if (!blockId) {
            floorSelect.disabled = true;
            return;
        }

        const filteredFloors = floorsData.filter(f => f.block_id == blockId);
        filteredFloors.forEach(f => {
            const opt = document.createElement('option');
            opt.value = f.id;
            opt.textContent = f.name || `Tầng ${f.floor_number}`;
            floorSelect.appendChild(opt);
        });

        floorSelect.disabled = false;
    }

    function populateApartments(floorId) {
        apartmentId.innerHTML = '<option value="">— Chọn căn hộ —</option>';

        if (!floorId) {
            apartmentId.disabled = true;
            return;
        }

        const filteredApts = apartmentsData.filter(a => a.floor_id == floorId);
        filteredApts.forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.apartment_number;
            apartmentId.appendChild(opt);
        });

        apartmentId.disabled = false;
    }

    blockSelect.addEventListener('change', function () {
        populateFloors(this.value);
        fetchedOldValue = 0;
        updateOldValueDisplay();
    });

    floorSelect.addEventListener('change', function () {
        populateApartments(this.value);
        fetchedOldValue = 0;
        updateOldValueDisplay();
    });

    function fetchOldValue() {
        const aptId = apartmentId.value;
        const t     = type.value;
        const m     = recordMonth.value;
        const y     = recordYear.value;

        if (!aptId || !t || !m || !y) return;

        fetch(`{{ route('admin.utility-readings.get-old-value') }}?apartment_id=${aptId}&type=${t}&month=${m}&year=${y}`)
            .then(res => res.json())
            .then(data => {
                fetchedOldValue = data.old_value ?? 0;
                updateOldValueDisplay();
            })
            .catch(() => {
                fetchedOldValue = 0;
                updateOldValueDisplay();
            });
    }

    function updateOldValueDisplay() {
        if (isResetCb && isResetCb.checked) {
            oldValueDisp.value = 0;
        } else {
            oldValueDisp.value = fetchedOldValue;
        }
        calcUsage();
    }

    function calcUsage() {
        const oldVal = parseInt(oldValueDisp.value) || 0;
        const newVal = parseInt(newValueInp.value) || 0;
        const usage  = Math.max(0, newVal - oldVal);
        usageDisp.value = usage.toLocaleString('vi-VN');
        usageDisp.style.color = usage > 0 ? '#10b981' : '#94a3b8';
    }

    apartmentId.addEventListener('change', fetchOldValue);
    type.addEventListener('change', fetchOldValue);
    recordMonth.addEventListener('change', fetchOldValue);
    recordYear.addEventListener('change', fetchOldValue);
    newValueInp.addEventListener('input', calcUsage);
    if (isResetCb) {
        isResetCb.addEventListener('change', updateOldValueDisplay);
    }

    // Xử lý xem trước ảnh
    const imageProof = document.getElementById('image_proof');
    const previewContainer = document.getElementById('image_preview_container');
    const previewImg = document.getElementById('image_preview');

    if (imageProof) {
        imageProof.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                previewImg.src = '';
                previewContainer.style.display = 'none';
            }
        });
    }

    // Khôi phục giá trị cũ nếu có lỗi validate hoặc chọn lại
    const oldApartmentId = "{{ old('apartment_id') }}";
    if (oldApartmentId) {
        const apt = apartmentsData.find(a => a.id == oldApartmentId);
        if (apt) {
            blockSelect.value = apt.floor.block_id;
            populateFloors(apt.floor.block_id);
            floorSelect.value = apt.floor_id;
            populateApartments(apt.floor_id);
            apartmentId.value = oldApartmentId;
            fetchOldValue();
        }
    }
});
</script>
@endpush
