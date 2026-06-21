@extends('layouts.admin.master')

@section('page_title', 'Quản lý lịch đặt tiện ích')

@section('content')
<div class="amb-page">

    {{-- Header --}}
    <div class="amb-header">
        <div>
            <p class="amb-eyebrow">Tiện ích chung cư</p>
            <h1 class="amb-title">Quản lý lịch đặt</h1>
        </div>
        <a href="{{ route('admin.amenities.index') }}" class="amb-btn amb-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Danh sách tiện ích
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="amb-alert amb-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="amb-alert amb-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Filter --}}
    <form method="GET" class="amb-filter">
        <select name="facility_id" onchange="this.form.submit()">
            <option value="">Tất cả tiện ích</option>
            @foreach($facilities as $f)
                <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                    {{ $f->name }}
                </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Đã duyệt</option>
            <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Từ chối</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
        </select>
        @if(request('facility_id') || request('status'))
            <a href="{{ route('admin.amenities.bookings') }}" class="amb-btn amb-btn--ghost amb-btn--sm">Xóa bộ lọc</a>
        @endif
    </form>

    {{-- Bảng --}}
    <div class="amb-card">
        <div class="amb-card-header">
            <span>{{ $bookings->total() }} lịch đặt</span>
        </div>

        @if($bookings->isEmpty())
        <div class="amb-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p>Không có lịch đặt nào phù hợp</p>
        </div>
        @else
        <table class="amb-table">
            <thead>
                <tr>
                    <th>Cư dân</th>
                    <th>Tiện ích</th>
                    <th>Ngày đặt</th>
                    <th>Giờ sử dụng</th>
                    <th>Trạng thái</th>
                    <th>Đặt lúc</th>
                    <th style="width:130px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>
                        <div class="amb-user">
                            <div class="amb-avatar">{{ mb_substr($booking->user->name ?? 'N', 0, 1) }}</div>
                            <div>
                                <p class="amb-user-name">{{ $booking->user->name ?? '—' }}</p>
                                <p class="amb-user-email">{{ $booking->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.amenities.show', $booking->facility) }}" class="amb-facility-link">
                            {{ $booking->facility->name ?? '—' }}
                        </a>
                    </td>
                    <td>
                        <span class="amb-date">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                    </td>
                    <td>
                        <span class="amb-time">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</span>
                    </td>
                    <td>
                        <span class="amb-badge amb-badge--{{ $booking->status_class }}">
                            {{ $booking->status_label }}
                        </span>
                    </td>
                    <td class="amb-created">{{ $booking->created_at->format('d/m H:i') }}</td>
                    <td>
                        @if($booking->status === 'pending')
                        <div class="amb-row-actions">
                            <form method="POST" action="{{ route('admin.amenities.bookings.approve', $booking) }}">
                                @csrf
                                <button type="submit" class="amb-btn amb-btn--xs amb-btn--approve">Duyệt</button>
                            </form>
                            <form method="POST" action="{{ route('admin.amenities.bookings.reject', $booking) }}">
                                @csrf
                                <button type="submit" class="amb-btn amb-btn--xs amb-btn--reject" onclick="return confirm('Từ chối lịch đặt này?')">Từ chối</button>
                            </form>
                        </div>
                        @else
                            <span style="color:#cbd5e1">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($bookings->hasPages())
        <div class="amb-pagination">
            {{ $bookings->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

<style>
.amb-page { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }

.amb-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.amb-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.amb-title { font-size: 1.65rem; font-weight: 700; color: #0f172a; margin: 0; }

.amb-alert { padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; margin-bottom: 20px; }
.amb-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.amb-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

.amb-filter { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; align-items: center; }
.amb-filter select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; color: #374151; background: #fff; outline: none; cursor: pointer; }
.amb-filter select:focus { border-color: #3b82f6; }

.amb-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.amb-card-header { padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155; background: #f8fafc; }

.amb-table { width: 100%; border-collapse: collapse; }
.amb-table th { text-align: left; padding: 10px 14px; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.amb-table td { padding: 12px 14px; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.amb-table tr:last-child td { border-bottom: none; }
.amb-table tr:hover td { background: #fafbff; }

.amb-user { display: flex; align-items: center; gap: 10px; }
.amb-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0; }
.amb-user-name { font-weight: 600; color: #0f172a; margin: 0; font-size: 0.875rem; }
.amb-user-email { font-size: 0.75rem; color: #94a3b8; margin: 1px 0 0; }

.amb-facility-link { font-weight: 600; color: #2563eb; text-decoration: none; }
.amb-facility-link:hover { text-decoration: underline; }

.amb-date { font-weight: 600; color: #0f172a; }
.amb-time { font-size: 0.82rem; color: #475569; background: #f1f5f9; padding: 3px 8px; border-radius: 5px; font-family: monospace; }
.amb-created { font-size: 0.78rem; color: #94a3b8; }

.amb-badge { font-size: 0.72rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
.amb-badge--warning   { background: #fef9c3; color: #a16207; }
.amb-badge--success   { background: #dcfce7; color: #15803d; }
.amb-badge--danger    { background: #fee2e2; color: #b91c1c; }
.amb-badge--secondary { background: #f1f5f9; color: #64748b; }
.amb-badge--info      { background: #e0f2fe; color: #0369a1; }

.amb-row-actions { display: flex; gap: 5px; }

.amb-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; border-radius: 7px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
.amb-btn--outline { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.amb-btn--outline:hover { background: #f8fafc; }
.amb-btn--ghost { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.amb-btn--ghost:hover { background: #f1f5f9; }
.amb-btn--sm { padding: 6px 12px; font-size: 0.8rem; }
.amb-btn--xs { padding: 4px 10px; font-size: 0.75rem; }
.amb-btn--approve { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.amb-btn--approve:hover { background: #bbf7d0; }
.amb-btn--reject  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.amb-btn--reject:hover  { background: #fecaca; }

.amb-pagination { padding: 14px 18px; border-top: 1px solid #f1f5f9; }
.amb-empty { text-align: center; padding: 48px; color: #94a3b8; }
.amb-empty p { margin-top: 12px; font-size: 0.9rem; }
</style>
@endsection
