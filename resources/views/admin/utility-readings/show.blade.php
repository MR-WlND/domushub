@extends('layouts.admin.master')

@section('page_title', 'Chi tiết chỉ số điện nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
<style>
/* ── Detail layout styling ───────────────────────── */
.detail-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px rgba(0, 35, 111, 0.04);
    padding: 30px;
    max-width: 800px;
    margin: 0 auto;
}
.detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 30px;
}
.detail-item {
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 12px;
}
.detail-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
}
.detail-value {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
}
.detail-value--highlight {
    color: #00236f;
    font-size: 20px;
}
.detail-badge-wrap {
    display: inline-block;
    margin-top: 4px;
}
.proof-section {
    margin-top: 30px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}
.proof-img-container {
    margin-top: 12px;
    max-width: 100%;
    text-align: center;
}
.proof-img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 12px;
    border: 1px solid #cbd5e1;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* ── Print Media ─────────────────────────────────── */
@media print {
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }
    .dashboard-sidebar, 
    .dashboard-topbar,
    .util-page-header,
    .no-print {
        display: none !important;
    }
    .dashboard-main,
    .dashboard-shell,
    .dashboard-content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    .detail-card {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        max-width: 100% !important;
    }
    .detail-value--highlight {
        color: #000000 !important;
    }
    .proof-img {
        max-height: 300px !important;
        box-shadow: none !important;
        border: 1px solid #000000 !important;
    }
}
</style>
@endpush

@section('content')
<div class="util-page-header no-print">
    <div>
        <h1>Chi tiết chỉ số điện nước</h1>
        <p>
            Căn hộ <strong>{{ $reading->apartment->apartment_number }}</strong> – Kỳ {{ $reading->record_month }}/{{ $reading->record_year }}
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="util-btn util-btn--outline" onclick="window.print()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.821V21h10.56v-7.179m-10.56 0H3.75A1.5 1.5 0 0 1 2.25 12.18V7.5a1.5 1.5 0 0 1 1.5-1.5h16.5a1.5 1.5 0 0 1 1.5 1.5v4.68a1.5 1.5 0 0 1-1.5 1.5H17.28m-10.56 0h10.56M9 3.75h6" />
            </svg>
            In chi tiết
        </button>
        @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'technician' && in_array($reading->status, ['pending', 'rejected']) && $reading->recorded_by === auth()->id()))
        <a href="{{ route('admin.utility-readings.edit', $reading->id) }}" class="util-btn util-btn--primary">
            Chỉnh sửa
        </a>
        @endif
        <a href="{{ route('admin.utility-readings.index', ['month' => $reading->record_month, 'year' => $reading->record_year]) }}" class="util-btn util-btn--outline">
            Quay lại
        </a>
    </div>
</div>

<article class="detail-card">
    @if($reading->status === 'rejected')
    <div class="no-print" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; color: #b91c1c;">
        <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">❌ Chỉ số này đã bị từ chối</div>
        <div style="font-size: 13px; color: #991b1b;">
            <strong>Lý do từ chối:</strong> <em>{{ $reading->reject_reason }}</em>
        </div>
        <div style="font-size: 11px; margin-top: 6px; color: #b91c1c; opacity: 0.9;">
            Người từ chối: <strong>{{ $reading->rejecter->name ?? 'Kế toán viên' }}</strong>
        </div>
    </div>
    @endif

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Căn hộ</div>
            <div class="detail-value">{{ $reading->apartment->apartment_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Tòa nhà / Tầng</div>
            <div class="detail-value">
                {{ $reading->apartment->floor->block->name ?? '—' }} / {{ $reading->apartment->floor->name ?? 'Tầng ' . $reading->apartment->floor->floor_number }}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Loại dịch vụ</div>
            <div class="detail-value">{{ $reading->type_label }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Kỳ áp dụng</div>
            <div class="detail-value">Tháng {{ $reading->record_month }}/{{ $reading->record_year }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Chỉ số mới</div>
            <div class="detail-value">{{ number_format($reading->new_value) }}</div>
        </div>
        <div class="detail-item" style="grid-column: span 2;">
            <div class="detail-label">Lượng tiêu thụ thực tế</div>
            <div class="detail-value detail-value--highlight">
                {{ number_format($reading->usage_amount) }} 
                <small style="font-size: 14px; color: #64748b; font-weight: 600;">
                    {{ $reading->type === 'electricity' ? 'kWh' : 'm³' }}
                </small>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Trạng thái phê duyệt</div>
            <div class="detail-badge-wrap">
                @if($reading->status === 'approved')
                    <span class="util-badge util-badge--success" style="background:#e6f4ea; color:#137333; font-size:12px;">Đã chốt</span>
                @elseif($reading->status === 'rejected')
                    <span class="util-badge util-badge--danger" style="background:#fce8e6; color:#c5221f; font-size:12px;">Bị từ chối</span>
                @else
                    <span class="util-badge util-badge--warning" style="background:#fef7e0; color:#b06000; font-size:12px;">Chờ chốt</span>
                @endif
            </div>
        </div>
        <div class="detail-item" style="grid-column: span 2;">
            <div class="detail-label">Người ghi nhận</div>
            <div class="detail-value">{{ $reading->recorder->name ?? 'Hệ thống' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Ngày ghi nhận</div>
            <div class="detail-value">{{ $reading->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Cập nhật cuối</div>
            <div class="detail-value">{{ $reading->updated_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    @if($reading->image_proof)
    <div class="proof-section">
        <div class="detail-label">Ảnh công tơ minh chứng</div>
        <div class="proof-img-container">
            <img src="{{ asset('storage/' . $reading->image_proof) }}" alt="Ảnh minh chứng công tơ" class="proof-img">
        </div>
    </div>
    @else
    <div class="proof-section" style="text-align: center; color: #94a3b8; padding: 20px 0;">
        Chưa có hình ảnh chụp công tơ minh chứng.
    </div>
    @endif
</article>
