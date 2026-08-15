@extends('layouts.admin.master')

@section('page_title', 'Chi tiết sự cố – DomusHub')

@push('styles')
<style>
.ird{max-width:900px;}
.ird__back{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#64748b;text-decoration:none;margin-bottom:20px;transition:.2s;}
.ird__back:hover{color:#4F46E5;}

/* Header */
.ird__header{background:white;border-radius:14px;padding:28px 32px;border:1px solid #f1f5f9;margin-bottom:20px;}
.ird__header-top{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:16px;}
.ird__title{font-size:22px;font-weight:800;color:#0f172a;line-height:1.3;margin:0;}
.ird__badges{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;}
.ird__badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:700;}
.ird__badge--high{background:#FEE2E2;color:#DC2626;}
.ird__badge--medium{background:#FEF3C7;color:#D97706;}
.ird__badge--low{background:#D1FAE5;color:#059669;}
.ird__badge--pending{background:#FEF3C7;color:#D97706;}
.ird__badge--processing{background:#DBEAFE;color:#2563EB;}
.ird__badge--resolved{background:#D1FAE5;color:#059669;}
.ird__time{font-size:12.5px;color:#94a3b8;display:flex;align-items:center;gap:6px;white-space:nowrap;}

/* Info grid */
.ird__info{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
.ird__info-card{background:#f8fafc;border-radius:10px;padding:14px 16px;border:1px solid #f1f5f9;}
.ird__info-label{font-size:10.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;}
.ird__info-value{font-size:14px;font-weight:600;color:#1e293b;display:flex;align-items:center;gap:6px;}
.ird__info-value i{font-size:12px;color:#94a3b8;}

/* Content sections */
.ird__content{display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;}
.ird__main{display:flex;flex-direction:column;gap:20px;}
.ird__section{background:white;border-radius:14px;padding:24px 28px;border:1px solid #f1f5f9;}
.ird__section-title{font-size:11.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.ird__section-title i{font-size:13px;}
.ird__desc{font-size:14px;color:#334155;line-height:1.7;white-space:pre-wrap;}
.ird__desc--empty{color:#94a3b8;font-style:italic;}

/* Gallery */
.ird__gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;}
.ird__gallery-item{aspect-ratio:1;border-radius:12px;overflow:hidden;border:2px solid #e2e8f0;cursor:pointer;position:relative;transition:.2s;}
.ird__gallery-item:hover{border-color:#4F46E5;transform:scale(1.02);box-shadow:0 4px 16px rgba(79,70,229,.1);}
.ird__gallery-item img,.ird__gallery-item video{width:100%;height:100%;object-fit:cover;display:block;}
.ird__gallery-play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.4);}
.ird__gallery-play i{color:white;font-size:28px;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3));}
.ird__gallery-tag{position:absolute;top:8px;left:8px;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:700;background:rgba(0,0,0,.6);color:white;}

/* Sidebar */
.ird__sidebar{display:flex;flex-direction:column;gap:20px;}
.ird__sidebar-card{background:white;border-radius:14px;padding:22px 24px;border:1px solid #f1f5f9;}
.ird__sidebar-title{font-size:11.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;}

/* Status change */
.ird__status-btns{display:flex;flex-direction:column;gap:8px;}
.ird__status-btn{width:100%;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:700;border:2px solid #e2e8f0;background:white;color:#64748b;cursor:pointer;transition:.2s;text-align:left;display:flex;align-items:center;gap:10px;}
.ird__status-btn:hover{border-color:#cbd5e1;background:#f8fafc;}
.ird__status-btn--active{color:white;border-color:transparent;}
.ird__status-btn--pending.ird__status-btn--active{background:#F59E0B;border-color:#F59E0B;}
.ird__status-btn--processing.ird__status-btn--active{background:#3B82F6;border-color:#3B82F6;}
.ird__status-btn--resolved.ird__status-btn--active{background:#10B981;border-color:#10B981;}
.ird__status-msg{font-size:12px;margin-top:8px;font-weight:600;min-height:18px;}

/* Assign form */
.ird__assign-form{display:flex;flex-direction:column;gap:12px;}
.ird__assign-select{width:100%;height:42px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 12px;font-size:13px;font-family:inherit;color:#1e293b;background:white;cursor:pointer;}
.ird__assign-select:focus{outline:none;border-color:#4F46E5;box-shadow:0 0 0 3px rgba(79,70,229,.08);}
.ird__assign-textarea{width:100%;min-height:80px;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;font-family:inherit;color:#1e293b;resize:vertical;}
.ird__assign-textarea:focus{outline:none;border-color:#4F46E5;box-shadow:0 0 0 3px rgba(79,70,229,.08);}
.ird__assign-btn{width:100%;padding:11px 16px;border-radius:8px;font-size:13px;font-weight:700;border:none;background:#4F46E5;color:white;cursor:pointer;transition:.2s;}
.ird__assign-btn:hover{background:#4338CA;}
.ird__assigned{display:flex;align-items:center;gap:10px;padding:12px 14px;background:#EEF2FF;border-radius:10px;border:1px solid #C7D2FE;}
.ird__assigned i{font-size:16px;color:#4F46E5;}
.ird__assigned-info{flex:1;}
.ird__assigned-name{font-size:13px;font-weight:700;color:#1e293b;}
.ird__assigned-phone{font-size:11.5px;color:#64748b;}

/* Alert */
.ird__alert{padding:14px 18px;border-radius:10px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:10px;background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;font-weight:500;}

/* Lightbox */
.ird-lightbox{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.9);display:none;align-items:center;justify-content:center;padding:24px;}
.ird-lightbox--active{display:flex;}
.ird-lightbox__close{position:absolute;top:20px;right:24px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.1);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;}
.ird-lightbox__close:hover{background:rgba(255,255,255,.2);}
.ird-lightbox__content img,.ird-lightbox__content video{max-width:90vw;max-height:85vh;border-radius:10px;display:block;}

@media(max-width:900px){
    .ird__content{grid-template-columns:1fr;}
    .ird__info{grid-template-columns:1fr 1fr;}
}
@media(max-width:640px){
    .ird__header{padding:20px;}
    .ird__section{padding:18px 20px;}
    .ird__info{grid-template-columns:1fr;}
    .ird__gallery{grid-template-columns:repeat(auto-fill,minmax(100px,1fr));}
}
</style>
@endpush

@section('content')
@php
    $roleLabels = [
        'cleaning' => 'Vệ sinh', 'security' => 'Bảo vệ', 'technician' => 'Kỹ thuật',
        'receptionist' => 'Lễ tân', 'staff' => 'Nhân viên', 'manager' => 'Quản lý', 'admin' => 'Admin',
    ];
    $roleLabel = $roleLabels[$report->reporter->role ?? ''] ?? ($report->reporter->role ?? '—');
    $priorityLabels = ['high' => 'Cao', 'medium' => 'Trung bình', 'low' => 'Thấp'];
    $statusLabels = ['pending' => 'Chờ xử lý', 'processing' => 'Đang xử lý', 'resolved' => 'Đã xử lý'];
@endphp

<div class="ird">
    <a href="{{ portal_route('cleaning-reports.index') }}" class="ird__back">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </a>

    @if(session('success'))
    <div class="ird__alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    {{-- HEADER --}}
    <div class="ird__header">
        <div class="ird__header-top">
            <div>
                <h1 class="ird__title">{{ $report->title }}</h1>
                <div class="ird__badges">
                    <span class="ird__badge ird__badge--{{ $report->status }}">
                        <i class="fa-solid fa-{{ $report->status === 'pending' ? 'clock' : ($report->status === 'processing' ? 'gear' : 'circle-check') }}"></i>
                        {{ $statusLabels[$report->status] }}
                    </span>
                    <span class="ird__badge ird__badge--{{ $report->priority }}">
                        {{ $priorityLabels[$report->priority] }}
                    </span>
                </div>
            </div>
            <span class="ird__time">
                <i class="fa-regular fa-calendar"></i> {{ $report->created_at->format('H:i – d/m/Y') }}
            </span>
        </div>

        <div class="ird__info">
            <div class="ird__info-card">
                <div class="ird__info-label">Người báo cáo</div>
                <div class="ird__info-value"><i class="fa-solid fa-user"></i> {{ $report->reporter->name ?? '—' }}</div>
            </div>
            <div class="ird__info-card">
                <div class="ird__info-label">Bộ phận</div>
                <div class="ird__info-value"><i class="fa-solid fa-building"></i> {{ $roleLabel }}</div>
            </div>
            <div class="ird__info-card">
                <div class="ird__info-label">Vị trí sự cố</div>
                <div class="ird__info-value"><i class="fa-solid fa-location-dot"></i> {{ $report->location ?: '—' }}</div>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="ird__content">
        <div class="ird__main">
            {{-- Description --}}
            <div class="ird__section">
                <div class="ird__section-title"><i class="fa-solid fa-align-left"></i> Mô tả chi tiết</div>
                @if($report->description)
                    <p class="ird__desc">{{ $report->description }}</p>
                @else
                    <p class="ird__desc ird__desc--empty">Không có mô tả chi tiết.</p>
                @endif
            </div>

            {{-- Gallery --}}
            @if($report->images && count($report->images) > 0)
            <div class="ird__section">
                <div class="ird__section-title"><i class="fa-solid fa-images"></i> Ảnh / Video đính kèm ({{ count($report->images) }})</div>
                <div class="ird__gallery">
                    @foreach($report->images as $media)
                    @php
                        $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                        $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                        $url = asset('storage/' . $media);
                    @endphp
                    <div class="ird__gallery-item" onclick="openLightbox('{{ $url }}', '{{ $isVideo ? 'video' : 'image' }}')">
                        @if($isVideo)
                            <video src="{{ $url }}" muted preload="metadata"></video>
                            <div class="ird__gallery-play"><i class="fa-solid fa-play"></i></div>
                            <span class="ird__gallery-tag"><i class="fa-solid fa-video"></i> Video</span>
                        @else
                            <img src="{{ $url }}" alt="Ảnh sự cố">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Admin note --}}
            @if($report->admin_note)
            <div class="ird__section">
                <div class="ird__section-title"><i class="fa-solid fa-note-sticky"></i> Ghi chú quản lý</div>
                <p class="ird__desc">{{ $report->admin_note }}</p>
            </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <div class="ird__sidebar">
            {{-- Status --}}
            <div class="ird__sidebar-card">
                <div class="ird__sidebar-title">Cập nhật trạng thái</div>
                <div class="ird__status-btns">
                    <button type="button" class="ird__status-btn ird__status-btn--pending {{ $report->status == 'pending' ? 'ird__status-btn--active' : '' }}" onclick="changeStatus('pending', this)">
                        <i class="fa-solid fa-clock"></i> Chờ xử lý
                    </button>
                    <button type="button" class="ird__status-btn ird__status-btn--processing {{ $report->status == 'processing' ? 'ird__status-btn--active' : '' }}" onclick="changeStatus('processing', this)">
                        <i class="fa-solid fa-gear"></i> Đang xử lý
                    </button>
                    <button type="button" class="ird__status-btn ird__status-btn--resolved {{ $report->status == 'resolved' ? 'ird__status-btn--active' : '' }}" onclick="changeStatus('resolved', this)">
                        <i class="fa-solid fa-circle-check"></i> Đã xử lý
                    </button>
                </div>
                <div class="ird__status-msg" id="statusMsg"></div>
            </div>

            {{-- Assign --}}
            <div class="ird__sidebar-card">
                <div class="ird__sidebar-title">Phân công xử lý</div>
                @if($report->assignee)
                <div class="ird__assigned">
                    <i class="fa-solid fa-user-gear"></i>
                    <div class="ird__assigned-info">
                        <div class="ird__assigned-name">{{ $report->assignee->name }}</div>
                        <div class="ird__assigned-phone">{{ $report->assignee->phone ?? '' }}</div>
                    </div>
                </div>
                @endif
                <form class="ird__assign-form" method="POST" action="{{ portal_route('cleaning-reports.assign', $report->id) }}" style="margin-top:{{ $report->assignee ? '12px' : '0' }}">
                    @csrf
                    <select name="assigned_to" class="ird__assign-select" required>
                        <option value="">{{ $report->assignee ? 'Đổi người xử lý...' : 'Chọn kỹ thuật viên...' }}</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $report->assigned_to == $tech->id ? 'selected' : '' }}>{{ $tech->name }}{{ $tech->phone ? ' – ' . $tech->phone : '' }}</option>
                        @endforeach
                    </select>
                    <textarea name="admin_note" class="ird__assign-textarea" placeholder="Ghi chú cho kỹ thuật viên (tuỳ chọn)...">{{ $report->admin_note }}</textarea>
                    <button type="submit" class="ird__assign-btn"><i class="fa-solid fa-user-plus"></i> Phân công</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="ird-lightbox" id="irdLightbox">
    <button class="ird-lightbox__close" onclick="closeLightbox()" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
    <div class="ird-lightbox__content" id="irdLightboxContent"></div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const reportId = {{ $report->id }};

    // ═══════════════════════════════════════
    // STATUS UPDATE
    // ═══════════════════════════════════════
    window.changeStatus = function(status, btnEl) {
        document.querySelectorAll('.ird__status-btn').forEach(b => b.classList.remove('ird__status-btn--active'));
        btnEl.classList.add('ird__status-btn--active');

        const msg = document.getElementById('statusMsg');
        msg.textContent = 'Đang cập nhật...';
        msg.style.color = '#64748b';

        fetch('/{{ request()->segment(1) }}/cleaning-reports/' + reportId + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ status })
        }).then(r => {
            if (r.ok) {
                msg.textContent = '✓ Đã cập nhật trạng thái';
                msg.style.color = '#059669';
                setTimeout(() => { msg.textContent = ''; }, 2500);
            } else throw new Error();
        }).catch(() => {
            msg.textContent = '✗ Lỗi cập nhật, thử lại';
            msg.style.color = '#dc2626';
        });
    };

    // ═══════════════════════════════════════
    // LIGHTBOX
    // ═══════════════════════════════════════
    window.openLightbox = function(url, type) {
        const content = document.getElementById('irdLightboxContent');
        if (type === 'video') {
            content.innerHTML = '<video src="' + url + '" controls autoplay style="max-width:90vw;max-height:85vh;border-radius:10px;"></video>';
        } else {
            content.innerHTML = '<img src="' + url + '" alt="Xem ảnh" style="max-width:90vw;max-height:85vh;border-radius:10px;">';
        }
        document.getElementById('irdLightbox').classList.add('ird-lightbox--active');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function() {
        const lb = document.getElementById('irdLightbox');
        lb.classList.remove('ird-lightbox--active');
        const video = lb.querySelector('video');
        if (video) video.pause();
        document.getElementById('irdLightboxContent').innerHTML = '';
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
    document.getElementById('irdLightbox').addEventListener('click', function(e) {
        if (e.target === this) closeLightbox();
    });
})();
</script>
@endpush
