@extends('layouts.admin.master')

@section('page_title', 'Thêm tiện ích mới')

@push('styles')
<style>
/* ═══ PAGE ═══ */
.af-page { max-width:980px; margin:0 auto; }
.af-back { font-size:13px; color:#64748b; text-decoration:none; display:inline-block; margin-bottom:16px; }
.af-back:hover { color:#0b57d0; }

/* ═══ HEADER (sticky) ═══ */
.af-header { display:flex; justify-content:space-between; align-items:center; padding:16px 0; margin-bottom:24px; position:sticky; top:0; background:#fff; z-index:20; border-bottom:1px solid #e2e8f0; }
.af-header__title { font-size:20px; font-weight:700; color:#0b1c30; }
.af-header__sub { font-size:13px; color:#64748b; margin-top:2px; }
.af-header__actions { display:flex; gap:10px; }
.af-btn { padding:10px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:.15s; }
.af-btn--primary { background:#0b57d0; color:#fff; } .af-btn--primary:hover { background:#094bb3; }
.af-btn--ghost { background:#f1f5f9; color:#475569; } .af-btn--ghost:hover { background:#e2e8f0; }

/* ═══ ERROR ═══ */
.af-errors { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:14px 18px; margin-bottom:20px; color:#dc2626; font-size:13px; }
.af-errors ul { margin:6px 0 0; padding-left:18px; }

/* ═══ GRID ═══ */
.af-grid { display:grid; grid-template-columns:1.3fr 1fr; gap:20px; align-items:start; }
@media(max-width:860px) { .af-grid { grid-template-columns:1fr; } }

/* ═══ CARD ═══ */
.af-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:0; }
.af-card + .af-card { margin-top:16px; }
.af-card__title { font-size:14px; font-weight:700; color:#0b1c30; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; }

/* ═══ FORM ═══ */
.af-field { margin-bottom:18px; }
.af-field:last-child { margin-bottom:0; }
.af-label { display:block; font-size:13px; font-weight:500; color:#334155; margin-bottom:6px; }
.af-label .req { color:#ef4444; margin-left:2px; }
.af-input { width:100%; padding:10px 12px; font-size:14px; border:1px solid #d1d5db; border-radius:7px; color:#0f172a; background:#fff; transition:border-color .15s, box-shadow .15s; box-sizing:border-box; }
.af-input:focus { outline:none; border-color:#0b57d0; box-shadow:0 0 0 3px rgba(11,87,208,.08); }
.af-input:disabled { background:#f8fafc; color:#94a3b8; }
textarea.af-input { resize:vertical; min-height:80px; }
select.af-input { appearance:auto; }

/* Row */
.af-row { display:flex; gap:14px; } .af-row > * { flex:1; }
@media(max-width:600px) { .af-row { flex-direction:column; gap:0; } }

/* Input with suffix */
.af-input-wrap { display:flex; }
.af-input-wrap .af-input { border-radius:7px 0 0 7px; border-right:0; }
.af-input-wrap__suffix { padding:10px 12px; font-size:13px; color:#64748b; background:#f8fafc; border:1px solid #d1d5db; border-radius:0 7px 7px 0; white-space:nowrap; display:flex; align-items:center; }

/* Time range */
.af-time { display:flex; align-items:center; gap:8px; }
.af-time .af-input { flex:1; }
.af-time__sep { color:#94a3b8; font-weight:600; font-size:14px; }

/* Char count */
.af-count { text-align:right; font-size:11px; color:#94a3b8; margin-top:3px; }

/* Day chips */
.af-days { display:flex; gap:6px; flex-wrap:wrap; }
.af-day-chip { cursor:pointer; }
.af-day-chip input { display:none; }
.af-day-chip span { display:inline-block; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; background:#f1f5f9; color:#64748b; border:1.5px solid transparent; transition:.15s; user-select:none; }
.af-day-chip input:checked + span { background:#eff6ff; color:#0b57d0; border-color:#bfdbfe; }
.af-day-chip:hover span { background:#e8eef6; }

/* Upload */
.af-upload { position:relative; border:2px dashed #cbd5e1; border-radius:8px; padding:20px; text-align:center; cursor:pointer; transition:.15s; }
.af-upload:hover { border-color:#0b57d0; background:#f8fbff; }
.af-upload__text { font-size:13px; color:#64748b; line-height:1.6; }
.af-upload__text strong { color:#0b57d0; }
.af-upload input { position:absolute; inset:0; opacity:0; cursor:pointer; }
.af-previews { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
.af-previews img { width:56px; height:56px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0; }

/* Radio */
.af-radios { display:flex; flex-direction:column; gap:10px; }
.af-radio { display:flex; align-items:center; gap:8px; font-size:13px; color:#334155; cursor:pointer; }
.af-radio input { accent-color:#0b57d0; width:16px; height:16px; }

/* Radio cards */
.af-radio-cards { display:flex; flex-direction:column; gap:8px; }
.af-rcard { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:8px; cursor:pointer; transition:.15s; }
.af-rcard:hover { border-color:#93c5fd; }
.af-rcard:has(input:checked) { border-color:#0b57d0; background:#f8fbff; }
.af-rcard input { margin-top:2px; accent-color:#0b57d0; flex-shrink:0; }
.af-rcard__title { font-size:13px; font-weight:600; color:#0f172a; }
.af-rcard__desc { font-size:12px; color:#64748b; margin-top:1px; }

/* Sub settings */
.af-sub { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-top:12px; }

/* Separator */
.af-sep { height:1px; background:#f1f5f9; margin:20px 0; }

/* Hint */
.af-hint { font-size:12px; color:#94a3b8; margin-top:6px; }

/* Footer */
.af-footer { display:flex; justify-content:flex-end; gap:10px; padding-top:20px; margin-top:24px; border-top:1px solid #e2e8f0; }
</style>
@endpush

@section('content')
<div class="af-page">

    <a href="{{ portal_route('amenities.index') }}" class="af-back">← Quay lại danh sách</a>

    <form method="POST" action="{{ portal_route('amenities.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Header --}}
        <div class="af-header">
            <div>
                <div class="af-header__title">Thêm tiện ích mới</div>
                <div class="af-header__sub">Thiết lập tiện ích cho cư dân sử dụng</div>
            </div>
            <div class="af-header__actions">
                <a href="{{ portal_route('amenities.index') }}" class="af-btn af-btn--ghost">Hủy</a>
                <button type="submit" class="af-btn af-btn--primary">Lưu tiện ích</button>
            </div>
        </div>

        {{-- Errors --}}
        @if($errors->any())
        <div class="af-errors">
            <strong>Vui lòng kiểm tra lại:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Grid --}}
        <div class="af-grid">

            {{-- LEFT --}}
            <div>
                <div class="af-card">
                    <div class="af-card__title">Thông tin chung</div>

                    <div class="af-field">
                        <label class="af-label">Tên tiện ích <span class="req">*</span></label>
                        <input type="text" name="name" class="af-input" value="{{ old('name') }}" placeholder="VD: Hồ bơi, Phòng Gym, Sân BBQ..." required>
                    </div>

                    <div class="af-row">
                        <div class="af-field">
                            <label class="af-label">Loại</label>
                            <select name="facility_type" class="af-input">
                                <option value="">Chọn loại</option>
                                <option value="Thể thao" {{ old('facility_type')=='Thể thao'?'selected':'' }}>Thể thao</option>
                                <option value="Giải trí" {{ old('facility_type')=='Giải trí'?'selected':'' }}>Giải trí</option>
                                <option value="Sinh hoạt chung" {{ old('facility_type')=='Sinh hoạt chung'?'selected':'' }}>Sinh hoạt chung</option>
                            </select>
                        </div>
                        <div class="af-field">
                            <label class="af-label">Sức chứa <span class="req">*</span></label>
                            <div class="af-input-wrap">
                                <input type="number" name="capacity" class="af-input" value="{{ old('capacity', 50) }}" min="1" required>
                                <span class="af-input-wrap__suffix">người</span>
                            </div>
                        </div>
                    </div>

                    <div class="af-row">
                        <div class="af-field">
                            <label class="af-label">Tòa nhà</label>
                            <select name="block_id" id="blockSelect" class="af-input">
                                <option value="">Không chọn</option>
                                @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id')==$block->id?'selected':'' }}>{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="af-field">
                            <label class="af-label">Tầng</label>
                            <select name="floor_id" id="floorSelect" class="af-input" {{ old('block_id')?'':'disabled' }}>
                                <option value="">Không chọn</option>
                            </select>
                            <input type="hidden" id="oldFloorId" value="{{ old('floor_id') }}">
                        </div>
                    </div>

                    <div class="af-field">
                        <label class="af-label">Mô tả</label>
                        <textarea name="description" class="af-input" rows="3" maxlength="500" placeholder="Mô tả ngắn gọn..." oninput="this.nextElementSibling.textContent=this.value.length+'/500'">{{ old('description') }}</textarea>
                        <span class="af-count">{{ strlen(old('description','')) }}/500</span>
                    </div>

                    <div class="af-sep"></div>

                    {{-- Giờ hoạt động --}}
                    <div class="af-row">
                        <div class="af-field">
                            <label class="af-label">Giờ hoạt động</label>
                            <div class="af-time">
                                <input type="time" name="open_time" class="af-input" value="{{ old('open_time','06:00') }}">
                                <span class="af-time__sep">–</span>
                                <input type="time" name="close_time" class="af-input" value="{{ old('close_time','22:00') }}">
                            </div>
                        </div>
                        <div class="af-field">
                            <label class="af-label">Ngày hoạt động</label>
                            <div class="af-days">
                                @php $days=['T2','T3','T4','T5','T6','T7','CN']; $sel=old('operating_days',$days); @endphp
                                @foreach($days as $d)
                                <label class="af-day-chip"><input type="checkbox" name="operating_days[]" value="{{ $d }}" {{ in_array($d,$sel)?'checked':'' }}><span>{{ $d }}</span></label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="af-field">
                        <label class="af-label">Nội quy sử dụng</label>
                        <textarea name="rules" class="af-input" rows="4" maxlength="1000" placeholder="Nhập nội quy..." oninput="this.nextElementSibling.textContent=this.value.length+'/1000'">{{ old('rules') }}</textarea>
                        <span class="af-count">{{ strlen(old('rules','')) }}/1000</span>
                    </div>

                    <div class="af-sep"></div>

                    {{-- Ảnh --}}
                    <div class="af-field">
                        <label class="af-label">Hình ảnh <span class="req">*</span> (tối đa 5 ảnh, 5MB/ảnh)</label>
                        <div class="af-upload">
                            <div class="af-upload__text"><strong>Nhấn để chọn ảnh</strong> hoặc kéo thả vào đây<br>JPG, PNG, WEBP</div>
                            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" id="imgInput">
                        </div>
                        <div class="af-previews" id="imgPreviews"></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT --}}
            <div>
                <div class="af-card">
                    <div class="af-card__title">Biểu giá & Đặt chỗ</div>

                    <div class="af-field">
                        <label class="af-label">Loại phí <span class="req">*</span></label>
                        <div class="af-radios">
                            <label class="af-radio"><input type="radio" name="fee_type" value="free" {{ old('fee_type','free')=='free'?'checked':'' }}> Miễn phí</label>
                            <label class="af-radio"><input type="radio" name="fee_type" value="per_hour" {{ old('fee_type')=='per_hour'?'checked':'' }}> Theo giờ</label>
                            <label class="af-radio"><input type="radio" name="fee_type" value="per_use" {{ old('fee_type')=='per_use'?'checked':'' }}> Theo lượt</label>
                            <label class="af-radio"><input type="radio" name="fee_type" value="per_person" {{ old('fee_type')=='per_person'?'checked':'' }}> Theo người</label>
                        </div>
                    </div>

                    <div class="af-field">
                        <label class="af-label">Đơn giá</label>
                        <div class="af-input-wrap">
                            <input type="number" name="price" id="priceInput" class="af-input" value="{{ old('price',0) }}" {{ old('fee_type','free')=='free'?'disabled':'' }}>
                            <span class="af-input-wrap__suffix">VND</span>
                        </div>
                    </div>

                    <div class="af-sep"></div>

                    <div class="af-field">
                        <label class="af-label">Hình thức đặt chỗ <span class="req">*</span></label>
                        <div class="af-radio-cards">
                            <label class="af-rcard">
                                <input type="radio" name="booking_type" value="none" {{ old('booking_type','none')=='none'?'checked':'' }}>
                                <div><div class="af-rcard__title">Không cần đặt trước</div><div class="af-rcard__desc">Cư dân đến sử dụng trực tiếp</div></div>
                            </label>
                            <label class="af-rcard">
                                <input type="radio" name="booking_type" value="time_slot" {{ old('booking_type')=='time_slot'?'checked':'' }}>
                                <div><div class="af-rcard__title">Theo khung giờ</div><div class="af-rcard__desc">Chọn thời gian bắt đầu và thời lượng</div></div>
                            </label>
                            <label class="af-rcard">
                                <input type="radio" name="booking_type" value="slot" {{ old('booking_type')=='slot'?'checked':'' }}>
                                <div><div class="af-rcard__title">Theo thời gian cụ thể</div><div class="af-rcard__desc">Đặt lịch giờ bắt đầu – kết thúc</div></div>
                            </label>
                        </div>
                    </div>

                    <div class="af-sub" id="slotSettings" style="display:{{ in_array(old('booking_type'),['time_slot','slot'])?'block':'none' }}">
                        <div class="af-field">
                            <label class="af-label">Thời lượng 1 lượt <span class="req">*</span></label>
                            <div class="af-input-wrap">
                                <input type="number" name="slot_duration" class="af-input" value="{{ old('slot_duration',60) }}">
                                <span class="af-input-wrap__suffix">phút</span>
                            </div>
                        </div>
                        <div class="af-row">
                            <div class="af-field">
                                <label class="af-label">Đặt trước tối thiểu</label>
                                <div class="af-input-wrap">
                                    <input type="number" name="min_advance_booking_hours" class="af-input" value="{{ old('min_advance_booking_hours',2) }}">
                                    <span class="af-input-wrap__suffix">giờ</span>
                                </div>
                            </div>
                            <div class="af-field">
                                <label class="af-label">Đặt trước tối đa</label>
                                <div class="af-input-wrap">
                                    <input type="number" name="max_advance_booking_days" class="af-input" value="{{ old('max_advance_booking_days',7) }}">
                                    <span class="af-input-wrap__suffix">ngày</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="af-card" style="margin-top:16px;">
                    <div class="af-card__title">Trạng thái</div>
                    <div class="af-radios">
                        <label class="af-radio"><input type="radio" name="status" value="available" {{ old('status','available')=='available'?'checked':'' }}> Hoạt động</label>
                        <label class="af-radio"><input type="radio" name="status" value="maintenance" {{ old('status')=='maintenance'?'checked':'' }}> Bảo trì</label>
                        <label class="af-radio"><input type="radio" name="status" value="closed" {{ old('status')=='closed'?'checked':'' }}> Ngừng sử dụng</label>
                    </div>
                    <p class="af-hint">Tiện ích sẽ hiển thị cho cư dân sau khi lưu.</p>
                </div>
            </div>
        </div>

        {{-- Bottom actions --}}
        <div class="af-footer">
            <a href="{{ portal_route('amenities.index') }}" class="af-btn af-btn--ghost">Hủy</a>
            <button type="submit" class="af-btn af-btn--primary">Lưu tiện ích</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Fee type
document.querySelectorAll('input[name="fee_type"]').forEach(r => {
    r.addEventListener('change', function(){ document.getElementById('priceInput').disabled = this.value === 'free'; });
});
// Booking type
document.querySelectorAll('input[name="booking_type"]').forEach(r => {
    r.addEventListener('change', function(){ document.getElementById('slotSettings').style.display = this.value === 'none' ? 'none' : 'block'; });
});
// Block → Floor
document.getElementById('blockSelect').addEventListener('change', function(){
    const fs = document.getElementById('floorSelect'); fs.innerHTML = '<option value="">Không chọn</option>';
    if(!this.value){ fs.disabled=true; return; } fs.disabled=false;
    fetch('/api/floors?block_id='+this.value).then(r=>r.json()).then(floors=>{
        const old = document.getElementById('oldFloorId').value;
        floors.forEach(f=>{ fs.innerHTML+=`<option value="${f.id}" ${f.id==old?'selected':''}>${f.name}</option>`; });
    }).catch(()=>{});
});
if(document.getElementById('blockSelect').value) document.getElementById('blockSelect').dispatchEvent(new Event('change'));
// Image preview
document.getElementById('imgInput').addEventListener('change', function(){
    const c = document.getElementById('imgPreviews'); c.innerHTML='';
    [...this.files].slice(0,5).forEach(f=>{ const r=new FileReader(); r.onload=e=>{c.innerHTML+=`<img src="${e.target.result}">`;}; r.readAsDataURL(f); });
});
</script>
@endpush
