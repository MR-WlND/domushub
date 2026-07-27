@extends('layouts.receptionist.master')

@section('page_title', 'Chi tiết phản ánh #{{ $ticket->id }} – Lễ tân DomusHub')

@push('styles')
<style>
    .back-link{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#7B3FE4;text-decoration:none;margin-bottom:20px;}
    .back-link:hover{text-decoration:underline;}
    .detail-grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;}
    .card-box{background:white;border-radius:14px;padding:24px;box-shadow:0 2px 8px rgba(123,63,228,.05);margin-bottom:20px;}
    .card-box h2{font-size:16px;font-weight:700;color:#1B2559;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #F4F7FE;display:flex;align-items:center;gap:8px;}
    .info-row{display:flex;justify-content:space-between;align-items:flex-start;padding:10px 0;border-bottom:1px solid #F4F7FE;font-size:13px;}
    .info-row:last-child{border-bottom:none;}
    .info-label{color:#A3AED0;font-weight:500;flex-shrink:0;width:140px;}
    .info-value{color:#1B2559;font-weight:600;text-align:right;}
    .badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;}
    .badge-warning{background:#FFF4E5;color:#FF9B05;}
    .badge-info{background:#EEF2FF;color:#3652D9;}
    .badge-success{background:#E6F9F0;color:#05CD99;}
    .badge-secondary{background:#F4F7FE;color:#707EAE;}
    .badge-danger{background:#FFF0F0;color:#EE5D50;}
    .desc-box{background:#F8F5FF;border-radius:10px;padding:16px;font-size:13.5px;line-height:1.6;color:#1B2559;}
    .progress-item{display:flex;gap:14px;padding:12px 0;border-bottom:1px solid #F4F7FE;}
    .progress-item:last-child{border-bottom:none;}
    .progress-dot{width:10px;height:10px;border-radius:50%;background:#7B3FE4;margin-top:4px;flex-shrink:0;}
    .progress-meta{font-size:11.5px;color:#A3AED0;margin-top:3px;}
</style>
@endpush

@section('content')
<a href="{{ route('receptionist.tickets.index') }}" class="back-link">
    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
</a>

<div class="detail-grid">
    <!-- Main -->
    <div>
        <div class="card-box">
            <h2>{{ $ticket->title }}</h2>
            <div class="info-row">
                <span class="info-label">Trạng thái</span>
                @php
                    $statusMap = [
                        'pending'     => ['label' => 'Chờ xử lý',   'class' => 'badge-warning'],
                        'assigned'    => ['label' => 'Đã phân công','class' => 'badge-info'],
                        'in_progress' => ['label' => 'Đang xử lý',  'class' => 'badge-info'],
                        'completed'   => ['label' => 'Hoàn thành',  'class' => 'badge-success'],
                        'cancelled'   => ['label' => 'Đã hủy',      'class' => 'badge-secondary'],
                    ];
                    $s = $statusMap[$ticket->status] ?? ['label' => $ticket->status, 'class' => 'badge-secondary'];
                @endphp
                <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ưu tiên</span>
                <span class="info-value">{{ match($ticket->priority) {
                    'urgent' => '🔴 Khẩn cấp',
                    'high'   => '🟠 Cao',
                    'medium' => '🟡 Trung bình',
                    'low'    => '🟢 Thấp',
                    default  => $ticket->priority,
                } }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Loại phản ánh</span>
                <span class="info-value">{{ $ticket->ticket_type === 'complaint' ? 'Khiếu nại' : 'Báo cáo sự cố' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cư dân gửi</span>
                <span class="info-value">{{ $ticket->sender->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Căn hộ</span>
                <span class="info-value">{{ $ticket->apartment->apartment_number ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kỹ thuật viên</span>
                <span class="info-value">{{ $ticket->handler->name ?? 'Chưa phân công' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ngày gửi</span>
                <span class="info-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Mô tả -->
        <div class="card-box">
            <h2>Mô tả chi tiết</h2>
            <div class="desc-box">{{ $ticket->description ?: 'Không có mô tả.' }}</div>
        </div>

        <!-- Tiến trình -->
        @if($ticket->progresses->count())
        <div class="card-box">
            <h2>Tiến trình xử lý</h2>
            @foreach($ticket->progresses->sortByDesc('created_at') as $progress)
            <div class="progress-item">
                <div class="progress-dot"></div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1B2559;">{{ $progress->note }}</div>
                    <div class="progress-meta">{{ $progress->user->name ?? '—' }} · {{ $progress->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Sidebar info -->
    <div>
        <div class="card-box">
            <h2>Thông tin thêm</h2>
            <div class="info-row">
                <span class="info-label">Mã phản ánh</span>
                <span class="info-value">#{{ $ticket->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cập nhật lần cuối</span>
                <span class="info-value">{{ $ticket->updated_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
