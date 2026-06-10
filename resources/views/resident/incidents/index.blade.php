@extends('layouts.resident.master')

@section('title', 'Phản ánh của tôi – DomusHub')

@section('content')
<div class="incidents-page">
    {{-- Header --}}
    <div class="incidents-page__hero">
        <div>
            <p class="incidents-page__eyebrow">Phản ánh & Hỗ trợ</p>
            <h1 class="incidents-page__title">Phản ánh của tôi</h1>
            <p class="incidents-page__subtitle">Theo dõi tình trạng xử lý các phản ánh bạn đã gửi.</p>
        </div>
        <a href="{{ route('resident.incidents.create') }}" class="btn-primary" id="btn-create-incident">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Gửi phản ánh mới
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert--success" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Danh sách --}}
    @if ($incidents->isEmpty())
        <div class="incidents-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                </path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <p>Bạn chưa có phản ánh nào.</p>
            <a href="{{ route('resident.incidents.create') }}" class="btn-primary">Gửi phản ánh đầu tiên</a>
        </div>
    @else
        <div class="incidents-list">
            @foreach ($incidents as $incident)
                <a href="{{ route('resident.incidents.show', $incident->id) }}" class="incident-card"
                    id="incident-{{ $incident->id }}">
                    <div class="incident-card__top">
                        <div class="incident-card__meta">
                            <span class="incident-tag incident-tag--{{ $incident->category }}">
                                {{ $incident->category_label }}
                            </span>
                            <span class="incident-priority incident-priority--{{ $incident->priority }}">
                                {{ $incident->priority_label }}
                            </span>
                        </div>
                        <span class="incident-status incident-status--{{ $incident->status }}">
                            {{ $incident->status_label }}
                        </span>
                    </div>
                    <h3 class="incident-card__title">{{ $incident->title }}</h3>
                    <p class="incident-card__desc">{{ Str::limit($incident->description, 120) }}</p>
                    <div class="incident-card__footer">
                        <span class="incident-card__date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            {{ $incident->created_at->format('d/m/Y H:i') }}
                        </span>
                        @if ($incident->assignedTo)
                            <span class="incident-card__technician">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                KTV: {{ $incident->assignedTo->name }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="incidents-pagination">
            {{ $incidents->links() }}
        </div>
    @endif
</div>
@endsection
