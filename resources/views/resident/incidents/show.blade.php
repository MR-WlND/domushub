@extends('layouts.resident.master')

@section('title', $incident->title . ' – DomusHub')

@section('content')
<div class="incidents-page">
    {{-- Back --}}
    <div class="incidents-page__hero">
        <div>
            <p class="incidents-page__eyebrow">Phản ánh #{{ $incident->id }}</p>
            <h1 class="incidents-page__title">{{ $incident->title }}</h1>
        </div>
        <a href="{{ route('resident.incidents.index') }}" class="btn-outline" id="btn-back-list">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    <div class="incident-detail-grid">
        {{-- Nội dung chính --}}
        <div class="incident-detail__main">
            <div class="detail-card">
                <div class="detail-card__header">
                    <h2 class="detail-card__title">Thông tin phản ánh</h2>
                    <span class="incident-status incident-status--{{ $incident->status }}">
                        {{ $incident->status_label }}
                    </span>
                </div>

                <div class="detail-meta-row">
                    <span class="incident-tag incident-tag--{{ $incident->category }}">{{ $incident->category_label }}</span>
                    <span class="incident-priority incident-priority--{{ $incident->priority }}">{{ $incident->priority_label }}</span>
                    <span class="detail-meta__date">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        {{ $incident->created_at->format('d/m/Y H:i') }}
                    </span>
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

            {{-- Timeline xử lý --}}
            <div class="detail-card">
                <h2 class="detail-card__title">Tiến trình xử lý</h2>
                <div class="timeline">
                    {{-- Gửi phản ánh --}}
                    <div class="timeline-item timeline-item--done">
                        <div class="timeline-dot timeline-dot--done">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Gửi phản ánh</p>
                            <p class="timeline-time">{{ $incident->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Phân công --}}
                    <div class="timeline-item {{ $incident->assigned_at ? 'timeline-item--done' : 'timeline-item--pending' }}">
                        <div class="timeline-dot {{ $incident->assigned_at ? 'timeline-dot--done' : 'timeline-dot--pending' }}">
                            @if ($incident->assigned_at)
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            @else
                                <span></span>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Phân công kỹ thuật viên</p>
                            @if ($incident->assignedTo)
                                <p class="timeline-desc">KTV: <strong>{{ $incident->assignedTo->name }}</strong></p>
                                <p class="timeline-time">{{ $incident->assigned_at?->format('d/m/Y H:i') }}</p>
                            @else
                                <p class="timeline-time timeline-time--pending">Chờ phân công...</p>
                            @endif
                        </div>
                    </div>

                    {{-- Xử lý --}}
                    <div class="timeline-item {{ in_array($incident->status, ['in_progress','resolved','confirmed']) ? 'timeline-item--done' : 'timeline-item--pending' }}">
                        <div class="timeline-dot {{ in_array($incident->status, ['in_progress','resolved','confirmed']) ? 'timeline-dot--done' : 'timeline-dot--pending' }}">
                            @if (in_array($incident->status, ['in_progress','resolved','confirmed']))
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            @else
                                <span></span>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Kỹ thuật viên xử lý</p>
                            @if ($incident->technician_note)
                                <p class="timeline-desc">{{ $incident->technician_note }}</p>
                            @endif
                            @if ($incident->resolved_at)
                                <p class="timeline-time">{{ $incident->resolved_at->format('d/m/Y H:i') }}</p>
                            @else
                                <p class="timeline-time timeline-time--pending">Đang xử lý...</p>
                            @endif
                        </div>
                    </div>

                    {{-- Xác nhận hoàn thành --}}
                    <div class="timeline-item {{ $incident->status === 'confirmed' ? 'timeline-item--done' : 'timeline-item--pending' }}">
                        <div class="timeline-dot {{ $incident->status === 'confirmed' ? 'timeline-dot--confirmed' : 'timeline-dot--pending' }}">
                            @if ($incident->status === 'confirmed')
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            @else
                                <span></span>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <p class="timeline-title">Quản lý xác nhận hoàn thành</p>
                            @if ($incident->confirmed_at)
                                <p class="timeline-time">{{ $incident->confirmed_at->format('d/m/Y H:i') }}</p>
                            @else
                                <p class="timeline-time timeline-time--pending">Chờ xác nhận...</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar thông tin --}}
        <div class="incident-detail__sidebar">
            <div class="detail-card detail-card--compact">
                <h3 class="detail-card__subtitle">Trạng thái hiện tại</h3>
                <span class="incident-status incident-status--{{ $incident->status }} incident-status--lg">
                    {{ $incident->status_label }}
                </span>
            </div>

            @if ($incident->assignedTo)
                <div class="detail-card detail-card--compact">
                    <h3 class="detail-card__subtitle">Kỹ thuật viên phụ trách</h3>
                    <div class="technician-info">
                        <div class="technician-avatar">{{ strtoupper(substr($incident->assignedTo->name, 0, 1)) }}</div>
                        <div>
                            <p class="technician-name">{{ $incident->assignedTo->name }}</p>
                            <p class="technician-role">Kỹ thuật viên</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($incident->confirmedBy)
                <div class="detail-card detail-card--compact">
                    <h3 class="detail-card__subtitle">Xác nhận bởi</h3>
                    <p class="detail-meta__value">{{ $incident->confirmedBy->name }}</p>
                    <p class="detail-meta__date">{{ $incident->confirmed_at?->format('d/m/Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
