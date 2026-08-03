@extends('layouts.resident.master')

@push('styles')
<style>
.rf-page { max-width: 1100px; margin: 0 auto; padding: 32px 20px; font-family: 'Inter', sans-serif; }

.rf-header { margin-bottom: 24px; }
.rf-title { font-size: 28px; font-weight: 700; color: #1e3a8a; margin: 0 0 8px; }
.rf-subtitle { font-size: 15px; color: #475569; margin: 0; line-height: 1.6; max-width: 800px; }

/* Toolbar: Search and Filters */
.rf-toolbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 28px; }

.rf-search { flex: 1; min-width: 250px; position: relative; }
.rf-search input { width: 100%; padding: 10px 16px 10px 40px; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 14px; outline: none; background: #f8fafc; transition: border-color 0.2s; box-sizing: border-box; }
.rf-search input:focus { border-color: #3b82f6; background: #fff; }
.rf-search svg { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

.rf-filters { display: flex; gap: 10px; flex-wrap: wrap; }
.rf-filter-btn { padding: 8px 18px; border: 1px solid #e2e8f0; border-radius: 20px; font-size: 14px; font-weight: 500; color: #475569; background: #fff; text-decoration: none; transition: all 0.2s; cursor: pointer; }
.rf-filter-btn:hover { border-color: #cbd5e1; background: #f1f5f9; }
.rf-filter-btn.active { background: #1e3a8a; color: #fff; border-color: #1e3a8a; }

/* Grid */
.rf-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 32px; }

/* Card */
.rf-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; transition: box-shadow 0.2s, transform 0.2s; text-decoration: none; color: inherit; height: 220px; }
.rf-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.06); transform: translateY(-2px); }

/* Left Side: Image */
.rf-card-img-wrap { width: 45%; position: relative; overflow: hidden; background: linear-gradient(135deg, #e0f2fe, #ede9fe); }
.rf-card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
.rf-card-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }

.rf-badge { position: absolute; top: 12px; right: 12px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.9); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.rf-badge-dot { width: 8px; height: 8px; border-radius: 50%; }
.rf-badge--available { color: #16a34a; }
.rf-badge--available .rf-badge-dot { background: #16a34a; }
.rf-badge--maintenance { color: #dc2626; }
.rf-badge--maintenance .rf-badge-dot { background: #dc2626; }
.rf-badge--closed { color: #ea580c; }
.rf-badge--closed .rf-badge-dot { background: #ea580c; }

/* Right Side: Info */
.rf-card-body { width: 55%; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; }
.rf-card-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rf-card-location { font-size: 13px; color: #64748b; display: flex; align-items: center; gap: 4px; margin-bottom: 16px; }
.rf-card-location svg { flex-shrink: 0; }

.rf-info-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px; }
.rf-info-label { color: #475569; }
.rf-info-val { font-weight: 600; color: #0f172a; }
.rf-info-val--free { color: #16a34a; }

.rf-card-actions { display: flex; gap: 10px; margin-top: auto; }
.rf-btn { flex: 1; padding: 8px; border-radius: 6px; font-size: 13px; font-weight: 600; text-align: center; text-decoration: none; transition: all 0.2s; border: 1px solid transparent; display: flex; align-items: center; justify-content: center; }
.rf-btn-primary { background: #1e3a8a; color: #fff; }
.rf-btn-primary:hover { background: #1e3a8a; opacity: 0.9; color: #fff; }
.rf-btn-outline { background: #fff; color: #475569; border-color: #cbd5e1; }
.rf-btn-outline:hover { background: #f8fafc; color: #0f172a; }
.rf-btn-disabled { background: #cbd5e1; color: #fff; cursor: not-allowed; }

/* Modal */
.rf-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.2s; padding: 20px; }
.rf-modal-overlay.active { opacity: 1; pointer-events: auto; }
.rf-modal-content { background: #fff; width: 100%; max-width: 500px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); transform: translateY(20px); transition: transform 0.2s; max-height: 90vh; overflow-y: auto; display: flex; flex-direction: column; }
.rf-modal-overlay.active .rf-modal-content { transform: translateY(0); }
.rf-modal-header { padding: 16px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: #fff; z-index: 10; border-radius: 12px 12px 0 0; }
.rf-modal-title { font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; }
.rf-modal-close { background: none; border: none; font-size: 24px; color: #64748b; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 8px; transition: all 0.2s; }
.rf-modal-close:hover { background: #f1f5f9; color: #0f172a; }
.rf-modal-body { padding: 24px; }

.rfd-form-group { margin-bottom: 16px; }
.rfd-form-group label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
.rfd-input { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #0f172a; outline: none; transition: all 0.2s; box-sizing: border-box; }
.rfd-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.rfd-slots { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; }
.rfd-slot { padding: 8px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; font-weight: 600; color: #475569; background: #fff; cursor: pointer; transition: all 0.2s; user-select: none; }
.rfd-slot:hover { border-color: #94a3b8; }
.rfd-slot.active { background: #1e3a8a; border-color: #1e3a8a; color: #fff; }
.rfd-slot.disabled { background: #f1f5f9; color: #cbd5e1; cursor: not-allowed; border-color: #e2e8f0; }

.rfd-people-wrap { display: flex; align-items: center; gap: 12px; }
.rfd-people-btn { width: 32px; height: 32px; border: 1px solid #cbd5e1; border-radius: 50%; background: #fff; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #475569; }
.rfd-people-val { font-size: 16px; font-weight: 700; color: #0f172a; width: 24px; text-align: center; }

.rfd-price-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: #64748b; }
.rfd-price-val { font-weight: 600; color: #0f172a; }
.rfd-total-box { background: #eff6ff; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.rfd-total-label { font-size: 15px; font-weight: 700; color: #1e3a8a; }
.rfd-total-val { font-size: 18px; font-weight: 800; color: #1e3a8a; }

.rfd-submit { width: 100%; padding: 14px; background: #059669; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: background 0.2s; }
.rfd-submit:hover { background: #047857; }

.rfd-alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; }
.rfd-alert--error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
.rfd-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }


@media (max-width: 900px) {
    .rf-grid { grid-template-columns: 1fr; }
    .rf-toolbar { flex-direction: column; align-items: stretch; }
}
@media (max-width: 500px) {
    .rf-card { flex-direction: column; height: auto; }
    .rf-card-img-wrap { width: 100%; height: 180px; }
    .rf-card-body { width: 100%; box-sizing: border-box; }
}
</style>
@endpush

@section('title', 'Tiện ích chung cư – DomusHub')

@section('content')
<div class="rf-page">

    {{-- Header --}}
    <div class="rf-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="rf-title">Tiện ích Toà nhà</h1>
            <p class="rf-subtitle">Khám phá và đăng ký các dịch vụ cao cấp dành riêng cho cư dân ResiCare. Tận hưởng không gian sống đẳng cấp và hiện đại ngay tại nơi bạn ở.</p>
        </div>
        <a href="{{ route('resident.facility-bookings.index') }}" class="rf-btn rf-btn-outline" style="white-space: nowrap; padding: 10px 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 6px;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Lịch sử đặt tiện ích
        </a>
    </div>

    {{-- Toolbar --}}
    <div class="rf-toolbar">
        <div class="rf-search">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <form action="{{ route('resident.facilities.index') }}" method="GET" style="margin:0;">
                <input type="text" name="search" placeholder="Tìm kiếm tiện ích..." value="{{ request('search') }}">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            </form>
        </div>
        <div class="rf-filters">
            <a href="{{ route('resident.facilities.index') }}" class="rf-filter-btn {{ !request('status') ? 'active' : '' }}">Tất cả</a>
            {{-- Fake category filters for UI demonstration --}}
            <a href="#" class="rf-filter-btn">Thư giãn</a>
            <a href="#" class="rf-filter-btn">Thể thao</a>
            <a href="#" class="rf-filter-btn">Khu vực chung</a>
        </div>
    </div>

    {{-- Grid --}}
    @if($facilities->isEmpty())
    <div style="text-align: center; padding: 40px 0; color: #64748b;">
        <p>Không tìm thấy tiện ích nào.</p>
    </div>
    @else
    <div class="rf-grid">
        @foreach($facilities as $facility)
        @php
            $isAvailable = $facility->status === 'available';
            $statusText = $isAvailable ? 'Sẵn sàng' : ($facility->status === 'maintenance' ? 'Bảo trì' : 'Tạm ngưng');
            $statusClass = $isAvailable ? 'rf-badge--available' : ($facility->status === 'maintenance' ? 'rf-badge--maintenance' : 'rf-badge--closed');
            $priceText = ($facility->fee_type == 'free' || !$facility->price || $facility->price == 0) ? 'Miễn phí' : $facility->price_label;
            $priceClass = ($priceText === 'Miễn phí') ? 'rf-info-val--free' : '';
        @endphp
        <div class="rf-card">
            <div class="rf-card-img-wrap">
                @if($facility->images && count($facility->images) > 0)
                    <img src="{{ asset('storage/' . $facility->images[0]) }}" alt="{{ $facility->name }}">
                @else
                    <div class="rf-card-placeholder">
                        @include('partials.facility-placeholder', ['name' => $facility->name])
                    </div>
                @endif
                <div class="rf-badge {{ $statusClass }}">
                    <span class="rf-badge-dot"></span> {{ $statusText }}
                </div>
            </div>
            
            <div class="rf-card-body">
                <div>
                    <h3 class="rf-card-title" title="{{ $facility->name }}">{{ $facility->name }}</h3>
                    <div class="rf-card-location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $facility->floor?->name ? $facility->floor->name . ', ' : '' }}{{ $facility->block?->name ?: 'Khu vực chung' }}
                    </div>

                    <div class="rf-info-row">
                        <span class="rf-info-label">Giờ mở cửa:</span>
                        <span class="rf-info-val">{{ $facility->operating_hours }}</span>
                    </div>
                    <div class="rf-info-row">
                        <span class="rf-info-label">Chi phí:</span>
                        <span class="rf-info-val {{ $priceClass }}">{{ $priceText }}</span>
                    </div>
                </div>

                <div class="rf-card-actions">
                    @if($isAvailable)
                    <button type="button" class="rf-btn rf-btn-primary" onclick="event.preventDefault(); openBookingModal('modal-{{ $facility->id }}')">Đăng ký</button>
                    @else
                    <button class="rf-btn rf-btn-disabled" disabled>Tạm đóng</button>
                    @endif
                    <a href="{{ route('resident.facilities.show', $facility) }}" class="rf-btn rf-btn-outline">Chi tiết</a>
                </div>
            </div>
        </div>

        {{-- Booking Modal for this Facility --}}
        @if($isAvailable)
        @php
            $bookingType = $facility->booking_type ?? 'time_slot';
            // Use real slots for today
            $slots = $facility->getTimeSlots();
            $hasFee = $facility->fee_type !== 'free' && $facility->price > 0;
        @endphp
        <div id="modal-{{ $facility->id }}" class="rf-modal-overlay" onclick="closeBookingModal('modal-{{ $facility->id }}', event)">
            <div class="rf-modal-content" onclick="event.stopPropagation()">
                <div class="rf-modal-header">
                    <h2 class="rf-modal-title">Đăng ký: {{ $facility->name }}</h2>
                    <button class="rf-modal-close" onclick="closeBookingModal('modal-{{ $facility->id }}')">&times;</button>
                </div>
                <div class="rf-modal-body">
                    <form method="POST" action="{{ route('resident.facilities.book.store', $facility) }}">
                        @csrf
                        <div class="rfd-form-group">
                            <label>Chọn ngày</label>
                            <input type="date" name="booking_date" class="rfd-input" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        @if(in_array($bookingType, ['time_slot', 'slot']))
                            <div class="rfd-form-group">
                                <label>Khung giờ</label>
                                <input type="hidden" name="time_slot" id="selected_slot_{{ $facility->id }}" value="">
                                <input type="hidden" name="start_time" id="start_time_{{ $facility->id }}" value="">
                                <input type="hidden" name="end_time" id="end_time_{{ $facility->id }}" value="">
                                <div class="rfd-slots">
                                    @if(empty($slots))
                                        <div style="font-size:13px; color:#94a3b8; grid-column:1/-1;">Chưa cấu hình khung giờ.</div>
                                    @endif
                                    @foreach($slots as $s)
                                        @php
                                            $timeLabel = $s['label'];
                                        @endphp
                                        <div class="rfd-slot" onclick="selectModalSlot('{{ $facility->id }}', this, '{{ $s['start'] }}', '{{ $s['end'] }}')">
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
                                    <button type="button" class="rfd-people-btn" onclick="changeModalPeople('{{ $facility->id }}', -1, {{ $facility->capacity ?? 10 }})">−</button>
                                    <span class="rfd-people-val" id="people_display_{{ $facility->id }}">1</span>
                                    <input type="hidden" name="number_of_people" id="number_of_people_{{ $facility->id }}" value="1">
                                    <button type="button" class="rfd-people-btn" onclick="changeModalPeople('{{ $facility->id }}', 1, {{ $facility->capacity ?? 10 }})">+</button>
                                </div>
                            </div>
                        <div class="rfd-form-group">
                            <label>Ghi chú (Tùy chọn)</label>
                            <textarea name="note" class="rfd-input" placeholder="Nhập ghi chú cho BQL..."></textarea>
                        </div>
                        @if($hasFee)
                            <div class="rfd-price-row">
                                <span>Phí dịch vụ:</span>
                                <span class="rfd-price-val">{{ $facility->price_label }}</span>
                            </div>
                        @endif
                        <button type="submit" class="rfd-submit">Xác nhận đăng ký</button>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    @if($facilities->count() >= 6)
    <div class="rf-load-more">
        <a href="#" class="rf-load-btn">
            Xem thêm tiện ích
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
    </div>
    @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
function openBookingModal(modalId) {
    document.getElementById(modalId).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBookingModal(modalId, event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = '';
}

function selectModalSlot(facilityId, element, start, end) {
    const modal = document.getElementById('modal-' + facilityId);
    if (!modal) return;
    modal.querySelectorAll('.rfd-slot').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
    document.getElementById('selected_slot_' + facilityId).value = start + ' - ' + end;
    
    const startInput = document.getElementById('start_time_' + facilityId);
    const endInput = document.getElementById('end_time_' + facilityId);
    if(startInput) startInput.value = start;
    if(endInput) endInput.value = end;
}

function changeModalPeople(facilityId, delta, maxCap) {
    const input = document.getElementById('number_of_people_' + facilityId);
    const display = document.getElementById('people_display_' + facilityId);
    if(!input || !display) return;
    
    let val = parseInt(input.value) || 1;
    val += delta;
    if(val < 1) val = 1;
    if(val > maxCap) val = maxCap;
    
    input.value = val;
    display.innerText = val;
}
</script>
@endpush
