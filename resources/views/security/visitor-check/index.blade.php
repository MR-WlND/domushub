@extends('layouts.security.master')

@section('page_title', 'Quản lý khách — DomusHub')

@push('styles')
    @vite(['resources/css/security/qr-scanner.css'])
@endpush

@section('content')
<div class="qs-page">

    {{-- Page Header --}}
    <div class="qs-header">
        <p class="qs-eyebrow">Quản lý khách</p>
        <h1 class="qs-title">Danh sách khách vào / ra</h1>
    </div>

    <div class="qs-layout">

        {{-- ===== LEFT: CAMERA SCANNER ===== --}}
        <div class="qs-panel">
            <div class="qs-panel__header">
                <div class="qs-panel__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h2 class="qs-panel__title">Quét mã QR khách</h2>
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
                    <p class="qs-manual__label">Nhập mã QR Token khách thủ công:</p>
                    <div class="qs-manual__row">
                        <input type="text" id="manual-input" class="qs-manual__input" placeholder="Dán mã QR Token khách">
                        <button type="button" class="qs-manual__btn" onclick="manualScan()">Xác thực</button>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <h3 class="qs-result__idle-title">Sẵn sàng kiểm tra khách</h3>
                <p style="margin:0;font-size:.85rem;">Quét QR do cư dân chia sẻ để xác thực</p>
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
        { fps: 20, qrbox: { width: 280, height: 280 }, aspectRatio: 1.0, experimentalFeatures: { useBarCodeDetectorIfSupported: true } },
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
    const val = document.getElementById('manual-input').value.trim();
    if (!val) { showToast('Vui lòng nhập mã QR khách', 'error'); return; }
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

    fetch('{{ route("security.visitor-check.scan") }}', {
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
        const v = data.visitor;
        const action = data.action; // 'checkin' or 'checkout'

        if (action === 'checkin') {
            bar.className = 'qs-result__status-bar is-success';
            msg.textContent = '✓ QR hợp lệ — Cho phép vào';
        } else {
            bar.className = 'qs-result__status-bar is-warning';
            msg.textContent = '⚠ Khách đã ở trong — Cho phép ra';
        }

        body.innerHTML = `
            <div class="qs-info-card">
                <div class="qs-info-card__thumb">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="qs-info-card__content">
                    <h3 class="qs-info-card__name">${v.guest_name}</h3>
                    <p class="qs-info-card__sub">${v.guest_phone || 'Không có số điện thoại'}</p>
                    <div class="qs-info-rows">
                        <div class="qs-info-row"><span class="qs-info-row__label">Căn hộ</span><span class="qs-info-row__val">${v.apartment} — ${v.block}</span></div>
                        <div class="qs-info-row"><span class="qs-info-row__label">Cư dân tạo</span><span class="qs-info-row__val">${v.registered_by}</span></div>
                        <div class="qs-info-row"><span class="qs-info-row__label">Hạn sử dụng</span><span class="qs-info-row__val" style="color:#f59e0b;">${v.expired_at}</span></div>
                        ${v.has_vehicle ? `<div class="qs-info-row"><span class="qs-info-row__label">Xe khách</span><span class="qs-info-row__val" style="font-weight:700;color:#2563eb;">🚗 ${v.vehicle_plate} (${v.vehicle_type})</span></div>` : ''}
                        ${v.note ? `<div class="qs-info-row"><span class="qs-info-row__label">Ghi chú</span><span class="qs-info-row__val">${v.note}</span></div>` : ''}
                        ${data.check_in_at ? `<div class="qs-info-row"><span class="qs-info-row__label">Giờ vào</span><span class="qs-info-row__val">${data.check_in_at}</span></div>` : ''}
                        <div class="qs-info-row"><span class="qs-info-row__label">Trạng thái</span><span class="qs-info-row__val"><span class="qs-badge qs-badge--${badgeColor(v.status)}">${v.status_label}</span></span></div>
                    </div>
                </div>
            </div>
            ${v.has_vehicle ? `<div class="qs-warn-box" style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;"><span>🚗</span><span>Khách có gửi xe <strong>${v.vehicle_plate}</strong> — cho phép xe vào bãi khách.</span></div>` : ''}
        `;

        if (action === 'checkin') {
            actions.innerHTML = `
                <button class="qs-action-btn qs-action-btn--confirm" onclick="confirmAction('checkin', '${token}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Xác nhận Cho Vào
                </button>
                <button class="qs-action-btn qs-action-btn--reset" onclick="resetPanel()">Huỷ</button>
            `;
        } else {
            actions.innerHTML = `
                <button class="qs-action-btn qs-action-btn--checkout" onclick="confirmAction('checkout', '${token}')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    Xác nhận Cho Ra
                </button>
                <button class="qs-action-btn qs-action-btn--reset" onclick="resetPanel()">Huỷ</button>
            `;
        }

    } else {
        bar.className = 'qs-result__status-bar is-error';
        msg.textContent = '✕ ' + (data.message || 'Mã không hợp lệ');

        const v = data.visitor;
        body.innerHTML = v ? `
            <div class="qs-info-card">
                <div class="qs-info-card__thumb">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="qs-info-card__content">
                    <h3 class="qs-info-card__name">${v.guest_name}</h3>
                    <p class="qs-info-card__sub">${v.guest_phone || 'Không có số điện thoại'}</p>
                    <div class="qs-info-rows">
                        <div class="qs-info-row"><span class="qs-info-row__label">Trạng thái</span><span class="qs-info-row__val"><span class="qs-badge qs-badge--danger">${v.status_label}</span></span></div>
                        <div class="qs-info-row"><span class="qs-info-row__label">Căn hộ</span><span class="qs-info-row__val">${v.apartment}</span></div>
                    </div>
                </div>
            </div>
            <div class="qs-warn-box" style="background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3);color:#fca5a5;">
                <span>✕</span><span>${data.message || 'Mã QR không còn giá trị sử dụng.'}</span>
            </div>
        ` : `<div style="padding:2rem;text-align:center;color:#94a3b8;">${data.message || 'Không tìm thấy thông tin khách.'}</div>`;

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

function confirmAction(type, token) {
    const btn = type === 'checkin' ? document.querySelector('.qs-action-btn--confirm') : document.querySelector('.qs-action-btn--checkout');
    if (btn) { btn.disabled = true; btn.innerHTML = '<div class="qs-spin"></div> Đang ghi nhận...'; }

    const url = type === 'checkin' ? '{{ route("security.visitor-check.checkin") }}' : '{{ route("security.visitor-check.checkout") }}';

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_token: token })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            addLog(token, type === 'checkin' ? 'in' : 'out');
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

function addLog(token, direction) {
    // Truncate UUID token for display
    const label = 'Khách (...' + token.substring(0, 8) + ')';
    logs.unshift({ label, direction, time: new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit'}) });
    if (logs.length > 5) logs.pop();
    const section = document.getElementById('log-section');
    const list = document.getElementById('log-list');
    section.style.display = 'block';
    list.innerHTML = logs.map(l => `
        <div class="qs-log-item">
            <div class="qs-log-dot qs-log-dot--${l.direction === 'in' ? 'in' : 'out'}"></div>
            <span class="qs-log-plate">${l.label}</span>
            <span class="qs-log-time">${l.time}</span>
        </div>
    `).join('');
}

function badgeColor(status) {
    return status === 'pending' ? 'pending' : (status === 'checked_in' ? 'active' : (status === 'checked_out' ? 'gray' : 'danger'));
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
