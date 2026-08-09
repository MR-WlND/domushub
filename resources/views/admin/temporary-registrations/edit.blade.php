@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Đăng ký Tạm trú / Tạm vắng')

@push('styles')
<style>
    .tr-form-page {
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    
    .tr-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }

    .tr-title {
        font-size: 28px;
        font-weight: 700;
        color: #00236f;
        margin: 0 0 6px 0;
        line-height: 1.2;
    }

    .tr-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .btn-back {
        font-size: 14px;
        font-weight: 600;
        color: #0b57d0;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-back:hover {
        text-decoration: underline;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }

    .tr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }

    .info-group {
        margin-bottom: 20px;
    }

    .info-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 15px;
        color: #0f172a;
        font-weight: 500;
    }
    
    .info-value-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        color: #334155;
        line-height: 1.5;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .file-attachment {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: #f8f9ff;
        border: 1px dashed #c7d2fe;
        border-radius: 8px;
        margin-top: 8px;
    }

    .file-icon {
        width: 40px;
        height: 40px;
        background: #e0e7ff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
    }

    .file-link {
        font-size: 14px;
        font-weight: 600;
        color: #4f46e5;
        text-decoration: none;
    }

    .file-link:hover {
        text-decoration: underline;
    }

    .btn {
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        width: 100%;
        margin-bottom: 12px;
    }

    .btn-success {
        background-color: #16a34a;
        color: #ffffff;
    }
    
    .btn-success:hover {
        background-color: #15803d;
        box-shadow: 0 4px 6px rgba(22, 163, 74, 0.2);
    }

    .btn-danger {
        background-color: #ffffff;
        color: #dc2626;
        border: 1px solid #dc2626;
    }
    
    .btn-danger:hover {
        background-color: #fef2f2;
    }
    
    .btn-primary {
        background-color: #00236f;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #0b57d0;
    }

    .rejection-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        padding: 16px;
        border-radius: 8px;
        margin-top: 16px;
    }

    @media (max-width: 992px) {
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
            <h1 class="tr-title">Chi tiết Đăng ký</h1>
            <p class="tr-subtitle">Mã đơn: #{{ str_pad($temporaryRegistration->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <a href="{{ route('admin.temporary-registrations.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    <div class="main-grid">
        <!-- Cột Trái: Thông tin chi tiết -->
        <div class="left-col">
            <div class="tr-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Thông tin Cư dân & Căn hộ
                    </h3>
                </div>
                
                <div class="grid-2">
                    <div class="info-group">
                        <div class="info-label">Cư dân đăng ký</div>
                        <div class="info-value">{{ $temporaryRegistration->user ? $temporaryRegistration->user->name : 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Số điện thoại</div>
                        <div class="info-value">{{ $temporaryRegistration->user ? $temporaryRegistration->user->phone : 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">CCCD / CMND</div>
                        <div class="info-value">{{ ($temporaryRegistration->user && $temporaryRegistration->user->cccd) ? $temporaryRegistration->user->cccd : 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Căn hộ</div>
                        <div class="info-value">
                            {{ $temporaryRegistration->apartment ? $temporaryRegistration->apartment->apartment_number : 'N/A' }} 
                            @if($temporaryRegistration->apartment && $temporaryRegistration->apartment->floor)
                                - Tầng {{ $temporaryRegistration->apartment->floor->name }}
                                - Tòa {{ $temporaryRegistration->apartment->floor->block->name }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="tr-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Chi tiết Đăng ký
                    </h3>
                </div>

                <div class="grid-2">
                    <div class="info-group">
                        <div class="info-label">Loại đăng ký</div>
                        <div class="info-value" style="color: #00236f; font-weight: 700;">
                            {{ $temporaryRegistration->type === 'residence' ? 'Tạm trú' : 'Tạm vắng' }}
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Ngày tạo đơn</div>
                        <div class="info-value">{{ $temporaryRegistration->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Thời gian bắt đầu</div>
                        <div class="info-value">{{ $temporaryRegistration->start_date ? $temporaryRegistration->start_date->format('d/m/Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Thời gian kết thúc</div>
                        <div class="info-value">{{ $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('d/m/Y') : 'Không xác định' }}</div>
                    </div>
                </div>

                <div class="info-group" style="margin-top: 8px;">
                    <div class="info-label">Lý do chi tiết</div>
                    <div class="info-value-box">
                        {{ $temporaryRegistration->reason ?: 'Không có lý do được cung cấp.' }}
                    </div>
                </div>

                @if($temporaryRegistration->attachment_path)
                <div class="info-group" style="margin-top: 24px;">
                    <div class="info-label">Giấy tờ đính kèm</div>
                    <div class="file-attachment">
                        <div class="file-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </div>
                        <div>
                            <a href="{{ Storage::url($temporaryRegistration->attachment_path) }}" target="_blank" class="file-link">Xem tệp đính kèm</a>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Nhấn vào để mở file (JPG, PNG, PDF)</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Cột Phải: Trạng thái & Thao tác -->
        <div class="right-col">
            <div class="tr-card" style="background: #f8fafc;">
                <h3 class="info-label" style="margin-bottom: 16px;">Trạng thái hiện tại</h3>
                
                <div style="margin-bottom: 24px;">
                    @if($temporaryRegistration->status == 'pending')
                        <span class="status-badge badge-pending" style="font-size: 15px; padding: 8px 16px;">Chờ duyệt</span>
                    @elseif($temporaryRegistration->status == 'approved')
                        <span class="status-badge badge-approved" style="font-size: 15px; padding: 8px 16px;">Đã duyệt</span>
                    @else
                        <span class="status-badge badge-rejected" style="font-size: 15px; padding: 8px 16px;">Từ chối</span>
                    @endif
                </div>

                @if($temporaryRegistration->status == 'rejected' && $temporaryRegistration->rejection_reason)
                    <div class="rejection-box">
                        <div class="info-label" style="color: #b91c1c;">Lý do từ chối:</div>
                        <div style="font-size: 14px; color: #7f1d1d; margin-top: 8px;">
                            {{ $temporaryRegistration->rejection_reason }}
                        </div>
                    </div>
                @endif

                @if($temporaryRegistration->status == 'pending')
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
                    
                    <h3 class="info-label" style="margin-bottom: 16px;">Thao tác xử lý</h3>
                    
                    <form action="{{ route('admin.temporary-registrations.approve', $temporaryRegistration->id) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Bạn xác nhận muốn duyệt Đơn đăng ký này?');" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Duyệt đơn đăng ký
                        </button>
                    </form>

                    <button type="button" onclick="promptReject({{ $temporaryRegistration->id }})" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Từ chối đơn
                    </button>
                @endif
                
                @if($temporaryRegistration->status == 'approved' && $temporaryRegistration->approver)
                    <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0;">
                    <div class="info-group" style="margin-bottom: 0;">
                        <div class="info-label">Người duyệt</div>
                        <div class="info-value" style="font-size: 14px;">{{ $temporaryRegistration->approver->name }}</div>
                    </div>
                @endif
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
            form.action = `/admin/temporary-registrations/${id}/reject`;
            document.getElementById('globalRejectReason').value = reason.trim();
            form.submit();
        } else if (reason !== null) {
            alert("Bạn phải nhập lý do từ chối!");
        }
    }
</script>
@endif
@endsection
