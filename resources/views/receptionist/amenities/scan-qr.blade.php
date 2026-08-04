@extends('layouts.receptionist.master')

@section('page_title', 'Quét QR Check-in Tiện ích – Lễ tân DomusHub')

@section('topbar_left')
<nav style="font-size:13px;color:#A3AED0;display:flex;align-items:center;gap:6px;">
    <a href="{{ route('receptionist.dashboard') }}" style="color:#7B3FE4;text-decoration:none;">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
    <a href="{{ route('receptionist.amenities.index') }}" style="color:#7B3FE4;text-decoration:none;">Đặt lịch tiện ích</a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
    <span>Quét QR Check-in</span>
</nav>
@endsection

@section('content')
<style>
.rq-page { max-width: 1100px; margin: 0 auto; }
.rq-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.rq-title { font-size: 24px; font-weight: 700; color: #00236f; margin: 0; }
.rq-btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #f1f5f9; color: #475569; border-radius: 8px; font-weight: 600; font-size: 13px; text-decoration: none; transition: all 0.2s; }
.rq-btn-back:hover { background: #e2e8f0; color: #0f172a; }

.rq-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media (max-width: 850px) { .rq-layout { grid-template-columns: 1fr; } }

.rq-card { background: #fff; border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
.rq-card-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }

/* Camera box */
.rq-cam-box { position: relative; width: 100%; height: 280px; background: #0f172a; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.rq-cam-placeholder { text-align: center; color: #94a3b8; padding: 20px; }
.rq-cam-placeholder i { font-size: 48px; margin-bottom: 12px; color: #64748b; }

.rq-cam-btn { width: 100%; padding: 12px; margin-top: 16px; border: none; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
.rq-cam-btn--start { background: linear-gradient(135deg, #7B3FE4, #5B24B7); color: #fff; box-shadow: 0 4px 12px rgba(123,63,228,0.25); }
.rq-cam-btn--start:hover { opacity: 0.9; }
.rq-cam-btn--stop { background: #ef4444; color: #fff; }
.rq-cam-btn--stop:hover { background: #dc2626; }

.rq-manual { margin-top: 24px; padding-top: 20px; border-top: 1px dashed #e2e8f0; }
.rq-manual-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; display: block; }
.rq-manual-row { display: flex; gap: 8px; }
.rq-manual-input { flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; outline: none; }
.rq-manual-input:focus { border-color: #7B3FE4; }
.rq-manual-btn { padding: 10px 18px; background: #0f172a; color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; white-space: nowrap; }

/* Result side */
.rq-idle { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; min-height: 350px; text-align: center; color: #94a3b8; }
.rq-idle-icon { width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; color: #7B3FE4; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 16px; }

.rq-status-bar { padding: 12px 16px; border-radius: 10px; font-weight: 700; font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.rq-status-bar--success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.rq-status-bar--error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.rq-info-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
.rq-info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
.rq-info-row:last-child { border-bottom: none; }
.rq-info-label { color: #64748b; font-weight: 500; }
.rq-info-val { color: #0f172a; font-weight: 700; text-align: right; }

.rq-checkin-btn { width: 100%; padding: 14px; background: #16a34a; color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 15px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(22,163,74,0.25); }
.rq-checkin-btn:hover { background: #15803d; }
.rq-reset-btn { width: 100%; padding: 10px; background: #f1f5f9; color: #475569; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer; margin-top: 8px; }

.rq-toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; color: #fff; z-index: 9999; display: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
.rq-toast--success { background: #16a34a; }
.rq-toast--error { background: #dc2626; }
</style>

<div class="rq-page">
    <div class="rq-header">
        <h1 class="rq-title"><i class="fa-solid fa-qrcode" style="color:#7B3FE4;"></i> Quét QR Check-in Tiện ích</h1>
        <a href="{{ route('receptionist.amenities.index') }}" class="rq-btn-back">
            <i class="fa-solid fa-arrow-left"></i> Về danh sách
        </a>
    </div>

    <div class="rq-layout">
        {{-- LEFT PANEL: SCANNER --}}
        <div class="rq-card">
            <h2 class="rq-card-title"><i class="fa-solid fa-camera" style="color:#7B3FE4;"></i> Máy quét QR</h2>
            
            <div class="rq-cam-box">
                <div id="cam-placeholder" class="rq-cam-placeholder">
                    <i class="fa-solid fa-camera-retro"></i>
                    <p style="margin:0;font-size:13px;">Nhấn nút bên dưới để bật Camera</p>
                </div>
                <div id="reader" style="width:100%;display:none;"></div>
            </div>

            <button type="button" class="rq-cam-btn rq-cam-btn--start" id="start-btn" onclick="startCamera()">
                <i class="fa-solid fa-power-off"></i> Bật Camera
            </button>
            <button type="button" class="rq-cam-btn rq-cam-btn--stop" id="stop-btn" style="display:none;" onclick="stopCamera()">
                <i class="fa-solid fa-stop"></i> Tắt Camera
            </button>

            <div class="rq-manual">
                <span class="rq-manual-label">Nhập mã QR thủ công:</span>
                <div class="rq-manual-row">
                    <input type="text" id="manual-input" class="rq-manual-input" placeholder="Dán chuỗi mã QR tại đây...">
                    <button type="button" class="rq-manual-btn" onclick="manualScan()">Kiểm tra</button>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: RESULT --}}
        <div class="rq-card">
            <h2 class="rq-card-title"><i class="fa-solid fa-clipboard-check" style="color:#7B3FE4;"></i> Kết quả quét</h2>

            {{-- Idle State --}}
            <div id="idle-state" class="rq-idle">
                <div class="rq-idle-icon">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3 style="font-size:16px;color:#475569;margin:0 0 4px;">Sẵn sàng kiểm tra</h3>
                <p style="font-size:13px;margin:0;">Vui lòng quét mã QR trên ứng dụng cư dân để xác thực lịch đặt tiện ích.</p>
            </div>

            {{-- Scan Result --}}
            <div id="scan-result" style="display:none; flex-direction:column;">
                <div id="status-bar" class="rq-status-bar"></div>
                <div id="result-body"></div>
                <div id="result-actions"></div>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="rq-toast"></div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
let html5QrcodeScanner = null;
let lastScannedToken = null;

function startCamera() {
    document.getElementById('cam-placeholder').style.display = 'none';
    document.getElementById('reader').style.display = 'block';
    document.getElementById('start-btn').style.display = 'none';
    document.getElementById('stop-btn').style.display = 'flex';

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
        showToast('Không thể bật camera: ' + err, 'error');
        stopCamera();
    });
}

function stopCamera() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().catch(() => {});
        html5QrcodeScanner = null;
    }
    document.getElementById('cam-placeholder').style.display = 'block';
    document.getElementById('reader').style.display = 'none';
    document.getElementById('start-btn').style.display = 'flex';
    document.getElementById('stop-btn').style.display = 'none';
}

function manualScan() {
    const val = document.getElementById('manual-input').value.trim();
    if (!val) { showToast('Vui lòng nhập mã QR', 'error'); return; }
    doScan(val);
}

document.getElementById('manual-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') manualScan();
});

function doScan(token) {
    document.getElementById('idle-state').style.display = 'none';
    document.getElementById('scan-result').style.display = 'flex';
    
    const bar = document.getElementById('status-bar');
    bar.className = 'rq-status-bar';
    bar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tra cứu dữ liệu...';
    document.getElementById('result-body').innerHTML = '';
    document.getElementById('result-actions').innerHTML = '';

    fetch('{{ route("receptionist.amenities.scan-qr.scan") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_code: token })
    })
    .then(r => r.json())
    .then(data => renderResult(data, token))
    .catch(() => renderError('Lỗi kết nối máy chủ. Vui lòng thử lại.', token));
}

function renderResult(data, token) {
    const bar = document.getElementById('status-bar');
    const body = document.getElementById('result-body');
    const actions = document.getElementById('result-actions');

    if (data.success) {
        const b = data.booking;
        bar.className = 'rq-status-bar rq-status-bar--success';
        bar.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + data.message;

        body.innerHTML = `
            <div class="rq-info-card">
                <div class="rq-info-row"><span class="rq-info-label">Tiện ích:</span><span class="rq-info-val" style="color:#7B3FE4;font-size:15px;">${b.facility_name}</span></div>
                <div class="rq-info-row"><span class="rq-info-label">Cư dân:</span><span class="rq-info-val">${b.user_name}</span></div>
                <div class="rq-info-row"><span class="rq-info-label">Ngày sử dụng:</span><span class="rq-info-val">${b.booking_date}</span></div>
                <div class="rq-info-row"><span class="rq-info-label">Khung giờ:</span><span class="rq-info-val">${b.start_time} – ${b.end_time}</span></div>
                <div class="rq-info-row"><span class="rq-info-label">Số người:</span><span class="rq-info-val">${b.number_of_people} người</span></div>
                <div class="rq-info-row"><span class="rq-info-label">Thanh toán:</span><span class="rq-info-val">${b.amount > 0 ? b.amount.toLocaleString('vi-VN') + 'đ (' + (b.payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán') + ')' : 'Miễn phí'}</span></div>
            </div>
        `;

        actions.innerHTML = `
            <button class="rq-checkin-btn" onclick="confirmCheckin('${token}')">
                <i class="fa-solid fa-circle-check"></i> Xác nhận Check-in
            </button>
            <button class="rq-reset-btn" onclick="resetPanel()">Bỏ qua</button>
        `;
    } else {
        bar.className = 'rq-status-bar rq-status-bar--error';
        bar.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> ' + data.message;

        const b = data.booking;
        if (b) {
            body.innerHTML = `
                <div class="rq-info-card">
                    <div class="rq-info-row"><span class="rq-info-label">Tiện ích:</span><span class="rq-info-val">${b.facility_name}</span></div>
                    <div class="rq-info-row"><span class="rq-info-label">Cư dân:</span><span class="rq-info-val">${b.user_name}</span></div>
                    <div class="rq-info-row"><span class="rq-info-label">Ngày sử dụng:</span><span class="rq-info-val">${b.booking_date} (${b.start_time} - ${b.end_time})</span></div>
                    <div class="rq-info-row"><span class="rq-info-label">Trạng thái:</span><span class="rq-info-val" style="color:#ef4444;">${b.status_label}</span></div>
                </div>
            `;
        }
        actions.innerHTML = `<button class="rq-reset-btn" onclick="resetPanel()">Quét lại</button>`;
    }
}

function renderError(msg) {
    const bar = document.getElementById('status-bar');
    bar.className = 'rq-status-bar rq-status-bar--error';
    bar.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> ' + msg;
    document.getElementById('result-actions').innerHTML = `<button class="rq-reset-btn" onclick="resetPanel()">Thử lại</button>`;
}

function confirmCheckin(token) {
    const btn = document.querySelector('.rq-checkin-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...'; }

    fetch('{{ route("receptionist.amenities.scan-qr.checkin") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_code: token })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(resetPanel, 2000);
        } else {
            showToast(data.message || 'Lỗi Check-in.', 'error');
        }
    })
    .catch(() => showToast('Lỗi kết nối.', 'error'));
}

function resetPanel() {
    document.getElementById('idle-state').style.display = 'flex';
    document.getElementById('scan-result').style.display = 'none';
    document.getElementById('manual-input').value = '';
    lastScannedToken = null;
}

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.className = `rq-toast rq-toast--${type}`;
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3500);
}
</script>
@endsection
