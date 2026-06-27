@extends('layouts.admin.master')

@section('page_title', 'Quản lý tiện ích')

@section('content')
<div class="am-page">

    {{-- Header --}}
    <div class="am-header">
        <div>
            <p class="am-eyebrow">Dịch vụ cư dân</p>
            <h1 class="am-title">Quản lý tiện ích chung cư</h1>
        </div>
        <a href="{{ route('admin.amenities.create') }}" class="am-btn am-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Thêm tiện ích
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="am-alert am-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="am-alert am-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Thống kê nhanh --}}
    <div class="am-stats-row">
        <div class="am-stat-card">
            <div class="am-stat-icon am-stat-icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            </div>
            <div>
                <p class="am-stat-value">{{ $facilities->count() }}</p>
                <p class="am-stat-label">Tổng tiện ích</p>
            </div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-icon am-stat-icon--green">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <p class="am-stat-value">{{ $facilities->where('status','available')->count() }}</p>
                <p class="am-stat-label">Đang hoạt động</p>
            </div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-icon am-stat-icon--orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div>
                <p class="am-stat-value">{{ $facilities->where('status','maintenance')->count() }}</p>
                <p class="am-stat-label">Đang bảo trì</p>
            </div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-icon am-stat-icon--purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <p class="am-stat-value">{{ $facilities->sum('pending_bookings_count') }}</p>
                <p class="am-stat-label">Chờ duyệt</p>
            </div>
        </div>
    </div>

    {{-- Grid tiện ích --}}
    @if($facilities->isEmpty())
    <div class="am-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <p>Chưa có tiện ích nào.</p>
        <a href="{{ route('admin.amenities.create') }}" class="am-btn am-btn--primary" style="margin-top:12px;">Thêm tiện ích đầu tiên</a>
    </div>
    @else
    <div class="am-grid">
        @foreach($facilities as $facility)
        <div class="am-card">
            {{-- Top: icon + badge trạng thái --}}
            <div class="am-card-top">
                <div class="am-card-icon">
                    @php
                        $name = strtolower($facility->name);
                    @endphp
                    @if(str_contains($name, 'bơi') || str_contains($name, 'pool'))🏊
                    @elseif(str_contains($name, 'gym') || str_contains($name, 'tập'))💪
                    @elseif(str_contains($name, 'bbq') || str_contains($name, 'nướng'))🔥
                    @elseif(str_contains($name, 'tennis'))🎾
                    @elseif(str_contains($name, 'đọc') || str_contains($name, 'sách'))📚
                    @elseif(str_contains($name, 'sinh hoạt') || str_contains($name, 'hội'))🏛️
                    @else🏢
                    @endif
                </div>
                <span class="am-status-badge am-status--{{ $facility->status }}">
                    {{ $facility->status_label }}
                </span>
            </div>

            {{-- Tên + mô tả --}}
            <h3 class="am-card-name">{{ $facility->name }}</h3>
            <p class="am-card-desc">{{ $facility->description ?? 'Chưa có mô tả' }}</p>

            {{-- Meta: sức chứa + tổng đặt --}}
            <div class="am-card-meta">
                <div class="am-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Sức chứa: <strong>{{ $facility->capacity }} người</strong>
                </div>
                <div class="am-meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Tổng lịch đặt: <strong>{{ $facility->bookings_count }}</strong>
                </div>
            </div>

            {{-- Badge chờ duyệt --}}
            @if($facility->pending_bookings_count > 0)
            <a href="{{ route('admin.amenities.show', $facility) }}" class="am-pending-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $facility->pending_bookings_count }} lịch đặt chờ duyệt
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            @endif

            {{-- Actions --}}
            <div class="am-card-actions">
                <a href="{{ route('admin.amenities.show', $facility) }}" class="am-btn am-btn--sm am-btn--outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Lịch đặt
                </a>
                <a href="{{ route('admin.amenities.edit', $facility) }}" class="am-btn am-btn--sm am-btn--ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Sửa
                </a>
                <form method="POST" action="{{ route('admin.amenities.destroy', $facility) }}"
                      onsubmit="return confirm('Xóa tiện ích \'{{ addslashes($facility->name) }}\'?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="am-btn am-btn--sm am-btn--danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Xóa
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

<style>
.am-page { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

/* Header */
.am-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.am-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.am-title { font-size: 1.65rem; font-weight: 700; color: #0f172a; margin: 0; }

/* Alert */
.am-alert { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; margin-bottom: 20px; }
.am-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.am-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

/* Stats */
.am-stats-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; margin-bottom: 28px; }
.am-stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; }
.am-stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.am-stat-icon--blue   { background: #eff6ff; color: #2563eb; }
.am-stat-icon--green  { background: #f0fdf4; color: #16a34a; }
.am-stat-icon--orange { background: #fff7ed; color: #ea580c; }
.am-stat-icon--purple { background: #faf5ff; color: #7c3aed; }
.am-stat-value { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1; }
.am-stat-label { font-size: 0.78rem; color: #64748b; margin: 3px 0 0; font-weight: 500; }

/* Grid */
.am-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 18px; }

/* Card */
.am-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; display: flex; flex-direction: column; gap: 10px; transition: box-shadow 0.2s, transform 0.2s; }
.am-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }

.am-card-top { display: flex; justify-content: space-between; align-items: center; }
.am-card-icon { font-size: 2rem; line-height: 1; }

.am-status-badge { font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; white-space: nowrap; }
.am-status--available   { background: #dcfce7; color: #15803d; }
.am-status--maintenance { background: #fef9c3; color: #a16207; }
.am-status--closed      { background: #fee2e2; color: #b91c1c; }

.am-card-name { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0; }
.am-card-desc { font-size: 0.82rem; color: #64748b; margin: 0; line-height: 1.5; flex: 1; }

.am-card-meta { display: flex; flex-direction: column; gap: 5px; }
.am-meta-item { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #475569; }
.am-meta-item strong { color: #0f172a; font-weight: 600; }

.am-pending-badge { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; font-size: 0.75rem; font-weight: 600; padding: 7px 10px; border-radius: 8px; display: flex; align-items: center; gap: 6px; text-decoration: none; transition: background 0.15s; }
.am-pending-badge:hover { background: #fde68a; color: #78350f; }

.am-card-actions { display: flex; gap: 6px; flex-wrap: wrap; padding-top: 12px; border-top: 1px solid #f1f5f9; }

/* Buttons */
.am-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 7px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
.am-btn--primary { background: #2563eb; color: #fff; }
.am-btn--primary:hover { background: #1d4ed8; color: #fff; }
.am-btn--outline { background: #fff; color: #2563eb; border: 1px solid #bfdbfe; }
.am-btn--outline:hover { background: #eff6ff; }
.am-btn--ghost { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.am-btn--ghost:hover { background: #f1f5f9; }
.am-btn--danger { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.am-btn--danger:hover { background: #fecaca; }
.am-btn--sm { padding: 5px 10px; font-size: 0.78rem; }

/* Empty */
.am-empty { text-align: center; padding: 60px 20px; color: #94a3b8; display: flex; flex-direction: column; align-items: center; }
.am-empty p { margin-top: 14px; font-size: 0.95rem; color: #94a3b8; }

@media (max-width: 640px) {
    .am-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
