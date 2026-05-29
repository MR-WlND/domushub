@extends('layouts.admin.master')

@section('page_title', 'Quản lý phương tiện')

@section('content')
<div class="admin-container">
    <div class="admin-header">
        <div>
            <h1>Quản lý phương tiện</h1>
            <p>Duyệt hoặc từ chối các yêu cầu đăng ký xe của cư dân.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Biển số</th>
                    <th>Loại xe</th>
                    <th>Căn hộ</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $v)
                <tr>
                    <td><span class="text-bold">{{ $v->license_plate }}</span></td>
                    <td>{{ ucfirst($v->vehicle_type) }}</td>
                    <td>{{ $v->apartment->apartment_number ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ $v->status }}">
                            {{ ucfirst($v->status) }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        @if($v->status == 'pending')
                            <div class="action-buttons">
                                <form action="{{ route('admin.vehicles.approve', $v) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-approve">Duyệt</button>
                                </form>
                                <form action="{{ route('admin.vehicles.reject', $v) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-reject">Từ chối</button>
                                </form>
                            </div>
                        @else
                            <span class="text-muted">Đã xử lý</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    .admin-container { padding: 24px; max-width: 1200px; margin: auto; }
    .admin-header { margin-bottom: 24px; }
    .admin-header h1 { font-size: 1.5rem; color: #1e293b; margin-bottom: 4px; }
    .admin-header p { color: #64748b; }

    .card-table { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; border: 1px solid #f1f5f9; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f8fafc; padding: 16px; text-align: left; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
    .table td { padding: 16px; border-bottom: 1px solid #f1f5f9; color: #334155; }

    .text-bold { font-weight: 700; color: #0f172a; }
    .text-muted { color: #94a3b8; font-size: 0.85rem; }

    .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
    .badge-pending { background: #fffbeb; color: #b45309; }
    .badge-approved { background: #f0fdf4; color: #166534; }
    .badge-rejected { background: #fef2f2; color: #991b1b; }

    .action-buttons { display: flex; gap: 8px; justify-content: flex-end; }
    .btn { padding: 6px 14px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
    .btn-approve { background: #0f172a; color: #fff; }
    .btn-approve:hover { background: #334155; }
    .btn-reject { background: #f1f5f9; color: #475569; }
    .btn-reject:hover { background: #e2e8f0; }

    .alert-success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; border: 1px solid #bbf7d0; }
</style>
@endpush
