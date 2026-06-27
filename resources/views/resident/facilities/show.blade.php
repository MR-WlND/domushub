@extends('layouts.resident.master')

@section('title', $facility->name . ' – DomusHub')

@section('content')
<div class="rfd-page">

    {{-- Breadcrumb --}}
    <div class="rfd-breadcrumb">
        <a href="{{ route('resident.facilities.index') }}">Tiện ích chung cư</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>{{ $facility->name }}</span>
    </div>

    <div class="rfd-layout">

        {{-- LEFT: Main info --}}
        <div class="rfd-main">

            {{-- Hero image / icon --}}
            <div class="rfd-hero">
                @if($facility->images && count($facility->images) > 0)
                    @if(count($facility->images) > 1)
                    <div class="rfd-gallery">
                        <div class="rfd-gallery-main">
                            <img src="{{ asset('storage/' . $facility->images[0]) }}" id="mainImg" alt="{{ $facility->name }}">
                        </div>
                        <div class="rfd-gallery-thumbs">
                            @foreach($facility->images as $i => $img)
                            <img src="{{ asset('storage/' . $img) }}" alt="Ảnh {{ $i+1 }}"
                                onclick="document.getElementById('mainImg').src=this.src"
                                class="rfd-thumb {{ $i === 0 ? 'rfd-thumb--active' : '' }}">
                            @endforeach
                        </div>
                    </div>
                    @else
                    <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}" class="rfd-hero-img">
                    @endif
                @else
                <div class="rfd-hero-placeholder">
                    @php $name = strtolower($facility->name); @endphp
                    @if(str_contains($name,'bơi')||str_contains($name,'pool')) 🏊
                    @elseif(str_contains($name,'gym')||str_contains($name,'tập')) 💪
                    @elseif(str_contains($name,'bbq')||str_contains($name,'nướng')) 🔥
                    @elseif(str_contains($name,'tennis')) 🎾
                    @elseif(str_contains($name,'đọc')||str_contains($name,'sách')) 📚
                    @elseif(str_contains($name,'sinh hoạt')||str_contains($name,'hội')) 🏛️
                    @else 🏢 @endif
                </div>
                @endif
            </div>

            {{-- Tên + trạng thái --}}
            <div class="rfd-title-row">
                <h1 class="rfd-name">{{ $facility->name }}</h1>
                <span class="rfd-status-badge rfd-status--{{ $facility->status }}">
                    @if($facility->status === 'available')
                        <span class="rfd-pulse"></span> Đang mở cửa
                    @elseif($facility->status === 'maintenance')
                        🔧 Đang bảo trì
                    @else
                        🚫 Tạm ngưng
                    @endif
                </span>
            </div>

            @if($facility->description)
            <p class="rfd-desc">{{ $facility->description }}</p>
            @endif

            {{-- Nội quy --}}
            @if($facility->rules)
            <div class="rfd-rules">
                <div class="rfd-rules-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Nội quy & Lưu ý
                </div>
                <p class="rfd-rules-text">{{ $facility->rules }}</p>
            </div>
            @endif

            {{-- Khung giờ có thể đặt --}}
            @if(count($slots) > 0)
            <div class="rfd-slots-section">
                <div class="rfd-section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Khung giờ có thể đặt
                </div>
                <div class="rfd-slots">
                    @foreach($slots as $slot)
                    <div class="rfd-slot {{ $facility->status !== 'available' ? 'rfd-slot--disabled' : '' }}">
                        <span class="rfd-slot-time">{{ $slot['label'] }}</span>
                    </div>
                    @endforeach
                </div>
                @if($facility->status !== 'available')
                <p class="rfd-slot-note">⚠️ Tiện ích hiện không mở cửa, không thể đặt lịch.</p>
                @endif
            </div>
            @endif

        </div>

        {{-- RIGHT: Sidebar info --}}
        <aside class="rfd-sidebar">

            {{-- Thông tin nhanh --}}
            <div class="rfd-info-card">
                <h3 class="rfd-info-title">Thông tin tiện ích</h3>

                <div class="rfd-info-row">
                    <div class="rfd-info-icon" style="background:#eff6ff;color:#2563eb">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <p class="rfd-info-label">Giờ hoạt động</p>
                        <p class="rfd-info-value">{{ $facility->operating_hours }}</p>
                    </div>
                </div>

                <div class="rfd-info-row">
                    <div class="rfd-info-icon" style="background:#f0fdf4;color:#16a34a">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div>
                        <p class="rfd-info-label">Sức chứa tối đa</p>
                        <p class="rfd-info-value">{{ $facility->capacity }} người</p>
                    </div>
                </div>

                <div class="rfd-info-row">
                    <div class="rfd-info-icon" style="background:#faf5ff;color:#7c3aed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div>
                        <p class="rfd-info-label">Thời lượng mỗi lần</p>
                        @php
                            $dur = $facility->slot_duration ?? 60;
                            $durLabel = match((int)$dur){ 30=>'30 phút', 60=>'1 tiếng', 90=>'1.5 tiếng', 120=>'2 tiếng', default=>$dur.' phút'};
                        @endphp
                        <p class="rfd-info-value">{{ $durLabel }}</p>
                    </div>
                </div>

                <div class="rfd-info-row">
                    <div class="rfd-info-icon" style="background:#fff7ed;color:#ea580c">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <p class="rfd-info-label">Giá sử dụng</p>
                        <p class="rfd-info-value {{ (!$facility->price_per_slot || $facility->price_per_slot == 0) ? 'rfd-free' : 'rfd-paid' }}">
                            {{ $facility->price_label }}
                        </p>
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="rfd-status-block rfd-status-block--{{ $facility->status }}">
                    @if($facility->status === 'available')
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Tiện ích đang <strong>mở cửa</strong></span>
                    @elseif($facility->status === 'maintenance')
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        <span>Tiện ích đang <strong>bảo trì</strong></span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        <span>Tiện ích <strong>tạm ngưng</strong></span>
                    @endif
                </div>

                {{-- Nút đặt lịch --}}
                @if($facility->status === 'available')
                <a href="{{ route('resident.facilities.book', $facility) }}" class="rfd-book-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="16" y1="2" x2="16" y2="6"/></svg>
                    Đặt lịch ngay
                </a>
                @else
                <button class="rfd-book-btn rfd-book-btn--disabled" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    Không thể đặt lịch
                </button>
                @endif

                <a href="{{ route('resident.facility-bookings.index') }}" class="rfd-history-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h18v18H3z" fill="none"/><polyline points="3 9 12 3 21 9"/><line x1="9" y1="21" x2="9" y2="9"/><line x1="15" y1="21" x2="15" y2="9"/></svg>
                    Xem lịch đặt của tôi
                </a>
            </div>

        </aside>
    </div>
