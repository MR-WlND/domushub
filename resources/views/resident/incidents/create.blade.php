@extends('layouts.resident.master')

@section('title', 'Gửi phản ánh – DomusHub')

@section('content')
<div class="incidents-page">
    <div class="incidents-page__hero">
        <div>
            <p class="incidents-page__eyebrow">Phản ánh & Hỗ trợ</p>
            <h1 class="incidents-page__title">Gửi phản ánh mới</h1>
            <p class="incidents-page__subtitle">Mô tả chi tiết sự cố để nhóm quản lý có thể hỗ trợ bạn nhanh nhất.</p>
        </div>
        <a href="{{ route('resident.incidents.index') }}" class="btn-outline" id="btn-back-incidents">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Quay lại
        </a>
    </div>

    <div class="incident-form-wrapper">
        <form action="{{ route('resident.incidents.store') }}" method="POST" enctype="multipart/form-data"
            class="incident-form" id="form-incident-create">
            @csrf

            @if ($errors->any())
                <div class="alert alert--error">
                    <ul style="margin:0; padding-left:1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tiêu đề --}}
            <div class="form-group">
                <label class="form-label" for="title">Tiêu đề phản ánh <span class="form-required">*</span></label>
                <input type="text" id="title" name="title" class="form-input @error('title') form-input--error @enderror"
                    value="{{ old('title') }}" placeholder="VD: Đường ống nước tầng 3 bị rò rỉ" maxlength="200" required>
                @error('title')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Loại sự cố & Mức ưu tiên --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="category">Loại sự cố <span class="form-required">*</span></label>
                    <select id="category" name="category" class="form-select @error('category') form-input--error @enderror" required>
                        <option value="">-- Chọn loại sự cố --</option>
                        <option value="electrical" {{ old('category') === 'electrical' ? 'selected' : '' }}>⚡ Điện</option>
                        <option value="plumbing" {{ old('category') === 'plumbing' ? 'selected' : '' }}>💧 Nước / Ống nước</option>
                        <option value="elevator" {{ old('category') === 'elevator' ? 'selected' : '' }}>🛗 Thang máy</option>
                        <option value="cleaning" {{ old('category') === 'cleaning' ? 'selected' : '' }}>🧹 Vệ sinh</option>
                        <option value="security" {{ old('category') === 'security' ? 'selected' : '' }}>🔒 An ninh</option>
                        <option value="infrastructure" {{ old('category') === 'infrastructure' ? 'selected' : '' }}>🏗️ Hạ tầng / Kết cấu</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>📋 Khác</option>
                    </select>
                    @error('category')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="priority">Mức độ ưu tiên <span class="form-required">*</span></label>
                    <select id="priority" name="priority" class="form-select @error('priority') form-input--error @enderror" required>
                        <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>🟢 Thấp</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>🟡 Trung bình</option>
                        <option value="high" {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>🟠 Cao</option>
                        <option value="urgent" {{ old('priority', 'medium') === 'urgent' ? 'selected' : '' }}>🔴 Khẩn cấp</option>
                    </select>
                    @error('priority')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Mô tả --}}
            <div class="form-group">
                <label class="form-label" for="description">Mô tả chi tiết <span class="form-required">*</span></label>
                <textarea id="description" name="description"
                    class="form-textarea @error('description') form-input--error @enderror"
                    rows="5" placeholder="Mô tả chi tiết vị trí, triệu chứng và mức độ ảnh hưởng của sự cố..."
                    maxlength="2000" required>{{ old('description') }}</textarea>
                <div class="form-hint">Tối đa 2000 ký tự. Mô tả càng chi tiết, nhóm kỹ thuật càng xử lý nhanh hơn.</div>
                @error('description')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Upload ảnh --}}
            <div class="form-group">
                <label class="form-label" for="images">Ảnh đính kèm (không bắt buộc)</label>
                <div class="file-upload-area" id="file-upload-area">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <p>Kéo thả ảnh vào đây hoặc <label for="images" style="color:var(--color-primary);cursor:pointer;text-decoration:underline;">chọn file</label></p>
                    <p class="form-hint">PNG, JPG, WEBP – Tối đa 5MB mỗi ảnh</p>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" style="display:none">
                </div>
                <div class="file-preview" id="file-preview"></div>
            </div>

            {{-- Submit --}}
            <div class="form-actions">
                <a href="{{ route('resident.incidents.index') }}" class="btn-outline">Hủy</a>
                <button type="submit" class="btn-primary" id="btn-submit-incident">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Gửi phản ánh
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const fileInput = document.getElementById('images');
    const preview = document.getElementById('file-preview');
    const dropArea = document.getElementById('file-upload-area');

    fileInput.addEventListener('change', renderPreviews);

    ['dragover', 'dragenter'].forEach(ev => {
        dropArea.addEventListener(ev, e => { e.preventDefault(); dropArea.classList.add('file-upload-area--active'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
        dropArea.addEventListener(ev, e => { e.preventDefault(); dropArea.classList.remove('file-upload-area--active'); });
    });
    dropArea.addEventListener('drop', e => {
        fileInput.files = e.dataTransfer.files;
        renderPreviews();
    });

    function renderPreviews() {
        preview.innerHTML = '';
        Array.from(fileInput.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'file-preview__item';
                div.innerHTML = `<img src="${e.target.result}" alt="${file.name}"><span>${file.name}</span>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush
