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
</script>
@endsection
