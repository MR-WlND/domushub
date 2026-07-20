@extends('layouts.admin.master')

@section('page_title', ($pageTitle ?? 'Lịch sử Quản trị Hệ thống') . ' – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Quản trị Hệ thống</h1>
            <p class="db-header__sub">Tra cứu và kiểm soát các hoạt động quản trị quan trọng, bảo mật của hệ thống.</p>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 24px;">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form">
            <div class="al-filter-group">
                <label class="al-filter-label">Từ khóa</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="Tìm thao tác hoặc đối tượng...">
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
                        <th>Thời gian</th>
                        <th>Người thực hiện</th>
                        <th>Thao tác thực hiện</th>
                        <th>Đối tượng bị thay đổi</th>
                        <th>IP Address</th>
                        <th>User Agent</th>
                        <th style="text-align:center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td><strong>{{ $log->causer->name ?? 'Hệ thống' }}</strong></td>
                        <td style="font-weight: 500; color:#0b57d0;">{{ $log->description }}</td>
                        <td>{{ $log->properties['target'] ?? '—' }}</td>
                        <td style="color:#475569; font-size:13px;">{{ $log->properties['ip_address'] ?? '—' }}</td>
                        <td style="color:#64748b; font-size:12px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $log->properties['user_agent'] ?? '' }}">
                            {{ $log->properties['user_agent'] ?? '—' }}
                        </td>
                        <td style="text-align:center;">
                            @if($log->properties && count($log->properties) > 0)
                                <a href="{{ portal_route('system-logs.show', $log->id) }}" class="al-btn-detail" style="text-decoration:none; display:inline-block; line-height: normal; padding: 4px 8px;">Xem chi tiết</a>
                            @else
                                <span style="color:#94a3b8;font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-row">Chưa có dữ liệu hệ thống nào.</td></tr>
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
