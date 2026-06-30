@extends('layouts.admin.master')

@section('page_title', ($pageTitle ?? 'Lịch sử Tài chính') . ' – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Tài chính</h1>
            <p class="db-header__sub">Tra cứu toàn bộ hóa đơn và trạng thái thanh toán trong hệ thống.</p>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 24px;">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form">
            <div class="al-filter-group">
                <label class="al-filter-label">Mã / Tiêu đề HĐ</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="VD: BILL-00001...">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Trạng thái</label>
                <select name="status" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="paid"     {{ request('status') == 'paid'     ? 'selected' : '' }}>Thành công</option>
                    <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Chờ xác nhận</option>
                    <option value="failed"   {{ request('status') == 'failed'   ? 'selected' : '' }}>Thất bại</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Đã hoàn</option>
                </select>
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
                @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
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
                        <th>Thời gian TT</th>
                        <th>Mã biên lai (Mã HĐ)</th>
                        <th>Số tiền</th>
                        <th>Phương thức</th>
                        <th>Mã GD (Ngân hàng)</th>
                        <th>Người xác nhận</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;white-space:nowrap;">
                            {{ $log->paid_at ? $log->paid_at->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td>
                            <strong style="font-family:monospace;color:#0f172a;">
                                {{ $log->receipt_code }}
                            </strong>
                            <div style="font-size:11px;color:#64748b;">
                                (HĐ: {{ $log->invoice->invoice_code ?? '—' }})
                            </div>
                        </td>
                        <td style="font-weight:600;color:#059669;">
                            {{ number_format($log->amount) }} đ
                        </td>
                        <td>
                            @php
                                $methods = [
                                    'cash' => 'Tiền mặt',
                                    'bank_transfer' => 'Chuyển khoản',
                                    'vnpay' => 'VNPay',
                                ];
                                echo $methods[$log->payment_method ?? ''] ?? ($log->payment_method ?? '—');
                            @endphp
                        </td>
                        <td style="font-family:monospace;color:#475569;">
                            {{ $log->transaction_code ?? '—' }}
                        </td>
                        <td>{{ $log->recorder->name ?? 'Hệ thống' }}</td>
                        <td style="text-align:center;">
                            @php
                                $statuses = [
                                    'paid'      => ['Thành công',   '#059669', '#d1fae5'],
                                    'pending'   => ['Chờ xác nhận', '#d97706', '#fef3c7'],
                                    'failed'    => ['Thất bại',     '#dc2626', '#fee2e2'],
                                    'refunded'  => ['Đã hoàn',      '#4f46e5', '#e0e7ff'],
                                ];
                                [$sl, $sc, $sb] = $statuses[$log->status ?? ''] ?? ['Khác', '#475569', '#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{ $sc }};background:{{ $sb }};">{{ $sl }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-row">Chưa có dữ liệu giao dịch tài chính nào.</td></tr>
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
