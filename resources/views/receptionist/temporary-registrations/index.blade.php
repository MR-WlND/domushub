@extends('layouts.receptionist.master')

@section('page_title', 'Quản lý Tạm trú - Tạm vắng')

@push('styles')
<style>
    .tr-page {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        /* Removed background and padding to match other pages */
    }
    
    .tr-container {
        /* Removed card styling to blend with the layout */
        margin-bottom: 24px;
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

    .btn-create {
        display: inline-flex;
        align-items: center;
        background-color: #00236f;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-create:hover {
        background-color: #0b57d0;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 35, 111, 0.2);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 35, 111, 0.06);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon.blue { background-color: #e0f2fe; color: #0284c7; }
    .stat-icon.yellow { background-color: #fef3c7; color: #d97706; }
    .stat-icon.red { background-color: #fee2e2; color: #dc2626; }
    .stat-icon.green { background-color: #dcfce7; color: #16a34a; }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 10px;
        line-height: 1;
    }

    .filter-container {
        background: #f8f9ff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 24px;
    }

    .filter-form {
        display: flex;
        gap: 16px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
    }

    .filter-input {
        height: 40px;
        border: 1px solid #d9e2f2;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 14px;
        color: #0f172a;
        outline: none;
        background-color: #ffffff;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .filter-input:focus {
        border-color: #00236f;
        box-shadow: 0 0 0 3px rgba(0, 35, 111, 0.08);
    }

    .filter-input-with-icon {
        position: relative;
    }
    
    .filter-input-with-icon svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    
    .filter-input-with-icon input {
        padding-left: 40px;
    }

    .btn-filter {
        height: 40px;
        background-color: #00236f;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 0 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-filter:hover {
        background-color: #0b57d0;
        box-shadow: 0 4px 6px rgba(0, 35, 111, 0.2);
    }

    .table-container {
        overflow-x: auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .tr-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .tr-table th {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        padding: 14px 18px;
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        white-space: nowrap;
    }

    .tr-table td {
        font-size: 14px;
        color: #334155;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .tr-table tr:last-child td {
        border-bottom: none;
    }

    .tr-table tr:hover td {
        background-color: #f8faff;
    }

    .code-text {
        font-weight: 600;
        color: #0f172a;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }

    .action-icons {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }

    .action-btn.approve:hover { color: #16a34a; background-color: #dcfce7; }
    .action-btn.reject:hover { color: #dc2626; background-color: #fee2e2; }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #e2e8f0;
        margin-top: 8px;
    }

    .pagination-info {
        font-size: 13px;
        color: #64748b;
    }
    
    .pagination-links nav div:first-child {
        display: none;
    }
    .pagination-links svg {
        width: 16px;
        height: 16px;
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endpush

@section('content')
<div class="tr-page">
    <div class="tr-container">
        
        {{-- Header --}}
        <div class="tr-header">
            <div>
                <h1 class="tr-title">Quản lý Tạm trú - Tạm vắng</h1>
                <p class="tr-subtitle">Quản lý danh sách cư dân đăng ký tạm trú, tạm vắng và trạng thái phê duyệt hồ sơ.</p>
            </div>
            <a href="{{ route('receptionist.temporary-registrations.create') }}" class="btn-create">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tạo đơn hộ
            </a>
        </div>

        @if(session('success'))
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
                {{ session('error') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span>Tổng yêu cầu mới</span>
                    <div class="stat-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['total_new'] ?? 0 }}</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <span>Đang chờ duyệt</span>
                    <div class="stat-icon yellow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['pending'] ?? 0 }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span>Sắp hết hạn</span>
                    <div class="stat-icon red">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['expiring_soon'] ?? 0 }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span>Đã phê duyệt</span>
                    <div class="stat-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="stat-value">{{ $stats['approved'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-container">
            <form method="GET" action="{{ route('receptionist.temporary-registrations.index') }}" class="filter-form">
                <div class="filter-group" style="flex: 2;">
                    <label class="filter-label">Tìm kiếm</label>
                    <div class="filter-input-with-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Tên, căn hộ...">
                    </div>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Loại đơn</label>
                    <select name="type" class="filter-input">
                        <option value="">Tất cả</option>
                        <option value="residence" {{ request('type') == 'residence' ? 'selected' : '' }}>Tạm trú</option>
                        <option value="absence" {{ request('type') == 'absence' ? 'selected' : '' }}>Tạm vắng</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Trạng thái</label>
                    <select name="status" class="filter-input">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>

                <div class="filter-group" style="flex: 0; min-width: auto;">
                    <button type="submit" class="btn-filter">Lọc</button>
                </div>
                @if(request()->anyFilled(['search', 'type', 'status']))
                    <div class="filter-group" style="flex: 0; min-width: auto;">
                        <a href="{{ route('receptionist.temporary-registrations.index') }}" style="color: #ef4444; font-size: 14px; text-decoration: none; padding-left: 8px;">Xóa</a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="table-container">
            <table class="tr-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Người Tạm Trú / Vắng</th>
                        <th>Căn hộ</th>
                        <th>Loại đơn</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $reg)
                        <tr>
                            <td class="code-text">#REQ-{{ str_pad($reg->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                @if($reg->type == 'residence')
                                    {{ $reg->guest_name ?? 'Khách' }}
                                @else
                                    {{ $reg->user->name ?? 'N/A' }}
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $reg->apartment->apartment_number ?? 'N/A' }}</div>
                                @if($reg->apartment && $reg->apartment->floor && $reg->apartment->floor->block)
                                    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                        {{ $reg->apartment->floor->block->name }} - {{ $reg->apartment->floor->name }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $reg->type == 'residence' ? 'Tạm trú' : 'Tạm vắng' }}</td>
                            <td>
                                {{ $reg->start_date->format('d/m/Y') }} - 
                                {{ $reg->end_date ? $reg->end_date->format('d/m/Y') : 'Chưa xác định' }}
                            </td>
                            <td>
                                @php
                                    $isEnded = $reg->end_date && \Carbon\Carbon::parse($reg->end_date)->startOfDay()->lt(now()->startOfDay());
                                @endphp
                                @if($reg->status == 'pending')
                                    <span class="badge-status badge-pending">Chờ duyệt</span>
                                @elseif($reg->status == 'approved')
                                    @if($isEnded)
                                        <span class="badge-status" style="background-color: #e2e8f0; color: #475569;">Đã kết thúc</span>
                                    @else
                                        <span class="badge-status badge-approved">Đã duyệt</span>
                                    @endif
                                @else
                                    <span class="badge-status badge-rejected">Từ chối</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-icons" style="justify-content: flex-end;">
                                    {{-- Nút Xem chi tiết --}}
                                    <a href="{{ route('receptionist.temporary-registrations.edit', $reg->id) }}" class="action-btn" title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    
                                    @if($reg->status == 'pending')
                                        {{-- Nút Duyệt --}}
                                        <form action="{{ route('receptionist.temporary-registrations.approve', $reg->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="action-btn approve" title="Duyệt đơn" onclick="return confirm('Xác nhận duyệt đăng ký này?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </button>
                                        </form>

                                        {{-- Nút Từ chối --}}
                                        <button type="button" class="action-btn reject" title="Từ chối" onclick="promptReject({{ $reg->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                            </svg>
                                        </button>
                                    @endif
                                    @if($reg->status == 'approved' && !$isEnded)
                                        {{-- Nút Gia hạn --}}
                                        <a href="{{ route('receptionist.temporary-registrations.create', ['extend_id' => $reg->id]) }}" class="action-btn" title="Gia hạn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </a>

                                        {{-- Nút Kết thúc sớm --}}
                                        <form action="{{ route('receptionist.temporary-registrations.end-early', $reg->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" class="action-btn" title="Kết thúc sớm" onclick="return confirm('Xác nhận kết thúc sớm đăng ký này (cập nhật ngày kết thúc thành hôm qua)?');">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 32px; color: #64748b;">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($registrations->hasPages())
        <div class="pagination-container">
            <div class="pagination-info">
                Hiển thị {{ $registrations->firstItem() ?? 0 }}-{{ $registrations->lastItem() ?? 0 }} của {{ $registrations->total() }} kết quả
            </div>
            <div class="pagination-links">
                {{ $registrations->links() }}
            </div>
        </div>
        @else
        <div class="pagination-container" style="justify-content: flex-start;">
            <div class="pagination-info">
                Hiển thị {{ $registrations->count() }} kết quả
            </div>
        </div>
        @endif
        
    </div>
</div>

<!-- Custom Modal Từ chối -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 400px; max-width: 90%; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top: 0; color: #1e293b; font-size: 18px; font-weight: 700;">Từ chối đơn đăng ký</h3>
        <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Vui lòng nhập lý do từ chối đơn đăng ký này.</p>
        
        <form id="globalRejectForm" method="POST" action="">
            @csrf
            <textarea name="rejection_reason" id="globalRejectReason" rows="3" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 16px; font-family: inherit; font-size: 14px; resize: vertical;" placeholder="Nhập lý do..."></textarea>
            
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 16px; background: #f1f5f9; color: #475569; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px;">Hủy</button>
                <button type="button" onclick="submitRejectModal()" style="padding: 8px 16px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px;">Xác nhận Từ chối</button>
            </div>
        </form>
    </div>
</div>

<script>
    function promptReject(id) {
        let form = document.getElementById('globalRejectForm');
        form.action = `/receptionist/temporary-registrations/${id}/reject`;
        document.getElementById('globalRejectReason').value = '';
        
        let modal = document.getElementById('rejectModal');
        modal.style.display = 'flex';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
    
    function submitRejectModal() {
        let reason = document.getElementById('globalRejectReason').value;
        if (reason.trim() !== "") {
            document.getElementById('globalRejectForm').submit();
        } else {
            alert("Lý do từ chối không được để trống!");
        }
    }
</script>
@endsection
