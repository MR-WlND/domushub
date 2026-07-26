@extends('layouts.admin.master')

@section('page_title', 'Sửa chấm công — ' . optional($record->user)->name)
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
@endpush

@php
    $statusOptions = [
        'working'  => 'Đang làm việc',
        'present'  => 'Đúng giờ',
        'late'     => 'Đi trễ',
        'absent'   => 'Vắng mặt',
        'half_day' => 'Nửa ngày',
    ];
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật',
        'security'   => 'An ninh',
        'cleaning'   => 'Vệ sinh',
    ];
@endphp

@section('content')
<div class="attendance-page">

    {{-- ── Header ── --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự › Chấm công</p>
            <h1>Chỉnh sửa bản ghi chấm công</h1>
        </div>
        <div class="attendance-page__actions">
            <a href="{{ portal_route('attendance.show', $record->id) }}" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                Xem chi tiết
            </a>
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

    {{-- Employee banner --}}
    <div class="att-employee-banner">
        @if ($record->user && $record->user->avatar)
            <img src="{{ asset('storage/' . $record->user->avatar) }}"
                 alt="{{ $record->user->name }}" class="att-employee-banner__avatar">
        @else
            <div class="att-employee-banner__initial">
                {{ strtoupper(mb_substr(optional($record->user)->name ?? 'N', 0, 1)) }}
            </div>
        @endif
        <div>
            <div class="att-employee-banner__name">{{ optional($record->user)->name }}</div>
            <div class="att-employee-banner__meta">
                {{ $roleLabels[optional($record->user)->role] ?? optional($record->user)->role }} &mdash;
                Ngày: <strong>{{ $record->work_date->format('d/m/Y') }}</strong>
                ({{ $record->work_date->isoFormat('dddd') }})
            </div>
        </div>
    </div>

    <form method="POST" action="{{ portal_route('attendance.update', $record->id) }}">
        @csrf
        @method('PUT')

        <div class="att-form-card">

            {{-- Section: Ca & Vị trí --}}
            <div class="att-form-section">
                <div class="att-form-section__title">Ca làm việc</div>
                <div class="att-form-grid">

                    <div class="att-form-group">
                        <label class="att-form-label" for="shift">
                            Ca làm việc <span class="required">*</span>
                        </label>
                        <select name="shift" id="shift"
                                class="att-form-control @error('shift') is-invalid @enderror" required>
                            @foreach ($shifts as $value => $label)
                                <option value="{{ $value }}"
                                        @selected(old('shift', $record->shift) === $value)>
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
                            <option value="">— Chọn vị trí —</option>
                            @foreach ($workLocations as $loc)
                                <option value="{{ $loc }}"
                                        @selected(old('work_location', $record->work_location) === $loc)>
                                    {{ $loc }}
                                </option>
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
                               value="{{ old('check_in_at', optional($record->check_in_at)->format('H:i')) }}">
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
                               value="{{ old('check_out_at', optional($record->check_out_at)->format('H:i')) }}">
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
                                <option value="{{ $value }}"
                                        @selected(old('status', $record->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="att-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Hiển thị thông tin tính toán hiện tại --}}
                @if ($record->working_hours !== null || $record->late_minutes !== null)
                <div style="margin-top: 16px; padding: 14px 18px; background: #f8faff; border-radius: 10px; border: 1px solid #e8edf5; display: flex; gap: 28px; flex-wrap: wrap;">
                    @if ($record->working_hours !== null)
                    <div>
                        <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:4px;">Giờ công hiện tại</div>
                        <div style="font-size:20px; font-weight:700; color:var(--color-text);">{{ number_format($record->working_hours, 1) }}h</div>
                    </div>
                    @endif
                    @if ($record->late_minutes !== null && $record->late_minutes > 0)
                    <div>
                        <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:4px;">Đi muộn</div>
                        <div style="font-size:20px; font-weight:700; color:#ea580c;">{{ $record->late_minutes }} phút</div>
                    </div>
                    @endif
                    <div style="font-size:12px; color:#94a3b8; align-self:center; font-style:italic;">
                        * Sẽ tính lại khi bạn lưu với đủ giờ vào và giờ ra
                    </div>
                </div>
                @endif
            </div>

            {{-- Section: Ghi chú --}}
            <div class="att-form-section">
                <div class="att-form-section__title">Ghi chú</div>
                <div class="att-form-group">
                    <label class="att-form-label" for="note">Ghi chú của quản lý</label>
                    <textarea name="note" id="note"
                              class="att-form-control @error('note') is-invalid @enderror"
                              placeholder="Nhập ghi chú nếu cần…">{{ old('note', $record->note) }}</textarea>
                    @error('note')
                        <span class="att-form-error">{{ $message }}</span>
                    @enderror
                </div>

                @if ($record->recorder)
                <div style="margin-top: 12px; font-size:13px; color:#94a3b8;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle; margin-right:4px;">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Đã chỉnh sửa bởi: <strong>{{ $record->recorder->name }}</strong>
                    lúc {{ $record->updated_at->format('H:i d/m/Y') }}
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="att-form-actions">
                <button type="submit" class="att-btn att-btn--primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu thay đổi
                </button>
                <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                    Hủy
                </a>
            </div>

        </div>
    </form>

</div>
@endsection
