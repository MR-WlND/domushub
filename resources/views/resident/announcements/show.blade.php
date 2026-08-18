@extends('layouts.resident.master')

@section('title', $announcement->title . ' – BQL Urban Living')

@push('styles')
    @vite(['resources/css/pages/resident/announcements/index.css'])
    <style>
        .ra-detail-page {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            font-family: 'Inter', sans-serif;
        }
        
        .ra-breadcrumb {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 24px;
        }
        .ra-breadcrumb a {
            color: #4b5563;
            text-decoration: none;
        }
        .ra-breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .ra-tags {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        
        .ra-detail-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 16px 0;
            line-height: 1.3;
            letter-spacing: -0.02em;
        }
        
        .ra-meta-row {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.9rem;
            color: #4b5563;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f3f4f6;
        }
        .ra-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .ra-cover {
            width: 100%;
            height: auto;
            border-radius: 12px;
            margin-bottom: 32px;
        }
        
        .ra-content {
            font-size: 1rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: 60px;
        }
        .ra-content p {
            margin-bottom: 16px;
        }
        
        /* Related section */
        .ra-related-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .ra-related-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .ra-related-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: #4b5563;
            text-decoration: none;
        }
        .ra-related-link:hover {
            color: #111827;
        }
        
        .ra-related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }
        
        .ra-related-card {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .ra-related-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }
        .ra-related-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .ra-rc-tag {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 99px;
            letter-spacing: 0.05em;
            background: #f9fafb;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .ra-rc-date {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }
        .ra-rc-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
            line-height: 1.4;
        }
    </style>
@endpush

@section('content')
<div class="ra-detail-page">
    
    <div class="ra-breadcrumb">
        <a href="{{ route('resident.dashboard') }}">Trang chủ</a> &nbsp;›&nbsp; 
        <a href="{{ route('resident.announcements.index') }}">Thông báo</a> &nbsp;›&nbsp; 
        <span style="color: #111827; font-weight: 500;">Chi tiết thông báo</span>
    </div>

    @php
        $catRaw = strtolower($announcement->category ?? '');
        $catText = $categoryMap[$catRaw] ?? ($announcement->category ?? 'Chung');
        
        $tagBg = '#f3f4f6';
        $tagColor = '#374151';
        
        if ($catRaw === 'warning' || $catRaw === 'urgent') {
            $tagBg = '#b91c1c';
            $tagColor = '#ffffff';
        } elseif ($catRaw === 'maintenance') {
            $tagBg = '#f3f4f6';
            $tagColor = '#4b5563';
        } elseif ($catRaw === 'event') {
            $tagBg = '#e0e7ff';
            $tagColor = '#4f46e5';
        }
    @endphp

    <div class="ra-tags">
        @if($announcement->pinned)
            <span class="ra-tag" style="background-color: #b91c1c; color: #ffffff;"><i class="fa-solid fa-triangle-exclamation"></i> QUAN TRỌNG</span>
        @endif
        <span class="ra-tag" style="background-color: {{ $announcement->pinned ? '#f3f4f6' : $tagBg }}; color: {{ $announcement->pinned ? '#4b5563' : $tagColor }};">{{ mb_strtoupper($catText) }}</span>
    </div>

    <h1 class="ra-detail-title">{{ $announcement->title }}</h1>

    <div class="ra-meta-row">
        <div class="ra-meta-item">
            <i class="fa-regular fa-calendar" style="color: #6b7280;"></i>
            <span>{{ $announcement->created_at->format('d \T\h\á\n\g m, Y') }}</span>
        </div>
        <div class="ra-meta-item">
            <i class="fa-regular fa-circle-user" style="color: #6b7280;"></i>
            <span>Ban Quản Lý Tòa Nhà</span>
        </div>
    </div>

    @if($announcement->image_path)
        <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Cover" class="ra-cover">
    @endif

    <div class="ra-content">
        {!! $announcement->content !!}
    </div>

    @if($relatedAnnouncements->count() > 0)
    <div class="ra-related">
        <div class="ra-related-header">
            <h2 class="ra-related-title">Thông báo liên quan</h2>
            <a href="{{ route('resident.announcements.index') }}" class="ra-related-link">XEM TẤT CẢ &rarr;</a>
        </div>
        
        <div class="ra-related-grid">
            @foreach($relatedAnnouncements as $rel)
                @php
                    $relCatRaw = strtolower($rel->category ?? '');
                    $relCatText = $categoryMap[$relCatRaw] ?? ($rel->category ?? 'Chung');
                @endphp
                <a href="{{ route('resident.announcements.show', $rel->id) }}" class="ra-related-card">
                    <div class="ra-related-card-top">
                        <span class="ra-rc-tag">{{ mb_strtoupper($relCatText) }}</span>
                        <span class="ra-rc-date">{{ $rel->created_at->format('d \T\h\g m') }}</span>
                    </div>
                    <h3 class="ra-rc-title">{{ Str::limit($rel->title, 70) }}</h3>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
