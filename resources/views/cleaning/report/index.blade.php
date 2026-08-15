@extends('layouts.cleaning.master')

@section('page_title', 'Báo cáo sự cố – DomusHub')

@push('styles')
<style>
    .content{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;}
    .report-form-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .report-form-card__title{font-size:18px;font-weight:700;color:#1B2559;margin-bottom:6px;}
    .report-form-card__sub{font-size:13px;color:#A3AED0;margin-bottom:24px;}
    .form-group{margin-bottom:20px;}
    .form-label{display:block;font-size:12.5px;font-weight:600;color:#1B2559;margin-bottom:7px;}
    .form-label .req{color:#EE5D50;}
    .form-input{width:100%;height:46px;border:1.5px solid #E9EDF7;border-radius:10px;padding:0 14px;font-size:13.5px;color:#1B2559;font-family:inherit;background:#FAFCFE;transition:.2s;}
    .form-input:focus{outline:none;border-color:#3652D9;background:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
    .form-textarea{width:100%;min-height:110px;border:1.5px solid #E9EDF7;border-radius:10px;padding:12px 14px;font-size:13.5px;color:#1B2559;font-family:inherit;background:#FAFCFE;resize:vertical;transition:.2s;}
    .form-textarea:focus{outline:none;border-color:#3652D9;background:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
    .form-select{width:100%;height:46px;border:1.5px solid #E9EDF7;border-radius:10px;padding:0 14px;font-size:13.5px;color:#1B2559;font-family:inherit;background:#FAFCFE;appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23A3AED0' viewBox='0 0 16 16'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;transition:.2s;}
    .form-select:focus{outline:none;border-color:#3652D9;background-color:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
    .form-row{display:flex;gap:16px;}
    .form-row .form-group{flex:1;}
    .priority-group{display:flex;gap:8px;height:46px;align-items:center;}
    .priority-option{display:flex;align-items:center;gap:6px;padding:10px 14px;border-radius:10px;border:1.5px solid #E9EDF7;cursor:pointer;transition:.2s;font-size:12.5px;font-weight:600;color:#707EAE;background:white;}
    .priority-option:hover{border-color:#3652D9;}
    .priority-option:has(input:checked){border-color:#3652D9;background:#F4F7FE;color:#1B2559;}
    .priority-option input{display:none;}
    .priority-dot{width:8px;height:8px;border-radius:50%;}
    .priority-dot--low{background:#05CD99;}
    .priority-dot--medium{background:#FFB547;}
    .priority-dot--high{background:#EE5D50;}
    .location-badge{display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;background:linear-gradient(135deg,#EEF2FF,#E0E7FF);border:1.5px solid #C7D2FE;font-size:13.5px;font-weight:600;color:#3730A3;height:46px;}
    .location-badge i{font-size:15px;color:#6366F1;}
    .location-badge--muted{background:#F8FAFC;border-color:#E2E8F0;color:#94A3B8;}
    .location-badge--muted i{color:#CBD5E1;}
    .upload-area{border:2px dashed #D8E0F0;border-radius:12px;padding:28px;text-align:center;cursor:pointer;transition:.2s;background:#FAFCFE;}
    .upload-area:hover{border-color:#3652D9;background:#F4F7FE;}
    .upload-area i{font-size:24px;color:#A3AED0;margin-bottom:8px;}
    .upload-area p{font-size:13px;color:#707EAE;font-weight:500;}
    .upload-area span{font-size:11px;color:#A3AED0;display:block;margin-top:3px;}
    .upload-preview{display:flex;gap:10px;margin-top:14px;flex-wrap:wrap;}
    .upload-thumb{width:80px;height:80px;border-radius:10px;overflow:hidden;border:2px solid #E9EDF7;position:relative;cursor:pointer;transition:.2s;}
    .upload-thumb:hover{border-color:#3652D9;transform:scale(1.03);}
    .upload-thumb img,.upload-thumb video{width:100%;height:100%;object-fit:cover;display:block;}
    .upload-thumb__overlay{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35);border-radius:8px;opacity:1;}
    .upload-thumb__overlay i{color:white;font-size:18px;filter:drop-shadow(0 1px 2px rgba(0,0,0,.4));}
    .upload-thumb__remove{position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:50%;background:#EE5D50;color:white;border:2px solid white;font-size:10px;display:flex;align-items:center;justify-content:center;cursor:pointer;opacity:0;transition:.15s;z-index:2;}
    .upload-thumb:hover .upload-thumb__remove{opacity:1;}
    .upload-thumb__name{position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.6);color:white;font-size:9px;padding:2px 4px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

    /* Lightbox */
    .lightbox{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.85);align-items:center;justify-content:center;padding:20px;}
    .lightbox--active{display:flex;}
    .lightbox__content{max-width:90vw;max-height:85vh;border-radius:12px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.5);}
    .lightbox__content img,.lightbox__content video{max-width:90vw;max-height:85vh;display:block;border-radius:12px;}
    .lightbox__close{position:absolute;top:20px;right:24px;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.15);backdrop-filter:blur(4px);border:none;color:white;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;}
    .lightbox__close:hover{background:rgba(255,255,255,.3);}

    /* History media thumbnails */
    .history-item__media{display:flex;gap:6px;margin-top:6px;}
    .history-media-thumb{width:36px;height:36px;border-radius:6px;overflow:hidden;border:1.5px solid #E9EDF7;cursor:pointer;position:relative;transition:.15s;}
    .history-media-thumb:hover{border-color:#3652D9;transform:scale(1.05);}
    .history-media-thumb img,.history-media-thumb video{width:100%;height:100%;object-fit:cover;display:block;}
    .history-media-thumb__play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35);border-radius:4px;}
    .history-media-thumb__play i{color:white;font-size:10px;}
    .btn-submit{display:inline-flex;align-items:center;gap:10px;background:#3652D9;color:white;border:none;padding:13px 28px;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;margin-top:4px;}
    .btn-submit:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}

    /* RIGHT COL */
    .right-col{display:flex;flex-direction:column;gap:16px;}
    .info-card{background:white;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .info-card--emergency{background:linear-gradient(135deg,#FFF8F0,#FFF2E5);border:1px solid #FFE0B5;}
    .info-card--emergency .ic-icon{width:36px;height:36px;border-radius:50%;background:#FF9B05;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;margin-bottom:10px;}
    .info-card--emergency h3{font-size:13px;font-weight:700;color:#1B2559;margin-bottom:4px;}
    .info-card--emergency p{font-size:12px;color:#92400E;line-height:1.6;margin-bottom:8px;}
    .hotline{font-size:13px;font-weight:700;color:#3652D9;display:flex;align-items:center;gap:6px;}
    .info-card__header{font-size:10.5px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;}
    .guide-list{list-style:none;padding:0;margin:0;}
    .guide-list li{display:flex;align-items:flex-start;gap:8px;margin-bottom:12px;font-size:12px;color:#4A5568;line-height:1.6;}
    .guide-list li:last-child{margin-bottom:0;}
    .guide-dot{flex-shrink:0;width:20px;height:20px;border-radius:50%;background:#F4F7FE;display:flex;align-items:center;justify-content:center;font-size:9px;color:#3652D9;margin-top:1px;}
    .guide-list strong{color:#1B2559;}

    /* HISTORY */
    .history-card{background:white;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(54,82,217,.04);margin-top:20px;}
    .history-card__title{font-size:15px;font-weight:700;color:#1B2559;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
    .history-card__title i{color:#3652D9;font-size:13px;}
    .history-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #F4F7FE;font-size:12.5px;}
    .history-item:last-child{border-bottom:none;}
    .history-item__dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
    .history-item__dot--pending{background:#FFB547;}
    .history-item__dot--processing{background:#3652D9;}
    .history-item__dot--resolved{background:#05CD99;}
    .history-item__title{flex:1;color:#1B2559;font-weight:600;}
    .history-item__status{font-size:11px;font-weight:600;padding:3px 8px;border-radius:6px;}
    .history-item__status--pending{background:#FFF4E5;color:#FF9B05;}
    .history-item__status--processing{background:#EEF2FF;color:#3652D9;}
    .history-item__status--resolved{background:#E6F9F0;color:#05CD99;}
    .history-item__date{font-size:11px;color:#A3AED0;}

    .alert{padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
    .alert--success{background:#E6F9F0;color:#065F46;border:1px solid #A7F3D0;}
    .alert--error{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;}

    @media(max-width:1024px){.content{grid-template-columns:1fr;}}
    @media(max-width:640px){.form-row{flex-direction:column;gap:0;}}
</style>
@endpush

@section('topbar_left')
<div class="topbar-search">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" placeholder="Tìm kiếm tài sản, báo cáo..." aria-label="Tìm kiếm">
</div>
@endsection

@section('content')

@if(session('success'))
<div class="alert alert--success" style="grid-column:1/-1;"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert--error" style="grid-column:1/-1;"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
@endif

<!-- FORM -->
<div>
    <div class="report-form-card">
        <h1 class="report-form-card__title">Chi tiết sự cố</h1>
        <p class="report-form-card__sub">Vui lòng cung cấp thông tin chính xác để bộ phận kỹ thuật xử lý kịp thời.</p>

        <form method="POST" action="{{ route('cleaning.report.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Tiêu đề sự cố <span class="req">*</span></label>
                <input type="text" class="form-input" name="title" value="{{ old('title') }}" placeholder="Ví dụ: Rò rỉ nước tại hành lang tầng 5" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Khu vực phân công</label>
                    @if($todaySchedule && $todaySchedule->block)
                    <div class="location-badge">
                        <i class="fa-solid fa-building"></i>
                        <span>{{ $todaySchedule->block->name }} – {{ $todaySchedule->floor?->name ?? 'Tầng ' . $todaySchedule->floor?->floor_number }}</span>
                    </div>
                    <input type="hidden" name="block_id" value="{{ $todaySchedule->block_id }}">
                    <input type="hidden" name="floor_id" value="{{ $todaySchedule->floor_id }}">
                    @else
                    <div class="location-badge location-badge--muted">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Chưa có lịch phân ca hôm nay</span>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="form-label">Vị trí chi tiết</label>
                    <input type="text" class="form-input" name="location" value="{{ old('location') }}" placeholder="VD: Hành lang, Cầu thang, Phòng rác...">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mức độ ưu tiên</label>
                    <div class="priority-group">
                        <label class="priority-option"><input type="radio" name="priority" value="low" {{ old('priority', 'low') == 'low' ? 'checked' : '' }}><span class="priority-dot priority-dot--low"></span> Thấp</label>
                        <label class="priority-option"><input type="radio" name="priority" value="medium" {{ old('priority') == 'medium' ? 'checked' : '' }}><span class="priority-dot priority-dot--medium"></span> TB</label>
                        <label class="priority-option"><input type="radio" name="priority" value="high" {{ old('priority') == 'high' ? 'checked' : '' }}><span class="priority-dot priority-dot--high"></span> Cao</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả chi tiết</label>
                <textarea class="form-textarea" name="description" placeholder="Mô tả cụ thể tình trạng, thời gian phát hiện...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh / Video minh chứng</label>
                <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <p>Kéo thả hoặc nhấp để tải ảnh / video lên</p>
                    <span>Ảnh: JPG, PNG, WebP (10MB) — Video: MP4, MOV, AVI, WebM (50MB)</span>
                </div>
                <input type="file" id="fileInput" name="images[]" multiple accept="image/*,video/*" style="display:none;" onchange="previewFiles(this)">
                <div class="upload-preview" id="uploadPreview"></div>
            </div>

            <button type="submit" class="btn-submit">Gửi báo cáo <i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>

    <!-- HISTORY -->
    @if($reports->count() > 0)
    <div class="history-card">
        <h2 class="history-card__title"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử báo cáo ({{ $reports->count() }})</h2>
        @foreach($reports as $report)
        <div class="history-item" style="flex-wrap:wrap;">
            <div class="history-item__dot history-item__dot--{{ $report->status }}"></div>
            <span class="history-item__title">{{ $report->title }}</span>
            <span class="history-item__status history-item__status--{{ $report->status }}">
                {{ $report->status === 'pending' ? 'Chờ xử lý' : ($report->status === 'processing' ? 'Đang xử lý' : 'Đã xử lý') }}
            </span>
            <span class="history-item__date">{{ $report->created_at->format('d/m') }}</span>
            @if($report->images && count($report->images) > 0)
            <div class="history-item__media" style="width:100%;padding-left:20px;">
                @foreach($report->images as $media)
                @php
                    $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                    $url = asset('storage/' . $media);
                @endphp
                @if($isVideo)
                <div class="history-media-thumb" onclick="openLightbox('{{ $url }}', 'video')">
                    <video src="{{ $url }}" muted preload="metadata"></video>
                    <div class="history-media-thumb__play"><i class="fa-solid fa-play"></i></div>
                </div>
                @else
                <div class="history-media-thumb" onclick="openLightbox('{{ $url }}', 'image')">
                    <img src="{{ $url }}" alt="Ảnh sự cố">
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- RIGHT COL -->
<div class="right-col">
    <div class="info-card info-card--emergency">
        <div class="ic-icon"><i class="fa-solid fa-phone"></i></div>
        <h3>Cần trợ giúp gấp?</h3>
        <p>Nếu đây là tình huống đe dọa đến an toàn hoặc tài sản lớn, vui lòng liên hệ trực tiếp.</p>
        <div class="hotline"><i class="fa-solid fa-phone-volume"></i> Số máy lẻ: 911</div>
    </div>

    <div class="info-card">
        <div class="info-card__header">Hướng dẫn báo cáo</div>
        <ul class="guide-list">
            <li><span class="guide-dot"><i class="fa-solid fa-bullseye"></i></span><div><strong>Rõ ràng:</strong> Tiêu đề nên tóm tắt ngắn gọn vấn đề.</div></li>
            <li><span class="guide-dot"><i class="fa-solid fa-camera"></i></span><div><strong>Minh chứng:</strong> Đính kèm ảnh thực tế giúp xử lý nhanh hơn.</div></li>
            <li><span class="guide-dot"><i class="fa-solid fa-flag"></i></span><div><strong>Ưu tiên:</strong> Chỉ đánh "Cao" cho sự cố khẩn cấp (vỡ ống nước, mất điện...)</div></li>
        </ul>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ═══════════════════════════════════════
// FILE PREVIEW (trước khi gửi)
// ═══════════════════════════════════════
let selectedFiles = [];

function previewFiles(input) {
    const preview = document.getElementById('uploadPreview');
    preview.innerHTML = '';
    selectedFiles = Array.from(input.files);

    selectedFiles.forEach((file, idx) => {
        const thumb = document.createElement('div');
        thumb.className = 'upload-thumb';

        if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.muted = true;
            video.preload = 'metadata';
            video.addEventListener('loadeddata', () => { video.currentTime = 1; });
            thumb.appendChild(video);

            const overlay = document.createElement('div');
            overlay.className = 'upload-thumb__overlay';
            overlay.innerHTML = '<i class="fa-solid fa-video"></i>';
            thumb.appendChild(overlay);
        } else {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            thumb.appendChild(img);
        }

        // File name label
        const nameLabel = document.createElement('div');
        nameLabel.className = 'upload-thumb__name';
        nameLabel.textContent = file.name.length > 12 ? file.name.substring(0, 9) + '...' : file.name;
        thumb.appendChild(nameLabel);

        // Remove button
        const removeBtn = document.createElement('div');
        removeBtn.className = 'upload-thumb__remove';
        removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        removeBtn.onclick = function(e) {
            e.stopPropagation();
            selectedFiles.splice(idx, 1);
            rebuildFileInput();
            previewFromArray();
        };
        thumb.appendChild(removeBtn);

        // Click to preview full
        thumb.addEventListener('click', function() {
            const url = URL.createObjectURL(file);
            openLightbox(url, file.type.startsWith('video/') ? 'video' : 'image');
        });

        preview.appendChild(thumb);
    });
}

function previewFromArray() {
    const preview = document.getElementById('uploadPreview');
    preview.innerHTML = '';
    selectedFiles.forEach((file, idx) => {
        const thumb = document.createElement('div');
        thumb.className = 'upload-thumb';

        if (file.type.startsWith('video/')) {
            const video = document.createElement('video');
            video.src = URL.createObjectURL(file);
            video.muted = true;
            video.preload = 'metadata';
            video.addEventListener('loadeddata', () => { video.currentTime = 1; });
            thumb.appendChild(video);
            const overlay = document.createElement('div');
            overlay.className = 'upload-thumb__overlay';
            overlay.innerHTML = '<i class="fa-solid fa-video"></i>';
            thumb.appendChild(overlay);
        } else {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            thumb.appendChild(img);
        }

        const nameLabel = document.createElement('div');
        nameLabel.className = 'upload-thumb__name';
        nameLabel.textContent = file.name.length > 12 ? file.name.substring(0, 9) + '...' : file.name;
        thumb.appendChild(nameLabel);

        const removeBtn = document.createElement('div');
        removeBtn.className = 'upload-thumb__remove';
        removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        removeBtn.onclick = function(e) {
            e.stopPropagation();
            selectedFiles.splice(idx, 1);
            rebuildFileInput();
            previewFromArray();
        };
        thumb.appendChild(removeBtn);

        thumb.addEventListener('click', function() {
            openLightbox(URL.createObjectURL(file), file.type.startsWith('video/') ? 'video' : 'image');
        });

        preview.appendChild(thumb);
    });
}

function rebuildFileInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    document.getElementById('fileInput').files = dt.files;
}

// ═══════════════════════════════════════
// LIGHTBOX (xem ảnh/video full)
// ═══════════════════════════════════════
function openLightbox(url, type) {
    let lightbox = document.getElementById('mediaLightbox');
    if (!lightbox) {
        lightbox = document.createElement('div');
        lightbox.id = 'mediaLightbox';
        lightbox.className = 'lightbox';
        lightbox.innerHTML = '<button class="lightbox__close" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button><div class="lightbox__content" id="lightboxContent"></div>';
        lightbox.addEventListener('click', function(e) { if (e.target === this) closeLightbox(); });
        document.body.appendChild(lightbox);
    }

    const content = document.getElementById('lightboxContent');
    if (type === 'video') {
        content.innerHTML = `<video src="${url}" controls autoplay style="max-width:90vw;max-height:85vh;border-radius:12px;"></video>`;
    } else {
        content.innerHTML = `<img src="${url}" alt="Preview" style="max-width:90vw;max-height:85vh;border-radius:12px;">`;
    }
    lightbox.classList.add('lightbox--active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    const lightbox = document.getElementById('mediaLightbox');
    if (lightbox) {
        lightbox.classList.remove('lightbox--active');
        const content = document.getElementById('lightboxContent');
        const video = content.querySelector('video');
        if (video) video.pause();
        content.innerHTML = '';
    }
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush
