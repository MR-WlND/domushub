@extends('layouts.receptionist.master')

@section('page_title', 'Chi tiết Đăng ký Tạm trú / Tạm vắng')

@push('styles')
<style>
    .tr-form-page {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        color: #334155;
    }
    
    .tr-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 32px;
    }

    .tr-title {
        font-size: 32px;
        font-weight: 800;
        color: #00236f;
        margin: 0 0 8px 0;
        letter-spacing: -0.02em;
    }

    .tr-subtitle {
        font-size: 15px;
        color: #64748b;
        margin: 0;
        font-weight: 500;
    }

    .btn-back {
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .btn-back:hover {
        background: #f8fafc;
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .main-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
        align-items: start;
    }

    .tr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 36px;
        margin-bottom: 32px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        position: relative;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        margin-bottom: 32px;
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
    }

    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-purple { background: #faf5ff; color: #a855f7; }
    .icon-emerald { background: #ecfdf5; color: #10b981; }

    .card-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.01em;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 30px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        width: 100%;
        text-align: center;
    }

    .badge-pending { background-color: #fef3c7; color: #b45309; }
    .badge-approved { background-color: #dcfce7; color: #15803d; }
    .badge-rejected { background-color: #fee2e2; color: #b91c1c; }

    .info-group {
        margin-bottom: 28px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .info-value {
        font-size: 16px;
        color: #1e293b;
        font-weight: 600;
        line-height: 1.5;
    }
    
    .info-value-box {
        background: #f8fafc;
        border-left: 4px solid #cbd5e1;
        border-radius: 0 12px 12px 0;
        padding: 16px 20px;
        font-size: 15px;
        color: #475569;
        line-height: 1.6;
        font-style: italic;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px 24px;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 32px 24px;
    }

    .file-attachment {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        margin-top: 16px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .file-attachment:hover {
        border-color: #cbd5e1;
        box-shadow: 0 12px 24px -8px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    .file-icon {
        width: 48px;
        height: 48px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all 0.3s ease;
    }

    .file-attachment:hover .file-icon {
        background: #eff6ff;
        color: #3b82f6;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
        transition: color 0.3s ease;
    }

    .file-attachment:hover .file-name {
        color: #3b82f6;
    }
    
    .file-meta {
        font-size: 13px;
        font-weight: 500;
        color: #94a3b8;
    }

    .btn {
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 24px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        width: 100%;
        margin-bottom: 16px;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: #ffffff;
        color: #ef4444;
        border: 2px solid #fee2e2;
    }
    
    .btn-danger:hover {
        background: #fef2f2;
        border-color: #fca5a5;
    }
    
    .rejection-box {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 20px;
        border-radius: 0 12px 12px 0;
        margin-top: 24px;
    }

    .sidebar-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .section-divider {
        height: 1px;
        background: #e2e8f0;
        margin: 32px 0;
        border: none;
    }

    @media (max-width: 1100px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="tr-form-page">
    <div class="tr-header">
        <div>
            <h1 class="tr-title">Hồ sơ Đăng ký</h1>
            <p class="tr-subtitle">Mã đơn tham chiếu: <strong>#REQ-{{ str_pad($temporaryRegistration->id, 5, '0', STR_PAD_LEFT) }}</strong></p>
        </div>
        <a href="{{ route('receptionist.temporary-registrations.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 16px 24px; border-radius: 12px; margin-bottom: 32px; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 16px 24px; border-radius: 12px; margin-bottom: 32px; font-size: 15px; font-weight: 600; display: flex; align-items: center; gap: 12px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="main-grid">
        <!-- Cột Trái: Thông tin chi tiết -->
        <div class="left-col">
            
            <div class="tr-card">
                <div class="card-header">
                    <div class="card-icon icon-blue">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <h3 class="card-title">Cư dân & Căn hộ</h3>
                </div>
                @php
                    $apartmentOwner = null;
                    if ($temporaryRegistration->apartment) {
                        $ownerResident = $temporaryRegistration->apartment->residents()->where('relationship', 'owner')->first();
                        $apartmentOwner = $ownerResident ? $ownerResident->user : $temporaryRegistration->apartment->users()->first();
                    }
                @endphp
                <div class="grid-2">
                    <div class="info-group">
                        <div class="info-label">Chủ hộ / Đại diện căn hộ</div>
                        <div class="info-value">
                            {{ $apartmentOwner ? $apartmentOwner->name : 'Chưa có thông tin' }}
                            @if($apartmentOwner && $apartmentOwner->id === $temporaryRegistration->user_id)
                                <span style="font-size: 12px; color: #10b981; font-style: italic; margin-left: 8px;">(Người bảo lãnh)</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Số điện thoại liên hệ</div>
                        <div class="info-value">{{ $apartmentOwner ? $apartmentOwner->phone : 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Định danh CCCD/CMND</div>
                        <div class="info-value">{{ ($apartmentOwner && $apartmentOwner->cccd) ? $apartmentOwner->cccd : 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Căn hộ liên kết</div>
                        <div class="info-value">
                            {{ $temporaryRegistration->apartment ? $temporaryRegistration->apartment->apartment_number : 'N/A' }} 
                            @if($temporaryRegistration->apartment && $temporaryRegistration->apartment->floor)
                                <span style="color: #64748b; font-weight: 500;">
                                    (Tầng {{ $temporaryRegistration->apartment->floor->name }} - Tòa {{ $temporaryRegistration->apartment->floor->block->name }})
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($temporaryRegistration->type === 'residence')
            <div class="tr-card">
                <div class="card-header">
                    <div class="card-icon icon-purple">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 class="card-title">Chi tiết Khách Tạm trú</h3>
                </div>
                <div class="grid-3">
                    <div class="info-group">
                        <div class="info-label">Họ và tên</div>
                        <div class="info-value">{{ $temporaryRegistration->guest_name ?: 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Số điện thoại</div>
                        <div class="info-value">{{ $temporaryRegistration->guest_phone ?: 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">CCCD / CMND</div>
                        <div class="info-value">{{ $temporaryRegistration->guest_cccd ?: 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email cá nhân</div>
                        <div class="info-value">{{ $temporaryRegistration->guest_email ?: 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Giới tính</div>
                        <div class="info-value">
                            @if($temporaryRegistration->guest_gender == 'male') Nam
                            @elseif($temporaryRegistration->guest_gender == 'female') Nữ
                            @elseif($temporaryRegistration->guest_gender == 'other') Khác
                            @else N/A
                            @endif
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Ngày sinh</div>
                        <div class="info-value">{{ $temporaryRegistration->guest_dob ? $temporaryRegistration->guest_dob->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Quê quán</div>
                        <div class="info-value">{{ $temporaryRegistration->guest_hometown ?: 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Vai trò / Mối quan hệ</div>
                        <div class="info-value">{{ $temporaryRegistration->relationship ?: 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @endif

            <div class="tr-card">
                <div class="card-header">
                    <div class="card-icon icon-emerald">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <h3 class="card-title">Thời gian & Trích yếu</h3>
                </div>

                <div class="grid-3">
                    <div class="info-group">
                        <div class="info-label">Phân loại</div>
                        <div class="info-value" style="color: #3b82f6; font-weight: 800;">
                            {{ $temporaryRegistration->type === 'residence' ? 'Tạm trú' : 'Tạm vắng' }}
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Bắt đầu từ ngày</div>
                        <div class="info-value">{{ $temporaryRegistration->start_date ? $temporaryRegistration->start_date->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Kết thúc ngày</div>
                        <div class="info-value">{{ $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('d/m/Y') : 'Không xác định' }}</div>
                    </div>
                </div>

                <div class="info-group" style="margin-top: 8px;">
                    <div class="info-label">Mục đích / Lý do</div>
                    <div class="info-value-box">
                        {{ $temporaryRegistration->reason ?: 'Không có nội dung ghi chú được cung cấp.' }}
                    </div>
                </div>

                @if($temporaryRegistration->attachment_path || !empty($temporaryRegistration->attachments))
                <hr class="section-divider">
                <div class="info-group" style="margin-bottom: 0;">
                    <div class="info-label">Hồ sơ / Giấy tờ đính kèm</div>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px;">
                        @if($temporaryRegistration->attachment_path)
                        <a href="{{ Storage::url($temporaryRegistration->attachment_path) }}" target="_blank" class="file-attachment">
                            <div class="file-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            <div class="file-details">
                                <div class="file-name">Tệp đính kèm (cũ)</div>
                                <div class="file-meta">Nhấn để xem nội dung</div>
                            </div>
                        </a>
                        @endif

                        @if(!empty($temporaryRegistration->attachments))
                            @foreach($temporaryRegistration->attachments as $index => $path)
                            <a href="{{ Storage::url($path) }}" target="_blank" class="file-attachment">
                                <div class="file-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                </div>
                                <div class="file-details">
                                    <div class="file-name">Hồ sơ số #{{ $index + 1 }}</div>
                                    <div class="file-meta">Bản sao giấy tờ</div>
                                </div>
                            </a>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cột Phải: Trạng thái & Thao tác -->
        <div class="right-col">
            <div class="sidebar-card">
                <div class="info-label" style="text-align: center; margin-bottom: 24px; color: #64748b; font-size: 13px;">Tiến trình xét duyệt</div>
                
                <div style="margin-bottom: 32px;">
                    @if($temporaryRegistration->status == 'pending')
                        <span class="status-badge badge-pending">Chờ thẩm định</span>
                    @elseif($temporaryRegistration->status == 'approved')
                        <span class="status-badge badge-approved">Đã phê duyệt</span>
                    @else
                        <span class="status-badge badge-rejected">Từ chối cấp phép</span>
                    @endif
                </div>

                @if($temporaryRegistration->status == 'rejected' && $temporaryRegistration->rejection_reason)
                    <div class="rejection-box">
                        <div class="info-label" style="color: #b91c1c; font-size: 11px;">Phản hồi từ Ban quản lý</div>
                        <div style="font-size: 14px; color: #7f1d1d; margin-top: 8px; font-weight: 500;">
                            {{ $temporaryRegistration->rejection_reason }}
                        </div>
                    </div>
                @endif

                @if($temporaryRegistration->status == 'pending')
                    <hr class="section-divider">
                    
                    <div class="info-label" style="text-align: center; margin-bottom: 24px;">Hành động quản trị</div>
                    
                    <form action="{{ route('receptionist.temporary-registrations.approve', $temporaryRegistration->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn xác nhận hồ sơ này hợp lệ và muốn phê duyệt?');" class="btn btn-success">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Phê duyệt ngay
                        </button>
                    </form>

                    <button type="button" onclick="promptReject({{ $temporaryRegistration->id }})" class="btn btn-danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        Từ chối cấp phép
                    </button>
                @endif
                
                @if($temporaryRegistration->status == 'approved' && $temporaryRegistration->approver)
                    <hr class="section-divider">
                    <div style="text-align: center;">
                        <div class="info-label">Cán bộ phê duyệt</div>
                        <div class="info-value" style="font-size: 15px; color: #3b82f6;">{{ $temporaryRegistration->approver->name }}</div>
                        <div style="font-size: 13px; color: #94a3b8; margin-top: 4px;">{{ $temporaryRegistration->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
                
                <div style="margin-top: 32px; padding-top: 24px; border-top: 1px dashed #cbd5e1; text-align: center;">
                    <div class="info-label">Ngày tạo đơn</div>
                    <div class="info-value" style="font-size: 14px;">{{ $temporaryRegistration->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($temporaryRegistration->status == 'pending')
<form id="globalRejectForm" method="POST" action="" style="display: none;">
    @csrf
    <input type="hidden" name="rejection_reason" id="globalRejectReason">
</form>

<script>
    function promptReject(id) {
        let reason = prompt("Vui lòng nhập lý do từ chối (Bắt buộc):");
        if (reason !== null && reason.trim() !== "") {
            let form = document.getElementById('globalRejectForm');
            form.action = `/receptionist/temporary-registrations/${id}/reject`;
            document.getElementById('globalRejectReason').value = reason.trim();
            form.submit();
        } else if (reason !== null) {
            alert("Bạn phải nhập lý do từ chối!");
        }
    }
</script>
@endif
@endsection
