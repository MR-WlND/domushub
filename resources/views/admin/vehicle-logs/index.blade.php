@extends(auth()->user()->role === 'security' ? 'layouts.security.master' : 'layouts.admin.master')

@section('page_title', 'Lịch sử xe ra vào – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Xe ra vào</h1>
            <p class="db-header__sub">Tra cứu lịch sử check-in, check-out của các phương tiện trong khu dân cư.</p>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 24px;">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form">
            <div class="al-filter-group">
                <label class="al-filter-label">Từ khóa</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="Biển số, số căn hộ...">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="al-filter-input">
            </div>
            
            <div class="al-filter-group">
                <label class="al-filter-label">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="al-filter-input">
            </div>
            
            <div class="al-filter-actions">
                <button type="submit" class="al-btn-filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Lọc
                </button>
                @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                <a href="{{ request()->url() }}" class="al-btn-reset">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===================== DATA TABLE ===================== --}}
    <div class="table-card">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Biển số xe</th>
                        <th>Loại xe</th>
                        <th>Căn hộ</th>
                        <th>Thời gian vào</th>
                        <th>Thời gian ra</th>
                        <th>Bảo vệ xử lý</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <strong style="color: #0b1c30; font-family: monospace;">{{ $log->vehicle->license_plate ?? '—' }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 13px; color: #475569;">
                                @if($log->vehicle)
                                    {{ $log->vehicle->type === 'car' ? 'Ô tô' : ($log->vehicle->type === 'motorbike' ? 'Xe máy' : 'Xe điện') }}
                                @else
                                    —
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($log->vehicle && $log->vehicle->apartment)
                                <div style="font-weight: 500; color: #00236f;">{{ $log->vehicle->apartment->apartment_number }}</div>
                                <div style="font-size: 11px; color: #64748b;">
                                    Tầng {{ $log->vehicle->apartment->floor->floor_number ?? '' }} - Block {{ $log->vehicle->apartment->floor->block->name ?? '' }}
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #0b1c30;">{{ $log->check_in_at ? $log->check_in_at->format('H:i:s') : '—' }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $log->check_in_at ? $log->check_in_at->format('d/m/Y') : '—' }}</div>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #0b1c30;">{{ $log->check_out_at ? $log->check_out_at->format('H:i:s') : '—' }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $log->check_out_at ? $log->check_out_at->format('d/m/Y') : '—' }}</div>
                        </td>
                        <td>
                            <div style="font-size: 12px; color: #475569;">
                                <div>Vào: {{ $log->checkedInBy->name ?? '—' }}</div>
                                <div>Ra: {{ $log->checkedOutBy->name ?? '—' }}</div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @if($log->isInside())
                                <span class="db-badge" style="background:#e0f2fe; color:#0369a1;">Đang trong bãi</span>
                            @else
                                <span class="db-badge" style="background:#f1f5f9; color:#475569;">Đã ra</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-row">Chưa có dữ liệu ra vào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="al-pagination">
            {{ $logs->links('admin.users.pagination') }}
        </div>
    </div>

</div>

@push('styles')
    @vite(['resources/css/pages/admin/statistics.css', 'resources/css/pages/admin/dashboard.css', 'resources/css/pages/admin/activity-logs.css'])
@endpush
@endsection
