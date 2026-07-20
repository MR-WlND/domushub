@extends('layouts.admin.master')

@section('page_title', 'Ghi chỉ số đơn lẻ – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>Ghi chỉ số đơn lẻ</h1>
        <p>Nhập chỉ số mới cho một căn hộ</p>
    </div>
    <a href="{{ portal_route('utility-readings.index') }}" class="util-btn util-btn--outline">
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

    <form action="{{ portal_route('utility-readings.store') }}" method="POST" id="createForm" enctype="multipart/form-data">
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
                    class="util-form-input" required style="background:#f8fafc; pointer-events:none;">
                    <option value="water" selected>Nước</option>
                </select>
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
                    Thay công tơ mới (Chỉ số cũ sẽ đặt về 0 cho kỳ này)
                </label>
            </div>
        </div>

        {{-- ── Ảnh minh chứng công tơ ── --}}
        <div class="util-form-group" style="margin-top: 15px; margin-bottom: 20px;">
            <label class="util-form-label" style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-camera" style="font-size: 16px; color: #475569;"></i>
                <span>
                    Ảnh minh chứng công tơ
                    <span style="font-weight:400; color:#64748b; font-size:12px;">(Tối đa 5 ảnh, mỗi ảnh ≤ 4MB)</span>
                </span>
            </label>

            {{-- Các nút chọn ảnh --}}
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px;">

                {{-- Nút chụp ảnh qua WebRTC (hoạt động cả laptop lẫn mobile) --}}
                <button type="button" id="btn_camera" onclick="openCameraModal()"
                    style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#00236f;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 8px rgba(0,35,111,0.15);transition:all 0.2s;"
                    onmouseover="this.style.background='#001850'"
                    onmouseout="this.style.background='#00236f'">
                    <i class="fa-solid fa-camera" style="font-size: 14px;"></i>
                    Chụp ảnh
                </button>

                {{-- Nút chọn từ thư viện --}}
                <button type="button" id="btn_gallery" onclick="document.getElementById('image_gallery').click()"
                    style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#fff;color:#334155;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;"
                    onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#94a3b8';"
                    onmouseout="this.style.background='#fff'; this.style.borderColor='#cbd5e1';">
                    <i class="fa-regular fa-image" style="font-size: 14px;"></i>
                    Chọn từ thư viện
                </button>
            </div>

            {{-- Gallery input (multiple, không có capture) --}}
            <input type="file" id="image_gallery" accept="image/*" multiple style="display:none;">

            {{-- Input ẩn thực sự submit --}}
            <input type="file" name="images[]" id="image_submit_proxy" accept="image/*" multiple style="display:none;">

            {{-- Gallery preview --}}
            <div id="image_preview_gallery" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:6px;"></div>

            <p class="util-form-hint" style="margin-top:8px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-regular fa-lightbulb" style="color: #eab308; font-size: 13px;"></i>
                <span>Nhấn <strong>Chụp ảnh</strong> để mở camera (laptop & điện thoại), hoặc <strong>Chọn từ thư viện</strong> để upload ảnh có sẵn.</span>
            </p>
        </div>

        {{-- ── Camera Modal (WebRTC) ── --}}
        <div id="cameraModalOverlay"
             style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;"
             onclick="closeCameraModal()">
        </div>
        <div id="cameraModal"
             style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:94vw;max-width:540px;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.3);z-index:10000;overflow:hidden;">
            <div style="background:#00236f;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
                <div style="color:#fff;font-weight:700;font-size:1rem;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-camera" style="font-size: 15px;"></i>
                    Chụp ảnh công tơ
                </div>
                <button type="button" onclick="closeCameraModal()"
                    style="background:rgba(255,255,255,.2);border:none;color:#fff;width:32px;height:32px;border-radius:50%;font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
            </div>

            {{-- Chọn camera (front/back) --}}
            <div style="padding:10px 16px;background:#f8fafc;display:flex;align-items:center;gap:10px;border-bottom:1px solid #e2e8f0;">
                <label style="font-size:12px;font-weight:600;color:#475569;">Camera:</label>
                <select id="cameraSelect" onchange="switchCamera()"
                    style="flex:1;padding:5px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;">
                    <option value="environment">Camera sau (chính)</option>
                    <option value="user">Camera trước (selfie)</option>
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
                    Đang khởi động camera...
                </div>
            </div>

            {{-- Nút chụp --}}
            <div style="padding:16px;display:flex;gap:10px;justify-content:center;background:#f8fafc;">
                <button type="button" onclick="capturePhoto()"
                    style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#00236f;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(0,35,111,0.25);transition:transform 0.1s;"
                    onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform=''"
                    onmouseover="this.style.background='#001850'"
                    onmouseout="this.style.background='#00236f'">
                    <i class="fa-solid fa-camera" style="font-size: 16px;"></i>
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


        {{-- Actions --}}
        <div class="util-form-actions">
            <button type="submit" class="util-btn util-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Lưu chỉ số
            </button>
            <a href="{{ portal_route('utility-readings.index') }}" class="util-btn util-btn--outline">Hủy</a>
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

        fetch(`{{ portal_route('utility-readings.get-old-value') }}?apartment_id=${aptId}&type=${t}&month=${m}&year=${y}`)
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

    // ── Multi-image upload & preview ────────────────────
    const MAX_IMAGES = 5;
    let allFiles = new DataTransfer();

    const gallery    = document.getElementById('image_preview_gallery');
    const camInput   = document.getElementById('image_camera');   // capture="environment", NO multiple
    const galInput   = document.getElementById('image_gallery');   // multiple, NO capture
    const proxyInput = document.getElementById('image_submit_proxy'); // thực sự submit

    function syncProxy() {
        // Gán toàn bộ files vào proxy input để form submit
        proxyInput.files = allFiles.files;
    }

    function addFiles(fileList) {
        const remaining = MAX_IMAGES - allFiles.files.length;
        if (remaining <= 0) {
            alert(`⚠️ Tối đa ${MAX_IMAGES} ảnh minh chứng. Xóa bớt ảnh cũ trước khi thêm.`);
            return;
        }
        let added = 0;
        for (let i = 0; i < fileList.length; i++) {
            if (added >= remaining) break;
            allFiles.items.add(fileList[i]);
            added++;
        }
        syncProxy();
        renderGallery();
    }

    function renderGallery() {
        gallery.innerHTML = '';
        const files = allFiles.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const wrapper = document.createElement('div');
            wrapper.style.cssText = 'position:relative; width:110px; height:110px; border-radius:10px; overflow:hidden; border:2px solid #e2e8f0; box-shadow:0 2px 8px rgba(0,0,0,.08);';

            const img = document.createElement('img');
            img.style.cssText = 'width:100%; height:100%; object-fit:cover;';
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; };
            reader.readAsDataURL(file);

            // Số thứ tự
            const badge = document.createElement('span');
            badge.textContent = i + 1;
            badge.style.cssText = 'position:absolute;top:5px;left:5px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;font-weight:700;padding:2px 6px;border-radius:6px;';

            // Nút xóa
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.innerHTML = '✕';
            delBtn.title = 'Xóa ảnh này';
            delBtn.dataset.idx = i;
            delBtn.style.cssText = 'position:absolute;top:4px;right:4px;background:rgba(239,68,68,.85);color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:12px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;';
            delBtn.addEventListener('click', function () {
                removeFileAt(parseInt(this.dataset.idx));
            });

            wrapper.appendChild(img);
            wrapper.appendChild(badge);
            wrapper.appendChild(delBtn);
            gallery.appendChild(wrapper);
        }

        // Dim nút khi đã đủ ảnh
        const btnCam = document.getElementById('btn_camera');
        const btnGal = document.getElementById('btn_gallery');
        const isFull = files.length >= MAX_IMAGES;
        btnCam.style.opacity = isFull ? '0.4' : '1';
        btnCam.style.pointerEvents = isFull ? 'none' : '';
        btnGal.style.opacity = isFull ? '0.4' : '1';
        btnGal.style.pointerEvents = isFull ? 'none' : '';
    }

    function removeFileAt(idx) {
        const newDT = new DataTransfer();
        const files = allFiles.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== idx) newDT.items.add(files[i]);
        }
        allFiles = newDT;
        syncProxy();
        renderGallery();
    }

    // Camera: chụp qua WebRTC modal (hoạt động cả laptop lẫn mobile)
    galInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            addFiles(this.files);
        }
        this.value = '';
    });

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

// ── Camera Modal WebRTC ───────────────────────────────────────────────────
let cameraStream = null;

async function openCameraModal() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal   = document.getElementById('cameraModal');
    const video   = document.getElementById('cameraVideo');
    const loading = document.getElementById('cameraLoading');

    overlay.style.display = 'block';
    modal.style.display   = 'block';
    document.body.style.overflow = 'hidden';
    loading.style.display = 'flex';

    const facing = document.getElementById('cameraSelect').value || 'environment';
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
        loading.textContent = '❌ Trình duyệt không hỗ trợ camera. Hãy dùng Chrome/Firefox/Safari mới nhất.';
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
        video.srcObject = cameraStream;
        video.onloadedmetadata = () => {
            loading.style.display = 'none';
        };
    } catch (err) {
        let msg = 'Không thể mở camera.';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            msg = 'Trình duyệt đã chặn quyền camera.\n\nHãy cho phép quyền camera trong thanh địa chỉ và thử lại.';
        } else if (err.name === 'NotFoundError') {
            msg = 'Không tìm thấy camera. Kiểm tra kết nối webcam.';
        } else if (err.name === 'NotReadableError') {
            msg = 'Camera đang được dùng bởi ứng dụng khác. Đóng ứng dụng đó và thử lại.';
        } else if (err.name === 'OverconstrainedError') {
            // Thử lại không ràng buộc facingMode
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = cameraStream;
                video.onloadedmetadata = () => { loading.style.display = 'none'; };
                return;
            } catch (e2) {
                msg = '❌ Không thể mở camera: ' + e2.message;
            }
        }
        loading.innerHTML = `<div style="text-align:center;padding:20px;color:#fff;">${msg}<br><br><button onclick="closeCameraModal()" style="padding:8px 16px;background:#fff;color:#1e293b;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Đóng</button></div>`;
    }
}

