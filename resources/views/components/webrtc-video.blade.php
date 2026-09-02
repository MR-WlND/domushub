{{-- ── Global Video Recorder Modal (WebRTC) ── --}}
<div id="globalVideoModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;" onclick="if(!globalIsRecording) closeGlobalVideoModal()"></div>
<div id="globalVideoModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:94vw;max-width:540px;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.3);z-index:10000;overflow:hidden;">
    <div style="background:#0f172a;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
        <div style="color:#fff;font-weight:700;font-size:1rem;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-video" style="font-size: 15px;"></i>
            Quay video
        </div>
        <button type="button" id="globalVideoCloseBtn" onclick="closeGlobalVideoModal()" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:32px;height:32px;border-radius:50%;font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>

    {{-- Chọn camera (front/back) --}}
    <div style="padding:10px 16px;background:#f8fafc;display:flex;align-items:center;gap:10px;border-bottom:1px solid #e2e8f0;">
        <label style="font-size:12px;font-weight:600;color:#475569;">Camera:</label>
        <select id="globalVideoSelect" onchange="switchGlobalVideo()" style="flex:1;padding:5px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;">
            <option value="environment">Camera sau (chính)</option>
            <option value="user">Camera trước (selfie)</option>
        </select>
    </div>

    {{-- Video preview --}}
    <div style="background:#000;position:relative;aspect-ratio:4/3;max-height:360px;overflow:hidden;">
        <video id="globalVideoPreview" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;display:block;"></video>
        
        {{-- Record indicator --}}
        <div id="globalVideoRecordIndicator" style="display:none;position:absolute;top:12px;right:12px;background:rgba(220,38,38,0.9);color:white;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;align-items:center;gap:6px;">
            <div style="width:8px;height:8px;background:#fff;border-radius:50%;animation:pulse 1s infinite alternate;"></div>
            <span id="globalVideoTime">00:00</span>
        </div>
        
        {{-- Loading text --}}
        <div id="globalVideoLoading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;background:rgba(0,0,0,0.5);z-index:20;">
            Đang khởi động camera...
        </div>
    </div>

    {{-- Nút quay --}}
    <div style="padding:16px;display:flex;gap:10px;justify-content:center;background:#f8fafc;">
        <button type="button" id="globalVideoStartBtn" onclick="startGlobalRecording()" style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#dc2626;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(220,38,38,0.25);transition:background 0.2s;">
            <div style="width:12px;height:12px;background:#fff;border-radius:50%;"></div>
            Bắt đầu quay
        </button>
        
        <button type="button" id="globalVideoStopBtn" onclick="stopGlobalRecording()" style="display:none;align-items:center;gap:8px;padding:12px 28px;background:#0f172a;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(15,23,42,0.25);">
            <div style="width:12px;height:12px;background:#ef4444;border-radius:2px;"></div>
            Dừng quay
        </button>
        
        <button type="button" id="globalVideoCancelBtn" onclick="closeGlobalVideoModal()" style="padding:12px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:12px;font-size:14px;font-weight:600;cursor:pointer;">
            Hủy
        </button>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 1; }
    100% { opacity: 0.3; }
}
</style>

<script>
let globalVideoStream = null;
let globalMediaRecorder = null;
let globalVideoChunks = [];
let globalIsRecording = false;
let globalVideoTimer = null;
let globalVideoSeconds = 0;
let globalVideoPrefix = 'video';

async function openGlobalVideoModal(prefix = 'video') {
    globalVideoPrefix = prefix;
    const overlay = document.getElementById('globalVideoModalOverlay');
    const modal   = document.getElementById('globalVideoModal');
    const loading = document.getElementById('globalVideoLoading');

    overlay.style.display = 'block';
    modal.style.display   = 'block';
    document.body.style.overflow = 'hidden';
    loading.style.display = 'flex';
    
    document.getElementById('globalVideoStartBtn').style.display = 'inline-flex';
    document.getElementById('globalVideoStopBtn').style.display = 'none';
    document.getElementById('globalVideoRecordIndicator').style.display = 'none';
    document.getElementById('globalVideoCancelBtn').style.display = 'inline-block';
    document.getElementById('globalVideoCloseBtn').style.display = 'flex';
    document.getElementById('globalVideoSelect').disabled = false;

    const facing = document.getElementById('globalVideoSelect').value || 'environment';
    await startGlobalVideo(facing);
}

