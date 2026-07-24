@extends('layouts.admin.master')

@section('page_title', 'Tạo công việc vệ sinh – DomusHub')

@push('styles')
<style>
.ct-form{max-width:680px;}
.ct-form h1{font-size:22px;font-weight:800;color:#0f172a;margin-bottom:6px;}
.ct-form p{font-size:13px;color:#64748b;margin-bottom:24px;}
.ct-card{background:white;border-radius:10px;padding:28px;box-shadow:0 2px 8px rgba(0,0,0,.04);}
.fg{margin-bottom:18px;}
.fg label{display:block;font-size:12.5px;font-weight:600;color:#334155;margin-bottom:6px;}
.fg label .req{color:#dc2626;}
.fg input,.fg select,.fg textarea{width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:10px 14px;font-size:13px;color:#0f172a;font-family:inherit;background:#fafbfc;transition:.2s;}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:#082B7A;box-shadow:0 0 0 3px rgba(8,43,122,.08);background:white;}
.fg textarea{min-height:90px;resize:vertical;}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.fg-row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
.fg small{display:block;font-size:11px;color:#94a3b8;margin-top:4px;}
.form-actions{display:flex;gap:10px;margin-top:24px;}
.btn-save{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#082B7A,#1a3f9c);color:white;padding:12px 24px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;transition:.2s;}
.btn-save:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(8,43,122,.25);}
.btn-back{display:inline-flex;align-items:center;gap:8px;background:#f1f5f9;color:#475569;padding:12px 24px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:.2s;}
.btn-back:hover{background:#e2e8f0;}
.alert-err{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;background:#fee2e2;color:#991b1b;border:1px solid #fecaca;}
</style>
@endpush

@section('content')
<div class="ct-form">
    <h1>Tạo công việc vệ sinh mới</h1>
    <p>Giao công việc cho nhân viên vệ sinh</p>

    @if($errors->any())
    <div class="alert-err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
    @endif

    <div class="ct-card">
        <form method="POST" action="{{ portal_route('cleaning-tasks.store') }}">
            @csrf

            <div class="fg">
                <label>Tiêu đề công việc <span class="req">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="VD: Vệ sinh sảnh chính" required>
            </div>

            <div class="fg-row">
                <div class="fg">
                    <label>Giao cho <span class="req">*</span></label>
                    <select name="assigned_to" required>
                        <option value="">Chọn nhân viên</option>
                        @foreach($cleaningStaff as $staff)
                        <option value="{{ $staff->id }}" {{ old('assigned_to') == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label>Ngày thực hiện <span class="req">*</span></label>
                    <input type="date" name="task_date" value="{{ old('task_date', now()->format('Y-m-d')) }}" required>
                </div>
            </div>

            <div class="fg-row">
                <div class="fg">
                    <label>Khu vực <span class="req">*</span></label>
                    <input type="text" name="area" value="{{ old('area') }}" placeholder="VD: Tầng trệt - Sảnh chính" required>
                </div>
                <div class="fg">
                    <label>Nhóm khu vực</label>
                    <input type="text" name="area_group" value="{{ old('area_group') }}" placeholder="VD: Tầng trệt & Thang máy">
                </div>
            </div>

            <div class="fg-row3">
                <div class="fg">
                    <label>Giờ bắt đầu</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}">
                </div>
                <div class="fg">
                    <label>Giờ kết thúc</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}">
                </div>
                <div class="fg">
                    <label>Độ ưu tiên <span class="req">*</span></label>
                    <select name="priority" required>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                    </select>
                </div>
            </div>

            <div class="fg">
                <label>Mô tả</label>
                <textarea name="description" placeholder="Mô tả chi tiết công việc cần thực hiện...">{{ old('description') }}</textarea>
            </div>

            <div class="fg">
                <label>Danh sách kiểm tra (Checklist)</label>
                <textarea name="checklist" placeholder="Mỗi dòng là một bước kiểm tra&#10;VD:&#10;Quét sàn&#10;Lau sàn&#10;Đổ rác">{{ old('checklist') }}</textarea>
                <small>Mỗi dòng sẽ thành 1 mục kiểm tra cho nhân viên</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="fa-solid fa-paper-plane"></i> Tạo công việc</button>
                <a href="{{ portal_route('cleaning-tasks.index') }}" class="btn-back">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection
