@extends('layouts.receptionist.master')

@section('page_title', 'Chi tiết bưu phẩm #{{ $parcel->id }} – Lễ tân DomusHub')

@push('styles')
<style>
    .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #7B3FE4; text-decoration: none; margin-bottom: 20px; transition: .15s; }
    .back-link:hover { text-decoration: underline; }
    .detail-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }
    .card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(123,63,228,.07); margin-bottom: 20px; }
    .card h2 { font-size: 16px; font-weight: 700; color: #1B2559; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid #F4F7FE; display: flex; align-items: center; gap: 8px; }
    .info-row { display: flex; align-items: flex-start; padding: 11px 0; border-bottom: 1px solid #F4F7FE; font-size: 13px; gap: 12px; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #A3AED0; font-weight: 500; min-width: 150px; flex-shrink: 0; }
    .info-value { color: #1B2559; font-weight: 600; }
    .badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-warning  { background: #FFF4E5; color: #FF9B05; }
    .badge-info     { background: #EEF2FF; color: #3652D9; }
    .badge-success  { background: #E6F9F0; color: #05CD99; }
    .badge-secondary{ background: #F4F7FE; color: #707EAE; }
    /* Timeline */
    .timeline { position: relative; padding-left: 24px; }
    .timeline::before { content: ''; position: absolute; left: 7px; top: 0; bottom: 0; width: 2px; background: #F5F0FF; }
    .tl-item { position: relative; margin-bottom: 18px; }
    .tl-dot { position: absolute; left: -24px; top: 2px; width: 14px; height: 14px; border-radius: 50%; background: #7B3FE4; border: 3px solid #F5F0FF; }
    .tl-dot.inactive { background: #E9EDF7; }
    .tl-title { font-size: 13px; font-weight: 700; color: #1B2559; }
    .tl-time { font-size: 11.5px; color: #A3AED0; margin-top: 3px; }
    /* Action buttons */
    .action-area { display: flex; flex-direction: column; gap: 10px; }
    .btn-action { width: 100%; padding: 12px; border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 8px; transition: .2s; }
    .btn-notify  { background: #EEF2FF; color: #3652D9; }
    .btn-notify:hover  { background: #dde5ff; }
    .btn-receive { background: #E6F9F0; color: #05a56a; }
    .btn-receive:hover { background: #c6f0dc; }
    .btn-return  { background: #FFF4E5; color: #FF9B05; }
    .btn-return:hover  { background: #ffe0b2; }
    .desc-box { background: #F8F5FF; border-radius: 10px; padding: 14px 16px; font-size: 13.5px; line-height: 1.6; color: #1B2559; }
    @media(max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<a href="{{ route('receptionist.parcels.index') }}" class="back-link">
    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách bưu phẩm
</a>

<div class="detail-grid">

    {{-- ── Cột trái: Chi tiết ── --}}
    <div>
        <div class="card">
            <h2>
                <i class="fa-solid fa-box" style="color:#7B3FE4;"></i>
                Bưu phẩm #{{ $parcel->id }}
                <span class="badge badge-{{ $parcel->status_color }}" style="margin-left:auto;">
                    {{ $parcel->status_label }}
                </span>
            </h2>

            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-house"></i> Căn hộ nhận</span>
                <span class="info-value">{{ $parcel->apartment->apartment_number ?? '—' }}
                    @if($parcel->apartment?->floor)
                        <span style="font-weight:400;color:#A3AED0;">(Tầng {{ $parcel->apartment->floor->floor_number }})</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-user"></i> Người gửi</span>
                <span class="info-value">{{ $parcel->sender_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-barcode"></i> Mã vận đơn</span>
                <span class="info-value">{{ $parcel->tracking_code ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-truck"></i> Đơn vị vận chuyển</span>
                <span class="info-value">{{ $parcel->carrier ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fa-regular fa-clock"></i> Ngày đến</span>
                <span class="info-value">{{ $parcel->arrived_at->format('d/m/Y H:i') }}</span>
            </div>
            @if($parcel->received_at)
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-check-circle"></i> Cư dân nhận lúc</span>
                <span class="info-value" style="color:#05a56a;">{{ $parcel->received_at->format('d/m/Y H:i') }}</span>
            </div>
            @endif
            @if($parcel->returned_at)
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-rotate-left"></i> Hoàn trả lúc</span>
                <span class="info-value" style="color:#FF9B05;">{{ $parcel->returned_at->format('d/m/Y H:i') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label"><i class="fa-solid fa-user-tie"></i> Lễ tân nhập</span>
                <span class="info-value">{{ $parcel->creator->name ?? '—' }}</span>
            </div>
        </div>

        {{-- Mô tả --}}
        @if($parcel->description)
        <div class="card">
            <h2><i class="fa-solid fa-align-left" style="color:#7B3FE4;"></i> Mô tả bưu phẩm</h2>
            <div class="desc-box">{{ $parcel->description }}</div>
        </div>
        @endif

        {{-- Ghi chú --}}
        @if($parcel->note)
        <div class="card">
            <h2><i class="fa-solid fa-note-sticky" style="color:#FF9B05;"></i> Ghi chú lễ tân</h2>
            <div class="desc-box" style="background:#FFFBEB;">{{ $parcel->note }}</div>
        </div>
        @endif
    </div>

    {{-- ── Cột phải: Timeline + Actions ── --}}
    <div>
        {{-- Thao tác --}}
        @if(in_array($parcel->status, ['pending', 'notified']))
        <div class="card">
            <h2><i class="fa-solid fa-wand-magic-sparkles" style="color:#7B3FE4;"></i> Thao tác</h2>
            <div class="action-area">
                @if($parcel->status === 'pending')
                <form method="POST" action="{{ route('receptionist.parcels.notify', $parcel->id) }}">
                    @csrf
                    <button class="btn-action btn-notify">
                        <i class="fa-solid fa-bell"></i> Đánh dấu đã thông báo cư dân
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('receptionist.parcels.received', $parcel->id) }}">
                    @csrf
                    <button class="btn-action btn-receive">
                        <i class="fa-solid fa-check"></i> Xác nhận cư dân đã nhận
                    </button>
                </form>
                <form method="POST" action="{{ route('receptionist.parcels.returned', $parcel->id) }}">
                    @csrf
                    <button class="btn-action btn-return">
                        <i class="fa-solid fa-rotate-left"></i> Đánh dấu hoàn trả
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Timeline trạng thái --}}
        <div class="card">
            <h2><i class="fa-solid fa-timeline" style="color:#7B3FE4;"></i> Tiến trình</h2>
            <div class="timeline">
                <div class="tl-item">
                    <div class="tl-dot"></div>
                    <div class="tl-title">Bưu phẩm đến</div>
                    <div class="tl-time">{{ $parcel->arrived_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="tl-item">
                    <div class="tl-dot {{ in_array($parcel->status, ['notified','received','returned']) ? '' : 'inactive' }}"></div>
                    <div class="tl-title" style="{{ !in_array($parcel->status, ['notified','received','returned']) ? 'color:#A3AED0' : '' }}">
                        Đã thông báo cư dân
                    </div>
                    <div class="tl-time">{{ in_array($parcel->status, ['notified','received','returned']) ? '✓ Hoàn thành' : 'Chưa thực hiện' }}</div>
                </div>
                <div class="tl-item">
                    <div class="tl-dot {{ in_array($parcel->status, ['received','returned']) ? '' : 'inactive' }}"></div>
                    <div class="tl-title" style="{{ !in_array($parcel->status, ['received','returned']) ? 'color:#A3AED0' : '' }}">
                        Cư dân nhận hàng
                    </div>
                    <div class="tl-time">
                        {{ $parcel->received_at ? $parcel->received_at->format('d/m/Y H:i') : 'Chưa nhận' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