async function startGlobalVideo(facing) {
    const video   = document.getElementById('globalVideoPreview');
    const loading = document.getElementById('globalVideoLoading');

    if (globalVideoStream) {
        globalVideoStream.getTracks().forEach(t => t.stop());
        globalVideoStream = null;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        loading.textContent = '❌ Trình duyệt không hỗ trợ camera. Hãy dùng Chrome/Firefox/Safari mới nhất.';
        return;
    }

    try {
        const constraints = { 
            video: { facingMode: facing, width: { ideal: 1280 }, height: { ideal: 720 } },
            audio: true 
        };
        globalVideoStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = globalVideoStream;
        video.onloadedmetadata = () => { loading.style.display = 'none'; };
    } catch (err) {
        let msg = 'Không thể mở camera.';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            msg = 'Trình duyệt đã chặn quyền camera/microphone. Hãy cho phép trong thanh địa chỉ và thử lại.';
        } else {
            try {
                globalVideoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                video.srcObject = globalVideoStream;
                video.onloadedmetadata = () => { loading.style.display = 'none'; };
                return;
            } catch (e2) {
                msg = 'Không thể mở camera: ' + (err.message || err.name);
            }
        }
        loading.innerHTML = `<div style="text-align:center;padding:20px;color:#fff;">${msg}<br><br><button onclick="closeGlobalVideoModal()" style="padding:8px 16px;background:#fff;color:#1e293b;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Đóng</button></div>`;
    }
}

async function switchGlobalVideo() {
    const facing = document.getElementById('globalVideoSelect').value;
    const loading = document.getElementById('globalVideoLoading');
    loading.textContent = '🔄 Đang chuyển camera...';
    loading.style.display = 'flex';
    await startGlobalVideo(facing);
}

function startGlobalRecording() {
    if (!globalVideoStream) return;
    
    globalVideoChunks = [];
    
    try {
        globalMediaRecorder = new MediaRecorder(globalVideoStream, { mimeType: 'video/webm' });
    } catch (e) {
        try {
            globalMediaRecorder = new MediaRecorder(globalVideoStream, { mimeType: 'video/mp4' });
        } catch (e2) {
            globalMediaRecorder = new MediaRecorder(globalVideoStream);
        }
    }
    
    globalMediaRecorder.ondataavailable = function(e) {
        if (e.data.size > 0) {
            globalVideoChunks.push(e.data);
        }
    };
    
    globalMediaRecorder.onstop = function() {
        const blob = new Blob(globalVideoChunks, { type: globalMediaRecorder.mimeType || 'video/webm' });
        const ts = Date.now();
        
        let ext = 'webm';
        if (blob.type.includes('mp4')) ext = 'mp4';
        
        const file = new File([blob], `${globalVideoPrefix}_${ts}.${ext}`, { type: blob.type });
        
        window.dispatchEvent(new CustomEvent('global-video-recorded', { detail: { file: file } }));
        closeGlobalVideoModal();
    };
    
    globalMediaRecorder.start();
    globalIsRecording = true;
    
    // UI changes
    document.getElementById('globalVideoStartBtn').style.display = 'none';
    document.getElementById('globalVideoStopBtn').style.display = 'inline-flex';
    document.getElementById('globalVideoRecordIndicator').style.display = 'flex';
    document.getElementById('globalVideoCancelBtn').style.display = 'none';
    document.getElementById('globalVideoCloseBtn').style.display = 'none';
    document.getElementById('globalVideoSelect').disabled = true;
    
    globalVideoSeconds = 0;
    updateGlobalVideoTimer();
    globalVideoTimer = setInterval(updateGlobalVideoTimer, 1000);
}

function stopGlobalRecording() {
    if (globalMediaRecorder && globalIsRecording) {
        globalMediaRecorder.stop();
        globalIsRecording = false;
        clearInterval(globalVideoTimer);
        document.getElementById('globalVideoStopBtn').innerHTML = 'Đang xử lý...';
        document.getElementById('globalVideoStopBtn').disabled = true;
    }
}

function updateGlobalVideoTimer() {
    const mins = Math.floor(globalVideoSeconds / 60).toString().padStart(2, '0');
    const secs = (globalVideoSeconds % 60).toString().padStart(2, '0');
    document.getElementById('globalVideoTime').textContent = `${mins}:${secs}`;
    globalVideoSeconds++;
    
    // Auto stop after 60 seconds (optional limit)
    if (globalVideoSeconds > 60) {
        stopGlobalRecording();
    }
}

function closeGlobalVideoModal() {
    if (globalIsRecording) return; // Prevent closing while recording
    
    const overlay = document.getElementById('globalVideoModalOverlay');
    const modal   = document.getElementById('globalVideoModal');

    overlay.style.display = 'none';
    modal.style.display   = 'none';
    document.body.style.overflow = '';

    if (globalVideoStream) {
        globalVideoStream.getTracks().forEach(t => t.stop());
        globalVideoStream = null;
    }

    const video = document.getElementById('globalVideoPreview');
    if (video) video.srcObject = null;
    
    clearInterval(globalVideoTimer);
    
    // Reset buttons
    document.getElementById('globalVideoStopBtn').innerHTML = '<div style="width:12px;height:12px;background:#ef4444;border-radius:2px;"></div>Dừng quay';
    document.getElementById('globalVideoStopBtn').disabled = false;
}

document.addEventListener('keydown', e => { 
    if (e.key === 'Escape' && document.getElementById('globalVideoModal').style.display === 'block') {
        closeGlobalVideoModal(); 
    }
});
</script>
