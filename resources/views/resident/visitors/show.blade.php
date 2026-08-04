@extends('layouts.resident.master')
@section('title', 'Chi tiết khách: {{ $visitor->guest_name }}')
@push('styles')
    @vite(['resources/css/resident/visitors.css'])
    <style>
    .vd-wrap { max-width: 680px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }
    .vd-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .vd-eyebrow { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#7c3aed; margin:0 0 .2rem; }
    .vd-title { font-size:1.5rem; font-weight:800; color:#0f172a; margin:0; }
    .vd-back { display:inline-flex; align-items:center; gap:.4rem; font-size:.83rem; font-weight:700; color:#64748b; text-decoration:none; padding:.45rem .85rem; border:1.5px solid #e2e8f0; border-radius:9px; background:#fff; transition:all .15s; }
    .vd-back:hover { background:#f8fafc; color:#1e293b; }

    /* CARD */
    .vd-card { background:#fff; border:1px solid #e8ecf4; border-radius:16px; overflow:hidden; box-shadow:0 2px 10px rgba(0,20,80,.06); }

    /* STATUS STRIPE */
    .vd-stripe { height:5px; }
    .vd-stripe--checked_in  { background:linear-gradient(90deg,#059669,#10b981); }
    .vd-stripe--checked_out { background:linear-gradient(90deg,#94a3b8,#cbd5e1); }
    .vd-stripe--pending     { background:linear-gradient(90deg,#3b82f6,#6366f1); }
    .vd-stripe--cancelled   { background:linear-gradient(90deg,#ef4444,#f87171); }
    .vd-stripe--expired     { background:linear-gradient(90deg,#f59e0b,#fbbf24); }

    /* PHOTO + INFO */
    .vd-top { display:grid; grid-template-columns:1fr auto; gap:1.5rem; padding:1.5rem; align-items:start; }
    @media(max-width:480px){ .vd-top { grid-template-columns:1fr; } }

    .vd-info-rows { display:flex; flex-direction:column; gap:.55rem; }
    .vd-name { font-size:1.25rem; font-weight:800; color:#0f172a; margin:0 0 .75rem; display:flex; align-items:center; gap:.6rem; flex-wrap:wrap; }
    .vd-row  { display:flex; gap:.5rem; font-size:.85rem; align-items:flex-start; }
    .vd-lbl  { color:#64748b; font-weight:600; min-width:115px; flex-shrink:0; }
    .vd-val  { color:#1e293b; font-weight:700; flex:1; }
    .vd-val--apt { color:#7c3aed; }

    /* PHOTO BOX */
    .vd-photo-box { width:130px; flex-shrink:0; }
    @media(max-width:480px){ .vd-photo-box { width:100%; max-width:200px; } }
    .vd-photo-box img { width:100%; border-radius:12px; object-fit:cover; border:2px solid #e2e8f0; display:block; cursor:zoom-in; }
    .vd-photo-empty { width:100%; aspect-ratio:3/4; background:#f1f5f9; border:2px dashed #e2e8f0; border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.4rem; color:#94a3b8; font-size:.72rem; text-align:center; padding:.5rem; }
    .vd-photo-empty svg { opacity:.4; }

    /* CHIPS */
    .vd-chip { display:inline-flex; align-items:center; gap:.25rem; font-size:.72rem; font-weight:700; padding:.25rem .65rem; border-radius:999px; }
    .vd-chip--checked_in  { background:#dcfce7; color:#166534; }
    .vd-chip--checked_out { background:#f1f5f9; color:#475569; }
    .vd-chip--pending     { background:#dbeafe; color:#1e40af; }
    .vd-chip--cancelled   { background:#fee2e2; color:#991b1b; }
    .vd-chip--expired     { background:#ffedd5; color:#9a3412; }

    /* DIVIDER */
    .vd-divider { border:none; border-top:1px solid #f1f5f9; margin:0; }

    /* NOTE */
    .vd-note { margin:0 1.5rem 1.25rem; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:.8rem 1rem; font-size:.84rem; color:#1e293b; }
    .vd-note strong { display:block; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; color:#64748b; margin-bottom:.35rem; }

    /* VEHICLE */
    .vd-vehicle { margin:0 1.5rem 1.25rem; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:.75rem 1rem; display:flex; align-items:center; gap:.65rem; font-size:.85rem; color:#1e40af; font-weight:700; }

    /* CONFIRMED BOX */
    .vd-confirmed { margin:0 1.5rem 1.25rem; background:#dcfce7; border:1px solid #86efac; border-radius:10px; padding:.75rem 1rem; display:flex; align-items:center; gap:.65rem; font-size:.84rem; color:#166534; font-weight:700; }
    .vd-rejected { margin:0 1.5rem 1.25rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:10px; padding:.75rem 1rem; display:flex; align-items:center; gap:.65rem; font-size:.84rem; color:#991b1b; font-weight:700; }

    /* ACTIONS */
    .vd-actions { padding:1.25rem 1.5rem; border-top:1px solid #f1f5f9; display:flex; gap:.75rem; flex-wrap:wrap; }
    .vd-btn { display:inline-flex; align-items:center; gap:.45rem; padding:.7rem 1.25rem; border-radius:10px; font-size:.88rem; font-weight:800; font-family:inherit; cursor:pointer; border:2px solid; transition:all .2s; text-decoration:none; }
    .vd-btn--approve { background:linear-gradient(135deg,#059669,#10b981); color:#fff; border-color:transparent; box-shadow:0 3px 10px rgba(5,150,105,.25); }
    .vd-btn--approve:hover { background:linear-gradient(135deg,#047857,#059669); transform:translateY(-1px); }
    .vd-btn--reject  { background:#fff; color:#dc2626; border-color:#fca5a5; }
    .vd-btn--reject:hover  { background:#fef2f2; border-color:#ef4444; }
    .vd-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; }
    .vd-btn--outline { background:#fff; color:#64748b; border-color:#e2e8f0; }
    .vd-btn--outline:hover { background:#f8fafc; }

    /* SPIN */
    .vd-spin { width:16px; height:16px; border:2.5px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:vdspin .7s linear infinite; }
    @keyframes vdspin { to { transform:rotate(360deg); } }

    /* TOAST */
    .vd-toast { position:fixed; bottom:1.5rem; right:1.5rem; background:#1e293b; color:#fff; border-radius:12px; padding:.8rem 1.1rem; display:flex; align-items:center; gap:.6rem; font-size:.85rem; font-weight:600; z-index:9999; transform:translateY(100px); opacity:0; transition:all .3s cubic-bezier(.34,1.56,.64,1); pointer-events:none; box-shadow:0 8px 24px rgba(0,0,0,.2); max-width:360px; }
    .vd-toast.show { transform:translateY(0); opacity:1; }
    .vd-toast--success { background:#166534; }
    .vd-toast--error   { background:#991b1b; }
    </style>
@endpush

@section('content')
<div class="vd-wrap">

    {{-- HEADER --}}
    <div class="vd-header">
        <div>
            <p class="vd-eyebrow">Khách ghé thăm</p>
            <h1 class="vd-title">Chi tiết khách</h1>
        </div>
        <a href="{{ route('resident.visitors.index') }}" class="vd-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- MAIN CARD --}}
    <div class="vd-card">

        {{-- Status stripe --}}
        <div class="vd-stripe vd-stripe--{{ $visitor->status }}"></div>

        {{-- Top: info + photo --}}
        <div class="vd-top">

            {{-- Info --}}
            <div class="vd-info-rows">
                <div class="vd-name">
                    {{ $visitor->guest_name }}
                    <span class="vd-chip vd-chip--{{ $visitor->status }}">{{ $visitor->statusLabel() }}</span>
                </div>
                @if($visitor->guest_phone)
                <div class="vd-row">
                    <span class="vd-lbl">Điện thoại</span>
                    <span class="vd-val">{{ $visitor->guest_phone }}</span>
                </div>
                @endif
                <div class="vd-row">
                    <span class="vd-lbl">Căn hộ</span>
                    <span class="vd-val vd-val--apt">
                        {{ $visitor->apartment?->apartment_number ?? '—' }}
                        @if($visitor->apartment?->floor?->block)
                            · {{ $visitor->apartment->floor->block->name }}
                        @endif
                    </span>
                </div>
                <div class="vd-row">
                    <span class="vd-lbl">Gặp cư dân</span>
                    <span class="vd-val">{{ $visitor->resident_to_meet ?? '—' }}</span>
                </div>
                <div class="vd-row">
                    <span class="vd-lbl">Bảo vệ đăng ký</span>
                    <span class="vd-val">{{ $visitor->registeredBy?->name ?? '—' }}</span>
                </div>
                <div class="vd-row">
                    <span class="vd-lbl">Đăng ký lúc</span>
                    <span class="vd-val">{{ $visitor->created_at->format('H:i — d/m/Y') }}</span>
                </div>
                @if($visitor->check_in_at)
                <div class="vd-row">
                    <span class="vd-lbl">Vào lúc</span>
                    <span class="vd-val" style="color:#059669;">{{ $visitor->check_in_at->format('H:i — d/m/Y') }}</span>
                </div>
                @endif
                @if($visitor->check_out_at)
                <div class="vd-row">
                    <span class="vd-lbl">Ra lúc</span>
                    <span class="vd-val" style="color:#64748b;">{{ $visitor->check_out_at->format('H:i — d/m/Y') }}</span>
                </div>
                @endif
            </div>

            {{-- Photo --}}
            <div class="vd-photo-box">
                @if($visitor->face_image)
                    <img src="{{ asset('storage/'.$visitor->face_image) }}"
                         alt="{{ $visitor->guest_name }}"
                         onclick="zoomPhoto('{{ asset('storage/'.$visitor->face_image) }}','{{ $visitor->guest_name }}')"
                         title="Bấm để phóng to">
                    <p style="font-size:.68rem;color:#94a3b8;text-align:center;margin:.4rem 0 0;">Ảnh định danh · Bấm để phóng to</p>
                @else
                    <div class="vd-photo-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Không có ảnh</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Vehicle --}}
        @if($visitor->hasVehicle())
        <div class="vd-vehicle">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <span>{{ $visitor->vehicle_plate }} — {{ $visitor->vehicleTypeLabel() }}</span>
        </div>
        @endif

        {{-- Note --}}
        @if($visitor->note)
        <div class="vd-note">
            <strong>Lý do thăm</strong>
            {{ $visitor->note }}
        </div>
        @endif

        {{-- Already confirmed/rejected banner --}}
        @if($visitor->confirmed_by_resident && $visitor->status !== 'cancelled')
        <div class="vd-confirmed" id="status-banner">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Bạn đã xác nhận chấp nhận khách này.
        </div>
        @elseif($visitor->status === 'cancelled')
        <div class="vd-rejected" id="status-banner">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Khách đã bị từ chối / hủy.
        </div>
        @endif

        {{-- ACTIONS --}}
        @if(in_array($visitor->status, ['checked_in', 'pending']) && !$visitor->confirmed_by_resident)
        <div class="vd-actions" id="action-bar">
            <button class="vd-btn vd-btn--approve" id="btn-approve" onclick="doApprove()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Chấp nhận khách
            </button>
            <button class="vd-btn vd-btn--reject" id="btn-reject" onclick="doReject()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Từ chối
            </button>
        </div>
        @else
        <div class="vd-actions">
            <a href="{{ route('resident.visitors.index') }}" class="vd-btn vd-btn--outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại danh sách
            </a>
        </div>
        @endif

    </div>{{-- /vd-card --}}
</div>{{-- /vd-wrap --}}

{{-- Photo lightbox --}}
<div id="zoom-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:900;align-items:center;justify-content:center;" onclick="this.style.display='none'">
    <div onclick="event.stopPropagation()" style="position:relative;max-width:92vw;">
        <img id="zoom-img" src="" alt="" style="max-width:100%;max-height:85vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.5);display:block;">
        <div id="zoom-name" style="text-align:center;color:#fff;margin-top:.65rem;font-weight:700;font-size:.95rem;"></div>
        <button onclick="document.getElementById('zoom-overlay').style.display='none'"
            style="position:absolute;top:-10px;right:-10px;width:28px;height:28px;border-radius:50%;background:#fff;border:none;cursor:pointer;font-size:.9rem;font-weight:800;color:#1e293b;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.2);">✕</button>
    </div>
</div>

<div class="vd-toast" id="vd-toast"><span id="vd-icon"></span><span id="vd-msg"></span></div>

@endsection

@push('scripts')
<script>
const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
const URL_APPROVE  = '{{ route("resident.visitors.approve", $visitor->id) }}';
const URL_REJECT   = '{{ route("resident.visitors.reject",  $visitor->id) }}';

function zoomPhoto(url, name) {
    document.getElementById('zoom-img').src   = url;
    document.getElementById('zoom-name').textContent = name;
    document.getElementById('zoom-overlay').style.display = 'flex';
}

async function doApprove() {
    if (!confirm('Xác nhận chấp nhận khách "{{ $visitor->guest_name }}" vào căn hộ?')) return;
    const btn = document.getElementById('btn-approve');
    btn.disabled = true;
    btn.innerHTML = '<div class="vd-spin"></div> Đang xử lý...';

    try {
        const r    = await fetch(URL_APPROVE, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        });
        const data = await r.json();
        if (data.success) {
            showToast(data.message, 'success');
            replaceActionBar('<div class="vd-confirmed" id="status-banner"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Bạn đã xác nhận chấp nhận khách này.</div>');
        } else {
            showToast(data.message || 'Lỗi xử lý.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Chấp nhận khách';
        }
    } catch { showToast('Lỗi kết nối.', 'error'); btn.disabled = false; }
}

async function doReject() {
    if (!confirm('Từ chối khách "{{ $visitor->guest_name }}"? Bảo vệ sẽ yêu cầu khách rời đi.')) return;
    const btn = document.getElementById('btn-reject');
    btn.disabled = true;
    btn.innerHTML = '<div class="vd-spin" style="border-top-color:#dc2626;border-color:rgba(220,38,38,.2)"></div> Đang xử lý...';

    try {
        const r    = await fetch(URL_REJECT, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        });
        const data = await r.json();
        if (data.success) {
            showToast(data.message, 'success');
            replaceActionBar('<div class="vd-rejected" id="status-banner"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Khách đã bị từ chối. Bảo vệ đã được thông báo.</div>');
        } else {
            showToast(data.message || 'Lỗi xử lý.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Từ chối';
        }
    } catch { showToast('Lỗi kết nối.', 'error'); btn.disabled = false; }
}

function replaceActionBar(bannerHtml) {
    const bar = document.getElementById('action-bar');
    if (bar) {
        bar.insertAdjacentHTML('beforebegin', bannerHtml);
        bar.remove();
    }
}

function showToast(msg, type) {
    const t = document.getElementById('vd-toast');
    t.className = `vd-toast vd-toast--${type}`;
    document.getElementById('vd-icon').textContent = type === 'success' ? '✓ ' : '✕ ';
    document.getElementById('vd-msg').textContent  = msg;
    t.classList.add('show');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove('show'), 4000);
}
</script>
@endpush
