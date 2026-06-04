@extends('layouts.admin.master')

@section('page_title', 'Chốt số điện nước')
@section('page_kicker', 'Điện nước & Hoá đơn')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
    {{-- Page Header --}}
    <div class="ur-page-header">
        <div class="ur-page-header__left">
            <h1 class="ur-page-title">Chốt số điện nước</h1>
            <p class="ur-page-subtitle">Ghi nhận và chốt chỉ số điện, nước hàng tháng cho từng căn hộ</p>
        </div>
        <div class="ur-page-header__right">
            <span class="ur-month-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Tháng {{ $month }}/{{ $year }}
            </span>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="ur-alert ur-alert--success" id="alertSuccess">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('success') }}</span>
            <button class="ur-alert__close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="ur-alert ur-alert--error" id="alertError">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <span>{{ session('error') }}</span>
            <button class="ur-alert__close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
    @if(session('warning'))
        <div class="ur-alert ur-alert--warning" id="alertWarning">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>{{ session('warning') }}</span>
            <button class="ur-alert__close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- Filters --}}
    <form class="ur-filters" method="GET" action="{{ route('admin.utility-readings.index') }}" id="filterForm">
        <div class="ur-filter-group">
            <label class="ur-filter-label">Tháng</label>
            <select name="month" class="ur-filter-select" onchange="document.getElementById('filterForm').submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div class="ur-filter-group">
            <label class="ur-filter-label">Năm</label>
            <select name="year" class="ur-filter-select" onchange="document.getElementById('filterForm').submit()">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="ur-filter-group">
            <label class="ur-filter-label">Block</label>
            <select name="block_id" class="ur-filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">Tất cả block</option>
                @foreach($blocks as $block)
                    <option value="{{ $block->id }}" {{ $blockId == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ur-filter-group">
            <label class="ur-filter-label">Loại dịch vụ</label>
            <div class="ur-service-toggle">
                <button type="submit" name="service_type" value="electricity"
                    class="ur-toggle-btn {{ $serviceType === 'electricity' ? 'ur-toggle-btn--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Điện
                </button>
                <button type="submit" name="service_type" value="water"
                    class="ur-toggle-btn {{ $serviceType === 'water' ? 'ur-toggle-btn--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"></path></svg>
                    Nước
                </button>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="ur-summary-grid">
        <div class="ur-summary-card">
            <div class="ur-summary-card__icon ur-summary-card__icon--total">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div class="ur-summary-card__body">
                <span class="ur-summary-card__label">Tổng căn hộ</span>
                <strong class="ur-summary-card__value">{{ $stats['total_apartments'] }}</strong>
            </div>
        </div>
        <div class="ur-summary-card">
            <div class="ur-summary-card__icon ur-summary-card__icon--finalized">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div class="ur-summary-card__body">
                <span class="ur-summary-card__label">Đã chốt</span>
                <strong class="ur-summary-card__value">{{ $stats['finalized'] }}</strong>
            </div>
        </div>
        <div class="ur-summary-card">
            <div class="ur-summary-card__icon ur-summary-card__icon--draft">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div class="ur-summary-card__body">
                <span class="ur-summary-card__label">Chờ chốt</span>
                <strong class="ur-summary-card__value">{{ $stats['draft'] }}</strong>
            </div>
        </div>
        <div class="ur-summary-card">
            <div class="ur-summary-card__icon ur-summary-card__icon--consumption">
                @if($serviceType === 'electricity')
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"></path></svg>
                @endif
            </div>
            <div class="ur-summary-card__body">
                <span class="ur-summary-card__label">Tổng tiêu thụ</span>
                <strong class="ur-summary-card__value">{{ number_format($stats['total_consumption'], 0) }} <small>{{ $serviceType === 'electricity' ? 'kWh' : 'm³' }}</small></strong>
            </div>
        </div>
    </div>

    {{-- Price info bar --}}
    <div class="ur-price-bar">
        <div class="ur-price-bar__left">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            <span>Đơn giá {{ $serviceType === 'electricity' ? 'điện' : 'nước' }} hiện hành: <strong>{{ number_format($currentPrice, 0) }} VNĐ/{{ $serviceType === 'electricity' ? 'kWh' : 'm³' }}</strong></span>
        </div>
        <div class="ur-price-bar__right">
            Tổng thành tiền: <strong>{{ number_format($stats['total_amount'], 0) }} VNĐ</strong>
        </div>
    </div>

    {{-- Main Data Table --}}
    <form method="POST" action="{{ route('admin.utility-readings.store') }}" id="readingsForm">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="service_type" value="{{ $serviceType }}">
        <input type="hidden" name="block_id" value="{{ $blockId }}">

        <div class="ur-table-wrapper">
            <table class="ur-table" id="readingsTable">
                <thead>
                    <tr>
                        <th class="ur-table__th ur-table__th--stt">STT</th>
                        <th class="ur-table__th">Căn hộ</th>
                        <th class="ur-table__th">Block / Tầng</th>
                        <th class="ur-table__th ur-table__th--num">Chỉ số cũ</th>
                        <th class="ur-table__th ur-table__th--input">Chỉ số mới</th>
                        <th class="ur-table__th ur-table__th--num">Tiêu thụ</th>
                        <th class="ur-table__th ur-table__th--num">Đơn giá</th>
                        <th class="ur-table__th ur-table__th--num">Thành tiền</th>
                        <th class="ur-table__th ur-table__th--status">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $i => $row)
                        <tr class="ur-table__row {{ $row['status'] === 'finalized' ? 'ur-table__row--finalized' : '' }}" data-index="{{ $i }}">
                            <td class="ur-table__td ur-table__td--stt">{{ $i + 1 }}</td>
                            <td class="ur-table__td ur-table__td--apt">
                                <strong>{{ $row['apartment']->apartment_number }}</strong>
                            </td>
                            <td class="ur-table__td">
                                <span class="ur-block-badge">{{ $row['block_name'] }}</span>
                                <span class="ur-floor-text">Tầng {{ $row['floor_number'] }}</span>
                            </td>
                            <td class="ur-table__td ur-table__td--num">
                                {{ number_format($row['previous_reading'], 1) }}
                            </td>
                            <td class="ur-table__td ur-table__td--input">
                                @if($row['status'] === 'finalized')
                                    <span class="ur-reading-locked">{{ number_format($row['current_reading'], 1) }}</span>
                                @else
                                    <input type="hidden" name="readings[{{ $i }}][apartment_id]" value="{{ $row['apartment']->id }}">
                                    <input type="number"
                                        name="readings[{{ $i }}][current_reading]"
                                        class="ur-input-reading"
                                        value="{{ $row['current_reading'] > 0 ? $row['current_reading'] : '' }}"
                                        placeholder="Nhập chỉ số"
                                        step="0.1"
                                        min="{{ $row['previous_reading'] }}"
                                        data-previous="{{ $row['previous_reading'] }}"
                                        data-unit-price="{{ $row['unit_price'] }}"
                                        data-index="{{ $i }}"
                                        oninput="calculateRow(this)">
                                @endif
                            </td>
                            <td class="ur-table__td ur-table__td--num">
                                <span class="ur-consumption" id="consumption-{{ $i }}">{{ number_format($row['consumption'], 1) }}</span>
                                <small class="ur-unit">{{ $serviceType === 'electricity' ? 'kWh' : 'm³' }}</small>
                            </td>
                            <td class="ur-table__td ur-table__td--num">
                                {{ number_format($row['unit_price'], 0) }}
                            </td>
                            <td class="ur-table__td ur-table__td--num">
                                <strong class="ur-amount" id="amount-{{ $i }}">{{ number_format($row['total_amount'], 0) }}</strong>
                                <small class="ur-unit">VNĐ</small>
                            </td>
                            <td class="ur-table__td ur-table__td--status">
                                @if($row['status'] === 'finalized')
                                    <span class="ur-status-pill ur-status-pill--finalized">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Đã chốt
                                    </span>
                                @elseif($row['status'] === 'draft')
                                    <span class="ur-status-pill ur-status-pill--draft">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        Bản nháp
                                    </span>
                                @else
                                    <span class="ur-status-pill ur-status-pill--none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                        Chưa ghi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="ur-table__empty">
                                <div class="ur-empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                    <p>Không có căn hộ nào đang được sử dụng.</p>
                                    <small>Vui lòng thêm căn hộ và cập nhật trạng thái "Đang ở" trước.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        @if(count($tableData) > 0)
            <div class="ur-actions">
                <div class="ur-actions__left">
                    <span class="ur-actions__info">
                        {{ $stats['not_recorded'] + $stats['draft'] }} căn hộ chưa chốt
                    </span>
                </div>
                <div class="ur-actions__right">
                    <button type="submit" class="ur-btn ur-btn--save" id="btnSave">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        Lưu tất cả
                    </button>
                </div>
            </div>
        @endif
    </form>

    {{-- Finalize Form (separate) --}}
    @if($stats['draft'] > 0)
        <form method="POST" action="{{ route('admin.utility-readings.finalize') }}" id="finalizeForm"
              onsubmit="return confirm('Bạn có chắc chắn muốn chốt số tháng {{ $month }}/{{ $year }}? Sau khi chốt sẽ không thể sửa đổi.')">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="service_type" value="{{ $serviceType }}">
            <input type="hidden" name="block_id" value="{{ $blockId }}">

            <div class="ur-finalize-bar">
                <div class="ur-finalize-bar__info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>Có <strong>{{ $stats['draft'] }}</strong> bản ghi sẵn sàng chốt. Chốt xong sẽ không thể sửa đổi.</span>
                </div>
                <button type="submit" class="ur-btn ur-btn--finalize">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Chốt số tháng {{ $month }}/{{ $year }}
                </button>
            </div>
        </form>
    @endif

    @if($errors->any())
        <div class="ur-alert ur-alert--error" style="margin-top: 16px;">
            <ul style="margin:0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function calculateRow(input) {
        const index = input.dataset.index;
        const previous = parseFloat(input.dataset.previous) || 0;
        const unitPrice = parseFloat(input.dataset.unitPrice) || 0;
        const current = parseFloat(input.value) || 0;

        const consumption = Math.max(0, current - previous);
        const amount = consumption * unitPrice;

        const consumptionEl = document.getElementById('consumption-' + index);
        const amountEl = document.getElementById('amount-' + index);

        if (consumptionEl) {
            consumptionEl.textContent = consumption.toLocaleString('vi-VN', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1
            });
        }
        if (amountEl) {
            amountEl.textContent = Math.round(amount).toLocaleString('vi-VN');
        }

        // Visual feedback - highlight row
        const row = input.closest('tr');
        if (current > 0) {
            row.classList.add('ur-table__row--edited');
        } else {
            row.classList.remove('ur-table__row--edited');
        }
    }

    // Auto-dismiss alerts after 5s
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.ur-alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() { alert.remove(); }, 300);
            }, 5000);
        });
    });
</script>
@endpush
