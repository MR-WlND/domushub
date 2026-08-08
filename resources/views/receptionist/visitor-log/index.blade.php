@extends('layouts.receptionist.master')
@section('page_title', 'Lịch sử khách ra vào — DomusHub')
@push('styles')
<style>
.vl-wrap { padding: 1.5rem; max-width: 1100px; margin: 0 auto; }
.vl-header { margin-bottom: 1.5rem; }
.vl-eyebrow { font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#64748b; margin:0 0 .25rem; }
.vl-title  { font-size:1.5rem; font-weight:800; color:#0f172a; margin:0; }
.vl-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
@media(max-width:640px){ .vl-stats { grid-template-columns:repeat(2,1fr); } }
.vl-stat { background:#fff; border:1px solid #e8ecf4; border-radius:12px; padding:.9rem 1rem; display:flex; align-items:center; gap:.75rem; box-shadow:0 1px 4px rgba(0,20,80,.04); }
.vl-stat__icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.vl-stat__icon--blue  { background:#dbeafe; color:#2563eb; }
.vl-stat__icon--green { background:#dcfce7; color:#16a34a; }
.vl-stat__icon--gray  { background:#f1f5f9; color:#64748b; }
.vl-stat__icon--orange{ background:#ffedd5; color:#ea580c; }
.vl-stat__num { font-size:1.35rem; font-weight:800; color:#0f172a; line-height:1; }
.vl-stat__lbl { font-size:.72rem; color:#64748b; font-weight:600; margin-top:.1rem; }
.vl-filters { background:#fff; border:1px solid #e8ecf4; border-radius:12px; padding:.85rem 1rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:flex-end; margin-bottom:1.25rem; box-shadow:0 1px 4px rgba(0,20,80,.04); }
.vl-filter-group { display:flex; flex-direction:column; gap:.25rem; }
.vl-filter-lbl { font-size:.7rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.05em; }
.vl-filter-inp,.vl-filter-sel { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:8px; padding:.5rem .75rem; font-size:.85rem; font-family:inherit; color:#1e293b; transition:border-color .15s; }
.vl-filter-inp:focus,.vl-filter-sel:focus { outline:none; border-color:#2563eb; background:#fff; }
.vl-filter-inp { min-width:200px; }
.vl-filter-sel { cursor:pointer; }
.vl-filter-actions { display:flex; gap:.5rem; margin-left:auto; align-self:flex-end; }
.vl-btn { padding:.5rem 1rem; border-radius:8px; font-size:.83rem; font-weight:700; font-family:inherit; cursor:pointer; border:1.5px solid; display:inline-flex; align-items:center; gap:.4rem; transition:all .15s; text-decoration:none; }
.vl-btn--primary { background:#1d4ed8; color:#fff; border-color:#1d4ed8; }
.vl-btn--primary:hover { background:#1e40af; border-color:#1e40af; }
.vl-btn--outline { background:#fff; color:#64748b; border-color:#e2e8f0; }
.vl-btn--outline:hover { background:#f8fafc; }
.vl-btn--green { background:#16a34a; color:#fff; border-color:#16a34a; }
.vl-btn--green:hover { background:#15803d; border-color:#15803d; }
</style>
@endpush
@push('styles')
<style>
.vl-table-wrap { background:#fff; border:1px solid #e8ecf4; border-radius:14px; overflow:hidden; box-shadow:0 1px 4px rgba(0,20,80,.04); }
.vl-table { width:100%; border-collapse:collapse; }
.vl-table thead th { background:#f4f7ff; padding:.7rem 1rem; text-align:left; font-size:.72rem; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:.06em; border-bottom:1px solid #e8ecf4; white-space:nowrap; }
.vl-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background .1s; }
.vl-table tbody tr:last-child { border-bottom:none; }
.vl-table tbody tr:hover { background:#fafbff; }
.vl-table td { padding:.75rem 1rem; font-size:.84rem; color:#1e293b; vertical-align:middle; }
.vl-guest { display:flex; align-items:center; gap:.65rem; }
.vl-avatar { width:36px; height:36px; border-radius:9px; overflow:hidden; border:1.5px solid #e2e8f0; flex-shrink:0; object-fit:cover; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.85rem; }
.vl-avatar img { width:100%; height:100%; object-fit:cover; }
.vl-guest-name { font-weight:700; color:#0f172a; }
.vl-guest-phone { font-size:.74rem; color:#64748b; }
.vl-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; padding:.25rem .65rem; border-radius:999px; white-space:nowrap; }
.vl-badge--checked_in  { background:#dcfce7; color:#166534; }
.vl-badge--checked_out { background:#f1f5f9; color:#475569; }
.vl-badge--pending     { background:#dbeafe; color:#1e40af; }
.vl-badge--expired     { background:#ffedd5; color:#9a3412; }
.vl-badge--cancelled   { background:#fee2e2; color:#991b1b; }
.vl-type { font-size:.72rem; font-weight:700; padding:.2rem .55rem; border-radius:6px; }
.vl-type--walkin { background:#eff6ff; color:#1d4ed8; }
.vl-type--qr     { background:#f0fdf4; color:#166534; }
.vl-time { font-size:.8rem; color:#64748b; }
.vl-time strong { color:#1e293b; }
.vl-action-btn { padding:.3rem .6rem; border-radius:7px; border:1px solid #e2e8f0; background:#fff; color:#64748b; font-size:.74rem; font-weight:700; cursor:pointer; font-family:inherit; transition:all .15s; white-space:nowrap; }
.vl-action-btn:hover { background:#f1f5f9; border-color:#cbd5e1; }
.vl-action-btn--out { border-color:#fca5a5; color:#dc2626; }
.vl-action-btn--out:hover { background:#fef2f2; }
.vl-empty { padding:3rem 1.5rem; text-align:center; color:#94a3b8; }
.vl-empty svg { opacity:.35; margin-bottom:.75rem; }
.vl-empty p { margin:0; font-size:.88rem; }
.vl-pagination { padding:.85rem 1rem; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.vl-page-info { font-size:.8rem; color:#64748b; }
.vl-toast { position:fixed; bottom:1.5rem; right:1.5rem; background:#1e293b; color:#fff; border-radius:12px; padding:.8rem 1.1rem; display:flex; align-items:center; gap:.6rem; font-size:.85rem; font-weight:600; z-index:9999; transform:translateY(100px); opacity:0; transition:all .3s cubic-bezier(.34,1.56,.64,1); pointer-events:none; box-shadow:0 8px 24px rgba(0,0,0,.2); max-width:360px; }
.vl-toast.show { transform:translateY(0); opacity:1; }
.vl-toast--success { background:#166534; }
.vl-toast--error   { background:#991b1b; }
</style>
@endpush

@section('content')
<div class="vl-wrap">

    {{-- HEADER --}}
    <div class="vl-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem">
        <div>
            <p class="vl-eyebrow">Quản lý khách</p>
            <h1 class="vl-title">Lịch sử ra vào</h1>
        </div>
        <a href="{{ route('receptionist.walk-in.index') }}" class="vl-btn vl-btn--green">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Đăng ký khách mới
        </a>
    </div>

    {{-- STATS --}}
    <div class="vl-stats">
        <div class="vl-stat">
            <div class="vl-stat__icon vl-stat__icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div><div class="vl-stat__num">{{ $stats['total'] }}</div><div class="vl-stat__lbl">Hôm nay</div></div>
        </div>
        <div class="vl-stat">
            <div class="vl-stat__icon vl-stat__icon--green">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div><div class="vl-stat__num">{{ $stats['checked_in'] }}</div><div class="vl-stat__lbl">Đang ở trong</div></div>
        </div>
        <div class="vl-stat">
            <div class="vl-stat__icon vl-stat__icon--gray">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
            <div><div class="vl-stat__num">{{ $stats['checked_out'] }}</div><div class="vl-stat__lbl">Đã ra</div></div>
        </div>
        <div class="vl-stat">
            <div class="vl-stat__icon vl-stat__icon--orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <div><div class="vl-stat__num">{{ $stats['walk_in'] }}</div><div class="vl-stat__lbl">Walk-in</div></div>
        </div>
    </div>

    {{-- FILTERS --}}
    <form method="GET" action="{{ route('receptionist.visitor-log.index') }}">
        <div class="vl-filters">
            <div class="vl-filter-group">
                <label class="vl-filter-lbl">Ngày</label>
                <input type="date" name="date" class="vl-filter-inp" style="min-width:160px;"
                    value="{{ request('date', today()->toDateString()) }}">
            </div>
            <div class="vl-filter-group">
                <label class="vl-filter-lbl">Trạng thái</label>
                <select name="status" class="vl-filter-sel">
                    <option value="all"         {{ request('status','all')==='all'         ?'selected':'' }}>Tất cả</option>
                    <option value="checked_in"  {{ request('status')==='checked_in'        ?'selected':'' }}>Đang ở trong</option>
                    <option value="checked_out" {{ request('status')==='checked_out'       ?'selected':'' }}>Đã ra</option>
                    <option value="pending"     {{ request('status')==='pending'           ?'selected':'' }}>Chờ vào</option>
                    <option value="expired"     {{ request('status')==='expired'           ?'selected':'' }}>Hết hạn</option>
                    <option value="cancelled"   {{ request('status')==='cancelled'         ?'selected':'' }}>Đã hủy</option>
                </select>
            </div>
            <div class="vl-filter-group" style="flex:1;">
                <label class="vl-filter-lbl">Tìm kiếm</label>
                <input type="text" name="search" class="vl-filter-inp" style="width:100%;"
                    placeholder="Tên khách, SĐT, biển số xe..." value="{{ request('search') }}">
            </div>
            <div class="vl-filter-actions">
                <button type="submit" class="vl-btn vl-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Lọc
                </button>
                <a href="{{ route('receptionist.visitor-log.index') }}" class="vl-btn vl-btn--outline">Xoá lọc</a>
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="vl-table-wrap">
        @if($logs->isEmpty())
            <div class="vl-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <p>Không có dữ liệu cho bộ lọc này.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="vl-table">
                    <thead>
                        <tr>
                            <th>Khách</th>
                            <th>Căn hộ</th>
                            <th>Gặp cư dân</th>
                            <th>Loại</th>
                            <th>Vào</th>
                            <th>Ra</th>
                            <th>Trạng thái</th>
                            <th>Ảnh</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $v)
                            @php
                                $apt   = $v->apartment;
                                $block = $apt?->floor?->block;
                            @endphp
                            <tr id="vl-row-{{ $v->id }}">
                                <td>
                                    <div class="vl-guest">
                                        <div class="vl-avatar">
                                            @if($v->face_image)
                                                <img src="{{ asset('storage/'.$v->face_image) }}" alt="{{ $v->guest_name }}">
                                            @else
                                                {{ mb_strtoupper(mb_substr($v->guest_name,0,1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="vl-guest-name">{{ $v->guest_name }}</div>
                                            <div class="vl-guest-phone">{{ $v->guest_phone ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight:700;color:#1d4ed8;">{{ $apt?->apartment_number ?? '—' }}</span>
                                    @if($block)<br><span style="font-size:.74rem;color:#64748b;">{{ $block->name }}</span>@endif
                                </td>
                                <td style="font-size:.82rem;">{{ $v->resident_to_meet ?? '—' }}</td>
                                <td>
                                    @if($v->walk_in)
                                        <span class="vl-type vl-type--walkin">Walk-in</span>
                                    @else
                                        <span class="vl-type vl-type--qr">QR</span>
                                    @endif
                                </td>
                                <td class="vl-time">
                                    @if($v->check_in_at)
                                        <strong>{{ $v->check_in_at->format('H:i') }}</strong><br>{{ $v->check_in_at->format('d/m') }}
                                    @else —
                                    @endif
                                </td>
                                <td class="vl-time">
                                    @if($v->check_out_at)
                                        <strong>{{ $v->check_out_at->format('H:i') }}</strong><br>{{ $v->check_out_at->format('d/m') }}
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                                <td><span class="vl-badge vl-badge--{{ $v->status }}">{{ $v->statusLabel() }}</span></td>
                                <td>
                                    @if($v->face_image)
                                        <img src="{{ asset('storage/'.$v->face_image) }}" alt=""
                                            style="width:36px;height:36px;border-radius:7px;object-fit:cover;border:1.5px solid #e2e8f0;cursor:pointer;"
                                            onclick="showPhoto('{{ asset('storage/'.$v->face_image) }}','{{ $v->guest_name }}')">
                                    @else
                                        <span style="font-size:.74rem;color:#94a3b8;">Không có</span>
                                    @endif
                                </td>
                                <td>
                                    @if($v->status === 'checked_in')
                                        <button class="vl-action-btn vl-action-btn--out"
                                                onclick="checkoutVisitor({{ $v->id }}, '{{ addslashes($v->guest_name) }}')">
                                            Cho Ra
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="vl-pagination">
                <span class="vl-page-info">
                    Hiển thị {{ $logs->firstItem() }}–{{ $logs->lastItem() }} / {{ $logs->total() }} bản ghi
                </span>
                {{ $logs->links() }}
            </div>
            @endif
        @endif
    </div>

</div>

{{-- Photo lightbox --}}
<div id="photo-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:800;align-items:center;justify-content:center;" onclick="this.style.display='none'">
    <div style="position:relative;" onclick="event.stopPropagation()">
        <img id="photo-full" src="" alt="" style="max-width:90vw;max-height:80vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.4);">
        <div id="photo-name" style="text-align:center;color:#fff;margin-top:.75rem;font-weight:700;font-size:.95rem;"></div>
        <button onclick="document.getElementById('photo-overlay').style.display='none'"
            style="position:absolute;top:-12px;right:-12px;width:30px;height:30px;border-radius:50%;background:#fff;border:none;cursor:pointer;font-size:1rem;font-weight:800;color:#1e293b;display:flex;align-items:center;justify-content:center;">✕</button>
    </div>
</div>

<div class="vl-toast" id="vl-toast"><span id="vl-icon"></span><span id="vl-msg"></span></div>

<script>
const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
const CHECKOUT_URL = '{{ route("receptionist.walk-in.checkout") }}';

function showPhoto(url, name) {
    document.getElementById('photo-full').src  = url;
    document.getElementById('photo-name').textContent = name;
    document.getElementById('photo-overlay').style.display = 'flex';
}

async function checkoutVisitor(id, name) {
    if (!confirm(`Xác nhận cho khách "${name}" ra?`)) return;
    try {
        const r    = await fetch(CHECKOUT_URL, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ visitor_id: id }),
        });
        const data = await r.json();
        if (data.success) {
            showToast(data.message, 'success');
            const row = document.getElementById('vl-row-' + id);
            if (row) {
                row.querySelector('.vl-badge').className = 'vl-badge vl-badge--checked_out';
                row.querySelector('.vl-badge').textContent = 'Đã ra';
                const btn = row.querySelector('.vl-action-btn--out');
                if (btn) btn.remove();
                const now = new Date();
                const pad = n => String(n).padStart(2,'0');
                row.querySelectorAll('td')[5].innerHTML =
                    `<strong>${pad(now.getHours())}:${pad(now.getMinutes())}</strong><br>${pad(now.getDate())}/${pad(now.getMonth()+1)}`;
            }
        } else {
            showToast(data.message || 'Lỗi ghi nhận.', 'error');
        }
    } catch { showToast('Lỗi kết nối.', 'error'); }
}

function showToast(msg, type) {
    const t = document.getElementById('vl-toast');
    t.className = `vl-toast vl-toast--${type}`;
    document.getElementById('vl-icon').textContent = type === 'success' ? '✓ ' : '✕ ';
    document.getElementById('vl-msg').textContent  = msg;
    t.classList.add('show');
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove('show'), 4000);
}
</script>
@endsection
