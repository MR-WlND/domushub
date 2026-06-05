@extends('layouts.admin.master')

@section('page_title', 'Chốt số Điện Nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>⚡ Chốt số Điện Nước</h1>
        <p>Quản lý chỉ số điện nước theo tháng – Tháng {{ $month }}/{{ $year }}</p>
    </div>
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
</div>

{{-- ── Flash Message ───────────────────────────────── --}}
@if (session('success'))
    <div class="util-alert--success">✅ {{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="util-alert--danger">❌ {{ session('error') }}</div>
@endif

{{-- ── Stat Cards ──────────────────────────────────── --}}
<div class="util-stats-grid">
    <div class="util-stat-card">
        <div class="util-stat-card__label">📋 Tổng bản ghi</div>
        <div class="util-stat-card__value">{{ number_format($stats['total_records']) }}</div>
    </div>
    <div class="util-stat-card" style="border-top: 3px solid #f59e0b;">
        <div class="util-stat-card__label">⚡ Tổng tiêu thụ Điện</div>
        <div class="util-stat-card__value">
            {{ number_format($stats['total_electricity']) }}
            <small>kWh</small>
        </div>
    </div>
    <div class="util-stat-card" style="border-top: 3px solid #3b82f6;">
        <div class="util-stat-card__label">💧 Tổng tiêu thụ Nước</div>
        <div class="util-stat-card__value">
            {{ number_format($stats['total_water']) }}
            <small>m³</small>
        </div>
    </div>
    <div class="util-stat-card" style="border-top: 3px solid #10b981;">
        <div class="util-stat-card__label">🏠 Căn hộ đã chốt</div>
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
                        <option value="{{ $floor->id }}" {{ $floorId == $floor->id ? 'selected' : '' }}>
                            {{ $floor->block->name ?? '' }} – {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Loại</label>
                <select name="type">
                    <option value="">Tất cả</option>
                    <option value="electricity" {{ $type == 'electricity' ? 'selected' : '' }}>⚡ Điện</option>
                    <option value="water" {{ $type == 'water' ? 'selected' : '' }}>💧 Nước</option>
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
    <div class="util-table-wrap">
        <table class="util-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Phòng</th>
                    <th>Tòa / Tầng</th>
                    <th>Loại</th>
                    <th>Chỉ số cũ</th>
                    <th>Chỉ số mới</th>
                    <th>Tiêu thụ</th>
                    <th>Người ghi</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($readings as $index => $reading)
                <tr>
                    <td class="text-muted">{{ $readings->firstItem() + $index }}</td>
                    <td><span class="text-strong">{{ $reading->apartment->apartment_number ?? 'N/A' }}</span></td>
                    <td class="text-muted">
                        {{ $reading->apartment->floor->block->name ?? '—' }}
                        / {{ $reading->apartment->floor->name ?? 'Tầng ' . ($reading->apartment->floor->floor_number ?? '') }}
                    </td>
                    <td>
                        @if ($reading->type === 'electricity')
                            <span class="util-badge util-badge--electricity">⚡ Điện</span>
                        @else
                            <span class="util-badge util-badge--water">💧 Nước</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ number_format($reading->old_value) }}</td>
                    <td class="text-strong">{{ number_format($reading->new_value) }}</td>
                    <td>
                        <strong style="color: #00236f;">{{ number_format($reading->usage_amount) }}</strong>
                        <small style="color: #94a3b8;">{{ $reading->type === 'electricity' ? 'kWh' : 'm³' }}</small>
                    </td>
                    <td class="text-muted">{{ $reading->recorder->name ?? '—' }}</td>
                    <td>
                        <div class="util-actions">
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
    <div class="util-empty-actions">
        <a href="{{ route('admin.utility-readings.batch') }}" class="util-btn util-btn--secondary">⚡ Ghi hàng loạt</a>
        <a href="{{ route('admin.utility-readings.create') }}" class="util-btn util-btn--primary">+ Ghi đơn lẻ</a>
    </div>
</div>
@endif

@endsection
