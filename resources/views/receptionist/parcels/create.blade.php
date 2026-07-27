@extends('layouts.receptionist.master')

@section('page_title', 'Nhận bưu phẩm mới – Lễ tân DomusHub')

@push('styles')
<style>
    .form-card{background:white;border-radius:16px;padding:32px;box-shadow:0 2px 8px rgba(123,63,228,.06);max-width:640px;}
    .form-card h1{font-size:20px;font-weight:800;color:#1B2559;margin-bottom:24px;display:flex;align-items:center;gap:10px;}
    .form-group{margin-bottom:20px;}
    .form-group label{display:block;font-size:13px;font-weight:600;color:#1B2559;margin-bottom:8px;}
    .form-group label .req{color:#EE5D50;}
    .form-control{width:100%;padding:11px 14px;border:1.5px solid #E9EDF7;border-radius:10px;font-size:13.5px;font-family:inherit;color:#1B2559;outline:none;transition:.2s;background:white;}
    .form-control:focus{border-color:#7B3FE4;box-shadow:0 0 0 3px rgba(123,63,228,.1);}
    .form-control.is-invalid{border-color:#EE5D50;}
    .invalid-feedback{font-size:12px;color:#EE5D50;margin-top:5px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .btn-submit{padding:12px 28px;border-radius:10px;background:#7B3FE4;color:white;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:.2s;}
    .btn-submit:hover{background:#6930cc;transform:translateY(-1px);box-shadow:0 6px 16px rgba(123,63,228,.3);}
    .btn-cancel{padding:12px 20px;border-radius:10px;background:#F4F7FE;color:#707EAE;font-size:14px;font-weight:600;border:none;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;}
    .btn-cancel:hover{background:#e5e9f5;}
</style>
@endpush

@section('content')
<div style="max-width:640px;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('receptionist.parcels.index') }}" style="font-size:13px;color:#7B3FE4;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="form-card">
        <h1><i class="fa-solid fa-box" style="color:#7B3FE4;"></i> Nhận bưu phẩm mới</h1>

        <form method="POST" action="{{ route('receptionist.parcels.store') }}">
            @csrf

            <div class="form-group">
                <label>Căn hộ <span class="req">*</span></label>
                <select name="apartment_id" class="form-control {{ $errors->has('apartment_id') ? 'is-invalid' : '' }}" required>
                    <option value="">-- Chọn căn hộ --</option>
                    @foreach($apartments as $apt)
                    <option value="{{ $apt->id }}" {{ old('apartment_id') == $apt->id ? 'selected' : '' }}>
                        {{ $apt->apartment_number }}
                        @if($apt->floor) (Tầng {{ $apt->floor->floor_number }}) @endif
                    </option>
                    @endforeach
                </select>
                @error('apartment_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Tên người gửi <span class="req">*</span></label>
                <input type="text" name="sender_name" class="form-control {{ $errors->has('sender_name') ? 'is-invalid' : '' }}"
                       value="{{ old('sender_name') }}" placeholder="VD: Nguyễn Văn A / Shopee / Lazada..." required>
                @error('sender_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Mã vận đơn</label>
                    <input type="text" name="tracking_code" class="form-control"
                           value="{{ old('tracking_code') }}" placeholder="VD: GHN12345678">
                </div>
                <div class="form-group">
                    <label>Đơn vị vận chuyển</label>
                    <input type="text" name="carrier" class="form-control"
                           value="{{ old('carrier') }}" placeholder="VD: GHN, GHTK, VNPost...">
                </div>
            </div>

            <div class="form-group">
                <label>Mô tả bưu phẩm</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="VD: 1 thùng hàng lớn, đóng gói cẩn thận...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label>Ghi chú của lễ tân</label>
                <textarea name="note" class="form-control" rows="2"
                          placeholder="Ghi chú thêm (nếu có)...">{{ old('note') }}</textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Xác nhận nhận hàng</button>
                <a href="{{ route('receptionist.parcels.index') }}" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
