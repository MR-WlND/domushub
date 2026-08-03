@extends('layouts.admin.master')

@section('page_title', 'Quản lý Tiện ích')

@section('content')
<div class="am-page">

    {{-- Header --}}
    <div class="am-header">
        <div>
            <h1 class="am-title">Quản lý Tiện ích</h1>
            <p class="am-subtitle">Quản lý, giám sát và vận hành các khu vực tiện ích công cộng.</p>
        </div>
        <a href="{{ portal_route('amenities.create') }}" class="am-btn am-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Thêm tiện ích mới
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

    {{-- Filters --}}
    <form method="GET" action="{{ portal_route('amenities.index') }}" class="am-filter-bar">
        <div class="am-search-input">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tên tiện ích...">
        </div>
        <div class="am-filter-group">
            <select name="block_id" onchange="this.form.submit()">
                <option value="">Tất cả vị trí</option>
                @php
                    $blocks = \App\Models\Block::orderBy('name')->get();
                @endphp
                @foreach($blocks as $block)
                    <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="am-filter-group">
            <select name="status" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Đóng cửa</option>
            </select>
        </div>
    </form>

    {{-- Grid tiện ích --}}
    @php
        // Apply filters if needed since they are simple
        $query = $facilities;
        if (request('search')) {
            $query = $query->filter(function($item) {
                return false !== stristr($item->name, request('search'));
            });
        }
        if (request('block_id')) {
            $query = $query->where('block_id', request('block_id'));
        }
        if (request('status')) {
            $query = $query->where('status', request('status'));
        }
    @endphp

    @if($query->isEmpty())
    <div class="am-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <p>Không tìm thấy tiện ích nào phù hợp.</p>
        <a href="{{ portal_route('amenities.index') }}" class="am-btn" style="border: 1px solid #e2e8f0; margin-top:12px;">Xoá bộ lọc</a>
    </div>
    @else
    <div class="am-grid">
        @foreach($query as $facility)
        <div class="am-card {{ $facility->status == 'maintenance' ? 'am-card--maintenance' : '' }}">
            <div class="am-card-image-wrap">
                @if($facility->images && count($facility->images) > 0)
                    <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}" class="am-card-image">
                @else
                    <div class="am-card-no-image">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                @endif
                <span class="am-status-badge am-status--{{ $facility->status }}">
                    @if($facility->status == 'available')
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Đang hoạt động
                    @elseif($facility->status == 'maintenance')
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        Bảo trì
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        Đóng cửa
                    @endif
                </span>
            </div>

            <div class="am-card-content">
                <div class="am-card-header-inner">
                    <h3 class="am-card-name">{{ $facility->name }}</h3>
                </div>

                <div class="am-card-meta">
                    <div class="am-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $facility->floor?->name ? $facility->floor->name . ', ' : '' }}{{ $facility->block?->name ?: 'Chưa cập nhật' }}</span>
                    </div>
                    <div class="am-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>{{ $facility->operating_hours ?: 'Chưa cập nhật' }}</span>
                    </div>
                    @if($facility->status == 'maintenance')
                    <div class="am-meta-item am-meta-item--warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>Đang bảo trì</span>
                    </div>
                    @endif
                    <div class="am-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        <span>{{ $facility->price_label ?: 'Miễn phí' }}</span>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="am-card-actions">
                <a href="{{ portal_route('amenities.edit', $facility) }}" class="am-btn-action am-btn-action--outline">Sửa</a>
                <a href="{{ portal_route('amenities.show', $facility) }}" class="am-btn-action am-btn-action--primary">Xem lịch</a>
                
                <form method="POST" action="{{ portal_route('amenities.destroy', $facility) }}" class="am-action-delete-form" onsubmit="return confirm('Xóa tiện ích \'{{ addslashes($facility->name) }}\'?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="am-btn-action am-btn-action--danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

<style>
/* Base Variables */
:root {
    --primary: #1e3a8a; /* Dark blue matching design */
    --primary-hover: #1e40af;
    --bg-page: #f8fafc;
    --card-bg: #ffffff;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --border: #e2e8f0;
}

