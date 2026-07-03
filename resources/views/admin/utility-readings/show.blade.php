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
    <div class="no-print" style="margin-bottom: 24px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
            <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 16px 20px; display: flex; align-items: center;">
                <div style="background: #fee2e2; color: #ef4444; border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <span style="font-size: 14px; font-weight: 700; color: #1e293b;">Lý do từ chối</span>
            </div>
            @if($rejections && $rejections->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; vertical-align: middle;">
                        <thead>
                            <tr style="background: #f8fafc; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; font-weight: 700;">
                                <th style="padding: 10px 16px; width: 25%;">Thời gian</th>
                                <th style="padding: 10px 16px; width: 25%;">Người từ chối</th>
                                <th style="padding: 10px 16px; width: 50%;">Lý do cụ thể</th>
                            </tr>
                        </thead>
                        <tbody style="color: #334155;">
                            @foreach($rejections as $rej)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 16px; color: #94a3b8; font-weight: 400; white-space: nowrap; vertical-align: top;">{{ $rej['rejected_at'] }}</td>
                                <td style="padding: 12px 16px; font-weight: 500; color: #64748b; vertical-align: top;">{{ $rej['rejecter_name'] }}</td>
                                <td style="padding: 12px 16px; vertical-align: top;">
                                    <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; font-weight: 700; font-size: 12px;">
                                        <i class="fas fa-exclamation-triangle animate-pulse" style="font-size: 11px;"></i>
                                        <span>{{ $rej['reason'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="padding: 16px 20px; font-size: 13px; color: #991b1b;">
                    <strong>Lý do từ chối:</strong> <em>{{ $reading->reject_reason }}</em>
                    <div style="font-size: 11px; margin-top: 6px; color: #b91c1c; opacity: 0.9;">
                        Người từ chối: <strong>{{ $reading->rejecter->name ?? 'Kế toán viên' }}</strong> | Ngày từ chối: <strong>{{ $reading->updated_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            @endif
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

    @php
        $proofImages = $reading->images ?? [];
        if (empty($proofImages) && $reading->image_proof) {
            $proofImages = [$reading->image_proof];
        }
    @endphp

    @if(!empty($proofImages))
    <div class="proof-section">
        <div class="detail-label">📷 Ảnh công tơ minh chứng ({{ count($proofImages) }} ảnh)</div>
        <div style="display:flex; flex-wrap:wrap; gap:12px; margin-top:14px;">
            @foreach($proofImages as $idx => $imgPath)
            <div style="position:relative; width:160px; height:160px; border-radius:12px; overflow:hidden; border:2px solid #e2e8f0; box-shadow:0 3px 12px rgba(0,0,0,.07); cursor:pointer; transition:transform .15s, box-shadow .15s;"
                onclick="openLightbox({{ $idx }})"
                onmouseenter="this.style.transform='scale(1.03)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,.14)';"
                onmouseleave="this.style.transform=''; this.style.boxShadow='0 3px 12px rgba(0,0,0,.07)';">
                <img src="{{ asset('storage/'.$imgPath) }}" alt="Ảnh minh chứng {{ $idx+1 }}"
                    style="width:100%; height:100%; object-fit:cover;">
                <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,.45));padding:8px 10px;color:#fff;font-size:11px;font-weight:600;">
                    Ảnh {{ $idx+1 }} – Nhấn để xem
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;align-items:center;justify-content:center;flex-direction:column;">
        <button onclick="closeLightbox()" style="position:absolute;top:18px;right:22px;background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:50%;width:40px;height:40px;font-size:20px;cursor:pointer;line-height:1;">✕</button>
        <button onclick="prevImg()" id="lb_prev" style="position:absolute;left:18px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:50%;width:44px;height:44px;font-size:22px;cursor:pointer;">‹</button>
        <button onclick="nextImg()" id="lb_next" style="position:absolute;right:18px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,.15);color:#fff;border:none;border-radius:50%;width:44px;height:44px;font-size:22px;cursor:pointer;">›</button>
        <img id="lb_img" src="" alt="" style="max-width:90vw;max-height:82vh;border-radius:10px;box-shadow:0 8px 40px rgba(0,0,0,.5);">
        <div style="margin-top:12px;color:rgba(255,255,255,.7);font-size:13px;" id="lb_counter"></div>
    </div>
    @else
    <div class="proof-section" style="text-align: center; color: #94a3b8; padding: 20px 0;">
        Chưa có hình ảnh chụp công tơ minh chứng.
    </div>
    @if($reading->status !== 'rejected' && $rejections && $rejections->isNotEmpty())
    <div class="proof-section no-print" style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); overflow: hidden;">
            <div style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 16px 20px; display: flex; align-items: center;">
                <div style="background: #fee2e2; color: #ef4444; border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <span style="font-size: 14px; font-weight: 700; color: #1e293b;">📋 Lý do các lần bị từ chối trước đó</span>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; vertical-align: middle;">
                    <thead>
                        <tr style="background: #f8fafc; color: #64748b; border-bottom: 1px solid #e2e8f0; text-transform: uppercase; font-size: 11px; letter-spacing: 0.05em; font-weight: 700;">
                            <th style="padding: 10px 16px; width: 25%;">Thời gian</th>
                            <th style="padding: 10px 16px; width: 25%;">Người từ chối</th>
                            <th style="padding: 10px 16px; width: 50%;">Lý do cụ thể</th>
                        </tr>
                    </thead>
                    <tbody style="color: #334155;">
                        @foreach($rejections as $rej)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 16px; color: #94a3b8; font-weight: 400; white-space: nowrap; vertical-align: top;">{{ $rej['rejected_at'] }}</td>
                            <td style="padding: 12px 16px; font-weight: 500; color: #64748b; vertical-align: top;">{{ $rej['rejecter_name'] }}</td>
                            <td style="padding: 12px 16px; vertical-align: top;">
                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; font-weight: 700; font-size: 12px;">
                                    <i class="fas fa-exclamation-triangle animate-pulse" style="font-size: 11px;"></i>
                                    <span>{{ $rej['reason'] }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</article>

@push('scripts')
<script>
const lightboxImgs = @json(array_map(fn($p) => asset('storage/'.$p), $proofImages));
let lbIdx = 0;

function openLightbox(idx) {
    lbIdx = idx;
    const lb = document.getElementById('lightbox');
    lb.style.display = 'flex';
    updateLightbox();
    document.addEventListener('keydown', handleLbKey);
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.removeEventListener('keydown', handleLbKey);
}
function updateLightbox() {
    document.getElementById('lb_img').src = lightboxImgs[lbIdx];
    document.getElementById('lb_counter').textContent = `Ảnh ${lbIdx+1} / ${lightboxImgs.length}`;
    document.getElementById('lb_prev').style.display = lightboxImgs.length > 1 ? '' : 'none';
    document.getElementById('lb_next').style.display = lightboxImgs.length > 1 ? '' : 'none';
}
function prevImg() { lbIdx = (lbIdx - 1 + lightboxImgs.length) % lightboxImgs.length; updateLightbox(); }
function nextImg() { lbIdx = (lbIdx + 1) % lightboxImgs.length; updateLightbox(); }
function handleLbKey(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevImg();
    if (e.key === 'ArrowRight') nextImg();
}
// Click backdrop to close
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});
</script>
@endpush

@push('styles')
<style>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
</style>
@endpush
