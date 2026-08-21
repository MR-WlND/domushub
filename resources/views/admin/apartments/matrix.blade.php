@extends('layouts.admin.master')

@section('page_title', 'Sơ đồ Ma trận Căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    .matrix-container {
        padding: 40px;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        border: 1px solid #f8fafc;
        font-family: 'Inter', sans-serif;
    }
    .matrix-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 40px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 24px;
        flex-wrap: wrap;
        gap: 24px;
    }
    .matrix-title h2 {
        margin: 0 0 8px 0;
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.03em;
    }
    .matrix-title p {
        margin: 0;
        color: #64748b;
        font-size: 15px;
        font-weight: 500;
    }
    
    .matrix-controls {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .matrix-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }
    .stat-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        transition: all 0.2s;
    }
    .stat-badge:hover {
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .stat-badge .color-indicator {
        width: 12px;
        height: 12px;
        border-radius: 4px;
    }
    .stat-occupied .color-indicator { background: #10b981; box-shadow: 0 0 12px rgba(16, 185, 129, 0.5); }
    .stat-vacant .color-indicator { background: #94a3b8; }
    .stat-maintenance .color-indicator { background: #f59e0b; box-shadow: 0 0 12px rgba(245, 158, 11, 0.5); }
    
    .floor-row {
        display: flex;
        align-items: stretch;
        margin-bottom: 32px;
        position: relative;
    }
    
    .floor-label-wrap {
        width: 120px;
        display: flex;
        align-items: center;
        padding-right: 24px;
        border-right: 2px dashed #e2e8f0;
        position: relative;
        flex-shrink: 0;
    }
    
    .floor-label {
        font-weight: 800;
        font-size: 22px;
        color: #0f172a;
        letter-spacing: -0.02em;
    }
    .floor-label span {
        display: block;
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .apartments-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        padding-left: 32px;
        flex-grow: 1;
    }
    
    .apt-cell {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 110px;
        height: 110px;
        border-radius: 16px;
        padding: 16px;
        text-decoration: none;
        color: #0f172a;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .apt-cell::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        background: #e2e8f0;
        transition: width 0.2s;
    }
    
    .apt-cell:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }
    .apt-cell:hover::before {
        width: 6px;
    }
    
    .apt-cell.occupied::before { background: #10b981; }
    .apt-cell.vacant::before { background: #94a3b8; }
    .apt-cell.maintenance::before { background: #f59e0b; }
    
    .apt-number {
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.02em;
        z-index: 1;
    }
    
    .apt-info {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 1;
    }
    
    .apt-status-icon {
        position: absolute;
        bottom: -15px;
        right: -15px;
        opacity: 0.04;
        transition: all 0.4s ease;
    }
    .apt-cell:hover .apt-status-icon {
        transform: scale(1.1) rotate(-5deg);
        opacity: 0.08;
    }
    
    .apt-cell.occupied .apt-status-icon { color: #10b981; }
    .apt-cell.maintenance .apt-status-icon { color: #f59e0b; }

    .btn-return {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .btn-return:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    
    .form-select-modern {
        padding: 12px 40px 12px 20px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        font-weight: 600;
        color: #0f172a;
        font-size: 15px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 16px center;
        background-repeat: no-repeat;
        background-size: 16px 16px;
        transition: all 0.2s;
        min-width: 160px;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
    }
    .form-select-modern:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        background-color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="matrix-container">
    <div class="matrix-header">
        <div class="matrix-title">
            <h2>Sơ đồ Ma trận</h2>
            <p>Trực quan hóa trạng thái tòa nhà theo thời gian thực</p>
        </div>

        <form action="{{ route('admin.apartments.matrix') }}" method="GET" class="matrix-controls">
            <select name="block_id" class="form-select-modern" onchange="this.form.submit()">
                @foreach($blocks as $b)
                    <option value="{{ $b->id }}" {{ $blockId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.apartments.index') }}" class="btn-return">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Trở về
            </a>
        </form>
    </div>

    <div class="matrix-stats">
        <div class="stat-badge stat-occupied">
            <span class="color-indicator"></span>
            Đang ở: {{ $stats['occupied'] }}
        </div>
        <div class="stat-badge stat-vacant">
            <span class="color-indicator"></span>
            Trống: {{ $stats['vacant'] }}
        </div>
        <div class="stat-badge stat-maintenance">
            <span class="color-indicator"></span>
            Bảo trì: {{ $stats['maintenance'] }}
        </div>
    </div>

    <div class="matrix-grid">
        @forelse($floors as $floor)
            <div class="floor-row">
                <div class="floor-label-wrap">
                    <div class="floor-label">
                        {{ $floor->floor_number }}
                        <span>Tầng</span>
                    </div>
                </div>
                <div class="apartments-grid">
                    @forelse($floor->apartments as $apt)
                        <a href="{{ route('admin.apartments.show', $apt->id) }}" class="apt-cell {{ $apt->status }}" title="{{ $apt->status == 'occupied' ? 'Đang ở' : ($apt->status == 'vacant' ? 'Trống' : 'Bảo trì') }}">
                            <div class="apt-number">{{ $apt->apartment_number }}</div>
                            <div class="apt-info">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                                {{ number_format($apt->area, 0) }}m²
                            </div>
                            
                            @if($apt->status == 'occupied')
                                <svg class="apt-status-icon" xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            @elseif($apt->status == 'maintenance')
                                <svg class="apt-status-icon" xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                            @else
                                <svg class="apt-status-icon" xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
                            @endif
                        </a>
                    @empty
                        <div style="color: #94a3b8; font-size: 14px; font-weight: 500; display: flex; align-items: center; font-style: italic;">Chưa có dữ liệu căn hộ</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 80px 40px; color: #64748b; background: #f8fafc; border-radius: 20px; border: 2px dashed #cbd5e1;">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5" style="margin: 0 auto 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <div style="font-weight: 700; font-size: 18px; color: #334155; margin-bottom: 8px;">Không tìm thấy dữ liệu</div>
                <div style="font-size: 15px;">Tòa nhà này hiện chưa có dữ liệu tầng nào.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