.am-page { max-width: 1200px; margin: 0 auto; padding: 24px 20px; font-family: 'Inter', sans-serif; }

/* Header */
.am-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.am-title { font-size: 28px; font-weight: 800; color: var(--primary); margin: 0 0 6px 0; }
.am-subtitle { font-size: 14px; color: var(--text-muted); margin: 0; }

/* Filter Bar */
.am-filter-bar { display: flex; gap: 12px; margin-bottom: 28px; }
.am-search-input { flex-grow: 1; position: relative; }
.am-search-input svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
.am-search-input input { width: 100%; padding: 10px 14px 10px 40px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s; box-sizing: border-box; }
.am-search-input input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.1); }

.am-filter-group select { padding: 10px 32px 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none; background: #fff; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%2364748b' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; min-width: 160px; }
.am-filter-group select:focus { border-color: #3b82f6; }

/* Alert */
.am-alert { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 20px; }
.am-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.am-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

/* Grid */
.am-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }

/* Card */
.am-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: box-shadow 0.2s; }
.am-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
.am-card--maintenance .am-card-image-wrap::after { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0.4); pointer-events: none; }

.am-card-image-wrap { position: relative; width: 100%; height: 180px; background: #f1f5f9; border-bottom: 1px solid var(--border); }
.am-card-image { width: 100%; height: 100%; object-fit: cover; }
.am-card-no-image { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; }
.am-status-badge { position: absolute; top: 12px; right: 12px; font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 20px; white-space: nowrap; display: flex; align-items: center; gap: 4px; z-index: 10; }
.am-status--available   { background: #16a34a; color: #fff; }
.am-status--maintenance { background: #fecdd3; color: #be123c; }
.am-status--closed      { background: #fca5a5; color: #991b1b; }

.am-card-content { padding: 20px; flex: 1; display: flex; flex-direction: column; }
.am-card-header-inner { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.am-card-name { font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0; line-height: 1.3; }

.am-badge-price { font-size: 12px; font-weight: 700; color: #1e3a8a; background: #dbeafe; padding: 4px 10px; border-radius: 6px; white-space: nowrap; }
.am-badge-price--free { background: #e0e7ff; color: #4338ca; } /* Custom light style for free if needed */

.am-card-meta { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }
.am-meta-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; }
.am-meta-item svg { color: #64748b; }
.am-meta-item--warning { color: #b91c1c; font-weight: 500; }
.am-meta-item--warning svg { color: #b91c1c; }

.am-card-actions { display: flex; gap: 10px; padding: 16px 20px; padding-top: 0; background: #fff; }
.am-btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; border: 1px solid var(--border); flex: 1; }
.am-btn-action--outline { background: white; color: var(--text-main); }
.am-btn-action--outline:hover { background: #f8fafc; border-color: #cbd5e1; }
.am-btn-action--primary { background: var(--primary); color: white; border-color: var(--primary); }
.am-btn-action--primary:hover { background: var(--primary-hover); border-color: var(--primary-hover); }
.am-action-delete-form { margin: 0; margin-left: auto; }
.am-btn-action--danger { background: white; color: #ef4444; border-color: #fecaca; padding: 8px 12px; flex: none; width: auto; }
.am-btn-action--danger:hover { background: #fef2f2; }

/* Buttons */
.am-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.2s; }
.am-btn--primary { background: var(--primary); color: #fff; }
.am-btn--primary:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 6px rgba(30, 58, 138, 0.2); }

/* Empty */
.am-empty { text-align: center; padding: 80px 20px; background: var(--card-bg); border-radius: 16px; border: 1px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; }
.am-empty p { margin-top: 16px; font-size: 15px; color: var(--text-muted); }

@media (max-width: 640px) {
    .am-grid { grid-template-columns: 1fr; }
    .am-filter-bar { flex-direction: column; }
}
</style>
@endsection
