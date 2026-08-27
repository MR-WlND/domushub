@extends('layouts.resident.master')
@section('title', 'Khách ghé thăm – DomusHub')
@push('styles')
    @vite(['resources/css/resident/visitors.css'])
    <style>
    /* ---- OVERRIDES & EXTRAS ---- */
    .vr-wrap { max-width: 860px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }
    .vr-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .vr-eyebrow { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#00236f; margin:0 0 .2rem; }
    .vr-title   { font-size:1.5rem; font-weight:800; color:#0f172a; margin:0; }

    /* STATS */
    .vr-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; margin-bottom:1.5rem; }
    @media(max-width:768px){
        .vr-stats { grid-template-columns:1fr 1fr; }
        .vr-stats > :first-child { grid-column:span 2; }
    }
    .vr-stat { background:#fff; border:1px solid #e8ecf4; border-radius:12px; padding:.8rem 1rem; display:flex; align-items:center; gap:.65rem; box-shadow:0 1px 4px rgba(0,20,80,.04); }
    .vr-stat__ic { width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .vr-stat__ic--purple { background:#dbeafe; color:#00236f; }
    .vr-stat__ic--green  { background:#dcfce7; color:#16a34a; }
    .vr-stat__ic--gray   { background:#f1f5f9; color:#64748b; }
    .vr-stat__ic--orange { background:#ffedd5; color:#ea580c; }
    .vr-stat__n  { font-size:1.3rem; font-weight:800; color:#0f172a; line-height:1; }
    .vr-stat__lb { font-size:.7rem; color:#64748b; font-weight:600; margin-top:.1rem; }

    /* CARDS */
    .vr-list { display:flex; flex-direction:column; gap:.75rem; }
    .vr-card { background:#fff; border:1px solid #e8ecf4; border-left:4px solid transparent; border-radius:14px; overflow:hidden; box-shadow:0 1px 5px rgba(0,20,80,.04); transition:box-shadow .15s; }
    .vr-card:hover { box-shadow:0 4px 14px rgba(0,20,80,.09); }

    .vr-card--checked_in  { border-left-color:#047857; }
    .vr-card--checked_out { border-left-color:#64748b; }
    .vr-card--pending     { border-left-color:#2563eb; }
    .vr-card--cancelled   { border-left-color:#dc2626; }
    .vr-card--expired     { border-left-color:#d97706; }

    .vr-card__body { padding:.85rem 1rem; }
    @media(max-width:480px){
        .vr-card__body { padding:.85rem .75rem; }
    }
    .vr-card__top-row { display:flex; gap:1rem; align-items:flex-start; }
    .vr-card__avatar { width:60px; height:60px; border-radius:10px; overflow:hidden; border:1px solid #e2e8f0; flex-shrink:0; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.2rem; color:#64748b; box-shadow:0 1px 3px rgba(0,0,0,0.02); }
    .vr-card__avatar img { width:100%; height:100%; object-fit:cover; }
    .vr-card__main-info { flex:1; min-width:0; }
    .vr-card__name { font-size:.95rem; font-weight:700; color:#0f172a; margin:0 0 .25rem; display:inline-flex; align-items:center; gap:6px; }
    .vr-card__meta { font-size:.8rem; color:#475569; display:flex; flex-wrap:wrap; align-items:center; gap:.35rem; margin-bottom:.25rem; }
    .vr-card__apt  { font-weight:600; color:#0f172a; }
    .vr-dot { color:#94a3b8; }

    .vr-card__time-row { display:flex; align-items:center; gap:.25rem; font-size:.75rem; color:#64748b; }
    .vr-clock-icon { color:#94a3b8; flex-shrink:0; }

    .vr-card__divider { border-top:1px solid #f1f5f9; margin:.85rem 0 .75rem; }

    .vr-card__bottom-row { display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:nowrap; }
    .vr-card__badges { display:flex; gap:.4rem; flex-wrap:wrap; align-items:center; min-width:0; }

    /* badges */
    .vr-chip { display:inline-flex; align-items:center; gap:.2rem; font-size:.68rem; font-weight:600; padding:.15rem .45rem; border-radius:999px; line-height:1.2; flex-shrink:0; }
    .vr-chip--checked_in  { background:#d1fae5; color:#065f46; }
    .vr-chip--checked_out { background:#f1f5f9; color:#475569; }
    .vr-chip--pending     { background:#dbeafe; color:#1e40af; }
    .vr-chip--cancelled   { background:#fee2e2; color:#991b1b; }
    .vr-chip--expired     { background:#ffedd5; color:#9a3412; }
    .vr-chip--confirmed   { background:#d1fae5; color:#065f46; }

    /* action */
    .vr-btn { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .6rem; border-radius:6px; font-size:.72rem; font-weight:500; font-family:inherit; cursor:pointer; border:1.2px solid; text-decoration:none; transition:all .15s; flex-shrink:0; }
    .vr-btn--outline { background:#fff; color:#475569; border-color:#cbd5e1; }
    .vr-btn--outline:hover { background:#f8fafc; color:#0f172a; border-color:#cbd5e1; }

    /* empty */
    .vr-empty { background:#fff; border:1px solid #e8ecf4; border-radius:14px; padding:3.5rem 1.5rem; text-align:center; }
    .vr-empty svg { opacity:.3; margin-bottom:.75rem; }
    .vr-empty h3 { font-size:1rem; font-weight:700; color:#1e293b; margin:0 0 .4rem; }
    .vr-empty p  { font-size:.85rem; color:#64748b; margin:0; }

    /* New notification dot */
    .vr-card__new-dot { display:inline-block; width:7px; height:7px; border-radius:50%; background:#00236f; flex-shrink:0; }
    </style>
@endpush

@section('content')
<div class="vr-wrap">

    {{-- HEADER --}}
    <div class="vr-header">
        <div>
            <p class="vr-eyebrow">Dịch vụ</p>
            <h1 class="vr-title">Khách ghé thăm</h1>
        </div>
    </div>

    @if($visitors->isEmpty())
        <div class="vr-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <h3>Chưa có khách nào đến thăm</h3>
            <p>Khi bảo vệ đăng ký khách vào thăm căn hộ, bạn sẽ nhận được thông báo và thấy thông tin tại đây.</p>
        </div>
    @else

        {{-- STATS --}}
        @php
            $inCount   = $visitors->where('status','checked_in')->count();
            $outCount  = $visitors->where('status','checked_out')->count();
            $otherCount= $visitors->whereIn('status',['cancelled','expired','pending'])->count();
        @endphp
        <div class="vr-stats">
            <div class="vr-stat">
                <div class="vr-stat__ic vr-stat__ic--purple">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div><div class="vr-stat__n">{{ $visitors->count() }}</div><div class="vr-stat__lb">Tổng cộng</div></div>
            </div>
            <div class="vr-stat">
                <div class="vr-stat__ic vr-stat__ic--green">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div><div class="vr-stat__n">{{ $inCount }}</div><div class="vr-stat__lb">Đang ở trong</div></div>
            </div>
            <div class="vr-stat">
                <div class="vr-stat__ic vr-stat__ic--gray">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </div>
                <div><div class="vr-stat__n">{{ $outCount }}</div><div class="vr-stat__lb">Đã ra</div></div>
            </div>
        </div>

        {{-- LIST --}}
        <div class="vr-list">
            @foreach($visitors as $v)
                @php $isNew = $v->created_at->gt(now()->subHours(2)) && $v->status === 'checked_in'; @endphp
                <div class="vr-card vr-card--{{ $v->status }}">
                    <div class="vr-card__body">
                        
                        {{-- Upper Row: Avatar + Info --}}
                        <div class="vr-card__top-row">
                            <div class="vr-card__avatar">
                                @if($v->face_image)
                                    <img src="{{ asset('storage/'.$v->face_image) }}" alt="{{ $v->guest_name }}">
                                @else
                                    {{ mb_strtoupper(mb_substr($v->guest_name,0,1)) }}
                                @endif
                            </div>
                            <div class="vr-card__main-info">
                                <p class="vr-card__name">
                                    {{ $v->guest_name }}
                                    @if($isNew)
                                        <span class="vr-card__new-dot" title="Khách mới"></span>
                                    @endif
                                </p>
                                <div class="vr-card__meta">
                                    @if($v->guest_phone)
                                        <span>{{ $v->guest_phone }}</span>
                                        <span class="vr-dot">&middot;</span>
                                    @endif
                                    <span class="vr-card__apt">
                                        {{ $v->apartment?->apartment_number ?? '' }}
                                        @if($v->apartment?->floor?->block)
                                            - {{ $v->apartment->floor->block->name }}
                                        @endif
                                    </span>
                                </div>
                                <div class="vr-card__time-row">
                                    <svg class="vr-clock-icon" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>{{ $v->created_at->format('H:i d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Divider line --}}
                        <div class="vr-card__divider"></div>

                        {{-- Lower Row: Badges + Action button --}}
                        <div class="vr-card__bottom-row">
                            <div class="vr-card__badges">
                                @if($v->status === 'checked_in')
                                    <span class="vr-chip vr-chip--checked_in">
                                        Đã vào @if($v->check_in_at) lúc {{ $v->check_in_at->format('H:i') }} @endif
                                    </span>
                                @elseif($v->status === 'checked_out')
                                    <span class="vr-chip vr-chip--checked_out">
                                        Đã ra @if($v->check_out_at) lúc {{ $v->check_out_at->format('H:i') }} @endif
                                    </span>
                                @else
                                    <span class="vr-chip vr-chip--{{ $v->status }}">{{ $v->statusLabel() }}</span>
                                @endif

                                @if($v->confirmed_by_resident)
                                    <span class="vr-chip vr-chip--confirmed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Đã xác nhận</span>
                                    </span>
                                @endif
                            </div>

                            <div class="vr-card__action">
                                <a href="{{ route('resident.visitors.show', $v->id) }}" class="vr-btn vr-btn--outline">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <span>Chi tiết</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lắng nghe sự kiện Real-time qua Laravel Echo
    if (window.Echo && '{{ auth()->user()->apartment_id ?? "" }}') {
        window.Echo.private('apartment.{{ auth()->user()->apartment_id }}')
            .listen('VisitorStatusChanged', (e) => {
                const toast = document.createElement('div');
                toast.className = 'tk-alert tk-alert--success';
                toast.style.position = 'fixed';
                toast.style.bottom = '20px';
                toast.style.right = '20px';
                toast.style.zIndex = '9999';
                toast.style.padding = '12px 20px';
                toast.style.background = '#ecfdf5';
                toast.style.color = '#065f46';
                toast.style.borderRadius = '8px';
                toast.style.border = '1px solid #10b981';
                toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                toast.innerHTML = '<strong>' + e.visitor.guest_name + '</strong> vừa được cập nhật trạng thái.';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            });
    }
});
</script>
@endpush
