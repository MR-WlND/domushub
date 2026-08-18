@extends('layouts.resident.master')

@section('title', 'Tất cả thông báo')

@push('styles')
    @vite(['resources/css/pages/resident/announcements/index.css'])
@endpush

@section('content')
<div class="ra-page">
    <div class="ra-header">
        <h1 class="ra-title">Tất cả thông báo</h1>
        <p class="ra-subtitle">Cập nhật những thông tin mới nhất từ ban quản lý tòa nhà Urban Living.</p>

        <div class="ra-filters">
            <a href="{{ route('resident.announcements.index', ['tab' => 'all']) }}" class="ra-filter-btn {{ $tab === 'all' ? 'active' : '' }}">Tất cả</a>
            <a href="{{ route('resident.announcements.index', ['tab' => 'warning']) }}" class="ra-filter-btn {{ $tab === 'warning' ? 'active' : '' }}">Khẩn cấp</a>
            <a href="{{ route('resident.announcements.index', ['tab' => 'maintenance']) }}" class="ra-filter-btn {{ $tab === 'maintenance' ? 'active' : '' }}">Bảo trì</a>
            <a href="{{ route('resident.announcements.index', ['tab' => 'event']) }}" class="ra-filter-btn {{ $tab === 'event' ? 'active' : '' }}">Sự kiện</a>
            <a href="{{ route('resident.announcements.index', ['tab' => 'general']) }}" class="ra-filter-btn {{ $tab === 'general' ? 'active' : '' }}">Chung</a>
        </div>
    </div>

    <div class="ra-list">
        @forelse($announcements as $ann)
            @php
                $catRaw = strtolower($ann->category ?? '');
                $catText = $categoryMap[$catRaw] ?? ($ann->category ?? 'Chung');
                
                // Color mapping for tags based on the image
                $tagBg = '#f3f4f6';
                $tagColor = '#374151';
                
                if ($catRaw === 'warning' || $catRaw === 'urgent') {
                    $tagBg = '#fee2e2';
                    $tagColor = '#ef4444';
                } elseif ($catRaw === 'maintenance') {
                    $tagBg = '#d1fae5';
                    $tagColor = '#059669';
                } elseif ($catRaw === 'event') {
                    $tagBg = '#e0e7ff';
                    $tagColor = '#4f46e5';
                }
            @endphp
            <div class="ra-item {{ $ann->pinned ? 'pinned' : '' }}">
                <div class="ra-item-content">
                    <div class="ra-meta">
                        @if($ann->pinned)
                            <span class="ra-tag" style="background-color: #b91c1c; color: #ffffff;"><i class="fa-solid fa-triangle-exclamation"></i> QUAN TRỌNG</span>
                            <span class="ra-tag" style="background-color: #f3f4f6; color: #4b5563;">{{ mb_strtoupper($catText) }}</span>
                        @else
                            <span class="ra-tag" style="background-color: {{ $tagBg }}; color: {{ $tagColor }};">{{ mb_strtoupper($catText) }}</span>
                        @endif
                        <span class="ra-date">{{ $ann->created_at->isToday() ? 'Hôm nay, ' . $ann->created_at->format('H:i') : $ann->created_at->format('d/m/Y') }}</span>
                    </div>
                    <h3 class="ra-item-title">{{ $ann->title }}</h3>
                    <p class="ra-item-excerpt">{{ Str::limit(strip_tags($ann->content), 150) }}</p>
                    <a href="{{ route('resident.announcements.show', $ann->id) }}" class="ra-readmore">Xem chi tiết</a>
                </div>
                
                @if($ann->image_path)
                    <div class="ra-item-img" style="background-image: url('{{ asset('storage/' . $ann->image_path) }}')"></div>
                @else
                    <div class="ra-item-img placeholder">
                        <i class="fa-regular fa-newspaper"></i>
                    </div>
                @endif
            </div>
        @empty
            <div class="ra-empty">
                <p>Chưa có thông báo nào.</p>
            </div>
        @endforelse
    </div>

    <div class="ra-pagination">
        {{ $announcements->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
