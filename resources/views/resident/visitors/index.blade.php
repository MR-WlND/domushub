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
    @media(max-width:500px){ .vr-stats { grid-template-columns:1fr 1fr; } }
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
    .vr-card { background:#fff; border:1px solid #e8ecf4; border-radius:14px; overflow:hidden; box-shadow:0 1px 5px rgba(0,20,80,.04); transition:box-shadow .15s; display:flex; align-items:stretch; }
    .vr-card:hover { box-shadow:0 4px 14px rgba(0,20,80,.09); }


    .vr-card__body { display:flex; align-items:center; gap:1rem; padding:.85rem 1rem; flex:1; min-width:0; }
    .vr-card__avatar { width:46px; height:46px; border-radius:10px; overflow:hidden; border:2px solid #e2e8f0; flex-shrink:0; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.05rem; color:#64748b; }
    .vr-card__avatar img { width:100%; height:100%; object-fit:cover; }
    .vr-card__info { flex:1; min-width:0; }
    .vr-card__name { font-size:.9rem; font-weight:800; color:#0f172a; margin:0 0 .15rem; }
    .vr-card__meta { font-size:.77rem; color:#64748b; display:flex; flex-wrap:wrap; align-items:center; gap:.4rem; }
    .vr-card__apt  { font-weight:700; color:#1e293b; }

    /* badges */
    .vr-chip { display:inline-flex; align-items:center; gap:.25rem; font-size:.7rem; font-weight:700; padding:.2rem .6rem; border-radius:999px; }
    .vr-chip--checked_in  { background:#a7f3d0; color:#047857; }
    .vr-chip--checked_out { background:#f1f5f9; color:#475569; }
    .vr-chip--pending     { background:#dbeafe; color:#1e40af; }
    .vr-chip--cancelled   { background:#fee2e2; color:#991b1b; }
    .vr-chip--expired     { background:#ffedd5; color:#9a3412; }

    /* right action */
    .vr-card__actions { display:flex; align-items:center; gap:.5rem; padding-right:.85rem; flex-shrink:0; }
    .vr-btn { display:inline-flex; align-items:center; gap:.35rem; padding:.45rem .85rem; border-radius:8px; font-size:.8rem; font-weight:700; font-family:inherit; cursor:pointer; border:1.5px solid; text-decoration:none; transition:all .15s; }
    .vr-btn--outline { background:#fff; color:#64748b; border-color:#e2e8f0; }
    .vr-btn--outline:hover { background:#f8fafc; }

    /* empty */
    .vr-empty { background:#fff; border:1px solid #e8ecf4; border-radius:14px; padding:3.5rem 1.5rem; text-align:center; }
    .vr-empty svg { opacity:.3; margin-bottom:.75rem; }
    .vr-empty h3 { font-size:1rem; font-weight:700; color:#1e293b; margin:0 0 .4rem; }
    .vr-empty p  { font-size:.85rem; color:#64748b; margin:0; }

    /* New notification dot */
    .vr-card__new { width:8px; height:8px; border-radius:50%; background:#00236f; flex-shrink:0; }
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
                        {{-- Avatar / photo --}}
                        <div class="vr-card__avatar">
                            @if($v->face_image)
                                <img src="{{ asset('storage/'.$v->face_image) }}" alt="{{ $v->guest_name }}">
                            @else
                                {{ mb_strtoupper(mb_substr($v->guest_name,0,1)) }}
                            @endif
                        </div>
                        <div class="vr-card__info">
                            <p class="vr-card__name">{{ $v->guest_name }}</p>
                            <div class="vr-card__meta">
                                @if($v->guest_phone)
                                    <span>{{ $v->guest_phone }}</span>
                                    <span>&middot;</span>
                                @endif
                                <span class="vr-card__apt">
                                    {{ $v->apartment?->apartment_number ?? '' }}
                                    @if($v->apartment?->floor?->block)
                                        - {{ $v->apartment->floor->block->name }}
                                    @endif
                                </span>
                                <span>&middot;</span>
                                <span>{{ $v->created_at->format('H:i d/m/Y') }}</span>
                            </div>
                            <div style="margin-top:.35rem;display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;">
                                <span class="vr-chip vr-chip--{{ $v->status }}">{{ $v->statusLabel() }}</span>
                                @if($v->check_in_at)
                                    <span style="font-size:.72rem;color:#64748b;">Vào {{ $v->check_in_at->format('H:i') }}</span>
                                @endif
                                @if($v->check_out_at)
                                    <span style="font-size:.72rem;color:#64748b;">· Ra {{ $v->check_out_at->format('H:i') }}</span>
                                @endif
                                @if($v->confirmed_by_resident)
                                    <span style="font-size:.7rem;background:#a7f3d0;color:#047857;padding:.15rem .5rem;border-radius:999px;font-weight:700;">✓ Đã xác nhận</span>
                                @endif
                            </div>
                        </div>
                        @if($isNew)
                            <div class="vr-card__new" title="Khách mới"></div>
                        @endif
                    </div>
                    <div class="vr-card__actions">
                        <a href="{{ route('resident.visitors.show', $v->id) }}" class="vr-btn vr-btn--outline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Chi tiết
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>
@endsection
