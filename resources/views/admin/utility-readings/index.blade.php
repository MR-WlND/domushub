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
    @if(auth()->user()->role !== 'staff')
    <div class="util-header-actions">
        <button type="button" class="util-btn util-btn--outline" onclick="openImportModal()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Nhập từ Excel/CSV
        </button>
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
                    @if(auth()->user()->role !== 'technician')
                    <th>Chỉ số cũ</th>
                    @endif
                    <th>Chỉ số mới</th>
                    @if(auth()->user()->role !== 'technician')
                    <th>Tiêu thụ</th>
                    @endif
                    <th>Trạng thái</th>
                    <th>Người ghi</th>
                    @if(auth()->user()->role !== 'technician')
                    <th>Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($readings as $index => $reading)
                <tr>
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
                            <span class="util-badge util-badge--electricity">⚡ Điện</span>
                        @else
                            <span class="util-badge util-badge--water">💧 Nước</span>
                        @endif
                    </td>
                    @if(auth()->user()->role !== 'technician')
                    <td class="text-muted">{{ number_format($reading->old_value) }}</td>
                    @endif
                    <td class="text-strong">
                        {{ number_format($reading->new_value) }}
                        @if($reading->image_proof)
                        <span class="proof-photo-btn" data-img="{{ asset('storage/' . $reading->image_proof) }}" style="cursor:pointer; margin-left:6px; font-size:14px;" title="Xem minh chứng công tơ">
                            📷
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
                            <span class="util-badge util-badge--success" style="background:#e6f4ea; color:#137333; font-size:11px;">✅ Đã chốt</span>
                        @else
                            <span class="util-badge util-badge--warning" style="background:#fef7e0; color:#b06000; font-size:11px;">⏳ Chờ chốt</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $reading->recorder->name ?? '—' }}</td>
                    @if(auth()->user()->role !== 'technician')
                    <td>
                        <div class="util-actions">
                            @if($reading->status === 'pending')
                            <form action="{{ route('admin.utility-readings.approve', $reading->id) }}" method="POST" style="margin:0;">
                                @csrf
                                <button type="submit" class="util-btn util-btn--primary util-btn--xs" style="padding: 4px 8px; font-size:11px; height:auto; background:#15803d; border-color:#15803d; line-height:1;" title="Phê duyệt">
                                    Duyệt
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
                        </div>
                    </td>
                    @endif
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
        <a href="{{ route('admin.utility-readings.batch') }}" class="util-btn util-btn--secondary">⚡ Ghi hàng loạt</a>
        <a href="{{ route('admin.utility-readings.create') }}" class="util-btn util-btn--primary">+ Ghi đơn lẻ</a>
    </div>
    @endif
</div>
@endif

{{-- ── Import Modal ────────────────────────────────────── --}}
<div class="util-modal-backdrop" id="importModal">
    <div class="util-modal">
        <div class="util-modal-header">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:6px;">
                    <path d="M12 10v6m0 0-3-3m3 3 3-3M3 17V7a2 2 0 0 1 2-2h6l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Nhập chỉ số từ Excel / CSV
            </h3>
            <button class="util-modal-close" onclick="closeImportModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body">
            {{-- 1. Download template option --}}
            <div class="util-template-box">
                <div class="util-template-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                        <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/>
                    </svg>
                    Tải File Mẫu Tiện Lợi
                </div>
                <div class="util-template-desc">
                    Hệ thống sẽ chuẩn bị sẵn danh sách căn hộ kèm **chỉ số cũ** của kỳ trước để bạn dễ dàng điền chỉ số mới.
                </div>
                <div class="util-template-select-row">
                    <div>
                        <select id="template_month">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <select id="template_year">
                            @for ($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>Năm {{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" class="util-template-btn" onclick="downloadTemplateFile()">
                        Tải mẫu (.xlsx)
                    </button>
                </div>
            </div>

            {{-- 2. Upload Form --}}
            <form action="{{ route('admin.utility-readings.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <input type="file" name="csv_file" id="csv_file" accept=".xlsx,.xls,.csv,text/csv,text/plain" style="display: none;" onchange="handleFileSelect(this)">
                
                <div class="util-form-group" style="margin-bottom: 20px;">
                    <label class="util-form-label">Tháng / Năm áp dụng chỉ số <span class="required">*</span></label>
                    <div style="display: flex; gap: 12px; margin-top: 4px;">
                        <div style="flex: 1;">
                            <select name="import_month" id="import_month" class="util-form-input">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                        <div style="flex: 1;">
                            <select name="import_year" id="import_year" class="util-form-input">
                                @for ($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>Năm {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="util-drag-zone" id="dropZone" onclick="document.getElementById('csv_file').click()">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 16v-8m0 8-4-4m4 4 4-4M3 15v3a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-3"/>
                    </svg>
                    <div class="util-drag-text">Kéo thả file Excel hoặc CSV vào đây hoặc <span>chọn từ máy tính</span></div>
                    <div class="util-drag-sub">Hỗ trợ file .xlsx, .xls hoặc .csv dung lượng tối đa 4MB</div>
                </div>

                <div class="util-file-preview" id="filePreview">
                    <div class="util-file-info">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="color: #10b981; display:inline-block; vertical-align:middle; margin-right:4px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        <span id="fileNameDisplay">file_name.csv</span>
                    </div>
                    <span class="util-file-remove" onclick="removeSelectedFile(event)">Xóa</span>
                </div>

                <div class="util-form-actions" style="margin-top: 24px; padding-top: 18px;">
                    <button type="submit" class="util-btn util-btn--primary" id="btnSubmitImport" disabled style="width: 100%; justify-content: center;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                            <path d="M5 13l4 4L19 7"/>
                        </svg>
                        Bắt đầu nhập dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Proof Modal ────────────────────────────────────── --}}
<div class="util-modal-backdrop" id="proofModal">
    <div class="util-modal" style="max-width: 540px;">
        <div class="util-modal-header">
            <h3>📷 Ảnh chụp công tơ minh chứng</h3>
            <button class="util-modal-close" onclick="closeProofModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body" style="text-align: center; padding: 20px;">
            <img id="proofModalImg" src="" alt="Ảnh minh chứng" style="max-width: 100%; max-height: 480px; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Modal toggling
function openImportModal() {
    document.getElementById('importModal').classList.add('active');
}

function closeImportModal() {
    document.getElementById('importModal').classList.remove('active');
    // Clear selected file if any
    removeSelectedFile(null);
}

// Download template with dynamic month/year parameters
function downloadTemplateFile() {
    const month = document.getElementById('template_month').value;
    const year = document.getElementById('template_year').value;
    window.location.href = `{{ route('admin.utility-readings.import-template') }}?month=${month}&year=${year}`;
}

// File preview and selection
function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        document.getElementById('fileNameDisplay').textContent = file.name;
        document.getElementById('filePreview').style.display = 'flex';
        document.getElementById('btnSubmitImport').disabled = false;
    }
}

function removeSelectedFile(e) {
    if (e) e.stopPropagation();
    document.getElementById('csv_file').value = '';
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('btnSubmitImport').disabled = true;
}

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

    // Drag and drop events
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('csv_file');

    if (dropZone && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length) {
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        }, false);
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
});
</script>
@endpush
