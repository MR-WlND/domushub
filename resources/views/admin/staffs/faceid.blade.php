@extends('layouts.admin.master')

@section('title', 'Đăng Ký FaceID - ' . $staff->full_name)

@push('styles')
<style>
    .faceid-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        padding: 24px;
        max-width: 700px;
        margin: 0 auto;
        border: 1px solid #e2e8f0;
    }
    .video-container {
        position: relative;
        width: 100%;
        max-width: 500px;
        height: 380px;
        margin: 0 auto 20px;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    video, canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .status-alert {
        padding: 12px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 20px;
        text-align: center;
    }
    .btn-capture {
        background: #2563eb;
        color: #fff;
        font-weight: 700;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 15px;
        width: 100%;
        transition: all 0.2s;
    }
    .btn-capture:hover {
        background: #1d4ed8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid padding-horizontal-lg padding-vertical-md">

    <div class="faceid-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <div>
                <a href="{{ portal_route('staffs.index') }}" style="color:#64748b; font-size:13px; text-decoration:none; font-weight:600;">← Quay lại danh sách</a>
                <h1 style="margin:4px 0 0; font-size:22px; font-weight:800; color:#0f172a;">Đăng Ký FaceID Nhân Sự</h1>
            </div>
            <span style="background:#e0f2fe; color:#0369a1; padding:6px 12px; border-radius:20px; font-weight:700; font-size:13px;">
                {{ $staff->full_name }}
            </span>
        </div>

        <div id="statusBox" class="status-alert" style="background:#f1f5f9; color:#475569;">
            ⌛ Đang nạp mô hình AI (face-api.js)... Vui lòng đợi trong giây lát.
        </div>

        <div class="video-container">
            <video id="webcam" autoplay playsinline muted></video>
            <canvas id="overlay"></canvas>
        </div>

        <div style="text-align:center;">
            <button id="btnCapture" class="btn-capture" disabled onclick="captureFace()">
                📸 Quét & Lưu Khuôn Mặt
            </button>
            <p style="margin-top:10px; font-size:12px; color:#94a3b8;">
                Hướng thẳng khuôn mặt vào camera trong điều kiện đủ ánh sáng và nhấn nút Quét.
            </p>
        </div>
    </div>
</div>

@push('scripts')
{{-- Load face-api.js --}}
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
<script>
    const webcam = document.getElementById('webcam');
    const overlay = document.getElementById('overlay');
    const statusBox = document.getElementById('statusBox');
    const btnCapture = document.getElementById('btnCapture');

    const MODEL_URL = '/models';
    let isModelLoaded = false;

    async function initAI() {
        try {
            await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
            await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
            await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
            
            isModelLoaded = true;
            statusBox.style.background = '#dcfce7';
            statusBox.style.color = '#15803d';
            statusBox.textContent = '✅ Đã tải mô hình AI thành công! Hãy bật camera để bắt đầu.';
            
            startWebcam();
        } catch (err) {
            console.error(err);
            statusBox.style.background = '#fef2f2';
            statusBox.style.color = '#dc2626';
            statusBox.textContent = '❌ Không thể tải AI Models. Vui lòng kiểm tra thư mục /models trên máy chủ.';
        }
    }

    async function startWebcam() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
            webcam.srcObject = stream;
            
            webcam.onloadedmetadata = () => {
                btnCapture.disabled = false;
                statusBox.textContent = '🟢 Camera đã sẵn sàng! Vui lòng nhìn thẳng vào ống kính và nhấn "Quét & Lưu Khuôn Mặt".';
            };
        } catch (err) {
            console.error(err);
            statusBox.style.background = '#fef2f2';
            statusBox.style.color = '#dc2626';
            statusBox.textContent = '❌ Không thể truy cập Camera. Vui lòng cấp quyền camera cho trình duyệt!';
        }
    }

    async function captureFace() {
        if (!isModelLoaded) return;
        btnCapture.disabled = true;
        btnCapture.textContent = '⏳ Đang phân tích khuôn mặt...';
        
        statusBox.style.background = '#fef3c7';
        statusBox.style.color = '#b45309';
        statusBox.textContent = '🔍 Đang trích xuất 128 đặc điểm nhận dạng khuôn mặt...';

        const detection = await faceapi.detectSingleFace(webcam).withFaceLandmarks().withFaceDescriptor();

        if (!detection) {
            statusBox.style.background = '#fef2f2';
            statusBox.style.color = '#dc2626';
            statusBox.textContent = '⚠️ Không phát hiện khuôn mặt! Hãy điều chỉnh góc nhìn và đủ ánh sáng.';
            btnCapture.disabled = false;
            btnCapture.textContent = '📸 Quét & Lưu Khuôn Mặt';
            return;
        }

        const descriptorArray = Array.from(detection.descriptor);
        const descriptorJson = JSON.stringify(descriptorArray);

        // Gửi API lưu dữ liệu
        try {
            const res = await fetch("{{ portal_route('staffs.save-faceid', $staff) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ face_descriptor: descriptorJson })
            });

            const data = await res.json();

            if (data.success) {
                statusBox.style.background = '#dcfce7';
                statusBox.style.color = '#15803d';
                statusBox.textContent = '🎉 ' + data.message;
                
                setTimeout(() => {
                    window.location.href = "{{ portal_route('staffs.index') }}";
                }, 2000);
            } else {
                throw new Error(data.message || 'Lưu thất bại');
            }
        } catch (err) {
            statusBox.style.background = '#fef2f2';
            statusBox.style.color = '#dc2626';
            statusBox.textContent = '❌ Lỗi: ' + err.message;
            btnCapture.disabled = false;
            btnCapture.textContent = '📸 Quét & Lưu Khuôn Mặt';
        }
    }

    document.addEventListener('DOMContentLoaded', initAI);
</script>
@endpush
@endsection
