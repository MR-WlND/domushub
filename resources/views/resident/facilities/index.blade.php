@extends('layouts.resident.master')

@push('styles')
<style>
.rf-page { max-width: 1100px; margin: 0 auto; padding: 32px 20px; }

.rf-header { margin-bottom: 24px; }
.rf-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 6px; font-weight: 600; }
.rf-title { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.rf-subtitle { font-size: 0.9rem; color: #64748b; margin: 0; }

.rf-alert { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; margin-bottom: 20px; }
.rf-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }

/* Tabs */
.rf-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 0; }
.rf-tab { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px 8px 0 0; font-size: 0.83rem; font-weight: 600; color: #64748b; text-decoration: none; transition: all 0.15s; border-bottom: 2px solid transparent; margin-bottom: -2px; }
.rf-tab:hover { color: #2563eb; background: #f8fafc; }
.rf-tab--active { color: #2563eb; border-bottom-color: #2563eb; background: #eff6ff; }
.rf-tab-icon { width: 16px; height: 16px; flex-shrink: 0; }

/* Grid */
.rf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

/* Card */
.rf-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; text-decoration: none; color: inherit; display: flex; flex-direction: column; transition: box-shadow 0.2s, transform 0.2s; position: relative; }
.rf-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.1); transform: translateY(-3px); }
.rf-card--maintenance { opacity: 0.85; }
.rf-card--closed { opacity: 0.7; filter: grayscale(20%); }

