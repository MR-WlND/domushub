@extends('layouts.admin.master')

@section('page_title', 'Sửa chỉ số điện nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>Sửa chỉ số điện nước</h1>
        <p>
            Căn hộ <strong>{{ $reading->apartment->apartment_number }}</strong>
            – {{ $reading->type === 'electricity' ? 'Điện' : 'Nước' }}
            – Tháng {{ $reading->record_month }}/{{ $reading->record_year }}
        </p>
    </div>
    <a href="{{ route('admin.utility-readings.index', ['month' => $reading->record_month, 'year' => $reading->record_year]) }}"
        class="util-btn util-btn--outline">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Quay lại
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

{{-- ── Rejection Info ────────────────────────────────── --}}
@if($reading->status === 'rejected')
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; color: #b91c1c;">
        <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">❌ Chỉ số này đã bị từ chối</div>
        <div style="font-size: 13px; color: #991b1b;">
            <strong>Lý do từ chối:</strong> <em>{{ $reading->reject_reason }}</em>
        </div>
        <div style="font-size: 11px; margin-top: 6px; color: #b91c1c; opacity: 0.9;">
            Người từ chối: <strong>{{ $reading->rejecter->name ?? 'Kế toán viên' }}</strong>
        </div>
    </div>
@endif

{{-- ── Info Card ───────────────────────────────────── --}}
<div style="background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Căn hộ</div>
        <div style="font-size: 16px; font-weight: 700; color: #00236f; margin-top: 2px;">{{ $reading->apartment->apartment_number }}</div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Tòa / Tầng</div>
        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
            {{ $reading->apartment->floor->block->name ?? '—' }} / {{ $reading->apartment->floor->name ?? 'Tầng ' . $reading->apartment->floor->floor_number }}
        </div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Loại</div>
        <div style="margin-top: 4px;">
            @if ($reading->type === 'electricity')
                <span class="util-badge util-badge--electricity">Điện</span>
            @else
                <span class="util-badge util-badge--water">Nước</span>
            @endif
        </div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Kỳ ghi</div>
        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
            Tháng {{ $reading->record_month }}/{{ $reading->record_year }}
        </div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Người ghi</div>
        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
            {{ $reading->recorder->name ?? '—' }}
        </div>
    </div>
</div>

{{-- ── Form Card ───────────────────────────────────── --}}
<div class="util-form-card" style="max-width: 640px;">

    <div class="form-section-header">
        <div class="section-number">✎</div>
        <h4>Chỉnh sửa chỉ số</h4>
    </div>

    <form action="{{ route('admin.utility-readings.update', $reading->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="util-form-grid-3">
            @if(auth()->user()->role !== 'technician')
            <div class="util-form-group">
                <label class="util-form-label">Chỉ số cũ <span style="color:#ef4444">*</span></label>
                <input type="number" name="old_value" id="old_value"
                    value="{{ old('old_value', $reading->old_value) }}" min="0"
                    class="util-form-input {{ $errors->has('old_value') ? 'util-form-input--error' : '' }} util-form-input--readonly" 
                    required readonly>
                @error('old_value')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
                @if(auth()->user()->role === 'admin')
                <div style="margin-top: 6px;">
                    <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; font-size: 11px; font-weight: 600; color: #00236f;">
                        <input type="checkbox" id="manual_adjust_cb" style="width: 14px; height: 14px; accent-color: #00236f; cursor: pointer; margin: 0;">
                        Điều chỉnh thủ công
                    </label>
                </div>
                @endif
            </div>
            @else
            <input type="hidden" name="old_value" id="old_value" value="{{ $reading->old_value }}">
            @endif

            <div class="util-form-group" style="{{ auth()->user()->role === 'technician' ? 'grid-column: span 3;' : '' }}">
                <label class="util-form-label">Chỉ số mới <span style="color:#ef4444">*</span></label>
                <input type="number" name="new_value" id="new_value"
                    value="{{ old('new_value', $reading->new_value) }}" min="0"
                    class="util-form-input {{ $errors->has('new_value') ? 'util-form-input--error' : '' }}" required>
                @error('new_value')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            @if(auth()->user()->role !== 'technician')
            <div class="util-form-group">
                <label class="util-form-label">Tiêu thụ (tự tính)</label>
                <input type="text" id="usage_display" class="util-form-input util-form-input--readonly"
                    value="{{ number_format($reading->usage_amount) }}"
                    readonly style="font-weight: 700; color: #10b981;">
                <p class="util-form-hint">Cập nhật tự động</p>
            </div>
            @else
            <input type="hidden" id="usage_display" value="{{ $reading->usage_amount }}">
            @endif
        </div>

        {{-- ── Ảnh minh chứng hiện tại ── --}}
        @php
            $existingImages = $reading->images ?? [];
            if (empty($existingImages) && $reading->image_proof) {
                $existingImages = [$reading->image_proof];
            }
        @endphp
        @if (!empty($existingImages))
        <div style="margin-bottom:20px;">
            <label class="util-form-label">📷 Ảnh minh chứng hiện tại</label>
            <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:8px;">
                @foreach ($existingImages as $idx => $img)
                <div style="position:relative;width:120px;height:120px;border-radius:10px;overflow:hidden;border:2px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.06);">
                    <a href="{{ asset('storage/'.$img) }}" target="_blank">
                        <img src="{{ asset('storage/'.$img) }}" alt="Ảnh {{ $idx+1 }}"
                            style="width:100%;height:100%;object-fit:cover;">
                    </a>
                    <span style="position:absolute;top:4px;left:4px;background:rgba(0,0,0,.5);color:#fff;font-size:10px;font-weight:700;padding:2px 5px;border-radius:5px;">{{ $idx+1 }}</span>
                    {{-- Nút xóa ảnh --}}
                    <form method="POST" action="{{ route('admin.utility-readings.remove-image', $reading->id) }}"
                        style="position:absolute;top:3px;right:3px;"
                        onsubmit="return confirm('Xóa ảnh này?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="index" value="{{ $idx }}">
                        <button type="submit"
                            style="background:rgba(239,68,68,.85);color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            ✕
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Upload thêm ảnh mới ── --}}
        @if (count($existingImages) < 5)
        <div style="margin-bottom:20px;">
            <label class="util-form-label">
                ➕ Thêm ảnh minh chứng
                <span style="font-weight:400;color:#64748b;font-size:12px;">(Tối đa 5 ảnh tổng cộng)</span>
            </label>
            <div style="display:flex;gap:10px;flex-wrap:wrap;margin:10px 0;">
                <button type="button" onclick="document.getElementById('edit_cam').click()"
                    style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:linear-gradient(135deg,#0b57d0,#1a73e8);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
                    </svg>
                    Chụp ảnh
                </button>
                <button type="button" onclick="document.getElementById('edit_gal').click()"
                    style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:#fff;color:#1e293b;border:1.5px solid #cbd5e1;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;">
                    📁 Chọn ảnh
                </button>
            </div>
            <input type="file" id="edit_cam" name="images[]" accept="image/*" capture="environment" multiple style="display:none;">
            <input type="file" id="edit_gal" name="images[]" accept="image/*" multiple style="display:none;">
            <div id="edit_preview" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
        </div>
        @endif

        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #92400e;">
            ⚠️ Lưu ý: Sửa chỉ số cũ sẽ ảnh hưởng đến lượng tiêu thụ được tính toán.
        </div>

        <div class="util-form-actions">
            <button type="submit" class="util-btn util-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Cập nhật chỉ số
            </button>
            <a href="{{ route('admin.utility-readings.index', ['month' => $reading->record_month, 'year' => $reading->record_year]) }}"
                class="util-btn util-btn--outline">Hủy</a>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const oldValueInp = document.getElementById('old_value');
    const newValueInp = document.getElementById('new_value');
    const usageDisp   = document.getElementById('usage_display');
    const manualAdjustCb = document.getElementById('manual_adjust_cb');

    function calcUsage() {
        const oldVal = parseInt(oldValueInp.value) || 0;
        const newVal = parseInt(newValueInp.value) || 0;
        const usage  = Math.max(0, newVal - oldVal);
        usageDisp.value = usage.toLocaleString('vi-VN');
        usageDisp.style.color = usage > 0 ? '#10b981' : '#94a3b8';
    }

    if (manualAdjustCb) {
        manualAdjustCb.addEventListener('change', function() {
            if (this.checked) {
                oldValueInp.removeAttribute('readonly');
                oldValueInp.classList.remove('util-form-input--readonly');
            } else {
                oldValueInp.setAttribute('readonly', true);
                oldValueInp.classList.add('util-form-input--readonly');
                oldValueInp.value = "{{ $reading->old_value }}";
                calcUsage();
            }
        });
    }

    oldValueInp.addEventListener('input', calcUsage);
    newValueInp.addEventListener('input', calcUsage);

    // ── Multi-image upload cho edit ─────────────────
    let editDT = new DataTransfer();
    const MAX_EDIT = 5 - {{ count($existingImages ?? []) }};
    const editCam = document.getElementById('edit_cam');
    const editGal = document.getElementById('edit_gal');
    const editPreview = document.getElementById('edit_preview');

    function addEditFiles(files) {
        const remaining = MAX_EDIT - editDT.files.length;
        let added = 0;
        for (let i = 0; i < files.length && added < remaining; i++) {
            editDT.items.add(files[i]);
            added++;
        }
        if (editCam) editCam.files = editDT.files;
        renderEditPreview();
    }

    function renderEditPreview() {
        if (!editPreview) return;
        editPreview.innerHTML = '';
        for (let i = 0; i < editDT.files.length; i++) {
            const file = editDT.files[i];
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;width:90px;height:90px;border-radius:8px;overflow:hidden;border:2px solid #e2e8f0;';
            const img = document.createElement('img');
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; };
            reader.readAsDataURL(file);
            const del = document.createElement('button');
            del.type = 'button';
            del.innerHTML = '✕';
            del.dataset.idx = i;
            del.style.cssText = 'position:absolute;top:3px;right:3px;background:rgba(239,68,68,.85);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;';
            del.addEventListener('click', function() {
                const idx = parseInt(this.dataset.idx);
                const newDT = new DataTransfer();
                for (let j = 0; j < editDT.files.length; j++) {
                    if (j !== idx) newDT.items.add(editDT.files[j]);
                }
                editDT = newDT;
                if (editCam) editCam.files = editDT.files;
                renderEditPreview();
            });
            wrap.appendChild(img);
            wrap.appendChild(del);
            editPreview.appendChild(wrap);
        }
    }

    if (editCam) {
        editCam.addEventListener('change', function() { addEditFiles(this.files); this.value = ''; });
    }
    if (editGal) {
        editGal.addEventListener('change', function() {
            const files = this.files;
            const remaining = MAX_EDIT - editDT.files.length;
            for (let i = 0; i < files.length && i < remaining; i++) editDT.items.add(files[i]);
            if (editCam) editCam.files = editDT.files;
            renderEditPreview();
            this.value = '';
        });
    }
});
</script>
@endpush
