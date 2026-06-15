@extends('layouts.admin.master')

@section('page_title', 'Chốt số Điện Nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>Chốt số Điện Nước</h1>
        <p>Quản lý chỉ số điện nước theo tháng – Tháng {{ $month }}/{{ $year }}</p>
    </div>
    @if(auth()->user()->role !== 'staff')
    <div class="util-header-actions">
        <a href="{{ route('admin.utility-readings.batch') }}" class="util-btn util-btn--secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Ghi hàng loạt
        </a>
        <a href="{{ route('admin.utility-readings.create') }}" class="util-btn util-btn--primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ghi đơn lẻ
        </a>
    </div>
    @endif
</div>

{{-- ── Flash Message ───────────────────────────────── --}}
@if (session('success'))
    <div class="util-alert--success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="util-alert--danger">{{ session('error') }}</div>
@endif

{{-- ── Stat Cards ──────────────────────────────────── --}}
<div class="util-stats-grid">
    <div class="util-stat-card">
        <div class="util-stat-card__label">Tổng bản ghi</div>
        <div class="util-stat-card__value">{{ number_format($stats['total_records']) }}</div>
    </div>
    <div class="util-stat-card" style="border-top: 3px solid #f59e0b;">
        <div class="util-stat-card__label">Tổng tiêu thụ Điện</div>
        <div class="util-stat-card__value">
            {{ number_format($stats['total_electricity']) }}
            <small>kWh</small>
        </div>
    </div>
    <div class="util-stat-card" style="border-top: 3px solid #3b82f6;">
        <div class="util-stat-card__label">Tổng tiêu thụ Nước</div>
        <div class="util-stat-card__value">
            {{ number_format($stats['total_water']) }}
            <small>m³</small>
        </div>
    </div>
    <div class="util-stat-card" style="border-top: 3px solid #10b981;">
        <div class="util-stat-card__label">Căn hộ đã chốt</div>
        <div class="util-stat-card__value">
            {{ $stats['apartments_recorded'] }}
            <small>/ {{ $stats['apartments_total'] }}</small>
        </div>
        @php $pct = $stats['apartments_total'] > 0 ? round($stats['apartments_recorded'] / $stats['apartments_total'] * 100) : 0; @endphp
        <div style="margin-top: 10px; background: #e2e8f0; border-radius: 999px; height: 6px; overflow: hidden;">
            <div style="height: 100%; width: {{ $pct }}%; background: #10b981; border-radius: 999px; transition: width 0.6s ease;"></div>
        </div>
        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">{{ $pct }}% hoàn thành</div>
    </div>
</div>

{{-- ── Filter Panel ────────────────────────────────── --}}
<div class="util-filter-panel">
    <form action="{{ route('admin.utility-readings.index') }}" method="GET">
        <div class="util-filter-grid">
            <div>
                <label>Tòa nhà</label>
                <select name="block_id">
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
                <select name="floor_id">
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
                <select name="type">
                    <option value="">Tất cả</option>
                    <option value="electricity" {{ $type == 'electricity' ? 'selected' : '' }}>Điện</option>
                    <option value="water" {{ $type == 'water' ? 'selected' : '' }}>Nước</option>
                </select>
            </div>
            <div>
                <label>Tháng</label>
                <input type="number" name="month" value="{{ $month }}" min="1" max="12" placeholder="Tháng">
            </div>
            <div>
                <label>Năm</label>
                <input type="number" name="year" value="{{ $year }}" min="2020" max="2100" placeholder="Năm">
            </div>
            <div class="util-filter-actions">
                <button type="submit" class="util-btn util-btn--primary util-btn--sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Lọc
                </button>
                <a href="{{ route('admin.utility-readings.index') }}" class="util-btn util-btn--outline util-btn--sm">Đặt lại</a>
            </div>
        </div>
    </form>
</div>

{{-- ── Data Table ──────────────────────────────────── --}}
@if ($readings->count() > 0)
<div class="util-table-card">
    @if(auth()->user()->role !== 'technician')
    <div id="batchApproveBar" style="display:none; align-items:center; justify-content:space-between; padding:12px 20px; background:#f0fdf4; border-bottom:1px solid #bbf7d0;">
        <span style="font-size:13px; color:#15803d; font-weight:600;">
            Đang chọn <span id="selectedCount">0</span> mục chờ phê duyệt
        </span>
        <form action="{{ route('admin.utility-readings.batch-approve') }}" method="POST" id="batchApproveForm" style="margin:0; display:flex; gap:10px;">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="util-btn util-btn--primary util-btn--sm" style="background:#15803d; border-color:#15803d; font-size:12px; height:32px; padding:0 12px;">
                Duyệt các mục đã chọn
            </button>
        </form>
    </div>
    @endif
    <div class="util-table-wrap">
        <table class="util-table">
            <thead>
                <tr>
                    @if(auth()->user()->role !== 'technician')
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="selectAllReadings" style="width:15px;height:15px;accent-color:#00236f;cursor:pointer;">
                    </th>
                    @endif
                    <th>STT</th>
                    <th>Phòng</th>
                    <th>Tòa / Tầng</th>
                    <th>Loại</th>

                    <th>Chỉ số mới</th>
                    @if(auth()->user()->role !== 'technician')
                    <th>Tiêu thụ</th>
                    @endif
                    <th>Trạng thái</th>
                    <th>Người ghi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($readings as $index => $reading)
                <tr id="reading-{{ $reading->id }}" class="{{ request('highlight') == $reading->id ? 'util-row-highlight' : '' }}">
                    @if(auth()->user()->role !== 'technician')
                    <td style="text-align: center;">
                        @if($reading->status === 'pending')
                        <input type="checkbox" class="reading-checkbox" value="{{ $reading->id }}" style="width:15px;height:15px;accent-color:#00236f;cursor:pointer;">
                        @endif
                    </td>
                    @endif
                    <td class="text-muted">{{ $readings->firstItem() + $index }}</td>
                    <td><span class="text-strong">{{ $reading->apartment->apartment_number ?? 'N/A' }}</span></td>
                    <td class="text-muted">
                        {{ $reading->apartment->floor->block->name ?? '—' }}
                        / {{ $reading->apartment->floor->name ?? 'Tầng ' . ($reading->apartment->floor->floor_number ?? '') }}
                    </td>
                    <td>
                        @if ($reading->type === 'electricity')
                            <span class="util-badge util-badge--electricity">Điện</span>
                        @else
                            <span class="util-badge util-badge--water">Nước</span>
                        @endif
                    </td>

                    <td class="text-strong">
                        {{ number_format($reading->new_value) }}
                        @if($reading->image_proof)
                        <span class="proof-photo-btn" data-img="{{ asset('storage/' . $reading->image_proof) }}" style="cursor:pointer; margin-left:6px; color:#0b57d0; display:inline-flex; align-items:center; vertical-align:middle;" title="Xem minh chứng công tơ">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </span>
                        @endif
                    </td>
                    @if(auth()->user()->role !== 'technician')
                    <td>
                        <strong style="color: #00236f;">{{ number_format($reading->usage_amount) }}</strong>
                        <small style="color: #94a3b8;">{{ $reading->type === 'electricity' ? 'kWh' : 'm³' }}</small>
                    </td>
                    @endif
                    <td>
                        @if($reading->status === 'approved')
                            <span class="util-badge util-badge--success" style="background:#e6f4ea; color:#137333; font-size:11px;">Đã chốt</span>
                        @elseif($reading->status === 'rejected')
                            <span class="util-badge util-badge--danger" style="background:#fce8e6; color:#c5221f; font-size:11px;">Bị từ chối</span>
                        @else
                            <span class="util-badge util-badge--warning" style="background:#fef7e0; color:#b06000; font-size:11px;">Chờ chốt</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $reading->recorder->name ?? '—' }}</td>
                    <td>
                        <div class="util-actions">
                            @if(auth()->user()->role !== 'technician')
                                <button type="button" class="util-btn-view" onclick="openDetailModal({{ $reading->id }})" title="Xem chi tiết">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                                @if($reading->status === 'pending')
                                <form action="{{ route('admin.utility-readings.approve', $reading->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="util-btn util-btn--primary util-btn--xs" style="padding: 4px 8px; font-size:11px; height:auto; background:#15803d; border-color:#15803d; line-height:1;" title="Phê duyệt">
                                        Duyệt
                                    </button>
                                </form>
                                <form action="{{ route('admin.utility-readings.reject', $reading->id) }}" method="POST" style="margin:0;" id="reject-form-{{ $reading->id }}">
                                    @csrf
                                    <input type="hidden" name="reject_reason" id="reject-reason-{{ $reading->id }}">
                                    <button type="button" class="util-btn util-btn--danger util-btn--xs" style="padding: 4px 8px; font-size:11px; height:auto; background:#dc2626; border-color:#dc2626; color:white; line-height:1;" title="Từ chối" onclick="confirmAndReject({{ $reading->id }})">
                                        Từ chối
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('admin.utility-readings.edit', $reading->id) }}"
                                    class="util-btn-edit" title="Sửa">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.utility-readings.destroy', $reading->id) }}"
                                    method="POST" style="margin: 0;"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa chỉ số này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="util-btn-delete" title="Xóa">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="util-btn-view" onclick="openDetailModal({{ $reading->id }})" title="Xem chi tiết">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                                @if(in_array($reading->status, ['pending', 'rejected']) && $reading->recorded_by === auth()->id())
                                <a href="{{ route('admin.utility-readings.edit', $reading->id) }}"
                                    class="util-btn-edit" title="Sửa">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($readings->hasPages())
    <div style="padding: 16px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <div style="font-size: 13px; color: #64748b;">
            Hiển thị {{ $readings->firstItem() }}–{{ $readings->lastItem() }} / {{ $readings->total() }} bản ghi
        </div>
        <div style="display: flex; gap: 6px;">
            @if ($readings->onFirstPage())
                <span class="pagination-arrow disabled">‹</span>
            @else
                <a href="{{ $readings->previousPageUrl() }}" class="pagination-arrow">‹</a>
            @endif

            @foreach ($readings->getUrlRange(max(1, $readings->currentPage() - 2), min($readings->lastPage(), $readings->currentPage() + 2)) as $page => $url)
                <a href="{{ $url }}" class="pagination-page {{ $page == $readings->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach

            @if ($readings->hasMorePages())
                <a href="{{ $readings->nextPageUrl() }}" class="pagination-arrow">›</a>
            @else
                <span class="pagination-arrow disabled">›</span>
            @endif
        </div>
    </div>
    @endif
