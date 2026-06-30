@extends('layouts.admin.master')

@section('page_title', ($pageTitle ?? 'Lịch sử Thông báo') . ' – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Thông báo</h1>
            <p class="db-header__sub">Tra cứu toàn bộ thông báo đã được gửi đến cư dân trong hệ thống.</p>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 24px;">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form">
            <div class="al-filter-group">
                <label class="al-filter-label">Tiêu đề</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="Tìm theo tiêu đề thông báo...">
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
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Người gửi</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;white-space:nowrap;">
                            {{ $log->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($log->pinned)
                                    <span title="Đã ghim" style="color:#eab308;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/></svg>
                                    </span>
                                @endif
                                <strong style="color:#0f172a;">{{ $log->title }}</strong>
                            </div>
                        </td>
                        <td>
                            @php
                                $cats = [
                                    'general'       => ['Chung','#475569','#f1f5f9'],
                                    'maintenance'   => ['Bảo trì','#d97706','#fef3c7'],
                                    'event'         => ['Sự kiện','#4f46e5','#e0e7ff'],
                                    'security'      => ['An ninh','#dc2626','#fee2e2'],
                                    'finance'       => ['Tài chính','#059669','#d1fae5'],
                                    'emergency'     => ['Khẩn cấp','#b91c1c','#ffe4e6'],
                                ];
                                [$cl, $cc, $cb] = $cats[$log->category ?? 'general'] ?? ['Khác','#475569','#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{ $cc }};background:{{ $cb }};">{{ $cl }}</span>
                        </td>
                        <td>{{ $log->user->name ?? '—' }}</td>
                        <td style="text-align:center;">
                            @php
                                $statuses = [
                                    'published' => ['Đã gửi','#059669','#d1fae5'],
                                    'draft'     => ['Bản nháp','#d97706','#fef3c7'],
                                    'archived'  => ['Lưu trữ','#475569','#f1f5f9'],
                                ];
                                [$sl, $sc, $sb] = $statuses[$log->status ?? ''] ?? ['Khác','#475569','#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{ $sc }};background:{{ $sb }};">{{ $sl }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-row">Chưa có dữ liệu thông báo nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="al-pagination">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

@push('styles')
    @vite(['resources/css/pages/admin/statistics.css', 'resources/css/pages/admin/dashboard.css', 'resources/css/pages/admin/activity-logs.css'])
@endpush
@endsection