</div>

<style>
.rfd-page { max-width: 1060px; margin: 0 auto; padding: 28px 20px; }

.rfd-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; margin-bottom: 20px; }
.rfd-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.rfd-breadcrumb a:hover { text-decoration: underline; }

.rfd-layout { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }

/* Hero */
.rfd-hero { border-radius: 14px; overflow: hidden; margin-bottom: 20px; }
.rfd-hero-img { width: 100%; height: 280px; object-fit: cover; display: block; }
.rfd-hero-placeholder { height: 240px; background: linear-gradient(135deg, #e0f2fe, #ede9fe); display: flex; align-items: center; justify-content: center; font-size: 5rem; border-radius: 14px; }

.rfd-gallery-main img { width: 100%; height: 260px; object-fit: cover; border-radius: 10px; }
.rfd-gallery-thumbs { display: flex; gap: 8px; margin-top: 8px; }
.rfd-thumb { width: 72px; height: 56px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 2px solid transparent; transition: border-color 0.15s; }
.rfd-thumb:hover, .rfd-thumb--active { border-color: #2563eb; }

/* Title */
.rfd-title-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 10px; flex-wrap: wrap; }
.rfd-name { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0; }

.rfd-status-badge { display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; white-space: nowrap; }
.rfd-status--available { background: #dcfce7; color: #15803d; }
.rfd-status--maintenance { background: #fef9c3; color: #a16207; }
.rfd-status--closed { background: #fee2e2; color: #b91c1c; }
.rfd-pulse { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.6;transform:scale(1.3)} }

.rfd-desc { font-size: 0.9rem; color: #475569; line-height: 1.7; margin: 0 0 20px; }

/* Rules */
.rfd-rules { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; }
.rfd-rules-title { display: flex; align-items: center; gap: 7px; font-size: 0.82rem; font-weight: 700; color: #92400e; margin-bottom: 8px; }
.rfd-rules-text { font-size: 0.83rem; color: #78350f; line-height: 1.7; margin: 0; white-space: pre-line; }

/* Slots */
.rfd-slots-section { margin-bottom: 20px; }
.rfd-section-title { display: flex; align-items: center; gap: 7px; font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 10px; }
.rfd-slots { display: flex; flex-wrap: wrap; gap: 8px; }
.rfd-slot { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 7px 14px; }
.rfd-slot-time { font-size: 0.82rem; font-weight: 600; color: #2563eb; font-family: monospace; }
.rfd-slot--disabled { background: #f1f5f9; border-color: #e2e8f0; }
.rfd-slot--disabled .rfd-slot-time { color: #94a3b8; }
.rfd-slot-note { font-size: 0.8rem; color: #d97706; margin-top: 10px; font-weight: 500; }

/* Sidebar */
.rfd-sidebar { position: sticky; top: 20px; }
.rfd-info-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; display: flex; flex-direction: column; gap: 14px; }
.rfd-info-title { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0 0 4px; }
.rfd-info-row { display: flex; align-items: flex-start; gap: 12px; }
.rfd-info-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rfd-info-label { font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 2px; }
.rfd-info-value { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0; }
.rfd-free { color: #16a34a !important; }
.rfd-paid { color: #2563eb !important; }

.rfd-status-block { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; font-size: 0.85rem; margin-top: 4px; }
.rfd-status-block--available { background: #f0fdf4; color: #15803d; }
.rfd-status-block--maintenance { background: #fef9c3; color: #a16207; }
.rfd-status-block--closed { background: #fef2f2; color: #b91c1c; }

.rfd-book-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; text-decoration: none; transition: all 0.2s; margin-top: 8px; box-shadow: 0 4px 14px rgba(59,130,246,0.3); }
.rfd-book-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,130,246,0.4); color: #fff; }
.rfd-book-btn--disabled { background: #f1f5f9; color: #94a3b8; box-shadow: none; cursor: not-allowed; }
.rfd-book-btn--disabled:hover { transform: none; box-shadow: none; }

.rfd-history-link { display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 0.8rem; color: #64748b; text-decoration: none; margin-top: 10px; padding: 8px; border-radius: 8px; transition: all 0.15s; }
.rfd-history-link:hover { color: #2563eb; background: #eff6ff; }

@media (max-width: 768px) {
    .rfd-layout { grid-template-columns: 1fr; }
    .rfd-sidebar { position: static; }
}
</style>
@endsection