/* Ribbon */
.rf-ribbon { position: absolute; top: 12px; right: -8px; padding: 4px 14px 4px 10px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.04em; border-radius: 4px 0 0 4px; z-index: 2; }
.rf-ribbon--maintenance { background: #fef9c3; color: #a16207; }
.rf-ribbon--closed { background: #fee2e2; color: #b91c1c; }

/* Card image */
.rf-card-img { height: 160px; background: linear-gradient(135deg, #e0f2fe, #ede9fe); display: flex; align-items: center; justify-content: center; overflow: hidden; }
.rf-card-img img { width: 100%; height: 100%; object-fit: cover; }
.rf-card-icon-placeholder { display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; }
.rf-card-icon-placeholder svg { width: 4.2rem; height: 4.2rem; color: #6366f1; stroke-width: 1.5; opacity: 0.85; }

/* Card body */
.rf-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
.rf-card-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.rf-card-name { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }

/* Status dot */
.rf-status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.rf-dot--available { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
.rf-dot--maintenance { background: #eab308; box-shadow: 0 0 0 3px rgba(234,179,8,0.2); }
.rf-dot--closed { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }

.rf-card-desc { font-size: 0.82rem; color: #64748b; margin: 0; line-height: 1.5; }

/* Meta */
.rf-card-meta { display: flex; flex-direction: column; gap: 5px; flex: 1; }
.rf-meta-item { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #475569; }
.rf-meta-price { font-weight: 600; color: #2563eb; }
.rf-meta-price--free { color: #16a34a; }

/* Footer */
.rf-card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #f1f5f9; margin-top: 4px; }
.rf-status-badge { font-size: 0.7rem; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
.rf-badge--available { background: #dcfce7; color: #15803d; }
.rf-badge--maintenance { background: #fef9c3; color: #a16207; }
.rf-badge--closed { background: #fee2e2; color: #b91c1c; }
.rf-view-more { font-size: 0.78rem; font-weight: 600; color: #2563eb; }

.rf-empty { text-align: center; padding: 60px 20px; color: #94a3b8; display: flex; flex-direction: column; align-items: center; gap: 12px; }

@media (max-width: 640px) {
    .rf-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('title', 'Tiện ích chung cư – DomusHub')

@section('content')
<div class="rf-page">

    {{-- Header --}}
    <div class="rf-header">
        <div>
            <p class="rf-eyebrow">Dịch vụ cư dân</p>
            <h1 class="rf-title">Tiện ích chung cư</h1>
            <p class="rf-subtitle">Khám phá các tiện ích và đặt lịch sử dụng tại chung cư.</p>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="rf-alert rf-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter tabs --}}
    <div class="rf-tabs">
        <a href="{{ route('resident.facilities.index') }}"
            class="rf-tab {{ !request('status') ? 'rf-tab--active' : '' }}">
            <svg class="rf-tab-icon rf-icon-all" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" />
                <path d="M3 9h18" />
                <path d="M3 15h18" />
                <path d="M9 3v18" />
                <path d="M15 3v18" />
            </svg>
            Tất cả ({{ $facilities->count() }})
        </a>
        <a href="{{ route('resident.facilities.index', ['status' => 'available']) }}"
            class="rf-tab {{ request('status') === 'available' ? 'rf-tab--active' : '' }}">
            <svg class="rf-tab-icon rf-icon-available" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            Đang mở ({{ $facilities->where('status','available')->count() }})
        </a>
        <a href="{{ route('resident.facilities.index', ['status' => 'maintenance']) }}"
            class="rf-tab {{ request('status') === 'maintenance' ? 'rf-tab--active' : '' }}">
            <svg class="rf-tab-icon rf-icon-maintenance" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
            </svg>
            Bảo trì ({{ $facilities->where('status','maintenance')->count() }})
        </a>
        <a href="{{ route('resident.facilities.index', ['status' => 'closed']) }}"
            class="rf-tab {{ request('status') === 'closed' ? 'rf-tab--active' : '' }}">
            <svg class="rf-tab-icon rf-icon-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
            </svg>
            Tạm ngưng ({{ $facilities->where('status','closed')->count() }})
        </a>
    </div>

    {{-- Filter by status --}}
    @php
        $filtered = request('status')
            ? $facilities->where('status', request('status'))
            : $facilities;
    @endphp

    {{-- Grid --}}
    @if($filtered->isEmpty())
    <div class="rf-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
        <p>Không có tiện ích nào.</p>
    </div>
    @else
    <div class="rf-grid">
        @foreach($filtered as $facility)
        <a href="{{ route('resident.facilities.show', $facility) }}" class="rf-card rf-card--{{ $facility->status }}">
            {{-- Status ribbon --}}
            @if($facility->status === 'maintenance')
            <div class="rf-ribbon rf-ribbon--maintenance">Bảo trì</div>
            @elseif($facility->status === 'closed')
            <div class="rf-ribbon rf-ribbon--closed">Tạm ngưng</div>
            @endif

            {{-- Ảnh / icon placeholder --}}
            <div class="rf-card-img">
                @if($facility->images && count($facility->images) > 0)
                    <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}">
                @else
                    <div class="rf-card-icon-placeholder">
                        @include('partials.facility-placeholder', ['name' => $facility->name])
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="rf-card-body">
                <div class="rf-card-top">
                    <h3 class="rf-card-name">{{ $facility->name }}</h3>
                    <span class="rf-status-dot rf-dot--{{ $facility->status }}"></span>
                </div>

                @if($facility->description)
                <p class="rf-card-desc">{{ Str::limit($facility->description, 70) }}</p>
                @endif

                <div class="rf-card-meta">
                    @if($facility->open_time && $facility->close_time)
                    <span class="rf-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ $facility->operating_hours }}
                    </span>
                    @endif
                    <span class="rf-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        {{ $facility->capacity }} người
                    </span>
                    <span class="rf-meta-item rf-meta-price {{ (!$facility->price_per_slot || $facility->price_per_slot == 0) ? 'rf-meta-price--free' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        {{ $facility->price_label }}
                    </span>
                </div>

                <div class="rf-card-footer">
                    <span class="rf-status-badge rf-badge--{{ $facility->status }}">
                        @if($facility->status === 'available') Đang mở
                        @elseif($facility->status === 'maintenance') Bảo trì
                        @else Tạm ngưng
                        @endif
                    </span>
                    <span class="rf-view-more">Xem chi tiết →</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>

@endsection