</div>

@else
<div class="util-empty-state">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 16px;">
        <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>
    </svg>
    <p>Chưa có chỉ số nào được ghi cho tháng {{ $month }}/{{ $year }}.</p>
    @if(auth()->user()->role !== 'staff')
    <div class="util-empty-actions">
        <a href="{{ route('admin.utility-readings.batch') }}" class="util-btn util-btn--secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Ghi hàng loạt
        </a>
        <a href="{{ route('admin.utility-readings.create') }}" class="util-btn util-btn--primary">+ Ghi đơn lẻ</a>
    </div>
    @endif
</div>
@endif



{{-- ── Proof Modal ────────────────────────────────────── --}}
<div class="util-modal-backdrop" id="proofModal">
    <div class="util-modal" style="max-width: 540px;">
        <div class="util-modal-header">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:6px; color:#0b57d0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                Ảnh chụp công tơ minh chứng
            </h3>
            <button class="util-modal-close" onclick="closeProofModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body" style="text-align: center; padding: 20px;">
            <img id="proofModalImg" src="" alt="Ảnh minh chứng" style="max-width: 100%; max-height: 60vh; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        </div>
    </div>
</div>

{{-- ── Detail Modal ────────────────────────────────────── --}}
<div class="util-modal-backdrop" id="detailModal">
    <div class="util-modal" style="max-width: 680px;">
        <div class="util-modal-header">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:6px; color:#0b57d0;">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
                Chi tiết chỉ số điện nước
            </h3>
            <button class="util-modal-close" onclick="closeDetailModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body" style="padding: 24px;">
            <div id="detailModalContent" style="display: none;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px;">
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Căn hộ</div>
                        <div style="font-size: 15px; font-weight: 700; color: #00236f;" id="detApartment"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Tòa nhà / Tầng</div>
                        <div style="font-size: 14px; font-weight: 600; color: #334155;" id="detLocation"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Loại dịch vụ</div>
                        <div style="font-size: 14px; font-weight: 600; color: #334155;" id="detType"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Kỳ ghi nhận</div>
                        <div style="font-size: 14px; font-weight: 600; color: #334155;" id="detPeriod"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Chỉ số mới</div>
                        <div style="font-size: 14px; font-weight: 600; color: #334155;" id="detNew"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Trạng thái</div>
                        <div style="margin-top: 4px;" id="detStatus"></div>
                    </div>
                    <div style="grid-column: span 2; background: #f8fafc; padding: 12px; border-radius: 8px;">
                        <div style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase;">Lượng tiêu thụ thực tế</div>
                        <div style="font-size: 18px; font-weight: 800; color: #00236f; margin-top: 4px;" id="detUsage"></div>
                    </div>
                    <div id="detRejectInfo" style="grid-column: span 2; background: #fef2f2; border: 1px solid #fecaca; padding: 12px; border-radius: 8px; display: none;">
                        <div style="font-size: 11px; font-weight: 600; color: #b91c1c; text-transform: uppercase;">Lý do từ chối</div>
                        <div style="font-size: 14px; font-weight: 600; color: #991b1b; margin-top: 4px;" id="detRejectReason"></div>
                        <div style="font-size: 12px; color: #b91c1c; margin-top: 4px;">Người từ chối: <span id="detRejecter" style="font-weight: 700;"></span></div>
                    </div>
                    <div style="grid-column: span 2;">
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Người ghi nhận</div>
                        <div style="font-size: 14px; font-weight: 600; color: #334155;" id="detRecorder"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Ngày ghi nhận</div>
                        <div style="font-size: 13px; color: #64748b;" id="detCreatedAt"></div>
                    </div>
                    <div>
                        <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase;">Cập nhật cuối</div>
                        <div style="font-size: 13px; color: #64748b;" id="detUpdatedAt"></div>
                    </div>
                </div>

                <div id="detImageContainer" style="margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 16px; text-align: center;">
                    <div style="font-size: 11px; font-weight: 600; color: #94a3b8; text-transform: uppercase; text-align: left; margin-bottom: 8px;">Ảnh công tơ minh chứng</div>
                    <img id="detImage" src="" alt="Ảnh công tơ" style="max-width: 100%; max-height: 35vh; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                </div>
                
                <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                    <a id="detLinkPrint" href="" target="_blank" class="util-btn util-btn--outline util-btn--sm" style="display: inline-flex; align-items: center; gap: 4px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0H3.75A1.5 1.5 0 0 1 2.25 12.18V7.5a1.5 1.5 0 0 1 1.5-1.5h16.5a1.5 1.5 0 0 1 1.5 1.5v4.68a1.5 1.5 0 0 1-1.5 1.5H17.28m-10.56 0h10.56M9 3.75h6" />
                        </svg>
                        Mở tab & In
                    </a>
                    <button type="button" class="util-btn util-btn--outline util-btn--sm" onclick="closeDetailModal()">Đóng</button>
                </div>
            </div>
            <div id="detailModalLoading" style="text-align: center; padding: 40px; color: #64748b;">
                <div style="display: inline-block; width: 24px; height: 24px; border: 3px solid #cbd5e1; border-top-color: #00236f; border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 12px;"></div>
                <div>Đang tải dữ liệu...</div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

