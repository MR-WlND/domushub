@extends('layouts.security.master')

@section('page_title', 'Quét QR Xe Vào — DomusHub')

@push('styles')
    @vite(['resources/css/security/qr-scanner.css'])
@endpush

@section('content')
<div class="qs-page">

    {{-- Page Header --}}
    <div class="qs-header">
        <p class="qs-eyebrow">Quản lý bãi xe</p>
        <h1 class="qs-title">Quét QR — Xe Vào</h1>
    </div>

    <div class="qs-layout">

        {{-- ===== LEFT: CAMERA SCANNER ===== --}}
        <div class="qs-panel">
            <div class="qs-panel__header">
                <div class="qs-panel__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h10M7 12h10M7 17h10"/></svg>
                </div>
                <h2 class="qs-panel__title">Camera Quét QR</h2>
            </div>
            <div class="qs-panel__body">

                {{-- Camera viewport --}}
                <div class="qs-camera-box" id="camera-box">
                    <div class="qs-cam-placeholder" id="cam-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <p style="margin:0;font-size:.85rem;">Bấm "Bật Camera" để bắt đầu quét</p>
                    </div>
                    <div id="reader" style="width:100%;display:none;"></div>
                    <div class="qs-scanner-frame" id="scan-frame" style="display:none;">
                        <div class="qs-scanner-frame__inner">
                            <div class="qs-corner qs-corner--tl"></div>
                            <div class="qs-corner qs-corner--tr"></div>
                            <div class="qs-corner qs-corner--bl"></div>
                            <div class="qs-corner qs-corner--br"></div>
                            <div class="qs-scan-line"></div>
                        </div>
                    </div>
                </div>

                {{-- Camera controls --}}
                <button type="button" class="qs-start-btn" id="start-btn" onclick="startCamera()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                    Bật Camera
                </button>
                <button type="button" class="qs-stop-btn" id="stop-btn" style="display:none;" onclick="stopCamera()">Tắt Camera</button>

                {{-- Manual input --}}
                <div class="qs-manual">
                    <p class="qs-manual__label">Hoặc nhập mã thủ công:</p>
                    <div class="qs-manual__row">
                        <input type="text" id="manual-input" class="qs-manual__input" placeholder="Nhập biển số xe (VD: 29A-12345)">
                        <button type="button" class="qs-manual__btn" onclick="manualScan()">Tra cứu</button>
                    </div>
                </div>

                {{-- Recent log --}}
                <div class="qs-log" id="log-section" style="display:none;">
                    <p class="qs-log__title">Đã xử lý gần đây</p>
                    <div id="log-list"></div>
                </div>

            </div>
        </div>

        {{-- ===== RIGHT: RESULT PANEL ===== --}}
        <div class="qs-result" id="result-panel">
            <div class="qs-result__idle" id="idle-state">
                <div class="qs-result__idle-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <h3 class="qs-result__idle-title">Sẵn sàng quét</h3>
                <p style="margin:0;font-size:.85rem;">Đưa mã QR trên xe vào khung camera</p>
            </div>

            {{-- Populated after scan --}}
            <div id="scan-result" style="display:none;flex-direction:column;flex:1;">
                <div class="qs-result__status-bar" id="status-bar">
                    <div class="qs-status-dot"></div>
                    <span class="qs-status-msg" id="status-msg"></span>
                </div>
                <div class="qs-result__body" id="result-body"></div>
                <div class="qs-result__actions" id="result-actions"></div>
            </div>
        </div>

    </div>

    {{-- ===== ĐĂNG KÝ XE KHÁCH VÃNG LAI ===== --}}
    <div class="qs-panel" style="margin-top:24px;">
        <div class="qs-panel__header">
            <div class="qs-panel__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17h2m10 0h2M2 9l2-6h16l2 6M2 9h20v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V9z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
            </div>
            <h2 class="qs-panel__title">Đăng ký xe khách vãng lai</h2>
        </div>
        <div class="qs-panel__body">
            <form id="guest-vehicle-form" onsubmit="submitGuestVehicle(event)" style="display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:.8rem;font-weight:600;color:#334155;margin-bottom:4px;display:block;">Biển số xe <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="guest-plate" required placeholder="VD: 29A-12345" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none;transition:border .2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:600;color:#334155;margin-bottom:4px;display:block;">Loại xe <span style="color:#ef4444;">*</span></label>
                        <select id="guest-vehicle-type" required style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none;background:#fff;transition:border .2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                            <option value="motorbike">Xe máy</option>
                            <option value="electric_bike">Xe điện</option>
                            <option value="car">Ô tô</option>
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="font-size:.8rem;font-weight:600;color:#334155;margin-bottom:4px;display:block;">Tên khách</label>
                        <input type="text" id="guest-name" placeholder="Họ tên khách (tuỳ chọn)" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none;transition:border .2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:600;color:#334155;margin-bottom:4px;display:block;">SĐT khách</label>
                        <input type="text" id="guest-phone" placeholder="Số điện thoại (tuỳ chọn)" style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none;transition:border .2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>
                <div>
                    <label style="font-size:.8rem;font-weight:600;color:#334155;margin-bottom:4px;display:block;">Ghi chú</label>
                    <input type="text" id="guest-note" placeholder="VD: Khách đến giao hàng, thăm phòng 302..." style="width:100%;padding:10px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.875rem;outline:none;transition:border .2s;" onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <button type="submit" id="guest-submit-btn" style="padding:12px 20px;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Ghi nhận xe vào
                </button>
            </form>
            <div id="guest-result" style="margin-top:12px;display:none;"></div>
        </div>
    </div>

</div>

{{-- Toast --}}
<div class="qs-toast" id="toast">
    <span id="toast-icon"></span>
    <span id="toast-text"></span>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
let html5QrcodeScanner = null;
let lastScannedToken = null;
let scanning = false;
const logs = [];

// ---- Camera ----
function startCamera() {
    document.getElementById('cam-placeholder').style.display = 'none';
    document.getElementById('reader').style.display = 'block';
    document.getElementById('scan-frame').style.display = 'flex';
    document.getElementById('start-btn').style.display = 'none';
    document.getElementById('stop-btn').style.display = 'block';
    scanning = true;

    html5QrcodeScanner = new Html5Qrcode("reader");
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        (decodedText) => {
            if (decodedText !== lastScannedToken) {
                lastScannedToken = decodedText;
                doScan(decodedText);
            }
        },
        () => {}
    ).catch(err => {
        showToast('Không thể truy cập camera: ' + err, 'error');
        stopCamera();
    });
}

function stopCamera() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().catch(() => {});
        html5QrcodeScanner = null;
    }
    scanning = false;
    document.getElementById('cam-placeholder').style.display = 'flex';
    document.getElementById('reader').style.display = 'none';
    document.getElementById('scan-frame').style.display = 'none';
    document.getElementById('start-btn').style.display = 'flex';
    document.getElementById('stop-btn').style.display = 'none';
}

function manualScan() {
    const val = document.getElementById('manual-input').value.trim().toUpperCase().replace(/\s/g,'');
    if (!val) { showToast('Vui lòng nhập biển số xe', 'error'); return; }
    doScan(val);
}

document.getElementById('manual-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') manualScan();
});

// ---- Core scan ----
function doScan(token) {
    setIdleState(false);
    document.getElementById('result-body').innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:3rem;gap:.75rem;color:#94a3b8;"><div class="qs-spin"></div>Đang tra cứu...</div>';
    document.getElementById('result-actions').innerHTML = '';
    document.getElementById('status-bar').className = 'qs-result__status-bar';
    document.getElementById('status-msg').textContent = 'Đang xử lý...';

    fetch('{{ route("security.vehicle-checkin.scan") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_token: token })
    })
    .then(r => r.json())
    .then(data => renderResult(data, token))
    .catch(() => renderError('Lỗi kết nối mạng. Vui lòng thử lại.', token));
}

function renderResult(data, token) {
    const bar = document.getElementById('status-bar');
    const msg = document.getElementById('status-msg');
    const body = document.getElementById('result-body');
    const actions = document.getElementById('result-actions');

    if (data.success) {
        bar.className = 'qs-result__status-bar is-success';
        msg.textContent = data.warning ? '⚠ Cảnh báo — Được phép vào' : '✓ Xe hợp lệ — Cho phép vào';

        const v = data.vehicle;
        body.innerHTML = `
            <div class="qs-info-card">
                <div class="qs-info-card__thumb">
                    ${v.image ? `<img src="${v.image}" alt="xe">` : vehicleIcon(v.vehicle_type)}
                </div>
                <div class="qs-info-card__content">
                    <h3 class="qs-info-card__name">${v.license_plate}</h3>
                    <p class="qs-info-card__sub">${v.vehicle_type} ${v.brand ? '· ' + v.brand : ''}</p>
                    <div class="qs-info-rows">
                        <div class="qs-info-row"><span class="qs-info-row__label">Căn hộ</span><span class="qs-info-row__val">${v.apartment} — ${v.block}</span></div>
                        <div class="qs-info-row"><span class="qs-info-row__label">Lốt đỗ</span><span class="qs-info-row__val">${v.parking_lot || 'Không có'}</span></div>
                        <div class="qs-info-row"><span class="qs-info-row__label">Trạng thái</span><span class="qs-info-row__val"><span class="qs-badge qs-badge--active">${v.status_label}</span></span></div>
                    </div>
                </div>
            </div>
            ${data.warning ? `<div class="qs-warn-box"><span>⚠</span><span>${data.warning}</span></div>` : ''}
        `;

        actions.innerHTML = `
            <button class="qs-action-btn qs-action-btn--confirm" onclick="doCheckin('${token}')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Xác nhận Xe Vào
            </button>
            <button class="qs-action-btn qs-action-btn--reset" onclick="resetPanel()">Huỷ</button>
        `;

    } else {
        bar.className = 'qs-result__status-bar is-error';
        msg.textContent = '✕ ' + (data.message || 'Không hợp lệ');

        const v = data.vehicle;
        body.innerHTML = v ? `
            <div class="qs-info-card">
                <div class="qs-info-card__thumb">${vehicleIcon(v.vehicle_type)}</div>
                <div class="qs-info-card__content">
                    <h3 class="qs-info-card__name">${v.license_plate}</h3>
                    <p class="qs-info-card__sub">${v.vehicle_type}</p>
                    <div class="qs-info-rows">
                        <div class="qs-info-row"><span class="qs-info-row__label">Trạng thái</span><span class="qs-info-row__val"><span class="qs-badge qs-badge--danger">${v.status_label}</span></span></div>
                        <div class="qs-info-row"><span class="qs-info-row__label">Căn hộ</span><span class="qs-info-row__val">${v.apartment}</span></div>
                    </div>
                </div>
            </div>
        ` : `<div style="padding:2rem;text-align:center;color:#94a3b8;">${data.message || 'Không tìm thấy thông tin xe.'}</div>`;

        actions.innerHTML = `<button class="qs-action-btn qs-action-btn--reset" onclick="resetPanel()">Quét lại</button>`;
    }

    lastScannedToken = null;
}

