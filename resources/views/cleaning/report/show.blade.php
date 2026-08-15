@extends('layouts.cleaning.master')

@section('page_title', 'Chi tiết báo cáo – DomusHub')

@push('styles')
<style>
    .report-detail{max-width:720px;margin:0 auto;}
    .report-detail__back{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#707EAE;text-decoration:none;margin-bottom:16px;transition:.2s;}
    .report-detail__back:hover{color:#3652D9;}
    .report-detail__card{background:white;border-radius:16px;padding:32px;box-shadow:0 2px 16px rgba(54,82,217,.05);}
    .report-detail__header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;}
    .report-detail__title{font-size:20px;font-weight:700;color:#1B2559;margin:0 0 6px;}
    .report-detail__meta{display:flex;flex-wrap:wrap;gap:8px;align-items:center;}
    .report-detail__badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;}
    .report-detail__badge--pending{background:#FFF4E5;color:#D97706;}
    .report-detail__badge--processing{background:#EEF2FF;color:#3652D9;}
    .report-detail__badge--resolved{background:#E6F9F0;color:#059669;}
    .report-detail__badge--low{background:#ECFDF5;color:#059669;}
    .report-detail__badge--medium{background:#FFFBEB;color:#D97706;}
    .report-detail__badge--high{background:#FEF2F2;color:#DC2626;}
    .report-detail__date{font-size:12px;color:#A3AED0;}

    .report-detail__section{margin-bottom:24px;}
    .report-detail__label{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;}
    .report-detail__location{display:flex;align-items:center;gap:10px;padding:14px 18px;background:#F8FAFC;border-radius:12px;border:1px solid #E2E8F0;}
    .report-detail__location i{font-size:16px;color:#6366F1;}
    .report-detail__location span{font-size:14px;font-weight:600;color:#1B2559;}
    .report-detail__description{font-size:14px;color:#4A5568;line-height:1.7;white-space:pre-wrap;}
    .report-detail__description--empty{color:#A3AED0;font-style:italic;}

    /* Media Gallery */
    .report-gallery{display:grid;grid-template-columns:repeat(auto-fill, minmax(140px, 1fr));gap:12px;}
    .report-gallery__item{position:relative;border-radius:12px;overflow:hidden;cursor:pointer;aspect-ratio:1;border:2px solid #E9EDF7;transition:.2s;}
    .report-gallery__item:hover{border-color:#3652D9;transform:scale(1.02);box-shadow:0 4px 16px rgba(54,82,217,.12);}
    .report-gallery__item img,.report-gallery__item video{width:100%;height:100%;object-fit:cover;display:block;}
    .report-gallery__play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35);}
    .report-gallery__play i{color:white;font-size:28px;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3));}
    .report-gallery__type{position:absolute;top:8px;left:8px;padding:3px 8px;border-radius:6px;font-size:10px;font-weight:700;background:rgba(0,0,0,.6);color:white;backdrop-filter:blur(4px);}

    /* Lightbox */
    .lightbox{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.88);align-items:center;justify-content:center;padding:20px;}
    .lightbox--active{display:flex;}
    .lightbox__close{position:absolute;top:20px;right:24px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.12);backdrop-filter:blur(4px);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;}
    .lightbox__close:hover{background:rgba(255,255,255,.25);}

    /* Timeline */
    .report-detail__timeline{display:flex;flex-direction:column;gap:0;}
    .timeline-item{display:flex;align-items:flex-start;gap:12px;padding:12px 0;position:relative;}
    .timeline-item:not(:last-child)::after{content:'';position:absolute;left:11px;top:32px;bottom:0;width:2px;background:#E9EDF7;}
    .timeline-dot{width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;color:white;}
    .timeline-dot--created{background:#3652D9;}
    .timeline-dot--processing{background:#F59E0B;}
    .timeline-dot--resolved{background:#05CD99;}
    .timeline-info__title{font-size:13px;font-weight:600;color:#1B2559;}
    .timeline-info__date{font-size:11px;color:#A3AED0;margin-top:2px;}

    @media(max-width:640px){
        .report-detail__card{padding:20px;}
        .report-gallery{grid-template-columns:repeat(auto-fill, minmax(100px, 1fr));}
    }
</style>
@endpush

@section('content')
<div class="report-detail">
    <a href="{{ route('cleaning.report') }}" class="report-detail__back">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </a>

    <div class="report-detail__card">
        <!-- Header -->
        <div class="report-detail__header">
            <div>
                <h1 class="report-detail__title">{{ $report->title }}</h1>
                <div class="report-detail__meta">
                    <span class="report-detail__badge report-detail__badge--{{ $report->status }}">
                        @if($report->status === 'pending')
                            <i class="fa-solid fa-clock"></i> Chờ xử lý
                        @elseif($report->status === 'processing')
                            <i class="fa-solid fa-gear"></i> Đang xử lý
                        @else
                            <i class="fa-solid fa-circle-check"></i> Đã xử lý
                        @endif
                    </span>
                    <span class="report-detail__badge report-detail__badge--{{ $report->priority }}">
                        @if($report->priority === 'high')
                            <i class="fa-solid fa-triangle-exclamation"></i> Ưu tiên cao
                        @elseif($report->priority === 'medium')
                            <i class="fa-solid fa-minus"></i> Trung bình
                        @else
                            <i class="fa-solid fa-arrow-down"></i> Thấp
                        @endif
                    </span>
                    <span class="report-detail__date"><i class="fa-regular fa-calendar"></i> {{ $report->created_at->format('H:i – d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Location -->
        <div class="report-detail__section">
            <div class="report-detail__label">Vị trí sự cố</div>
            <div class="report-detail__location">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $report->location ?: 'Không xác định' }}</span>
            </div>
        </div>

        <!-- Description -->
        <div class="report-detail__section">
            <div class="report-detail__label">Mô tả chi tiết</div>
            @if($report->description)
                <p class="report-detail__description">{{ $report->description }}</p>
            @else
                <p class="report-detail__description report-detail__description--empty">Không có mô tả.</p>
            @endif
        </div>

        <!-- Media Gallery -->
        @if($report->images && count($report->images) > 0)
        <div class="report-detail__section">
            <div class="report-detail__label">Ảnh / Video đính kèm ({{ count($report->images) }})</div>
            <div class="report-gallery">
                @foreach($report->images as $media)
                @php
                    $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                    $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                    $url = asset('storage/' . $media);
                @endphp
                <div class="report-gallery__item" onclick="openLightbox('{{ $url }}', '{{ $isVideo ? 'video' : 'image' }}')">
                    @if($isVideo)
                        <video src="{{ $url }}" muted preload="metadata"></video>
                        <div class="report-gallery__play"><i class="fa-solid fa-play"></i></div>
                        <span class="report-gallery__type"><i class="fa-solid fa-video"></i> Video</span>
                    @else
                        <img src="{{ $url }}" alt="Ảnh sự cố">
                        <span class="report-gallery__type"><i class="fa-solid fa-image"></i> Ảnh</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="report-detail__section">
            <div class="report-detail__label">Tiến trình</div>
            <div class="report-detail__timeline">
                <div class="timeline-item">
                    <div class="timeline-dot timeline-dot--created"><i class="fa-solid fa-plus"></i></div>
                    <div class="timeline-info">
                        <div class="timeline-info__title">Đã gửi báo cáo</div>
                        <div class="timeline-info__date">{{ $report->created_at->format('H:i – d/m/Y') }}</div>
                    </div>
                </div>
                @if($report->status === 'processing' || $report->status === 'resolved')
                <div class="timeline-item">
                    <div class="timeline-dot timeline-dot--processing"><i class="fa-solid fa-gear"></i></div>
                    <div class="timeline-info">
                        <div class="timeline-info__title">Đang xử lý</div>
                        <div class="timeline-info__date">{{ $report->updated_at->format('H:i – d/m/Y') }}</div>
                    </div>
                </div>
                @endif
                @if($report->status === 'resolved')
                <div class="timeline-item">
                    <div class="timeline-dot timeline-dot--resolved"><i class="fa-solid fa-check"></i></div>
                    <div class="timeline-info">
                        <div class="timeline-info__title">Đã xử lý xong</div>
                        <div class="timeline-info__date">{{ $report->updated_at->format('H:i – d/m/Y') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openLightbox(url, type) {
    let lightbox = document.getElementById('mediaLightbox');
    if (!lightbox) {
        lightbox = document.createElement('div');
        lightbox.id = 'mediaLightbox';
        lightbox.className = 'lightbox';
        lightbox.innerHTML = '<button class="lightbox__close" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button><div id="lightboxContent"></div>';
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

document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush
