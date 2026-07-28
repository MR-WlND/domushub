@extends('layouts.admin.master')

@section('page_title', 'Chốt số Điện Nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>Chốt số Nước</h1>
        <p>Quản lý chỉ số nước theo tháng – Tháng {{ $month }}/{{ $year }}</p>
    </div>
    <div class="util-header-actions">
        @if(auth()->user()->role === 'admin')
        <a href="{{ portal_route('utility-logs.index') }}" class="util-btn util-btn--outline" style="background:#fff; color:#475569; border:1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Lịch sử
        </a>
        @endif
        @if(auth()->user()->role === 'technician')
        <a href="{{ portal_route('utility-readings.batch') }}" class="util-btn util-btn--secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Ghi hàng loạt
        </a>
        <a href="{{ portal_route('utility-readings.create') }}" class="util-btn util-btn--primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ghi đơn lẻ
        </a>
        @endif
    </div>
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
    <form action="{{ portal_route('utility-readings.index') }}" method="GET">
        <div class="util-filter-grid util-filter-grid--index">
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
                <label>Tháng</label>
                <select name="month" class="util-form-input">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label>Năm</label>
                <select name="year" class="util-form-input">
                    @for ($y = date('Y') + 1; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="util-filter-actions">
                <button type="submit" class="util-btn util-btn--primary util-btn--sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3H2l4 6.28V17l4 2v-7.72L16 3z" />
                    </svg>
                    Lọc
                </button>
                <a href="{{ portal_route('utility-readings.index') }}" class="util-btn util-btn--outline util-btn--sm">Đặt lại</a>
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
        <form action="{{ portal_route('utility-readings.batch-approve') }}" method="POST" id="batchApproveForm" style="margin:0; display:flex; gap:10px;">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="util-btn util-btn--primary util-btn--sm" style="background:#15803d; border-color:#15803d; font-size:12px; height:32px; padding:0 12px;">
                Duyệt các mục đã chọn
            </button>
        </form>
    </div>
    @endif
    <div class="util-table-controls" style="padding: 10px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: flex-end; align-items: center; background: #f8fafc; gap: 8px;">
        <label class="util-toggle-label" style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer; user-select: none; margin: 0;">
            <input type="checkbox" id="toggleProofImages" style="width: 16px; height: 16px; accent-color: #00236f; cursor: pointer;">
            Hiện ảnh công tơ thu nhỏ
        </label>
    </div>
    <div class="util-table-wrap">
        <table class="util-table">
            <thead>
                <tr>
                    @if(auth()->user()->role !== 'technician')
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="selectAllReadings" style="width:15px;height:15px;accent-color:#00236f;cursor:pointer;">
                    </th>
                    @endif
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
                    <th style="white-space: nowrap;">Thao tác</th>
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
                    
                    {{-- Kỳ ghi --}}
                    <td style="white-space:nowrap; font-weight:600; color:#00236f;">
                        T{{ $reading->record_month }}/{{ $reading->record_year }}
                    </td>

                    {{-- Căn hộ --}}
                    <td><span class="text-strong">{{ $reading->apartment->apartment_number ?? 'N/A' }}</span></td>

                    {{-- Tòa / Tầng --}}
                    <td class="text-muted">
                        {{ $reading->apartment->floor->block->name ?? '—' }}
                        / {{ $reading->apartment->floor->name ?? 'Tầng ' . ($reading->apartment->floor->floor_number ?? '') }}
                    </td>

                    {{-- Loại --}}
                    <td>
                        @if ($reading->type === 'electricity')
                            <span class="util-badge util-badge--electricity">Điện</span>
                        @else
                            <span class="util-badge util-badge--water">Nước</span>
                        @endif
                    </td>

                    {{-- Chỉ số cũ --}}
                    <td style="color:#64748b; font-size:13px;">{{ number_format($reading->old_value) }}</td>

                    {{-- Chỉ số mới --}}
                    <td style="font-weight:700; color:#0b1c30;">{{ number_format($reading->new_value) }}</td>

                    {{-- Tiêu thụ --}}
                    <td>
                        <strong style="color: #059669;">{{ number_format($reading->usage_amount) }}</strong>
                        <small style="color: #94a3b8;">{{ $reading->type === 'electricity' ? 'kWh' : 'm³' }}</small>
                    </td>

                    {{-- Người ghi --}}
                    <td class="text-muted">{{ $reading->recorder->name ?? '—' }}</td>

                    {{-- Ngày ghi --}}
                    <td style="font-size:11px; color:#64748b; white-space:nowrap;">
                        <div>{{ $reading->created_at->format('d/m/Y') }}</div>
                        <div>{{ $reading->created_at->format('H:i') }}</div>
                    </td>

                    {{-- Trạng thái --}}
                    <td style="text-align:center;">
                        @if($reading->status === 'approved')
                            @php
                                $approveLog = collect($reading->history_logs ?? [])->firstWhere('action', 'approved');
                                $approverName = $approveLog['user_name'] ?? 'Kế toán viên';
                                $approveTime = $approveLog['time'] ?? ($reading->updated_at ? $reading->updated_at->format('d/m/Y H:i') : '');
                                $tooltip = "Duyệt bởi: {$approverName} ({$approveTime})";
                            @endphp
                            <span class="util-badge util-badge--success" style="background:#e6f4ea; color:#137333; font-size:11px;" data-tooltip="{{ $tooltip }}">Đã chốt</span>
                        @elseif($reading->status === 'rejected')
                            @php
                                $rejectLog = collect($reading->history_logs ?? [])->firstWhere('action', 'rejected');
                                $rejecterName = $rejectLog['user_name'] ?? ($reading->rejecter->name ?? 'Kế toán viên');
                                $rejectTime = $rejectLog['time'] ?? ($reading->updated_at ? $reading->updated_at->format('d/m/Y H:i') : '');
                                $reason = $rejectLog['reason'] ?? ($reading->reject_reason ?? 'Không rõ lý do');
                                $tooltip = "Từ chối bởi: {$rejecterName} ({$rejectTime}). Lý do: {$reason}";
                            @endphp
                            <span class="util-badge util-badge--danger" style="background:#fce8e6; color:#c5221f; font-size:11px;" data-tooltip="{{ $tooltip }}">Bị từ chối</span>
                        @else
                            <span class="util-badge util-badge--warning" style="background:#fef7e0; color:#b06000; font-size:11px;">Chờ chốt</span>
                        @endif
                    </td>

                    {{-- Ảnh --}}
                    <td style="text-align:center;">
                        @php
                            $imgs = [];
                            if ($reading->image_proof) $imgs[] = asset('storage/' . $reading->image_proof);
                            if ($reading->images) {
                                foreach ($reading->images as $img) $imgs[] = asset('storage/' . $img);
                            }
                        @endphp
                        @if(count($imgs) > 0)
                            <span class="proof-photo-btn" data-img="{{ $imgs[0] }}" style="cursor:pointer; color:#0b57d0; display:inline-flex; align-items:center; vertical-align:middle;" title="Xem minh chứng công tơ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </span>
                            <div class="proof-thumbnail-container">
                                <div class="proof-photo-wrapper">
                                    <img src="{{ $imgs[0] }}" class="proof-thumbnail" data-img="{{ $imgs[0] }}" data-info="Phòng {{ $reading->apartment->apartment_number ?? 'N/A' }} - {{ $reading->type === 'electricity' ? 'Điện' : 'Nước' }}" alt="Proof">
                                </div>
                            </div>
                        @else
                            <span style="color:#cbd5e1; font-size:12px;">—</span>
                        @endif
                    </td>

                    {{-- Thao tác --}}
                    <td style="white-space: nowrap;">
                        <div class="util-actions">
                            @if(auth()->user()->role !== 'technician')
                                <button type="button" class="util-btn-view" onclick="openDetailModal({{ $reading->id }})" title="Xem chi tiết">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                                @if($reading->status === 'pending')
                                <form action="{{ portal_route('utility-readings.approve', $reading->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="util-btn-approve" title="Phê duyệt">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ portal_route('utility-readings.reject', $reading->id) }}" method="POST" style="margin:0;" id="reject-form-{{ $reading->id }}">
                                    @csrf
                                    <input type="hidden" name="reject_reason" id="reject-reason-{{ $reading->id }}">
                                    <button type="button" class="util-btn-reject" title="Từ chối" onclick="confirmAndReject({{ $reading->id }})">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if(auth()->user()->role === 'admin')
                                <a href="{{ portal_route('utility-readings.edit', $reading->id) }}"
                                    class="util-btn-edit" title="Sửa">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <form action="{{ portal_route('utility-readings.destroy', $reading->id) }}"
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
                                @endif
                            @else
                                <button type="button" class="util-btn-view" onclick="openDetailModal({{ $reading->id }})" title="Xem chi tiết">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                                @if(in_array($reading->status, ['pending', 'rejected']) && $reading->recorded_by === auth()->id())
                                <a href="{{ portal_route('utility-readings.edit', $reading->id) }}"
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
    @if(auth()->user()->role === 'technician')
    <div class="util-empty-actions">
        <a href="{{ portal_route('utility-readings.batch') }}" class="util-btn util-btn--secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Ghi hàng loạt
        </a>
        <a href="{{ portal_route('utility-readings.create') }}" class="util-btn util-btn--primary">+ Ghi đơn lẻ</a>
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
                Chi tiết chỉ số nước
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
                    <div id="detRejectInfo" style="grid-column: span 2; display: none;">
                        <div id="detRejectTableContainer" style="overflow-x: auto;">
                            <!-- Table will be dynamically injected here -->
                        </div>
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

{{-- ── Reject Reason Modal ─────────────────────────────── --}}
<div class="util-modal-backdrop" id="rejectModal">
    <div class="util-modal" style="max-width: 500px;">
        <div class="util-modal-header">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:6px; color:#dc2626;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Từ chối chỉ số nước
            </h3>
            <button class="util-modal-close" onclick="closeRejectModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body" style="padding: 20px 24px;">
            <div style="margin-bottom: 16px;">
                <label for="rejectReasonInput" style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 8px;">Nhập lý do từ chối chỉ số này:</label>
                <textarea id="rejectReasonInput" class="util-form-input" rows="3" style="height: auto; min-height: 90px; resize: vertical; padding: 12px;" placeholder="Ví dụ: Chỉ số nhập không chính xác hoặc ảnh mờ..."></textarea>
                <div id="rejectReasonError" style="color: #dc2626; font-size: 12px; margin-top: 6px; display: none; font-weight: 500;">
                    Vui lòng nhập lý do từ chối.
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f3f4f6; padding-top: 16px; margin-top: 20px;">
                <button type="button" class="util-btn util-btn--outline util-btn--sm" onclick="closeRejectModal()">Hủy</button>
                <button type="button" class="util-btn util-btn--primary util-btn--sm" style="background:#dc2626; border-color:#dc2626;" onclick="submitRejectForm()">Từ chối</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

/* Style overrides to prevent layout overflow and button clipping */
.dashboard-main,
.dashboard-content {
    min-width: 0 !important;
}
.util-actions {
    white-space: nowrap !important;
    flex-shrink: 0 !important;
    display: flex !important;
    gap: 4px !important;
    align-items: center !important;
}
.util-actions form {
    display: inline-flex !important;
    margin: 0 !important;
}

/* Premium Tooltip */
.util-badge[data-tooltip] {
    position: relative;
    cursor: help;
}
.util-badge[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 130%;
    left: 50%;
    transform: translateX(-50%) scale(0.95);
    background: #1e293b;
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.4;
    text-align: center;
    width: max-content;
    max-width: 260px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease;
    z-index: 999;
    white-space: normal;
}
.util-badge[data-tooltip]:hover::after {
    opacity: 1;
    transform: translateX(-50%) scale(1);
}
.util-badge[data-tooltip]::before {
    content: '';
    position: absolute;
    bottom: 118%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 5px;
    border-style: solid;
    border-color: #1e293b transparent transparent transparent;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
    z-index: 999;
}
.util-badge[data-tooltip]:hover::before {
    opacity: 1;
}
</style>

{{-- ── Global Proof Preview tooltip ─────────────────── --}}
<div id="globalProofPreview">
    <div class="proof-hover-header"></div>
    <img src="" alt="Xem trước">
</div>

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

    const previewEl = document.getElementById('globalProofPreview');
    const previewImg = previewEl ? previewEl.querySelector('img') : null;
    const previewHeader = previewEl ? previewEl.querySelector('.proof-hover-header') : null;

    function positionPreview(target, preview) {
        const rect = target.getBoundingClientRect();
        const previewWidth = 250; // width of preview element
        const previewHeight = 280; // approximate max height including padding & header

        let left = rect.left + (rect.width / 2) - (previewWidth / 2);
        let top = rect.top - previewHeight - 10;

        // Check window boundaries
        if (left < 10) left = 10;
        if (left + previewWidth > window.innerWidth - 10) {
            left = window.innerWidth - previewWidth - 10;
        }
        if (top < 10) {
            // Show below target if it goes off top of screen
            top = rect.bottom + 10;
        }

        preview.style.left = left + 'px';
        preview.style.top = top + 'px';
    }

    document.querySelectorAll('.proof-photo-btn, .proof-thumbnail').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const imgUrl = this.dataset.img;
            if (imgUrl) {
                document.getElementById('proofModalImg').src = imgUrl;
                document.getElementById('proofModal').classList.add('active');
            }
        });

        // Add hover preview to thumbnails
        if (btn.classList.contains('proof-thumbnail') && previewEl && previewImg && previewHeader) {
            btn.addEventListener('mouseenter', function(e) {
                const imgUrl = this.dataset.img;
                const info = this.dataset.info || 'Minh chứng công tơ';
                previewImg.src = imgUrl;
                previewHeader.textContent = info;
                
                positionPreview(this, previewEl);
                previewEl.classList.add('active');
            });

            btn.addEventListener('mousemove', function(e) {
                positionPreview(this, previewEl);
            });

            btn.addEventListener('mouseleave', function() {
                previewEl.classList.remove('active');
            });
        }
    });

    // Toggle showing proof thumbnails
    const toggleProofImages = document.getElementById('toggleProofImages');
    const tableWrap = document.querySelector('.util-table-wrap');

    if (toggleProofImages && tableWrap) {
        // Load state from localStorage, default to true for convenience
        const showImagesState = localStorage.getItem('domushub_show_proof_images');
        if (showImagesState === 'false') {
            toggleProofImages.checked = false;
            tableWrap.classList.remove('show-proof-thumbnails');
        } else {
            toggleProofImages.checked = true;
            tableWrap.classList.add('show-proof-thumbnails');
        }

        toggleProofImages.addEventListener('change', function() {
            if (this.checked) {
                tableWrap.classList.add('show-proof-thumbnails');
                localStorage.setItem('domushub_show_proof_images', 'true');
            } else {
                tableWrap.classList.remove('show-proof-thumbnails');
                localStorage.setItem('domushub_show_proof_images', 'false');
            }
        });
    }

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
            const rejectTableContainer = document.getElementById('detRejectTableContainer');
            const rejections = data.rejections || [];
            
            const hasRejection = rejections.some(rej => rej.action === 'rejected');
            
            if (reading.status === 'approved') {
                if (rejections.length > 0) {
                    let html = `
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; margin-top: 8px;">
                            <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 12px 16px; display: flex; align-items: center;">
                                <div style="background: #f1f5f9; color: #475569; border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 13px;">
                                    <i class="fas fa-history"></i>
                                </div>
                                <span style="font-size: 13px; font-weight: 700; color: #1e293b;">Lịch sử phê duyệt</span>
                            </div>
                            <div style="overflow-x: auto;">
                                <table style="width:100%; border-collapse:collapse; font-size:12px; text-align:left; vertical-align: middle;">
                                    <thead>
                                        <tr style="background: #f8fafc; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em; font-weight: 700;">
                                            <th style="padding: 8px 12px; width: 30%;">Thời gian</th>
                                            <th style="padding: 8px 12px; width: 30%;">Người thực hiện</th>
                                            <th style="padding: 8px 12px; width: 40%;">Hành động / Chi tiết</th>
                                        </tr>
                                    </thead>
                                    <tbody style="color: #334155;">
                    `;
                    rejections.forEach(rej => {
                        if (rej.action === 'approved') {
                            html += `
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; color: #94a3b8; font-weight: 400; white-space: nowrap;">${rej.rejected_at}</td>
                                    <td style="padding: 10px 12px; font-weight: 500; color: #64748b;">${rej.rejecter_name}</td>
                                    <td style="padding: 10px 12px;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #e6f4ea; border: 1px solid #a3cfbb; color: #146c43; font-weight: 700; font-size: 11px;">
                                            <i class="fas fa-check-circle" style="font-size: 10px;"></i>
                                            <span>Đã chốt số</span>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        } else {
                            html += `
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px 12px; color: #94a3b8; font-weight: 400; white-space: nowrap;">${rej.rejected_at}</td>
                                    <td style="padding: 10px 12px; font-weight: 500; color: #64748b;">${rej.rejecter_name}</td>
                                    <td style="padding: 10px 12px;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; font-weight: 700; font-size: 11px;">
                                            <i class="fas fa-exclamation-triangle" style="font-size: 10px;"></i>
                                            <span>Từ chối: ${rej.reason}</span>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }
                    });
                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    rejectTableContainer.innerHTML = html;
                    rejectInfoEl.style.display = 'block';
                } else {
                    rejectInfoEl.style.display = 'none';
                }
            } else {
                const rejectedLogs = rejections.filter(rej => rej.action === 'rejected');
                if (rejectedLogs.length > 0) {
                    let html = `
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; margin-top: 8px;">
                            <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 12px 16px; display: flex; align-items: center;">
                                <div style="background: #fee2e2; color: #ef4444; border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 13px;">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <span style="font-size: 13px; font-weight: 700; color: #1e293b;">Lý do từ chối</span>
                            </div>
                            <div style="overflow-x: auto;">
                                <table style="width:100%; border-collapse:collapse; font-size:12px; text-align:left; vertical-align: middle;">
                                    <thead>
                                        <tr style="background: #f8fafc; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em; font-weight: 700;">
                                            <th style="padding: 8px 12px; width: 30%;">Thời gian</th>
                                            <th style="padding: 8px 12px; width: 30%;">Người từ chối</th>
                                            <th style="padding: 8px 12px; width: 40%;">Lý do</th>
                                        </tr>
                                    </thead>
                                    <tbody style="color: #334155;">
                    `;
                    rejectedLogs.forEach(rej => {
                        html += `
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 10px 12px; color: #94a3b8; font-weight: 400; white-space: nowrap;">${rej.rejected_at}</td>
                                <td style="padding: 10px 12px; font-weight: 500; color: #64748b;">${rej.rejecter_name}</td>
                                <td style="padding: 10px 12px;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; font-weight: 700; font-size: 11px;">
                                        <i class="fas fa-exclamation-triangle" style="font-size: 10px;"></i>
                                        <span>${rej.reason}</span>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    rejectTableContainer.innerHTML = html;
                    rejectInfoEl.style.display = 'block';
                } else if (reading.status === 'rejected') {
                    rejectTableContainer.innerHTML = `
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden; margin-top: 8px;">
                            <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 12px 16px; display: flex; align-items: center;">
                                <div style="background: #fee2e2; color: #ef4444; border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; margin-right: 10px; font-size: 13px;">
                                    <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <span style="font-size: 13px; font-weight: 700; color: #1e293b;">Lý do từ chối</span>
                            </div>
                            <div style="padding: 16px; font-size:13px; color:#334155;">
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Thời gian: ${reading.updated_at || ''}</div>
                                <div style="font-size: 12px; color: #64748b; margin-bottom: 8px;">Người từ chối: <strong style="color: #0f172a;">${reading.rejecter_name || 'Kế toán viên'}</strong></div>
                                <span style="display: inline-block; background: #fee2e2; color: #b91c1c; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                    ${reading.reject_reason || 'Không rõ lý do'}
                                </span>
                            </div>
                        </div>
                    `;
                    rejectInfoEl.style.display = 'block';
                } else {
                    rejectInfoEl.style.display = 'none';
                }
            }

            // Image gallery container
            const imgContainer = document.getElementById('detImageContainer');
            const imgsUrls = reading.images_urls || (reading.image_proof_url ? [reading.image_proof_url] : []);

            imgContainer.innerHTML = '';
            if (imgsUrls.length > 0) {
                const label = document.createElement('div');
                label.style.cssText = 'font-size:11px;font-weight:600;color:#94a3b8;text-transform:uppercase;margin-bottom:10px;';
                label.textContent = `Ảnh công tơ minh chứng (${imgsUrls.length} ảnh)`;
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

let pendingRejectReadingId = null;

window.confirmAndReject = function(id) {
    pendingRejectReadingId = id;
    const modal = document.getElementById('rejectModal');
    const textarea = document.getElementById('rejectReasonInput');
    if (!modal || !textarea) return;
    textarea.value = '';
    const err = document.getElementById('rejectReasonError');
    if (err) err.style.display = 'none';
    modal.classList.add('active');
    setTimeout(() => textarea.focus(), 100);
}

window.closeRejectModal = function() {
    const modal = document.getElementById('rejectModal');
    if (modal) modal.classList.remove('active');
    pendingRejectReadingId = null;
}

window.submitRejectForm = function(event) {
    if (event) event.preventDefault();
    if (!pendingRejectReadingId) return;
    const id = pendingRejectReadingId;
    const textarea = document.getElementById('rejectReasonInput');
    const reason = textarea ? textarea.value.trim() : '';
    if (reason === "") {
        const err = document.getElementById('rejectReasonError');
        if (err) err.style.display = 'block';
        return;
    }
    
    const hiddenInput = document.getElementById('reject-reason-' + id);
    const form = document.getElementById('reject-form-' + id);
    if (hiddenInput && form) {
        hiddenInput.value = reason;
        form.submit();
    }
    closeRejectModal();
}
</script>
@endpush
