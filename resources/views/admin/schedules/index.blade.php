@extends('layouts.admin.master')

@section('page_title', 'Phân ca làm việc')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
@vite(['resources/css/admin/schedules.css'])
@endpush

@section('content')
<div class="dashboard-card schedule-page">
    {{-- Header --}}
    <div class="schedule-header">
        <h1 class="schedule-header__title">Bảng phân ca làm việc</h1>
        <div class="schedule-header__nav">
            <button type="button" class="btn-copy" onclick="openCopyModal()">
                <i class="fa-regular fa-copy"></i> <span class="hide-mobile">Sao chép lịch</span>
            </button>
            <a href="{{ route('admin.schedules.index', ['date' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn-nav">
                <i class="fa-solid fa-chevron-left"></i> <span class="hide-mobile">Tuần trước</span>
            </a>
            <div class="schedule-header__date-picker">
                <input type="date" id="jumpToDate" value="{{ $date }}" class="schedule-date-input" onchange="jumpToWeek(this.value)" title="Chọn ngày để nhảy đến tuần đó">
                <span class="schedule-header__week">{{ $startOfWeek->format('d/m') }} – {{ $endOfWeek->format('d/m/Y') }}</span>
            </div>
            <a href="{{ route('admin.schedules.index', ['date' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn-nav">
                <span class="hide-mobile">Tuần sau</span> <i class="fa-solid fa-chevron-right"></i>
            </a>
            <a href="{{ route('admin.schedules.index') }}" class="btn-nav btn-nav--today" title="Về tuần hiện tại">
                <i class="fa-solid fa-calendar-day"></i> <span class="hide-mobile">Hôm nay</span>
            </a>
        </div>
    </div>

    {{-- Schedule Grid --}}
    <div class="schedule-wrapper">
        <table class="schedule-grid">
            <thead>
                <tr>
                    <th>Ca trực</th>
                    @foreach($days as $day)
                        <th class="{{ $day->format('Y-m-d') === $today ? 'is-today' : '' }}">
                            <span class="day-name">{{ ['CN','T2','T3','T4','T5','T6','T7'][$day->dayOfWeek] }}</span>
                            <span class="day-date">{{ $day->format('d/m') }}</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $shift)
                <tr>
                    <td class="shift-label">
                        @php $shiftReqs = $shift->requirements->pluck('required_staff', 'department_id')->toJson(); @endphp
                        <div class="shift-label__name">
                            {{ $shift->name }}
                            <button type="button" class="shift-label__settings" onclick="openReqModal({{ $shift->id }}, '{{ $shift->name }}', {{ $shiftReqs }})" title="Cài đặt định mức">
                                <i class="fa-solid fa-gear"></i>
                            </button>
                        </div>
                        <div class="shift-label__time">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</div>
                    </td>
                    @foreach($days as $day)
                        @php
                            $dayStr = $day->format('Y-m-d');
                            $cellSchedules = $scheduleMatrix[$dayStr][$shift->id] ?? [];
                            $grouped = [];
                            foreach ($cellSchedules as $sc) {
                                $deptId = $sc->staff->department_id;
                                if (!isset($grouped[$deptId])) $grouped[$deptId] = [];
                                $grouped[$deptId][] = $sc;
                            }
                            $hasContent = false;
                            foreach ($departments as $d) {
                                $r = $shift->requirements->where('department_id', $d->id)->first();
                                if (($r ? $r->required_staff : 0) > 0 || isset($grouped[$d->id])) { $hasContent = true; break; }
                            }
                        @endphp
                        <td class="{{ $day->format('Y-m-d') === $today ? 'is-today' : '' }}">
                            <div class="schedule-cell" id="cell-{{ $dayStr }}-{{ $shift->id }}">
                                @if(!$hasContent)
                                    <div class="schedule-empty">Chưa có lịch</div>
                                @else
                                    @foreach($departments as $dept)
                                        @php
                                            $req = $shift->requirements->where('department_id', $dept->id)->first();
                                            $reqStaff = $req ? $req->required_staff : 0;
                                            $assigned = isset($grouped[$dept->id]) ? count($grouped[$dept->id]) : 0;
                                        @endphp
                                        @if($reqStaff > 0 || $assigned > 0)
                                        <div class="dept-group" data-required="{{ $reqStaff }}">
                                            <div class="dept-header">
                                                <span>{{ $dept->name }}</span>
                                                @if($assigned < $reqStaff)
                                                    <span class="dept-badge dept-badge--warning">Thiếu {{ $reqStaff - $assigned }}</span>
                                                @else
                                                    <span class="dept-badge dept-badge--ok">{{ $assigned }}/{{ $reqStaff }}</span>
                                                @endif
                                            </div>
                                            <div class="schedule-list" id="list-{{ $dayStr }}-{{ $shift->id }}-{{ $dept->id }}">
                                                @if(isset($grouped[$dept->id]))
                                                    @foreach($grouped[$dept->id] as $sc)
                                                        <div class="schedule-item {{ $sc->is_leader ? 'schedule-item--leader' : '' }}" id="schedule-item-{{ $sc->id }}">
                                                            <span class="schedule-item__name" title="{{ $sc->staff->full_name }}">
                                                                @if($sc->is_leader)<span class="schedule-item__leader-tag">TC</span>@endif
                                                                {{ mb_strimwidth($sc->staff->full_name, 0, 18, '...') }}
                                                            </span>
                                                            <div class="schedule-actions">
                                                                <button type="button" class="action-btn action-btn--star" onclick="toggleLeader({{ $sc->id }}, this)" title="Trưởng ca"><i class="fa-solid fa-star"></i></button>
                                                                <button type="button" class="action-btn action-btn--del" onclick="deleteSchedule({{ $sc->id }}, this)" title="Xóa"><i class="fa-solid fa-xmark"></i></button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <button type="button" class="schedule-add" onclick="openAddModal('{{ $dayStr }}', {{ $shift->id }}, '{{ $shift->name }}', '{{ $day->format('d/m/Y') }}', {{ $dept->id }}, '{{ $dept->name }}')">
                                                <i class="fa-solid fa-plus"></i> Thêm
                                            </button>
                                        </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Toast --}}
