@extends('layouts.resident.master')

@section('title', 'Tạm trú / Tạm vắng – DomusHub')

@push('styles')
<style>
    .tr-page {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        padding: 24px;
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
        background-color: #00236f;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
    }
    .btn-create:hover {
        background-color: #0b57d0;
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
    }
    .tr-table td {
        font-size: 14px;
        color: #334155;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
    }
    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }
    .action-icons { display: flex; gap: 8px; }
    .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #64748b;
        padding: 4px;
    }
    .action-btn:hover { color: #00236f; }
</style>
@endpush

@section('content')
<div class="tr-page">
    <div class="tr-header">
        <div>
            <h1 class="tr-title">Tạm trú / Tạm vắng</h1>
            <p class="tr-subtitle">Quản lý các yêu cầu tạm trú, tạm vắng của căn hộ.</p>
        </div>
        <a href="{{ route('resident.temporary-registrations.create') }}" class="btn-create">
            + Đăng ký mới
        </a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 24px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-container">
        <table class="tr-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Loại</th>
                    <th>Người Tạm Trú / Vắng</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                    <tr>
                        <td>#REQ-{{ str_pad($reg->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $reg->type == 'residence' ? 'Tạm trú' : 'Tạm vắng' }}</td>
                        <td>
                            @if($reg->type == 'residence')
                                {{ $reg->guest_name ?? 'Khách' }}
                            @else
                                {{ $reg->user->name ?? 'N/A' }}
                            @endif
                        </td>
                        <td>
                            {{ $reg->start_date->format('d/m/Y') }} - 
                            {{ $reg->end_date ? $reg->end_date->format('d/m/Y') : 'Chưa xác định' }}
                        </td>
                        <td>
                            @if($reg->status == 'pending')
                                <span class="badge-status badge-pending">Chờ duyệt</span>
                            @elseif($reg->status == 'approved')
                                <span class="badge-status badge-approved">Đã duyệt</span>
                            @else
                                <span class="badge-status badge-rejected">Từ chối</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-icons">
                                <a href="{{ route('resident.temporary-registrations.show', $reg->id) }}" class="action-btn" title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($reg->status == 'pending')
                                <form action="{{ route('resident.temporary-registrations.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn này?');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn" style="color: #dc2626;" title="Hủy đơn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @if($reg->status == 'approved')
                                    <a href="{{ route('resident.temporary-registrations.create', ['extend_id' => $reg->id]) }}" class="action-btn" style="color: #16a34a;" title="Gia hạn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('resident.temporary-registrations.end-early', $reg->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn báo cáo kết thúc sớm (hôm nay)?');" style="margin:0;">
                                        @csrf
                                        <button type="submit" class="action-btn" style="color: #d97706;" title="Kết thúc sớm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                        <td colspan="6" style="text-align: center; padding: 32px; color: #64748b;">Chưa có đơn đăng ký nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 16px;">
        {{ $registrations->links() }}
    </div>
</div>
@endsection
