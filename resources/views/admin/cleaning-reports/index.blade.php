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

/* Detail Slide Panel */
.ir-panel-overlay{position:fixed;inset:0;z-index:9990;background:rgba(15,23,42,.4);backdrop-filter:blur(3px);display:none;opacity:0;transition:opacity .25s;}
.ir-panel-overlay--active{display:block;opacity:1;}
.ir-panel{position:fixed;top:0;right:-480px;bottom:0;width:480px;max-width:100vw;background:white;z-index:9991;box-shadow:-8px 0 40px rgba(0,0,0,.1);transition:right .3s cubic-bezier(.4,0,.2,1);overflow-y:auto;}
.ir-panel--active{right:0;}
.ir-panel__header{position:sticky;top:0;background:white;padding:24px 28px 16px;border-bottom:1px solid #f1f5f9;z-index:2;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
.ir-panel__title{font-size:18px;font-weight:800;color:#0f172a;line-height:1.3;margin:0;}
.ir-panel__close{width:36px;height:36px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;flex-shrink:0;}
.ir-panel__close:hover{background:#e2e8f0;color:#0f172a;}
.ir-panel__body{padding:24px 28px 32px;}
.ir-panel__section{margin-bottom:24px;}
.ir-panel__section-title{font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;}
.ir-panel__info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.ir-panel__info-card{background:#f8fafc;border-radius:10px;padding:14px 16px;border:1px solid #f1f5f9;}
.ir-panel__info-label{font-size:10.5px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px;}
.ir-panel__info-value{font-size:14px;font-weight:600;color:#1e293b;}
.ir-panel__desc{font-size:14px;color:#334155;line-height:1.7;background:#f8fafc;border-radius:10px;padding:16px 18px;border:1px solid #f1f5f9;white-space:pre-wrap;}
.ir-panel__gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;}
.ir-panel__gallery-item{aspect-ratio:1;border-radius:10px;overflow:hidden;border:1.5px solid #e2e8f0;cursor:pointer;position:relative;transition:.2s;}
.ir-panel__gallery-item:hover{border-color:#4F46E5;transform:scale(1.02);}
.ir-panel__gallery-item img,.ir-panel__gallery-item video{width:100%;height:100%;object-fit:cover;display:block;}
.ir-panel__gallery-play{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.4);}
.ir-panel__gallery-play i{color:white;font-size:24px;}
.ir-panel__status-group{display:flex;gap:8px;flex-wrap:wrap;}
.ir-panel__status-btn{padding:8px 16px;border-radius:8px;font-size:13px;font-weight:700;border:2px solid #e2e8f0;background:white;color:#64748b;cursor:pointer;transition:.2s;}
.ir-panel__status-btn:hover{border-color:#cbd5e1;background:#f8fafc;}
.ir-panel__status-btn--active{color:white;}
.ir-panel__status-btn--pending.ir-panel__status-btn--active{background:#F59E0B;border-color:#F59E0B;}
.ir-panel__status-btn--processing.ir-panel__status-btn--active{background:#3B82F6;border-color:#3B82F6;}
.ir-panel__status-btn--resolved.ir-panel__status-btn--active{background:#10B981;border-color:#10B981;}
.ir-panel__status-msg{font-size:12px;margin-top:8px;font-weight:600;}

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
             data-id="{{ $report->id }}"
             data-title="{{ e($report->title) }}"
             data-reporter="{{ e($report->reporter->name ?? '—') }}"
             data-role="{{ $roleLabel }}"
             data-location="{{ e($report->location) }}"
             data-priority="{{ $report->priority }}"
             data-status="{{ $report->status }}"
             data-description="{{ e($report->description ?? '') }}"
             data-images='@json($report->images ?? [])'
             data-date="{{ $report->created_at->format('H:i – d/m/Y') }}"
             onclick="openPanel(this)"
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
                    <div class="ir-card__media-thumb" onclick="event.stopPropagation();">
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

{{-- DETAIL SLIDE PANEL --}}
<div class="ir-panel-overlay" id="panelOverlay" onclick="closePanel()"></div>
<div class="ir-panel" id="detailPanel">
    <div class="ir-panel__header">
        <h2 class="ir-panel__title" id="panelTitle"></h2>
        <button class="ir-panel__close" onclick="closePanel()" aria-label="Đóng">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="ir-panel__body">
        <div class="ir-panel__section">
            <div class="ir-panel__section-title">Thông tin sự cố</div>
            <div class="ir-panel__info-grid">
                <div class="ir-panel__info-card">
                    <div class="ir-panel__info-label">Người báo cáo</div>
                    <div class="ir-panel__info-value" id="panelReporter"></div>
                </div>
                <div class="ir-panel__info-card">
                    <div class="ir-panel__info-label">Bộ phận</div>
                    <div class="ir-panel__info-value" id="panelRole"></div>
                </div>
                <div class="ir-panel__info-card">
                    <div class="ir-panel__info-label">Khu vực</div>
                    <div class="ir-panel__info-value" id="panelLocation"></div>
                </div>
                <div class="ir-panel__info-card">
                    <div class="ir-panel__info-label">Mức ưu tiên</div>
                    <div class="ir-panel__info-value" id="panelPriority"></div>
                </div>
                <div class="ir-panel__info-card" style="grid-column:span 2;">
                    <div class="ir-panel__info-label">Thời gian báo cáo</div>
                    <div class="ir-panel__info-value" id="panelDate"></div>
                </div>
            </div>
        </div>

        <div class="ir-panel__section" id="panelDescSection">
            <div class="ir-panel__section-title">Mô tả chi tiết</div>
            <div class="ir-panel__desc" id="panelDesc"></div>
        </div>

        <div class="ir-panel__section" id="panelMediaSection" style="display:none;">
            <div class="ir-panel__section-title">Ảnh / Video đính kèm</div>
            <div class="ir-panel__gallery" id="panelGallery"></div>
        </div>

        <div class="ir-panel__section">
            <div class="ir-panel__section-title">Cập nhật trạng thái</div>
            <div class="ir-panel__status-group" id="panelStatusGroup">
                <button type="button" class="ir-panel__status-btn ir-panel__status-btn--pending" data-status="pending" onclick="updateStatusPanel('pending', this)"><i class="fa-solid fa-clock"></i> Chờ xử lý</button>
                <button type="button" class="ir-panel__status-btn ir-panel__status-btn--processing" data-status="processing" onclick="updateStatusPanel('processing', this)"><i class="fa-solid fa-gear"></i> Đang xử lý</button>
                <button type="button" class="ir-panel__status-btn ir-panel__status-btn--resolved" data-status="resolved" onclick="updateStatusPanel('resolved', this)"><i class="fa-solid fa-circle-check"></i> Đã xử lý</button>
            </div>
            <div class="ir-panel__status-msg" id="panelStatusMsg"></div>
        </div>
    </div>
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
    const priorityMap = { high: 'Cao', medium: 'Trung bình', low: 'Thấp' };
    let currentPanelId = null;

    // ═══════════════════════════════════════
    // SLIDE PANEL
    // ═══════════════════════════════════════
    window.openPanel = function(cardEl) {
        const d = cardEl.dataset;
        currentPanelId = d.id;

        document.getElementById('panelTitle').textContent = d.title;
        document.getElementById('panelReporter').textContent = d.reporter;
        document.getElementById('panelRole').textContent = d.role;
        document.getElementById('panelLocation').textContent = d.location || '—';
        document.getElementById('panelPriority').textContent = priorityMap[d.priority] || d.priority;
        document.getElementById('panelDate').textContent = d.date;

        // Description
        const desc = d.description;
        const descSection = document.getElementById('panelDescSection');
        if (desc) {
            descSection.style.display = '';
            document.getElementById('panelDesc').textContent = desc;
        } else {
            descSection.style.display = 'none';
        }

        // Media
        const images = JSON.parse(d.images || '[]');
        const gallery = document.getElementById('panelGallery');
        const mediaSection = document.getElementById('panelMediaSection');
        gallery.innerHTML = '';
        if (images.length > 0) {
            mediaSection.style.display = '';
            images.forEach(img => {
                const ext = img.split('.').pop().toLowerCase();
                const isVideo = ['mp4','mov','avi','webm'].includes(ext);
                const url = '/storage/' + img;
                const item = document.createElement('div');
                item.className = 'ir-panel__gallery-item';

                if (isVideo) {
                    item.innerHTML = `<video src="${url}" muted preload="metadata"></video><div class="ir-panel__gallery-play"><i class="fa-solid fa-play"></i></div>`;
                    item.onclick = () => openLightbox(url, 'video');
                } else {
                    item.innerHTML = `<img src="${url}" alt="Ảnh sự cố">`;
                    item.onclick = () => openLightbox(url, 'image');
                }
                gallery.appendChild(item);
            });
        } else {
            mediaSection.style.display = 'none';
        }

        // Status buttons
        document.querySelectorAll('#panelStatusGroup .ir-panel__status-btn').forEach(btn => {
            btn.classList.toggle('ir-panel__status-btn--active', btn.dataset.status === d.status);
        });
        document.getElementById('panelStatusMsg').textContent = '';

        document.getElementById('panelOverlay').classList.add('ir-panel-overlay--active');
        document.getElementById('detailPanel').classList.add('ir-panel--active');
        document.body.style.overflow = 'hidden';
    };

    window.closePanel = function() {
        document.getElementById('panelOverlay').classList.remove('ir-panel-overlay--active');
        document.getElementById('detailPanel').classList.remove('ir-panel--active');
        document.body.style.overflow = '';
    };

    // ═══════════════════════════════════════
    // STATUS UPDATE (inline pills)
    // ═══════════════════════════════════════
    window.updateStatus = function(id, status, btnEl) {
        const card = btnEl.closest('.ir-card');
        const pills = card.querySelectorAll('.ir-status-pill');
        pills.forEach(p => p.classList.remove('ir-status-pill--active'));
        btnEl.classList.add('ir-status-pill--active');
        card.dataset.status = status;

        fetch('/{{ request()->segment(1) }}/cleaning-reports/' + id + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ status })
        });
    };

    window.updateStatusPanel = function(status, btnEl) {
        if (!currentPanelId) return;
        document.querySelectorAll('#panelStatusGroup .ir-panel__status-btn').forEach(b => b.classList.remove('ir-panel__status-btn--active'));
        btnEl.classList.add('ir-panel__status-btn--active');

        const msg = document.getElementById('panelStatusMsg');
        msg.textContent = 'Đang cập nhật...';
        msg.style.color = '#64748b';

        fetch('/{{ request()->segment(1) }}/cleaning-reports/' + currentPanelId + '/status', {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({ status })
        }).then(r => {
            if (r.ok) {
                msg.textContent = '✓ Đã cập nhật';
                msg.style.color = '#059669';
                // Sync card pills
                const card = document.querySelector(`.ir-card[data-id="${currentPanelId}"]`);
                if (card) {
                    card.dataset.status = status;
                    card.querySelectorAll('.ir-status-pill').forEach(p => p.classList.remove('ir-status-pill--active'));
                    card.querySelector(`.ir-status-pill--${status}`).classList.add('ir-status-pill--active');
                }
                setTimeout(() => { msg.textContent = ''; }, 2000);
            } else throw new Error();
        }).catch(() => { msg.textContent = '✗ Lỗi, thử lại'; msg.style.color = '#dc2626'; });
    };

    // ═══════════════════════════════════════
    // LIGHTBOX
    // ═══════════════════════════════════════
    window.openLightbox = function(url, type) {
        const content = document.getElementById('lightboxContent');
        if (type === 'video') {
            content.innerHTML = `<video src="${url}" controls autoplay style="max-width:90vw;max-height:85vh;border-radius:10px;"></video>`;
        } else {
            content.innerHTML = `<img src="${url}" alt="Xem ảnh" style="max-width:90vw;max-height:85vh;border-radius:10px;">`;
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

    // Keyboard
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (document.getElementById('irLightbox').classList.contains('ir-lightbox--active')) closeLightbox();
            else if (document.getElementById('detailPanel').classList.contains('ir-panel--active')) closePanel();
        }
    });
})();
</script>
@endpush
