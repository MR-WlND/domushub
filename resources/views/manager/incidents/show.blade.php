@extends('layouts.manager.master')

@section('page_title', 'Chi tiết phản ánh #' . $incident->id . ' – DomusHub')

@section('content')
<div class="manager-page">
    <div class="page-header">
        <div>
            <p class="page-header__eyebrow">Phản ánh #{{ $incident->id }}</p>
            <h1 class="page-header__title">{{ $incident->title }}</h1>
        </div>
        <a href="{{ route('manager.incidents.index') }}" class="btn-outline" id="btn-back-list">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Quay lại
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert--error">{{ session('error') }}</div>
    @endif

    <div class="incident-detail-grid">
        {{-- Main --}}
        <div class="incident-detail__main">

            {{-- Thông tin phản ánh --}}
            <div class="detail-card">
                <div class="detail-card__header">
                    <h2 class="detail-card__title">Nội dung phản ánh</h2>
                    <span class="incident-status incident-status--{{ $incident->status }}">
                        {{ $incident->status_label }}
                    </span>
                </div>

                <div class="detail-meta-row">
                    <span class="incident-tag incident-tag--{{ $incident->category }}">{{ $incident->category_label }}</span>
                    <span class="incident-priority incident-priority--{{ $incident->priority }}">{{ $incident->priority_label }}</span>
                </div>

                <p class="detail-desc">{{ $incident->description }}</p>

                @if ($incident->images && count($incident->images) > 0)
                    <div class="detail-images">
                        @foreach ($incident->images as $img)
                            <a href="{{ Storage::url($img) }}" target="_blank" class="detail-image-link">
                                <img src="{{ Storage::url($img) }}" alt="Ảnh đính kèm" class="detail-image">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Phân công kỹ thuật viên --}}
            @if (in_array($incident->status, ['pending', 'assigned']))
                <div class="detail-card" id="assign-section">
                    <h2 class="detail-card__title">
                        {{ $incident->status === 'assigned' ? 'Thay đổi kỹ thuật viên' : 'Phân công kỹ thuật viên' }}
                    </h2>

                    @if ($incident->assignedTo)
                        <div class="technician-current">
                            <div class="technician-avatar">{{ strtoupper(substr($incident->assignedTo->name, 0, 1)) }}</div>
                            <div>
                                <p class="technician-name">Đang phân công: <strong>{{ $incident->assignedTo->name }}</strong></p>
                                <p class="timeline-time">{{ $incident->assigned_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('manager.incidents.assign', $incident->id) }}" method="POST"
                        class="assign-form" id="form-assign">
                        @csrf
                        <div class="form-row">
                            <div class="form-group" style="flex:1">
                                <label class="form-label" for="technician_id">Chọn kỹ thuật viên</label>
                                <select name="technician_id" id="technician_id" class="form-select" required>
                                    <option value="">-- Chọn KTV --</option>
                                    @foreach ($technicians as $tech)
                                        <option value="{{ $tech->id }}"
                                            {{ $incident->assigned_to == $tech->id ? 'selected' : '' }}>
                                            {{ $tech->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('technician_id')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group" style="display:flex;align-items:flex-end;">
                                <button type="submit" class="btn-primary" id="btn-assign">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    Phân công
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Ghi chú kỹ thuật viên --}}
            @if ($incident->technician_note)
                <div class="detail-card detail-card--technician">
                    <h2 class="detail-card__title">Ghi chú kỹ thuật viên</h2>
                    <p class="detail-desc">{{ $incident->technician_note }}</p>
                    @if ($incident->resolved_at)
                        <p class="timeline-time">Hoàn thành: {{ $incident->resolved_at->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            @endif

            {{-- Xác nhận hoàn thành --}}
            @if ($incident->status === 'resolved')
                <div class="detail-card detail-card--action" id="confirm-section">
                    <h2 class="detail-card__title">Xác nhận hoàn thành</h2>
                    <p class="detail-desc">Kỹ thuật viên đã cập nhật hoàn thành xử lý. Vui lòng xác nhận để đóng phản ánh này.</p>
                    <form action="{{ route('manager.incidents.confirm', $incident->id) }}" method="POST" id="form-confirm">
                        @csrf
                        <button type="submit" class="btn-success" id="btn-confirm"
                            onclick="return confirm('Xác nhận phản ánh này đã được xử lý xong?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            Xác nhận hoàn thành
                        </button>
                    </form>
                </div>
            @endif

            {{-- Đóng phản ánh --}}
            @if (! in_array($incident->status, ['confirmed', 'closed']))
                <div class="detail-card detail-card--danger" id="close-section">
                    <h2 class="detail-card__title">Đóng phản ánh</h2>
                    <form action="{{ route('manager.incidents.close', $incident->id) }}" method="POST" id="form-close">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="reason">Lý do đóng (tùy chọn)</label>
                            <input type="text" name="reason" id="reason" class="form-input"
                                placeholder="VD: Không thuộc phạm vi xử lý, cư dân rút phản ánh...">
                        </div>
                        <button type="submit" class="btn-danger" id="btn-close"
                            onclick="return confirm('Bạn chắc chắn muốn đóng phản ánh này?')">
                            Đóng phản ánh
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="incident-detail__sidebar">
            <div class="detail-card detail-card--compact">
                <h3 class="detail-card__subtitle">Thông tin cư dân</h3>
                <p class="detail-meta__value">{{ $incident->resident?->name ?? '—' }}</p>
                <p class="detail-meta__date">{{ $incident->resident?->email }}</p>
                <p class="detail-meta__date">{{ $incident->resident?->phone }}</p>
            </div>

            @if ($incident->apartment)
                <div class="detail-card detail-card--compact">
                    <h3 class="detail-card__subtitle">Căn hộ</h3>
                    <p class="detail-meta__value">{{ $incident->apartment->unit_number ?? '#' . $incident->apartment->id }}</p>
                </div>
            @endif

            <div class="detail-card detail-card--compact">
                <h3 class="detail-card__subtitle">Thời gian</h3>
                <div class="detail-meta__row">
                    <span class="detail-meta__label">Gửi lúc</span>
                    <span class="detail-meta__value">{{ $incident->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if ($incident->assigned_at)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">Phân công</span>
                        <span class="detail-meta__value">{{ $incident->assigned_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                @if ($incident->resolved_at)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">Xử lý xong</span>
                        <span class="detail-meta__value">{{ $incident->resolved_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                @if ($incident->confirmed_at)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">Xác nhận</span>
                        <span class="detail-meta__value">{{ $incident->confirmed_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>

            @if ($incident->assignedTo)
                <div class="detail-card detail-card--compact">
                    <h3 class="detail-card__subtitle">Kỹ thuật viên</h3>
                    <div class="technician-info">
                        <div class="technician-avatar">{{ strtoupper(substr($incident->assignedTo->name, 0, 1)) }}</div>
                        <div>
                            <p class="technician-name">{{ $incident->assignedTo->name }}</p>
                            <p class="technician-role">Kỹ thuật viên</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
