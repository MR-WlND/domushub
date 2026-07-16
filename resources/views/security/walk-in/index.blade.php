@extends('layouts.security.master')

@section('page_title', 'Đăng ký khách tại cổng — DomusHub')

@push('styles')
    @vite(['resources/css/security/qr-scanner.css'])
    <style>
        /* ---- Walk-in Page Extras ---- */
        .wi-flow {
            display: flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .wi-flow__step {
            display: flex;
            align-items: center;
            gap: .5rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 999px;
            padding: .4rem .9rem;
            font-size: .78rem;
            font-weight: 600;
            color: #64748b;
        }
        .wi-flow__step--active {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }
        .wi-flow__arrow { color: #cbd5e1; font-size: .9rem; }

        /* ---- Form ---- */
        .wi-form-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,35,111,.04);
        }
        .wi-form-card__header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .wi-form-card__icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: #eff6ff;
            display: flex; align-items: center; justify-content: center;
            color: #2563eb;
            flex-shrink: 0;
        }
        .wi-form-card__title { font-size: .9rem; font-weight: 700; color: #1e293b; margin: 0; }
        .wi-form-card__body { padding: 1.5rem; }

        .wi-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 640px) { .wi-form-grid { grid-template-columns: 1fr; } }

        .wi-field { display: flex; flex-direction: column; gap: .4rem; }
        .wi-field--full { grid-column: 1 / -1; }
        .wi-label {
            font-size: .78rem; font-weight: 700; color: #475569;
            text-transform: uppercase; letter-spacing: .06em;
        }
        .wi-label span { color: #ef4444; margin-left: 2px; }
        .wi-input, .wi-select, .wi-textarea {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: .65rem .9rem;
            font-size: .88rem;
            font-family: inherit;
            color: #1e293b;
            transition: border-color .15s, background .15s;
            width: 100%;
            box-sizing: border-box;
        }
        .wi-input:focus, .wi-select:focus, .wi-textarea:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,.08);
        }
        .wi-input::placeholder, .wi-textarea::placeholder { color: #94a3b8; }
        .wi-select { cursor: pointer; }
        .wi-textarea { min-height: 70px; resize: vertical; }

        .wi-resident-box {
            background: #f0fdf4;
            border: 1.5px solid #bbf7d0;
            border-radius: 9px;
            padding: .7rem .9rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-top: .4rem;
            font-size: .85rem;
        }
        .wi-resident-box__name { font-weight: 700; color: #166534; }
        .wi-resident-box__phone { color: #4ade80; font-size: .78rem; }

        .wi-apt-filter-wrap { position: relative; }
        .wi-apt-filter-input {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            padding: .65rem .9rem;
            font-size: .88rem;
            font-family: inherit;
            color: #1e293b;
            width: 100%;
            box-sizing: border-box;
            transition: border-color .15s, background .15s;
        }
        .wi-apt-filter-input:focus { outline: none; border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
        .wi-apt-filter-input::placeholder { color: #94a3b8; }
        .wi-apt-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0; right: 0;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
            max-height: 220px;
            overflow-y: auto;
            z-index: 100;
            display: none;
        }
        .wi-apt-dropdown.open { display: block; }
        .wi-apt-option {
            padding: .6rem .9rem;
            font-size: .87rem;
            cursor: pointer;
            color: #1e293b;
            transition: background .1s;
        }
        .wi-apt-option:hover, .wi-apt-option.highlighted { background: #eff6ff; color: #1d4ed8; }
        .wi-apt-option--empty { color: #94a3b8; cursor: default; }
        .wi-apt-option--empty:hover { background: transparent; color: #94a3b8; }

        .wi-submit-btn {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .95rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            transition: all .2s;
            margin-top: 1.25rem;
            box-shadow: 0 4px 12px rgba(29,78,216,.2);
        }
        .wi-submit-btn:hover { background: linear-gradient(135deg, #1e40af, #1d4ed8); box-shadow: 0 6px 16px rgba(29,78,216,.3); }
        .wi-submit-btn:disabled { opacity: .5; cursor: not-allowed; }

        /* ---- Right panel: Current visitors ---- */
        .wi-visitors-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,35,111,.04);
        }
        .wi-visitors-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }
        .wi-visitors-title {
            font-size: .9rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .wi-badge-count {
            background: #2563eb;
            color: #fff;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 800;
            padding: 1px 8px;
            min-width: 22px;
            text-align: center;
        }
        .wi-visitor-list { padding: 0; list-style: none; margin: 0; }
        .wi-visitor-item {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .9rem 1.25rem;
            border-bottom: 1px solid #f8fafc;
            transition: background .12s;
        }
        .wi-visitor-item:last-child { border-bottom: none; }
        .wi-visitor-item:hover { background: #fafbfc; }
        .wi-visitor-avatar {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #dbeafe, #eff6ff);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            color: #2563eb;
            font-weight: 800;
            font-size: .95rem;
        }
        .wi-visitor-avatar--walkin { background: linear-gradient(135deg, #dcfce7, #f0fdf4); color: #16a34a; }
        .wi-visitor-info { flex: 1; min-width: 0; }
        .wi-visitor-name { font-size: .88rem; font-weight: 700; color: #0f172a; margin: 0 0 .15rem; }
        .wi-visitor-meta { font-size: .77rem; color: #64748b; display: flex; flex-wrap: wrap; gap: .3rem .75rem; }
        .wi-visitor-apt { font-weight: 600; color: #2563eb; }
        .wi-visitor-time { color: #94a3b8; }
        .wi-visitor-badge-walkin {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: .68rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            flex-shrink: 0;
        }
        .wi-visitor-badge-qr {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .wi-checkout-btn {
            flex-shrink: 0;
            padding: .35rem .7rem;
            background: #fff;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 7px;
            font-size: .75rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all .15s;
            white-space: nowrap;
        }
        .wi-checkout-btn:hover { background: #fef2f2; border-color: #ef4444; }
        .wi-empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
            color: #94a3b8;
            font-size: .85rem;
        }
        .wi-empty-state svg { margin-bottom: .75rem; opacity: .4; }

        /* Loading spinner for resident search */
        .wi-resident-loading { display: none; }
        .wi-select-loading .wi-resident-loading { display: inline-block; }
        .wi-spin-sm {
            width: 14px; height: 14px;
            border: 2px solid #e2e8f0;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Divider label */
        .wi-section-label {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #94a3b8;
            margin: 1.25rem 0 .75rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .wi-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f1f5f9;
        }
    </style>
@endpush

@section('content')
<div class="qs-page">

    {{-- Page Header --}}
    <div class="qs-header">
        <p class="qs-eyebrow">Quản lý khách</p>
        <h1 class="qs-title">Đăng ký khách tại cổng</h1>
    </div>



    <div class="qs-layout">

        {{-- ===== LEFT: REGISTRATION FORM ===== --}}
        <div class="wi-form-card">
            <div class="wi-form-card__header">
                <div class="wi-form-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
                <h2 class="wi-form-card__title">Thông tin khách vãng lai</h2>
            </div>

            <div class="wi-form-card__body">

                {{-- THÔNG TIN KHÁCH --}}
                <p class="wi-section-label">Thông tin khách</p>
                <div class="wi-form-grid">
                    <div class="wi-field">
                        <label class="wi-label" for="guest_name">Họ tên khách <span>*</span></label>
                        <input type="text" id="guest_name" class="wi-input" placeholder="Nguyễn Văn A" maxlength="100" autocomplete="off">
                    </div>
                    <div class="wi-field">
                        <label class="wi-label" for="guest_phone">Số điện thoại</label>
                        <input type="text" id="guest_phone" class="wi-input" placeholder="09x xxx xxxx" maxlength="20" autocomplete="off">
                    </div>
                </div>

                {{-- THÔNG TIN CĂN HỘ --}}
                <p class="wi-section-label">Căn hộ muốn gặp</p>
                <div class="wi-field">
                    <label class="wi-label">Chọn căn hộ <span>*</span>
                        <span class="wi-resident-loading"><div class="wi-spin-sm" style="display:inline-block;vertical-align:middle;margin-left:4px;"></div></span>
                    </label>
                    <div class="wi-apt-filter-wrap">
                        <input type="text" id="apt_filter_input" class="wi-apt-filter-input" placeholder="Tìm số căn hộ..." autocomplete="off"
                               oninput="filterApartments(this.value)" onfocus="openAptDropdown()" onblur="closeAptDropdown()">
                        <input type="hidden" id="apartment_select">
                        <div id="apt_dropdown" class="wi-apt-dropdown">
                            @foreach($apartments as $apt)
                                <div class="wi-apt-option"
                                     data-id="{{ $apt->id }}"
                                     data-label="{{ $apt->apartment_number }}{{ $apt->floor?->block ? ' (' . $apt->floor->block->name . ')' : '' }}"
                                     onmousedown="selectApartment({{ $apt->id }}, '{{ $apt->apartment_number }}{{ $apt->floor?->block ? ' (' . $apt->floor->block->name . ')' : '' }}')">{{ $apt->apartment_number }}{{ $apt->floor?->block ? ' (' . $apt->floor->block->name . ')' : '' }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Auto hiển thị chủ căn hộ --}}
                <div id="resident-info-box" style="display:none;margin-top:.75rem;">
                    <div id="resident-info-list"></div>
                    <input type="hidden" id="confirmed_by_resident" value="">
                    <input type="text" id="resident_to_meet_manual" class="wi-input" style="display:none;margin-top:.5rem;" placeholder="Nhập tên cư dân cần gặp" maxlength="100">
                </div>

                {{-- GHI CHÚ --}}
                <p class="wi-section-label">Ghi chú</p>
                <div class="wi-field wi-field--full">
                    <label class="wi-label" for="note">Ghi chú thêm</label>
                    <textarea id="note" class="wi-textarea" placeholder="Mục đích thăm, hàng hóa mang theo…"></textarea>
                </div>

                {{-- SUBMIT --}}
                <button type="button" id="submit-btn" class="wi-submit-btn" onclick="submitWalkIn()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Xác nhận Cho Vào
                </button>

            </div>
        </div>

        {{-- ===== RIGHT PANEL ===== --}}
        <div class="wi-visitors-card">
                <div class="wi-visitors-header">
                    <h2 class="wi-visitors-title">
                        Khách đang trong tòa
                        <span class="wi-badge-count" id="visitor-count">{{ $currentVisitors->count() }}</span>
                    </h2>
                    <a href="{{ route('security.visitor-logs.index') }}" style="font-size:.78rem;font-weight:600;color:#2563eb;text-decoration:none;">Lịch sử →</a>
                </div>
                <ul class="wi-visitor-list" id="visitor-list">
                    @forelse($currentVisitors as $v)
                        @php $apt = $v->apartment; $block = $apt?->floor?->block; @endphp
                        <li class="wi-visitor-item" id="vi-{{ $v->id }}">
                            <div class="wi-visitor-avatar wi-visitor-avatar--walkin">{{ mb_strtoupper(mb_substr($v->guest_name,0,1)) }}</div>
                            <div class="wi-visitor-info">
                                <p class="wi-visitor-name">{{ $v->guest_name }}</p>
                                <div class="wi-visitor-meta">
                                    <span class="wi-visitor-apt">{{ $apt?->apartment_number ?? '—' }}@if($block) · {{ $block->name }}@endif</span>
                                    <span>Gặp: {{ $v->resident_to_meet ?? '—' }}</span>
                                    <span class="wi-visitor-time">{{ $v->check_in_at?->format('H:i') }}</span>
                                </div>
                                @if($v->hasVehicle())
                                    <div style="font-size:.75rem;color:#2563eb;font-weight:600;margin-top:.2rem;">🚗 {{ $v->vehicle_plate }}</div>
                                @endif
                            </div>
                            <button class="wi-checkout-btn" onclick="checkoutVisitor({{ $v->id }}, '{{ $v->guest_name }}')">Cho Ra</button>
                        </li>
                    @empty
                        <li class="wi-empty-state" id="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <p>Chưa có khách nào đang ở trong tòa</p>
                        </li>
                    @endforelse
                </ul>
        </div>

    </div>
</div>

{{-- Toast --}}
<div class="qs-toast" id="toast">
    <span id="toast-icon"></span>
    <span id="toast-text"></span>
</div>

<script>
const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
const RESIDENTS_URL = '{{ route("security.walk-in.residents") }}';
const STORE_URL     = '{{ route("security.walk-in.store") }}';
const CHECKOUT_URL  = '{{ route("security.walk-in.checkout") }}';

let selectedResidentName = '';
let selectedResidentId   = '';

// Khi chọn căn hộ → tự động load & hiển thị chủ căn hộ
async function onApartmentChange(apartmentId) {
    const infoBox  = document.getElementById('resident-info-box');
    const infoList = document.getElementById('resident-info-list');
    const manual   = document.getElementById('resident_to_meet_manual');
    const loading  = document.querySelector('.wi-resident-loading');

    selectedResidentName = '';
    selectedResidentId   = '';
    document.getElementById('confirmed_by_resident').value = '';
    infoBox.style.display  = 'none';
    manual.style.display   = 'none';
    manual.value           = '';

    if (!apartmentId) return;

    loading.style.display = 'inline';
    try {
        const res  = await fetch(RESIDENTS_URL + '?apartment_id=' + apartmentId, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();
        const residents = data.residents || [];

        loading.style.display = 'none';
        infoBox.style.display = 'block';

        if (residents.length === 0) {
            infoList.innerHTML = '';
            manual.style.display = 'block';
            manual.focus();
        } else {
            // Hiển thị tất cả cư dân, click để chọn
            infoList.innerHTML = residents.map(r => `
                <div class="wi-resident-box" id="res-${r.id}" onclick="selectResident(${r.id}, '${r.name.replace(/'/g, "\\'")}')"
                     style="cursor:pointer;margin-bottom:.4rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <div style="flex:1;">
                        <div class="wi-resident-box__name">${r.name}</div>
                        <div class="wi-resident-box__phone">${r.phone ? '📞 ' + r.phone : 'Không có SĐT'}</div>
                    </div>
                    <div id="res-check-${r.id}" style="display:none;color:#16a34a;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                </div>
            `).join('');

            // Tự động chọn cư dân đầu tiên
            if (residents.length === 1) {
                selectResident(residents[0].id, residents[0].name);
            }
        }
    } catch {
        loading.style.display = 'none';
        infoBox.style.display = 'block';
        infoList.innerHTML = '<p style="color:#ef4444;font-size:.83rem;">Lỗi tải danh sách cư dân.</p>';
    }
}

function selectResident(id, name) {
    // Bỏ chọn tất cả
    document.querySelectorAll('[id^="res-check-"]').forEach(el => el.style.display = 'none');
    document.querySelectorAll('[id^="res-"]').forEach(el => {
        if (el.id.startsWith('res-') && !el.id.startsWith('res-check-')) {
            el.style.background = '#f0fdf4';
            el.style.borderColor = '#bbf7d0';
        }
    });

    // Đánh dấu đã chọn
    const checkEl = document.getElementById('res-check-' + id);
    const boxEl   = document.getElementById('res-' + id);
    if (checkEl) checkEl.style.display = 'block';
    if (boxEl)   { boxEl.style.background = '#dcfce7'; boxEl.style.borderColor = '#86efac'; }

    selectedResidentId   = id;
    selectedResidentName = name;
    document.getElementById('confirmed_by_resident').value = id;
}

// ---- Apartment filter ----
const APT_DATA = [
    @foreach($apartments as $apt)
    { id: {{ $apt->id }}, label: '{{ $apt->apartment_number }}{{ $apt->floor?->block ? ' (' . $apt->floor->block->name . ')' : '' }}' },
    @endforeach
];

function openAptDropdown() {
    filterApartments(document.getElementById('apt_filter_input').value);
    document.getElementById('apt_dropdown').classList.add('open');
}
function closeAptDropdown() {
    setTimeout(() => document.getElementById('apt_dropdown').classList.remove('open'), 150);
}
function filterApartments(q) {
    const dd = document.getElementById('apt_dropdown');
    const lower = q.toLowerCase();
    const filtered = APT_DATA.filter(a => a.label.toLowerCase().includes(lower));
    if (filtered.length === 0) {
        dd.innerHTML = '<div class="wi-apt-option wi-apt-option--empty">Không tìm thấy căn hộ</div>';
    } else {
        dd.innerHTML = filtered.map(a =>
            `<div class="wi-apt-option" data-id="${a.id}" onmousedown="selectApartment(${a.id}, '${a.label.replace(/'/g, "\\'")}')">` + a.label + `</div>`
        ).join('');
    }
    dd.classList.add('open');
}
function selectApartment(id, label) {
    document.getElementById('apt_filter_input').value = label;
    document.getElementById('apartment_select').value = id;
    document.getElementById('apt_dropdown').classList.remove('open');
    onApartmentChange(id);
}

async function submitWalkIn() {
    const guestName    = document.getElementById('guest_name').value.trim();
    const guestPhone   = document.getElementById('guest_phone').value.trim();
    const apartmentId  = document.getElementById('apartment_select').value;
    const manual       = document.getElementById('resident_to_meet_manual');
    const confirmedId  = document.getElementById('confirmed_by_resident').value;
    const note         = document.getElementById('note').value.trim();
    const residentToMeet = selectedResidentName || manual.value.trim();

    if (!guestName)      { showToast('Vui lòng nhập họ tên khách.', 'error'); return; }
    if (!apartmentId)    { showToast('Vui lòng chọn căn hộ muốn gặp.', 'error'); return; }
    if (!residentToMeet) { showToast('Vui lòng chọn cư dân cần gặp.', 'error'); return; }

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<div class="qs-spin"></div> Đang ghi nhận...';

    try {
        const res  = await fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({
                guest_name:             guestName,
                guest_phone:            guestPhone || null,
                apartment_id:           parseInt(apartmentId),
                resident_to_meet:       residentToMeet,
                confirmed_by_resident:  confirmedId || null,
                note:                   note || null,
            }),
        });
        const data = await res.json();

        if (data.success) {
            showToast(data.message, 'success');
            addVisitorToList(data.visitor);
            resetForm();
        } else {
            showToast(data.message || 'Có lỗi xảy ra.', 'error');
        }
    } catch {
        showToast('Lỗi kết nối. Vui lòng thử lại.', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Xác nhận Cho Vào';
}

async function checkoutVisitor(visitorId, guestName) {
    if (!confirm(`Xác nhận cho khách "${guestName}" ra?`)) return;
    try {
        const res  = await fetch(CHECKOUT_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ visitor_id: visitorId }),
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            const item = document.getElementById('vi-' + visitorId);
            if (item) {
                item.style.transition = 'opacity .3s';
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    const badge = document.getElementById('visitor-count');
                    badge.textContent = Math.max(0, parseInt(badge.textContent || '0') - 1);
                }, 300);
            }
        } else {
            showToast(data.message || 'Lỗi ghi nhận.', 'error');
        }
    } catch {
        showToast('Lỗi kết nối.', 'error');
    }
}

function addVisitorToList(v) {
    const list  = document.getElementById('visitor-list');
    const empty = document.getElementById('empty-state');
    if (empty) empty.remove();

    const html = `
        <li class="wi-visitor-item" id="vi-${v.id}" style="opacity:0;transition:opacity .3s;">
            <div class="wi-visitor-avatar wi-visitor-avatar--walkin">${v.guest_name.charAt(0).toUpperCase()}</div>
            <div class="wi-visitor-info">
                <p class="wi-visitor-name">${v.guest_name}</p>
                <div class="wi-visitor-meta">
                    <span class="wi-visitor-apt">${v.apartment}${v.block !== '—' ? ' · ' + v.block : ''}</span>
                    <span>Gặp: ${v.resident_to_meet || '—'}</span>
                    <span class="wi-visitor-time">${v.check_in_at || 'Vừa xong'}</span>
                </div>
                ${v.has_vehicle ? `<div style="font-size:.75rem;color:#2563eb;font-weight:600;margin-top:.2rem;">🚗 ${v.vehicle_plate}</div>` : ''}
            </div>
            <button class="wi-checkout-btn" onclick="checkoutVisitor(${v.id}, '${v.guest_name.replace(/'/g, "\\'")}')">
                Cho Ra
            </button>
        </li>`;
    list.insertAdjacentHTML('afterbegin', html);
    requestAnimationFrame(() => document.getElementById('vi-' + v.id).style.opacity = '1');
    const badge = document.getElementById('visitor-count');
    badge.textContent = parseInt(badge.textContent || '0') + 1;
}

function resetForm() {
    document.getElementById('guest_name').value = '';
    document.getElementById('guest_phone').value = '';
    document.getElementById('apt_filter_input').value = '';
    document.getElementById('apartment_select').value = '';
    document.getElementById('resident-info-box').style.display = 'none';
    document.getElementById('resident-info-list').innerHTML = '';
    document.getElementById('resident_to_meet_manual').style.display = 'none';
    document.getElementById('resident_to_meet_manual').value = '';
    document.getElementById('confirmed_by_resident').value = '';
    document.getElementById('note').value = '';
    selectedResidentName = '';
    selectedResidentId   = '';
}

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.className = `qs-toast qs-toast--${type}`;
    document.getElementById('toast-icon').innerHTML = type === 'success' ? '✓' : '✕';
    document.getElementById('toast-text').textContent = msg;
    t.classList.add('is-visible');
    setTimeout(() => t.classList.remove('is-visible'), 3500);
}
</script>
@endsection
