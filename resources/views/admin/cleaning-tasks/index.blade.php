@extends('layouts.admin.master')

@section('page_title', 'Giao việc vệ sinh – DomusHub')

@push('styles')
<style>
.ct-page{max-width:1100px;}
.ct-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
.ct-header h1{font-size:24px;font-weight:800;color:#0f172a;}
.ct-header p{font-size:13px;color:#64748b;margin-top:2px;}
.btn-create{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#082B7A,#1a3f9c);color:white;padding:11px 20px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:.2s;}
.btn-create:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(8,43,122,.25);}
.ct-filters{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.ct-filters select,.ct-filters input{height:40px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 12px;font-size:13px;font-family:inherit;color:#334155;background:white;}
.ct-filters select:focus,.ct-filters input:focus{outline:none;border-color:#082B7A;box-shadow:0 0 0 3px rgba(8,43,122,.08);}
.ct-table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04);}
.ct-table th{text-align:left;padding:12px 16px;font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #e2e8f0;}
.ct-table td{padding:14px 16px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;vertical-align:middle;}
.ct-table tr:hover td{background:#f8fafc;}
.ct-task-title{font-weight:700;color:#0f172a;}
.ct-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;font-size:10.5px;font-weight:700;}
.ct-badge--high{background:#fee2e2;color:#dc2626;}
.ct-badge--medium{background:#fef3c7;color:#d97706;}
.ct-badge--low{background:#d1fae5;color:#059669;}
.ct-badge--done{background:#d1fae5;color:#059669;}
.ct-badge--progress{background:#dbeafe;color:#2563eb;}
.ct-badge--pending{background:#f1f5f9;color:#64748b;}
.ct-actions{display:flex;gap:6px;}
.ct-actions button{background:none;border:1px solid #e2e8f0;border-radius:6px;padding:6px 10px;font-size:11px;cursor:pointer;color:#64748b;transition:.15s;}
.ct-actions button:hover{border-color:#dc2626;color:#dc2626;}
.alert-box{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;}
</style>
@endpush

@section('content')
<div class="ct-page">
    <div class="ct-header">
        <div>
            <h1>Giao việc vệ sinh</h1>
            <p>Quản lý và giao công việc cho nhân viên vệ sinh</p>
        </div>
        <a href="{{ portal_route('cleaning-tasks.create') }}" class="btn-create"><i class="fa-solid fa-plus"></i> Tạo công việc mới</a>
    </div>

    @if(session('success'))
    <div class="alert-box"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form class="ct-filters" method="GET" action="{{ portal_route('cleaning-tasks.index') }}">
        <input type="date" name="date" value="{{ $date ?? '' }}">
        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="progress" {{ request('status') == 'progress' ? 'selected' : '' }}>Đang làm</option>
            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Hoàn thành</option>
        </select>
        <select name="staff_id">
            <option value="">Tất cả nhân viên</option>
            @foreach($cleaningStaff as $staff)
            <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
            @endforeach
        </select>
        <button type="submit" style="height:40px;padding:0 16px;background:#082B7A;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;">Lọc</button>
    </form>

    <table class="ct-table">
        <thead>
            <tr>
                <th>Công việc</th>
                <th>Nhân viên</th>
                <th>Khu vực</th>
                <th>Thời gian</th>
                <th>Ưu tiên</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td><span class="ct-task-title">{{ $task->title }}</span></td>
                <td>{{ $task->assignedUser->name ?? '—' }}</td>
                <td>{{ $task->area }}</td>
                <td>{{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</td>
                <td><span class="ct-badge ct-badge--{{ $task->priority }}">{{ $task->priority === 'high' ? 'Cao' : ($task->priority === 'medium' ? 'TB' : 'Thấp') }}</span></td>
                <td><span class="ct-badge ct-badge--{{ $task->status }}">{{ $task->status === 'done' ? 'Hoàn thành' : ($task->status === 'progress' ? 'Đang làm' : 'Chờ xử lý') }}</span></td>
                <td class="ct-actions">
                    <form method="POST" action="{{ portal_route('cleaning-tasks.destroy', $task->id) }}" onsubmit="return confirm('Xóa công việc này?')">@csrf @method('DELETE')<button type="submit">Xóa</button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:32px;">Không có công việc nào.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