<div class="toast" id="toast"></div>

{{-- Confirm Modal --}}
<div class="modal-overlay" id="confirmModal" role="alertdialog" aria-modal="true">
    <div class="confirm-box">
        <p class="confirm-box__message" id="confirmMessage"></p>
        <div class="confirm-box__actions">
            <button type="button" class="btn-cancel" id="confirmNo">Hủy</button>
            <button type="button" class="btn-submit" id="confirmYes">Xác nhận</button>
        </div>
    </div>
</div>

{{-- Add Schedule Modal --}}
<div class="modal-overlay" id="addModal" role="dialog" aria-labelledby="addModalTitle" aria-modal="true">
    <div class="modal-box">
        <h2 class="modal-box__title" id="addModalTitle">Phân ca làm việc</h2>
        <p class="modal-box__subtitle" id="modalSubtitle"></p>
        <form id="addForm" onsubmit="submitAddForm(event)">
            @csrf
            <input type="hidden" name="work_date" id="inputWorkDate">
            <input type="hidden" name="shift_id" id="inputShiftId">
            <input type="hidden" name="department_id" id="inputDeptId">
            <div class="form-group">
                <label class="modal-box__label">Chọn nhân sự <span>*</span></label>
                <select name="staff_id" id="inputStaffId" required></select>
            </div>
            <div class="modal-box__error" id="modalError"></div>
            <div class="modal-box__footer">
                <button type="button" class="btn-cancel" onclick="closeAddModal()">Hủy</button>
                <button type="submit" class="btn-submit" id="btnSubmitAdd">Gán ca</button>
            </div>
        </form>
    </div>
</div>

