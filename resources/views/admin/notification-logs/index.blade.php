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
                        <th>Nội dung thông báo</th>
                        <th>Nguồn</th>
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
                                <strong style="color:#0f172a;">{{ $log->data['title'] ?? 'Thông báo hệ thống' }}</strong>
                            </div>
                            <div style="font-size: 13px; color: #475569; margin-top: 4px;">
                                {!! $log->data['message'] ?? '' !!}
                            </div>
                            @if(!empty($log->data['url']) && $log->data['url'] !== '#')
                                <a href="{{ $log->data['url'] }}" data-notif-id="{{ $log->id }}" class="mark-read-and-redirect" style="font-size: 12px; color: #0b57d0; text-decoration: none; display: inline-block; margin-top: 6px;">Xem chi tiết &rarr;</a>
                            @endif
                        </td>
                        <td>
                            <span class="db-badge" style="color:#475569;background:#f1f5f9;">Hệ thống</span>
                        </td>
                        <td style="text-align:center;">
                            @if($log->read_at)
                                <span class="db-badge" style="color:#059669;background:#d1fae5;">Đã đọc</span>
                            @else
                                <span class="db-badge" style="color:#d97706;background:#fef3c7;">Chưa đọc</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="empty-row">Chưa có dữ liệu thông báo nào.</td></tr>
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
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('.mark-read-and-redirect');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const notifId = this.getAttribute('data-notif-id');
            const url = this.getAttribute('href');
            
            // Mark as read via AJAX
            fetch('{{ portal_route('notifications.mark-read', 'ID_HERE') }}'.replace('ID_HERE', notifId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                window.location.href = url;
            })
            .catch(err => {
                console.error('Error marking notification read:', err);
                window.location.href = url;
            });
        });
    });
});
</script>
@endpush
@endsection
