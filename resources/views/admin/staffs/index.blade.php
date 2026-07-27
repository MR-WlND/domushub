@extends('layouts.admin.master')

@section('page_title', 'Quản lý Nhân sự')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@section('content')
<div class="dashboard-card" style="margin-bottom:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <h1 style="font-size:24px; font-weight:700; color:#0b1c30; margin:0;">Quản lý nhân sự</h1>
        <a href="{{ route('admin.staffs.create') }}" class="btn-new-broadcast" style="width:auto; margin:0;"><i class="fa-solid fa-plus"></i> Thêm nhân sự mới</a>
    </div>

    @if (session('success'))
        <div style="background:#e6f4ea; color:#137333; padding:12px; border-radius:8px; margin-bottom:16px; font-size:14px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.staffs.index') }}" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:24px; align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, số điện thoại, CCCD..." style="padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; min-width:250px;">
        
        <select name="department_id" style="padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; background:#fff;">
            <option value="">-- Tất cả phòng ban --</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>

        <select name="status" style="padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; background:#fff;">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="active" @selected(request('status') === 'active')>Đang làm việc</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Đã nghỉ việc</option>
        </select>

        <button type="submit" class="btn-new-broadcast" style="width:auto; margin:0; padding:10px 20px;">Lọc</button>
        <a href="{{ route('admin.staffs.index') }}" style="padding:10px 20px; border-radius:8px; background:#f1f5f9; color:#475569; font-weight:600; text-decoration:none;">Xóa lọc</a>
    </form>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:left;">
            <thead style="background:#f8f9fa; border-bottom:1px solid #e2e8f0;">
                <tr>
                    <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase;">Nhân sự</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase;">Phòng ban</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase;">Liên hệ</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase;">Trạng thái</th>
                    <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#475569; text-transform:uppercase;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staffs as $staff)
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td style="padding:16px 20px;">
                        <div style="font-weight:700; color:#0b1c30; font-size:14px;">{{ $staff->full_name }}</div>
                        <div style="color:#64748b; font-size:12px; margin-top:4px;">
                            <i class="fa-solid fa-id-card"></i> {{ $staff->cccd ?? 'Chưa cập nhật' }}
                        </div>
                    </td>
                    <td style="padding:16px 20px; color:#475569; font-size:14px;">
                        {{ optional($staff->department)->name ?? 'Chưa phân bổ' }}
                    </td>
                    <td style="padding:16px 20px; color:#475569; font-size:14px;">
                        {{ $staff->phone ?? 'Chưa cập nhật' }}
                    </td>
                    <td style="padding:16px 20px;">
                        @if($staff->status === 'active')
                            <span class="dashboard-status-pill dashboard-status-pill--success">Đang làm việc</span>
                        @else
                            <span class="dashboard-status-pill dashboard-status-pill--secondary">Đã nghỉ việc</span>
                        @endif
                    </td>
                    <td style="padding:16px 20px;">
                        <a href="{{ portal_route('staffs.edit', $staff->id) }}" style="display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:6px; background:#f1f5f9; color:#0b57d0; font-size:13px; font-weight:600; text-decoration:none; margin-right:6px;">
                            <i class="fa-solid fa-pen-to-square"></i> Chi tiết
                        </a>


                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:40px; text-align:center; color:#64748b;">
                        Không tìm thấy nhân sự nào phù hợp.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $staffs->appends(request()->query())->links() }}
    </div>
</div>
@endsection
