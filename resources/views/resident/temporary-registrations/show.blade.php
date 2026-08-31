@extends('layouts.resident.master')

@section('title', 'Chi tiết Tạm trú / Tạm vắng – DomusHub')

@push('styles')
<style>
    .resident-content { padding: 0 !important; }
    .tr-form-page { 
        font-family: 'Inter', 'Segoe UI', sans-serif;
        max-width: 1440px;
        margin: 0 auto;
        padding: 30px 40px 60px;
        box-sizing: border-box;
        width: 100%;
    }
    @media (max-width: 768px) {
        .tr-form-page { padding: 20px 16px 40px; }
    }
    .tr-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
    .tr-title { font-size: 24px; font-weight: 700; color: #00236f; margin: 0 0 6px 0; }
    .tr-subtitle { font-size: 14px; color: #64748b; margin: 0; }
    .btn-back { color: #0b57d0; text-decoration: none; font-weight: 600; }
    .tr-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; margin-bottom: 24px; }
    .info-group { margin-bottom: 16px; }
    .info-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
    .info-value { font-size: 15px; font-weight: 500; color: #0f172a; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; }
    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }
</style>
@endpush

@section('content')
<div class="tr-form-page">
    <div class="tr-header">
        <div>
            <h1 class="tr-title">Chi tiết đơn đăng ký</h1>
            <p class="tr-subtitle">Mã đơn: #REQ-{{ str_pad($temporaryRegistration->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            @if($temporaryRegistration->status == 'approved')
                <a href="{{ route('resident.temporary-registrations.print', $temporaryRegistration->id) }}" target="_blank" style="padding: 8px 16px; background-color: #00236f; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    In CT01
                </a>
            @endif
            <a href="{{ route('resident.temporary-registrations.index') }}" class="btn-back">Quay lại danh sách</a>
        </div>
    </div>

    <div class="tr-card">
        <h3 style="margin-top: 0; margin-bottom: 16px; color: #00236f;">Trạng thái đơn</h3>
        <div>
            @if($temporaryRegistration->status == 'pending')
                <span class="status-badge badge-pending">Đang chờ BQL duyệt</span>
            @elseif($temporaryRegistration->status == 'approved')
                <span class="status-badge badge-approved">Đã được phê duyệt</span>
            @else
                <span class="status-badge badge-rejected">Bị từ chối</span>
                <div style="margin-top: 12px; color: #dc2626; background: #fef2f2; padding: 12px; border-radius: 8px;">
                    <strong>Lý do từ chối:</strong> {{ $temporaryRegistration->rejection_reason }}
                </div>
            @endif
        </div>
    </div>

    @if($temporaryRegistration->type === 'residence')
    <div class="tr-card">
        <h3 style="margin-top: 0; margin-bottom: 16px; color: #0f172a;">Thông tin Khách tạm trú</h3>
        <div class="grid-2">
            <div class="info-group">
                <div class="info-label">Họ và tên</div>
                <div class="info-value">{{ $temporaryRegistration->guest_name }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Số điện thoại</div>
                <div class="info-value">{{ $temporaryRegistration->guest_phone }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">CCCD / CMND</div>
                <div class="info-value">{{ $temporaryRegistration->guest_cccd }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $temporaryRegistration->guest_email ?: 'Không có' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Giới tính</div>
                <div class="info-value">
                    @if($temporaryRegistration->guest_gender == 'male') Nam
                    @elseif($temporaryRegistration->guest_gender == 'female') Nữ
                    @elseif($temporaryRegistration->guest_gender == 'other') Khác
                    @else Không có @endif
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Ngày sinh</div>
                <div class="info-value">{{ $temporaryRegistration->guest_dob ? $temporaryRegistration->guest_dob->format('d/m/Y') : 'Không có' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Quê quán</div>
                <div class="info-value">{{ $temporaryRegistration->guest_hometown ?: 'Không có' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Mối quan hệ</div>
                <div class="info-value">{{ $temporaryRegistration->relationship ?: 'Không có' }}</div>
            </div>
        </div>
    </div>
    @endif

    <div class="tr-card">
        <h3 style="margin-top: 0; margin-bottom: 16px; color: #0f172a;">Chi tiết lưu trú</h3>
        <div class="grid-2">
            <div class="info-group">
                <div class="info-label">Từ ngày</div>
                <div class="info-value">{{ $temporaryRegistration->start_date->format('d/m/Y') }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Đến ngày</div>
                <div class="info-value">{{ $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('d/m/Y') : 'Chưa xác định' }}</div>
            </div>
            <div class="info-group" style="grid-column: 1 / -1;">
                <div class="info-label">Lý do</div>
                <div class="info-value" style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                    {{ $temporaryRegistration->reason ?: 'Không có lý do chi tiết.' }}
                </div>
            </div>
        </div>

        @if($temporaryRegistration->attachment_path || !empty($temporaryRegistration->attachments))
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
        <h4 style="margin: 0 0 16px; color: #0f172a;">Tài liệu đính kèm</h4>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            @if($temporaryRegistration->attachment_path)
                <a href="{{ Storage::url($temporaryRegistration->attachment_path) }}" target="_blank" style="color: #0b57d0;">📄 Xem tệp đính kèm (cũ)</a>
            @endif
            @if(!empty($temporaryRegistration->attachments))
                @foreach($temporaryRegistration->attachments as $index => $path)
                    <a href="{{ Storage::url($path) }}" target="_blank" style="color: #0b57d0;">📄 Xem tệp đính kèm #{{ $index + 1 }}</a>
                @endforeach
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
