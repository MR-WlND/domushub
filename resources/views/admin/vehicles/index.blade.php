@extends('layouts.admin.master')

@section('page_title', 'Quản lý phương tiện')

@section('content')
    <div class="dashboard-grid">
        <article class="dashboard-card dashboard-card--primary">
            <p class="dashboard-card__label">Tổng quan quản lý</p>
            <h2>Yêu cầu phương tiện</h2>
            <p>Danh sách phương tiện đang chờ duyệt, yêu cầu thanh toán và cấp QR cho cư dân.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Tổng phương tiện</p>
            <strong>{{ $summary['total'] }}</strong>
            <p class="dashboard-card__muted">Tổng số yêu cầu được gửi đến hệ thống.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Chờ duyệt</p>
            <strong>{{ $summary['pending'] }}</strong>
            <p class="dashboard-card__muted">Phương tiện đang chờ admin xử lý.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Đã duyệt</p>
            <strong>{{ $summary['approved'] }}</strong>
            <p class="dashboard-card__muted">Phương tiện đã được admin chấp nhận.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Đã thanh toán</p>
            <strong>{{ $summary['paid'] }}</strong>
            <p class="dashboard-card__muted">Số xe đã hoàn tất thanh toán phí vé.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Chờ cấp QR</p>
            <strong>{{ $summary['awaiting_qr'] }}</strong>
            <p class="dashboard-card__muted">Xe đã thanh toán và đang chờ cấp mã QR.</p>
        </article>
    </div>

    <div class="dashboard-card" style="margin-top: 24px;">
        @if(session('success'))
            <div class="dashboard-alert dashboard-alert--success" style="margin-bottom: 1rem; padding: 14px 18px; border-radius: 14px; background: #ecfdf5; border: 1px solid #bbf7d0; color: #166534;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="dashboard-alert dashboard-alert--error" style="margin-bottom: 1rem; padding: 14px 18px; border-radius: 14px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;">
                <ul style="margin:0; padding-left: 18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="admin-table-wrapper">
            <table class="admin-vehicles-table">
                <thead>
                    <tr>
                        <th>Biển số</th>
                        <th>Loại / Hãng</th>
                        <th>Cư dân / Căn hộ</th>
                        <th>Trạng thái</th>
                        <th>Thanh toán</th>
                        <th>QR</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $vehicle)
                        @php
                            $payment = $vehicle->latestPaymentRequest;
                            $resident = $vehicle->apartment->residents->first();
                            $displayResident = $resident?->user?->name ?? 'Chưa xác định';
                            $paymentLabel = $payment
                                ? ($payment->status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán')
                                : 'Chưa tạo yêu cầu';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $vehicle->license_plate }}</strong>
                                <div class="muted-text">{{ ucfirst($vehicle->vehicle_type) }}</div>
                            </td>
                            <td>
                                {{ $vehicle->brand ?? '-' }}
                                <div class="muted-text">{{ ucfirst($vehicle->vehicle_type) }}</div>
                            </td>
                            <td>
                                {{ $displayResident }}<br>
                                <span class="muted-text">{{ $vehicle->apartment->apartment_number }}</span>
                            </td>
                            <td>
                                <span class="vehicle-status vehicle-status--{{ $vehicle->status }}">
                                    {{ $vehicle->status === 'pending' ? 'Đang xem xét' : ($vehicle->status === 'approved' ? 'Đã duyệt' : 'Không duyệt') }}
                                </span>
                                @if($vehicle->status === 'approved' && $payment)
                                    <div class="muted-text">Ngày tạo: {{ $payment->created_at->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $paymentLabel }}</div>
                                @if($payment)
                                    <div class="muted-text">{{ number_format($payment->amount, 0, ',', '.') }}đ</div>
                                @endif
                            </td>
                            <td>
                                @if($vehicle->qr_code)
                                    <div class="qr-preview">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($vehicle->qr_code) }}" alt="QR">
                                    </div>
                                    <div class="muted-text">Đã cấp</div>
                                @elseif($payment && $payment->status === 'paid')
                                    <span class="vehicle-status vehicle-status--warning">Chờ QR</span>
                                @else
                                    <span class="muted-text">Chưa có</span>
                                @endif
                            </td>
                            <td class="admin-actions-cell">
                                @if($vehicle->status === 'pending')
                                    <form method="POST" action="{{ route('admin.vehicles.approve', $vehicle) }}" style="display:inline-flex; gap: 8px; margin-bottom: 6px;">
                                        @csrf
                                        <button type="submit" class="admin-action admin-action--approve">Duyệt</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.vehicles.reject', $vehicle) }}" style="display:inline-flex; gap: 8px; margin-bottom: 6px;">
                                        @csrf
                                        <button type="submit" class="admin-action admin-action--reject">Từ chối</button>
                                    </form>
                                @endif

                                @if($vehicle->status === 'approved' && $payment && $payment->status === 'paid' && ! $vehicle->qr_code)
                                    <form method="POST" action="{{ route('admin.vehicles.issueQr', $vehicle) }}">
                                        @csrf
                                        <button type="submit" class="admin-action admin-action--primary">Cấp QR</button>
                                    </form>
                                @endif

                                @if($vehicle->status === 'rejected')
                                    <span class="vehicle-status vehicle-status--rejected">Đã từ chối</span>
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
.admin-table-wrapper { overflow-x: auto; }
.admin-vehicles-table { width: 100%; border-collapse: collapse; min-width: 900px; }
.admin-vehicles-table th, .admin-vehicles-table td { padding: 16px 14px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
.admin-vehicles-table th { color: #475569; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
.admin-vehicles-table tbody tr:hover { background: #f8fbff; }
.muted-text { color: #64748b; font-size: 0.85rem; margin-top: 4px; display: block; }
.vehicle-status { display: inline-flex; align-items: center; padding: 6px 10px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; }
.vehicle-status--pending { background: #fef3c7; color: #92400e; }
.vehicle-status--approved { background: #dcfce7; color: #166534; }
.vehicle-status--rejected { background: #fee2e2; color: #991b1b; }
.vehicle-status--warning { background: #fef3c7; color: #b45309; }
.admin-action { border: none; border-radius: 10px; padding: 10px 16px; font-weight: 700; cursor: pointer; }
.admin-action--approve { background: #0b63d8; color: #ffffff; }
.admin-action--reject { background: #ef4444; color: #ffffff; }
.admin-action--primary { background: #0f766e; color: #ffffff; }
.admin-actions-cell { display: flex; flex-direction: column; gap: 8px; }
.qr-preview img { width: 90px; height: 90px; border-radius: 14px; border: 1px solid #e2e8f0; }
@media(max-width: 1024px) {
    .admin-vehicles-table { min-width: 720px; }
}
@media(max-width: 768px) {
    .admin-vehicles-table { min-width: 100%; }
}
</style>
@endpush
