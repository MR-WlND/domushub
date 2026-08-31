@extends('layouts.resident.master')

@push('styles')
<style>
.resident-content { padding: 0 !important; }
.fb-page { max-width:1440px; margin:0 auto; padding: 30px 40px 60px; box-sizing: border-box; width: 100%; }
@media (max-width: 768px) { .fb-page { padding: 20px 16px 40px; } }
.fb-back { font-size:12px; color:#64748b; text-decoration:none; display:inline-block; margin-bottom:14px; } .fb-back:hover { color:#0b57d0; }
.fb-title { font-size:18px; font-weight:700; color:#0f172a; margin:0 0 20px; }

.fb-layout { display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start; }
@media(max-width:800px) { .fb-layout { grid-template-columns:1fr; } }

/* Left */
.fb-info { }
.fb-img { width:100%; height:200px; object-fit:cover; border-radius:10px; display:block; margin-bottom:16px; }
.fb-img-none { width:100%; height:200px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:13px; margin-bottom:16px; }
.fb-details { list-style:none; padding:0; margin:0 0 16px; }
.fb-details li { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
.fb-details li:last-child { border-bottom:none; }
.fb-details__label { color:#64748b; }
.fb-details__value { color:#0f172a; font-weight:600; }
.fb-desc { font-size:13px; color:#475569; line-height:1.7; margin-bottom:16px; }
.fb-rules { font-size:12px; color:#64748b; line-height:1.7; background:#f8fafc; padding:12px 14px; border-radius:8px; white-space:pre-line; }
.fb-rules-title { font-size:12px; font-weight:600; color:#334155; margin-bottom:6px; }

/* Right: Form */
.fb-form-card { background:#ffffff; border:1px solid rgba(0,0,0,0.12); border-radius:8px; padding:20px; position:sticky; top:80px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.fb-form-title { font-size:15px; font-weight:700; color:#0f172a; margin:0 0 16px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; }
.fb-field { margin-bottom:14px; }
.fb-field:last-child { margin-bottom:0; }
.fb-label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:5px; }
.fb-input { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; color:#0f172a; box-sizing:border-box; }
.fb-input:focus { outline:none; border-color:#0b57d0; box-shadow:0 0 0 2px rgba(11,87,208,.08); }
textarea.fb-input { resize:vertical; min-height:60px; }

/* Slots */
.fb-slots { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
.fb-slot { padding:7px 4px; text-align:center; border:1px solid #d1d5db; border-radius:5px; font-size:11px; font-weight:600; color:#475569; cursor:pointer; transition:.15s; user-select:none; }
.fb-slot:hover { border-color:#0b57d0; color:#0b57d0; }
.fb-slot.active { background:#0f172a; border-color:#0f172a; color:#fff; }
.fb-slot.disabled { background:#f8fafc; color:#cbd5e1; cursor:not-allowed; }

/* People */
.fb-people { display:flex; align-items:center; gap:12px; }
.fb-people-btn { width:30px; height:30px; border:1px solid #d1d5db; border-radius:50%; background:#fff; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#475569; transition:.15s; }
.fb-people-btn:hover { border-color:#0b57d0; color:#0b57d0; }
.fb-people-val { font-size:16px; font-weight:700; color:#0f172a; width:20px; text-align:center; }

/* Price */
.fb-price { display:flex; justify-content:space-between; align-items:center; padding:10px 12px; background:#f8fafc; border-radius:6px; margin-bottom:14px; font-size:13px; }
.fb-price__label { color:#64748b; }
.fb-price__val { font-size:16px; font-weight:700; color:#0f172a; }

/* Submit */
.fb-submit { width:100%; padding:11px; background:#1e3a8a; color:#fff; border:none; border-radius:7px; font-size:13px; font-weight:600; cursor:pointer; transition:.15s; }
.fb-submit:hover { background:#1e40af; }
.fb-submit:disabled { background:#e2e8f0; color:#94a3b8; cursor:not-allowed; }

/* Alert */
.fb-alert { padding:10px 14px; border-radius:6px; font-size:12px; margin-bottom:14px; }
.fb-alert--error { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
.fb-alert--success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
</style>
@endpush

@section('title', $facility->name . ' – DomusHub')

@section('content')
<div class="fb-page">

    <a href="{{ route('resident.facilities.index') }}" class="fb-back">← Tiện ích</a>
    <h1 class="fb-title">{{ $facility->name }}</h1>

    <div class="fb-layout">
        {{-- LEFT: Info --}}
        <div class="fb-info">
            @if($facility->images && count($facility->images) > 0)
                <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}" class="fb-img">
            @else
                <div class="fb-img-none">Chưa có ảnh</div>
            @endif

            <ul class="fb-details">
                <li><span class="fb-details__label">Trạng thái</span><span class="fb-details__value">{{ $facility->status=='available'?'Đang hoạt động':($facility->status=='maintenance'?'Bảo trì':'Đóng cửa') }}</span></li>
                <li><span class="fb-details__label">Giờ hoạt động</span><span class="fb-details__value">{{ $facility->operating_hours ?: '—' }}</span></li>
                <li><span class="fb-details__label">Sức chứa</span><span class="fb-details__value">{{ $facility->capacity }} người</span></li>
                <li><span class="fb-details__label">Phí</span><span class="fb-details__value">{{ $facility->price_label ?: 'Miễn phí' }}</span></li>
                <li><span class="fb-details__label">Vị trí</span><span class="fb-details__value">{{ $facility->block?->name ?: '—' }}{{ $facility->floor ? ', '.$facility->floor->name : '' }}</span></li>
            </ul>

            @if($facility->description)
            <p class="fb-desc">{{ $facility->description }}</p>
            @endif

            @if($facility->rules)
            <div class="fb-rules">
                <div class="fb-rules-title">Quy định sử dụng</div>
                {{ $facility->rules }}
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
