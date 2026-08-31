@extends('layouts.resident.master')

@push('styles')
<style>
.resident-content { padding: 0 !important; }
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.rfb-page { max-width: 1060px; margin: 0 auto; padding: 30px 40px 60px; font-family: 'Inter', sans-serif; box-sizing: border-box; width: 100%; }
@media (max-width: 768px) { .rfb-page { padding: 20px 16px 40px; } }

.rfb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; margin-bottom: 24px; }
.rfb-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.rfb-breadcrumb a:hover { text-decoration: underline; }

.rfb-layout { display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start; }

.rfb-form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 32px; box-shadow: 0 2px 20px rgba(0,0,0,0.06); }
.rfb-form-header { display: flex; align-items: center; gap: 16px; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9; }
.rfb-form-header-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6, #6366f1); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: #fff; flex-shrink: 0; }
.rfb-form-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 2px; }
.rfb-form-subtitle { font-size: 0.85rem; color: #64748b; margin: 0; }

.rfb-alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 10px; font-size: 0.875rem; margin-bottom: 20px; }
.rfb-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.rfb-alert ul { margin: 0; padding-left: 16px; }
.rfb-alert li { margin-bottom: 2px; }

.rfb-step { margin-bottom: 28px; }
.rfb-step-label { display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 12px; flex-wrap: wrap; }
.rfb-step-num { width: 26px; height: 26px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0; }
.rfb-multi-hint { font-size: 0.72rem; font-weight: 500; color: #7c3aed; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 20px; padding: 3px 12px; }

.rfb-input { width: 100%; padding: 11px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: 0.9rem; color: #0f172a; outline: none; transition: border-color 0.15s, box-shadow 0.15s; box-sizing: border-box; }
.rfb-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }

/* Slots */
.rfb-slots-wrap { min-height: 60px; }
.rfb-slots-hint { font-size: 0.82rem; color: #94a3b8; font-style: italic; }
.rfb-slots-grid { display: flex; flex-wrap: wrap; gap: 10px; }

.rfb-slot-btn {
    padding: 9px 18px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 600;
    color: #475569;
    transition: all 0.12s;
    font-family: monospace;
    user-select: none;
}
.rfb-slot-btn:hover:not(.booked) { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.rfb-slot-btn.selected { border-color: #2563eb; background: #3b82f6; color: #fff; box-shadow: 0 2px 8px rgba(59,130,246,0.35); }
.rfb-slot-btn.range { border-color: #93c5fd; background: #dbeafe; color: #1d4ed8; }
.rfb-slot-btn.booked { border-color: #fecaca; background: #fef2f2; color: #ef4444; cursor: not-allowed; }

.rfb-slots-loading { font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 6px; }
.rfb-slots-loading::before { content: ''; width: 14px; height: 14px; border: 2px solid #e2e8f0; border-top-color: #3b82f6; border-radius: 50%; animation: spin 0.6s linear infinite; display: inline-block; }
@keyframes spin { to { transform: rotate(360deg); } }

.rfb-legend { display: flex; gap: 16px; margin-top: 12px; font-size: 0.75rem; color: #64748b; flex-wrap: wrap; }
.rfb-legend-item { display: flex; align-items: center; gap: 5px; }
.rfb-legend-dot { width: 12px; height: 12px; border-radius: 3px; display: inline-block; }

.rfb-selected-slot { display: flex; align-items: center; gap: 8px; margin-top: 12px; padding: 10px 14px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; font-size: 0.85rem; color: #2563eb; font-weight: 500; flex-wrap: wrap; }
.rfb-slot-count { background: #2563eb; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 2px 9px; border-radius: 20px; }

/* People */
.rfb-people-wrap { display: flex; align-items: center; gap: 12px; }
.rfb-people-btn { width: 36px; height: 36px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #f8fafc; font-size: 1.2rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #475569; transition: all 0.15s; }
.rfb-people-btn:hover { border-color: #3b82f6; color: #2563eb; background: #eff6ff; }
.rfb-people-input { width: 70px; text-align: center; padding: 8px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 1rem; font-weight: 700; color: #0f172a; outline: none; }
.rfb-people-input:focus { border-color: #3b82f6; }
.rfb-people-max { font-size: 0.78rem; color: #94a3b8; }

/* Price preview */
.rfb-price-preview { background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%); border: 1px solid #bae6fd; border-radius: 14px; padding: 18px 20px; margin-bottom: 24px; }
.rfb-price-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #64748b; margin-bottom: 8px; }
.rfb-price-row:last-child { margin-bottom: 0; }
.rfb-price-unit { font-weight: 700; color: #374151; }
.rfb-price-total { font-size: 1.05rem; font-weight: 800; color: #0f172a; padding-top: 12px; border-top: 1.5px dashed #bae6fd; margin-top: 6px; }
.rfb-price-total span:last-child { color: #2563eb; font-size: 1.15rem; }

.rfb-free-notice { display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; font-size: 0.875rem; color: #15803d; margin-bottom: 24px; }

.rfb-submit { width: 100%; padding: 14px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 14px rgba(59,130,246,0.35); }
.rfb-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,130,246,0.45); }
.rfb-submit:active { transform: translateY(0); }
.rfb-note { font-size: 0.75rem; color: #94a3b8; text-align: center; margin-top: 10px; }
.rfb-note--fee { color: #0369a1; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 8px 12px; font-size: 0.82rem; }

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
@endpush

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
                        <input type="date" name="booking_date" id="booking_date" class="rfb-input"
                            value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}"
                            required onchange="loadSlots(this.value)">
                    </div>

                    {{-- Bước 2: Chọn khung giờ (chỉ hiện khi đặt theo giờ) --}}
                    @if(in_array($facility->booking_type ?? 'time_slot', ['time_slot', 'slot']))
                    <div class="rfb-step">
                        <div class="rfb-step-label">
                            <span class="rfb-step-num">2</span>
                            Chọn khung giờ
                        </div>
                        <div id="slots-container" class="rfb-slots-wrap">
                            <p class="rfb-slots-hint">← Vui lòng chọn ngày trước</p>
                        </div>
                        <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
                        <input type="hidden" name="end_time"   id="end_time"   value="{{ old('end_time') }}">
                        <div id="selected-slot-display" class="rfb-selected-slot" style="display:none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Đã chọn: <strong id="slot-label-display"></strong>
                            <span id="slot-count-badge" class="rfb-slot-count"></span>
                        </div>
                    </div>
                    @else
                    {{-- Đặt theo người: tự điền giờ mở/đóng cửa --}}
                    <input type="hidden" name="start_time" id="start_time" value="{{ $facility->open_time ? substr($facility->open_time, 0, 5) : '00:00' }}">
                    <input type="hidden" name="end_time"   id="end_time"   value="{{ $facility->close_time ? substr($facility->close_time, 0, 5) : '23:59' }}">
                    @endif

                    {{-- Bước {{ in_array($facility->booking_type ?? 'time_slot', ['time_slot', 'slot']) ? '3' : '2' }}: Số người --}}
                    <div class="rfb-step">
                        <div class="rfb-step-label">
                            <span class="rfb-step-num">{{ in_array($facility->booking_type ?? 'time_slot', ['time_slot', 'slot']) ? '3' : '2' }}</span>
                            Số người sử dụng
                        </div>
                        <div class="rfb-people-wrap">
                            <button type="button" class="rfb-people-btn" onclick="changePeople(-1)">−</button>
                            <input type="number" name="number_of_people" id="number_of_people"
                                class="rfb-people-input"
                                value="{{ old('number_of_people', 1) }}"
                                min="1" max="{{ $facility->capacity }}" required>
                            <button type="button" class="rfb-people-btn" onclick="changePeople(1)">+</button>
                            <span class="rfb-people-max" id="people-max-hint">tối đa {{ $facility->capacity }} người</span>
                        </div>
                    </div>

                    {{-- Tổng tiền preview --}}
                    @php
                        $hasFee = $facility->fee_type !== 'free' && $facility->price > 0;
                        $feeType = $facility->fee_type;
                    @endphp
                    @if($hasFee)
                    <div class="rfb-price-preview" id="price-preview">
                        <div class="rfb-price-row">
                            @if($feeType === 'per_person')
                                <span>Giá mỗi người (vé / lượt)</span>
                                <span class="rfb-price-unit">{{ number_format($facility->price) }}đ</span>
                            @elseif($feeType === 'per_use')
                                <span>Giá mỗi lượt</span>
                                <span class="rfb-price-unit">{{ number_format($facility->price) }}đ</span>
                            @else
                                <span>Giá mỗi giờ</span>
                                <span class="rfb-price-unit">{{ number_format($facility->price) }}đ</span>
                            @endif
                        </div>
                        <div class="rfb-price-row" id="price-row-slots" style="display:none">
                            <span id="price-formula-label">Số slot đã chọn</span>
                            <span id="price-slots-val">—</span>
                        </div>
                        <div class="rfb-price-row" id="price-row-people" style="display:none">
                            <span>Số người</span>
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
                    <button type="button" class="rfb-submit" id="submitBtn" onclick="validateAndSubmit()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Xác nhận đặt lịch
                    </button>
                    <div id="submit-error" style="display:none;margin-top:10px;padding:10px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;font-size:0.85rem;color:#b91c1c;"></div>
                    @if($hasFee)
                    <p class="rfb-note rfb-note--fee">
                        💳 Sau khi đặt lịch, bạn sẽ được chuyển đến <strong>trang thanh toán hóa đơn</strong> để hoàn tất.
                    </p>
                    @else
                    <p class="rfb-note">Lịch đặt được duyệt tự động. Bạn có thể xem lịch đặt trong mục <strong>Lịch đặt của tôi</strong>.</p>
                    @endif
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
                        <p class="rfb-info-label">Thời lượng mỗi slot</p>
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
                        @php
                            $isPaid = $facility->fee_type !== 'free' && $facility->price > 0;
                        @endphp
                        <p class="rfb-info-value {{ !$isPaid ? 'rfb-free' : 'rfb-paid' }}">
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

            <div class="rfb-available-notice">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>Tiện ích đang <strong>mở cửa</strong></span>
            </div>
        </aside>
    </div>
</div>

@endsection

@push('scripts')
<script>
const bookingType    = '{{ $facility->booking_type ?? 'time_slot' }}';
const feeType        = '{{ $facility->fee_type ?? 'free' }}';
const price          = {{ $facility->price ?? 0 }};
const slotDuration   = {{ $facility->slot_duration ?? 60 }};
const facilityId     = {{ $facility->id }};
const maxCapacity    = {{ $facility->capacity }};

// ─── Multi-slot selection state ─────────────────────
let allSlotsData    = [];   // [{start,end,label,available,remainingCapacity}]
let selectedIndices = [];   // sorted indices of selected slots
let selectionAnchor = -1;   // index of first clicked slot

// ─── Load slots from API ─────────────────────────────
async function loadSlots(date) {
    if (!date) return;
    resetSelection();
    const container = document.getElementById('slots-container');
    container.innerHTML = '<div class="rfb-slots-loading">Đang tải khung giờ...</div>';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const res = await fetch('/resident/api/available-slots', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ facility_id: facilityId, booking_date: date }),
        });
        const json = await res.json();
        buildSlotsData(json.success ? json.available_slots : []);
    } catch (e) {
        console.error("Lỗi khi tải slots:", e);
        buildSlotsData([]);
    }
    renderSlots();
}

function buildSlotsData(serverAvailable) {
    const rawSlots = @json($slots);
    allSlotsData = rawSlots.map(slot => {
        const matched = serverAvailable.find(s => s.start === slot.start);
        return {
            start:             slot.start,
            end:               slot.end,
            label:             slot.label,
            available:         !!matched,
            remainingCapacity: matched ? (matched.remaining_capacity ?? maxCapacity) : 0,
        };
    });
}

function renderSlots() {
    const container = document.getElementById('slots-container');

    if (allSlotsData.length === 0) {
        container.innerHTML = '<p style="color:#94a3b8;font-size:0.85rem">Tiện ích chưa cài đặt khung giờ hoặc không còn khung giờ trống cho ngày này.</p>';
        return;
    }

    let html = '<div class="rfb-slots-grid" id="slots-grid">';
    allSlotsData.forEach((slot, idx) => {
        if (slot.available) {
            const remText = slot.remainingCapacity < maxCapacity
                ? ` <span style="font-size:0.68rem;opacity:0.75">(còn ${slot.remainingCapacity})</span>` : '';
            html += `<button type="button" class="rfb-slot-btn" data-idx="${idx}"
                onclick="handleSlotClick(${idx}, this)">${slot.label}${remText}</button>`;
        } else {
            html += `<button type="button" class="rfb-slot-btn booked" disabled title="Đã đặt đầy">${slot.label}</button>`;
        }
    });
    html += '</div>';

    html += `<div class="rfb-legend">
        <div class="rfb-legend-item">
            <span class="rfb-legend-dot" style="background:#3b82f6;border:1.5px solid #2563eb"></span>Đầu/cuối đã chọn
        </div>
        <div class="rfb-legend-item">
            <span class="rfb-legend-dot" style="background:#dbeafe;border:1.5px solid #93c5fd"></span>Trong khoảng
        </div>
        <div class="rfb-legend-item">
            <span class="rfb-legend-dot" style="background:#fef2f2;border:1.5px solid #fecaca"></span>Đã đặt (Khóa)
        </div>
    </div>`;

    container.innerHTML = html;
}

// ─── Click handler: range selection ─────────────────
function handleSlotClick(idx) {
    // First click or reset anchor
    if (selectionAnchor === -1) {
        selectionAnchor = idx;
        selectedIndices = [idx];
    } else if (idx === selectionAnchor && selectedIndices.length === 1) {
        // Toggle off
        resetSelection();
        updateFormAndPrice();
        return;
    } else {
        // Build range from anchor to idx
        const from = Math.min(selectionAnchor, idx);
        const to   = Math.max(selectionAnchor, idx);

        // Block if any booked slot in range
        let blocked = false;
        for (let i = from; i <= to; i++) {
            if (!allSlotsData[i]?.available) { blocked = true; break; }
        }

        if (blocked) {
            // Start fresh from this idx
            selectionAnchor = idx;
            selectedIndices = [idx];
        } else {
            selectedIndices = [];
            for (let i = from; i <= to; i++) selectedIndices.push(i);
            // Keep anchor at earliest
            selectionAnchor = from;
        }
    }

    highlightSlots();
    updateFormAndPrice();
}

function highlightSlots() {
    document.querySelectorAll('.rfb-slot-btn:not(.booked)').forEach(btn => {
        const i = parseInt(btn.dataset.idx);
        btn.classList.remove('selected', 'range');
        if (selectedIndices.includes(i)) {
            const isEndpoint = (i === Math.min(...selectedIndices) || i === Math.max(...selectedIndices));
            btn.classList.add(isEndpoint ? 'selected' : 'range');
        }
    });
}

function resetSelection() {
    selectionAnchor = -1;
    selectedIndices = [];
    document.querySelectorAll('.rfb-slot-btn').forEach(b => b.classList.remove('selected', 'range'));
    document.getElementById('start_time').value = '';
    document.getElementById('end_time').value   = '';
    document.getElementById('selected-slot-display').style.display = 'none';
}

function updateFormAndPrice() {
    if (selectedIndices.length === 0) {
        document.getElementById('start_time').value = '';
        document.getElementById('end_time').value   = '';
        document.getElementById('selected-slot-display').style.display = 'none';
        const tp = document.getElementById('total-price');
        if (tp) tp.textContent = 'Chọn giờ và số người';
        return;
    }

    const sorted   = [...selectedIndices].sort((a, b) => a - b);
    const first    = allSlotsData[sorted[0]];
    const last     = allSlotsData[sorted[sorted.length - 1]];
    const numSlots = sorted.length;

    document.getElementById('start_time').value = first.start;
    document.getElementById('end_time').value   = last.end;

    // Display label
    const display = document.getElementById('selected-slot-display');
    display.style.display = 'flex';
    document.getElementById('slot-label-display').textContent = numSlots === 1
        ? first.label
        : `${first.start} – ${last.end}`;

    const badge = document.getElementById('slot-count-badge');
    if (badge) badge.textContent = numSlots === 1 ? '1 slot' : `${numSlots} slots`;

    // Capacity = min across selected slots
    const minCap = Math.min(...sorted.map(i => allSlotsData[i].remainingCapacity));
    const peopleInput = document.getElementById('number_of_people');
    if (peopleInput) {
        peopleInput.setAttribute('max', minCap);
        if (parseInt(peopleInput.value) > minCap) peopleInput.value = minCap;
        document.getElementById('people-max-hint').textContent = `tối đa ${minCap} người (khung giờ này)`;
    }

    updatePrice(numSlots);
}

function updatePrice(numSlots) {
    const people = Math.max(1, parseInt(document.getElementById('number_of_people')?.value || 1));

    if (!price || price <= 0 || feeType === 'free') return;

    if (feeType === 'per_person') {
        const total = price * people;

        const rowSlots = document.getElementById('price-row-slots');
        if (rowSlots) rowSlots.style.display = 'none';

        const rowPeople = document.getElementById('price-row-people');
        const fmla      = document.getElementById('price-formula');
        if (rowPeople && fmla) {
            rowPeople.style.display = 'flex';
            fmla.textContent = people + ' người × ' + Number(price).toLocaleString('vi-VN') + 'đ';
        }

        document.getElementById('total-price').textContent = total.toLocaleString('vi-VN') + 'đ';
    } else {
        let total = 0;
        let formulaText = '';
        
        if (feeType === 'per_use') {
            total = price;
            formulaText = '1 lượt × ' + Number(price).toLocaleString('vi-VN') + 'đ';
        } else {
            // per_hour
            let hours = (numSlots * slotDuration) / 60;
            if (hours === 0 || isNaN(hours)) hours = 1;
            total = hours * price;
            formulaText = hours + ' giờ × ' + Number(price).toLocaleString('vi-VN') + 'đ';
        }

        const rowSlots = document.getElementById('price-row-slots');
        const valSlots = document.getElementById('price-slots-val');
        if (rowSlots && valSlots) {
            rowSlots.style.display = 'flex';
            valSlots.textContent = formulaText + ' = ' + total.toLocaleString('vi-VN') + 'đ';
        }

        const rowPeople = document.getElementById('price-row-people');
        if (rowPeople) rowPeople.style.display = 'none';

        document.getElementById('total-price').textContent = total.toLocaleString('vi-VN') + 'đ';
    }
}

function changePeople(delta) {
    const input = document.getElementById('number_of_people');
    const max   = parseInt(input.getAttribute('max')) || maxCapacity;
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
    // Cập nhật giá nếu có
    if (feeType === 'per_person' || selectedIndices.length > 0) updatePrice(selectedIndices.length);
}

document.getElementById('number_of_people')?.addEventListener('input', function() {
    if (feeType === 'per_person' || selectedIndices.length > 0) updatePrice(selectedIndices.length);
});

function validateAndSubmit() {
    const errBox  = document.getElementById('submit-error');
    const dateVal = document.getElementById('booking_date').value;

    errBox.style.display = 'none';
    errBox.textContent   = '';

    if (!dateVal) {
        errBox.textContent   = '⚠️ Vui lòng chọn ngày sử dụng.';
        errBox.style.display = 'block';
        document.getElementById('booking_date').focus();
        errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    // Chỉ kiểm tra khung giờ khi đặt theo khung giờ
    if (['time_slot', 'slot'].includes(bookingType)) {
        const startVal = document.getElementById('start_time').value;
        const endVal   = document.getElementById('end_time').value;
        if (!startVal || !endVal) {
            errBox.textContent   = '⚠️ Vui lòng chọn khung giờ trước khi đặt lịch.';
            errBox.style.display = 'block';
            document.getElementById('slots-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
    }

    // Tất cả hợp lệ → submit
    document.getElementById('bookingForm').submit();
}

document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.getElementById('booking_date');
    if (dateInput && dateInput.value) loadSlots(dateInput.value);
    // Nếu phí theo người: hiển thị giá ngay khi load
    if (feeType === 'per_person' || feeType === 'per_use') updatePrice(0);
});
</script>
@endpush
