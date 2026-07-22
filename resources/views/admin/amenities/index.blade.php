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
        <a href="{{ portal_route('amenities.create') }}" class="am-btn am-btn--primary">
            Thêm tiện ích
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="am-alert am-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="am-alert am-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Thống kê nhanh --}}
    <div class="am-stats-row">
        <div class="am-stat-card">
            <div>
                <p class="am-stat-value">{{ $facilities->count() }}</p>
                <p class="am-stat-label">Tổng tiện ích</p>
            </div>
        </div>
        <div class="am-stat-card">
            <div>
                <p class="am-stat-value">{{ $facilities->where('status','available')->count() }}</p>
                <p class="am-stat-label">Đang hoạt động</p>
            </div>
        </div>
        <div class="am-stat-card">
            <div>
                <p class="am-stat-value">{{ $facilities->where('status','maintenance')->count() }}</p>
                <p class="am-stat-label">Đang bảo trì</p>
            </div>
        </div>
        <div class="am-stat-card">
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
        <a href="{{ portal_route('amenities.create') }}" class="am-btn am-btn--primary" style="margin-top:12px;">Thêm tiện ích đầu tiên</a>
    </div>
    @else
    <div class="am-grid">
        @foreach($facilities as $facility)
        <div class="am-card">
            {{-- Top: badge trạng thái --}}
            <div class="am-card-top">
                <span class="am-status-badge am-status--{{ $facility->status }}">
                    {{ $facility->status_label }}
                </span>
            </div>

            {{-- Tên + mô tả --}}
            <h3 class="am-card-name">{{ $facility->name }}</h3>
            <p class="am-card-desc">{{ $facility->description ?? 'Chưa có mô tả' }}</p>

            {{-- Meta: sức chứa + tổng đặt --}}
            <div class="am-card-meta">
                <div class="am-meta-item">Sức chứa: <strong>{{ $facility->capacity }} người</strong></div>
                <div class="am-meta-item">Tổng lịch đặt: <strong>{{ $facility->bookings_count }}</strong></div>
            </div>

            {{-- Badge chờ duyệt --}}
            @if($facility->pending_bookings_count > 0)
            <a href="{{ portal_route('amenities.show', $facility) }}" class="am-pending-badge">
                {{ $facility->pending_bookings_count }} lịch đặt chờ duyệt
            </a>
            @endif

            {{-- Actions --}}
            <div class="am-card-actions">
                <a href="{{ portal_route('amenities.show', $facility) }}" class="am-btn am-btn--sm am-btn--outline">Lịch đặt</a>
                <a href="{{ portal_route('amenities.edit', $facility) }}" class="am-btn am-btn--sm am-btn--ghost">Sửa</a>
                <form method="POST" action="{{ portal_route('amenities.destroy', $facility) }}"
                      onsubmit="return confirm('Xóa tiện ích \'{{ addslashes($facility->name) }}\'?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="am-btn am-btn--sm am-btn--danger">Xóa</button>
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
.am-card-icon { display: flex; align-items: center; justify-content: center; }
.am-card-icon svg { width: 2.2rem; height: 2.2rem; color: #6366f1; stroke-width: 1.8; opacity: 0.85; }

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
