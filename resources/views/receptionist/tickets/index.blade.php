@extends('layouts.receptionist.master')

@section('page_title', 'Phản ánh – Lễ tân DomusHub')

@push('styles')
<style>
    .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
    .page-header h1{font-size:22px;font-weight:800;color:#1B2559;}
    .filter-bar{background:white;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;box-shadow:0 2px 8px rgba(123,63,228,.05);}
    .filter-bar select,.filter-bar input{border:1px solid #E9EDF7;border-radius:8px;padding:8px 14px;font-size:13px;font-family:inherit;color:#1B2559;outline:none;background:white;}
    .filter-bar select:focus,.filter-bar input:focus{border-color:#7B3FE4;}
    .filter-bar button{padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;font-family:inherit;background:#7B3FE4;color:white;border:none;cursor:pointer;}
    .table-box{background:white;border-radius:14px;box-shadow:0 2px 8px rgba(123,63,228,.05);overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead{background:#F8F5FF;}
    thead th{padding:14px 16px;font-size:12px;font-weight:700;color:#7B3FE4;text-transform:uppercase;letter-spacing:.5px;text-align:left;}
    tbody tr{border-bottom:1px solid #F4F7FE;transition:.15s;}
    tbody tr:hover{background:#FAFBFF;}
    tbody td{padding:13px 16px;font-size:13px;color:#1B2559;}
    .badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;}
    .badge-warning{background:#FFF4E5;color:#FF9B05;}
    .badge-info{background:#EEF2FF;color:#3652D9;}
    .badge-success{background:#E6F9F0;color:#05CD99;}
    .badge-danger{background:#FFF0F0;color:#EE5D50;}
    .badge-secondary{background:#F4F7FE;color:#707EAE;}
    .priority-urgent{color:#EE5D50;font-weight:700;}
    .priority-high{color:#FF9B05;font-weight:700;}
    .priority-medium{color:#3652D9;}
    .priority-low{color:#A3AED0;}
    .empty-state{text-align:center;padding:60px 20px;color:#A3AED0;}
    .empty-state i{font-size:40px;margin-bottom:12px;display:block;}
    .view-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;background:#F5F0FF;color:#7B3FE4;text-decoration:none;transition:.15s;}
    .view-btn:hover{background:#EDE5FF;}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-triangle-exclamation" style="color:#3652D9;margin-right:8px;"></i>Phản ánh & Sự cố</h1>
</div>

<!-- Filter -->
<div class="filter-bar">
    <form method="GET" action="{{ route('receptionist.tickets.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;width:100%;align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tiêu đề, tên cư dân...">
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="assigned"    {{ request('status') === 'assigned'    ? 'selected' : '' }}>Đã phân công</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>Hoàn thành</option>
            <option value="cancelled"   {{ request('status') === 'cancelled'   ? 'selected' : '' }}>Đã hủy</option>
        </select>
        <button type="submit"><i class="fa-solid fa-filter"></i> Lọc</button>
    </form>
</div>

<!-- Table -->
<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Cư dân</th>
                <th>Căn hộ</th>
                <th>Ưu tiên</th>
                <th>Trạng thái</th>
                <th>Ngày gửi</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td style="color:#A3AED0;font-size:12px;">{{ $ticket->id }}</td>
                <td><strong>{{ Str::limit($ticket->title, 45) }}</strong></td>
                <td>{{ $ticket->sender->name ?? '—' }}</td>
                <td>{{ $ticket->apartment->apartment_number ?? '—' }}</td>
                <td>
                    <span class="priority-{{ $ticket->priority }}">
                        {{ match($ticket->priority) {
                            'urgent' => '🔴 Khẩn',
                            'high'   => '🟠 Cao',
                            'medium' => '🟡 TB',
                            'low'    => '🟢 Thấp',
                            default  => $ticket->priority,
                        } }}
                    </span>
                </td>
                <td>
                    @php
                        $statusMap = [
                            'pending'     => ['label' => 'Chờ xử lý',  'class' => 'badge-warning'],
                            'assigned'    => ['label' => 'Đã phân công','class' => 'badge-info'],
                            'in_progress' => ['label' => 'Đang xử lý', 'class' => 'badge-info'],
                            'completed'   => ['label' => 'Hoàn thành', 'class' => 'badge-success'],
                            'cancelled'   => ['label' => 'Đã hủy',     'class' => 'badge-secondary'],
                        ];
                        $s = $statusMap[$ticket->status] ?? ['label' => $ticket->status, 'class' => 'badge-secondary'];
                    @endphp
                    <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                </td>
                <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('receptionist.tickets.show', $ticket->id) }}" class="view-btn">
                        <i class="fa-solid fa-eye"></i> Xem
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Chưa có phản ánh nào.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($tickets->hasPages())
<div style="margin-top:20px;">{{ $tickets->links() }}</div>
@endif
@endsection
