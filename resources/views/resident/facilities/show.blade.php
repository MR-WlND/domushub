@extends('layouts.resident.master')

@push('styles')
<style>
.resident-content { padding: 0 !important; }
.fb-page { max-width:1440px; margin:0 auto; padding: 30px 40px 60px; box-sizing: border-box; width: 100%; font-family: 'Inter', sans-serif; background-color: #f8fafc; min-height: 100vh; }
@media (max-width: 768px) { 
    .fb-page { padding: 0 0 40px; background-color: #f8fafc; } 
    .fb-title-wrap { padding: 16px 16px 0; }
}
.fb-back { font-size:13px; color:#64748b; text-decoration:none; display:inline-flex; align-items:center; gap:6px; margin-bottom:16px; font-weight:500; } .fb-back:hover { color:#1e3a8a; }
.fb-title { font-size:26px; font-weight:800; color:#1e3a8a; margin:0 0 24px; }
@media (max-width: 768px) { .fb-title { font-size:20px; margin-bottom: 16px; } }

.fb-layout { display:grid; grid-template-columns:1fr 400px; gap:30px; align-items:start; }
@media(max-width:992px) { .fb-layout { grid-template-columns:1fr; } }
@media (max-width: 768px) { .fb-layout { gap: 16px; padding: 0 16px; } }

/* Left */
.fb-info { display: flex; flex-direction: column; gap: 24px; }
@media (max-width: 768px) { .fb-info { gap: 16px; margin-top: -30px; position: relative; z-index: 10; } }

.fb-img { width:100%; height:400px; object-fit:cover; border-radius:12px; display:block; }
@media(max-width:768px) { .fb-img { height:260px; border-radius: 0; width: calc(100% + 32px); margin-left: -16px; margin-right: -16px; } }
.fb-img-none { width:100%; height:400px; background:#e2e8f0; border-radius:12px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:14px; }
@media(max-width:768px) { .fb-img-none { height:260px; border-radius: 0; width: calc(100% + 32px); margin-left: -16px; margin-right: -16px; } }

.fb-details-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; }
@media (max-width: 768px) { .fb-details-box { padding: 20px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); } }
.fb-mobile-title { display: none; font-size: 16px; font-weight: 700; color: #1e3a8a; margin: 0 0 16px; }
@media (max-width: 768px) { .fb-mobile-title { display: block; } }

.fb-details-row { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 16px; }
.fb-details-row:last-child { margin-bottom: 0; }
@media(max-width:768px) { .fb-details-row { display: flex; flex-direction: column; gap: 16px; margin-bottom: 16px; } }

.fb-detail-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; }
.fb-detail-item--full { grid-column: 1 / -1; }
.fb-detail-item:last-child { border-bottom: none; padding-bottom: 0; }
@media (max-width: 768px) { .fb-detail-item { border: none; padding: 0; justify-content: flex-start; align-items: center; } }

.fb-detail-icon { display: none; width: 32px; height: 32px; border-radius: 50%; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; }
.fb-detail-icon svg { width: 18px; height: 18px; }
.icon-green { background: #ecfdf5; color: #10b981; }
.icon-blue { background: #eff6ff; color: #3b82f6; }
@media (max-width: 768px) { .fb-detail-icon { display: flex; } }

.fb-detail-text { display: flex; justify-content: space-between; width: 100%; align-items: center; }
@media (max-width: 768px) { .fb-detail-text { flex-direction: column; align-items: flex-start; gap: 2px; } }

.fb-details__label { color:#64748b; font-size:14px; }
@media (max-width: 768px) { .fb-details__label { font-size: 12px; font-weight: 500; } }
.fb-details__value { color:#0f172a; font-weight:600; font-size:14px; }
@media (max-width: 768px) { .fb-details__value { font-size: 14px; color: #1e3a8a; } .fb-details__value--green { color: #10b981; } }

.fb-status-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; margin-right: 6px; }
@media (max-width: 768px) { .fb-status-dot { display: none; } }

.fb-desc { font-size:14px; color:#475569; line-height:1.6; }

.fb-rules { font-size:14px; color:#475569; line-height:1.7; background:#f8fafc; padding:24px; border-radius:12px; border: 1px solid #e2e8f0; }
@media (max-width: 768px) { .fb-rules { background: #ffffff; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); padding: 20px; } }
.fb-rules-title { font-size:16px; font-weight:700; color:#1e3a8a; margin-bottom:12px; }
@media (max-width: 768px) { .fb-rules-title { margin-bottom: 8px; } }
.fb-rules ul { margin: 0; padding-left: 20px; }
.fb-rules li { margin-bottom: 8px; }
.fb-rules li:last-child { margin-bottom: 0; }

/* Right: Form */
.fb-form-card { background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; position:sticky; top:24px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
@media (max-width: 768px) { .fb-form-card { padding: 20px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); position: static; } }
.fb-form-title { font-size:18px; font-weight:800; color:#1e3a8a; margin:0 0 24px; }
.fb-field { margin-bottom:20px; }
.fb-field:last-child { margin-bottom:0; }
.fb-label { display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:8px; }
.fb-input { width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:8px; font-size:14px; color:#0f172a; box-sizing:border-box; transition: all 0.2s; font-family: 'Inter', sans-serif; }
.fb-input:focus { outline:none; border-color:#1e3a8a; box-shadow:0 0 0 3px rgba(30,58,138,.1); }
textarea.fb-input { resize:vertical; min-height:80px; }

/* Slots */
.fb-slots { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; }
.fb-slot { padding:10px 4px; text-align:center; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; color:#64748b; cursor:pointer; transition:all 0.2s; user-select:none; }
.fb-slot:hover { border-color:#1e3a8a; color:#1e3a8a; }
.fb-slot.active { background:#eff6ff; border-color:#3b82f6; color:#1d4ed8; font-weight: 600; }
.fb-slot.disabled { background:#f8fafc; color:#cbd5e1; cursor:not-allowed; border-color:#f1f5f9; }

@media (max-width: 768px) {
    .fb-slot { background: #eff6ff; color: #1e3a8a; border: none; font-weight: 500; border-radius: 6px; padding: 8px 4px; }
    .fb-slot.active { background: #1e3a8a; color: #ffffff; }
}

/* People */
.fb-people { display:flex; align-items:center; gap:16px; }
.fb-people-btn { width:32px; height:32px; border:1px solid #e2e8f0; border-radius:50%; background:#fff; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b; transition:.15s; }
.fb-people-btn:hover { border-color:#1e3a8a; color:#1e3a8a; }
.fb-people-val { font-size:16px; font-weight:600; color:#0f172a; width:24px; text-align:center; }

/* Price */
.fb-price { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:#f8fafc; border-radius:8px; margin-bottom:20px; font-size:14px; border: 1px solid #e2e8f0; }
.fb-price__label { color:#475569; font-weight: 500; }
.fb-price__val { font-size:18px; font-weight:700; color:#1e3a8a; }

/* Submit */
.fb-submit { width:100%; padding:14px; background:#1e3a8a; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:.2s; }
.fb-submit:hover { background:#1e40af; }
.fb-submit:disabled { background:#cbd5e1; color:#fff; cursor:not-allowed; }

/* Alert */
.fb-alert { padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:20px; }
.fb-alert--error { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
.fb-alert--success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
</style>
@endpush

@section('title', $facility->name . ' – DomusHub')

@section('content')
<div class="fb-page">

    <div class="fb-title-wrap">
        <a href="{{ route('resident.facilities.index') }}" class="fb-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Tiện ích
        </a>
        <h1 class="fb-title">{{ $facility->name }}</h1>
    </div>

    <div class="fb-layout">
        {{-- LEFT: Info --}}
        <div class="fb-info">
            @if($facility->images && count($facility->images) > 0)
                <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}" class="fb-img">
            @else
                <div class="fb-img-none">Chưa có ảnh</div>
            @endif

            <div class="fb-details-box">
                <h3 class="fb-mobile-title">Chi tiết</h3>
                <div class="fb-details-row">
                    <div class="fb-detail-item fb-detail-item--full">
                        <div class="fb-detail-icon icon-green">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                        </div>
                        <div class="fb-detail-text">
                            <span class="fb-details__label">Trạng thái</span>
                            <span class="fb-details__value fb-details__value--green">
                                <span class="fb-status-dot"></span>{{ $facility->status == 'available' ? 'Đang hoạt động' : ($facility->status == 'maintenance' ? 'Bảo trì' : 'Đóng cửa') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="fb-details-row">
                    <div class="fb-detail-item">
                        <div class="fb-detail-icon icon-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div class="fb-detail-text">
                            <span class="fb-details__label">Giờ hoạt động</span>
                            <span class="fb-details__value">{{ $facility->operating_hours ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="fb-detail-item">
                        <div class="fb-detail-icon icon-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                        </div>
                        <div class="fb-detail-text">
                            <span class="fb-details__label">Sức chứa</span>
                            <span class="fb-details__value">Tối đa {{ $facility->capacity }} người</span>
                        </div>
                    </div>
                </div>

                <div class="fb-details-row" style="margin-bottom: 0;">
                    <div class="fb-detail-item">
                        <div class="fb-detail-icon icon-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </div>
                        <div class="fb-detail-text">
                            <span class="fb-details__label">Phí sử dụng</span>
                            <span class="fb-details__value">{{ $facility->price_label ?: 'Miễn phí' }}</span>
                        </div>
                    </div>
                    <div class="fb-detail-item">
                        <div class="fb-detail-icon icon-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <div class="fb-detail-text">
                            <span class="fb-details__label">Vị trí</span>
                            <span class="fb-details__value">{{ $facility->block?->name ?: '—' }}{{ $facility->floor ? ', '.$facility->floor->name : '' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($facility->description)
            <p class="fb-desc">{{ $facility->description }}</p>
            @endif

            @if($facility->rules)
            <div class="fb-rules">
                <div class="fb-rules-title">Quy định sử dụng</div>
                <div style="white-space: pre-line;">{{ $facility->rules }}</div>
            </div>
            @endif
        </div>

        {{-- RIGHT: Booking Form --}}
        <div>
            <div class="fb-form-card">
                <h2 class="fb-form-title">Đặt lịch sử dụng</h2>

                @if($errors->any())
                <div class="fb-alert fb-alert--error"><ul style="margin:0;padding-left:14px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                @if(session('error'))<div class="fb-alert fb-alert--error">{{ session('error') }}</div>@endif
                @if(session('success'))<div class="fb-alert fb-alert--success">{{ session('success') }}</div>@endif

                @php
                    $isOpen = $facility->status === 'available';
                    $bookingType = $facility->booking_type ?? 'time_slot';
                    $feeType = $facility->fee_type ?? 'free';
                    $price = $facility->price ?? 0;
                    $hasFee = $feeType !== 'free' && $price > 0;
                    $slots = $facility->getTimeSlots();
                @endphp

                <form method="POST" action="{{ route('resident.facilities.book.store', $facility) }}">
                    @csrf

                    <div class="fb-field">
                        <label class="fb-label">Ngày</label>
                        <input type="date" name="booking_date" class="fb-input" value="{{ old('booking_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required {{ !$isOpen?'disabled':'' }}>
                    </div>

                    @if(in_array($bookingType, ['time_slot', 'slot']))
                    <div class="fb-field">
                        <label class="fb-label">Khung giờ</label>
                        <input type="hidden" name="start_time" id="start_time" value="">
                        <input type="hidden" name="end_time" id="end_time" value="">
                        <div class="fb-slots">
                            @foreach($slots as $s)
                            <div class="fb-slot" onclick="selectSlot(this,'{{ $s['start'] }}','{{ $s['end'] }}')">{{ $s['label'] }}</div>
                            @endforeach
                        </div>
                    </div>
                    @elseif($bookingType === 'none' && $feeType === 'per_hour')
                    {{-- Không chọn slot nhưng phí theo giờ → hỏi số giờ --}}
                    <input type="hidden" name="start_time" value="{{ $facility->open_time ? substr($facility->open_time,0,5) : '06:00' }}">
                    <input type="hidden" name="end_time" id="end_time_calc" value="{{ $facility->close_time ? substr($facility->close_time,0,5) : '22:00' }}">
                    <div class="fb-field">
                        <label class="fb-label">Số giờ sử dụng</label>
                        <div class="fb-people">
                            <button type="button" class="fb-people-btn" onclick="changeHours(-1)">−</button>
                            <span class="fb-people-val" id="hDisplay">1</span>
                            <input type="hidden" id="hInput" value="1">
                            <button type="button" class="fb-people-btn" onclick="changeHours(1)">+</button>
                        </div>
                    </div>
                    @else
                    <input type="hidden" name="start_time" value="{{ $facility->open_time ? substr($facility->open_time,0,5) : '00:00' }}">
                    <input type="hidden" name="end_time" value="{{ $facility->close_time ? substr($facility->close_time,0,5) : '23:59' }}">
                    @endif

                    <div class="fb-field">
                        <label class="fb-label">Số người</label>
                        <div class="fb-people">
                            <button type="button" class="fb-people-btn" onclick="changePeople(-1)" {{ !$isOpen?'disabled':'' }}>−</button>
                            <span class="fb-people-val" id="pDisplay">{{ old('number_of_people', 2) }}</span>
                            <input type="hidden" name="number_of_people" id="pInput" value="{{ old('number_of_people', 2) }}">
                            <button type="button" class="fb-people-btn" onclick="changePeople(1)" {{ !$isOpen?'disabled':'' }}>+</button>
                        </div>
                    </div>

                    <div class="fb-field">
                        <label class="fb-label">Ghi chú</label>
                        <textarea name="note" class="fb-input" placeholder="Tùy chọn..." {{ !$isOpen?'disabled':'' }}>{{ old('note') }}</textarea>
                    </div>

                    @if($hasFee)
                    <div class="fb-price">
                        <span class="fb-price__label">Tạm tính:</span>
                        <span class="fb-price__val" id="totalPrice">0đ</span>
                    </div>
                    @endif

                    <button type="submit" class="fb-submit" {{ !$isOpen?'disabled':'' }}>Xác nhận đặt lịch</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const maxCap = {{ $facility->capacity ?? 10 }};
const feeType = '{{ $feeType }}';
const price = {{ $price }};
const openTime = '{{ $facility->open_time ? substr($facility->open_time,0,5) : "06:00" }}';

function selectSlot(el, start, end) {
    document.querySelectorAll('.fb-slot').forEach(s => s.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('start_time').value = start;
    document.getElementById('end_time').value = end;
    calcPrice();
}

function changePeople(d) {
    const input = document.getElementById('pInput');
    const display = document.getElementById('pDisplay');
    let v = parseInt(input.value) || 1;
    v += d;
    if (v < 1) v = 1;
    if (v > maxCap) v = maxCap;
    input.value = v;
    display.textContent = v;
    calcPrice();
}

let hours = 1;
function changeHours(d) {
    hours += d;
    if (hours < 1) hours = 1;
    if (hours > 8) hours = 8;
    const display = document.getElementById('hDisplay');
    const input = document.getElementById('hInput');
    if (display) display.textContent = hours;
    if (input) input.value = hours;
    // Update end_time based on hours
    const endEl = document.getElementById('end_time_calc');
    if (endEl) {
        const [h, m] = openTime.split(':').map(Number);
        const endH = Math.min(h + hours, 22);
        endEl.value = String(endH).padStart(2,'0') + ':' + String(m).padStart(2,'0');
    }
    calcPrice();
}

function calcPrice() {
    if (feeType === 'free' || price <= 0) return;
    const people = parseInt(document.getElementById('pInput')?.value) || 1;
    let total = 0;
    if (feeType === 'per_person') total = price * people;
    else if (feeType === 'per_hour') total = price * (hours || 1);
    else total = price;
    const el = document.getElementById('totalPrice');
    if (el) el.textContent = total.toLocaleString('vi-VN') + 'đ';
}

document.addEventListener('DOMContentLoaded', calcPrice);
</script>
@endpush
