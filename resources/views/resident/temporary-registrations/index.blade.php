@extends('layouts.resident.master')

@section('title', 'Tạm trú / Tạm vắng – DomusHub')

@push('styles')
<style>
    .resident-content { padding: 0 !important; }
    .tr-page {
        max-width: 1440px;
        margin: 0 auto;
        padding: 30px 40px 60px;
        box-sizing: border-box;
        width: 100%;
    }
    @media (max-width: 768px) {
        .tr-page { padding: 20px 16px 40px; }
    }
    .tr-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }
    .tr-title {
        font-size: 24px;
        font-weight: 700;
        color: #00236f;
        margin: 0 0 6px 0;
    }
    .tr-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }
    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #00236f;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.18s;
    }
    .btn-create:hover {
        background-color: #001b57;
        color: #fff;
    }

    /* Alert banners */
    .alert-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Grid Layout */
    .tr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }
    .tr-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.12);
        border-radius: 8px;
        padding: 24px;
        transition: box-shadow 0.2s, transform 0.2s;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .tr-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    .tr-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .tr-card-code {
        font-size: 16px;
        font-weight: 700;
        color: #00236f;
    }
    .tr-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .tr-card-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 14px;
        color: #334155;
    }
    .tr-card-icon {
        color: #374151;
        font-size: 16px;
        width: 20px;
        text-align: center;
        margin-top: 2px;
    }
    .tr-card-content {
        display: flex;
        flex-direction: column;
    }
    .tr-card-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 2px;
    }
    .tr-card-value {
        font-weight: 500;
        color: #1e293b;
    }
    .tr-card-footer {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
    }
    
    /* ID code */
    .reg-code {
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
    }

    /* Badges */
    .badge-status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .badge-pending  { background: #fef3c7; color: #d97706; }
    .badge-approved { background: #dcfce7; color: #16a34a; }
    .badge-rejected { background: #fee2e2; color: #dc2626; }

    /* Action icons */
    .action-icons { display: flex; gap: 6px; align-items: center; }
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.15s;
        text-decoration: none;
    }
    .action-btn:hover { color: #1e293b; }
    .action-btn.btn-view:hover  { color: #2563eb; }
    .action-btn.btn-edit:hover  { color: #2563eb; }
    .action-btn.btn-delete:hover { color: #dc2626; }
    .action-btn.btn-extend:hover { color: #16a34a; }
    .action-btn.btn-end:hover   { color: #d97706; }

    /* Footer Pagination */
    .tr-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        font-size: 14px;
        color: #64748b;
    }
    .tr-footer .pagination {
        display: flex;
        gap: 4px;
        align-items: center;
    }
    .tr-footer .pagination a,
    .tr-footer .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        font-size: 13px;
        color: #475569;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.15s;
    }
    .tr-footer .pagination a:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .tr-footer .pagination span.active,
    .tr-footer .pagination [aria-current] {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .tr-footer .pagination [aria-disabled] {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Mobile Responsive adjustments */
    @media (max-width: 768px) {
        .tr-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }
        .btn-create {
            justify-content: center;
            width: 100%;
        }
        .tr-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .tr-card {
            border-radius: 12px;
            padding: 20px;
        }
        .tr-footer {
            flex-direction: column;
            gap: 16px;
        }
    }
</style>

<div id="extendModal" class="invite-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 400px; max-width: 90%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top: 0; color: #1e293b; font-size: 18px; font-weight: 700;">Gia hạn thời gian</h3>
        <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Vui lòng chọn ngày kết thúc mới.</p>
        
        <form id="extendForm" method="POST" action="">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #475569;">Ngày kết thúc (mới)</label>
                <input type="date" name="end_date" id="extend_end_date" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-family: inherit;">
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeExtendModal()" style="padding: 8px 16px; background: #f1f5f9; color: #475569; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px;">Hủy</button>
                <button type="submit" style="padding: 8px 16px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px;">Xác nhận Gia hạn</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openExtendModal(id, currentEndDate) {
        document.getElementById('extendForm').action = `/resident/temporary-registrations/${id}/extend`;
        document.getElementById('extend_end_date').value = currentEndDate;
        document.getElementById('extendModal').style.display = 'flex';
    }
    function closeExtendModal() {
        document.getElementById('extendModal').style.display = 'none';
    }
</script>
@endpush

@section('content')
<div class="tr-page">

    {{-- Header --}}
    <div class="tr-header">
        <div>
            <h1 class="tr-title">Tạm trú / Tạm vắng</h1>
            <p class="tr-subtitle">Quản lý các yêu cầu tạm trú, tạm vắng của căn hộ.</p>
        </div>
        <a href="{{ route('resident.temporary-registrations.create') }}" class="btn-create">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Đăng ký mới
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert-banner alert-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-banner alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Grid Cards --}}
    <div class="tr-grid">
        @forelse($registrations as $reg)
            <div class="tr-card">
                <div class="tr-card-header">
                    <span class="tr-card-code">#REQ-{{ str_pad($reg->id, 4, '0', STR_PAD_LEFT) }}</span>
                    @if($reg->status == 'pending')
                        <span class="badge-status badge-pending">Chờ duyệt</span>
                    @elseif($reg->status == 'approved')
                        <span class="badge-status badge-approved">Đã duyệt</span>
                    @else
                        <span class="badge-status badge-rejected">Từ chối</span>
                    @endif
                </div>
                
                <div class="tr-card-body">
                    <div class="tr-card-row">
                        <i class="fa-solid fa-list-ul tr-card-icon"></i>
                        <div class="tr-card-content">
                            <span class="tr-card-label">Loại</span>
                            <span class="tr-card-value">{{ $reg->type == 'residence' ? 'Tạm trú' : 'Tạm vắng' }}</span>
                        </div>
                    </div>
                    
                    <div class="tr-card-row">
                        <i class="fa-regular fa-user tr-card-icon"></i>
                        <div class="tr-card-content">
                            <span class="tr-card-label">Người tạm trú / vắng</span>
                            <span class="tr-card-value" style="color: #2563eb;">
                                @if($reg->type == 'residence')
                                    {{ $reg->guest_name ?? 'Khách' }}
                                @else
                                    {{ $reg->user->name ?? 'N/A' }}
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="tr-card-row">
                        <i class="fa-regular fa-clock tr-card-icon"></i>
                        <div class="tr-card-content">
                            <span class="tr-card-label">Thời gian</span>
                            <span class="tr-card-value">
                                {{ $reg->start_date->format('d/m/Y') }} - 
                                {{ $reg->end_date ? $reg->end_date->format('d/m/Y') : 'Chưa xác định' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="tr-card-footer">
                    <div class="action-icons">
                        {{-- View --}}
                        <a href="{{ route('resident.temporary-registrations.show', $reg->id) }}" class="action-btn btn-view" title="Xem chi tiết">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>

                        {{-- Edit (pending / rejected only) --}}
                        @if(in_array($reg->status, ['pending', 'rejected']))
                            <a href="{{ route('resident.temporary-registrations.edit', $reg->id) }}" class="action-btn btn-edit" title="Chỉnh sửa">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        @endif

                        {{-- Cancel (pending only) --}}
                        @if($reg->status == 'pending')
                            <form action="{{ route('resident.temporary-registrations.destroy', $reg->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete" title="Hủy đơn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        @endif

                                {{-- Extend / End-early / Print (approved only) --}}
                                @if($reg->status == 'approved')
                                    <a href="{{ route('resident.temporary-registrations.print', $reg->id) }}" target="_blank" class="action-btn" title="In Tờ khai CT01" style="color: #00236f;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="action-btn btn-extend" title="Gia hạn" onclick="openExtendModal({{ $reg->id }}, '{{ $reg->end_date ? $reg->end_date->format('Y-m-d') : '' }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('resident.temporary-registrations.end-early', $reg->id) }}" method="POST"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn báo cáo kết thúc sớm (hôm nay)?');" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="action-btn btn-end" title="Kết thúc sớm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #94a3b8; font-size: 15px; background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 12px;">
                <i class="fa-regular fa-folder-open" style="font-size: 2.5rem; margin-bottom: 12px; display: block; opacity: 0.6;"></i>
                Chưa có đơn đăng ký nào
            </div>
        @endforelse
    </div>

    {{-- Footer: count + pagination --}}
    <div class="tr-footer">
        <span>Hiển thị {{ $registrations->count() }} kết quả</span>
        <div>{{ $registrations->links() }}</div>
    </div>

</div>
@endsection
