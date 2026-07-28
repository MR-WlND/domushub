@extends('layouts.security.master')
@section('page_title', 'Đăng ký khách tại cổng — DomusHub')
@push('styles')
<style>
/* WRAP */
.wi-wrap{max-width:720px;margin:0 auto;padding:1.5rem 1rem 3rem}
/* STEPPER */
.wi-stepper{display:flex;align-items:center;margin-bottom:2rem}
.wi-step-item{display:flex;flex-direction:column;align-items:center;gap:.3rem;flex:1;position:relative}
.wi-step-item:not(:last-child)::after{content:'';position:absolute;top:18px;left:calc(50% + 20px);right:calc(-50% + 20px);height:2px;background:#e2e8f0;z-index:0}
.wi-step-item.s-active:not(:last-child)::after,.wi-step-item.s-done:not(:last-child)::after{background:#2563eb}
.wi-step-circle{width:36px;height:36px;border-radius:50%;border:2.5px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:800;color:#94a3b8;position:relative;z-index:1;transition:all .25s}
.wi-step-item.s-active .wi-step-circle{border-color:#2563eb;background:#2563eb;color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.15)}
.wi-step-item.s-done .wi-step-circle{border-color:#2563eb;background:#2563eb;color:#fff}
.wi-step-lbl{font-size:.72rem;font-weight:700;color:#94a3b8;white-space:nowrap}
.wi-step-item.s-active .wi-step-lbl,.wi-step-item.s-done .wi-step-lbl{color:#2563eb}
/* SECTION CARD */
.wi-sec{background:#fff;border:1px solid #e8ecf4;border-radius:14px;margin-bottom:1.25rem;overflow:hidden;box-shadow:0 2px 10px rgba(0,20,80,.04)}
.wi-sec-hd{padding:.8rem 1.25rem;background:#f4f7ff;border-bottom:1px solid #e8ecf4;display:flex;align-items:center;gap:.6rem}
.wi-sec-icon{width:30px;height:30px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0}
.wi-sec-title{font-size:.88rem;font-weight:800;color:#1e293b;margin:0}
.wi-sec-body{padding:1.25rem}
</style>
@endpush
@push('styles')
<style>
/* FORM */
.wi-grid2{display:grid;grid-template-columns:1fr 1fr;gap:.85rem}
@media(max-width:540px){.wi-grid2{grid-template-columns:1fr}}
.wi-span2{grid-column:1/-1}
.wi-fld{display:flex;flex-direction:column;gap:.3rem}
.wi-lbl{font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em}
.wi-lbl em{color:#ef4444;font-style:normal}
.wi-inp,.wi-sel,.wi-ta{background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:9px;padding:.62rem .9rem;font-size:.875rem;font-family:inherit;color:#1e293b;width:100%;box-sizing:border-box;transition:border-color .15s}
.wi-inp:focus,.wi-sel:focus,.wi-ta:focus{outline:none;border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.wi-inp::placeholder,.wi-ta::placeholder{color:#b0bac9}
.wi-ta{min-height:80px;resize:vertical}
.wi-sel{cursor:pointer}
/* APARTMENT DROPDOWN */
.wi-apt-wrap{position:relative}
.wi-apt-dd{position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border:1.5px solid #e2e8f0;border-radius:10px;box-shadow:0 10px 28px rgba(0,0,0,.1);max-height:200px;overflow-y:auto;z-index:300;display:none}
.wi-apt-dd.open{display:block}
.wi-apt-opt{padding:.55rem .9rem;font-size:.86rem;cursor:pointer;color:#1e293b;transition:background .1s}
.wi-apt-opt:hover{background:#eff6ff;color:#1d4ed8}
.wi-apt-opt--e{color:#94a3b8;cursor:default}
/* RESIDENT CHIPS */
.wi-res{background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:11px;padding:.75rem 1rem;display:flex;align-items:center;gap:.75rem;cursor:pointer;margin-bottom:.45rem;transition:all .15s;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.wi-res:hover{background:#dcfce7;border-color:#86efac;box-shadow:0 2px 8px rgba(34,197,94,.15)}
.wi-res.sel{background:#dcfce7;border-color:#22c55e;box-shadow:0 2px 8px rgba(34,197,94,.2)}
.wi-res-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#22c55e,#16a34a);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.9rem;flex-shrink:0}
.wi-res-info{flex:1;min-width:0}
.wi-res-name{font-size:.9rem;font-weight:800;color:#15803d;margin-bottom:.1rem}
.wi-res-phone{font-size:.8rem;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:.3rem}
.wi-res-chk{display:none;color:#16a34a;flex-shrink:0}
.wi-res.sel .wi-res-chk{display:flex}
.wi-res-empty{background:#fff8f0;border:1.5px dashed #fed7aa;border-radius:11px;padding:.85rem 1rem;display:flex;align-items:center;gap:.65rem;color:#92400e;font-size:.84rem;font-weight:600}
</style>
@endpush
@push('styles')
<style>
/* CAMERA */
.wi-cam-layout{display:grid;grid-template-columns:1fr 1fr;gap:1rem;align-items:start}
@media(max-width:540px){.wi-cam-layout{grid-template-columns:1fr}}
.wi-cam-vp{width:100%;aspect-ratio:4/3;background:#1e293b;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center}
.wi-cam-vp video{width:100%;height:100%;object-fit:cover;display:block}
.wi-cam-off{display:flex;flex-direction:column;align-items:center;gap:.5rem;color:#64748b;text-align:center;padding:1rem}
.wi-cam-off p{margin:0;font-size:.78rem}
.wi-snap-btn{display:flex;width:100%;margin-top:.75rem;padding:.65rem 1rem;background:#1d4ed8;color:#fff;border:none;border-radius:9px;font-size:.88rem;font-weight:800;font-family:inherit;cursor:pointer;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 3px 10px rgba(29,78,216,.25);transition:all .2s}
.wi-snap-btn:hover{background:#1e40af}
.wi-snap-btn:disabled{opacity:.45;cursor:not-allowed}
.wi-prev-lbl{font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.05em;margin:0 0 .5rem}
.wi-prev-box{width:100%;aspect-ratio:4/3;background:#f8fafc;border:2px dashed #e2e8f0;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center}
.wi-prev-box img{width:100%;height:100%;object-fit:cover;display:block}
.wi-prev-empty{display:flex;flex-direction:column;align-items:center;gap:.4rem;color:#b0bac9;font-size:.74rem;text-align:center;padding:.75rem}
.wi-prev-empty svg{opacity:.45}
.wi-prev-empty p{margin:0}
.wi-cam-acts{display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-top:.6rem}
.wi-btn-sm{padding:.5rem .7rem;border-radius:8px;font-size:.8rem;font-weight:700;font-family:inherit;cursor:pointer;border:1.5px solid;display:flex;align-items:center;justify-content:center;gap:.35rem;transition:all .15s}
.wi-btn-outline{background:#fff;color:#64748b;border-color:#e2e8f0}
.wi-btn-outline:hover{background:#f1f5f9}
.wi-btn-del{background:#fff;color:#dc2626;border-color:#fca5a5}
.wi-btn-del:hover{background:#fef2f2}
.wi-btn-del:disabled,.wi-btn-sm:disabled{opacity:.4;cursor:not-allowed}
</style>
@endpush
@push('styles')
<style>
/* REVIEW */
.wi-review{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:.9rem 1rem;display:grid;grid-template-columns:1fr auto;gap:.5rem 1rem;align-items:start}
.wi-rv-rows{display:flex;flex-direction:column;gap:.4rem}
.wi-rv-row{display:flex;gap:.5rem;font-size:.84rem}
.wi-rv-lbl{color:#64748b;font-weight:600;min-width:105px;flex-shrink:0}
.wi-rv-val{color:#1e293b;font-weight:700;flex:1;word-break:break-word}
.wi-rv-val.accent{color:#1d4ed8}
.wi-rv-thumb{width:72px;height:72px;border-radius:9px;overflow:hidden;border:2px solid #e2e8f0;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.wi-rv-thumb img{width:100%;height:100%;object-fit:cover}
.wi-rv-thumb-empty{color:#94a3b8;font-size:.65rem;text-align:center;padding:.3rem;display:flex;flex-direction:column;align-items:center;gap:.2rem}
.wi-notice{background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:.7rem 1rem;display:flex;align-items:flex-start;gap:.6rem;font-size:.8rem;color:#1e40af;margin-top:.85rem}
.wi-notice svg{flex-shrink:0;margin-top:1px}
/* FOOTER */
.wi-footer{margin-top:1.5rem}
.wi-btn-main{width:100%;padding:.95rem 1rem;background:linear-gradient(135deg,#1d4ed8,#3b82f6);color:#fff;border:none;border-radius:12px;font-size:1rem;font-weight:800;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.6rem;transition:all .2s;box-shadow:0 5px 18px rgba(29,78,216,.3);letter-spacing:.02em}
.wi-btn-main:hover{background:linear-gradient(135deg,#1e40af,#2563eb);transform:translateY(-1px)}
.wi-btn-main:active{transform:translateY(0)}
.wi-btn-main:disabled{opacity:.55;cursor:not-allowed;transform:none}
.wi-footer-links{display:flex;align-items:center;justify-content:center;gap:1.5rem;margin-top:.85rem}
.wi-footer-link{font-size:.8rem;font-weight:700;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:.35rem}
.wi-footer-link:hover{color:#1d4ed8}

/* UTILS */
.wi-spin{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:wspin .7s linear infinite;flex-shrink:0}
.wi-spin-sm{width:13px;height:13px;border:2px solid #e2e8f0;border-top-color:#2563eb;border-radius:50%;animation:wspin .7s linear infinite;display:inline-block;vertical-align:middle}
@keyframes wspin{to{transform:rotate(360deg)}}
.wi-toast{position:fixed;bottom:1.5rem;right:1.5rem;background:#1e293b;color:#fff;border-radius:12px;padding:.8rem 1.1rem;display:flex;align-items:center;gap:.6rem;font-size:.85rem;font-weight:600;z-index:9999;transform:translateY(100px);opacity:0;transition:all .3s cubic-bezier(.34,1.56,.64,1);pointer-events:none;box-shadow:0 8px 24px rgba(0,0,0,.2);max-width:360px}
.wi-toast.show{transform:translateY(0);opacity:1}
.wi-toast--success{background:#166534}
.wi-toast--error{background:#991b1b}
</style>
@endpush

@section('content')
<div class="wi-wrap">

{{-- STEPPER --}}
<div class="wi-stepper">
    <div class="wi-step-item s-active" id="stp-1">
        <div class="wi-step-circle" id="stp-c-1">1</div>
        <span class="wi-step-lbl">Đăng ký</span>
    </div>
    <div class="wi-step-item" id="stp-2">
        <div class="wi-step-circle" id="stp-c-2">2</div>
        <span class="wi-step-lbl">Chụp Ảnh</span>
    </div>
    <div class="wi-step-item" id="stp-3">
        <div class="wi-step-circle" id="stp-c-3">3</div>
        <span class="wi-step-lbl">Xác Nhận</span>
    </div>
</div>

{{-- PHẦN 1: THÔNG TIN KHÁCH --}}
<div class="wi-sec">
    <div class="wi-sec-hd">
        <div class="wi-sec-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        </div>
        <h2 class="wi-sec-title">Phần 1: Thông tin khách đăng ký</h2>
    </div>
    <div class="wi-sec-body">
        <div class="wi-grid2">
            <div class="wi-fld">
                <label class="wi-lbl" for="guest_name">Họ và tên khách <em>*</em></label>
                <input type="text" id="guest_name" class="wi-inp" placeholder="Nhập đầy đủ họ tên" maxlength="100" autocomplete="off">
            </div>
            <div class="wi-fld">
                <label class="wi-lbl" for="guest_phone">Số điện thoại</label>
                <input type="text" id="guest_phone" class="wi-inp" placeholder="090x xxx xxx" maxlength="20" autocomplete="off">
            </div>
            <div class="wi-fld wi-span2">
                <label class="wi-lbl">Căn hộ đến thăm <em>*</em> <span id="apt-load" style="display:none"><span class="wi-spin-sm"></span></span></label>
                <div class="wi-apt-wrap">
                    <input type="text" id="apt_filter" class="wi-inp" placeholder="Chọn căn hộ..." autocomplete="off"
                        oninput="filterApt(this.value)" onfocus="openAptDd()" onblur="closeAptDd()">
                    <input type="hidden" id="apartment_id">
                    <div id="apt_dd" class="wi-apt-dd">
                        @foreach($apartments as $apt)
                        <div class="wi-apt-opt" onmousedown="pickApt({{ $apt->id }},'{{ addslashes($apt->apartment_number.($apt->floor?->block?' ('.$apt->floor->block->name.')':'')) }}')">{{ $apt->apartment_number }}{{ $apt->floor?->block?' ('.$apt->floor->block->name.')':'' }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="wi-fld wi-span2" id="res-box" style="display:none">
                <div id="res-list"></div>
                <input type="hidden" id="confirmed_by_resident">
                <input type="text" id="res_manual" class="wi-inp" style="display:none;margin-top:.4rem" placeholder="Nhập tên cư dân cần gặp" maxlength="100">
            </div>
            <div class="wi-fld wi-span2">
                <label class="wi-lbl" for="note">Lý do thăm</label>
                <textarea id="note" class="wi-ta" placeholder="Ví dụ: Thăm người thân, giao hàng, bảo trì..."></textarea>
            </div>
        </div>
    </div>
</div>

{{-- PHẦN 2: CHỤP ẢNH --}}
<div class="wi-sec">
    <div class="wi-sec-hd">
        <div class="wi-sec-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
        <h2 class="wi-sec-title">Phần 2: Chụp ảnh định danh</h2>
    </div>
    <div class="wi-sec-body">
        <div class="wi-cam-layout">
            <div>
                <div class="wi-cam-vp" id="cam-vp">
                    <video id="cam-video" autoplay playsinline muted style="display:none;width:100%;height:100%;object-fit:cover"></video>
                    <div class="wi-cam-off" id="cam-off">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <p>Camera chưa bật</p>
                    </div>
                </div>
                <button type="button" id="btn-snap" class="wi-snap-btn" onclick="snapPhoto()" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                    CHỤP ẢNH (F9)
                </button>
            </div>
            <div>
                <p class="wi-prev-lbl">Ảnh xem trước</p>
                <div class="wi-prev-box">
                    <div class="wi-prev-empty" id="prev-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <p>Bật và chụp ảnh để<br>xem trước ở đây</p>
                    </div>
                    <img id="prev-img" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover">
                </div>
                <div class="wi-cam-acts">
                    <button type="button" class="wi-btn-sm wi-btn-del" id="btn-del" onclick="clearPhoto()" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        Xoá ảnh
                    </button>
                    <button type="button" class="wi-btn-sm wi-btn-outline" id="btn-cam" onclick="toggleCam()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                        <span id="cam-lbl">Bật camera</span>
                    </button>
                </div>
            </div>
        </div>
        <canvas id="wi-canvas" style="display:none"></canvas>
        <input type="hidden" id="face_image_data">
    </div>
</div>

{{-- PHẦN 3: KIỂM TRA & XÁC NHẬN --}}
<div class="wi-sec">
    <div class="wi-sec-hd">
        <div class="wi-sec-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <h2 class="wi-sec-title">Phần 3: Kiểm tra &amp; Xác nhận</h2>
    </div>
    <div class="wi-sec-body">
        <div class="wi-review">
            <div class="wi-rv-rows">
                <div class="wi-rv-row"><span class="wi-rv-lbl">Khách:</span><span class="wi-rv-val accent" id="rv-name">—</span></div>
                <div class="wi-rv-row"><span class="wi-rv-lbl">Số điện thoại:</span><span class="wi-rv-val" id="rv-phone">—</span></div>
                <div class="wi-rv-row"><span class="wi-rv-lbl">Căn hộ:</span><span class="wi-rv-val" id="rv-apt">—</span></div>
                <div class="wi-rv-row" id="rv-note-row" style="display:none"><span class="wi-rv-lbl">Lý do:</span><span class="wi-rv-val" id="rv-note" style="font-style:italic"></span></div>
            </div>
            <div class="wi-rv-thumb">
                <div class="wi-rv-thumb-empty" id="rv-thumb-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span style="font-size:.65rem;color:#94a3b8;margin-top:.2rem;display:block">Ảnh khách</span>
                </div>
                <img id="rv-photo" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover">
            </div>
        </div>
        <div class="wi-notice">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span id="rv-notice">Vui lòng điền đầy đủ thông tin ở Phần 1 để xem trước ở đây.</span>
        </div>
    </div>
</div>

{{-- FOOTER --}}
<div class="wi-footer">
    <button type="button" id="btn-submit" class="wi-btn-main" onclick="doSubmit()">
        GỬI THÔNG BÁO CHO CƯ DÂN
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
    </button>
    <div class="wi-footer-links">
        <a href="#" class="wi-footer-link" onclick="printGuest();return false">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            In thẻ khách
        </a>
        <a href="#" class="wi-footer-link" onclick="resetForm();return false">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
            Huỷ đăng ký
        </a>
    </div>
</div>

</div>{{-- /wi-wrap --}}
<div class="wi-toast" id="wi-toast"><span id="toast-icon"></span><span id="toast-msg"></span></div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';
const URL_RES   = '{{ route("security.walk-in.residents") }}';
const URL_STORE = '{{ route("security.walk-in.store") }}';
const APT_DATA  = [
    @foreach($apartments as $apt)
    {id:{{ $apt->id }},label:'{{ addslashes($apt->apartment_number.($apt->floor?->block?' ('.$apt->floor->block->name.')':'')) }}'},
    @endforeach
];
let selResId='', selResName='', camStream=null, camOn=false;

/* STEPPER */
function setStep(n){
    [1,2,3].forEach(i=>{
        const it=document.getElementById('stp-'+i), c=document.getElementById('stp-c-'+i);
        it.className='wi-step-item'+(i<n?' s-done':(i===n?' s-active':''));
        c.innerHTML = i<n
            ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><polyline points="20 6 9 17 4 12"/></svg>'
            : i;
    });
}

/* APARTMENT DROPDOWN */
function openAptDd(){filterApt(document.getElementById('apt_filter').value);document.getElementById('apt_dd').classList.add('open')}
function closeAptDd(){setTimeout(()=>document.getElementById('apt_dd').classList.remove('open'),160)}
function filterApt(q){
    const dd=document.getElementById('apt_dd'),lo=q.trim().toLowerCase();
    const items=APT_DATA.filter(a=>a.label.toLowerCase().includes(lo));
    dd.innerHTML=items.length===0
        ?'<div class="wi-apt-opt wi-apt-opt--e">Không tìm thấy</div>'
        :items.map(a=>`<div class="wi-apt-opt" onmousedown="pickApt(${a.id},'${a.label.replace(/'/g,"\\'")}')">` +a.label+'</div>').join('');
    dd.classList.add('open');
}
function pickApt(id,label){
    document.getElementById('apt_filter').value=label;
    document.getElementById('apartment_id').value=id;
    document.getElementById('apt_dd').classList.remove('open');
    loadRes(id); updateReview();
}
</script>

<script>
/* RESIDENTS */
async function loadRes(aptId){
    const box=document.getElementById('res-box'),list=document.getElementById('res-list');
    const manual=document.getElementById('res_manual'),loader=document.getElementById('apt-load');
    selResId=''; selResName='';
    document.getElementById('confirmed_by_resident').value='';
    box.style.display='none'; manual.style.display='none'; manual.value='';
    if(!aptId) return;
    loader.style.display='inline';
    try{
        const r=await fetch(URL_RES+'?apartment_id='+aptId,{headers:{Accept:'application/json','X-CSRF-TOKEN':CSRF}});
        if(!r.ok) throw new Error('HTTP status ' + r.status);
        const data=await r.json(); const residents=data.residents||[];
        loader.style.display='none'; box.style.display='block';
        if(!residents.length){
            list.innerHTML='<div class="wi-res-empty"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> Căn hộ chưa có cư dân — nhập tên bên dưới</div>';
            manual.style.display='block';
            manual.focus();
        }else{
            list.innerHTML=residents.map(r=>`
                <div class="wi-res" id="res-${r.id}" onclick="pickRes(${r.id},'${(r.name||'').replace(/\\/g,"\\\\").replace(/'/g,"\\'")}')">
                    <div class="wi-res-avatar">${(r.name||'C').charAt(0).toUpperCase()}</div>
                    <div class="wi-res-info">
                        <div class="wi-res-name">${r.name||'Cư dân'}</div>
                        <div class="wi-res-phone">${r.phone?'<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><path d=\'M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13 19.79 19.79 0 0 1 1.54 4.18 2 2 0 0 1 3.5 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.08 6.08l1.08-1.08a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z\'/></svg> '+r.phone:'<span style=\'color:#94a3b8;font-weight:500\'>Không có số điện thoại</span>'}</div>
                    </div>
                    <div class="wi-res-chk"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" fill="#22c55e" stroke="none"/><polyline points="20 6 9 17 4 12" stroke="#fff"/></svg></div>
                </div>`).join('');
            if(residents.length===1) pickRes(residents[0].id,residents[0].name);
        }
    }catch(err){
        console.error('Lỗi tải cư dân:', err);
        loader.style.display='none';
        box.style.display='block';
        list.innerHTML='<p style="color:#ef4444;font-size:.82rem">Lỗi tải cư dân.</p>';
    }
}
function pickRes(id,name){
    document.querySelectorAll('.wi-res').forEach(el=>el.classList.remove('sel'));
    document.getElementById('res-'+id)?.classList.add('sel');
    selResId=id; selResName=name;
    document.getElementById('confirmed_by_resident').value=id;
    updateReview();
}

/* CAMERA */
async function toggleCam(){camOn?stopCam():await startCam();}
async function startCam(){
    try{
        camStream=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'},audio:false});
        const v=document.getElementById('cam-video');
        v.srcObject=camStream; v.style.display='block';
        document.getElementById('cam-off').style.display='none';
        document.getElementById('btn-snap').disabled=false;
        document.getElementById('cam-lbl').textContent='Tắt camera';
        camOn=true; setStep(2);
    }catch(e){showToast('Không thể truy cập camera: '+e.message,'error');}
}
function stopCam(){
    if(camStream){camStream.getTracks().forEach(t=>t.stop());camStream=null;}
    const v=document.getElementById('cam-video');
    v.srcObject=null; v.style.display='none';
    document.getElementById('cam-off').style.display='flex';
    document.getElementById('btn-snap').disabled=true;
    document.getElementById('cam-lbl').textContent='Bật camera';
    camOn=false;
}
function snapPhoto(){
    const v=document.getElementById('cam-video'),c=document.getElementById('wi-canvas');
    c.width=v.videoWidth||640; c.height=v.videoHeight||480;
    c.getContext('2d').drawImage(v,0,0);
    const url=c.toDataURL('image/jpeg',.88);
    document.getElementById('face_image_data').value=url;
    document.getElementById('prev-img').src=url;
    document.getElementById('prev-img').style.display='block';
    document.getElementById('prev-empty').style.display='none';
    document.getElementById('btn-del').disabled=false;
    document.getElementById('rv-photo').src=url;
    document.getElementById('rv-photo').style.display='block';
    document.getElementById('rv-thumb-empty').style.display='none';
    stopCam(); setStep(3); updateReview();
    showToast('Đã chụp ảnh thành công!','success');
}
function clearPhoto(){
    document.getElementById('face_image_data').value='';
    document.getElementById('prev-img').style.display='none';
    document.getElementById('prev-empty').style.display='flex';
    document.getElementById('btn-del').disabled=true;
    document.getElementById('rv-photo').style.display='none';
    document.getElementById('rv-thumb-empty').style.display='flex';
    setStep(2);
}
document.addEventListener('keydown',e=>{if(e.key==='F9'){e.preventDefault();if(!document.getElementById('btn-snap').disabled)snapPhoto();}});
</script>

<script>
/* REVIEW */
function updateReview(){
    const name=document.getElementById('guest_name').value.trim();
    const phone=document.getElementById('guest_phone').value.trim();
    const aptLbl=document.getElementById('apt_filter').value.trim();
    const manual=document.getElementById('res_manual').value.trim();
    const resident=selResName||manual;
    const note=document.getElementById('note').value.trim();
    document.getElementById('rv-name').textContent=name||'—';
    document.getElementById('rv-phone').textContent=phone||'—';
    document.getElementById('rv-apt').textContent=aptLbl?(aptLbl+(resident?' ('+resident+')'):''):'—';
    const nr=document.getElementById('rv-note-row');
    if(note){document.getElementById('rv-note').textContent='"'+note+'"';nr.style.display='flex';}
    else{nr.style.display='none';}
    const notice=document.getElementById('rv-notice');
    if(!name) notice.textContent='Vui lòng điền đầy đủ thông tin ở Phần 1 để xem trước ở đây.';
    else if(!aptLbl) notice.textContent='Vui lòng chọn căn hộ đến thăm.';
    else if(!resident) notice.textContent='Vui lòng chọn hoặc nhập tên cư dân cần gặp.';
    else if(selResId) notice.innerHTML='Sẵn sàng gửi thông báo đến cư dân <strong>'+selResName+'</strong> và ghi nhận khách vào.';
    else notice.textContent='Thông tin đã đầy đủ. Bấm nút bên dưới để ghi nhận.';
}
['guest_name','guest_phone','note','res_manual'].forEach(id=>{
    document.getElementById(id)?.addEventListener('input',updateReview);
});

/* SUBMIT */
async function doSubmit(){
    const name=document.getElementById('guest_name').value.trim();
    const aptId=document.getElementById('apartment_id').value;
    const resident=selResName||document.getElementById('res_manual').value.trim();
    if(!name){showToast('Vui lòng nhập họ tên khách.','error');document.getElementById('guest_name').focus();return;}
    if(!aptId){showToast('Vui lòng chọn căn hộ.','error');document.getElementById('apt_filter').focus();return;}
    if(!resident){showToast('Vui lòng chọn hoặc nhập tên cư dân cần gặp.','error');return;}
    const btn=document.getElementById('btn-submit');
    btn.disabled=true;
    btn.innerHTML='<div class="wi-spin"></div> Đang ghi nhận...';
    try{
        const r=await fetch(URL_STORE,{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body:JSON.stringify({
                guest_name:name,
                guest_phone:document.getElementById('guest_phone').value.trim()||null,
                apartment_id:parseInt(aptId),
                resident_to_meet:resident,
                confirmed_by_resident:selResId||null,
                note:document.getElementById('note').value.trim()||null,
                vehicle_plate:null,vehicle_type:null,
                face_image:document.getElementById('face_image_data').value||null,
                notify_resident:!!selResId,
            }),
        });
        const data=await r.json();
        if(data.success){showToast(data.message+(selResId?' · Đã thông báo cư dân.':''),'success');setTimeout(resetForm,1400);}
        else showToast(data.message||'Có lỗi xảy ra.','error');
    }catch{showToast('Lỗi kết nối.','error');}
    btn.disabled=false;
    btn.innerHTML='GỬI THÔNG BÁO CHO CƯ DÂN <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>';
}

/* RESET */
function resetForm(){
    ['guest_name','guest_phone','note'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('apt_filter').value=''; document.getElementById('apartment_id').value='';
    document.getElementById('res-box').style.display='none';
    document.getElementById('res-list').innerHTML='';
    document.getElementById('res_manual').style.display='none';
    document.getElementById('res_manual').value='';
    document.getElementById('confirmed_by_resident').value='';
    selResId=''; selResName='';
    clearPhoto(); stopCam(); setStep(1); updateReview();
    window.scrollTo({top:0,behavior:'smooth'});
}
function printGuest(){showToast('Tính năng in thẻ sẽ sớm ra mắt.','error');}

/* TOAST */
function showToast(msg,type){
    const t=document.getElementById('wi-toast');
    t.className='wi-toast wi-toast--'+type;
    document.getElementById('toast-icon').textContent=type==='success'?'✓ ':'✕ ';
    document.getElementById('toast-msg').textContent=msg;
    t.classList.add('show');
    clearTimeout(t._t); t._t=setTimeout(()=>t.classList.remove('show'),4000);
}

/* INIT */
setStep(1); updateReview();
</script>
@endsection
