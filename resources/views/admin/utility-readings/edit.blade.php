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

{{-- ── Success Alert ─────────────────────────────────── --}}
@if (session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;color:#166534;font-size:14px;font-weight:600;">
        ✓ {{ session('success') }}
    </div>
@endif

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
    <div style="margin-bottom: 20px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
            <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 16px 20px; display: flex; align-items: center;">
                <div style="background: #fee2e2; color: #ef4444; border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <span style="font-size: 14px; font-weight: 700; color: #1e293b;">Lý do từ chối</span>
            </div>
            @if($rejections && $rejections->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; vertical-align: middle;">
                        <thead>
                            <tr style="background: #f8fafc; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; font-weight: 700;">
                                <th style="padding: 10px 16px; width: 25%;">Thời gian</th>
                                <th style="padding: 10px 16px; width: 25%;">Người từ chối</th>
                                <th style="padding: 10px 16px; width: 50%;">Lý do cụ thể</th>
                            </tr>
                        </thead>
                        <tbody style="color: #334155;">
                            @foreach($rejections as $rej)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #94a3b8; font-weight: 400; white-space: nowrap; vertical-align: top;">{{ $rej['rejected_at'] }}</td>
                                <td style="padding: 12px 16px; font-weight: 500; color: #64748b; vertical-align: top;">{{ $rej['rejecter_name'] }}</td>
                                <td style="padding: 12px 16px; vertical-align: top;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; font-weight: 700; font-size: 12px;">
                                        <i class="fas fa-exclamation-triangle animate-pulse" style="font-size: 11px;"></i>
                                        <span>{{ $rej['reason'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 16px 20px; font-size: 13px; color: #991b1b;">
                    <strong>Lý do từ chối:</strong> <em>{{ $reading->reject_reason }}</em>
                    <div style="font-size: 11px; margin-top: 6px; color: #b91c1c; opacity: 0.9;">
                        Người từ chối: <strong>{{ $reading->rejecter->name ?? 'Kế toán viên' }}</strong> | Ngày từ chối: <strong>{{ $reading->updated_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            @endif
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
                    <a href="javascript:void(0)" onclick="openLightbox({{ $idx }})" style="display:block;width:100%;height:100%;cursor:pointer;transition:transform .15s;"
                       onmouseenter="this.style.transform='scale(1.03)';"
                       onmouseleave="this.style.transform='';">
                        <img src="{{ asset('storage/'.$img) }}" alt="Ảnh {{ $idx+1 }}"
                            style="width:100%;height:100%;object-fit:cover;">
                    </a>
                    <span style="position:absolute;top:4px;left:4px;background:rgba(0,0,0,.5);color:#fff;font-size:10px;font-weight:700;padding:2px 5px;border-radius:5px;pointer-events:none;">{{ $idx+1 }}</span>
                    {{-- Nút xóa ảnh --}}
                    <button type="button"
                        onclick="deleteExistingImage({{ $idx }})"
                        style="position:absolute;top:3px;right:3px;z-index:2;background:rgba(239,68,68,.85);color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        ✕
                    </button>
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
                <button type="button" onclick="openCameraModal()"
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
            <input type="file" id="edit_gal" accept="image/*" multiple style="display:none;">
            <input type="file" name="images[]" id="edit_cam" accept="image/*" multiple style="display:none;">
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

{{-- Lightbox --}}
<div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;align-items:center;justify-content:center;flex-direction:column;">
    <button type="button" onclick="closeLightbox()" style="position:absolute;top:18px;right:22px;background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:50%;width:40px;height:40px;font-size:20px;cursor:pointer;line-height:1;">✕</button>
    <button type="button" onclick="prevImg()" id="lb_prev" style="position:absolute;left:18px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:50%;width:44px;height:44px;font-size:22px;cursor:pointer;">‹</button>
    <button type="button" onclick="nextImg()" id="lb_next" style="position:absolute;right:18px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:50%;width:44px;height:44px;font-size:22px;cursor:pointer;">›</button>
    <img id="lb_img" src="" alt="" style="max-width:90vw;max-height:82vh;border-radius:10px;box-shadow:0 8px 40px rgba(0,0,0,.5);">
    <div style="margin-top:12px;color:rgba(255,255,255,.7);font-size:13px;" id="lb_counter"></div>
</div>

{{-- Form xóa ảnh ẩn --}}
<form id="delete-image-form" method="POST" action="{{ route('admin.utility-readings.remove-image', $reading->id) }}" style="display:none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="index" id="delete-image-index" value="">
</form>

{{-- ── Camera Modal (WebRTC) ── --}}
<div id="cameraModalOverlay"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;"
     onclick="closeCameraModal()">
</div>
<div id="cameraModal"
     style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:94vw;max-width:540px;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.3);z-index:10000;overflow:hidden;">
    <div style="background:linear-gradient(135deg,#0b57d0,#1a73e8);padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
        <div style="color:#fff;font-weight:700;font-size:1rem;">📸 Chụp ảnh công tơ</div>
        <button type="button" onclick="closeCameraModal()"
            style="background:rgba(255,255,255,.2);border:none;color:#fff;width:32px;height:32px;border-radius:50%;font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    {{-- Chọn camera (front/back) --}}
    <div style="padding:10px 16px;background:#f8fafc;display:flex;align-items:center;gap:10px;border-bottom:1px solid #e2e8f0;">
        <label style="font-size:12px;font-weight:600;color:#475569;">Camera:</label>
        <select id="cameraSelect" onchange="switchCamera()"
            style="flex:1;padding:5px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;">
            <option value="environment">📷 Camera sau (chính)</option>
            <option value="user">🤳 Camera trước (selfie)</option>
        </select>
    </div>

    {{-- Video preview --}}
    <div style="background:#000;position:relative;aspect-ratio:4/3;max-height:360px;overflow:hidden;">
        <video id="cameraVideo" autoplay playsinline muted
            style="width:100%;height:100%;object-fit:cover;display:block;"></video>
        {{-- Khung ngắm --}}
        <div style="position:absolute;inset:0;pointer-events:none;">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:70%;height:70%;border:2px dashed rgba(255,255,255,0.5);border-radius:8px;"></div>
        </div>
        {{-- Loading text --}}
        <div id="cameraLoading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;background:rgba(0,0,0,0.5);">
            🔄 Đang khởi động camera...
        </div>
    </div>

    {{-- Nút chụp --}}
    <div style="padding:16px;display:flex;gap:10px;justify-content:center;background:#f8fafc;">
        <button type="button" onclick="capturePhoto()"
            style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:linear-gradient(135deg,#0b57d0,#1a73e8);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(11,87,208,.35);transition:transform .1s;"
            onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform=''">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
            Chụp ảnh
        </button>
        <button type="button" onclick="closeCameraModal()"
            style="padding:12px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;">
            Hủy
        </button>
    </div>

    {{-- Canvas ẩn để capture --}}
    <canvas id="cameraCanvas" style="display:none;"></canvas>
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
    editCam = document.getElementById('edit_cam');
    editGal = document.getElementById('edit_gal');
    editPreview = document.getElementById('edit_preview');

    if (editGal) {
        editGal.addEventListener('change', function() {
            if (this.files.length > 0) {
                addEditFiles(this.files);
            }
            this.value = '';
        });
    }
});

// ── Global Multi-image state & helpers ──────────
let editDT = new DataTransfer();
const MAX_EDIT = 5 - {{ count($existingImages ?? []) }};
let editCam = null;
let editGal = null;
let editPreview = null;

function addEditFiles(files) {
    const remaining = MAX_EDIT - editDT.files.length;
    if (remaining <= 0) {
        alert(`⚠️ Tối đa 5 ảnh tổng cộng. Vui lòng xóa bớt ảnh cũ trước khi thêm.`);
        return;
    }
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

const lightboxImgs = @json(array_map(fn($p) => asset('storage/'.$p), $existingImages));
let lbIdx = 0;

function openLightbox(idx) {
    lbIdx = idx;
    const lb = document.getElementById('lightbox');
    if (lb) lb.style.display = 'flex';
    updateLightbox();
    document.addEventListener('keydown', handleLbKey);
}
function closeLightbox() {
    const lb = document.getElementById('lightbox');
    if (lb) lb.style.display = 'none';
    document.removeEventListener('keydown', handleLbKey);
}
function updateLightbox() {
    const lbImg = document.getElementById('lb_img');
    const lbCounter = document.getElementById('lb_counter');
    const lbPrev = document.getElementById('lb_prev');
    const lbNext = document.getElementById('lb_next');
    
    if (lbImg) lbImg.src = lightboxImgs[lbIdx];
    if (lbCounter) lbCounter.textContent = `Ảnh ${lbIdx+1} / ${lightboxImgs.length}`;
    if (lbPrev) lbPrev.style.display = lightboxImgs.length > 1 ? '' : 'none';
    if (lbNext) lbNext.style.display = lightboxImgs.length > 1 ? '' : 'none';
}
function prevImg() { lbIdx = (lbIdx - 1 + lightboxImgs.length) % lightboxImgs.length; updateLightbox(); }
function nextImg() { lbIdx = (lbIdx + 1) % lightboxImgs.length; updateLightbox(); }
function handleLbKey(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevImg();
    if (e.key === 'ArrowRight') nextImg();
}
// Click backdrop to close
document.addEventListener('DOMContentLoaded', function() {
    const lb = document.getElementById('lightbox');
    if (lb) {
        lb.addEventListener('click', function(e) {
            if (e.target === this) closeLightbox();
        });
    }
});

function deleteExistingImage(idx) {
    if (confirm('Xóa ảnh này?')) {
        const form = document.getElementById('delete-image-form');
        const input = document.getElementById('delete-image-index');
        if (form && input) {
            input.value = idx;
            form.submit();
        }
    }
}

// ── Camera Modal WebRTC ───────────────────────────────────────────────────
let cameraStream = null;

async function openCameraModal() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal   = document.getElementById('cameraModal');
    const video   = document.getElementById('cameraVideo');
    const loading = document.getElementById('cameraLoading');

    if (overlay) overlay.style.display = 'flex';
    if (modal) modal.style.display   = 'block';
    document.body.style.overflow = 'hidden';
    if (loading) loading.style.display = 'flex';

    const facing = document.getElementById('cameraSelect')?.value || 'environment';
    await startCamera(facing);
}

async function startCamera(facing) {
    const video   = document.getElementById('cameraVideo');
    const loading = document.getElementById('cameraLoading');

    // Dừng stream cũ nếu có
    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (loading) loading.textContent = '❌ Trình duyệt không hỗ trợ camera. Hãy dùng Chrome/Firefox/Safari mới nhất.';
        return;
    }

    try {
        const constraints = {
            video: {
                facingMode: facing,
                width:  { ideal: 1920 },
                height: { ideal: 1080 },
            }
        };
        cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        if (video) video.srcObject = cameraStream;
        if (video) {
            video.onloadedmetadata = () => {
                if (loading) loading.style.display = 'none';
            };
        }
    } catch (err) {
        let msg = '❌ Không thể mở camera.';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            msg = '🔒 Trình duyệt đã chặn quyền camera.\n\nHãy cho phép quyền camera trong thanh địa chỉ và thử lại.';
        } else if (err.name === 'NotFoundError') {
            msg = '📷 Không tìm thấy camera. Kiểm tra kết nối webcam.';
        } else if (err.name === 'NotReadableError') {
            msg = '⚠️ Camera đang được dùng bởi ứng dụng khác. Đóng ứng dụng đó và thử lại.';
        } else if (err.name === 'OverconstrainedError') {
            // Thử lại không ràng buộc facingMode
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
                if (video) video.srcObject = cameraStream;
                if (video) {
                    video.onloadedmetadata = () => { if (loading) loading.style.display = 'none'; };
                }
                return;
            } catch (e2) {
                msg = '❌ Không thể mở camera: ' + e2.message;
            }
        }
        if (loading) loading.innerHTML = `<div style="text-align:center;padding:20px;color:#fff;">${msg}<br><br><button onclick="closeCameraModal()" style="padding:8px 16px;background:#fff;color:#1e293b;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Đóng</button></div>`;
    }
}

async function switchCamera() {
    const facing = document.getElementById('cameraSelect').value;
    const loading = document.getElementById('cameraLoading');
    if (loading) {
        loading.textContent = '🔄 Đang chuyển camera...';
        loading.style.display = 'flex';
    }
    await startCamera(facing);
}

function capturePhoto() {
    const video  = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');

    if (!video || !video.videoWidth || !video.videoHeight) {
        alert('Camera chưa sẵn sàng, vui lòng đợi.');
        return;
    }

    if (canvas) {
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(blob => {
            if (!blob) { alert('Không thể chụp ảnh.'); return; }

            // Flash effect
            const flash = document.createElement('div');
            flash.style.cssText = 'position:fixed;inset:0;background:#fff;opacity:0.8;z-index:99999;pointer-events:none;transition:opacity .3s;';
            document.body.appendChild(flash);
            setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 300); }, 50);

            const ts   = Date.now();
            const file = new File([blob], `camera_${ts}.jpg`, { type: 'image/jpeg' });
            
            // Add file programmatically
            addEditFiles([file]);

            closeCameraModal();
        }, 'image/jpeg', 0.92);
    }
}

function closeCameraModal() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal   = document.getElementById('cameraModal');

    if (overlay) overlay.style.display = 'none';
    if (modal) modal.style.display   = 'none';
    document.body.style.overflow = '';

    if (cameraStream) {
        cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
    }

    const video = document.getElementById('cameraVideo');
    if (video) video.srcObject = null;
    const loading = document.getElementById('cameraLoading');
    if (loading) {
        loading.style.display = 'flex';
        loading.textContent   = '🔄 Đang khởi động camera...';
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCameraModal(); });
</script>
@endpush

@push('styles')
<style>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
</style>
@endpush

