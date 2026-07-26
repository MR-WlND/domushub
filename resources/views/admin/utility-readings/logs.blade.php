@extends('layouts.admin.master')

@section('page_title', 'Lịch sử ghi số Điện Nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>Lịch sử ghi số Nước</h1>
        <p>Theo dõi và đối soát hoạt động ghi nhận, chỉnh sửa, chốt số và từ chối chỉ số nước</p>
    </div>
    <div class="util-header-actions">
        <a href="{{ route('admin.utility-readings.index') }}" class="util-btn util-btn--secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 4px; vertical-align: middle;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
            </svg>
            Quay lại trang chốt số
        </a>
    </div>
</div>

{{-- ── Filter Panel ────────────────────────────────── --}}
<div class="util-filter-panel">
    <form action="{{ route('admin.utility-readings.logs') }}" method="GET">
        <div class="util-filter-grid util-filter-grid--logs">
            <div class="util-filter-grid__search">
                <label>Tìm kiếm</label>
                <div class="filter-search-wrapper">
                    <span class="filter-search-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Số phòng, tên người..." class="util-form-input filter-search-input">
                </div>
            </div>
            <div>
                <label>Tòa nhà</label>
                <select name="block_id" class="util-form-input">
                    <option value="">Tất cả tòa</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}" {{ $blockId == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tầng</label>
                <select name="floor_id" class="util-form-input">
                    <option value="">Tất cả tầng</option>
                    @foreach ($floors as $floor)
                        <option value="{{ $floor->id }}" data-block-id="{{ $floor->block_id }}" {{ $floorId == $floor->id ? 'selected' : '' }}>
                            {{ $floor->block->name ?? '' }} – {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Loại</label>
                <select name="type" class="util-form-input">
                    <option value="">Tất cả loại</option>
                    <option value="electricity" {{ $type == 'electricity' ? 'selected' : '' }}>Điện</option>
                    <option value="water" {{ $type == 'water' ? 'selected' : '' }}>Nước</option>
                </select>
            </div>
            <div>
                <label>Hành động</label>
                <select name="action" class="util-form-input">
                    <option value="">Tất cả thao tác</option>
                    <option value="recorded" {{ $action == 'recorded' ? 'selected' : '' }}>Ghi số</option>
                    <option value="updated" {{ $action == 'updated' ? 'selected' : '' }}>Chỉnh sửa</option>
                    <option value="approved" {{ $action == 'approved' ? 'selected' : '' }}>Chốt số</option>
                    <option value="rejected" {{ $action == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div>
                <label>Tháng kỳ chốt</label>
                <select name="month" class="util-form-input">
                    <option value="">Tất cả tháng</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label>Năm kỳ chốt</label>
                <select name="year" class="util-form-input">
                    <option value="">Tất cả năm</option>
                    @for ($y = date('Y') + 1; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="util-filter-actions util-filter-actions--logs">
                <button type="submit" class="util-btn util-btn--primary util-btn--sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3H2l4 6.28V17l4 2v-7.72L16 3z" />
                    </svg>
                    Lọc
                </button>
                <a href="{{ route('admin.utility-readings.logs') }}" class="util-btn util-btn--outline util-btn--sm">Đặt lại</a>
            </div>
        </div>
    </form>
</div>

{{-- ── Data Table ──────────────────────────────────── --}}
@if ($logs->count() > 0)
<div class="util-table-card">
    <div class="util-table-wrap">
        <table class="util-table">
            <thead>
                <tr>
                    <th style="width: 60px;">STT</th>
                    <th>Căn hộ</th>
                    <th>Tòa / Tầng</th>
                    <th>Loại</th>
                    <th>Kỳ</th>
                    <th>Chỉ số chốt</th>
                    <th>Tiêu thụ</th>
                    <th>Hành động</th>
                    <th>Người thực hiện</th>
                    <th>Chi tiết nhật ký</th>
                    <th>Thời gian</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $index => $log)
                <tr>
                    <td class="text-muted">{{ $logs->firstItem() + $index }}</td>
                    <td>
                        <span class="text-strong">
                            {{ $log->apartment->apartment_number ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-muted">
                        {{ $log->apartment->floor->block->name ?? '—' }} 
                        / {{ $log->apartment->floor->name ?? 'Tầng ' . ($log->apartment->floor->floor_number ?? '') }}
                    </td>
                    <td>
                        @if ($log->type === 'electricity')
                            <span class="util-badge util-badge--electricity">Điện</span>
                        @else
                            <span class="util-badge util-badge--water">Nước</span>
                        @endif
                    </td>
                    <td class="text-strong">Tháng {{ $log->record_month }}/{{ $log->record_year }}</td>
                    <td>
                        <div style="font-size: 13px;">
                            <span class="text-muted">{{ number_format($log->old_value) }}</span> 
                            <span style="color: #94a3b8; margin: 0 4px;">→</span>
                            <span class="text-strong" style="color: #0f172a;">{{ number_format($log->new_value) }}</span>
                        </div>
                    </td>
                    <td>
                        @php
                            $usage = max(0, $log->new_value - $log->old_value);
                        @endphp
                        <strong style="color: #00236f;">{{ number_format($usage) }}</strong>
                        <small style="color: #94a3b8;">{{ $log->type === 'electricity' ? 'kWh' : 'm³' }}</small>
                    </td>
                    <td>
                        <span class="util-badge {{ $log->action_badge_class }}">
                            {{ $log->action_label }}
                        </span>
                    </td>
                    <td>
                        @if($log->user)
                            <span class="text-strong" style="font-size: 13px;">{{ $log->user->name }}</span>
                            <div style="font-size: 11px; color: #64748b; margin-top: 1px;">
                                @if($log->user->role === 'admin')
                                    Admin
                                @elseif($log->user->role === 'manager')
                                    Quản lý
                                @elseif($log->user->role === 'staff')
                                    Kế toán
                                @elseif($log->user->role === 'technician')
                                    Kỹ thuật viên
                                @endif
                            </div>
                        @else
                            <span style="color: #94a3b8; font-size: 13px; font-style: italic;">Hệ thống</span>
                        @endif
                    </td>
                    <td style="max-width: 250px; font-size: 13px;">
                        @if ($log->action === 'recorded')
                            <span style="color: #475569;">Ghi nhận số kỳ mới: <strong>{{ number_format($log->new_value) }}</strong>.</span>
                        @elseif ($log->action === 'updated')
                            <span style="color: #b45309;">
                                Chỉnh sửa chỉ số từ 
                                <strong style="text-decoration: line-through; color: #94a3b8;">{{ number_format($log->getOriginal('new_value') ?? 0) }}</strong> 
                                thành <strong>{{ number_format($log->new_value) }}</strong>.
                            </span>
                        @elseif ($log->action === 'approved')
                            <span style="color: #047857; font-weight: 500;">
                                Đã duyệt & chốt số kỳ này để đồng bộ hóa đơn.
                            </span>
                        @elseif ($log->action === 'rejected')
                            <div style="color: #b91c1c; font-weight: 500;">Bị từ chối chốt số.</div>
                            <div style="font-size: 11.5px; color: #7f1d1d; background: #fff5f5; border: 1px dashed #fca5a5; padding: 4px 8px; border-radius: 6px; margin-top: 4px; line-height: 1.4;">
                                <strong>Lý do:</strong> <em>{{ $log->reject_reason }}</em>
                            </div>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size: 12.5px; white-space: nowrap;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($logs->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px; color: #64748b;">
            Hiển thị {{ $logs->firstItem() }}–{{ $logs->lastItem() }} / {{ $logs->total() }} dòng nhật ký
        </div>
        <div style="display: flex; gap: 6px;">
            @if ($logs->onFirstPage())
                <span class="pagination-arrow disabled">‹</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="pagination-arrow">‹</a>
            @endif

            @foreach ($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="pagination-page {{ $page == $logs->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            @if ($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="pagination-arrow">›</a>
            @else
                <span class="pagination-arrow disabled">›</span>
            @endif
        </div>
    </div>
    @endif
</div>
@else
<div class="util-empty-state" style="padding: 80px 20px;">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 16px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
    </svg>
    <p>Không tìm thấy nhật ký ghi nhận chỉ số nước nào khớp với bộ lọc.</p>
    <div class="util-empty-actions">
        <a href="{{ route('admin.utility-readings.logs') }}" class="util-btn util-btn--outline">Đặt lại bộ lọc</a>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const blockSelect = document.querySelector('select[name="block_id"]');
    const floorSelect = document.querySelector('select[name="floor_id"]');
    
    if (blockSelect && floorSelect) {
        const allFloorOptions = Array.from(floorSelect.options);
        
        function filterFloors() {
            const selectedBlockId = blockSelect.value;
            const currentSelectedValue = floorSelect.value;
            
            floorSelect.innerHTML = '';
            
            allFloorOptions.forEach(option => {
                const blockIdOfOption = option.getAttribute('data-block-id');
                
                if (!selectedBlockId || !blockIdOfOption || blockIdOfOption === selectedBlockId) {
                    floorSelect.appendChild(option);
                }
            });
            
            const optionsArray = Array.from(floorSelect.options);
            const exists = optionsArray.some(opt => opt.value === currentSelectedValue);
            if (exists) {
                floorSelect.value = currentSelectedValue;
            } else {
                floorSelect.value = '';
            }
        }
        
        blockSelect.addEventListener('change', filterFloors);
        filterFloors();
    }
});
</script>
@endpush
