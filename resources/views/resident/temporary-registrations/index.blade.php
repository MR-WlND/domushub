@extends('layouts.resident.master')

@section('title', 'Tạm trú / Tạm vắng – DomusHub')

@push('styles')
<style>
    .tr-page {
        max-width: 1440px;
        margin: 0 auto;
        /* Global layout already provides padding via .resident-content */
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
        background-color: #2563eb;
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
        background-color: #1d4ed8;
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

    /* Table */
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
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .tr-table td {
        font-size: 14px;
        color: #334155;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .tr-table tbody tr:last-child td {
        border-bottom: none;
    }
    .tr-table tbody tr:hover {
        background: #f8fafc;
    }

    /* ID code */
    .reg-code {
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
    }

    /* Person name in blue */
    .person-name {
        color: #2563eb;
        font-weight: 500;
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

    /* Footer */
    .tr-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 14px;
        font-size: 13px;
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

    /* Mobile Responsive Card Layout */
    @media (max-width: 768px) {
        /* Header responsive */
        .tr-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }
        .btn-create {
            justify-content: center;
            width: 100%;
        }

        /* Convert table to cards */
        .table-container {
            border: none;
            background: transparent;
        }
        .tr-table, .tr-table tbody, .tr-table tr, .tr-table td {
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
        .tr-table thead {
            display: none; /* Hide header columns */
        }
        .tr-table tr {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            padding: 16px;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .tr-table tr:hover {
            background: #ffffff;
        }
        
        /* Position ID Code & Status Badge at the top of card */
        .tr-table td.td-code {
            border-bottom: 1px solid #f1f5f9;
            padding: 0 0 16px 0 !important;
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .tr-table td.td-code::before {
            content: 'MÃ ĐƠN';
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 6px;
        }
        .tr-table td.td-code .reg-code {
            font-size: 18px;
            color: #00236f;
            font-weight: 500;
        }
        
        /* Style rest of cells as key-value rows with icons */
        .tr-table td.td-type,
        .tr-table td.td-person,
        .tr-table td.td-time {
            padding: 10px 0 10px 36px !important;
            border-bottom: none !important;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            font-size: 13px;
            color: #1e293b;
            position: relative;
        }

        /* Label details */
        .tr-table td.td-type::before {
            content: 'Loại';
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .tr-table td.td-person::before {
            content: 'Người Tạm trú / vắng';
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .tr-table td.td-time::before {
            content: 'Thời gian';
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        /* Icons using background-image */
        .tr-table td.td-type::after {
            content: '';
            position: absolute;
            left: 0;
            top: 14px;
            width: 20px;
            height: 20px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%2364748b"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>') no-repeat center;
            background-size: contain;
        }
        .tr-table td.td-person::after {
            content: '';
            position: absolute;
            left: 0;
            top: 14px;
            width: 20px;
            height: 20px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%2364748b"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>') no-repeat center;
            background-size: contain;
        }
        .tr-table td.td-time::after {
            content: '';
            position: absolute;
            left: 0;
            top: 14px;
            width: 20px;
            height: 20px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%2364748b"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>') no-repeat center;
            background-size: contain;
        }

        /* Move status badge to the top-right next to Code ID */
        .tr-table td.td-status {
            position: absolute;
            top: 16px;
            right: 16px;
            padding: 0 !important;
            width: auto;
            border-bottom: none !important;
        }

        /* Action buttons row at the bottom right */
        .tr-table td.td-action {
            border-top: 1px solid #f1f5f9;
            padding: 12px 0 0 0 !important;
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }
    }
</style>
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

    {{-- Table --}}
    <div class="table-container">
        <table class="tr-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Loại</th>
                    <th>Người tạm trú / vắng</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                    <tr>
                        <td class="td-code"><span class="reg-code">#REQ-{{ str_pad($reg->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                        <td class="td-type">{{ $reg->type == 'residence' ? 'Tạm trú' : 'Tạm vắng' }}</td>
                        <td class="td-person">
                            <span class="person-name">
                                @if($reg->type == 'residence')
                                    {{ $reg->guest_name ?? 'Khách' }}
                                @else
                                    {{ $reg->user->name ?? 'N/A' }}
                                @endif
                            </span>
                        </td>
                        <td class="td-time">
                            {{ $reg->start_date->format('d/m/Y') }} - 
                            {{ $reg->end_date ? $reg->end_date->format('d/m/Y') : 'Chưa xác định' }}
                        </td>
                        <td class="td-status">
                            @if($reg->status == 'pending')
                                <span class="badge-status badge-pending">Chờ duyệt</span>
                            @elseif($reg->status == 'approved')
                                <span class="badge-status badge-approved">Đã duyệt</span>
                            @else
                                <span class="badge-status badge-rejected">Từ chối</span>
                            @endif
                        </td>
                        <td class="td-action">
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

                                {{-- Extend / End-early (approved only) --}}
                                @if($reg->status == 'approved')
                                    <a href="{{ route('resident.temporary-registrations.create', ['extend_id' => $reg->id]) }}"
                                       class="action-btn btn-extend" title="Gia hạn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </a>
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8; font-size: 14px;">
                            Chưa có đơn đăng ký nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Footer: count + pagination --}}
    <div class="tr-footer">
        <span>Hiển thị {{ $registrations->count() }} kết quả</span>
        <div>{{ $registrations->links() }}</div>
    </div>

</div>
@endsection
