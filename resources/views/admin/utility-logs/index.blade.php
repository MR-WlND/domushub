@extends('layouts.admin.master')

@section('page_title', 'Lịch sử Ghi số Điện Nước – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Ghi số Điện Nước</h1>
            <p class="db-header__sub">Tra cứu toàn bộ lịch sử ghi nhận chỉ số điện và nước trong hệ thống.</p>
        </div>
        <div>
            <a href="{{ route('admin.utility-readings.index') }}" class="al-btn-filter" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
                Chốt số tháng này
            </a>
        </div>
    </div>

    {{-- ===================== STAT CARDS ===================== --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:14px; margin-top:24px;">
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #00236f;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Tổng bản ghi</div>
            <div style="font-size:26px; font-weight:800; color:#00236f; margin-top:6px;">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #f59e0b;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Ghi điện</div>
            <div style="font-size:26px; font-weight:800; color:#b45309; margin-top:6px;">{{ number_format($stats['elec']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #3b82f6;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Ghi nước</div>
            <div style="font-size:26px; font-weight:800; color:#1d4ed8; margin-top:6px;">{{ number_format($stats['water']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #10b981;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Đã duyệt</div>
            <div style="font-size:26px; font-weight:800; color:#059669; margin-top:6px;">{{ number_format($stats['approved']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #f97316;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Chờ duyệt</div>
            <div style="font-size:26px; font-weight:800; color:#c2410c; margin-top:6px;">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #ef4444;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Từ chối</div>
            <div style="font-size:26px; font-weight:800; color:#dc2626; margin-top:6px;">{{ number_format($stats['rejected']) }}</div>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 20px;">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form" style="flex-wrap:wrap;">
            <div class="al-filter-group">
                <label class="al-filter-label">Số căn hộ</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="VD: 101, 202...">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Loại</label>
                <select name="type" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="electricity" {{ request('type') == 'electricity' ? 'selected' : '' }}>⚡ Điện</option>
                    <option value="water" {{ request('type') == 'water' ? 'selected' : '' }}>💧 Nước</option>
                </select>
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Trạng thái</label>
                <select name="status" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>✅ Đã duyệt</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>⏳ Chờ duyệt</option>
                    <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>❌ Bị từ chối</option>
                </select>
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Tháng</label>
                <input type="number" name="month" value="{{ request('month') }}" min="1" max="12"
                       class="al-filter-input" placeholder="1–12" style="width:80px;">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Năm</label>
                <input type="number" name="year" value="{{ request('year', date('Y')) }}" min="2020" max="2099"
                       class="al-filter-input" placeholder="{{ date('Y') }}" style="width:90px;">
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
                @if(request()->anyFilled(['search', 'type', 'status', 'month', 'year', 'date_from', 'date_to']))
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
                        <th>Kỳ ghi</th>
                        <th>Căn hộ</th>
                        <th>Tòa / Tầng</th>
                        <th>Loại</th>
                        <th>Chỉ số cũ</th>
                        <th>Chỉ số mới</th>
                        <th>Tiêu thụ</th>
                        <th>Người ghi</th>
                        <th>Ngày ghi</th>
                        <th style="text-align:center">Trạng thái</th>
                        <th style="text-align:center">Ảnh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap; font-weight:600; color:#00236f;">
                            T{{ $log->record_month }}/{{ $log->record_year }}
                        </td>
                        <td>
                            <strong style="color:#0b1c30;">{{ $log->apartment->apartment_number ?? '—' }}</strong>
                        </td>
                        <td style="font-size:12px; color:#64748b;">
                            {{ $log->apartment->floor->block->name ?? '—' }} /
                            Tầng {{ $log->apartment->floor->floor_number ?? '—' }}
                        </td>
                        <td>
                            @if($log->type === 'electricity')
                                <span class="db-badge" style="background:#fef3c7; color:#b45309; font-size:11px;">⚡ Điện</span>
                            @else
                                <span class="db-badge" style="background:#dbeafe; color:#1d4ed8; font-size:11px;">💧 Nước</span>
                            @endif
                        </td>
                        <td style="color:#64748b; font-size:13px;">{{ number_format($log->old_value) }}</td>
                        <td style="font-weight:700; color:#0b1c30;">{{ number_format($log->new_value) }}</td>
                        <td>
                            <strong style="color:#059669;">{{ number_format($log->usage_amount) }}</strong>
                            <small style="color:#94a3b8;">{{ $log->type === 'electricity' ? 'kWh' : 'm³' }}</small>
                        </td>
                        <td style="font-size:12px; color:#475569;">{{ $log->recorder->name ?? '—' }}</td>
                        <td style="font-size:11px; color:#64748b; white-space:nowrap;">
                            <div>{{ $log->created_at->format('d/m/Y') }}</div>
                            <div>{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td style="text-align:center;">
                            @if($log->status === 'approved')
                                <span class="db-badge" style="background:#dcfce7; color:#15803d; font-size:11px;">Đã duyệt</span>
                            @elseif($log->status === 'rejected')
                                <span class="db-badge" style="background:#fee2e2; color:#dc2626; font-size:11px;" title="{{ $log->reject_reason }}">
                                    Từ chối
                                </span>
                            @else
                                <span class="db-badge" style="background:#fef3c7; color:#b45309; font-size:11px;">Chờ duyệt</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @php
                                $imgs = [];
                                if ($log->image_proof) $imgs[] = asset('storage/' . $log->image_proof);
                                if ($log->images) {
                                    foreach ($log->images as $img) $imgs[] = asset('storage/' . $img);
                                }
                            @endphp
                            @if(count($imgs) > 0)
                                <a href="{{ $imgs[0] }}" target="_blank" style="color:#0b57d0; font-size:12px; text-decoration:underline;" title="Xem {{ count($imgs) }} ảnh minh chứng">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </a>
                            @else
                                <span style="color:#cbd5e1; font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="empty-row">Chưa có dữ liệu ghi số điện nước nào.</td></tr>
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
    <style>
        .db-badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 9999px;
        }
        .al-filter-form {
            flex-wrap: wrap;
        }
    </style>
@endpush
@endsection
