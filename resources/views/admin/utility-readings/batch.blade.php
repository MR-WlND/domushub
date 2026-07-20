@extends('layouts.admin.master')

@section('page_title', 'Ghi chỉ số hàng loạt – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
<style>
/* ── Batch table two-column layout ─────────────── */
.batch-meter-input {
    width: 130px;
    height: 38px;
    padding: 8px 12px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    color: #1e293b;
    background: #ffffff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
}
.batch-meter-input:focus {
    border-color: #0b57d0;
    box-shadow: 0 0 0 3px rgba(11,87,208,.08);
}
.batch-meter-input.elec-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
.batch-meter-input.water-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.batch-meter-input:disabled { background: #f8fafc; cursor: not-allowed; opacity: .55; }
.batch-meter-input.input-invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,.1) !important; }

.usage-chip {
    display: inline-block;
    min-width: 52px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-align: center;
}
.usage-chip--elec  { background: #fef3c7; color: #92400e; }
.usage-chip--water { background: #dbeafe; color: #1e40af; }
.usage-chip--zero  { background: #f1f5f9; color: #94a3b8; }

.col-divider {
    border-left: 2px dashed #e2e8f0;
    padding-left: 14px;
    margin-left: 6px;
}

.done-icon { font-size: 18px; line-height: 1; }

/* ── Color Grouping & Borders ─────────────────── */
.col-elec {
    background-color: #fffbeb !important;
}
.col-water {
    background-color: #eff6ff !important;
}
.col-divider-border {
    border-right: 3px solid #94a3b8 !important;
}

.batch-row--all-done td { background: #f0fdf4; }
.batch-row--all-done td.col-elec,
.batch-row--all-done td.col-water {
    background-color: #f0fdf4 !important;
}
.batch-row--all-done .batch-meter-input { display: none; }
</style>
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>Ghi chỉ số hàng loạt</h1>
        <p>Chốt cả <strong>điện</strong> và <strong>nước</strong> cho nhiều căn hộ cùng lúc</p>
    </div>
    <a href="{{ portal_route('utility-readings.index') }}" class="util-btn util-btn--outline">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Quay lại danh sách
    </a>
</div>

{{-- ── Error Alert ─────────────────────────────────── --}}
@if (isset($errors) && $errors->any())
    <div class="util-alert--danger">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

{{-- ── Filter Panel ────────────────────────────────── --}}
<div class="util-filter-panel">
    <form action="{{ portal_route('utility-readings.batch') }}" method="GET" id="filterForm">
        <div class="util-filter-grid">
            <div>
                <label>Tòa nhà</label>
                <select name="block_id" onchange="this.form.submit()">
                    <option value="">— Chọn tòa nhà —</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}" {{ $selectedBlockId == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tầng</label>
                <select name="floor_id" onchange="this.form.submit()">
                    <option value="">Tất cả tầng</option>
                    @foreach ($floors as $floor)
                        @if (!$selectedBlockId || $floor->block_id == $selectedBlockId)
                            <option value="{{ $floor->id }}" {{ $selectedFloorId == $floor->id ? 'selected' : '' }}>
                                {{ $floor->block->name ?? '' }} – {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tháng</label>
                <input type="number" name="month" value="{{ $selectedMonth }}" min="1" max="12">
            </div>
            <div>
                <label>Năm</label>
                <input type="number" name="year" value="{{ $selectedYear }}" min="2020" max="2100">
            </div>
            <div class="util-filter-actions">
                <button type="submit" class="util-btn util-btn--primary util-btn--sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Tải
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ── Main content ────────────────────────────────── --}}
@if (count($apartmentData) > 0)
@php
    $total      = count($apartmentData);
    $elecDone   = collect($apartmentData)->where('elec_recorded', true)->count();
    $waterDone  = collect($apartmentData)->where('water_recorded', true)->count();
    $bothDone   = collect($apartmentData)->where('both_recorded', true)->count();
    $elecPct    = $total > 0 ? round($elecDone / $total * 100) : 0;
    $waterPct   = $total > 0 ? round($waterDone / $total * 100) : 0;
@endphp

{{-- ── Progress summary ────────────────────────────── --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 12px; margin-bottom: 20px;">

    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:16px 20px;">
        <div style="font-size:12px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Điện – Tháng {{ $selectedMonth }}/{{ $selectedYear }}</div>
        <div style="font-size:26px;font-weight:800;color:#b45309;">{{ $elecDone }}<span style="font-size:16px;font-weight:600;color:#d97706;"> / {{ $total }}</span></div>
        <div style="margin-top:8px;background:#e2e8f0;border-radius:999px;height:6px;overflow:hidden;">
            <div style="height:100%;width:{{ $elecPct }}%;background:linear-gradient(90deg,#f59e0b,#d97706);border-radius:999px;"></div>
        </div>
        <div style="font-size:12px;color:#92400e;margin-top:4px;">{{ $elecPct }}% hoàn thành</div>
    </div>

    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 20px;">
        <div style="font-size:12px;font-weight:700;color:#1e40af;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Nước – Tháng {{ $selectedMonth }}/{{ $selectedYear }}</div>
        <div style="font-size:26px;font-weight:800;color:#1d4ed8;">{{ $waterDone }}<span style="font-size:16px;font-weight:600;color:#3b82f6;"> / {{ $total }}</span></div>
        <div style="margin-top:8px;background:#e2e8f0;border-radius:999px;height:6px;overflow:hidden;">
            <div style="height:100%;width:{{ $waterPct }}%;background:linear-gradient(90deg,#3b82f6,#1d4ed8);border-radius:999px;"></div>
        </div>
        <div style="font-size:12px;color:#1e40af;margin-top:4px;">{{ $waterPct }}% hoàn thành</div>
    </div>

    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;">
        <div style="font-size:12px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px;">Hoàn tất cả 2</div>
        <div style="font-size:26px;font-weight:800;color:#15803d;">{{ $bothDone }}<span style="font-size:16px;font-weight:600;color:#22c55e;"> / {{ $total }}</span></div>
        <div style="margin-top:8px;background:#e2e8f0;border-radius:999px;height:6px;overflow:hidden;">
            <div style="height:100%;width:{{ $total > 0 ? round($bothDone/$total*100) : 0 }}%;background:linear-gradient(90deg,#10b981,#059669);border-radius:999px;"></div>
        </div>
        <div style="font-size:12px;color:#166534;margin-top:4px;">{{ $total > 0 ? round($bothDone/$total*100) : 0 }}% hoàn thành</div>
    </div>

    <div style="background:#f8faff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 20px;display:flex;flex-direction:column;justify-content:center;gap:6px;">
        <div style="font-size:13px;font-weight:600;color:#334155;">Tổng căn hộ: <strong>{{ $total }}</strong></div>
        <div style="font-size:13px;color:#64748b;">Chưa chốt điện: <strong style="color:#f59e0b;">{{ $total - $elecDone }}</strong></div>
        <div style="font-size:13px;color:#64748b;">Chưa chốt nước: <strong style="color:#3b82f6;">{{ $total - $waterDone }}</strong></div>
    </div>
</div>

{{-- ── Batch Form ───────────────────────────────────── --}}
<form action="{{ portal_route('utility-readings.batch.store') }}" method="POST" id="batchForm" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="record_month" value="{{ $selectedMonth }}">
    <input type="hidden" name="record_year"  value="{{ $selectedYear }}">

    <div class="util-table-card">
        {{-- Table header bar --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #e2e8f0;flex-wrap:wrap;gap:10px;">
            <div style="font-weight:700;color:#0f172a;font-size:14px;">
                Danh sách căn hộ – Tháng {{ $selectedMonth }}/{{ $selectedYear }}
            </div>
            <div style="display:flex;align-items:center;gap:16px;font-size:13px;color:#64748b;">
                <span>Cột vàng = Điện &nbsp;|&nbsp; Cột xanh = Nước</span>
                <label style="display:flex;align-items:center;gap:6px;font-weight:600;cursor:pointer;">
                    <input type="checkbox" id="selectAll" checked style="width:15px;height:15px;accent-color:#00236f;cursor:pointer;">
                    Chọn tất cả
                </label>
            </div>
        </div>

        <div class="util-table-wrap">
            <table class="util-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:40px; text-align:center;"></th>
                        <th rowspan="2" style="width:80px; text-align:center;">PHÒNG</th>
                        <th rowspan="2" style="width:150px; text-align:center;">TÒA / TẦNG</th>
                        <th colspan="{{ auth()->user()->role === 'technician' ? '1' : '3' }}" class="col-elec col-divider-border" style="text-align:center; font-weight:700; color:#92400e;">
                            ĐIỆN — THÁNG {{ $selectedMonth }}/{{ $selectedYear }}
                        </th>
 
                        {{-- Water Header Group --}}
                        <th colspan="{{ auth()->user()->role === 'technician' ? '1' : '3' }}" class="col-water" style="text-align:center; font-weight:700; color:#1e40af;">
                            NƯỚC — THÁNG {{ $selectedMonth }}/{{ $selectedYear }}
                        </th>
 
                        <th rowspan="2" style="width:140px; text-align:center; vertical-align:middle;">Ảnh công tơ</th>
                        <th rowspan="2" style="min-width:90px; vertical-align:middle; text-align:center;">Trạng thái</th>
                    </tr>
                    <tr>
                        @if(auth()->user()->role !== 'technician')
                        <th class="col-elec" style="text-align:center; font-size:12px; font-weight:600; color:#92400e;">CS Cũ</th>
                        @endif
                        <th class="col-elec {{ auth()->user()->role === 'technician' ? 'col-divider-border' : '' }}" style="text-align:center; font-size:12px; font-weight:600; color:#92400e;">CS Mới</th>
                        @if(auth()->user()->role !== 'technician')
                        <th class="col-elec col-divider-border" style="text-align:center; font-size:12px; font-weight:600; color:#92400e;">Tiêu thụ</th>
                        @endif
 
                        @if(auth()->user()->role !== 'technician')
                        <th class="col-water" style="text-align:center; font-size:12px; font-weight:600; color:#1e40af;">CS Cũ</th>
                        @endif
                        <th class="col-water" style="text-align:center; font-size:12px; font-weight:600; color:#1e40af;">CS Mới</th>
                        @if(auth()->user()->role !== 'technician')
                        <th class="col-water" style="text-align:center; font-size:12px; font-weight:600; color:#1e40af;">Tiêu thụ</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($apartmentData as $i => $data)
                    @php $bothDoneRow = $data['elec_recorded'] && $data['water_recorded']; @endphp
                    <tr id="row-{{ $i }}" class="{{ $bothDoneRow ? 'batch-row--all-done' : '' }}">
                        {{-- Checkbox --}}
                        <td style="text-align:center;">
                            @if (!$bothDoneRow)
                                <input type="checkbox" class="row-check" data-index="{{ $i }}" checked
                                    style="width:15px;height:15px;accent-color:#00236f;cursor:pointer;">
                            @else
                                <span class="done-icon">
                                    <svg width="18" height="18" fill="none" stroke="#166534" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </span>
                            @endif
                        </td>
 
                        {{-- Room --}}
                        <td style="text-align:center;"><strong class="text-strong">{{ $data['apartment']->apartment_number }}</strong></td>
 
                        {{-- Block/Floor --}}
                        <td class="text-muted" style="font-size:12px; text-align:center;">
                            {{ $data['apartment']->floor->block->name ?? '—' }}
                            / {{ $data['apartment']->floor->name ?? 'Tầng ' . $data['apartment']->floor->floor_number }}
                        </td>
 
                        {{-- ── ELECTRICITY ─────────────────────── --}}
                        @if(auth()->user()->role !== 'technician')
                        <td class="col-elec" style="font-weight:600;color:#92400e;text-align:center;">
                            {{ number_format($data['elec_old']) }}
                        </td>
                        @endif
                        <td class="col-elec {{ auth()->user()->role === 'technician' ? 'col-divider-border' : '' }}" style="text-align:center;">
                            @if (!$data['elec_recorded'])
                                <input type="hidden"
                                    name="readings[{{ $i }}][apartment_id]"
                                    value="{{ $data['apartment']->id }}"
                                    class="apt-id-input">
                                <input type="number"
                                    name="readings[{{ $i }}][elec_new]"
                                    class="batch-meter-input elec-input"
                                    min="{{ $data['elec_old'] }}"
                                    data-old="{{ $data['elec_old'] }}"
                                    data-original-old="{{ $data['elec_old'] }}"
                                    data-type="elec"
                                    data-index="{{ $i }}"
                                    placeholder="Chỉ số mới">
                                <div style="margin-top: 4px;">
                                    <label style="display:inline-flex; align-items:center; gap:4px; font-size:10px; color:#92400e; cursor:pointer;">
                                        <input type="checkbox"
                                            name="readings[{{ $i }}][elec_is_reset]"
                                            value="1"
                                            class="elec-reset-cb"
                                            data-index="{{ $i }}"
                                            style="width:12px; height:12px; margin:0;">
                                        Thay mới
                                    </label>
                                </div>
                            @else
                                <span style="color:#15803d;font-weight:600;font-size:13px;">✓ Đã chốt</span>
                                <input type="hidden" name="readings[{{ $i }}][apartment_id]" value="{{ $data['apartment']->id }}">
                            @endif
                        </td>
                        @if(auth()->user()->role !== 'technician')
                        <td class="col-elec col-divider-border" style="text-align:center;">
                            <span class="usage-chip {{ !$data['elec_recorded'] ? 'usage-chip--zero' : 'usage-chip--elec' }}"
                                id="elec-usage-{{ $i }}">—</span>
                        </td>
                        @endif
 
                        {{-- ── WATER ───────────────────────────── --}}
                        @if(auth()->user()->role !== 'technician')
                        <td class="col-water" style="font-weight:600;color:#1e40af;text-align:center;">
                            {{ number_format($data['water_old']) }}
                        </td>
                        @endif
                        <td class="col-water" style="text-align:center;">
                            @if (!$data['water_recorded'])
                                <input type="number"
                                    name="readings[{{ $i }}][water_new]"
                                    class="batch-meter-input water-input"
                                    min="{{ $data['water_old'] }}"
                                    data-old="{{ $data['water_old'] }}"
                                    data-original-old="{{ $data['water_old'] }}"
                                    data-type="water"
                                    data-index="{{ $i }}"
                                    placeholder="Chỉ số mới">
                                <div style="margin-top: 4px;">
                                    <label style="display:inline-flex; align-items:center; gap:4px; font-size:10px; color:#1e40af; cursor:pointer;">
                                        <input type="checkbox"
                                            name="readings[{{ $i }}][water_is_reset]"
                                            value="1"
                                            class="water-reset-cb"
                                            data-index="{{ $i }}"
                                            style="width:12px; height:12px; margin:0;">
                                        Thay mới
                                    </label>
                                </div>
                            @else
                                <span style="color:#15803d;font-weight:600;font-size:13px;">✓ Đã chốt</span>
                            @endif
                        </td>
                        @if(auth()->user()->role !== 'technician')
                        <td class="col-water" style="text-align:center;">
                            <span class="usage-chip {{ !$data['water_recorded'] ? 'usage-chip--zero' : 'usage-chip--water' }}"
                                id="water-usage-{{ $i }}">—</span>
                        </td>
                        @endif

                        {{-- Ảnh công tơ --}}
                        <td style="text-align:center; vertical-align:middle; min-width:140px;">
                            @if (!$bothDoneRow)
                                <div style="display:flex;flex-direction:column;align-items:center;gap:5px;">
                                    {{-- Nút chụp camera (WebRTC) --}}
                                    <button type="button"
                                        onclick="openCameraModalBatch({{ $i }})"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px;background:#00236f;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.2s;"
                                        onmouseover="this.style.background='#001850'"
                                        onmouseout="this.style.background='#00236f'">
                                        <i class="fa-solid fa-camera" style="font-size: 12px;"></i>
                                        Chụp
                                    </button>
                                    {{-- Nút thư viện --}}
                                    <button type="button"
                                        onclick="document.getElementById('gal_{{ $i }}').click()"
                                        style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px;background:#fff;color:#334155;border:1px solid #cbd5e1;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.2s;"
                                        onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#94a3b8';"
                                        onmouseout="this.style.background='#fff'; this.style.borderColor='#cbd5e1';">
                                        <i class="fa-regular fa-image" style="font-size: 12px;"></i>
                                        Chọn ảnh
                                    </button>
                                    {{-- Gallery input (multiple, không capture) --}}
                                    <input type="file" id="gal_{{ $i }}"
                                        accept="image/*" multiple style="display:none;"
                                        data-row="{{ $i }}">
                                    {{-- Proxy input thực sự submit --}}
                                    <input type="file" id="proxy_{{ $i }}"
                                        name="readings[{{ $i }}][images][]"
                                        accept="image/*" multiple style="display:none;">
                                    {{-- Preview thumbnails --}}
                                    <div id="preview_{{ $i }}"
                                        style="display:flex;flex-wrap:wrap;gap:4px;justify-content:center;margin-top:4px;"></div>
                                </div>
                            @else
                                <span style="color:#64748b; font-size:12px;">—</span>
                            @endif
                        </td>


                        {{-- Status --}}
                        <td style="text-align:center;">
                            @if ($bothDoneRow)
                                <span class="util-badge util-badge--success">Đã chốt</span>
                            @elseif ($data['elec_recorded'] || $data['water_recorded'])
                                <span class="util-badge" style="background:#e0f2fe;color:#075985;font-size:11px;">
                                    {{ $data['elec_recorded'] ? 'Điện ' : '' }}{{ $data['water_recorded'] ? 'Nước' : '' }} Chốt 1 phần
                                </span>
                            @else
                                <span class="util-badge util-badge--warning">Chưa chốt</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="util-batch-footer" style="flex-wrap:wrap;gap:12px;">
            <div class="util-batch-footer__info">
                Tháng <strong>{{ $selectedMonth }}/{{ $selectedYear }}</strong>
                &nbsp;·&nbsp; Tổng: <strong>{{ $total }}</strong> căn hộ
                &nbsp;·&nbsp; Đang chọn: <strong id="countNum">{{ $total - $bothDone }}</strong>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <span style="font-size:12px;color:#64748b;">Có thể để trống ô không cần ghi</span>
                <button type="submit" class="util-btn util-btn--primary" id="batchSubmitBtn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Lưu tất cả
                </button>
            </div>
        </div>
    </div>
</form>

@elseif ($selectedBlockId || $selectedFloorId)
<div class="util-empty-state">
    <svg width="48" height="48" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 16px;">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
    <p>Không tìm thấy căn hộ nào trong khu vực đã chọn.</p>
</div>
@else
<div class="util-empty-state">
    <svg width="52" height="52" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 16px;">
        <rect x="2" y="3" width="20" height="18" rx="1"/>
        <path d="M9 3v18"/><path d="M15 3v18"/><path d="M2 9h20"/><path d="M2 15h20"/>
    </svg>
    <p>Vui lòng chọn <strong>Tòa nhà</strong> để hiển thị danh sách căn hộ cần chốt.</p>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tính tiêu thụ realtime ─────────────────────────
    document.querySelectorAll('.batch-meter-input').forEach(function (inp) {
        inp.addEventListener('input', function () {
            const oldVal   = parseInt(this.dataset.old) || 0;
            const newVal   = parseInt(this.value) || 0;
            const usage    = Math.max(0, newVal - oldVal);
            const idx      = this.dataset.index;
            const type     = this.dataset.type; // 'elec' or 'water'
            const chipId   = type + '-usage-' + idx;
            const chip     = document.getElementById(chipId);

            // Validate: new >= old
            if (this.value !== '' && newVal < oldVal) {
                this.classList.add('input-invalid');
            } else {
                this.classList.remove('input-invalid');
            }

            if (chip) {
                if (usage > 0) {
                    chip.textContent = usage.toLocaleString('vi-VN');
                    chip.className   = 'usage-chip usage-chip--' + (type === 'elec' ? 'elec' : 'water');
                } else {
                    chip.textContent = '—';
                    chip.className   = 'usage-chip usage-chip--zero';
                }
            }
        });
    });

    // ── Xử lý cờ Thay mới (Reset) ──────────────────────
    function handleResetCheckbox(cb, inputClass) {
        const idx = cb.dataset.index;
        const row = cb.closest('tr');
        const inp = row.querySelector('.' + inputClass);
        
        if (inp) {
            if (cb.checked) {
                inp.dataset.old = 0;
                inp.min = 0;
            } else {
                const originalOld = inp.dataset.originalOld || 0;
                inp.dataset.old = originalOld;
                inp.min = originalOld;
            }
            // Trigger input event to recalculate realtime usage
            inp.dispatchEvent(new Event('input'));
        }
    }

    document.querySelectorAll('.elec-reset-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            handleResetCheckbox(this, 'elec-input');
        });
    });

    document.querySelectorAll('.water-reset-cb').forEach(cb => {
        cb.addEventListener('change', function() {
            handleResetCheckbox(this, 'water-input');
        });
    });

    // ── Select All ────────────────────────────────────
    const selectAll = document.getElementById('selectAll');
    const countEl   = document.getElementById('countNum');

    function updateCount() {
        const n = document.querySelectorAll('.row-check:checked').length;
        if (countEl) countEl.textContent = n;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(cb => {
                cb.checked = this.checked;
                toggleRow(cb);
            });
            updateCount();
        });
    }

    document.querySelectorAll('.row-check').forEach(cb => {
        cb.addEventListener('change', function () {
            toggleRow(this);
            updateCount();
            if (selectAll) {
                selectAll.checked =
                    document.querySelectorAll('.row-check').length ===
                    document.querySelectorAll('.row-check:checked').length;
            }
        });
    });

    function toggleRow(checkbox) {
        const row    = checkbox.closest('tr');
        const inputs = row.querySelectorAll('input[name^="readings"]');
        if (checkbox.checked) {
            inputs.forEach(i => { i.disabled = false; });
            row.style.opacity = '1';
        } else {
            inputs.forEach(i => { i.disabled = true; });
            row.style.opacity = '0.4';
        }
    }

    // ── Submit validation ─────────────────────────────
    const form = document.getElementById('batchForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            // Xoá inputs của row bỏ chọn
            document.querySelectorAll('.row-check:not(:checked)').forEach(cb => {
                cb.closest('tr').querySelectorAll('input[name^="readings"]').forEach(i => i.remove());
            });

            // Kiểm tra còn ít nhất 1 giá trị mới
            const activeInputs = form.querySelectorAll('.batch-meter-input:not([disabled])');
            const hasAnyValue  = Array.from(activeInputs).some(i => i.value.trim() !== '');

            if (!hasAnyValue) {
                e.preventDefault();
                alert('⚠️ Vui lòng nhập ít nhất 1 chỉ số mới (điện hoặc nước) trước khi lưu.');
                return;
            }

            // Kiểm tra new >= old
            let hasInvalid = false;
            activeInputs.forEach(inp => {
                if (inp.value.trim() !== '') {
                    const oldVal = parseInt(inp.dataset.old) || 0;
                    const newVal = parseInt(inp.value) || 0;
                    if (newVal < oldVal) {
                        inp.classList.add('input-invalid');
                        hasInvalid = true;
                    }
                }
            });

            if (hasInvalid) {
                e.preventDefault();
                alert('⚠️ Chỉ số mới không được nhỏ hơn chỉ số cũ. Vui lòng kiểm tra các ô được tô đỏ.');
                return;
            }
        });
    }
    // Camera inputs: capture="environment", NO multiple → mở camera thực sự
    document.querySelectorAll('input[id^="cam_"]').forEach(function(inp) {
        const rowId = inp.id.replace('cam_', '');
        inp.addEventListener('change', function() {
            if (this.files.length > 0) {
                addBatchFiles(this, rowId);
                syncBatchProxy(rowId);
            }
            this.value = '';
        });
    });

    // Gallery inputs: multiple, NO capture → chọn nhiều ảnh từ thư viện
    document.querySelectorAll('input[id^="gal_"]').forEach(function(inp) {
        const rowId = inp.id.replace('gal_', '');
        inp.addEventListener('change', function() {
            if (this.files.length > 0) {
                const dt = getBatchDT(rowId);
                const remaining = MAX_BATCH_IMAGES - dt.files.length;
                const files = this.files;
                let added = 0;
                for (let i = 0; i < files.length && added < remaining; i++) {
                    dt.items.add(files[i]);
                    added++;
                }
                syncBatchProxy(rowId);
                renderBatchPreview(rowId);
            }
            this.value = '';
        });
    });
});

// ── Multi-image preview cho batch ─────────────────
// Lưu DataTransfer per-row để gộp ảnh từ camera + gallery
const rowFilesMap = {};
const MAX_BATCH_IMAGES = 5;

function getBatchDT(rowId) {
    if (!rowFilesMap[rowId]) rowFilesMap[rowId] = new DataTransfer();
    return rowFilesMap[rowId];
}

function addBatchFiles(camInp, rowId) {
    const dt = getBatchDT(rowId);
    const remaining = MAX_BATCH_IMAGES - dt.files.length;
    const files = camInp.files;
    let added = 0;
    for (let i = 0; i < files.length && added < remaining; i++) {
        dt.items.add(files[i]);
        added++;
    }
    // Gán lại files vào input camera (sẽ được submit)
    camInp.files = dt.files;
    renderBatchPreview(rowId);
}

function renderBatchPreview(rowId) {
    const container = document.getElementById('preview_' + rowId);
    if (!container) return;
    const dt = getBatchDT(rowId);
    container.innerHTML = '';
    for (let i = 0; i < dt.files.length; i++) {
        const file = dt.files[i];
        const wrap = document.createElement('div');
        wrap.style.cssText = 'position:relative;width:48px;height:48px;border-radius:6px;overflow:hidden;border:1.5px solid #e2e8f0;';
        const img = document.createElement('img');
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        reader.readAsDataURL(file);
        // Nút xóa nhỏ
        const del = document.createElement('button');
        del.type = 'button';
        del.innerHTML = '✕';
        del.dataset.rowId = rowId;
        del.dataset.idx = i;
        del.style.cssText = 'position:absolute;top:1px;right:1px;background:rgba(239,68,68,.8);color:#fff;border:none;border-radius:50%;width:16px;height:16px;font-size:9px;line-height:1;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;';
        del.addEventListener('click', function() {
            removeBatchFileAt(this.dataset.rowId, parseInt(this.dataset.idx));
        });
        wrap.appendChild(img);
        wrap.appendChild(del);
        container.appendChild(wrap);
    }
}

function syncBatchProxy(rowId) {
    const proxyInp = document.getElementById('proxy_' + rowId);
    if (proxyInp) proxyInp.files = getBatchDT(rowId).files;
}

function removeBatchFileAt(rowId, idx) {
    const dt = getBatchDT(rowId);
    const newDT = new DataTransfer();
    const files = dt.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== idx) newDT.items.add(files[i]);
    }
    rowFilesMap[rowId] = newDT;
    syncBatchProxy(rowId);
    renderBatchPreview(rowId);
}


// ── Camera Modal WebRTC cho Batch ────────────────────────────────────────
let batchCameraStream = null;
let batchCameraRowId  = null;

function openCameraModalBatch(rowId) {
    batchCameraRowId = rowId;

    // Nếu modal chưa có trong DOM thì tạo
    if (!document.getElementById('batchCameraModal')) {
        createBatchCameraModal();
    }

    document.getElementById('batchCameraOverlay').style.display = 'block';
    document.getElementById('batchCameraModal').style.display   = 'block';
    document.body.style.overflow = 'hidden';
    document.getElementById('batchCameraLoading').style.display = 'flex';

    const facing = document.getElementById('batchCameraSelect')?.value || 'environment';
    startBatchCamera(facing);
}

function createBatchCameraModal() {
    const overlay = document.createElement('div');
    overlay.id = 'batchCameraOverlay';
    overlay.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;';
    overlay.onclick = closeBatchCameraModal;
    document.body.appendChild(overlay);

    const modal = document.createElement('div');
    modal.id = 'batchCameraModal';
    modal.style.cssText = 'display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:94vw;max-width:520px;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.3);z-index:10000;overflow:hidden;';
    modal.innerHTML = `
        <div style="background:#00236f;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;">
            <div style="color:#fff;font-weight:700;font-size:0.95rem;display:flex;align-items:center;gap:6px;">
                <i class="fa-solid fa-camera" style="font-size: 14px;"></i>
                Chụp ảnh công tơ
            </div>
            <button type="button" onclick="closeBatchCameraModal()" style="background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:50%;font-size:1.1rem;cursor:pointer;">✕</button>
        </div>
        <div style="padding:8px 14px;background:#f8fafc;display:flex;align-items:center;gap:8px;border-bottom:1px solid #e2e8f0;">
            <label style="font-size:12px;font-weight:600;color:#475569;">Camera:</label>
            <select id="batchCameraSelect" onchange="switchBatchCamera()" style="flex:1;padding:4px 8px;border:1px solid #cbd5e1;border-radius:7px;font-size:12px;">
                <option value="environment">Camera sau</option>
                <option value="user">Camera trước</option>
            </select>
        </div>
        <div style="background:#000;position:relative;aspect-ratio:4/3;max-height:320px;overflow:hidden;">
            <video id="batchCameraVideo" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;display:block;"></video>
            <div style="position:absolute;inset:0;pointer-events:none;">
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:70%;height:70%;border:2px dashed rgba(255,255,255,0.5);border-radius:8px;"></div>
            </div>
            <div id="batchCameraLoading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;background:rgba(0,0,0,0.5);">Đang khởi động camera...</div>
        </div>
        <div style="padding:14px;display:flex;gap:10px;justify-content:center;background:#f8fafc;">
            <button type="button" onclick="captureBatchPhoto()" style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#00236f;color:#fff;border:none;border-radius:11px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(0,35,111,0.25);">
                <i class="fa-solid fa-camera" style="font-size: 15px;"></i>
                Chụp
            </button>
            <button type="button" onclick="closeBatchCameraModal()" style="padding:11px 18px;background:#f1f5f9;color:#475569;border:none;border-radius:11px;font-size:13px;font-weight:600;cursor:pointer;">Hủy</button>
        </div>
        <canvas id="batchCameraCanvas" style="display:none;"></canvas>
    `;
    document.body.appendChild(modal);
}

async function startBatchCamera(facing) {
    const video   = document.getElementById('batchCameraVideo');
    const loading = document.getElementById('batchCameraLoading');

    if (batchCameraStream) {
        batchCameraStream.getTracks().forEach(t => t.stop());
        batchCameraStream = null;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        loading.textContent = 'Trình duyệt không hỗ trợ camera.';
        return;
    }

    try {
        batchCameraStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: facing, width: { ideal: 1920 }, height: { ideal: 1080 } }
        });
        video.srcObject = batchCameraStream;
        video.onloadedmetadata = () => { loading.style.display = 'none'; };
    } catch (err) {
        let msg = 'Không thể mở camera.';
        if (err.name === 'NotAllowedError')  msg = 'Quyền camera bị chặn. Cho phép trong thanh địa chỉ và thử lại.';
        if (err.name === 'NotFoundError')    msg = 'Không tìm thấy camera. Kiểm tra webcam.';
        if (err.name === 'NotReadableError') msg = 'Camera đang dùng bởi ứng dụng khác.';
        if (err.name === 'OverconstrainedError') {
            try {
                batchCameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = batchCameraStream;
                video.onloadedmetadata = () => { loading.style.display = 'none'; };
                return;
            } catch(e2) { msg = e2.message; }
        }
        loading.innerHTML = `<div style="text-align:center;padding:16px;color:#fff;">${msg}<br><br><button onclick="closeBatchCameraModal()" style="padding:7px 14px;background:#fff;color:#1e293b;border:none;border-radius:7px;cursor:pointer;font-weight:600;">Đóng</button></div>`;
    }
}

function switchBatchCamera() {
    const facing = document.getElementById('batchCameraSelect').value;
    const loading = document.getElementById('batchCameraLoading');
    loading.textContent = '🔄 Đang chuyển camera...';
    loading.style.display = 'flex';
    startBatchCamera(facing);
}

function captureBatchPhoto() {
    const video  = document.getElementById('batchCameraVideo');
    const canvas = document.getElementById('batchCameraCanvas');
    if (!video.videoWidth) { alert('Camera chưa sẵn sàng.'); return; }

    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    canvas.toBlob(blob => {
        if (!blob) { alert('Không thể chụp ảnh.'); return; }

        // Flash
        const flash = document.createElement('div');
        flash.style.cssText = 'position:fixed;inset:0;background:#fff;opacity:0.8;z-index:99999;pointer-events:none;transition:opacity .3s;';
        document.body.appendChild(flash);
        setTimeout(() => { flash.style.opacity='0'; setTimeout(()=>flash.remove(),300); }, 50);

        const rowId = batchCameraRowId;
        const file  = new File([blob], `cam_${rowId}_${Date.now()}.jpg`, { type: 'image/jpeg' });
        const dt    = getBatchDT(rowId);

        if (dt.files.length >= 5) {
            alert('⚠️ Tối đa 5 ảnh cho mỗi hàng.');
            closeBatchCameraModal();
            return;
        }

        dt.items.add(file);
        syncBatchProxy(rowId);
        renderBatchPreview(rowId);
        closeBatchCameraModal();
    }, 'image/jpeg', 0.92);
}

function closeBatchCameraModal() {
    const overlay = document.getElementById('batchCameraOverlay');
    const modal   = document.getElementById('batchCameraModal');
    if (overlay) overlay.style.display = 'none';
    if (modal)   modal.style.display   = 'none';
    document.body.style.overflow = '';

    if (batchCameraStream) {
        batchCameraStream.getTracks().forEach(t => t.stop());
        batchCameraStream = null;
    }
    const video = document.getElementById('batchCameraVideo');
    if (video) video.srcObject = null;
    const loading = document.getElementById('batchCameraLoading');
    if (loading) { loading.style.display='flex'; loading.textContent='🔄 Đang khởi động camera...'; }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeBatchCameraModal(); });
</script>
@endpush
