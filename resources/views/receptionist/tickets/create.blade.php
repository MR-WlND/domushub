@extends('layouts.receptionist.master')

@section('page_title', 'Tạo phản ánh mới – Lễ tân DomusHub')

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
    .btn-submit{padding:12px 28px;border-radius:10px;background:#3652D9;color:white;font-size:14px;font-weight:700;border:none;cursor:pointer;font-family:inherit;transition:.2s;}
    .btn-submit:hover{background:#2b42b1;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}
    .btn-cancel{padding:12px 20px;border-radius:10px;background:#F4F7FE;color:#707EAE;font-size:14px;font-weight:600;border:none;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-flex;align-items:center;}
    .btn-cancel:hover{background:#e5e9f5;}
</style>
@endpush

@section('content')
<div style="max-width:640px;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('receptionist.tickets.index') }}" style="font-size:13px;color:#3652D9;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="form-card">
        <h1>Tạo phản ánh mới</h1>
        <p style="font-size:13px;color:#707EAE;margin-bottom:20px;">Lễ tân tạo phản ánh, sự cố trên hệ thống thay mặt cho cư dân trực tiếp tại sảnh.</p>

        <form method="POST" action="{{ route('receptionist.tickets.store') }}">
            @csrf

            <div class="form-group">
                <label>Căn hộ yêu cầu <span class="req">*</span></label>
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

            <div class="form-row">
                <div class="form-group">
                    <label>Loại phản ánh <span class="req">*</span></label>
                    <select name="ticket_type" class="form-control" required>
                        <option value="complaint" {{ old('ticket_type') == 'complaint' ? 'selected' : '' }}>Sự cố / Khiếu nại</option>
                        <option value="report" {{ old('ticket_type') == 'report' ? 'selected' : '' }}>Tố cáo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Mức độ ưu tiên <span class="req">*</span></label>
                    <select name="priority" class="form-control" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Tiêu đề phản ánh <span class="req">*</span></label>
                <input type="text" name="title" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}"
                       value="{{ old('title') }}" placeholder="VD: Ống nước rò rỉ, Hỏng bóng đèn..." required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Mô tả chi tiết <span class="req">*</span></label>
                <textarea name="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="4"
                          placeholder="Mô tả chi tiết sự cố mà cư dân báo cáo..." required>{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:12px;margin-top:8px;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Lưu phản ánh</button>
                <a href="{{ route('receptionist.tickets.index') }}" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