@endsection

@push('scripts')
<script>


document.addEventListener('DOMContentLoaded', function() {
    const blockSelect = document.querySelector('select[name="block_id"]');
    const floorSelect = document.querySelector('select[name="floor_id"]');
    
    if (blockSelect && floorSelect) {
        // Lưu danh sách tất cả các options tầng ban đầu
        const allFloorOptions = Array.from(floorSelect.options);
        
        function filterFloors() {
            const selectedBlockId = blockSelect.value;
            const currentSelectedValue = floorSelect.value;
            
            // Xóa tất cả option hiện tại
            floorSelect.innerHTML = '';
            
            // Lọc và chèn lại các option phù hợp
            allFloorOptions.forEach(option => {
                const blockIdOfOption = option.getAttribute('data-block-id');
                
                // Hiển thị nếu là option mặc định "Tất cả tầng" hoặc thuộc tòa nhà được chọn hoặc chưa chọn tòa nhà nào
                if (!selectedBlockId || !blockIdOfOption || blockIdOfOption === selectedBlockId) {
                    floorSelect.appendChild(option);
                }
            });
            
            // Khôi phục lại giá trị đã chọn nếu nó vẫn nằm trong danh sách được hiển thị
            const optionsArray = Array.from(floorSelect.options);
            const exists = optionsArray.some(opt => opt.value === currentSelectedValue);
            if (exists) {
                floorSelect.value = currentSelectedValue;
            } else {
                floorSelect.value = '';
            }
        }
        
        // Lắng nghe sự kiện thay đổi tòa nhà
        blockSelect.addEventListener('change', filterFloors);
        
        // Chạy lúc load trang để lọc sẵn nếu có tòa nhà được chọn trước đó
        filterFloors();
    }



    // Proof image popup modal
    window.closeProofModal = function() {
        document.getElementById('proofModal').classList.remove('active');
    }

    document.querySelectorAll('.proof-photo-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const imgUrl = this.dataset.img;
            if (imgUrl) {
                document.getElementById('proofModalImg').src = imgUrl;
                document.getElementById('proofModal').classList.add('active');
            }
        });
    });

    // Batch approve selection
    const selectAllReadings = document.getElementById('selectAllReadings');
    const batchApproveBar = document.getElementById('batchApproveBar');
    const selectedCount = document.getElementById('selectedCount');
    const batchApproveForm = document.getElementById('batchApproveForm');
    const readingCheckboxes = document.querySelectorAll('.reading-checkbox');

    function updateBatchBar() {
        if (!batchApproveBar) return;
        const checkedBoxes = document.querySelectorAll('.reading-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            selectedCount.textContent = count;
            batchApproveBar.style.display = 'flex';

            // Clear previous ids and add checked ids
            batchApproveForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
            checkedBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                batchApproveForm.appendChild(input);
            });
        } else {
            batchApproveBar.style.display = 'none';
        }
    }

    if (selectAllReadings) {
        selectAllReadings.addEventListener('change', function() {
            readingCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBatchBar();
        });
    }

    readingCheckboxes.forEach(cb => {
      cb.addEventListener('change', function() {
          updateBatchBar();
          if (selectAllReadings) {
              selectAllReadings.checked = (readingCheckboxes.length === document.querySelectorAll('.reading-checkbox:checked').length);
          }
      });
  });

  // Close modals when clicking on the backdrop
  document.querySelectorAll('.util-modal-backdrop').forEach(backdrop => {
      backdrop.addEventListener('click', function(e) {
          if (e.target === this) {
              this.classList.remove('active');
          }
      });
  });

  // Close modals with Escape key
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
          document.querySelectorAll('.util-modal-backdrop.active').forEach(modal => {
              modal.classList.remove('active');
          });
      }
  });

  // Scroll to highlighted reading and trigger a smooth scroll
  const highlightId = new URLSearchParams(window.location.search).get('highlight');
  if (highlightId) {
      const targetRow = document.getElementById('reading-' + highlightId);
      if (targetRow) {
          setTimeout(() => {
              targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }, 300);
      }
  }
});

