@extends('layouts.resident.master')

@section('title', 'Đăng ký khách – DomusHub')

@push('styles')
    @vite(['resources/css/resident/visitors.css'])
@endpush

@section('content')
<div class="vq">

    {{-- HEADER --}}
    <div class="vq__header">
        <div>
            <p class="vq__eyebrow">Quản lý cư dân</p>
            <h1 class="vq__title">QR mời khách</h1>
        </div>
        <a href="{{ route('resident.visitors.create') }}" class="vq-btn vq-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tạo QR mới
        </a>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="vq-alert vq-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- EMPTY STATE --}}
    @if($visitors->isEmpty())
        <div class="vq-empty">
            <div class="vq-empty__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <h3 class="vq-empty__title">Chưa có QR khách nào</h3>
            <p class="vq-empty__desc">Tạo mã QR để mời khách vào tòa nhà một cách nhanh chóng và an toàn, không cần điền giấy tờ.</p>
            <a href="{{ route('resident.visitors.create') }}" class="vq-btn vq-btn--primary">
                Tạo QR mời khách ngay
            </a>
        </div>
    @else

        {{-- STATS --}}
        @php
            $pending    = $visitors->where('status', 'pending')->count();
            $checkedIn  = $visitors->where('status', 'checked_in')->count();
            $checkedOut = $visitors->where('status', 'checked_out')->count();
            $expired    = $visitors->whereIn('status', ['expired','cancelled'])->count();
        @endphp
        <div class="vq-stats">
            <div class="vq-stat stat--all">
                <div class="vq-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M7 7h10M7 12h10M7 17h10"/></svg>
                </div>
                <div><span class="vq-stat__num">{{ $visitors->count() }}</span><span class="vq-stat__label">Tổng</span></div>
            </div>
            <div class="vq-stat stat--pending">
                <div class="vq-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div><span class="vq-stat__num">{{ $pending }}</span><span class="vq-stat__label">Chờ vào</span></div>
            </div>
            <div class="vq-stat stat--active">
                <div class="vq-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div><span class="vq-stat__num">{{ $checkedIn }}</span><span class="vq-stat__label">Đang ở trong</span></div>
            </div>
            <div class="vq-stat stat--out">
                <div class="vq-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="15 9 9 12 15 15"/></svg>
                </div>
                <div><span class="vq-stat__num">{{ $checkedOut + $expired }}</span><span class="vq-stat__label">Đã ra / Hết hạn</span></div>
            </div>
        </div>

        {{-- GRID --}}
        <div class="vq-grid">
            @foreach($visitors as $visitor)
                <div class="vq-card vq-card--{{ $visitor->status }}">
                    <div class="vq-card__stripe"></div>
                    <div class="vq-card__body">
                        <h3 class="vq-card__name">{{ $visitor->guest_name }}</h3>
                        @if($visitor->guest_phone)
                            <p class="vq-card__phone">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.54 4.18 2 2 0 0 1 3.5 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.08 6.08l1.08-1.08a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                {{ $visitor->guest_phone }}
                            </p>
                        @endif
                        <div class="vq-card__meta">
                            <span class="vq-chip vq-chip--{{ $visitor->status }}">{{ $visitor->statusLabel() }}</span>
                            <span class="vq-chip" style="background:#f1f5f9;color:#475569;">
                                Hết hạn: {{ $visitor->expired_at->format('H:i d/m') }}
                            </span>
                        </div>
                        @if($visitor->note)
                            <p style="font-size:.82rem;color:#64748b;margin:0;">📝 {{ Str::limit($visitor->note, 60) }}</p>
                        @endif
                    </div>
                    <div class="vq-card__footer">
                        <span class="vq-card__time">Tạo {{ $visitor->created_at->diffForHumans() }}</span>
                        <div style="display:flex;gap:.5rem;">
                            @if(in_array($visitor->status, ['pending', 'checked_in']))
                                <a href="{{ route('resident.visitors.show', $visitor->id) }}" class="vq-btn vq-btn--sm vq-btn--outline">
                                    Xem QR
                                </a>
                                @if($visitor->status === 'pending')
                                    <form method="POST" action="{{ route('resident.visitors.destroy', $visitor->id) }}"
                                          onsubmit="return confirm('Hủy QR mời khách này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="vq-btn vq-btn--sm vq-btn--danger">Hủy</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('resident.visitors.show', $visitor->id) }}" class="vq-btn vq-btn--sm vq-btn--outline">Chi tiết</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @endif

</div>
@endsection
