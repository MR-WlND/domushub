@extends('layouts.admin.master')

@section('page_title', ($pageTitle ?? 'Lịch sử thao tác') . ' – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Thao tác</h1>
            <p class="db-header__sub">Tra cứu và kiểm soát toàn bộ hoạt động vận hành của hệ thống.</p>
        </div>
    </div>

    {{-- ===================== TAB NAVIGATION ===================== --}}
    <div class="al-tab-bar">
        <a href="{{ route('admin.activity-logs.entry-exit') }}"
           class="al-tab {{ $tab === 'entry_exit' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Ra vào
        </a>
        <a href="{{ route('admin.activity-logs.parking') }}"
           class="al-tab {{ $tab === 'parking' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Bãi xe
        </a>
        <a href="{{ route('admin.activity-logs.facility') }}"
           class="al-tab {{ $tab === 'facility' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Tiện ích
        </a>
        <a href="{{ route('admin.activity-logs.system') }}"
           class="al-tab {{ $tab === 'system' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Hệ thống
        </a>
        <a href="{{ route('admin.activity-logs.finance') }}"
           class="al-tab {{ $tab === 'finance' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            Tài chính
        </a>
        <a href="{{ route('admin.activity-logs.hardware') }}"
           class="al-tab {{ $tab === 'hardware' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            Phần cứng
        </a>
        <a href="{{ route('admin.activity-logs.communication') }}"
           class="al-tab {{ $tab === 'communication' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            Thông báo
        </a>
        <a href="{{ route('admin.activity-logs.utility') }}"
           class="al-tab {{ $tab === 'utility' ? 'al-tab--active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"></path></svg>
            Ghi số điện nước
        </a>
    </div>

    {{-- ===================== SECTION HEADER ===================== --}}
    <div class="al-section-header">
        <div>
            <h2 class="al-section-title">{{ $pageTitle }}</h2>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="al-filter-group">
                <label class="al-filter-label">Từ khóa</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="Tìm kiếm...">
            </div>

            @if(in_array($tab, ['system', 'communication']))
            <div class="al-filter-group">
                <label class="al-filter-label">Người thực hiện</label>
                <select name="causer_id" class="al-filter-input">
                    <option value="">Tất cả</option>
                    @foreach($users ?? [] as $u)
                        <option value="{{ $u->id }}" {{ request('causer_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($tab === 'entry_exit')
            <div class="al-filter-group">
                <label class="al-filter-label">Trạng thái</label>
                <select name="status" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ vào</option>
                    <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Đã vào</option>
                    <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Đã ra</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            @endif

            @if($tab === 'parking')
            <div class="al-filter-group">
                <label class="al-filter-label">Trạng thái</label>
                <select name="status" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="inside" {{ request('status') == 'inside' ? 'selected' : '' }}>Trong bãi</option>
                    <option value="exited" {{ request('status') == 'exited' ? 'selected' : '' }}>Đã ra</option>
                </select>
            </div>
            @endif

            @if($tab === 'facility')
            <div class="al-filter-group">
                <label class="al-filter-label">Trạng thái</label>
                <select name="status" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Đã sử dụng</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            @endif

            @if($tab === 'finance')
            <div class="al-filter-group">
                <label class="al-filter-label">Phương thức</label>
                <select name="method" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                    <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                    <option value="vnpay" {{ request('method') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                </select>
            </div>
            @endif

            @if($tab === 'utility')
            <div class="al-filter-group">
                <label class="al-filter-label">Tòa nhà</label>
                <select name="block_id" class="al-filter-input">
                    <option value="">Tất cả tòa</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}" {{ $blockId == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-filter-group">
                <label class="al-filter-label">Tầng</label>
                <select name="floor_id" class="al-filter-input">
                    <option value="">Tất cả tầng</option>
                    @foreach ($floors as $floor)
                        <option value="{{ $floor->id }}" data-block-id="{{ $floor->block_id }}" {{ $floorId == $floor->id ? 'selected' : '' }}>
                            {{ $floor->block->name ?? '' }} – {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="al-filter-group">
                <label class="al-filter-label">Loại</label>
                <select name="type" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="electricity" {{ request('type') == 'electricity' ? 'selected' : '' }}>Điện</option>
                    <option value="water" {{ request('type') == 'water' ? 'selected' : '' }}>Nước</option>
                </select>
            </div>
            <div class="al-filter-group">
                <label class="al-filter-label">Hành động</label>
                <select name="action" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="recorded" {{ request('action') == 'recorded' ? 'selected' : '' }}>Ghi số</option>
                    <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Chỉnh sửa</option>
                    <option value="approved" {{ request('action') == 'approved' ? 'selected' : '' }}>Chốt số</option>
                    <option value="rejected" {{ request('action') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div class="al-filter-group">
                <label class="al-filter-label">Kỳ (Tháng/Năm)</label>
                <div style="display:flex; gap: 4px;">
                    <input type="number" name="month" value="{{ request('month') }}" class="al-filter-input" placeholder="Tháng" min="1" max="12" style="width:60px; padding: 4px;">
                    <input type="number" name="year" value="{{ request('year') }}" class="al-filter-input" placeholder="Năm" min="2020" max="2100" style="width:70px; padding: 4px;">
                </div>
            </div>
            @endif

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
                @if(request()->anyFilled(['search','causer_id','status','method','date_from','date_to']))
                <a href="{{ request()->url() }}" class="al-btn-reset">Xóa lọc</a>
                @endif
                <button type="submit" formaction="{{ route('admin.activity-logs.export') }}" class="al-btn-export">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Xuất Excel
                </button>
            </div>
        </form>
    </div>

    {{-- ===================== DATA TABLE ===================== --}}
    <div class="table-card">
        <div class="table-wrap">

            {{-- TAB: RA VÀO --}}
            @if($tab === 'entry_exit')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thời gian đăng ký</th>
                        <th>Tên khách</th>
                        <th>SĐT</th>
                        <th>Căn hộ</th>
                        <th>Người đăng ký</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $log->guest_name }}</strong></td>
                        <td style="color:#475569;">{{ $log->guest_phone }}</td>
                        <td>{{ $log->apartment->apartment_number ?? '—' }}</td>
                        <td>{{ $log->registeredBy->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#475569;">{{ $log->check_in_at ? $log->check_in_at->format('d/m H:i') : '—' }}</td>
                        <td style="font-size:12px;color:#475569;">{{ $log->check_out_at ? $log->check_out_at->format('d/m H:i') : '—' }}</td>
                        <td style="text-align:center;">
                            @php
                                $s = [
                                    'pending'     => ['Chờ vào','#2563eb','#dbeafe'],
                                    'checked_in'  => ['Đã vào','#059669','#d1fae5'],
                                    'checked_out' => ['Đã ra','#475569','#f1f5f9'],
                                    'expired'     => ['Hết hạn','#d97706','#fef3c7'],
                                    'cancelled'   => ['Đã hủy','#dc2626','#fee2e2'],
                                ];
                                [$sl,$sc,$sb] = $s[$log->status] ?? [ucfirst($log->status),'#475569','#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{$sc}};background:{{$sb}};">{{$sl}}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="empty-row">Chưa có dữ liệu ra vào nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif

            {{-- TAB: BÃI XE --}}
            @if($tab === 'parking')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thời gian vào</th>
                        <th>Biển số</th>
                        <th>Loại xe</th>
                        <th>Căn hộ</th>
                        <th>Check-in bởi</th>
                        <th>Thời gian ra</th>
                        <th>Check-out bởi</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ $log->check_in_at ? $log->check_in_at->format('d/m/Y H:i') : '—' }}</td>
                        <td><strong>{{ $log->vehicle->license_plate ?? '—' }}</strong></td>
                        <td style="color:#475569;">
                            @php
                                $types = ['car' => 'Ô tô','motorbike' => 'Xe máy','electric_bike' => 'Xe điện'];
                                echo $types[$log->vehicle->type ?? ''] ?? '—';
                            @endphp
                        </td>
                        <td>{{ $log->vehicle->apartment->apartment_number ?? '—' }}</td>
                        <td>{{ $log->checkedInBy->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#475569;">{{ $log->check_out_at ? $log->check_out_at->format('d/m H:i') : '—' }}</td>
                        <td>{{ $log->checkedOutBy->name ?? '—' }}</td>
                        <td style="text-align:center;">
                            @php $inside = $log->status === 'inside'; @endphp
                            <span class="db-badge" style="color:{{ $inside ? '#059669' : '#475569' }};background:{{ $inside ? '#d1fae5' : '#f1f5f9' }};">
                                {{ $inside ? 'Trong bãi' : 'Đã ra' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="empty-row">Chưa có dữ liệu ra vào bãi xe nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif

            {{-- TAB: TIỆN ÍCH --}}
            @if($tab === 'facility')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ngày đặt</th>
                        <th>Cư dân</th>
                        <th>Tiện ích</th>
                        <th>Khung giờ</th>
                        <th>Số người</th>
                        <th>Thanh toán</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ \Carbon\Carbon::parse($log->booking_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $log->user->name ?? '—' }}</strong></td>
                        <td>{{ $log->facility->name ?? '—' }}</td>
                        <td style="font-size:12px;color:#475569;">{{ $log->start_time }} – {{ $log->end_time }}</td>
                        <td style="text-align:center;">{{ $log->number_of_people ?? 1 }}</td>
                        <td>
                            @php $paid = $log->payment_status === 'paid'; @endphp
                            <span class="db-badge" style="color:{{ $paid ? '#059669' : '#d97706' }};background:{{ $paid ? '#d1fae5' : '#fef3c7' }};">
                                {{ $paid ? 'Đã TT' : 'Chưa TT' }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            @php
                                $fs = [
                                    'pending'   => ['Chờ duyệt','#d97706','#fef3c7'],
                                    'approved'  => ['Đã duyệt','#2563eb','#dbeafe'],
                                    'used'      => ['Đã dùng','#059669','#d1fae5'],
                                    'cancelled' => ['Đã hủy','#475569','#f1f5f9'],
                                    'rejected'  => ['Từ chối','#dc2626','#fee2e2'],
                                    'completed' => ['Hoàn thành','#4f46e5','#e0e7ff'],
                                ];
                                [$fl,$fc,$fb] = $fs[$log->status] ?? [ucfirst($log->status),'#475569','#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{$fc}};background:{{$fb}};">{{$fl}}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-row">Chưa có dữ liệu đặt tiện ích nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif

            {{-- TAB: HỆ THỐNG / PHẦN CỨNG / THÔNG BÁO (dùng chung từ Spatie Activity) --}}
            @if(in_array($tab, ['system', 'hardware', 'communication']))
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người thực hiện</th>
                        <th>Phân hệ</th>
                        <th>Mô tả hành động</th>
                        <th style="text-align:center">Dữ liệu thay đổi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $log->causer->name ?? 'Hệ thống' }}</strong></td>
                        <td>
                            @php
                                $mMap = [
                                    'hardware'      => ['Phần cứng','#2563eb','#dbeafe'],
                                    'qr'            => ['QR Scanner','#4f46e5','#e0e7ff'],
                                    'scanner'       => ['Scanner','#6366f1','#eef2ff'],
                                    'notification'  => ['Thông báo','#059669','#d1fae5'],
                                    'announcement'  => ['Thông báo','#059669','#d1fae5'],
                                    'resident'      => ['Cư dân','#16a34a','#f0fdf4'],
                                    'default'       => ['Hệ thống','#475569','#f1f5f9'],
                                ];
                                [$ml,$mc,$mb] = $mMap[$log->log_name ?? 'default'] ?? ['Khác','#475569','#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{$mc}};background:{{$mb}};">{{$ml}}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td style="text-align:center;">
                            @if($log->properties && count($log->properties) > 0)
                                <button onclick="showProps({{ $log->id }}, {{ json_encode($log->properties) }})"
                                        class="al-btn-detail">Xem JSON</button>
                            @else
                                <span style="color:#94a3b8;font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-row">Chưa có dữ liệu lịch sử nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif

            {{-- TAB: TÀI CHÍNH --}}
            @if($tab === 'finance')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thời gian TT</th>
                        <th>Mã biên lai</th>
                        <th>Mã GD</th>
                        <th>Người nộp</th>
                        <th>Căn hộ</th>
                        <th>Số tiền</th>
                        <th>Phương thức</th>
                        <th>Ghi nhận bởi</th>
                        <th style="text-align:center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ $log->paid_at ? $log->paid_at->format('d/m/Y H:i') : '—' }}</td>
                        <td><strong>{{ $log->receipt_code }}</strong></td>
                        <td style="font-size:12px;color:#64748b;">{{ $log->transaction_code ?? '—' }}</td>
                        <td>{{ $log->payer_name ?? '—' }}</td>
                        <td>{{ $log->invoice->apartment->apartment_number ?? '—' }}</td>
                        <td style="font-weight:700;color:#0f172a;">{{ number_format($log->amount) }} đ</td>
                        <td>
                            @php
                                $methods = ['cash' => 'Tiền mặt','bank_transfer' => 'Chuyển khoản','vnpay' => 'VNPay'];
                                echo $methods[$log->payment_method ?? ''] ?? ($log->payment_method ?? '—');
                            @endphp
                        </td>
                        <td>{{ $log->recorder->name ?? '—' }}</td>
                        <td style="text-align:center;">
                            @php
                                $ps = [
                                    'paid'     => ['Thành công','#059669','#d1fae5'],
                                    'pending'  => ['Chờ xác nhận','#d97706','#fef3c7'],
                                    'failed'   => ['Thất bại','#dc2626','#fee2e2'],
                                    'refunded' => ['Đã hoàn','#4f46e5','#e0e7ff'],
                                ];
                                [$pl,$pc,$pb] = $ps[$log->status ?? ''] ?? ['Khác','#475569','#f1f5f9'];
                            @endphp
                            <span class="db-badge" style="color:{{$pc}};background:{{$pb}};">{{$pl}}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty-row">Chưa có giao dịch thanh toán nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif

            {{-- TAB: GHI SỐ ĐIỆN NƯỚC --}}
            @if($tab === 'utility')
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Căn hộ</th>
                        <th>Kỳ (T/N)</th>
                        <th>Loại</th>
                        <th style="text-align:right">Chỉ số cũ</th>
                        <th style="text-align:right">Chỉ số mới</th>
                        <th style="text-align:right">Tiêu thụ</th>
                        <th>Người thực hiện</th>
                        <th style="text-align:center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $props = $log->properties;
                            $oldVal = $props['old_value'] ?? 0;
                            $newVal = $props['new_value'] ?? 0;
                            $usage = max(0, $newVal - $oldVal);
                            
                            $actionStr = $props['action'] ?? '';
                            $aBadge = ['Ghi số','#475569','#f1f5f9'];
                            if($actionStr === 'updated') $aBadge = ['Cập nhật','#2563eb','#dbeafe'];
                            if($actionStr === 'approved') $aBadge = ['Chốt số','#059669','#d1fae5'];
                            if($actionStr === 'rejected') $aBadge = ['Từ chối','#dc2626','#fee2e2'];
                        @endphp
                    <tr>
                        <td style="color:#64748b;font-size:12px;">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ $log->apartment->apartment_number ?? '—' }}</strong></td>
                        <td>{{ str_pad($props['record_month'] ?? 0, 2, '0', STR_PAD_LEFT) }}/{{ $props['record_year'] ?? '' }}</td>
                        <td>
                            @if(($props['type'] ?? '') === 'electricity')
                                <span style="color:#eab308;font-weight:600;">⚡ Điện</span>
                            @else
                                <span style="color:#3b82f6;font-weight:600;">💧 Nước</span>
                            @endif
                        </td>
                        <td style="text-align:right;">{{ number_format($oldVal) }}</td>
                        <td style="text-align:right;">{{ number_format($newVal) }}</td>
                        <td style="text-align:right;font-weight:700;color:#0f172a;">{{ number_format($usage) }}</td>
                        <td>{{ $log->causer->name ?? '—' }}</td>
                        <td style="text-align:center;">
                            <span class="db-badge" style="color:{{$aBadge[1]}};background:{{$aBadge[2]}};">{{$aBadge[0]}}</span>
                            @if($actionStr === 'rejected' && !empty($props['reject_reason']))
                                <div style="font-size:11px;color:#dc2626;margin-top:4px;">Lý do: {{ $props['reject_reason'] }}</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty-row">Chưa có dữ liệu ghi số.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif

        </div>

        {{-- PAGINATION --}}
        <div class="al-pagination">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

{{-- Modal JSON --}}
<div id="jsonModal" class="al-modal" style="display:none;" onclick="if(event.target===this)closeModal()">
    <div class="al-modal__box">
        <div class="al-modal__header">
            <strong>Chi tiết thay đổi</strong>
            <button onclick="closeModal()" class="al-modal__close">✕</button>
        </div>
        <pre id="jsonContent" class="al-modal__pre"></pre>
    </div>
</div>

@push('styles')
    @vite(['resources/css/pages/admin/statistics.css', 'resources/css/pages/admin/dashboard.css', 'resources/css/pages/admin/activity-logs.css'])
@endpush

@push('scripts')
<script>
function showProps(id, data) {
    document.getElementById('jsonContent').textContent = JSON.stringify(data, null, 2);
    document.getElementById('jsonModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('jsonModal').style.display = 'none';
}
</script>
@endpush
@endsection
