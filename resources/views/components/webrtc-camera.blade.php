{{-- ── Global Camera Modal (WebRTC) ── --}}
<div id="globalCameraModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;" onclick="closeGlobalCameraModal()"></div>
<div id="globalCameraModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:94vw;max-width:540px;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.3);z-index:10000;overflow:hidden;">
    <div style="background:#00236f;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
        <div style="color:#fff;font-weight:700;font-size:1rem;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-camera" style="font-size: 15px;"></i>
            Chụp ảnh
        </div>
        <button type="button" onclick="closeGlobalCameraModal()" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:32px;height:32px;border-radius:50%;font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    {{-- Chọn camera (front/back) --}}
    <div style="padding:10px 16px;background:#f8fafc;display:flex;align-items:center;gap:10px;border-bottom:1px solid #e2e8f0;">
        <label style="font-size:12px;font-weight:600;color:#475569;">Camera:</label>
        <select id="globalCameraSelect" onchange="switchGlobalCamera()" style="flex:1;padding:5px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;">
            <option value="environment">Camera sau (chính)</option>
            <option value="user">Camera trước (selfie)</option>
        </select>
    </div>

    {{-- Video preview --}}
    <div style="background:#000;position:relative;aspect-ratio:4/3;max-height:360px;overflow:hidden;">
        <video id="globalCameraVideo" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;display:block;"></video>
        {{-- Loading text --}}
        <div id="globalCameraLoading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;background:rgba(0,0,0,0.5);z-index:20;">
            Đang khởi động camera...
        </div>
    </div>

    {{-- Nút chụp --}}
    <div style="padding:16px;display:flex;gap:10px;justify-content:center;background:#f8fafc;">
        <button type="button" onclick="captureGlobalPhoto()" style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#00236f;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(0,35,111,0.25);transition:transform 0.1s;" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform=''" onmouseover="this.style.background='#001850'" onmouseout="this.style.background='#00236f'">
            <i class="fa-solid fa-camera" style="font-size: 16px;"></i>
            Chụp ảnh
        </button>
        <button type="button" onclick="closeGlobalCameraModal()" style="padding:12px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;">
            Hủy
        </button>
    </div>

    {{-- Canvas ẩn để capture --}}
    <canvas id="globalCameraCanvas" style="display:none;"></canvas>
</div>

<script>
let globalCameraStream = null;

async function openGlobalCameraModal(prefix = 'camera') {
    globalCameraPrefix = prefix;
    const overlay = document.getElementById('globalCameraModalOverlay');
    const modal   = document.getElementById('globalCameraModal');
    const loading = document.getElementById('globalCameraLoading');

    overlay.style.display = 'block';
    modal.style.display   = 'block';
    document.body.style.overflow = 'hidden';
    loading.style.display = 'flex';

    const facing = document.getElementById('globalCameraSelect').value || 'environment';
    await startGlobalCamera(facing);
}

async function startGlobalCamera(facing) {
    const video   = document.getElementById('globalCameraVideo');
    const loading = document.getElementById('globalCameraLoading');

    if (globalCameraStream) {
        globalCameraStream.getTracks().forEach(t => t.stop());
        globalCameraStream = null;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        loading.textContent = '❌ Trình duyệt không hỗ trợ camera. Hãy dùng Chrome/Firefox/Safari mới nhất.';
        return;
    }

    try {
        const constraints = { video: { facingMode: facing, width: { ideal: 1920 }, height: { ideal: 1080 } } };
        globalCameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = globalCameraStream;
        video.onloadedmetadata = () => { loading.style.display = 'none'; };
    } catch (err) {
        let msg = 'Không thể mở camera.';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            msg = 'Trình duyệt đã chặn quyền camera. Hãy cho phép quyền camera trong thanh địa chỉ và thử lại.';
        } else {
            try {
                globalCameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                video.srcObject = globalCameraStream;
                video.onloadedmetadata = () => { loading.style.display = 'none'; };
                return;
            } catch (e2) {
                try {
                    globalCameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
                    video.srcObject = globalCameraStream;
                    video.onloadedmetadata = () => { loading.style.display = 'none'; };
                    return;
                } catch (e3) {
                    if (err.name === 'NotFoundError') { msg = 'Không tìm thấy camera. Kiểm tra kết nối webcam.'; } 
                    else if (err.name === 'NotReadableError') { msg = 'Camera đang được dùng bởi ứng dụng khác.'; } 
                    else { msg = 'Không thể mở camera: ' + (err.message || err.name); }
                }
            }
        }
        loading.innerHTML = `<div style="text-align:center;padding:20px;color:#fff;">${msg}<br><br><button onclick="closeGlobalCameraModal()" style="padding:8px 16px;background:#fff;color:#1e293b;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Đóng</button></div>`;
    }
}

async function switchGlobalCamera() {
    const facing = document.getElementById('globalCameraSelect').value;
    const loading = document.getElementById('globalCameraLoading');
    loading.textContent = '🔄 Đang chuyển camera...';
    loading.style.display = 'flex';
    await startGlobalCamera(facing);
}

function captureGlobalPhoto() {
    const video  = document.getElementById('globalCameraVideo');
    const canvas = document.getElementById('globalCameraCanvas');

    if (!video.videoWidth || !video.videoHeight) {
        alert('Camera chưa sẵn sàng, vui lòng đợi.');
        return;
    }

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(blob => {
        if (!blob) { alert('Không thể chụp ảnh.'); return; }

        const flash = document.createElement('div');
        flash.style.cssText = 'position:fixed;inset:0;background:#fff;opacity:0.8;z-index:99999;pointer-events:none;transition:opacity .3s;';
        document.body.appendChild(flash);
        setTimeout(() => { flash.style.opacity = '0'; setTimeout(() => flash.remove(), 300); }, 50);

        const ts   = Date.now();
        const file = new File([blob], `${globalCameraPrefix}_${ts}.jpg`, { type: 'image/jpeg' });
        
        window.dispatchEvent(new CustomEvent('global-camera-captured', { detail: { file: file } }));

        closeGlobalCameraModal();
    }, 'image/jpeg', 0.92);
}

function closeGlobalCameraModal() {
    const overlay = document.getElementById('globalCameraModalOverlay');
    const modal   = document.getElementById('globalCameraModal');

    overlay.style.display = 'none';
    modal.style.display   = 'none';
    document.body.style.overflow = '';

    if (globalCameraStream) {
        globalCameraStream.getTracks().forEach(t => t.stop());
        globalCameraStream = null;
    }

    const video = document.getElementById('globalCameraVideo');
    if (video) video.srcObject = null;
    const loading = document.getElementById('globalCameraLoading');
    if (loading) {
        loading.style.display = 'flex';
        loading.textContent   = '🔄 Đang khởi động camera...';
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape' && document.getElementById('globalCameraModal').style.display === 'block') closeGlobalCameraModal(); });
</script>
