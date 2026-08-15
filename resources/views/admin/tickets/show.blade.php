@extends('layouts.admin.master')

@section('page_title', 'Chi tiết ' . ($ticket->ticket_type === 'report' ? 'tố cáo' : 'phản ánh') . ' #' . $ticket->id)
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/show.css'])
@endpush

@section('content')
<div class="tk-detail-page">

    {{-- Header --}}
    <div class="tk-detail-header">
        <a href="{{ url()->previous() == url()->current() ? portal_route('tickets.index') : url()->previous() }}" class="tk-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Quay lại
        </a>
        <div class="tk-detail-header-content">
            <div class="tk-detail-title-group">
                <h1 class="tk-detail-title">REQ-{{ $ticket->id }}</h1>
                <span class="tk-detail-status tk-status--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
            </div>
            <div class="tk-detail-meta">
                Ngày gửi: {{ $ticket->created_at->format('d/m/Y - h:i A') }}
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">{{ $errors->first() }}</div>
    @endif

    <div class="tk-detail-grid">
        {{-- LEFT COLUMN --}}
        <div class="tk-detail-left">
            
            {{-- Issue Card --}}
            <div class="tk-card">
                <div class="tk-card-header tk-card-header--icon" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="tk-card-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25c-5.385 4.362-8.25 8.92-8.25 12.375 0 4.5 3.75 8.25 8.25 8.25s8.25-3.75 8.25-8.25c0-3.455-2.865-8.013-8.25-12.375z"/></svg>
                        </div>
                        <h2 class="tk-card-title" style="margin: 0;">{{ $ticket->title }}</h2>
                    </div>
                    
                    @if(auth()->user()->role === 'technician')
                    <div class="tk-action-list" style="margin: 0; display: flex; gap: 8px;">
                        @if($ticket->status === 'pending' || $ticket->status === 'assigned')
                            <form action="{{ portal_route('tickets.accept', $ticket->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="tk-btn-primary" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 6px;">Nhận việc</button>
                            </form>
                        @elseif($ticket->status === 'in_progress')
                            <button type="button" class="tk-btn-outline" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 6px;" onclick="openProgressModal()">Cập nhật tiến độ</button>
                        @elseif($ticket->status === 'completed')
                            <span style="padding: 6px 14px; font-size: 0.85rem; font-weight: 700; border-radius: 6px; background: #dcfce7; color: #166534;">Đã hoàn thành</span>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="tk-card-body">
                    <div class="tk-section-title">NỘI DUNG PHẢN ÁNH</div>
                    <div class="tk-description-box">
                        {{ $ticket->description }}
                    </div>

                    @if($ticket->images && count($ticket->images) > 0)
                        <div class="tk-section-title">ẢNH ĐÍNH KÈM</div>
                        <div class="tk-photo-grid">
                            @foreach($ticket->images as $img)
                                <img src="{{ asset('storage/' . $img) }}" alt="Photo" class="tk-photo-item" onclick="openAdminImgModal(this.src)">
                            @endforeach
                        </div>
                    @elseif($ticket->image)
                        <div class="tk-section-title">ẢNH ĐÍNH KÈM</div>
                        <div class="tk-photo-grid">
                            <img src="{{ asset('storage/' . $ticket->image) }}" alt="Photo" class="tk-photo-item" onclick="openAdminImgModal(this.src)">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resident Info Card --}}
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem;">Thông tin cư dân</h2>
                </div>
                <div class="tk-card-body">
                    <div class="tk-resident-info">
                        <div class="tk-resident-item">
                            <div class="tk-resident-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="tk-resident-text">
                                <label>Họ và tên</label>
                                <span>{{ $ticket->sender->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="tk-resident-item">
                            <div class="tk-resident-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="tk-resident-text">
                                <label>Căn hộ</label>
                                <span>Phòng {{ $ticket->apartment->apartment_number ?? 'N/A' }}, Tòa {{ $ticket->apartment->floor->block->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="tk-resident-item">
                            <div class="tk-resident-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div class="tk-resident-text">
                                <label>Liên hệ</label>
                                <span>{{ $ticket->sender->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            {{-- Reported Person (if report) --}}
            @if($ticket->ticket_type === 'report')
            <div class="tk-card" style="border: 1px solid #fecaca;">
                <div class="tk-card-header" style="background: #fef2f2;">
                    <h2 class="tk-card-title" style="font-size: 1.1rem; color: #dc2626;">Người bị tố cáo</h2>
                </div>
                <div class="tk-card-body">
                    @if($ticket->accused_user_id)
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 8px;">{{ $ticket->accusedUser->name ?? 'N/A' }}</div>
                        <div style="font-size: 0.9rem; color: #64748b;">Phản hồi: {{ $ticket->accusedResponseLabel() }}</div>
                        @if($ticket->accused_response_comment)
                            <div style="font-size: 0.9rem; font-style: italic; margin-top: 8px;">"{{ $ticket->accused_response_comment }}"</div>
                        @endif
                    @else
                        @if($ticket->reported_person)
                            <div style="font-size: 0.9rem; color: #dc2626; margin-bottom: 12px;">Cư dân ghi: {{ $ticket->reported_person }}</div>
                        @endif
                        <form method="POST" action="{{ portal_route('tickets.assign-accused', $ticket->id) }}">
                            @csrf
                            <select name="accused_user_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 12px;">
                                <option value="" disabled selected>-- Chọn cư dân --</option>
                                @foreach($residents as $res)
                                    <option value="{{ $res->id }}">{{ $res->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="tk-btn-primary" style="background: #dc2626;">Gửi thông báo tố cáo</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="tk-detail-right">


            {{-- Internal Notes (Timeline) --}}
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem;">Ghi chú nội bộ</h2>
                </div>
                <div class="tk-card-body" style="padding: 16px;">
                    @if($ticket->progress->isEmpty())
                        <div class="tk-notes-empty">Chưa có ghi chú nào.</div>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; max-height: 400px; overflow-y: auto;">
                            @foreach($ticket->progress as $prog)
                                <div style="background: #f8fafc; border-radius: 8px; padding: 12px; font-size: 0.9rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <strong style="color: #0f172a;">{{ $prog->updatedBy->name ?? 'Hệ thống' }}</strong>
                                        <span style="color: #94a3b8; font-size: 0.8rem;">{{ $prog->created_at->format('M d, h:i A') }}</span>
                                    </div>
                                    <div style="color: #2563eb; font-weight: 600; font-size: 0.8rem; margin-bottom: 4px;">[{{ $prog->statusLabel() }}]</div>
                                    @if($prog->comment)
                                        <div style="color: #334155;">{{ $prog->comment }}</div>
                                    @endif
                                    @if($prog->image_proof)
                                        <img src="{{ asset('storage/' . $prog->image_proof) }}" style="max-width:100%; border-radius:6px; margin-top:8px; cursor:pointer;" onclick="openAdminImgModal(this.src)">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Admin Assign Form --}}
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem;">Phân công kỹ thuật viên (Tối đa 5)</h2>
                </div>
                <div class="tk-card-body">
                    <form method="POST" action="{{ portal_route('tickets.assign', $ticket->id) }}">
                        @csrf
                        <div style="margin-bottom: 12px;">
                            <label style="font-size:0.85rem; color:#64748b; margin-bottom:6px; display:block; font-weight:600;">Chọn KTV phụ trách (tối đa 5 người):</label>
                            <div style="max-height: 180px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background: #f8fafc;">
                                @php
                                    $assignedIds = $ticket->technicians->pluck('id')->toArray();
                                    if (empty($assignedIds) && $ticket->handler_id) {
                                        $assignedIds = [$ticket->handler_id];
                                    }
                                @endphp
                                @foreach($technicians as $tech)
                                    <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 0.9rem; cursor: pointer; color: #1e293b;">
                                        <input type="checkbox" name="technician_ids[]" value="{{ $tech->id }}"
                                            class="tech-checkbox-limit"
                                            {{ in_array($tech->id, $assignedIds) ? 'checked' : '' }}>
                                        <span>{{ $tech->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <small style="color: #64748b; font-size: 0.78rem; display: block; margin-top: 4px;">* KTV đầu tiên chọn sẽ là KTV trưởng phụ trách chính.</small>
                        </div>
                        <button type="submit" class="tk-btn-primary" style="padding: 8px 16px;">Lưu phân công</button>
                    </form>
                </div>
            </div>
            @endif


        </div>
    </div>
</div>

{{-- Progress Modal --}}
<div id="progressModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:92%; max-width:480px; border-radius:16px; padding:24px; position:relative; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <span style="position:absolute; top:18px; right:20px; font-size:1.5rem; font-weight:bold; cursor:pointer; color:#64748b;" onclick="closeProgressModal()">&times;</span>
        <h2 style="margin-bottom: 20px; font-size:1.25rem; font-weight:700; color:#0f172a;" id="modalTitle">Cập nhật tiến độ</h2>
        
        <form id="progressForm" method="POST" action="{{ portal_route('tickets.update-progress', $ticket->id) }}" enctype="multipart/form-data">
            @csrf
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#334155; margin-bottom:6px;">Trạng thái mới <span style="color:#dc2626;">*</span></label>
                <select name="status" id="modalStatus" required onchange="updateShowModalNotes(this.value)" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-size:0.9rem; background:#fff;">
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>🔄 Đang xử lý (tiếp tục)</option>
                    <option value="completed">✅ Hoàn thành</option>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#334155; margin-bottom:6px;">Báo cáo / Ghi chú <span id="showCommentNote" style="font-weight:normal; color:#64748b; font-size:0.8rem;">(tùy chọn)</span></label>
                <textarea name="comment" id="showComment" rows="3" placeholder="Mô tả công việc đã thực hiện, kết quả, ghi chú..." style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; font-size:0.9rem; box-sizing:border-box;"></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#334155; margin-bottom:6px;">Chụp ảnh / Tải ảnh lên <span id="showProofNote" style="font-weight:normal; color:#64748b; font-size:0.8rem;">(tùy chọn)</span></label>
                <div style="border: 2px dashed #cbd5e1; border-radius:8px; padding: 16px; text-align: center; cursor: pointer; background: #f8fafc;" onclick="document.getElementById('showImgInput').click()">
                    <input type="file" name="image_proof" id="showImgInput" accept="image/*" capture="environment" style="display:none;" onchange="handleShowFileSelect(this)">
                    <div id="showUploadPlaceholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5" style="margin: 0 auto 6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span style="display:block; font-size:0.85rem; color:#64748b;">Nhấn để chọn ảnh hoặc chụp ảnh</span>
                    </div>
                    <div id="showUploadPreview" style="display:none; align-items:center; gap:10px; justify-content:center;">
                        <img id="showPreviewImg" src="" style="width:50px; height:50px; object-fit:cover; border-radius:6px;">
                        <span id="showPreviewName" style="font-size:0.85rem; color:#334155; font-weight:500;"></span>
                    </div>
                </div>
                <button type="button" class="tk-btn-outline" style="margin-top: 10px; padding: 8px 12px; font-size: 0.85rem; width: auto; display: inline-flex; align-items: center; gap: 6px;" onclick="openShowCameraCapture()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 7h-2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M9 7l1.5-3h3L15 7"/><circle cx="12" cy="15" r="3"/></svg>
                    Mở camera máy tính
                </button>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="tk-btn-outline" style="width: auto; padding: 10px 20px;" onclick="closeProgressModal()">Hủy</button>
                <button type="submit" class="tk-btn-primary" id="modalSubmitBtn" style="width: auto; padding: 10px 24px;">Cập nhật tiến độ</button>
            </div>
        </form>
    </div>
</div>

<div id="showCameraModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:10000;cursor:pointer;" onclick="closeShowCameraModal()"></div>
<div id="showCameraModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%, -50%);width:92vw;max-width:520px;background:#fff;border-radius:16px;box-shadow:0 24px 48px rgba(15,23,42,0.25);z-index:10001;padding:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div style="font-weight:700;font-size:0.95rem;color:#0f172a;">Camera máy tính</div>
        <button type="button" class="tk-btn-outline" style="padding:4px 10px;font-size:0.8rem;width:auto;" onclick="closeShowCameraModal()">Đóng</button>
    </div>
    <video id="showCameraVideo" autoplay playsinline style="width:100%;height:auto;border-radius:14px;background:#000;"></video>
    <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:12px;">
        <button type="button" class="tk-btn-outline" style="width:auto;padding:8px 16px;" onclick="closeShowCameraModal()">Hủy</button>
        <button type="button" class="tk-btn-primary" style="width:auto;padding:8px 20px;" onclick="captureShowCameraPhoto()">Chụp ảnh</button>
    </div>
</div>

{{-- Image Modal --}}
<div id="adminImgModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; cursor:pointer;" onclick="closeAdminImgModal()">
    <span style="position:absolute; top:20px; right:30px; color:#fff; font-size:2rem; font-weight:bold; cursor:pointer;">&times;</span>
    <img id="adminModalImg" src="" style="max-width:90%; max-height:90%; border-radius:12px; object-fit:contain;">
</div>

<script>
function openAdminImgModal(src) {
    const modal = document.getElementById('adminImgModal');
    document.getElementById('adminModalImg').src = src;
    modal.style.display = 'flex';
}
function closeAdminImgModal() {
    document.getElementById('adminImgModal').style.display = 'none';
}

function openProgressModal() {
    document.getElementById('progressModal').style.display = 'flex';
    const statusVal = document.getElementById('modalStatus').value;
    updateShowModalNotes(statusVal);
}

function closeProgressModal() {
    document.getElementById('progressModal').style.display = 'none';
}

function updateShowModalNotes(status) {
    const proofNote = document.getElementById('showProofNote');
    const commentNote = document.getElementById('showCommentNote');
    if (status === 'completed') {
        if (proofNote) proofNote.textContent = '(bắt buộc khi hoàn thành)';
        if (commentNote) commentNote.textContent = '(bắt buộc khi hoàn thành)';
    } else {
        if (proofNote) proofNote.textContent = '(tùy chọn)';
        if (commentNote) commentNote.textContent = '(tùy chọn)';
    }
}

function handleShowFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('showPreviewImg').src = e.target.result;
        document.getElementById('showPreviewName').textContent = file.name;
        document.getElementById('showUploadPlaceholder').style.display = 'none';
        document.getElementById('showUploadPreview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

let showCameraStream = null;
function openShowCameraCapture() {
    const overlay = document.getElementById('showCameraModalOverlay');
    const modal   = document.getElementById('showCameraModal');
    const video   = document.getElementById('showCameraVideo');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Trình duyệt của bạn không hỗ trợ mở camera.');
        return;
    }

    overlay.style.display = 'block';
    modal.style.display   = 'block';

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            showCameraStream = stream;
            video.srcObject = stream;
            video.play();
        })
        .catch(error => {
            alert('Không thể kết nối camera. Bạn có thể chọn file ảnh tải lên.');
            closeShowCameraModal();
        });
}

function closeShowCameraModal() {
    const overlay = document.getElementById('showCameraModalOverlay');
    const modal   = document.getElementById('showCameraModal');
    const video   = document.getElementById('showCameraVideo');

    if (overlay) overlay.style.display = 'none';
    if (modal)   modal.style.display   = 'none';

    if (showCameraStream) {
        showCameraStream.getTracks().forEach(track => track.stop());
        showCameraStream = null;
    }
    if (video) video.srcObject = null;
}

function captureShowCameraPhoto() {
    const video = document.getElementById('showCameraVideo');
    if (!video || !video.videoWidth) {
        alert('Camera chưa sẵn sàng.');
        return;
    }

    const canvas  = document.createElement('canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;

    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        if (!blob) return;
        const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        const input = document.getElementById('showImgInput');
        input.files = dataTransfer.files;
        handleShowFileSelect(input);
        closeShowCameraModal();
    });
}
</script>
@endsection