function openDetailModal(id) {
    const modal = document.getElementById('detailModal');
    const loading = document.getElementById('detailModalLoading');
    const content = document.getElementById('detailModalContent');
    
    modal.classList.add('active');
    loading.style.display = 'block';
    content.style.display = 'none';

    fetch(`{{ url('admin/utility-readings') }}/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const reading = data.reading;
            document.getElementById('detApartment').textContent = reading.apartment_number;
            document.getElementById('detLocation').textContent = reading.location;
            document.getElementById('detType').textContent = reading.type_label;
            document.getElementById('detPeriod').textContent = `Tháng ${reading.record_month}/${reading.record_year}`;
            document.getElementById('detNew').textContent = Number(reading.new_value).toLocaleString('vi-VN');
            document.getElementById('detUsage').textContent = `${Number(reading.usage_amount).toLocaleString('vi-VN')} ${reading.type === 'electricity' ? 'kWh' : 'm³'}`;
            document.getElementById('detRecorder').textContent = reading.recorder_name;
            document.getElementById('detCreatedAt').textContent = reading.created_at;
            document.getElementById('detUpdatedAt').textContent = reading.updated_at;

            // Status badge
            const statusEl = document.getElementById('detStatus');
            statusEl.innerHTML = '';
            const badge = document.createElement('span');
            badge.className = 'util-badge';
            if (reading.status === 'approved') {
                badge.className += ' util-badge--success';
                badge.textContent = 'Đã chốt';
                badge.style.cssText = 'background:#e6f4ea; color:#137333; font-size:11px;';
            } else if (reading.status === 'rejected') {
                badge.className += ' util-badge--danger';
                badge.textContent = 'Bị từ chối';
                badge.style.cssText = 'background:#fce8e6; color:#c5221f; font-size:11px;';
            } else {
                badge.className += ' util-badge--warning';
                badge.textContent = 'Chờ chốt';
                badge.style.cssText = 'background:#fef7e0; color:#b06000; font-size:11px;';
            }
            statusEl.appendChild(badge);

            // Reject reason details
            const rejectInfoEl = document.getElementById('detRejectInfo');
            const rejectReasonEl = document.getElementById('detRejectReason');
            const rejecterEl = document.getElementById('detRejecter');
            if (reading.status === 'rejected') {
                rejectReasonEl.textContent = reading.reject_reason || 'Không rõ lý do';
                rejecterEl.textContent = reading.rejecter_name || 'Kế toán viên';
                rejectInfoEl.style.display = 'block';
            } else {
                rejectInfoEl.style.display = 'none';
            }

            // Image gallery container
            const imgContainer = document.getElementById('detImageContainer');
            const imgsUrls = reading.images_urls || (reading.image_proof_url ? [reading.image_proof_url] : []);

            imgContainer.innerHTML = '';
            if (imgsUrls.length > 0) {
                const label = document.createElement('div');
                label.style.cssText = 'font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;margin-bottom:10px;';
                label.textContent = `📷 Ảnh công tơ minh chứng (${imgsUrls.length} ảnh)`;
                imgContainer.appendChild(label);

                const galleryWrap = document.createElement('div');
                galleryWrap.style.cssText = 'display:flex;flex-wrap:wrap;gap:10px;justify-content:center;';

                imgsUrls.forEach((url, idx) => {
                    const thumb = document.createElement('img');
                    thumb.src = url;
                    thumb.alt = `Ảnh ${idx+1}`;
                    thumb.style.cssText = 'width:130px;height:130px;object-fit:cover;border-radius:9px;border:2px solid #e2e8f0;cursor:zoom-in;transition:transform .15s,box-shadow .15s;';
                    thumb.title = `Ảnh ${idx+1} – Click để xem to`;
                    thumb.addEventListener('mouseenter', function() {
                        this.style.transform = 'scale(1.05)';
                        this.style.boxShadow = '0 6px 20px rgba(0,0,0,.18)';
                    });
                    thumb.addEventListener('mouseleave', function() {
                        this.style.transform = '';
                        this.style.boxShadow = '';
                    });
                    thumb.addEventListener('click', function() {
                        // Open in new tab for fullscreen view
                        window.open(url, '_blank');
                    });
                    galleryWrap.appendChild(thumb);
                });
                imgContainer.appendChild(galleryWrap);
                imgContainer.style.display = 'block';
            } else {
                imgContainer.style.display = 'none';
            }

            // Print / Link tab
    document.getElementById('detLinkPrint').href = `{{ url('admin/utility-readings') }}/${reading.id}`;

            loading.style.display = 'none';
            content.style.display = 'block';
        }
    })
    .catch(err => {
        console.error('Error fetching detail:', err);
        closeDetailModal();
    });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('active');
}

window.confirmAndReject = function(id) {
    const reason = prompt("Nhập lý do từ chối chỉ số này:");
    if (reason === null) return; // Hủy bỏ
    if (reason.trim() === "") {
        alert("Vui lòng cung cấp lý do từ chối.");
        return;
    }
    document.getElementById('reject-reason-' + id).value = reason.trim();
    document.getElementById('reject-form-' + id).submit();
}
</script>
@endpush
