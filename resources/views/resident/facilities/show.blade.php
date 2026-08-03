@extends('layouts.resident.master')

@push('styles')
<style>
.rfd-page { max-width: 1100px; margin: 0 auto; padding: 28px 20px; font-family: 'Inter', sans-serif; }

.rfd-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: #64748b; margin-bottom: 16px; font-weight: 500; }
.rfd-breadcrumb a { color: #64748b; text-decoration: none; }
.rfd-breadcrumb a:hover { color: #2563eb; }
.rfd-breadcrumb span { color: #2563eb; font-weight: 600; }

.rfd-page-title { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 24px; }

.rfd-layout { display: grid; grid-template-columns: 1.4fr 1fr; gap: 32px; align-items: start; }

/* Left Column */
.rfd-hero { border-radius: 12px; overflow: hidden; margin-bottom: 24px; position: relative; }
.rfd-hero-img { width: 100%; height: 360px; object-fit: cover; display: block; }
.rfd-hero-placeholder { height: 360px; background: linear-gradient(135deg, #e0f2fe, #ede9fe); display: flex; align-items: center; justify-content: center; }
.rfd-badge { position: absolute; bottom: 16px; left: 16px; background: rgba(255, 255, 255, 0.95); padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; color: #1e3a8a; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.rfd-badge svg { color: #3b82f6; }

.rfd-card { background: #fff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
.rfd-card-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }

.rfd-desc { font-size: 15px; color: #475569; line-height: 1.6; margin: 0 0 20px; }

.rfd-meta-grid { display: flex; flex-wrap: wrap; gap: 20px; }
.rfd-meta-item { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #475569; }
.rfd-meta-item svg { color: #1e3a8a; }

.rfd-rules-list { list-style: none; padding: 0; margin: 0; }
.rfd-rules-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 15px; color: #475569; margin-bottom: 12px; line-height: 1.5; }
.rfd-rules-list li svg.check { color: #2563eb; flex-shrink: 0; margin-top: 2px; }
.rfd-rules-list li svg.cross { color: #dc2626; flex-shrink: 0; margin-top: 2px; }

/* Right Column: Form */
.rfd-form-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: sticky; top: 24px; }
.rfd-form-title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; }

.rfd-form-group { margin-bottom: 20px; }
.rfd-form-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 10px; }

.rfd-input { width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; color: #0f172a; outline: none; transition: all 0.2s; background: #fff; box-sizing: border-box; }
.rfd-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.rfd-input:disabled { background: #f8fafc; color: #94a3b8; }

/* Time Slots */
.rfd-slots { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.rfd-slot { padding: 10px; text-align: center; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; color: #475569; background: #fff; cursor: pointer; transition: all 0.2s; user-select: none; }
.rfd-slot:hover { border-color: #94a3b8; }
.rfd-slot.active { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
.rfd-slot.disabled { background: #f1f5f9; color: #cbd5e1; cursor: not-allowed; border-color: #e2e8f0; }

/* People Counter */
.rfd-people-wrap { display: flex; align-items: center; gap: 16px; }
.rfd-people-btn { width: 36px; height: 36px; border: 1px solid #cbd5e1; border-radius: 50%; background: #fff; font-size: 20px; font-weight: 400; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #475569; transition: all 0.15s; }
.rfd-people-btn:hover { border-color: #94a3b8; color: #0f172a; }
.rfd-people-val { font-size: 18px; font-weight: 700; color: #0f172a; width: 24px; text-align: center; }

textarea.rfd-input { resize: vertical; min-height: 80px; }

/* Pricing */
.rfd-price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; font-size: 14px; color: #64748b; }
.rfd-price-val { font-weight: 600; color: #0f172a; }

.rfd-total-box { background: #eff6ff; border-radius: 8px; padding: 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.rfd-total-label { font-size: 16px; font-weight: 700; color: #1e3a8a; }
.rfd-total-val { font-size: 22px; font-weight: 800; color: #1e3a8a; }

.rfd-submit { width: 100%; padding: 16px; background: #059669; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s; }
.rfd-submit:hover { background: #047857; }
.rfd-submit:disabled { background: #94a3b8; cursor: not-allowed; }

.rfd-alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
.rfd-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.rfd-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }

@media (max-width: 900px) {
    .rfd-layout { grid-template-columns: 1fr; }
    .rfd-form-card { position: static; }
    .rfd-slots { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endpush

@section('title', 'Đăng ký sử dụng: ' . $facility->name . ' – DomusHub')

@section('content')
<div class="rfd-page">
    <div class="rfd-breadcrumb">
        <a href="#">Trang chủ</a> &rsaquo;
        <a href="{{ route('resident.facilities.index') }}">Tiện ích</a> &rsaquo;
        <span>{{ $facility->name }}</span>
    </div>

    <h1 class="rfd-page-title">Đăng ký sử dụng: {{ $facility->name }}</h1>

    <div class="rfd-layout">
        {{-- LEFT COLUMN: INFO --}}
        <div>
            <div class="rfd-hero">
                @if($facility->images && count($facility->images) > 0)
                    <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}" class="rfd-hero-img">
                @else
                    <div class="rfd-hero-placeholder">
                        @include('partials.facility-placeholder', ['name' => $facility->name])
                    </div>
                @endif
                <div class="rfd-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 12h20M12 2v20M5.5 5.5l13 13M18.5 5.5l-13 13"/></svg>
                    {{ $facility->facility_type ?: 'Tiện ích' }}
                </div>
            </div>

            <div class="rfd-card">
                <h3 class="rfd-card-title">Giới thiệu tiện ích</h3>
                <p class="rfd-desc">{{ $facility->description ?: 'Chưa có giới thiệu cho tiện ích này.' }}</p>
                
                <div class="rfd-meta-grid">
                    <div class="rfd-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Giờ mở cửa: {{ $facility->operating_hours ?: 'Chưa cập nhật' }}
                    </div>
                    <div class="rfd-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Sức chứa: {{ $facility->capacity ?: 'Không giới hạn' }} người
                    </div>
                    <div class="rfd-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $facility->location ?: 'Chưa cập nhật' }}
                    </div>
                </div>
            </div>

            <div class="rfd-card">
                <h3 class="rfd-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Quy định sử dụng
                </h3>
                <ul class="rfd-rules-list">
                    @if($facility->rules)
                        @foreach(explode("\n", trim($facility->rules)) as $rule)
                            @if(trim($rule))
                                <li>
                                    @if(str_contains(strtolower($rule), 'không') || str_contains(strtolower($rule), 'cấm'))
                                        <svg class="cross" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    @else
                                        <svg class="check" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                                    @endif
                                    <span>{{ trim($rule) }}</span>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li>
                            <svg class="check" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                            <span>Tuân thủ các quy định chung của Ban Quản Lý tòa nhà.</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- RIGHT COLUMN: BOOKING FORM --}}
        <div>
            <div class="rfd-form-card">
                <h2 class="rfd-form-title">Thông tin đăng ký</h2>

                @if($errors->any())
                <div class="rfd-alert rfd-alert--error">
                    <ul style="margin:0; padding-left:16px;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif
                @if(session('error'))
                <div class="rfd-alert rfd-alert--error">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                <div class="rfd-alert rfd-alert--success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('resident.facilities.book.store', $facility) }}" id="bookingForm">
                    @csrf
                    
                    @php
                        $isOpen = $facility->status === 'available';
                        $bookingType = $facility->booking_type ?? 'time_slot';
                        $feeType = $facility->fee_type ?? 'free';
                        $price = $facility->price ?? 0;
                        $hasFee = $feeType !== 'free' && $price > 0;
                        
                        $slots = $facility->getTimeSlots();
                    @endphp

                    <div class="rfd-form-group">
                        <label>Chọn ngày</label>
                        <input type="date" name="booking_date" class="rfd-input" value="{{ old('booking_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required {{ !$isOpen ? 'disabled' : '' }}>
                    </div>

                    @if(in_array($bookingType, ['time_slot', 'slot']))
                        <div class="rfd-form-group">
                            <label>Khung giờ</label>
                            <input type="hidden" name="time_slot" id="selected_slot" value="">
                            <input type="hidden" name="start_time" id="start_time" value="">
                            <input type="hidden" name="end_time" id="end_time" value="">
                            
                            <div class="rfd-slots">
                                @if(empty($slots))
                                    <div style="font-size:13px; color:#94a3b8; grid-column:1/-1;">Chưa cấu hình khung giờ.</div>
                                @endif
                                @foreach($slots as $s)
                                    @php
                                        $timeLabel = $s['label'];
                                    @endphp
                                    <div class="rfd-slot" onclick="selectSlot(this, '{{ $s['start'] }}', '{{ $s['end'] }}')">
                                        {{ $timeLabel }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="start_time" value="{{ $facility->open_time ? substr($facility->open_time, 0, 5) : '00:00' }}">
                        <input type="hidden" name="end_time" value="{{ $facility->close_time ? substr($facility->close_time, 0, 5) : '23:59' }}">
                    @endif

                    <div class="rfd-form-group">
                        <label>Số người tham gia</label>
                        <div class="rfd-people-wrap">
                            <button type="button" class="rfd-people-btn" onclick="changePeople(-1)" {{ !$isOpen ? 'disabled' : '' }}>−</button>
                            <span class="rfd-people-val" id="people_display">{{ old('number_of_people', 2) }}</span>
                            <input type="hidden" name="number_of_people" id="number_of_people" value="{{ old('number_of_people', 2) }}">
                            <button type="button" class="rfd-people-btn" onclick="changePeople(1)" {{ !$isOpen ? 'disabled' : '' }}>+</button>
                        </div>
                    </div>

                    <div class="rfd-form-group">
                        <label>Ghi chú (Tùy chọn)</label>
                        <textarea name="note" class="rfd-input" placeholder="Nhập ghi chú cho BQL..." {{ !$isOpen ? 'disabled' : '' }}>{{ old('note') }}</textarea>
                    </div>

                    @if($hasFee)
                        <div class="rfd-price-row">
                            <span>Phí dịch vụ:</span>
                            <span class="rfd-price-val">{{ $facility->price_label }}</span>
                        </div>
                        <div class="rfd-total-box">
                            <span class="rfd-total-label">Tạm tính:</span>
                            <span class="rfd-total-val" id="total-price-text">0đ</span>
                        </div>
                    @endif

                    <button type="submit" class="rfd-submit" id="submitBtn" {{ !$isOpen ? 'disabled' : '' }}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Xác nhận đăng ký
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const feeType = '{{ $feeType ?? 'free' }}';
const price = {{ $price ?? 0 }};
const maxCap = {{ $facility->capacity ?? 10 }};

function selectSlot(element, start, end) {
    document.querySelectorAll('.rfd-slot').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    document.getElementById('selected_slot').value = start + ' - ' + end;
    
    document.getElementById('start_time').value = start;
    document.getElementById('end_time').value = end;
}

function changePeople(delta) {
    const input = document.getElementById('number_of_people');
    const display = document.getElementById('people_display');
    if(!input || !display) return;
    
    let val = parseInt(input.value) || 1;
    val += delta;
    if(val < 1) val = 1;
    if(val > maxCap) val = maxCap;
    
    input.value = val;
    display.innerText = val;
    calculatePrice();
}

function calculatePrice() {
    if (feeType === 'free' || price <= 0) return;
    const people = parseInt(document.getElementById('number_of_people')?.value) || 1;
    
    let total = 0;
    if (feeType === 'per_person') {
        total = price * people;
    } else {
        total = price; // Fallback to per_use/per_hour approximation for now
    }
    
    const totalEl = document.getElementById('total-price-text');
    if(totalEl) {
        totalEl.innerText = total.toLocaleString('vi-VN') + 'đ';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Initial calculation
    calculatePrice();
});
</script>
@endpush
