@extends('layouts.admin.master')

@section('page_title', 'Báo cáo sự cố vận hành – DomusHub')

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════
   INCIDENT REPORTS – Card-based Dashboard
   ══════════════════════════════════════════════════════════ */
.ir-page{max-width:1200px;}

/* STATS BAR */
.ir-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;}
.ir-stat{background:white;border-radius:12px;padding:18px 20px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:14px;transition:box-shadow .2s;}
.ir-stat:hover{box-shadow:0 4px 12px rgba(0,0,0,.04);}
.ir-stat__icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;}
.ir-stat__icon--total{background:#EEF2FF;color:#4F46E5;}
.ir-stat__icon--pending{background:#FEF3C7;color:#D97706;}
.ir-stat__icon--processing{background:#DBEAFE;color:#2563EB;}
.ir-stat__icon--resolved{background:#D1FAE5;color:#059669;}
.ir-stat__number{font-size:24px;font-weight:800;color:#0f172a;line-height:1;}
.ir-stat__label{font-size:12px;color:#64748b;margin-top:2px;}

/* HEADER + FILTERS */
.ir-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:20px;gap:16px;flex-wrap:wrap;}
.ir-header__left h1{font-size:22px;font-weight:800;color:#0f172a;margin:0 0 4px;}
.ir-header__left p{font-size:13px;color:#64748b;margin:0;}
.ir-filters{display:flex;gap:8px;flex-wrap:wrap;}
.ir-filter{height:36px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 12px;font-size:12.5px;font-weight:500;font-family:inherit;color:#475569;background:white;cursor:pointer;transition:.2s;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:28px;}
.ir-filter:focus{outline:none;border-color:#4F46E5;box-shadow:0 0 0 3px rgba(79,70,229,.08);}
.ir-filter--active{border-color:#4F46E5;background:#F5F3FF;color:#4F46E5;}

/* CARD LIST */
.ir-list{display:flex;flex-direction:column;gap:12px;}
.ir-card{background:white;border-radius:14px;padding:20px 24px;border:1px solid #f1f5f9;display:grid;grid-template-columns:auto 1fr auto;gap:16px;align-items:start;transition:box-shadow .2s,border-color .2s;cursor:pointer;}
.ir-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.05);border-color:#e2e8f0;}
.ir-card--high{border-left:4px solid #EF4444;}
.ir-card--medium{border-left:4px solid #F59E0B;}
.ir-card--low{border-left:4px solid #10B981;}

/* Priority indicator */
.ir-card__priority{width:36px;display:flex;flex-direction:column;align-items:center;gap:4px;padding-top:2px;}
.ir-card__priority-dot{width:10px;height:10px;border-radius:50%;}
.ir-card__priority-dot--high{background:#EF4444;box-shadow:0 0 8px rgba(239,68,68,.3);}
.ir-card__priority-dot--medium{background:#F59E0B;}
.ir-card__priority-dot--low{background:#10B981;}
.ir-card__priority-label{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.3px;color:#94a3b8;}

/* Card body */
.ir-card__body{min-width:0;}
.ir-card__title{font-size:15px;font-weight:700;color:#0f172a;margin:0 0 6px;line-height:1.3;}
.ir-card__meta{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-bottom:10px;}
.ir-card__meta-item{display:flex;align-items:center;gap:5px;font-size:12px;color:#64748b;}
.ir-card__meta-item i{font-size:11px;color:#94a3b8;}
.ir-card__dept{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:5px;font-size:10.5px;font-weight:700;background:#f1f5f9;color:#475569;}
.ir-card__media{display:flex;gap:6px;margin-top:8px;}
.ir-card__media-thumb{width:48px;height:48px;border-radius:8px;overflow:hidden;border:1.5px solid #e2e8f0;position:relative;flex-shrink:0;}
.ir-card__media-thumb img,.ir-card__media-thumb video{width:100%;height:100%;object-fit:cover;display:block;}
.ir-card__media-thumb--video::after{content:'';position:absolute;inset:0;background:rgba(0,0,0,.35);display:flex;align-items:center;justify-content:center;}
.ir-card__media-play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35);border-radius:6px;}
.ir-card__media-play i{color:white;font-size:12px;}
.ir-card__desc{font-size:12.5px;color:#64748b;margin-top:6px;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}

/* Card right */
.ir-card__right{display:flex;flex-direction:column;align-items:flex-end;gap:10px;min-width:140px;}
.ir-card__time{font-size:11.5px;color:#94a3b8;white-space:nowrap;}
.ir-card__status-pills{display:flex;gap:4px;}
.ir-status-pill{padding:5px 10px;border-radius:6px;font-size:11px;font-weight:700;border:1.5px solid #e2e8f0;background:white;color:#64748b;cursor:pointer;transition:.2s;white-space:nowrap;}
.ir-status-pill:hover{background:#f8fafc;border-color:#cbd5e1;}
.ir-status-pill--active{border-color:currentColor;color:white;}
.ir-status-pill--pending.ir-status-pill--active{background:#F59E0B;border-color:#F59E0B;}
.ir-status-pill--processing.ir-status-pill--active{background:#3B82F6;border-color:#3B82F6;}
.ir-status-pill--resolved.ir-status-pill--active{background:#10B981;border-color:#10B981;}

/* Empty state */
.ir-empty{text-align:center;padding:60px 20px;background:white;border-radius:14px;border:1px solid #f1f5f9;}
.ir-empty i{font-size:40px;color:#e2e8f0;margin-bottom:12px;}
.ir-empty h3{font-size:16px;font-weight:700;color:#334155;margin:0 0 6px;}
.ir-empty p{font-size:13px;color:#94a3b8;margin:0;}

/* Pagination */
.ir-pagination{margin-top:20px;display:flex;justify-content:center;}

/* Lightbox */
.ir-lightbox{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.9);display:none;align-items:center;justify-content:center;padding:24px;}
.ir-lightbox--active{display:flex;}
.ir-lightbox__close{position:absolute;top:20px;right:24px;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.1);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;}
.ir-lightbox__close:hover{background:rgba(255,255,255,.2);}
.ir-lightbox__content{max-width:90vw;max-height:85vh;}
.ir-lightbox__content img,.ir-lightbox__content video{max-width:90vw;max-height:85vh;border-radius:10px;display:block;}

/* Alert */
.ir-alert{padding:14px 18px;border-radius:10px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:10px;background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;font-weight:500;}

/* Responsive */
@media(max-width:1024px){.ir-stats{grid-template-columns:repeat(2,1fr);}}
@media(max-width:768px){
    .ir-stats{grid-template-columns:1fr 1fr;}
    .ir-card{grid-template-columns:1fr;gap:12px;}
    .ir-card__priority{flex-direction:row;width:auto;}
    .ir-card__right{flex-direction:row;align-items:center;min-width:0;flex-wrap:wrap;}
    .ir-panel{width:100vw;}
    .ir-header{flex-direction:column;align-items:stretch;}
}
@media(max-width:480px){.ir-stats{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php
    $totalCount = $reports->total();
    $pendingCount = \App\Models\CleaningReport::whereHas('reporter', fn($q) => $q->where('role','!=','resident'))->where('status','pending')->count();
    $processingCount = \App\Models\CleaningReport::whereHas('reporter', fn($q) => $q->where('role','!=','resident'))->where('status','processing')->count();
    $resolvedCount = \App\Models\CleaningReport::whereHas('reporter', fn($q) => $q->where('role','!=','resident'))->where('status','resolved')->count();

    $roleLabels = [
        'cleaning' => 'Vệ sinh',
        'security' => 'Bảo vệ',
        'technician' => 'Kỹ thuật',
        'receptionist' => 'Lễ tân',
        'staff' => 'Nhân viên',
        'manager' => 'Quản lý',
        'admin' => 'Admin',
    ];
@endphp

<div class="ir-page">
    {{-- STATS --}}
    <div class="ir-stats">
        <div class="ir-stat">
            <div class="ir-stat__icon ir-stat__icon--total"><i class="fa-solid fa-clipboard-list"></i></div>
            <div><div class="ir-stat__number">{{ $pendingCount + $processingCount + $resolvedCount }}</div><div class="ir-stat__label">Tổng báo cáo</div></div>
        </div>
        <div class="ir-stat">
            <div class="ir-stat__icon ir-stat__icon--pending"><i class="fa-solid fa-clock"></i></div>
            <div><div class="ir-stat__number">{{ $pendingCount }}</div><div class="ir-stat__label">Chờ xử lý</div></div>
        </div>
        <div class="ir-stat">
            <div class="ir-stat__icon ir-stat__icon--processing"><i class="fa-solid fa-gear"></i></div>
            <div><div class="ir-stat__number">{{ $processingCount }}</div><div class="ir-stat__label">Đang xử lý</div></div>
        </div>
        <div class="ir-stat">
            <div class="ir-stat__icon ir-stat__icon--resolved"><i class="fa-solid fa-circle-check"></i></div>
            <div><div class="ir-stat__number">{{ $resolvedCount }}</div><div class="ir-stat__label">Đã xử lý</div></div>
        </div>
    </div>

    {{-- HEADER + FILTERS --}}
    @if(session('success'))
    <div class="ir-alert"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <div class="ir-header">
        <div class="ir-header__left">
            <h1>Báo cáo sự cố vận hành</h1>
            <p>Tổng hợp sự cố từ tất cả nhân viên vận hành tòa nhà</p>
        </div>
        <form class="ir-filters" method="GET" action="{{ portal_route('cleaning-reports.index') }}">
            <select name="status" class="ir-filter {{ request('status') ? 'ir-filter--active' : '' }}" onchange="this.form.submit()">
                <option value="">Trạng thái</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
            </select>
            <select name="priority" class="ir-filter {{ request('priority') ? 'ir-filter--active' : '' }}" onchange="this.form.submit()">
                <option value="">Ưu tiên</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
            </select>
            <select name="role" class="ir-filter {{ request('role') ? 'ir-filter--active' : '' }}" onchange="this.form.submit()">
                <option value="">Bộ phận</option>
                <option value="cleaning" {{ request('role') == 'cleaning' ? 'selected' : '' }}>Vệ sinh</option>
                <option value="security" {{ request('role') == 'security' ? 'selected' : '' }}>Bảo vệ</option>
                <option value="technician" {{ request('role') == 'technician' ? 'selected' : '' }}>Kỹ thuật</option>
                <option value="receptionist" {{ request('role') == 'receptionist' ? 'selected' : '' }}>Lễ tân</option>
            </select>
        </form>
    </div>

    {{-- CARD LIST --}}
    <div class="ir-list">
        @forelse($reports as $report)
        @php
            $reporterRole = $report->reporter->role ?? '';
            $roleLabel = $roleLabels[$reporterRole] ?? $reporterRole;
        @endphp
        <div class="ir-card ir-card--{{ $report->priority }}"
             onclick="window.location='{{ portal_route('cleaning-reports.show', $report->id) }}'"
        >
            {{-- Priority --}}
            <div class="ir-card__priority">
                <div class="ir-card__priority-dot ir-card__priority-dot--{{ $report->priority }}"></div>
                <span class="ir-card__priority-label">{{ $report->priority === 'high' ? 'Cao' : ($report->priority === 'medium' ? 'TB' : 'Thấp') }}</span>
            </div>

            {{-- Body --}}
            <div class="ir-card__body">
                <h3 class="ir-card__title">{{ $report->title }}</h3>
                <div class="ir-card__meta">
                    <span class="ir-card__dept">{{ $roleLabel }}</span>
                    <span class="ir-card__meta-item"><i class="fa-solid fa-user"></i> {{ $report->reporter->name ?? '—' }}</span>
                    <span class="ir-card__meta-item"><i class="fa-solid fa-location-dot"></i> {{ $report->location ?: '—' }}</span>
                </div>
                @if($report->description)
                <p class="ir-card__desc">{{ $report->description }}</p>
                @endif
                @if($report->images && count($report->images) > 0)
                <div class="ir-card__media">
                    @foreach(array_slice($report->images, 0, 4) as $media)
                    @php
                        $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                        $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                    @endphp
                    <div class="ir-card__media-thumb">
                        @if($isVideo)
                        <video src="{{ asset('storage/' . $media) }}" muted preload="metadata"></video>
                        <div class="ir-card__media-play"><i class="fa-solid fa-play"></i></div>
                        @else
                        <img src="{{ asset('storage/' . $media) }}" alt="">
                        @endif
                    </div>
                    @endforeach
                    @if(count($report->images) > 4)
                    <div class="ir-card__media-thumb" style="display:flex;align-items:center;justify-content:center;background:#f1f5f9;font-size:12px;font-weight:700;color:#64748b;">
                        +{{ count($report->images) - 4 }}
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Right --}}
            <div class="ir-card__right">
                <span class="ir-card__time"><i class="fa-regular fa-clock"></i> {{ $report->created_at->format('H:i d/m') }}</span>
                <div class="ir-card__status-pills" onclick="event.stopPropagation();">
                    <button type="button" class="ir-status-pill ir-status-pill--pending {{ $report->status == 'pending' ? 'ir-status-pill--active' : '' }}" onclick="updateStatus({{ $report->id }}, 'pending', this)" title="Chờ xử lý">Chờ</button>
                    <button type="button" class="ir-status-pill ir-status-pill--processing {{ $report->status == 'processing' ? 'ir-status-pill--active' : '' }}" onclick="updateStatus({{ $report->id }}, 'processing', this)" title="Đang xử lý">Xử lý</button>
                    <button type="button" class="ir-status-pill ir-status-pill--resolved {{ $report->status == 'resolved' ? 'ir-status-pill--active' : '' }}" onclick="updateStatus({{ $report->id }}, 'resolved', this)" title="Đã xong">Xong</button>
                </div>
                @if($report->assignee)
                <span class="ir-card__meta-item" style="font-size:11px;"><i class="fa-solid fa-wrench"></i> {{ $report->assignee->name }}</span>
                @endif
            </div>
        </div>
        @empty
        <div class="ir-empty">
            <i class="fa-solid fa-clipboard-check"></i>
            <h3>Chưa có báo cáo sự cố</h3>
            <p>Khi nhân viên gửi báo cáo sự cố, chúng sẽ hiển thị tại đây.</p>
        </div>
        @endforelse
    </div>

    <div class="ir-pagination">{{ $reports->withQueryString()->links() }}</div>
</div>

{{-- LIGHTBOX --}}
<div class="ir-lightbox" id="irLightbox">
    <button class="ir-lightbox__close" onclick="closeLightbox()" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
    <div class="ir-lightbox__content" id="lightboxContent"></div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    // Status update (inline pills)
    window.updateStatus = function(id, status, btnEl) {
        const card = btnEl.closest('.ir-card');
        const pills = card.querySelectorAll('.ir-status-pill');
        pills.forEach(p => p.classList.remove('ir-status-pill--active'));
        btnEl.classList.add('ir-status-pill--active');

        fetch('/{{ request()->segment(1) }}/cleaning-reports/' + id + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ status })
        });
    };

    // Lightbox
    window.openLightbox = function(url, type) {
        const content = document.getElementById('lightboxContent');
        if (type === 'video') {
            content.innerHTML = `<video src="${url}" controls autoplay style="max-width:90vw;max-height:85vh;border-radius:10px;"></video>`;
        } else {
            content.innerHTML = `<img src="${url}" alt="" style="max-width:90vw;max-height:85vh;border-radius:10px;">`;
        }
        document.getElementById('irLightbox').classList.add('ir-lightbox--active');
    };

    window.closeLightbox = function() {
        const lb = document.getElementById('irLightbox');
        lb.classList.remove('ir-lightbox--active');
        const video = lb.querySelector('video');
        if (video) video.pause();
        document.getElementById('lightboxContent').innerHTML = '';
    };

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
})();
</script>
@endpush