function renderError(msg) {
    const bar = document.getElementById('status-bar');
    bar.className = 'qs-result__status-bar is-error';
    document.getElementById('status-msg').textContent = '✕ ' + msg;
    document.getElementById('result-body').innerHTML = `<div style="padding:2rem;text-align:center;color:#f87171;">${msg}</div>`;
    document.getElementById('result-actions').innerHTML = `<button class="qs-action-btn qs-action-btn--reset" onclick="resetPanel()">Thử lại</button>`;
    lastScannedToken = null;
}

function doCheckin(token) {
    const btn = document.querySelector('.qs-action-btn--confirm');
    if (btn) { btn.disabled = true; btn.innerHTML = '<div class="qs-spin"></div> Đang ghi nhận...'; }

    fetch('{{ route("security.vehicle-checkin.confirm") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_token: token })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            addLog(token, 'in');
            setTimeout(resetPanel, 2500);
        } else {
            showToast(data.message || 'Lỗi ghi nhận.', 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối.', 'error'));
}

function resetPanel() {
    setIdleState(true);
    lastScannedToken = null;
    document.getElementById('manual-input').value = '';
}

function setIdleState(isIdle) {
    document.getElementById('idle-state').style.display = isIdle ? 'flex' : 'none';
    const sr = document.getElementById('scan-result');
    sr.style.display = isIdle ? 'none' : 'flex';
}

function addLog(plate, direction) {
    logs.unshift({ plate, direction, time: new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'}) });
    if (logs.length > 5) logs.pop();
    const section = document.getElementById('log-section');
    const list = document.getElementById('log-list');
    section.style.display = 'block';
    list.innerHTML = logs.map(l => `
        <div class="qs-log-item">
            <div class="qs-log-dot qs-log-dot--${l.direction}"></div>
            <span class="qs-log-plate">${l.plate}</span>
            <span class="qs-log-time">${l.time}</span>
        </div>
    `).join('');
}

function vehicleIcon(type) {
    return `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>`;
}

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.className = `qs-toast qs-toast--${type}`;
    document.getElementById('toast-icon').innerHTML = type === 'success' ? '✓' : '✕';
    document.getElementById('toast-text').textContent = msg;
    t.classList.add('is-visible');
    setTimeout(() => t.classList.remove('is-visible'), 3500);
}

// ---- Guest Vehicle Registration ----
function submitGuestVehicle(e) {
    e.preventDefault();
    const btn = document.getElementById('guest-submit-btn');
    const resultDiv = document.getElementById('guest-result');
    const plate = document.getElementById('guest-plate').value.trim();
    const vehicleType = document.getElementById('guest-vehicle-type').value;
    const guestName = document.getElementById('guest-name').value.trim();
    const guestPhone = document.getElementById('guest-phone').value.trim();
    const guestNote = document.getElementById('guest-note').value.trim();

    if (!plate) { showToast('Vui lòng nhập biển số xe', 'error'); return; }

    btn.disabled = true;
    btn.innerHTML = '<div class="qs-spin"></div> Đang ghi nhận...';

    fetch('{{ route("security.vehicle-checkin.guest") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({
            guest_plate: plate,
            guest_vehicle_type: vehicleType,
            guest_name: guestName || null,
            guest_phone: guestPhone || null,
            guest_note: guestNote || null
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `<div style="padding:12px 16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:8px;color:#065f46;font-size:.85rem;font-weight:500;">${data.message}</div>`;
            document.getElementById('guest-vehicle-form').reset();
            addLog(plate, 'in');
        } else {
            showToast(data.message || 'Có lỗi xảy ra', 'error');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `<div style="padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#991b1b;font-size:.85rem;font-weight:500;">${data.message}</div>`;
        }
    })
    .catch(() => {
        showToast('Lỗi kết nối mạng', 'error');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Ghi nhận xe vào';
    });
}
</script>
@endsection
