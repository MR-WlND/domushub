@extends('layouts.resident.master')

@section('title', 'Đặt lịch – ' . $facility->name . ' – DomusHub')

@section('content')
<div class="rfb-page">

    {{-- Breadcrumb --}}
    <div class="rfb-breadcrumb">
        <a href="{{ route('resident.facilities.index') }}">Tiện ích</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('resident.facilities.show', $facility) }}">{{ $facility->name }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Đặt lịch</span>
    </div>

    <div class="rfb-layout">

        {{-- LEFT: Form --}}
        <div class="rfb-main">
            <div class="rfb-form-card">
                <div class="rfb-form-header">
                    <div class="rfb-form-header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="16" y1="2" x2="16" y2="6"/></svg>
                    </div>
                    <div>
                        <h1 class="rfb-form-title">Đặt lịch sử dụng</h1>
                        <p class="rfb-form-subtitle">{{ $facility->name }}</p>
                    </div>
                </div>

                @if($errors->any())
                <div class="rfb-alert rfb-alert--error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <ul>
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                @if(session('error'))
                <div class="rfb-alert rfb-alert--error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
                @endif

                <form method="POST" action="{{ route('resident.facilities.book.store', $facility) }}" id="bookingForm">
                    @csrf

                    {{-- Bước 1: Chọn ngày --}}
                    <div class="rfb-step">
                        <div class="rfb-step-label">
                            <span class="rfb-step-num">1</span>
                            Chọn ngày sử dụng
                        </div>
                        <input
                            type="date"
                            name="booking_date"
                            id="booking_date"
                            class="rfb-input"
                            value="{{ old('booking_date') }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            onchange="loadSlots(this.value)"
                        >
                    </div>

                    {{-- Bước 2: Chọn khung giờ --}}
                    <div class="rfb-step">
                        <div class="rfb-step-label">
                            <span class="rfb-step-num">2</span>
                            Chọn khung giờ
                        </div>
                        <div id="slots-container" class="rfb-slots-wrap">
                            <p class="rfb-slots-hint">← Vui lòng chọn ngày trước</p>
                        </div>
                        <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}" required>
                        <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time') }}" required>
                        <div id="selected-slot-display" class="rfb-selected-slot" style="display:none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Đã chọn: <strong id="slot-label-display"></strong>
                        </div>
                    </div>

                    {{-- Bước 3: Số người --}}
                    <div class="rfb-step">
                        <div class="rfb-step-label">
                            <span class="rfb-step-num">3</span>
                            Số người sử dụng
                        </div>
                        <div class="rfb-people-wrap">
                            <button type="button" class="rfb-people-btn" onclick="changePeople(-1)">−</button>
                            <input type="number" name="number_of_people" id="number_of_people"
                                class="rfb-people-input"
                                value="{{ old('number_of_people', 1) }}"
                                min="1" max="{{ $facility->capacity }}" required>
                            <button type="button" class="rfb-people-btn" onclick="changePeople(1)">+</button>
                            <span class="rfb-people-max">tối đa {{ $facility->capacity }} người</span>
                        </div>
                    </div>

                    {{-- Tổng tiền preview --}}
                    @if($facility->price_per_slot && $facility->price_per_slot > 0)
                    <div class="rfb-price-preview" id="price-preview">
                        <div class="rfb-price-row">
                            @if($facility->slot_duration == 0)
                            <span>Giá mỗi lượt / người</span>
                            @else
                            <span>Giá mỗi slot ({{ $facility->slot_duration }} phút) / người</span>
                            @endif
                            <span>{{ number_format($facility->price_per_slot) }}đ</span>
                        </div>
                        <div class="rfb-price-row" id="price-row-people" style="display:none">
                            <span>Số người × số slot</span>
                            <span id="price-formula">—</span>
                        </div>
                        <div class="rfb-price-row rfb-price-total">
                            <span>Tổng dự kiến</span>
                            <span id="total-price">Chọn giờ và số người</span>
                        </div>
                    </div>
                    @else
                    <div class="rfb-free-notice">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Tiện ích này <strong>miễn phí</strong> sử dụng
                    </div>
                    @endif

                    {{-- Submit --}}
                    <button type="submit" class="rfb-submit" id="submitBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Xác nhận đặt lịch
                    </button>
                    <p class="rfb-note">Lịch đặt sẽ chờ ban quản lý duyệt trước khi có hiệu lực.</p>
                </form>
            </div>
        </div>

        {{-- RIGHT: Sidebar info --}}
        <aside class="rfb-sidebar">
            <div class="rfb-info-card">
                <h3 class="rfb-info-title">Thông tin tiện ích</h3>
                <div class="rfb-info-row">
                    <div class="rfb-info-icon" style="background:#eff6ff;color:#2563eb">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <p class="rfb-info-label">Giờ hoạt động</p>
                        <p class="rfb-info-value">{{ $facility->operating_hours }}</p>
                    </div>
                </div>
                <div class="rfb-info-row">
                    <div class="rfb-info-icon" style="background:#f0fdf4;color:#16a34a">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <p class="rfb-info-label">Sức chứa tối đa</p>
                        <p class="rfb-info-value">{{ $facility->capacity }} người</p>
                    </div>
                </div>
                <div class="rfb-info-row">
                    <div class="rfb-info-icon" style="background:#faf5ff;color:#7c3aed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div>
                        <p class="rfb-info-label">Thời lượng mỗi lần</p>
                        @php
                            $dur = $facility->slot_duration ?? 60;
                            $durLabel = match((int)$dur){ 0=>'Cả ngày', 30=>'30 phút', 60=>'1 tiếng', 90=>'1.5 tiếng', 120=>'2 tiếng', default=>$dur.' phút'};
                        @endphp
                        <p class="rfb-info-value">{{ $durLabel }}</p>
                    </div>
                </div>
                <div class="rfb-info-row">
                    <div class="rfb-info-icon" style="background:#fff7ed;color:#ea580c">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <p class="rfb-info-label">Giá sử dụng</p>
                        <p class="rfb-info-value {{ (!$facility->price_per_slot || $facility->price_per_slot == 0) ? 'rfb-free' : 'rfb-paid' }}">
                            {{ $facility->price_label }}
                        </p>
                    </div>
                </div>

                @if($facility->rules)
                <div class="rfb-rules">
                    <p class="rfb-rules-title">📋 Nội quy & Lưu ý</p>
                    <p class="rfb-rules-text">{{ $facility->rules }}</p>
                </div>
                @endif
            </div>

            {{-- Trạng thái --}}
            <div class="rfb-available-notice">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>Tiện ích đang <strong>mở cửa</strong></span>
            </div>
        </aside>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.rfb-page { max-width: 1060px; margin: 0 auto; padding: 28px 20px; font-family: 'Inter', sans-serif; }

