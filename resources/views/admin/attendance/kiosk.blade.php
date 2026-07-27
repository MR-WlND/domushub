@extends('layouts.admin.master')

@section('title', 'Kiosk Chấm Công FaceID AI')

@push('styles')
<style>
    .kiosk-container {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    .video-card {
        flex: 2;
        min-width: 320px;
        background: #0f172a;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        color: #fff;
        position: relative;
    }
    .log-card {
        flex: 1;
        min-width: 300px;
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }
    .video-viewport {
        position: relative;
        width: 100%;
        height: 440px;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }
    video, canvas {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        object-fit: cover;
    }
    .kiosk-status {
        position: absolute;
        top: 16px; left: 16px; right: 16px;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(4px);
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        z-index: 10;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .scan-log-item {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .scan-log-item:last-child { border-bottom: none; }
    .badge-in { background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 11px; }
    .badge-out { background: #e0f2fe; color: #0369a1; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 11px; }
</style>
@endpush

@section('content')
<div class="container-fluid padding-horizontal-lg padding-vertical-md">

    <div style="margin-bottom:20px;">
        <h1 style="margin:0; font-size:24px; font-weight:800; color:#0f172a;">🤖 Kiosk Chấm Công AI FaceID</h1>
        <p style="margin:4px 0 0; color:#64748b; font-size:14px;">Trạm nhận diện khuôn mặt tự động thời gian thực phục vụ Check-in / Check-out cho Nhân sự.</p>
    </div>

    <div class="kiosk-container">
        {{-- Webcam Card --}}
        <div class="video-card">
            <div class="kiosk-status" id="kioskStatus">
                <span style="width:10px; height:10px; border-radius:50%; background:#ef4444; display:inline-block;" id="statusDot"></span>
                <span id="statusText">Đang khởi tạo máy quét FaceID...</span>
            </div>

            <div class="video-viewport">
                <video id="webcam" autoplay playsinline muted></video>
                <canvas id="overlay"></canvas>
            </div>
        </div>

        {{-- Realtime Log Card --}}
        <div class="log-card">
            <h3 style="margin:0 0 16px; font-size:16px; font-weight:800; color:#0f172a;">Lịch Sử Chấm Công Vừa Nhận Diện</h3>
            <div id="logList" style="max-height: 400px; overflow-y: auto;">
                <p style="text-align:center; color:#94a3b8; padding:30px 0;">Đang chờ người chấm công...</p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
<script>
    const webcam = document.getElementById('webcam');
    const overlay = document.getElementById('overlay');
    const statusDot = document.getElementById('statusDot');
    const statusText = document.getElementById('statusText');
    const logList = document.getElementById('logList');

    const MODEL_URL = '/models';
    const rawStaffs = @json($staffs);

    let faceMatcher = null;
    let isProcessing = false;
    let lastScanTime = {}; // Lock per staff for 10 seconds to avoid spamming

    async function initKiosk() {
        try {
            statusText.textContent = '⏳ Đang nạp AI Recognition Models...';
            await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);

            statusText.textContent = '🧠 Đang nạp danh sách dữ liệu khuôn mặt nhân viên...';
            buildFaceMatcher();

            startWebcam();
        } catch (err) {
            console.error(err);
            statusText.textContent = '❌ Lỗi khởi tạo AI Models!';
        }
    }

    function buildFaceMatcher() {
        const labeledDescriptors = [];

        rawStaffs.forEach(staff => {
            if (staff.face_descriptor) {
                try {
                    const descArr = new Float32Array(JSON.parse(staff.face_descriptor));
                    labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(staff.id.toString(), [descArr]));
                } catch (e) {
                    console.error('Invalid descriptor for staff ID:', staff.id);
                }
            }
        });

        if (labeledDescriptors.length > 0) {
            faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.45); // Euclidean threshold
            statusText.textContent = `✅ Đã nạp ${labeledDescriptors.length} mẫu khuôn mặt. Bật Camera...`;
        } else {
            statusText.textContent = '⚠️ Chưa có nhân viên nào đăng ký FaceID!';
        }
    }

    async function startWebcam() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
            webcam.srcObject = stream;

            webcam.onloadedmetadata = () => {
                statusDot.style.background = '#22c55e';
                statusText.textContent = '🟢 Kiosk AI sẵn sàng! Nhìn vào Camera để chấm công.';
                onPlay();
            };
        } catch (err) {
            statusDot.style.background = '#ef4444';
            statusText.textContent = '❌ Không mở được Camera! Vui lòng cho phép quyền truy cập.';
        }
    }

    async function onPlay() {
        if (webcam.paused || webcam.ended) return setTimeout(() => onPlay(), 100);

        const displaySize = { width: webcam.videoWidth, height: webcam.videoHeight };
        faceapi.matchDimensions(overlay, displaySize);

        setInterval(async () => {
            if (isProcessing || !faceMatcher) return;

            const detections = await faceapi.detectAllFaces(webcam).withFaceLandmarks().withFaceDescriptors();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);

            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            for (const detection of resizedDetections) {
                const bestMatch = faceMatcher.findBestMatch(detection.descriptor);
                const box = detection.detection.box;

                if (bestMatch.label !== 'unknown') {
                    const staffId = parseInt(bestMatch.label);
                    const staff = rawStaffs.find(s => s.id === staffId);

                    // Draw green box
                    const drawBox = new faceapi.draw.DrawBox(box, { 
                        label: `${staff ? staff.full_name : 'Staff'} (${Math.round((1 - bestMatch.distance) * 100)}%)`,
                        boxColor: '#22c55e' 
                    });
                    drawBox.draw(overlay);

                    // Throttle API call 10s per staff
                    const now = Date.now();
                    if (!lastScanTime[staffId] || (now - lastScanTime[staffId] > 10000)) {
                        lastScanTime[staffId] = now;
                        sendFaceCheckin(staffId);
                    }
                } else {
                    const drawBox = new faceapi.draw.DrawBox(box, { label: 'Chưa rõ danh tính', boxColor: '#ef4444' });
                    drawBox.draw(overlay);
                }
            }
        }, 500);
    }

    async function sendFaceCheckin(staffId) {
        isProcessing = true;
        try {
            const res = await fetch("{{ portal_route('attendance.face-checkin') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ staff_id: staffId })
            });

            const data = await res.json();

            if (data.success) {
                appendLogItem(data);
            } else {
                console.warn(data.message);
            }
        } catch (e) {
            console.error('Face Checkin Error:', e);
        } finally {
            isProcessing = false;
        }
    }

    function appendLogItem(data) {
        if (logList.querySelector('p')) {
            logList.innerHTML = '';
        }

        const isCheckin = data.type === 'check_in';
        const badgeClass = isCheckin ? 'badge-in' : 'badge-out';
        const label = isCheckin ? `CHECK-IN (${data.status_label})` : `CHECK-OUT (${data.working_hours}h)`;

        const div = document.createElement('div');
        div.className = 'scan-log-item';
        div.innerHTML = `
            <div>
                <strong style="color:#0f172a; font-size:14px;">${data.staff_name}</strong>
                <div style="font-size:12px; color:#64748b;">${data.time}</div>
            </div>
            <span class="${badgeClass}">${label}</span>
        `;

        logList.prepend(div);
    }

    document.addEventListener('DOMContentLoaded', initKiosk);
</script>
@endpush
@endsection
