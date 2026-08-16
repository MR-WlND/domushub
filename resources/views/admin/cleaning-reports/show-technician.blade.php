@extends('layouts.admin.master')

@section('page_title', 'Chi tiết sự cố – DomusHub')
@section('home_route', portal_route('tickets.my-tasks'))
@section('user_name', auth()->user()->name ?? 'KTV')
@section('user_role_label', 'KỸ THUẬT VIÊN')

@push('styles')
<style>
.irt{max-width:800px;}
.irt__back{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#64748b;text-decoration:none;margin-bottom:20px;transition:.2s;}
.irt__back:hover{color:#4F46E5;}

.irt__card{background:white;border-radius:14px;padding:28px 32px;border:1px solid #f1f5f9;margin-bottom:20px;}

/* Header */
.irt__title{font-size:20px;font-weight:800;color:#0f172a;margin:0 0 10px;}
.irt__badges{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;}
.irt__badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:700;}
.irt__badge--high{background:#FEE2E2;color:#DC2626;}
.irt__badge--medium{background:#FEF3C7;color:#D97706;}
.irt__badge--low{background:#D1FAE5;color:#059669;}
.irt__badge--pending{background:#FEF3C7;color:#D97706;}
.irt__badge--processing{background:#DBEAFE;color:#2563EB;}
.irt__badge--resolved{background:#D1FAE5;color:#059669;}

/* Info grid */
.irt__info{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:24px;}
.irt__info-item{background:#f8fafc;border-radius:10px;padding:14px 16px;border:1px solid #f1f5f9;}
.irt__info-label{font-size:10.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;}
.irt__info-value{font-size:14px;font-weight:600;color:#1e293b;display:flex;align-items:center;gap:6px;}
.irt__info-value i{font-size:12px;color:#94a3b8;}

/* Section */
.irt__section{margin-bottom:24px;}
.irt__section:last-child{margin-bottom:0;}
.irt__section-title{font-size:11.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;display:flex;align-items:center;gap:8px;}
.irt__section-title i{font-size:13px;}
.irt__desc{font-size:14px;color:#334155;line-height:1.7;white-space:pre-wrap;background:#f8fafc;border-radius:10px;padding:16px 18px;border:1px solid #f1f5f9;}

/* Gallery */
.irt__gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;}
.irt__gallery-item{aspect-ratio:1;border-radius:12px;overflow:hidden;border:2px solid #e2e8f0;cursor:pointer;position:relative;transition:.2s;}
.irt__gallery-item:hover{border-color:#4F46E5;transform:scale(1.02);}
.irt__gallery-item img,.irt__gallery-item video{width:100%;height:100%;object-fit:cover;display:block;}
.irt__gallery-play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.4);}
.irt__gallery-play i{color:white;font-size:28px;}

/* Status update */
.irt__status-btns{display:flex;gap:10px;flex-wrap:wrap;}
.irt__status-btn{padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;border:2px solid #e2e8f0;background:white;color:#64748b;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:8px;}
.irt__status-btn:hover{border-color:#cbd5e1;background:#f8fafc;}
.irt__status-btn--active{color:white;border-color:transparent;}
.irt__status-btn--processing.irt__status-btn--active{background:#3B82F6;border-color:#3B82F6;}
.irt__status-btn--resolved.irt__status-btn--active{background:#10B981;border-color:#10B981;}
.irt__status-msg{font-size:12px;margin-top:10px;font-weight:600;min-height:18px;}

/* Admin note */
.irt__note{background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px 18px;font-size:13px;color:#92400E;line-height:1.6;}
.irt__note-label{font-size:10.5px;font-weight:700;color:#D97706;text-transform:uppercase;margin-bottom:6px;}

/* Lightbox */
.irt-lightbox{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.9);display:none;align-items:center;justify-content:center;padding:24px;}
.irt-lightbox--active{display:flex;}
.irt-lightbox__close{position:absolute;top:20px;right:24px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.1);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;}
.irt-lightbox__close:hover{background:rgba(255,255,255,.2);}
.irt-lightbox__content img,.irt-lightbox__content video{max-width:90vw;max-height:85vh;border-radius:10px;display:block;}

@media(max-width:640px){
    .irt__card{padding:20px;}
    .irt__gallery{grid-template-columns:repeat(auto-fill,minmax(100px,1fr));}
    .irt__status-btns{flex-direction:column;}
}
</style>
@endpush

@section('content')
@php
    $roleLabels = ['cleaning'=>'Vệ sinh','security'=>'Bảo vệ','technician'=>'Kỹ thuật','receptionist'=>'Lễ tân','staff'=>'Nhân viên','manager'=>'Quản lý'];
    $roleLabel = $roleLabels[$report->reporter->role ?? ''] ?? '—';
    $priorityLabels = ['high'=>'Cao','medium'=>'Trung bình','low'=>'Thấp'];
    $statusLabels = ['pending'=>'Chờ xử lý','processing'=>'Đang xử lý','resolved'=>'Đã xử lý'];
@endphp

<div class="irt">
    <a href="{{ portal_route('tickets.my-tasks') }}" class="irt__back">
        <i class="fa-solid fa-arrow-left"></i> Quay lại nhiệm vụ
    </a>

    <div class="irt__card">
        {{-- Header --}}
        <h1 class="irt__title">{{ $report->title }}</h1>
        <div class="irt__badges">
            <span class="irt__badge irt__badge--{{ $report->status }}">
                <i class="fa-solid fa-{{ $report->status === 'pending' ? 'clock' : ($report->status === 'processing' ? 'gear' : 'circle-check') }}"></i>
                {{ $statusLabels[$report->status] }}
            </span>
            <span class="irt__badge irt__badge--{{ $report->priority }}">
                {{ $priorityLabels[$report->priority] }}
            </span>
            <span style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:4px;">
                <i class="fa-regular fa-calendar"></i> {{ $report->created_at->format('H:i – d/m/Y') }}
            </span>
        </div>

        {{-- Info --}}
        <div class="irt__info">
            <div class="irt__info-item">
                <div class="irt__info-label">Người báo cáo</div>
                <div class="irt__info-value"><i class="fa-solid fa-user"></i> {{ $report->reporter->name ?? '—' }}</div>
            </div>
            <div class="irt__info-item">
                <div class="irt__info-label">Bộ phận</div>
                <div class="irt__info-value"><i class="fa-solid fa-building"></i> {{ $roleLabel }}</div>
            </div>
            <div class="irt__info-item">
                <div class="irt__info-label">Vị trí sự cố</div>
                <div class="irt__info-value"><i class="fa-solid fa-location-dot"></i> {{ $report->location ?: '—' }}</div>
            </div>
        </div>

        {{-- Admin note (nếu có) --}}
        @if($report->admin_note)
        <div class="irt__section">
            <div class="irt__note">
                <div class="irt__note-label"><i class="fa-solid fa-bullhorn"></i> Ghi chú từ quản lý</div>
                {{ $report->admin_note }}
            </div>
        </div>
        @endif

        {{-- Description --}}
        <div class="irt__section">
            <div class="irt__section-title"><i class="fa-solid fa-align-left"></i> Mô tả sự cố</div>
            @if($report->description)
                <div class="irt__desc">{{ $report->description }}</div>
            @else
                <p style="font-size:13px;color:#94a3b8;font-style:italic;">Không có mô tả chi tiết.</p>
            @endif
        </div>

        {{-- Gallery --}}
        @if($report->images && count($report->images) > 0)
        <div class="irt__section">
            <div class="irt__section-title"><i class="fa-solid fa-images"></i> Ảnh / Video đính kèm ({{ count($report->images) }})</div>
            <div class="irt__gallery">
                @foreach($report->images as $media)
                @php
                    $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                    $url = asset('storage/' . $media);
                @endphp
                <div class="irt__gallery-item" onclick="openLightbox('{{ $url }}', '{{ $isVideo ? 'video' : 'image' }}')">
                    @if($isVideo)
                        <video src="{{ $url }}" muted preload="metadata"></video>
                        <div class="irt__gallery-play"><i class="fa-solid fa-play"></i></div>
                    @else
                        <img src="{{ $url }}" alt="Ảnh sự cố">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Status update --}}
        <div class="irt__section">
            <div class="irt__section-title"><i class="fa-solid fa-list-check"></i> Cập nhật tiến độ</div>
            <div class="irt__status-btns" id="statusBtns">
                <button type="button" class="irt__status-btn irt__status-btn--processing {{ $report->status == 'processing' ? 'irt__status-btn--active' : '' }}" data-status="processing">
                    <i class="fa-solid fa-gear"></i> Đang xử lý
                </button>
                <button type="button" class="irt__status-btn irt__status-btn--resolved {{ $report->status == 'resolved' ? 'irt__status-btn--active' : '' }}" data-status="resolved">
                    <i class="fa-solid fa-circle-check"></i> Đã hoàn thành
                </button>
            </div>
            <div class="irt__status-msg" id="statusMsg"></div>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="irt-lightbox" id="irtLightbox">
    <button class="irt-lightbox__close" onclick="closeLightbox()" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
    <div class="irt-lightbox__content" id="irtLightboxContent"></div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const reportId = {{ $report->id }};
    const baseUrl = '/{{ request()->segment(1) }}/cleaning-reports/' + reportId;

    // Status update
    document.getElementById('statusBtns').addEventListener('click', function(e) {
        const btn = e.target.closest('.irt__status-btn');
        if (!btn) return;

        const status = btn.dataset.status;
        document.querySelectorAll('.irt__status-btn').forEach(b => b.classList.remove('irt__status-btn--active'));
        btn.classList.add('irt__status-btn--active');

        const msg = document.getElementById('statusMsg');
        msg.textContent = 'Đang cập nhật...';
        msg.style.color = '#64748b';

        fetch(baseUrl + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ status })
        }).then(r => {
            if (r.ok) {
                msg.textContent = status === 'resolved' ? '✓ Đã đánh dấu hoàn thành!' : '✓ Đã cập nhật trạng thái';
                msg.style.color = '#059669';
                setTimeout(() => { msg.textContent = ''; }, 3000);
            } else throw new Error();
        }).catch(() => {
            msg.textContent = '✗ Lỗi cập nhật, thử lại';
            msg.style.color = '#dc2626';
        });
    });

    // Lightbox
    window.openLightbox = function(url, type) {
        const content = document.getElementById('irtLightboxContent');
        if (type === 'video') {
            content.innerHTML = '<video src="' + url + '" controls autoplay style="max-width:90vw;max-height:85vh;border-radius:10px;"></video>';
        } else {
            content.innerHTML = '<img src="' + url + '" alt="Xem ảnh" style="max-width:90vw;max-height:85vh;border-radius:10px;">';
        }
        document.getElementById('irtLightbox').classList.add('irt-lightbox--active');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function() {
        const lb = document.getElementById('irtLightbox');
        lb.classList.remove('irt-lightbox--active');
        const video = lb.querySelector('video');
        if (video) video.pause();
        document.getElementById('irtLightboxContent').innerHTML = '';
        document.body.style.overflow = '';
    };

    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
    document.getElementById('irtLightbox').addEventListener('click', function(e) { if (e.target === this) closeLightbox(); });
})();
</script>
@endpush