{{-- Copy Schedule Modal (redesigned) --}}
<div class="modal-overlay" id="copyModal" role="dialog" aria-labelledby="copyModalTitle" aria-modal="true">
    <div class="modal-box modal-box--wide">
        <h2 class="modal-box__title" id="copyModalTitle">Sao chép lịch phân ca</h2>
        <p class="modal-box__subtitle">Nhân bản lịch phân ca từ một ngày sang một hoặc nhiều ngày khác.</p>
        <form id="copyForm" onsubmit="submitCopyForm(event)">
            @csrf
            {{-- Step 1 --}}
            <div class="copy-section">
                <div class="copy-section__header">
                    <span class="copy-step">1</span>
                    <span class="copy-section__title">Chọn ngày nguồn</span>
                </div>
                <div class="form-group--sm">
                    <input type="date" id="copyFrom" required class="modal-box__input" onchange="loadPreview()">
                </div>
                <div class="copy-preview" id="copyPreview">
                    <span class="copy-preview__empty">Chọn ngày để xem số lượng phân công</span>
                </div>
            </div>
            {{-- Step 2 --}}
            <div class="copy-section">
                <div class="copy-section__header">
                    <span class="copy-step">2</span>
                    <span class="copy-section__title">Chọn ngày đích</span>
                </div>
                <div class="copy-target-type">
                    <label class="copy-radio"><input type="radio" name="target_type" value="single" checked onchange="toggleTargetType()"><span>Một ngày</span></label>
                    <label class="copy-radio"><input type="radio" name="target_type" value="week" onchange="toggleTargetType()"><span>Cả tuần (T2–CN)</span></label>
                    <label class="copy-radio"><input type="radio" name="target_type" value="custom" onchange="toggleTargetType()"><span>Nhiều ngày</span></label>
                </div>
                <div id="targetSingle" class="form-group--sm">
                    <input type="date" id="copyToSingle" class="modal-box__input">
                </div>
                <div id="targetWeek" class="form-group--sm copy-hidden">
                    <label class="modal-box__label">Tuần bắt đầu từ (Thứ 2)</label>
                    <input type="date" id="copyToWeekStart" class="modal-box__input" onchange="updateWeekPreview()">
                    <div class="copy-week-preview" id="weekPreview"></div>
                </div>
                <div id="targetCustom" class="form-group--sm copy-hidden">
                    <label class="modal-box__label">Thêm ngày (nhấn + hoặc Enter)</label>
                    <div class="copy-multi-input">
                        <input type="date" id="copyToCustomInput" class="modal-box__input" onkeydown="if(event.key==='Enter'){event.preventDefault();addCustomDate()}">
                        <button type="button" class="btn-submit copy-multi-add" onclick="addCustomDate()">+</button>
                    </div>
                    <div class="copy-date-tags" id="customDateTags"></div>
                </div>
            </div>
            {{-- Step 3 --}}
            <div class="copy-section">
                <div class="copy-section__header">
                    <span class="copy-step">3</span>
                    <span class="copy-section__title">Khi ngày đích đã có lịch</span>
                </div>
                <div class="copy-mode-options">
                    <label class="copy-mode-card"><input type="radio" name="copy_mode" value="skip" checked><div class="copy-mode-card__content"><strong>Giữ nguyên &amp; bổ sung</strong><small>Bỏ qua trùng, chỉ thêm mới</small></div></label>
                    <label class="copy-mode-card"><input type="radio" name="copy_mode" value="overwrite"><div class="copy-mode-card__content"><strong>Ghi đè toàn bộ</strong><small>Xóa lịch cũ, thay bằng lịch nguồn</small></div></label>
                </div>
            </div>
            <div class="modal-box__error" id="copyError"></div>
            <div class="modal-box__success" id="copySuccess"></div>
            <div class="modal-box__footer">
                <button type="button" class="btn-cancel" onclick="closeCopyModal()">Hủy</button>
                <button type="submit" class="btn-submit" id="btnSubmitCopy"><i class="fa-regular fa-copy"></i> Sao chép</button>
            </div>
        </form>
    </div>
</div>

{{-- Requirement Modal --}}
<div class="modal-overlay" id="reqModal" role="dialog" aria-labelledby="reqModalTitle" aria-modal="true">
    <div class="modal-box">
        <h2 class="modal-box__title" id="reqModalTitle">Định mức nhân sự</h2>
        <p class="modal-box__subtitle" id="reqModalSubtitle"></p>
        <form id="reqForm" onsubmit="submitReqForm(event)">
            @csrf
            <input type="hidden" id="reqShiftId">
            <div class="req-list">
                @foreach($departments as $dept)
                    <div class="req-row">
                        <span class="req-row__label">{{ $dept->name }}</span>
                        <div class="req-row__input-wrap">
                            <input type="number" id="req-dept-{{ $dept->id }}" class="req-dept-input req-row__input" data-dept-id="{{ $dept->id }}" min="0" value="0">
                            <span class="req-row__unit">người</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-box__error" id="reqError"></div>
            <div class="modal-box__footer">
                <button type="button" class="btn-cancel" onclick="closeReqModal()">Hủy</button>
                <button type="submit" class="btn-submit" id="btnSubmitReq">Lưu định mức</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    window.scheduleConfig = {
        staffsUrl: "{{ route('admin.api.staffs') }}",
        storeUrl: "{{ route('admin.staff-schedules.store') }}",
        baseUrl: "{{ url('admin/staff-schedules') }}",
        copyUrl: "{{ route('admin.staff-schedules.copy') }}",
        previewCopyUrl: "{{ route('admin.staff-schedules.preview-copy') }}",
        schedulesUrl: "{{ url('admin/schedules') }}"
    };
</script>
@vite(['resources/js/admin/schedules.js'])
@endpush
