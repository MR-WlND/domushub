@extends('layouts.resident.master')

@section('title', 'Gửi phản ánh – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
@endpush

@section('content')
<div class="tk">

    {{-- HEADER --}}
    <div class="tk__header">
        <div>
            <p class="tk__eyebrow">Hệ thống phản ánh</p>
            <h1 class="tk__title">Gửi phản ánh mới</h1>
        </div>

        <a href="{{ route('resident.tickets.index') }}"
           class="tk-btn tk-btn--outline" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- FORM PANEL --}}
    <div class="tk-panel">
        <div class="tk-panel__body">

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="tk-alert tk-alert--error" style="margin-bottom:1.5rem; display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Vui lòng hoàn thiện đúng các thông tin sau:</span>
                    </div>
                    <ul style="margin: 4px 0 0 24px; padding: 0; font-size: 0.9rem; line-height: 1.5;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- INFO BOX --}}
            <div style="background: #f5f3ff; border-left: 4px solid #7c3aed; padding: 16px; margin-bottom: 24px; border-radius: 4px; font-size: 0.9rem; color: #4c1d95;">
                <strong>Hướng dẫn:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    <li>Mô tả chi tiết sự cố để Ban quản lý xử lý nhanh chóng hơn.</li>
                    <li>Đính kèm ảnh sự cố (nếu có) để minh chứng rõ ràng.</li>
                    <li>Phản ánh sẽ được xử lý trong vòng <strong>24-48 giờ</strong> làm việc.</li>
                </ul>
            </div>

            <form method="POST"
                  action="{{ route('resident.tickets.store') }}"
                  enctype="multipart/form-data"
                  style="display: flex; flex-direction: column; gap: 1.25rem;">

                @csrf

                {{-- TIÊU ĐỀ --}}
                <div>
                    <label class="tk-label">
                        Tiêu đề phản ánh <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           class="tk-input @error('title') tk-input--err @enderror"
                           value="{{ old('title') }}"
                           placeholder="Ví dụ: Ống nước bị rò rỉ tại phòng ngủ"
                           required>
                </div>

                {{-- MÔ TẢ --}}
                <div>
                    <label class="tk-label">
                        Mô tả chi tiết <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="description"
                              class="tk-textarea @error('description') tk-textarea--err @enderror"
                              placeholder="Mô tả chi tiết sự cố: vị trí, thời gian phát hiện, mức độ ảnh hưởng..."
                              rows="5"
                              required>{{ old('description') }}</textarea>
                </div>

                {{-- MỨC ĐỘ ƯU TIÊN --}}
                <div>
                    <label class="tk-label">
                        Mức độ ưu tiên <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <select name="priority"
                                class="tk-select @error('priority') tk-input--err @enderror"
                                required>
                            <option value="" disabled {{ old('priority') ? '' : 'selected' }}>-- Chọn mức độ --</option>
                            <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Thấp – Không gấp</option>
                            <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Trung bình – Cần xử lý sớm</option>
                            <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>Cao – Ảnh hưởng sinh hoạt</option>
                            <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Khẩn cấp – Cần xử lý ngay</option>
                        </select>
                        <div class="tk-chevron">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                </div>

                {{-- ẢNH ĐÍNH KÈM (nhiều ảnh) --}}
                <div>
                    <label class="tk-label">
                        Ảnh đính kèm (tùy chọn, tối đa 5 ảnh)
                    </label>

                    <input type="file"
                           name="images[]"
                           id="ticket-images"
                           style="display: none;"
                           accept="image/*"
                           multiple
                           onchange="previewTicketImages(this)">

                    <div class="tk-upload" id="upload-box" onclick="document.getElementById('ticket-images').click()">

                        <div id="upload-placeholder" class="tk-upload__placeholder">
                            <div style="background: #f5f3ff; color: #7c3aed; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <p>Nhấp vào đây để tải ảnh sự cố lên</p>
                            <span>Định dạng hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB/ảnh, tối đa 5 ảnh)</span>
                        </div>

                        <div id="upload-preview" class="tk-upload__preview-grid" style="display: none;" onclick="event.stopPropagation();">
                        </div>
                    </div>
                </div>

                {{-- ACTION FOOTER --}}
                <div style="margin-top: 0.5rem; display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('resident.tickets.index') }}" class="tk-btn tk-btn--outline">
                        Hủy bỏ
                    </a>

                    <button type="submit" class="tk-btn tk-btn--primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        Gửi phản ánh
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
function previewTicketImages(input) {
    const files = input.files;
    const previewContainer = document.getElementById('upload-preview');
    const placeholder = document.getElementById('upload-placeholder');

    if (files.length > 5) {
        alert('Chỉ được chọn tối đa 5 ảnh.');
        input.value = '';
        return;
    }

    if (files.length > 0) {
        previewContainer.innerHTML = '';
        placeholder.style.display = 'none';
        previewContainer.style.display = 'grid';

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'tk-upload__preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Ảnh ${index + 1}">
                    <span class="tk-upload__preview-label">Ảnh ${index + 1}</span>
                `;
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        });

        // Nút thay đổi ảnh
        const changeBtn = document.createElement('div');
        changeBtn.className = 'tk-upload__change-btn';
        changeBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Chọn lại ảnh
        `;
        changeBtn.onclick = function(e) {
            e.stopPropagation();
            removeTicketPreviews();
            document.getElementById('ticket-images').click();
        };
        previewContainer.appendChild(changeBtn);
    }
}

function removeTicketPreviews() {
    document.getElementById('ticket-images').value = "";
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('upload-preview').innerHTML = '';
    document.getElementById('upload-placeholder').style.display = 'flex';
}
</script>

@endsection

