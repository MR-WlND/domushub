@extends('layouts.receptionist.master')

@section('page_title', 'Nhận bưu phẩm mới – Lễ tân DomusHub')



@section('content')
<div style="max-width:700px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('receptionist.parcels.index') }}" style="font-size:14px;color:#0b57d0;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-weight:600;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card" style="padding:32px;">
        <h1 style="font-size:20px;font-weight:700;color:#0b1c30;margin-bottom:24px;display:flex;align-items:center;gap:10px;">Nhận bưu phẩm mới</h1>

        <form method="POST" action="{{ route('receptionist.parcels.store') }}">
            @csrf

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Căn hộ <span style="color:#ba1a1a;">*</span></label>
                <select name="apartment_id" class="form-input {{ $errors->has('apartment_id') ? 'is-invalid' : '' }}" required>
                    <option value="">-- Chọn căn hộ --</option>
                    @foreach($apartments as $apt)
                    <option value="{{ $apt->id }}" {{ old('apartment_id') == $apt->id ? 'selected' : '' }}>
                        {{ $apt->apartment_number }}
                        @if($apt->floor) (Tầng {{ $apt->floor->floor_number }}) @endif
                    </option>
                    @endforeach
                </select>
                @error('apartment_id')<div style="font-size:12px;color:#ba1a1a;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Tên người gửi <span style="color:#ba1a1a;">*</span></label>
                <input type="text" name="sender_name" class="form-input {{ $errors->has('sender_name') ? 'is-invalid' : '' }}"
                       value="{{ old('sender_name') }}" placeholder="VD: Nguyễn Văn A / Shopee / Lazada..." required>
                @error('sender_name')<div style="font-size:12px;color:#ba1a1a;margin-top:4px;">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label">Mã vận đơn</label>
                    <input type="text" name="tracking_code" class="form-input"
                           value="{{ old('tracking_code') }}" placeholder="VD: GHN12345678">
                </div>
                <div class="form-group">
                    <label class="form-label">Đơn vị vận chuyển</label>
                    <input type="text" name="carrier" class="form-input"
                           value="{{ old('carrier') }}" placeholder="VD: GHN, GHTK, VNPost...">
                </div>
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Mô tả bưu phẩm</label>
                <textarea name="description" class="form-input" rows="3"
                          placeholder="VD: 1 thùng hàng lớn, đóng gói cẩn thận...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group" style="margin-bottom:24px;">
                <label class="form-label">Ghi chú của lễ tân</label>
                <textarea name="note" class="form-input" rows="2"
                          placeholder="Ghi chú thêm (nếu có)...">{{ old('note') }}</textarea>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-new-broadcast" style="width:auto; margin:0;"><i class="fa-solid fa-check"></i> Xác nhận nhận hàng</button>
                <a href="{{ route('receptionist.parcels.index') }}" style="display:inline-flex;align-items:center;padding:12px 24px;border-radius:8px;background:#f1f5f9;color:#475569;font-weight:600;text-decoration:none;transition:0.2s;">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
