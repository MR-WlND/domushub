@extends('layouts.resident.master')

@section('title', $announcement->title . ' – BQL DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/announcements/index.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="res-announcements" style="max-width: 800px;">
        {{-- Back button header --}}
        <div style="margin-bottom: 8px;">
            <a href="{{ route('resident.dashboard') }}" class="res-card__readmore" style="font-size: 14px; font-weight: 600; text-decoration: none;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ
            </a>
        </div>

        {{-- Detail card --}}
        <div class="res-detail-card">
            @if($announcement->image_path)
                <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Banner" class="res-detail-banner">
            @endif

            <div class="res-detail-content">
                {{-- Meta --}}
                <div class="res-detail-meta">
                    <span class="res-badge res-badge--{{ $announcement->category }}">
                        @if($announcement->category === 'maintenance')
                            Bảo trì kỹ thuật
                        @elseif($announcement->category === 'warning')
                            Cảnh báo khẩn cấp
                        @elseif($announcement->category === 'event')
                            Sự kiện
                        @else
                            Tin tức chung
                        @endif
                    </span>
                    <span style="font-size: 13px; color: #64748b;">
                        Đăng ngày: {{ $announcement->created_at->format('H:i d/m/Y') }} 
                        ({{ $announcement->created_at->diffForHumans() }})
                    </span>
                    @if($announcement->pinned)
                        <span style="font-size: 12px; font-weight: 700; color: #854d0e; background: #fef08a; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                            <i class="fa-solid fa-thumbtack"></i> Đã ghim
                        </span>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="res-detail-title">{{ $announcement->title }}</h1>

                {{-- Author info --}}
                <div class="res-detail-author-row">
                    @if($announcement->user && $announcement->user->avatar)
                        <img src="{{ asset('storage/' . $announcement->user->avatar) }}" alt="Avatar" class="res-detail-author-avatar">
                    @else
                        <div class="res-detail-author-avatar" style="display: flex; align-items: center; justify-content: center; background: #eff6ff; border: 1px solid #bfdbfe; color: #0b57d0; font-size: 16px; font-weight: 700;">
                            {{ strtoupper(mb_substr($announcement->user->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="res-detail-author-name">{{ $announcement->user->name ?? 'Ban Quản Trị' }}</h4>
                        <p class="res-detail-author-title">
                            @if($announcement->user && $announcement->user->role === 'admin')
                                Quản trị viên hệ thống
                            @elseif($announcement->user && $announcement->user->role === 'manager')
                                Ban Quản Lý Chung Cư
                            @else
                                Nhân viên Ban Quản Trị
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Body content --}}
                <div class="res-detail-body">
                    {!! $announcement->content !!}
                </div>

                {{-- Signature --}}
                <div class="res-detail-signature">
                    <span class="res-signature-label">Trân trọng,</span>
                    <span class="res-signature-name">Ban Quản Lý Chung Cư DomusHub</span>
                </div>
            </div>
        </div>
    </div>
@endsection