.rfb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; margin-bottom: 24px; }
.rfb-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.rfb-breadcrumb a:hover { text-decoration: underline; }

.rfb-layout { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }

/* Form card */
.rfb-form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 32px; box-shadow: 0 2px 20px rgba(0,0,0,0.06); }

.rfb-form-header { display: flex; align-items: center; gap: 16px; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
.rfb-form-header-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
.rfb-form-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.rfb-form-subtitle { font-size: 0.85rem; color: #64748b; margin: 0; }

/* Alert */
.rfb-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 10px; font-size: 0.875rem; margin-bottom: 20px; }
.rfb-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.rfb-alert ul { margin: 0; padding-left: 16px; }
.rfb-alert li { margin-bottom: 2px; }

/* Steps */
.rfb-step { margin-bottom: 28px; }
.rfb-step-label { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
.rfb-step-num { width: 26px; height: 26px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0; }

.rfb-input { width: 100%; padding: 11px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; color: #0f172a; outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box; }
.rfb-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }

/* Slots */
.rfb-slots-wrap { min-height: 60px; }
.rfb-slots-hint { font-size: 0.82rem; color: #94a3b8; font-style: italic; }
.rfb-slots-grid { display: flex; flex-wrap: wrap; gap: 10px; }
.rfb-slot-btn { padding: 9px 18px; border: 2px solid #e2e8f0; border-radius: 10px; background: #f8fafc; cursor: pointer; font-size: 0.82rem; font-weight: 600; color: #475569; transition: all 0.15s; font-family: monospace; }
.rfb-slot-btn:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.rfb-slot-btn.active { border-color: #3b82f6; background: #3b82f6; color: #fff; transform: scale(1.03); }
.rfb-slot-btn.booked { border-color: #f1f5f9; background: #f8fafc; color: #cbd5e1; cursor: not-allowed; text-decoration: line-through; }
.rfb-slots-loading { font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px; }
.rfb-slots-loading::before { content: ''; width: 14px; height: 14px; border: 2px solid #e2e8f0; border-top-color: #3b82f6; border-radius: 50%; animation: spin 0.6s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

.rfb-selected-slot { display: flex; align-items: center; gap: 8px; margin-top: 12px; padding: 10px 14px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 0.85rem; color: #2563eb; font-weight: 500; }

/* People */
.rfb-people-wrap { display: flex; align-items: center; gap: 12px; }
.rfb-people-btn { width: 36px; height: 36px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #f8fafc; font-size: 1.2rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #475569; transition: all 0.15s; }
.rfb-people-btn:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.rfb-people-input { width: 70px; text-align: center; padding: 8px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 1rem; font-weight: 700; color: #0f172a; outline: none; }
.rfb-people-input:focus { border-color: #3b82f6; }
.rfb-people-max { font-size: 0.78rem; color: #94a3b8; }

/* Price preview */
.rfb-price-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; margin-bottom: 24px; }
.rfb-price-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #64748b; margin-bottom: 8px; }
.rfb-price-row:last-child { margin-bottom: 0; }
.rfb-price-total { font-size: 1rem; font-weight: 700; color: #0f172a; padding-top: 10px; border-top: 1px solid #e2e8f0; margin-top: 4px; }

.rfb-free-notice { display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; font-size: 0.875rem; color: #15803d; margin-bottom: 24px; }

/* Submit */
.rfb-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 14px rgba(59,130,246,0.35); }
.rfb-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,130,246,0.45); }
.rfb-submit:active { transform: translateY(0); }
.rfb-note { font-size: 0.75rem; color: #94a3b8; text-align: center; margin-top: 10px; }

/* Sidebar */
.rfb-sidebar { position: sticky; top: 20px; display: flex; flex-direction: column; gap: 14px; }
.rfb-info-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px; display: flex; flex-direction: column; gap: 14px; }
.rfb-info-title { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0 0 4px; }
.rfb-info-row { display: flex; align-items: flex-start; gap: 12px; }
.rfb-info-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rfb-info-label { font-size: 0.72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 2px; }
.rfb-info-value { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin: 0; }
.rfb-free { color: #16a34a !important; }
.rfb-paid { color: #2563eb !important; }

.rfb-rules { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-top: 4px; }
.rfb-rules-title { font-size: 0.78rem; font-weight: 700; color: #92400e; margin: 0 0 6px; }
.rfb-rules-text { font-size: 0.78rem; color: #78350f; line-height: 1.6; margin: 0; white-space: pre-line; }

.rfb-available-notice { display: flex; align-items: center; gap: 8px; padding: 12px 14px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; font-size: 0.85rem; color: #15803d; }

@media (max-width: 768px) {
    .rfb-layout { grid-template-columns: 1fr; }
    .rfb-sidebar { position: static; }
}
</style>

<script>
const pricePerSlot = {{ $facility->price_per_slot ?? 0 }};
const slotDuration = {{ $facility->slot_duration ?? 60 }};
const availableSlotsUrl = '{{ route("resident.facilities.index") }}';
const facilityId = {{ $facility->id }};

let selectedStart = '';
let selectedEnd = '';

async function loadSlots(date) {
    if (!date) return;

    const container = document.getElementById('slots-container');
    container.innerHTML = '<div class="rfb-slots-loading">Đang tải khung giờ...</div>';

    try {
        const res = await fetch('/resident/api/available-slots', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': 'Bearer ' + (document.querySelector('meta[name="api-token"]')?.getAttribute('content') || ''),
            },
            body: JSON.stringify({ facility_id: facilityId, booking_date: date }),
        });

        const json = await res.json();

        if (json.success && json.available_slots.length > 0) {
            const allSlots = @json($slots);
            renderSlots(allSlots, json.available_slots);
        } else {
            container.innerHTML = '<p style="color:#94a3b8;font-size:0.85rem">Không còn khung giờ trống cho ngày này.</p>';
        }
    } catch (e) {
        // Fallback: show all slots without availability check
        const allSlots = @json($slots);
        renderSlotsFallback(allSlots);
    }
}

function renderSlots(allSlots, availableSlots) {
    const container = document.getElementById('slots-container');

    if (allSlots.length === 0) {
        container.innerHTML = '<p style="color:#94a3b8;font-size:0.85rem">Tiện ích chưa cài đặt khung giờ.</p>';
        return;
    }

    let html = '<div class="rfb-slots-grid">';
    allSlots.forEach(slot => {
        const matched = availableSlots.find(s => s.start === slot.start);
        if (matched) {
            const rem = matched.remaining_capacity !== undefined ? matched.remaining_capacity : {{ $facility->capacity }};
            const remText = matched.remaining_capacity !== undefined ? ` (Còn ${matched.remaining_capacity} chỗ)` : '';
            html += `<button type="button" class="rfb-slot-btn" onclick="selectSlot('${slot.start}','${slot.end}','${slot.label}', ${rem}, this)">${slot.label}${remText}</button>`;
        } else {
            html += `<button type="button" class="rfb-slot-btn booked" disabled>${slot.label}</button>`;
        }
    });
    html += '</div>';
    html += '<div style="display:flex;gap:16px;margin-top:12px;font-size:0.75rem;color:#64748b"><span style="display:flex;align-items:center;gap:4px"><span style="width:12px;height:12px;border-radius:3px;background:#eff6ff;border:1.5px solid #3b82f6;display:inline-block"></span>Còn trống</span><span style="display:flex;align-items:center;gap:4px"><span style="width:12px;height:12px;border-radius:3px;background:#f8fafc;border:1.5px solid #e2e8f0;display:inline-block"></span>Đã đặt</span></div>';
    container.innerHTML = html;
}

function renderSlotsFallback(allSlots) {
    const container = document.getElementById('slots-container');
    if (allSlots.length === 0) {
        container.innerHTML = '<p style="color:#94a3b8;font-size:0.85rem">Tiện ích chưa cài đặt khung giờ.</p>';
        return;
    }
    let html = '<div class="rfb-slots-grid">';
    allSlots.forEach(slot => {
        html += `<button type="button" class="rfb-slot-btn" onclick="selectSlot('${slot.start}','${slot.end}','${slot.label}', {{ $facility->capacity }}, this)">${slot.label}</button>`;
    });
    html += '</div>';
    container.innerHTML = html;
}

function selectSlot(start, end, label, remainingCapacity, btn) {
    document.querySelectorAll('.rfb-slot-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    selectedStart = start;
    selectedEnd = end;
    document.getElementById('start_time').value = start;
    document.getElementById('end_time').value = end;

    const display = document.getElementById('selected-slot-display');
    display.style.display = 'flex';
    document.getElementById('slot-label-display').textContent = label;

    // Cập nhật số người tối đa cho input số người
    const input = document.getElementById('number_of_people');
    if (input) {
        input.setAttribute('max', remainingCapacity);
        if (parseInt(input.value) > remainingCapacity) {
            input.value = remainingCapacity;
        }
        // Cập nhật text giới hạn
        const hint = document.querySelector('.rfb-people-max');
        if (hint) {
            hint.textContent = `tối đa ${remainingCapacity} người (khung giờ này)`;
        }
    }

    updatePrice(start, end);
}

function updatePrice(start, end) {
    if (!pricePerSlot || pricePerSlot <= 0) return;
    let slots = 1;
    if (slotDuration > 0) {
        const s = start.split(':');
        const e = end.split(':');
        const startMin = parseInt(s[0]) * 60 + parseInt(s[1]);
        const endMin   = parseInt(e[0]) * 60 + parseInt(e[1]);
        const minutes  = endMin - startMin;
        slots    = Math.ceil(minutes / slotDuration);
    }
    const people   = Math.max(1, parseInt(document.getElementById('number_of_people')?.value || 1));
    const total    = slots * pricePerSlot * people;

    // Hiển thị công thức
    const formulaRow = document.getElementById('price-row-people');
    const formulaSpan = document.getElementById('price-formula');
    if (formulaRow && formulaSpan) {
        formulaRow.style.display = 'flex';
        if (slotDuration === 0) {
            formulaSpan.textContent = people + ' người × 1 lượt = ' + people + ' đơn vị';
        } else {
            formulaSpan.textContent = people + ' người × ' + slots + ' slot = ' + (people * slots) + ' đơn vị';
        }
    }

    document.getElementById('total-price').textContent = total.toLocaleString('vi-VN') + 'đ';
}

function changePeople(delta) {
    const input = document.getElementById('number_of_people');
    const max = parseInt(input.getAttribute('max'));
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
    // Cập nhật lại giá khi đổi số người
    if (selectedStart && selectedEnd) {
        updatePrice(selectedStart, selectedEnd);
    }
}

// Lắng nghe thay đổi trực tiếp vào ô số người
document.getElementById('number_of_people')?.addEventListener('input', function() {
    if (selectedStart && selectedEnd) {
        updatePrice(selectedStart, selectedEnd);
    }
});

// Pre-load slots if date already selected (old input)
document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('booking_date');
    if (dateInput.value) {
        loadSlots(dateInput.value);
    }
});
</script>
@endsection
