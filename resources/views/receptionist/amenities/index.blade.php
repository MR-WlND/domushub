@extends('layouts.receptionist.master')

@section('page_title', 'Đặt lịch tiện ích – Lễ tân DomusHub')

@section('topbar_left')
<nav style="font-size:13px;color:#A3AED0;display:flex;align-items:center;gap:6px;">
    <a href="{{ route('receptionist.dashboard') }}" style="color:#7B3FE4;text-decoration:none;">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
    <span>Đặt lịch tiện ích</span>
</nav>
@endsection

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <h1 style="font-size:24px;font-weight:700;color:#00236f;margin:0;">Đặt lịch tiện ích</h1>
</div>

<!-- Filter -->
<div class="filter-card">
    <form method="GET" action="{{ route('receptionist.amenities.index') }}" class="filter-form">
        <select name="facility_id" class="filter-select">
            <option value="">-- Tất cả tiện ích --</option>
            @foreach($facilities as $facility)
            <option value="{{ $facility->id }}" {{ request('facility_id') == $facility->id ? 'selected' : '' }}>
                {{ $facility->name }}
            </option>
            @endforeach
        </select>
        <select name="status" class="filter-select">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
            <option value="cancelled"{{ request('status') === 'cancelled'? 'selected' : '' }}>Đã hủy</option>
        </select>
        <button type="submit" class="filter-btn-submit"><i class="fa-solid fa-filter"></i> Lọc</button>
        @if(request('facility_id') || request('status'))
            <a href="{{ route('receptionist.amenities.index') }}" class="filter-btn-reset">
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
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Tiện ích</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Cư dân</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Thời gian đặt</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Thời gian sử dụng</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Trạng thái</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr style="border-bottom:1px solid #e2e8f0; transition:background-color 0.2s ease;">
                <td style="padding:14px 20px; color:#757682; font-size:13px;">{{ $booking->id }}</td>
                <td style="padding:14px 20px; color:#0b1c30; font-size:14px;"><strong>{{ $booking->facility->name ?? '—' }}</strong></td>
                <td style="padding:14px 20px; color:#0b1c30; font-size:14px;">{{ $booking->user->name ?? '—' }}</td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('d/m H:i') }}
                    – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </td>
                <td style="padding:14px 20px;">
                    @php
                        $map = [
                            'pending'   => ['Chờ duyệt', 'warning'],
                            'approved'  => ['Đã duyệt',  'success'],
                            'rejected'  => ['Từ chối',   'danger'],
                            'cancelled' => ['Đã hủy',    'secondary'],
                        ];
                        [$label, $cls] = $map[$booking->status] ?? [$booking->status, 'secondary'];
                    @endphp
                    <span class="dashboard-status-pill dashboard-status-pill--{{ $cls }}">{{ $label }}</span>
                </td>
                <td style="padding:14px 20px; display:flex; gap:8px;">
                    @if($booking->status === 'pending')
                    <form method="POST" action="{{ route('receptionist.amenities.approve', $booking->id) }}">
                        @csrf
                        <button style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; border:none; background:#e6f4ea; color:#137333; cursor:pointer;">
                            <i class="fa-solid fa-check"></i> Duyệt
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receptionist.amenities.reject', $booking->id) }}" onsubmit="return confirmReject(this);">
                        @csrf
                        <input type="hidden" name="reason" value="">
                        <button type="submit" style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; border:none; background:#fee2e2; color:#b91c1c; cursor:pointer;">
                            <i class="fa-solid fa-xmark"></i> Từ chối
                        </button>
                    </form>
                    @else
                    <span style="font-size:13px;color:#94a3b8;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        Chưa có đặt lịch nào.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($bookings->hasPages())
<div style="margin-top:20px;">{{ $bookings->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
    function confirmReject(form) {
        const reason = prompt("Nhập lý do từ chối đặt lịch tiện ích này:");
        if (reason === null) {
            return false; // Hủy gửi form
        }
        form.querySelector('input[name="reason"]').value = reason;
        return true;
    }
</script>
@endpush
