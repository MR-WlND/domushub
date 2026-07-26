@extends('layouts.admin.master')

@section('page_title', 'Thêm chấm công')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật',
        'security'   => 'An ninh',
        'cleaning'   => 'Vệ sinh',
    ];

    $statusOptions = [
        'working'  => 'Đang làm việc',
        'present'  => 'Đúng giờ',
        'late'     => 'Đi trễ',
        'absent'   => 'Vắng mặt',
        'half_day' => 'Nửa ngày',
    ];
@endphp

@section('content')
<div class="attendance-page">

    {{-- ── Header ── --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự › Chấm công</p>
            <h1>Thêm bản ghi chấm công</h1>
        </div>
        <div class="attendance-page__actions">
            <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="att-alert att-alert--danger">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ portal_route('attendance.store') }}">
        @csrf

        <div class="att-form-card">

            {{-- Section: Thông tin nhân viên & ngày --}}
            <div class="att-form-section">
                <div class="att-form-section__title">Thông tin nhân viên</div>
                <div class="att-form-grid">

                    <div class="att-form-group">
                        <label class="att-form-label" for="user_id">
                            Nhân viên <span class="required">*</span>
                        </label>
                        <select name="user_id" id="user_id"
                                class="att-form-control @error('user_id') is-invalid @enderror" required>
                            <option value="">— Chọn nhân viên —</option>
                            @foreach ($staffList as $staff)
                                <option value="{{ $staff->id }}"
                                        @selected(old('user_id') == $staff->id)>
                                    {{ $staff->name }} ({{ $roleLabels[$staff->role] ?? $staff->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="att-form-group">
                        <label class="att-form-label" for="work_date">
                            Ngày làm việc <span class="required">*</span>
                        </label>
                        <input type="date" name="work_date" id="work_date"
                               class="att-form-control @error('work_date') is-invalid @enderror"
                               value="{{ old('work_date', today()->toDateString()) }}" required>
                        @error('work_date')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="att-form-group">
                        <label class="att-form-label" for="shift">
                            Ca làm việc <span class="required">*</span>
                        </label>
                        <select name="shift" id="shift"
                                class="att-form-control @error('shift') is-invalid @enderror" required>
                            @foreach ($shifts as $value => $label)
                                <option value="{{ $value }}" @selected(old('shift', 'full_day') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('shift')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="att-form-group">
                        <label class="att-form-label" for="work_location">Vị trí làm việc</label>
                        <select name="work_location" id="work_location" class="att-form-control">
                            <option value="">— Chọn vị trí (nếu có) —</option>
                            @foreach ($workLocations as $loc)
                                <option value="{{ $loc }}" @selected(old('work_location') === $loc)>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- Section: Giờ công --}}
            <div class="att-form-section">
                <div class="att-form-section__title">Giờ làm việc</div>
                <div class="att-form-grid">

                    <div class="att-form-group">
                        <label class="att-form-label" for="check_in_at">Giờ check-in</label>
                        <input type="time" name="check_in_at" id="check_in_at"
                               class="att-form-control @error('check_in_at') is-invalid @enderror"
                               value="{{ old('check_in_at', config('attendance.start_time', '08:00')) }}">
                        <span style="font-size:12px; color:#94a3b8; margin-top:2px;">
                            Giờ chuẩn: {{ config('attendance.start_time', '08:00') }}
                        </span>
                        @error('check_in_at')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="att-form-group">
                        <label class="att-form-label" for="check_out_at">Giờ check-out</label>
                        <input type="time" name="check_out_at" id="check_out_at"
                               class="att-form-control @error('check_out_at') is-invalid @enderror"
                               value="{{ old('check_out_at') }}">
                        <span style="font-size:12px; color:#94a3b8; margin-top:2px;">
                            Giờ chuẩn: {{ config('attendance.end_time', '17:00') }} — để trống nếu đang làm
                        </span>
                        @error('check_out_at')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="att-form-group">
                        <label class="att-form-label" for="status">
                            Trạng thái <span class="required">*</span>
                        </label>
                        <select name="status" id="status"
                                class="att-form-control @error('status') is-invalid @enderror" required>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'present') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span style="font-size:12px; color:#94a3b8; margin-top:2px;">
                            Nếu chỉ có check-in, chọn "Đang làm việc"
                        </span>
                        @error('status')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Section: Ghi chú --}}
            <div class="att-form-section">
                <div class="att-form-section__title">Ghi chú</div>
                <div class="att-form-group">
                    <label class="att-form-label" for="note">Ghi chú của quản lý</label>
                    <textarea name="note" id="note"
                              class="att-form-control @error('note') is-invalid @enderror"
                              placeholder="Nhập ghi chú nếu cần (VD: nghỉ có phép, lý do đặc biệt…)">{{ old('note') }}</textarea>
                    @error('note')
                        <span class="att-form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="att-form-actions">
                <button type="submit" class="att-btn att-btn--primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu bản ghi
                </button>
                <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                    Hủy
                </a>
            </div>

        </div>
    </form>

</div>
@endsection