async function switchCamera() {
    const facing = document.getElementById('cameraSelect').value;
    const loading = document.getElementById('cameraLoading');
    loading.textContent = '🔄 Đang chuyển camera...';
    loading.style.display = 'flex';
    await startCamera(facing);
}

function capturePhoto() {
    const video  = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');

    if (!video.videoWidth || !video.videoHeight) {
        alert('Camera chưa sẵn sàng, vui lòng đợi.');
        return;
    }

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
        const dt   = new DataTransfer();
        dt.items.add(file);

        // Thêm vào danh sách ảnh (dùng addFiles của DOMContentLoaded scope)
        const evt = new Event('camera-captured');
        document.dispatchEvent(Object.assign(evt, { capturedFile: file }));

        closeCameraModal();
    }, 'image/jpeg', 0.92);
}

function closeCameraModal() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal   = document.getElementById('cameraModal');

    overlay.style.display = 'none';
    modal.style.display   = 'none';
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

// Lắng nghe event từ capturePhoto
document.addEventListener('camera-captured', function(e) {
    // Gọi addFiles từ scope DOMContentLoaded thông qua proxy
    const proxyInput = document.getElementById('image_submit_proxy');
    const galleryEl  = document.getElementById('image_preview_gallery');

    // Thêm vào allFiles (cần truy cập scope cha) – dùng cách gọi qua input proxy
    const dt = new DataTransfer();
    // Copy existing
    if (proxyInput.files) {
        for (let i = 0; i < proxyInput.files.length; i++) {
            dt.items.add(proxyInput.files[i]);
        }
    }

    const MAX = 5;
    if (dt.files.length >= MAX) {
        alert(`⚠️ Tối đa ${MAX} ảnh. Xóa bớt ảnh cũ trước khi thêm.`);
        return;
    }

    dt.items.add(e.capturedFile);
    proxyInput.files = dt.files;

    // Re-render preview
    renderPreviewFromProxy();
});

function renderPreviewFromProxy() {
    const proxyInput = document.getElementById('image_submit_proxy');
    const gallery    = document.getElementById('image_preview_gallery');
    const btnCam     = document.getElementById('btn_camera');
    const btnGal     = document.getElementById('btn_gallery');
    const MAX = 5;

    gallery.innerHTML = '';
    const files = proxyInput.files;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:relative;width:110px;height:110px;border-radius:10px;overflow:hidden;border:2px solid #e2e8f0;box-shadow:0 2px 8px rgba(0,0,0,.08);';

        const img = document.createElement('img');
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        reader.readAsDataURL(file);

        const badge = document.createElement('span');
        badge.textContent = i + 1;
        badge.style.cssText = 'position:absolute;top:5px;left:5px;background:rgba(0,0,0,.55);color:#fff;font-size:11px;font-weight:700;padding:2px 6px;border-radius:6px;';

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.innerHTML = '✕';
        delBtn.dataset.idx = i;
        delBtn.style.cssText = 'position:absolute;top:4px;right:4px;background:rgba(239,68,68,.85);color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;';
        delBtn.addEventListener('click', function() {
            const idx = parseInt(this.dataset.idx);
            const dt2 = new DataTransfer();
            for (let j = 0; j < proxyInput.files.length; j++) {
                if (j !== idx) dt2.items.add(proxyInput.files[j]);
            }
            proxyInput.files = dt2.files;
            renderPreviewFromProxy();
        });

        wrapper.appendChild(img);
        wrapper.appendChild(badge);
        wrapper.appendChild(delBtn);
        gallery.appendChild(wrapper);
    }

    const isFull = files.length >= MAX;
    btnCam.style.opacity = isFull ? '0.4' : '1';
    btnCam.style.pointerEvents = isFull ? 'none' : '';
    btnGal.style.opacity = isFull ? '0.4' : '1';
    btnGal.style.pointerEvents = isFull ? 'none' : '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCameraModal(); });
</script>
@endpush

