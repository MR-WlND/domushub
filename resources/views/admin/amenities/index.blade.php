@extends('layouts.admin.master')

@section('page_title', 'Quản lý Tiện ích')

@push('styles')
<style>
.af-page { max-width:1100px; margin:0 auto; }

/* Header */
.af-page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.af-page-header__title { font-size:26px; font-weight:700; color:#00236f; font-family:'Inter', system-ui, -apple-system, sans-serif; letter-spacing:-0.02em; }
.af-page-header__sub { font-size:13.5px; color:#64748b; margin-top:4px; font-family:'Inter', system-ui, -apple-system, sans-serif; }
.af-btn { padding:10px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:.15s; display:inline-block; }
.af-btn--primary { background:#00236f; color:#fff; }
.af-btn--ghost { background:#f1f5f9; color:#475569; } .af-btn--ghost:hover { background:#e2e8f0; }
.af-btn--sm { padding:7px 14px; font-size:12px; }
.af-btn--danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; } .af-btn--danger:hover { background:#fee2e2; }

/* Alert */
.af-alert { padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px; font-weight:500; }
.af-alert--success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.af-alert--error { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }

/* Filters */
.af-filters { display:flex; gap:10px; margin-bottom:24px; flex-wrap:wrap; }
.af-search { flex:1; min-width:200px; padding:9px 14px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; color:#0f172a; }
.af-search:focus { outline:none; border-color:#0b57d0; box-shadow:0 0 0 3px rgba(11,87,208,.08); }
.af-search::placeholder { color:#94a3b8; }
.af-select { padding:9px 32px 9px 12px; border:1px solid #d1d5db; border-radius:7px; font-size:13px; background:#fff; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='none' stroke='%2364748b' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; min-width:140px; cursor:pointer; }
.af-select:focus { outline:none; border-color:#0b57d0; }

/* Stats */
.af-stats { display:flex; gap:12px; margin-bottom:20px; }
.af-stat { padding:8px 16px; border-radius:8px; font-size:13px; font-weight:500; color:#475569; background:#fff; border:1px solid #e2e8f0; }
.af-stat strong { font-size:18px; font-weight:700; color:#0b57d0; display:block; line-height:1.2; }

/* Grid */
.af-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:16px; }

/* Card */
.af-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s; }
.af-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.06); }
.af-card__img { width:100%; height:150px; object-fit:cover; background:#f1f5f9; display:block; cursor:pointer; }
.af-card__noimg { width:100%; height:150px; background:#f8fafc; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:13px; font-weight:500; }
.af-card__body { padding:16px; flex:1; }
.af-card__name { font-size:15px; font-weight:700; color:#0b1c30; margin-bottom:8px; }
.af-card__meta { font-size:12px; color:#64748b; line-height:1.8; }
.af-card__meta-row { display:flex; justify-content:space-between; }
.af-card__status { display:inline-block; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:600; }
.af-card__status--available { background:#dcfce7; color:#166534; }
.af-card__status--maintenance { background:#fef3c7; color:#92400e; }
.af-card__status--closed { background:#fee2e2; color:#991b1b; }
.af-card__actions { display:flex; gap:8px; padding:12px 16px; border-top:1px solid #f1f5f9; }
.af-card__actions a, .af-card__actions button { font-size:12px; font-weight:600; padding:6px 12px; border-radius:6px; border:none; cursor:pointer; text-decoration:none; transition:.15s; }
.af-card__actions .af-act-edit { background:#f1f5f9; color:#334155; } .af-card__actions .af-act-edit:hover { background:#e2e8f0; }
.af-card__actions .af-act-view { background:#eff6ff; color:#0b57d0; } .af-card__actions .af-act-view:hover { background:#dbeafe; }
.af-card__actions .af-act-del { background:#fff; color:#dc2626; border:1px solid #fecaca; margin-left:auto; } .af-card__actions .af-act-del:hover { background:#fef2f2; }

/* Empty */
.af-empty { text-align:center; padding:60px 20px; color:#94a3b8; }
.af-empty__text { font-size:14px; margin-bottom:12px; }
</style>
@endpush

@section('content')
<div class="af-page">

    {{-- Header --}}
    <div class="af-page-header">
        <div>
            <div class="af-page-header__title">Quản lý Tiện ích</div>
            <div class="af-page-header__sub">Giám sát và vận hành tiện ích công cộng</div>
        </div>
        <a href="{{ portal_route('amenities.create') }}" class="af-btn af-btn--primary">+ Thêm tiện ích</a>
    </div>

    {{-- Flash --}}
    @if(session('success'))<div class="af-alert af-alert--success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="af-alert af-alert--error">{{ session('error') }}</div>@endif

    {{-- Filters --}}
    <form method="GET" action="{{ portal_route('amenities.index') }}" class="af-filters">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên..." class="af-search">
        <select name="block_id" class="af-select" onchange="this.form.submit()">
            <option value="">Tất cả vị trí</option>
            @foreach(\App\Models\Block::orderBy('name')->get() as $block)
            <option value="{{ $block->id }}" {{ request('block_id')==$block->id?'selected':'' }}>{{ $block->name }}</option>
            @endforeach
        </select>
        <select name="status" class="af-select" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="available" {{ request('status')=='available'?'selected':'' }}>Hoạt động</option>
            <option value="maintenance" {{ request('status')=='maintenance'?'selected':'' }}>Bảo trì</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Đóng cửa</option>
        </select>
    </form>

    {{-- Grid --}}
    @php
        $allFacilities = $facilities;
        $query = $allFacilities;
        if(request('search')) $query = $query->filter(fn($i)=>stristr($i->name, request('search')));
        if(request('block_id')) $query = $query->where('block_id', request('block_id'));
        if(request('status')) $query = $query->where('status', request('status'));
    @endphp

    {{-- Grid --}}
    @if($query->isEmpty())
    <div class="af-empty">
        <div class="af-empty__text">Không tìm thấy tiện ích nào.</div>
        <a href="{{ portal_route('amenities.index') }}" class="af-btn af-btn--ghost af-btn--sm">Xóa bộ lọc</a>
    </div>
    @else
    <div class="af-grid">
        @foreach($query as $facility)
        <div class="af-card">
            <a href="{{ portal_route('amenities.show', $facility) }}" style="display:block;text-decoration:none;">
            @if($facility->images && count($facility->images) > 0)
                <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}" class="af-card__img">
            @else
                <div class="af-card__noimg">Chưa có ảnh</div>
            @endif
            </a>

            <div class="af-card__body">
                <a href="{{ portal_route('amenities.show', $facility) }}" class="af-card__name" style="text-decoration:none;color:#0b1c30;">{{ $facility->name }}</a>
                <div class="af-card__meta">
                    <div class="af-card__meta-row">
                        <span>Vị trí: {{ $facility->block?->name ?: '—' }}{{ $facility->floor ? ', '.$facility->floor->name : '' }}</span>
                        <span class="af-card__status af-card__status--{{ $facility->status }}">
                            {{ $facility->status=='available'?'Hoạt động':($facility->status=='maintenance'?'Bảo trì':'Đóng') }}
                        </span>
                    </div>
                    <div class="af-card__meta-row">
                        <span>Giờ: {{ $facility->open_time ? substr($facility->open_time,0,5).'–'.substr($facility->close_time,0,5) : '—' }}</span>
                        <span>Sức chứa: {{ $facility->capacity }} người</span>
                    </div>
                    <div class="af-card__meta-row">
                        <span>Phí: {{ $facility->fee_type=='free'?'Miễn phí':number_format($facility->price).'đ/'.(match($facility->fee_type){'per_hour'=>'giờ','per_use'=>'lượt','per_person'=>'người',default=>'lượt'}) }}</span>
                        <span>Đặt chỗ: {{ $facility->booking_type=='none'?'Không cần':($facility->booking_type=='time_slot'?'Khung giờ':'Thời gian') }}</span>
                    </div>
                </div>
            </div>

            <div class="af-card__actions">
                <a href="{{ portal_route('amenities.edit', $facility) }}" class="af-act-edit">Sửa</a>
                <a href="{{ portal_route('amenities.show', $facility) }}" class="af-act-view">Xem lịch</a>
                <form method="POST" action="{{ portal_route('amenities.destroy', $facility) }}" style="margin-left:auto;" onsubmit="return confirm('Xóa tiện ích \'{{ addslashes($facility->name) }}\'?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="af-act-del">Xóa</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
