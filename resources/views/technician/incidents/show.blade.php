@extends('layouts.technician.master')

@section('page_title', 'Chi tiết công việc #' . $incident->id . ' – DomusHub')

@section('content')
<div class="manager-page">
    <div class="page-header">
        <div>
            <p class="page-header__eyebrow">Công việc #{{ $incident->id }}</p>
            <h1 class="page-header__title">{{ $incident->title }}</h1>
        </div>
        <a href="{{ route('technician.incidents.index') }}" class="btn-outline" id="btn-back-list">
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
        {{-- Main Content --}}
        <div class="incident-detail__main">

            {{-- Thông tin phản ánh --}}
            <div class="detail-card">
                <div class="detail-card__header">
                    <h2 class="detail-card__title">Nội dung phản ánh sự cố</h2>
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

            {{-- Cập nhật trạng thái xử lý --}}
            @if (in_array($incident->status, ['assigned', 'in_progress']))
                <div class="detail-card detail-card--action" id="update-status-section">
                    <h2 class="detail-card__title">Cập nhật tiến độ xử lý</h2>
                    <form action="{{ route('technician.incidents.update-status', $incident->id) }}" method="POST" id="form-update-status" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label" for="status">Trạng thái công việc</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="in_progress" {{ $incident->status === 'in_progress' ? 'selected' : '' }}>
                                    🔧 Đang tiến hành xử lý
                                </option>
                                <option value="resolved">
                                    ✅ Đã hoàn thành xử lý
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="note">Ghi chú kỹ thuật (mô tả kết quả xử lý, vật tư thay thế nếu có...)</label>
                            <textarea name="note" id="note" class="form-textarea" rows="4" 
                                placeholder="Nhập ghi chú chi tiết hoặc báo cáo kết quả tại đây...">{{ old('note', $incident->technician_note) }}</textarea>
                            @error('note')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="images">Ảnh kết quả xử lý (không bắt buộc)</label>
                            <input type="file" name="images[]" id="images" class="form-input" accept="image/*" multiple>
                            @error('images.*')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            <p class="form-hint">PNG, JPG, WEBP – Tối đa 5MB mỗi ảnh</p>
                        </div>


                        <button type="submit" class="btn-primary" id="btn-submit-status">
                            Cập nhật tiến độ
                        </button>
                    </form>
                </div>
            @else
                <div class="detail-card detail-card--technician">
                    <h2 class="detail-card__title">Báo cáo của bạn</h2>
                    @if ($incident->technician_note)
                        <p class="detail-desc"><strong>Ghi chú:</strong> {{ $incident->technician_note }}</p>
                    @else
                        <p class="detail-desc" style="color: var(--color-text-muted)">Không có ghi chú nào.</p>
                    @endif
                    
                    @if ($incident->resolved_at)
                        <p class="timeline-time" style="margin-top: 0.5rem;">Cập nhật xong lúc: {{ $incident->resolved_at->format('d/m/Y H:i') }}</p>
                    @endif

                    @if ($incident->status === 'resolved')
                        <div class="alert alert--info" style="margin-top: 1rem; margin-bottom: 0;">
                            Sự cố đã được bạn đánh dấu giải quyết xong. Đang chờ Quản lý tòa nhà kiểm tra và xác nhận.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="incident-detail__sidebar">
            <div class="detail-card detail-card--compact">
                <h3 class="detail-card__subtitle">Vị trí & Liên hệ</h3>
                @if ($incident->apartment)
                    <p class="detail-meta__value" style="font-size: 1.1rem; font-weight: 600; color: #00236f;">
                        Căn hộ {{ $incident->apartment->unit_number ?? '#' . $incident->apartment->id }}
                    </p>
                    @if ($incident->apartment->building)
                        <p class="detail-meta__date">Tòa nhà: {{ $incident->apartment->building->name }}</p>
                    @endif
                @else
                    <p class="detail-meta__value">Chung cư DomusHub</p>
                @endif
                
                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 0.75rem 0;">
                
                <p class="detail-meta__label" style="font-size: 0.75rem; text-transform: uppercase;">Cư dân gửi phản ánh</p>
                <p class="detail-meta__value">{{ $incident->resident?->name ?? '—' }}</p>
                <p class="detail-meta__date">SĐT: {{ $incident->resident?->phone ?? '—' }}</p>
                <p class="detail-meta__date">Email: {{ $incident->resident?->email ?? '—' }}</p>
            </div>

            <div class="detail-card detail-card--compact">
                <h3 class="detail-card__subtitle">Thông tin lịch trình</h3>
                <div class="detail-meta__row">
                    <span class="detail-meta__label">Ngày gửi phản ánh</span>
                    <span class="detail-meta__value">{{ $incident->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if ($incident->assigned_at)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">Ngày phân công</span>
                        <span class="detail-meta__value">{{ $incident->assigned_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                @if ($incident->resolved_at)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">Giải quyết lúc</span>
                        <span class="detail-meta__value">{{ $incident->resolved_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                @if ($incident->confirmed_at)
                    <div class="detail-meta__row">
                        <span class="detail-meta__label">Quản lý xác nhận</span>
                        <span class="detail-meta__value">{{ $incident->confirmed_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>

            @if ($incident->assignedBy)
                <div class="detail-card detail-card--compact">
                    <h3 class="detail-card__subtitle">Người phân công</h3>
                    <p class="detail-meta__value">{{ $incident->assignedBy->name }}</p>
                    <p class="detail-meta__date">Bộ phận Quản lý</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
