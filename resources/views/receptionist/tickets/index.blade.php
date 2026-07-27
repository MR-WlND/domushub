@extends('layouts.receptionist.master')

@section('page_title', 'Phản ánh – Lễ tân DomusHub')

@section('topbar_left')
<nav style="font-size:13px;color:#A3AED0;display:flex;align-items:center;gap:6px;">
    <a href="{{ route('receptionist.dashboard') }}" style="color:#7B3FE4;text-decoration:none;">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
    <span>Phản ánh & Sự cố</span>
</nav>
@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <h1 style="font-size:24px;font-weight:700;color:#00236f;margin:0;">Phản ánh & Sự cố</h1>
    <a href="{{ route('receptionist.tickets.create') }}" class="btn-new-broadcast" style="width:auto; margin-bottom:0;">
        <i class="fa-solid fa-plus"></i> Tạo phản ánh
    </a>
</div>

<!-- Filter -->
<div class="filter-card">
    <form method="GET" action="{{ route('receptionist.tickets.index') }}" class="filter-form">
        <div class="filter-input-wrapper">
            <i class="fa-solid fa-magnifying-glass filter-input-icon"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Tìm tiêu đề, tên cư dân...">
        </div>
        <select name="status" class="filter-select">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="assigned"    {{ request('status') === 'assigned'    ? 'selected' : '' }}>Đã phân công</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>Hoàn thành</option>
            <option value="cancelled"   {{ request('status') === 'cancelled'   ? 'selected' : '' }}>Đã hủy</option>
        </select>
        <button type="submit" class="filter-btn-submit"><i class="fa-solid fa-filter"></i> Lọc</button>
        @if(request('search') || request('status'))
            <a href="{{ route('receptionist.tickets.index') }}" class="filter-btn-reset">
                <i class="fa-solid fa-arrows-rotate"></i> Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="dashboard-card" style="padding:0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead style="background:#f8f9ff; border-bottom:1px solid #e2e8f0;">
            <tr>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">ID</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Tiêu đề</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Cư dân</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Căn hộ</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Ưu tiên</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Trạng thái</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Ngày gửi</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr style="border-bottom:1px solid #e2e8f0; transition:background-color 0.2s ease;">
                <td style="padding:14px 20px; color:#757682; font-size:13px;">{{ $ticket->id }}</td>
                <td style="padding:14px 20px; color:#0b1c30; font-size:14px;"><strong>{{ Str::limit($ticket->title, 45) }}</strong></td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">{{ $ticket->sender->name ?? '—' }}</td>
                <td style="padding:14px 20px; color:#0b1c30; font-size:14px;">{{ $ticket->apartment->apartment_number ?? '—' }}</td>
                <td style="padding:14px 20px;">
                    <span style="font-weight:700; color: {{ match($ticket->priority) { 'urgent' => '#ba1a1a', 'high' => '#b06000', 'medium' => '#0b57d0', 'low' => '#137333', default => '#475569' } }}">
                        {{ match($ticket->priority) {
                            'urgent' => '🔴 Khẩn',
                            'high'   => '🟠 Cao',
                            'medium' => '🟡 TB',
                            'low'    => '🟢 Thấp',
                            default  => $ticket->priority,
                        } }}
                    </span>
                </td>
                <td style="padding:14px 20px;">
                    @php
                        $statusMap = [
                            'pending'     => ['label' => 'Chờ xử lý',  'class' => 'warning'],
                            'assigned'    => ['label' => 'Đã phân công','class' => 'warning'],
                            'in_progress' => ['label' => 'Đang xử lý', 'class' => 'warning'],
                            'completed'   => ['label' => 'Hoàn thành', 'class' => 'success'],
                            'cancelled'   => ['label' => 'Đã hủy',     'class' => 'secondary'],
                        ];
                        $s = $statusMap[$ticket->status] ?? ['label' => $ticket->status, 'class' => 'secondary'];
                    @endphp
                    <span class="dashboard-status-pill dashboard-status-pill--{{ $s['class'] }}">{{ $s['label'] }}</span>
                </td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">{{ $ticket->created_at->format('d/m/Y') }}</td>
                <td style="padding:14px 20px;">
                    <a href="{{ route('receptionist.tickets.show', $ticket->id) }}" style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; background:#f0f4ff; color:#0b57d0; text-decoration:none;">
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
