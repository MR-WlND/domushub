@extends('layouts.admin.master')

@section('page_title', 'Chỉnh sửa: ' . $facility->name)

@push('styles')
<style>
.af-page { max-width:980px; margin:0 auto; }
.af-back { font-size:13px; color:#64748b; text-decoration:none; display:inline-block; margin-bottom:16px; }
.af-back:hover { color:#0b57d0; }
.af-header { display:flex; justify-content:space-between; align-items:center; padding:16px 0; margin-bottom:24px; position:sticky; top:0; background:#fff; z-index:20; border-bottom:1px solid #e2e8f0; }
.af-header__title { font-size:26px; font-weight:700; color:#00236f; font-family:'Inter', system-ui, -apple-system, sans-serif; letter-spacing:-0.02em; }
.af-header__sub { font-size:13.5px; color:#64748b; margin-top:4px; font-family:'Inter', system-ui, -apple-system, sans-serif; }
.af-header__actions { display:flex; gap:10px; }
.af-btn { padding:10px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:.15s; }
.af-btn--primary { background:#0b57d0; color:#fff; } .af-btn--primary:hover { background:#094bb3; }
.af-btn--ghost { background:#f1f5f9; color:#475569; } .af-btn--ghost:hover { background:#e2e8f0; }
.af-btn--danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; } .af-btn--danger:hover { background:#fee2e2; }
.af-errors { background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:14px 18px; margin-bottom:20px; color:#dc2626; font-size:13px; }
.af-errors ul { margin:6px 0 0; padding-left:18px; }
.af-grid { display:grid; grid-template-columns:1.3fr 1fr; gap:20px; align-items:start; }
@media(max-width:860px) { .af-grid { grid-template-columns:1fr; } }
.af-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; }
.af-card + .af-card { margin-top:16px; }
.af-card__title { font-size:14px; font-weight:700; color:#0b1c30; margin-bottom:18px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; }
.af-field { margin-bottom:18px; } .af-field:last-child { margin-bottom:0; }
.af-label { display:block; font-size:13px; font-weight:500; color:#334155; margin-bottom:6px; }
.af-label .req { color:#ef4444; margin-left:2px; }
.af-input { width:100%; padding:10px 12px; font-size:14px; border:1px solid #d1d5db; border-radius:7px; color:#0f172a; background:#fff; transition:border-color .15s; box-sizing:border-box; }
.af-input:focus { outline:none; border-color:#0b57d0; box-shadow:0 0 0 3px rgba(11,87,208,.08); }
.af-input:disabled { background:#f8fafc; color:#94a3b8; }
textarea.af-input { resize:vertical; min-height:80px; }
.af-row { display:flex; gap:14px; } .af-row > * { flex:1; }
@media(max-width:600px) { .af-row { flex-direction:column; gap:0; } }
.af-input-wrap { display:flex; }
.af-input-wrap .af-input { border-radius:7px 0 0 7px; border-right:0; }
.af-input-wrap__suffix { padding:10px 12px; font-size:13px; color:#64748b; background:#f8fafc; border:1px solid #d1d5db; border-radius:0 7px 7px 0; white-space:nowrap; display:flex; align-items:center; }
.af-time { display:flex; align-items:center; gap:8px; } .af-time .af-input { flex:1; }
.af-time__sep { color:#94a3b8; font-weight:600; }
.af-count { text-align:right; font-size:11px; color:#94a3b8; margin-top:3px; }
.af-days { display:flex; gap:6px; flex-wrap:wrap; }
.af-day-chip { cursor:pointer; } .af-day-chip input { display:none; }
.af-day-chip span { display:inline-block; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; background:#f1f5f9; color:#64748b; border:1.5px solid transparent; transition:.15s; user-select:none; }
.af-day-chip input:checked + span { background:#eff6ff; color:#0b57d0; border-color:#bfdbfe; }
.af-sep { height:1px; background:#f1f5f9; margin:20px 0; }
.af-radios { display:flex; flex-direction:column; gap:10px; }
.af-radio { display:flex; align-items:center; gap:8px; font-size:13px; color:#334155; cursor:pointer; }
.af-radio input { accent-color:#0b57d0; width:16px; height:16px; }
.af-radio-cards { display:flex; flex-direction:column; gap:8px; }
.af-rcard { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:8px; cursor:pointer; transition:.15s; }
.af-rcard:hover { border-color:#93c5fd; }
.af-rcard:has(input:checked) { border-color:#0b57d0; background:#f8fbff; }
.af-rcard input { margin-top:2px; accent-color:#0b57d0; flex-shrink:0; }
.af-rcard__title { font-size:13px; font-weight:600; color:#0f172a; }
.af-rcard__desc { font-size:12px; color:#64748b; margin-top:1px; }
.af-sub { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; margin-top:12px; }
.af-hint { font-size:12px; color:#94a3b8; margin-top:6px; }
.af-footer { display:flex; justify-content:flex-end; gap:10px; padding-top:20px; margin-top:24px; border-top:1px solid #e2e8f0; }
/* Images */
.af-gallery { display:flex; flex-wrap:wrap; gap:10px; margin-top:10px; }
.af-gallery-item { position:relative; width:80px; height:80px; border-radius:8px; overflow:hidden; border:1px solid #e2e8f0; }
.af-gallery-item img { width:100%; height:100%; object-fit:cover; }
.af-gallery-item__del { position:absolute; top:4px; right:4px; width:20px; height:20px; border-radius:50%; background:rgba(0,0,0,.6); color:#fff; border:none; font-size:11px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.af-gallery-item__del:hover { background:#dc2626; }
.af-upload { position:relative; border:2px dashed #cbd5e1; border-radius:8px; padding:16px; text-align:center; cursor:pointer; transition:.15s; margin-top:12px; }
.af-upload:hover { border-color:#0b57d0; background:#f8fbff; }
.af-upload__text { font-size:13px; color:#64748b; } .af-upload__text strong { color:#0b57d0; }
.af-upload input { position:absolute; inset:0; opacity:0; cursor:pointer; }
</style>
@endpush

@section('content')
<div class="af-page">

    <a href="{{ portal_route('amenities.index') }}" class="af-back">← Quay lại danh sách</a>

    <form method="POST" action="{{ portal_route('amenities.update', $facility) }}" enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')

        <div class="af-header">
            <div>
                <div class="af-header__title">Chỉnh sửa: {{ $facility->name }}</div>
                <div class="af-header__sub">Cập nhật thông tin và cài đặt tiện ích</div>
            </div>
            <div class="af-header__actions">
                <a href="{{ portal_route('amenities.index') }}" class="af-btn af-btn--ghost">Hủy</a>
                <button type="submit" class="af-btn af-btn--primary">Lưu thay đổi</button>
            </div>
        </div>

        @if($errors->any())
        <div class="af-errors">
            <strong>Vui lòng kiểm tra lại:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="af-grid">
            {{-- LEFT --}}
            <div>
                <div class="af-card">
                    <div class="af-card__title">Thông tin chung</div>

                    <div class="af-field">
                        <label class="af-label">Tên tiện ích <span class="req">*</span></label>
                        <input type="text" name="name" class="af-input" value="{{ old('name', $facility->name) }}" required>
                    </div>

                    <div class="af-row">
                        <div class="af-field">
                            <label class="af-label">Loại</label>
                            <select name="facility_type" class="af-input">
                                <option value="">Chọn loại</option>
                                @foreach(['Thể thao','Giải trí','Sinh hoạt chung'] as $type)
                                <option value="{{ $type }}" {{ old('facility_type', $facility->facility_type)==$type?'selected':'' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="af-field">
                            <label class="af-label">Sức chứa <span class="req">*</span></label>
                            <div class="af-input-wrap">
                                <input type="number" name="capacity" class="af-input" value="{{ old('capacity', $facility->capacity) }}" min="1" required>
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
                                <option value="{{ $block->id }}" {{ old('block_id', $facility->block_id)==$block->id?'selected':'' }}>{{ $block->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="af-field">
                            <label class="af-label">Tầng</label>
                            <select name="floor_id" id="floorSelect" class="af-input">
                                <option value="">Không chọn</option>
                            </select>
                            <input type="hidden" id="oldFloorId" value="{{ old('floor_id', $facility->floor_id) }}">
                        </div>
                    </div>

                    <div class="af-field">
                        <label class="af-label">Mô tả</label>
                        <textarea name="description" class="af-input" rows="3" maxlength="500" oninput="this.nextElementSibling.textContent=this.value.length+'/500'">{{ old('description', $facility->description) }}</textarea>
                        <span class="af-count">{{ strlen(old('description', $facility->description ?? '')) }}/500</span>
                    </div>

                    <div class="af-sep"></div>

                    <div class="af-row">
                        <div class="af-field">
                            <label class="af-label">Giờ hoạt động</label>
                            <div class="af-time">
                                <input type="time" name="open_time" class="af-input" value="{{ old('open_time', $facility->open_time ? substr($facility->open_time, 0, 5) : '06:00') }}">
                                <span class="af-time__sep">–</span>
                                <input type="time" name="close_time" class="af-input" value="{{ old('close_time', $facility->close_time ? substr($facility->close_time, 0, 5) : '22:00') }}">
                            </div>
                        </div>
                        <div class="af-field">
                            <label class="af-label">Ngày hoạt động</label>
                            <div class="af-days">
                                @php $days=['T2','T3','T4','T5','T6','T7','CN']; $sel=old('operating_days', $facility->operating_days ?? $days); @endphp
                                @foreach($days as $d)
                                <label class="af-day-chip"><input type="checkbox" name="operating_days[]" value="{{ $d }}" {{ in_array($d, $sel)?'checked':'' }}><span>{{ $d }}</span></label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="af-field">
                        <label class="af-label">Nội quy sử dụng</label>
                        <textarea name="rules" class="af-input" rows="4" maxlength="1000" oninput="this.nextElementSibling.textContent=this.value.length+'/1000'">{{ old('rules', $facility->rules) }}</textarea>
                        <span class="af-count">{{ strlen(old('rules', $facility->rules ?? '')) }}/1000</span>
                    </div>

                    <div class="af-sep"></div>

                    {{-- Ảnh --}}
                    <div class="af-field">
                        <label class="af-label">Hình ảnh</label>
                        @if($facility->images && count($facility->images) > 0)
                        <div class="af-gallery">
                            @foreach($facility->images as $index => $image)
                            <div class="af-gallery-item">
                                <img src="{{ asset('storage/' . $image) }}" alt="">
                                <form method="POST" action="{{ portal_route('amenities.images.destroy', [$facility, $index]) }}" style="margin:0;" onsubmit="return confirm('Xóa ảnh này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="af-gallery-item__del">×</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="af-hint">Chưa có ảnh nào.</p>
                        @endif
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
                            @foreach(['free'=>'Miễn phí','per_hour'=>'Theo giờ','per_use'=>'Theo lượt','per_person'=>'Theo người'] as $val=>$lbl)
                            <label class="af-radio"><input type="radio" name="fee_type" value="{{ $val }}" {{ old('fee_type', $facility->fee_type)==$val?'checked':'' }}> {{ $lbl }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="af-field">
                        <label class="af-label">Đơn giá</label>
                        <div class="af-input-wrap">
                            <input type="number" name="price" id="priceInput" class="af-input" value="{{ old('price', $facility->price) }}" {{ old('fee_type', $facility->fee_type)=='free'?'disabled':'' }}>
                            <span class="af-input-wrap__suffix">VND</span>
                        </div>
                    </div>

                    <div class="af-sep"></div>

                    <div class="af-field">
                        <label class="af-label">Hình thức đặt chỗ <span class="req">*</span></label>
                        <div class="af-radio-cards">
                            <label class="af-rcard">
                                <input type="radio" name="booking_type" value="none" {{ old('booking_type', $facility->booking_type)=='none'?'checked':'' }}>
                                <div><div class="af-rcard__title">Không cần đặt trước</div><div class="af-rcard__desc">Cư dân đến sử dụng trực tiếp</div></div>
                            </label>
                            <label class="af-rcard">
                                <input type="radio" name="booking_type" value="time_slot" {{ old('booking_type', $facility->booking_type)=='time_slot'?'checked':'' }}>
                                <div><div class="af-rcard__title">Theo khung giờ</div><div class="af-rcard__desc">Chọn thời gian bắt đầu và thời lượng</div></div>
                            </label>
                            <label class="af-rcard">
                                <input type="radio" name="booking_type" value="slot" {{ old('booking_type', $facility->booking_type)=='slot'?'checked':'' }}>
                                <div><div class="af-rcard__title">Theo thời gian cụ thể</div><div class="af-rcard__desc">Đặt lịch giờ bắt đầu – kết thúc</div></div>
                            </label>
                        </div>
                    </div>

                    <div class="af-sub" id="slotSettings" style="display:{{ in_array(old('booking_type', $facility->booking_type),['time_slot','slot'])?'block':'none' }}">
                        <div class="af-field">
                            <label class="af-label">Thời lượng 1 lượt <span class="req">*</span></label>
                            <div class="af-input-wrap">
                                <input type="number" name="slot_duration" class="af-input" value="{{ old('slot_duration', $facility->slot_duration ?? 60) }}">
                                <span class="af-input-wrap__suffix">phút</span>
                            </div>
                        </div>
                        <div class="af-row">
                            <div class="af-field">
                                <label class="af-label">Đặt trước tối thiểu</label>
                                <div class="af-input-wrap">
                                    <input type="number" name="min_advance_booking_hours" class="af-input" value="{{ old('min_advance_booking_hours', $facility->min_advance_booking_hours ?? 2) }}">
                                    <span class="af-input-wrap__suffix">giờ</span>
                                </div>
                            </div>
                            <div class="af-field">
                                <label class="af-label">Đặt trước tối đa</label>
                                <div class="af-input-wrap">
                                    <input type="number" name="max_advance_booking_days" class="af-input" value="{{ old('max_advance_booking_days', $facility->max_advance_booking_days ?? 7) }}">
                                    <span class="af-input-wrap__suffix">ngày</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="af-card" style="margin-top:16px;">
                    <div class="af-card__title">Trạng thái</div>
                    <div class="af-radios">
                        <label class="af-radio"><input type="radio" name="status" value="available" {{ old('status', $facility->status)=='available'?'checked':'' }}> Hoạt động</label>
                        <label class="af-radio"><input type="radio" name="status" value="maintenance" {{ old('status', $facility->status)=='maintenance'?'checked':'' }}> Bảo trì</label>
                        <label class="af-radio"><input type="radio" name="status" value="closed" {{ old('status', $facility->status)=='closed'?'checked':'' }}> Ngừng sử dụng</label>
                    </div>
                </div>

                {{-- Upload thêm ảnh --}}
                <div class="af-card" style="margin-top:16px;">
                    <div class="af-card__title">Thêm ảnh mới</div>
                    <div class="af-upload" onclick="document.getElementById('newImages').click()">
                        <div class="af-upload__text"><strong>Nhấn để chọn ảnh</strong> hoặc kéo thả<br>JPG, PNG, WEBP — tối đa 5MB/ảnh</div>
                        <input type="file" name="new_images[]" id="newImages" multiple accept="image/jpeg,image/png,image/webp" style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                    </div>
                    <div id="newPreviews" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
                </div>
            </div>
        </div>

        <div class="af-footer">
            <a href="{{ portal_route('amenities.index') }}" class="af-btn af-btn--ghost">Hủy</a>
            <button type="submit" class="af-btn af-btn--primary">Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('input[name="fee_type"]').forEach(r=>{
    r.addEventListener('change',function(){ document.getElementById('priceInput').disabled = this.value==='free'; });
});
document.querySelectorAll('input[name="booking_type"]').forEach(r=>{
    r.addEventListener('change',function(){ document.getElementById('slotSettings').style.display = this.value==='none'?'none':'block'; });
});
document.getElementById('blockSelect').addEventListener('change',function(){
    const fs=document.getElementById('floorSelect'); fs.innerHTML='<option value="">Không chọn</option>';
    if(!this.value){fs.disabled=true;return;} fs.disabled=false;
    fetch('/api/floors?block_id='+this.value).then(r=>r.json()).then(floors=>{
        const old=document.getElementById('oldFloorId').value;
        floors.forEach(f=>{fs.innerHTML+=`<option value="${f.id}" ${f.id==old?'selected':''}>${f.name}</option>`;});
    }).catch(()=>{});
});
if(document.getElementById('blockSelect').value) document.getElementById('blockSelect').dispatchEvent(new Event('change'));
document.getElementById('newImages').addEventListener('change',function(){
    const c=document.getElementById('newPreviews');c.innerHTML='';
    [...this.files].slice(0,5).forEach(f=>{const r=new FileReader();r.onload=e=>{c.innerHTML+=`<img src="${e.target.result}" style="width:56px;height:56px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;">`;};r.readAsDataURL(f);});
});
</script>
@endpush
