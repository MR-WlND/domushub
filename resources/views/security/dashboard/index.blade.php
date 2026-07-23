@extends('layouts.security.master')

@section('page_title', 'Bảng điều khiển — Bảo vệ')

@push('styles')
<style>
.sec-dash { font-family: 'Inter', system-ui, sans-serif; }
.sec-dash__header { margin-bottom: 2rem; }
.sec-dash__eyebrow { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #0b57d0; margin: 0 0 .3rem; }
.sec-dash__title { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0; }
.sec-dash__subtitle { font-size: .875rem; color: #64748b; margin: .25rem 0 0; }

/* Stats Grid */
.sec-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.sec-stat { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; transition: box-shadow .2s, transform .2s; }
.sec-stat:hover { box-shadow: 0 4px 12px rgba(0,35,111,.06); transform: translateY(-1px); }
.sec-stat__icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sec-stat__icon--blue { background: #eff6ff; color: #2563eb; }
.sec-stat__icon--green { background: #f0fdf4; color: #16a34a; }
.sec-stat__icon--amber { background: #fffbeb; color: #d97706; }
.sec-stat__icon--purple { background: #faf5ff; color: #7c3aed; }
.sec-stat__icon--rose { background: #fff1f2; color: #e11d48; }
.sec-stat__body { flex: 1; }
.sec-stat__num { font-size: 1.5rem; font-weight: 800; color: #0f172a; display: block; line-height: 1; }
.sec-stat__label { font-size: .75rem; font-weight: 600; color: #64748b; margin-top: .25rem; }

/* Quick Actions */
.sec-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.sec-action { display: flex; align-items: center; gap: .875rem; padding: 1.1rem 1.25rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: #1e293b; transition: all .2s; }
.sec-action:hover { border-color: #2563eb; background: #f8fbff; box-shadow: 0 2px 8px rgba(37,99,235,.08); transform: translateY(-1px); }
.sec-action__icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sec-action__icon--checkin { background: #dcfce7; color: #16a34a; }
.sec-action__icon--checkout { background: #fef3c7; color: #d97706; }
.sec-action__icon--visitor { background: #ede9fe; color: #7c3aed; }
.sec-action__text { font-size: .875rem; font-weight: 600; }
.sec-action__desc { font-size: .75rem; color: #64748b; margin-top: 2px; }

/* Recent Activity */
.sec-recent { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.sec-recent__header { padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
.sec-recent__title { font-size: .9rem; font-weight: 700; color: #1e293b; margin: 0; }
.sec-recent__badge { font-size: .7rem; font-weight: 700; background: #eff6ff; color: #2563eb; padding: 3px 8px; border-radius: 20px; }
.sec-recent__list { list-style: none; padding: 0; margin: 0; }
.sec-recent__item { display: flex; align-items: center; gap: .875rem; padding: .75rem 1.25rem; border-bottom: 1px solid #f8fafc; transition: background .15s; }
.sec-recent__item:last-child { border-bottom: none; }
.sec-recent__item:hover { background: #f8fafc; }
.sec-recent__dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.sec-recent__dot--in { background: #22c55e; }
.sec-recent__dot--out { background: #94a3b8; }
.sec-recent__plate { font-size: .85rem; font-weight: 700; color: #0f172a; min-width: 100px; }
.sec-recent__type { font-size: .75rem; color: #64748b; flex: 1; }
.sec-recent__time { font-size: .75rem; color: #94a3b8; }
.sec-recent__empty { padding: 2rem; text-align: center; color: #94a3b8; font-size: .85rem; }

@media (max-width: 768px) {
    .sec-stats { grid-template-columns: repeat(2, 1fr); }
    .sec-actions { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="sec-dash">

    {{-- Header --}}
    <div class="sec-dash__header">
        <p class="sec-dash__eyebrow">Bảng điều khiển</p>
        <h1 class="sec-dash__title">Xin chào, {{ auth()->user()->name ?? 'Bảo vệ' }}</h1>
        <p class="sec-dash__subtitle">{{ now()->format('l, d/m/Y') }} — Ca trực hiện tại</p>
    </div>

    {{-- Stats --}}
    <div class="sec-stats">
        <div class="sec-stat">
            <div class="sec-stat__icon sec-stat__icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="sec-stat__body">
                <span class="sec-stat__num">{{ $vehiclesInside }}</span>
                <span class="sec-stat__label">Xe trong bãi</span>
            </div>
        </div>

        <div class="sec-stat">
            <div class="sec-stat__icon sec-stat__icon--green">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="sec-stat__body">
                <span class="sec-stat__num">{{ $todayCheckins }}</span>
                <span class="sec-stat__label">Xe vào hôm nay</span>
            </div>
        </div>

        <div class="sec-stat">
            <div class="sec-stat__icon sec-stat__icon--amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div class="sec-stat__body">
                <span class="sec-stat__num">{{ $todayCheckouts }}</span>
                <span class="sec-stat__label">Xe ra hôm nay</span>
            </div>
        </div>

        <div class="sec-stat">
            <div class="sec-stat__icon sec-stat__icon--purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="sec-stat__body">
                <span class="sec-stat__num">{{ $visitorsInside }}</span>
                <span class="sec-stat__label">Khách đang ở</span>
            </div>
        </div>

        <div class="sec-stat">
            <div class="sec-stat__icon sec-stat__icon--rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="sec-stat__body">
                <span class="sec-stat__num">{{ $todayVisitors }}</span>
                <span class="sec-stat__label">Khách hôm nay</span>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="sec-actions">
        <a href="{{ route('security.vehicle-checkin.index') }}" class="sec-action">
            <div class="sec-action__icon sec-action__icon--checkin">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="sec-action__text">Quét xe vào</div>
                <div class="sec-action__desc">Scan QR cho phương tiện vào bãi</div>
            </div>
        </a>

        <a href="{{ route('security.vehicle-checkout.index') }}" class="sec-action">
            <div class="sec-action__icon sec-action__icon--checkout">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div>
                <div class="sec-action__text">Quét xe ra</div>
                <div class="sec-action__desc">Scan QR cho phương tiện ra khỏi bãi</div>
            </div>
        </a>

        <a href="{{ route('security.walk-in.index') }}" class="sec-action">
            <div class="sec-action__icon sec-action__icon--visitor">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="sec-action__text">Đăng ký khách</div>
                <div class="sec-action__desc">Đăng ký khách vãng lai tại cổng</div>
            </div>
        </a>
    </div>

    {{-- Recent Activity --}}
    <div class="sec-recent">
        <div class="sec-recent__header">
            <h3 class="sec-recent__title">Hoạt động gần đây</h3>
            <span class="sec-recent__badge">Hôm nay</span>
        </div>

        @if($recentLogs->isEmpty())
            <div class="sec-recent__empty">Chưa có hoạt động nào hôm nay.</div>
        @else
            <ul class="sec-recent__list">
                @foreach($recentLogs as $log)
                    <li class="sec-recent__item">
                        <div class="sec-recent__dot sec-recent__dot--{{ $log->status === 'inside' ? 'in' : 'out' }}"></div>
                        <span class="sec-recent__plate">{{ $log->vehicle?->license_plate ?? '—' }}</span>
                        <span class="sec-recent__type">
                            {{ $log->status === 'inside' ? 'Xe vào' : 'Xe ra' }}
                            · {{ $log->vehicle?->typeLabel() ?? '' }}
                        </span>
                        <span class="sec-recent__time">
                            {{ $log->status === 'inside' ? $log->check_in_at?->format('H:i') : $log->check_out_at?->format('H:i') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
@endsection
