@extends('layouts.admin.master')

@section('page_title', ($pageTitle ?? 'Chi tiết Lịch sử Quản trị Hệ thống') . ' – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Chi tiết Thao tác</h1>
            <p class="db-header__sub">Thông tin chi tiết về hành động và dữ liệu thay đổi của thao tác trên hệ thống.</p>
        </div>
        <div class="db-header__actions">
            <a href="{{ portal_route('system-logs.index') }}" class="db-btn db-btn--outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 24px;">
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0;">Thông tin chung</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px;">
            <div>
                <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Thời gian</strong>
                <div style="font-size: 15px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</div>
            </div>
            <div>
                <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Người thực hiện</strong>
                <div style="font-size: 15px; font-weight: 500;">{{ $log->causer->name ?? 'Hệ thống' }}</div>
            </div>
            <div>
                <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Thao tác thực hiện</strong>
                <div style="font-size: 15px; color: #0b57d0; font-weight: 500;">{{ $log->description }}</div>
            </div>
            <div>
                <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">Đối tượng bị thay đổi</strong>
                <div style="font-size: 15px;">{{ $log->properties['target'] ?? '—' }}</div>
            </div>
            <div>
                <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">IP Address</strong>
                <div style="font-size: 15px;">{{ $log->properties['ip_address'] ?? '—' }}</div>
            </div>
            <div style="grid-column: 1 / -1;">
                <strong style="color: #64748b; font-size: 13px; display: block; margin-bottom: 4px;">User Agent</strong>
                <div style="font-size: 15px; background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;">{{ $log->properties['user_agent'] ?? '—' }}</div>
            </div>
        </div>

    </div>

</div>

@push('styles')
    @vite(['resources/css/pages/admin/statistics.css', 'resources/css/pages/admin/dashboard.css', 'resources/css/pages/admin/activity-logs.css'])
@endpush
@endsection
