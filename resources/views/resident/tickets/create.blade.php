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
            <div id="infoBoxComplaint" style="background: #f5f3ff; border-left: 4px solid #7c3aed; padding: 16px; margin-bottom: 24px; border-radius: 4px; font-size: 0.9rem; color: #4c1d95;">
                <strong>Hướng dẫn:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    <li>Mô tả chi tiết sự cố để Ban quản lý xử lý nhanh chóng hơn.</li>
                    <li>Đính kèm ảnh sự cố (nếu có) để minh chứng rõ ràng.</li>
                    <li>Phản ánh sẽ được xử lý trong vòng <strong>24-48 giờ</strong> làm việc.</li>
                </ul>
            </div>
            <div id="infoBoxReport" style="display: none; background: #fef2f2; border-left: 4px solid #dc2626; padding: 16px; margin-bottom: 24px; border-radius: 4px; font-size: 0.9rem; color: #991b1b;">
                <strong>Lưu ý khi tố cáo:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    <li><strong>Bắt buộc đính kèm ảnh/video</strong> làm bằng chứng.</li>
                    <li>Nhập tên người bị tố cáo nếu biết (không bắt buộc).</li>
                    <li>Nếu không biết ai gây ra, Ban quản lý sẽ điều tra qua camera.</li>
                    <li>Người gây ra sẽ phải chịu <strong>chi phí đền bù</strong> nếu xác minh đúng.</li>
                </ul>
            </div>

            <form method="POST"
                  action="{{ route('resident.tickets.store') }}"
                  enctype="multipart/form-data"
                  style="display: flex; flex-direction: column; gap: 1.25rem;">

                @csrf

                {{-- LOẠI PHẢN ÁNH --}}
                <div>
                    <label class="tk-label">
                        Loại phản ánh <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <select name="ticket_type"
                                id="ticketTypeSelect"
                                class="tk-select"
                                required
                                onchange="toggleTicketType()">
                            <option value="complaint" {{ old('ticket_type', 'complaint') === 'complaint' ? 'selected' : '' }}>Phản ánh sự cố</option>
                            <option value="report" {{ old('ticket_type') === 'report' ? 'selected' : '' }}>Tố cáo</option>
                        </select>
                        <div class="tk-chevron">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                </div>

                {{-- NGƯỜI BỊ TỐ CÁO (chỉ hiện khi chọn Tố cáo, không bắt buộc) --}}
                <div id="reportedPersonGroup" style="display: {{ old('ticket_type') === 'report' ? 'block' : 'none' }};">
                    <label class="tk-label">
                        Tên người bị tố cáo <span style="color: #94a3b8; font-weight: 400; font-size: 0.8rem;">(nếu biết)</span>
                    </label>
                    <input type="text"
                           name="reported_person"
                           id="reportedPersonInput"
                           class="tk-input @error('reported_person') tk-input--err @enderror"
                           value="{{ old('reported_person') }}"
                           placeholder="Nhập họ tên người gây ra (bỏ trống nếu không biết)">
                </div>

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

                {{-- ẢNH / VIDEO ĐÍNH KÈM --}}
                <div>
                    <label class="tk-label">
                        Ảnh / Video đính kèm (tùy chọn, tối đa 5 file)
                    </label>

                    <input type="file"
                           name="images[]"
                           id="ticket-images"
                           style="display: none;"
                           accept="image/*,video/mp4,video/quicktime,video/x-msvideo,video/webm"
                           multiple
                           onchange="previewTicketImages(this)">

                    <div class="tk-upload" id="upload-box" onclick="document.getElementById('ticket-images').click()">

                        <div id="upload-placeholder" class="tk-upload__placeholder">
                            <div style="background: #f5f3ff; color: #7c3aed; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <p>Nhấp vào đây để tải ảnh hoặc video sự cố lên</p>
                            <span>Ảnh: JPG, PNG, WEBP (tối đa 20MB) · Video: MP4, MOV, AVI, WEBM (tối đa 20MB) · Tối đa 5 file</span>
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

                    <button type="submit" id="submitBtn" class="tk-btn tk-btn--primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        <span id="submitBtnText">Gửi phản ánh</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
// Biến toàn cục lưu trữ DataTransfer để quản lý danh sách file được chọn
let ticketFilesTransfer = new DataTransfer();

const VIDEO_EXTS = ['mp4', 'mov', 'avi', 'webm'];

function isVideoFile(file) {
    const ext = file.name.split('.').pop().toLowerCase();
    return VIDEO_EXTS.includes(ext) || file.type.startsWith('video/');
}

function previewTicketImages(input) {
    const files = input.files;
    if (!files || files.length === 0) return;

    // Duyệt và thêm các file mới vào DataTransfer (giới hạn tối đa 5)
    for (let i = 0; i < files.length; i++) {
        if (ticketFilesTransfer.files.length >= 5) {
            alert('Chỉ được chọn tối đa 5 file.');
            break;
        }
        ticketFilesTransfer.items.add(files[i]);
    }

    // Cập nhật lại danh sách file cho input để form submit gửi đi đầy đủ
    input.files = ticketFilesTransfer.files;

    // Render giao diện preview
    renderTicketPreviews();
}

function renderTicketPreviews() {
    const previewContainer = document.getElementById('upload-preview');
    const placeholder = document.getElementById('upload-placeholder');

    // Xóa preview cũ trước khi render lại
    previewContainer.innerHTML = '';

    if (ticketFilesTransfer.files.length === 0) {
        placeholder.style.display = 'flex';
        previewContainer.style.display = 'none';
        return;
    }

    placeholder.style.display = 'none';
    previewContainer.style.display = 'grid';

    Array.from(ticketFilesTransfer.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'tk-upload__preview-item';

        if (isVideoFile(file)) {
            // Video preview
            const url = URL.createObjectURL(file);
            div.innerHTML = `
                <div style="position: relative; width: 100%; height: 120px; background: #0f172a; border-radius: 10px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <video src="${url}#t=0.5" style="width: 100%; height: 100%; object-fit: cover;" muted playsinline preload="auto"></video>
                    <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                        <div style="background: rgba(124, 58, 237, 0.85); border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#fff" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        </div>
                    </div>
                </div>
                <span class="tk-upload__preview-label">Video ${index + 1}</span>
                <button type="button" class="tk-upload__preview-item-remove" onclick="removeTicketImage(event, ${index})" title="Xóa file này">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            previewContainer.appendChild(div);

            // Đảm bảo video hiển thị thumbnail
            const videoEl = div.querySelector('video');
            videoEl.addEventListener('loadeddata', function() {
                this.currentTime = 0.5;
            });
        } else {
            // Image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Ảnh ${index + 1}">
                    <span class="tk-upload__preview-label">Ảnh ${index + 1}</span>
                    <button type="button" class="tk-upload__preview-item-remove" onclick="removeTicketImage(event, ${index})" title="Xóa file này">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                previewContainer.appendChild(div);
            }
            reader.readAsDataURL(file);
        }
    });

    // Nút thêm file mới
    if (ticketFilesTransfer.files.length < 5) {
        const changeBtn = document.createElement('div');
        changeBtn.className = 'tk-upload__change-btn';
        changeBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Thêm file
        `;
        changeBtn.onclick = function(e) {
            e.stopPropagation();
            document.getElementById('ticket-images').click();
        };
        previewContainer.appendChild(changeBtn);
    }
}

function removeTicketImage(event, index) {
    event.stopPropagation();
    event.preventDefault();

    // Tạo DataTransfer mới, bỏ file tại index
    const newTransfer = new DataTransfer();
    Array.from(ticketFilesTransfer.files).forEach((file, i) => {
        if (i !== index) {
            newTransfer.items.add(file);
        }
    });
    ticketFilesTransfer = newTransfer;

    // Cập nhật lại input file
    document.getElementById('ticket-images').files = ticketFilesTransfer.files;

    // Render lại preview
    renderTicketPreviews();
}

function removeTicketPreviews() {
    ticketFilesTransfer = new DataTransfer();
    document.getElementById('ticket-images').files = ticketFilesTransfer.files;
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('upload-preview').innerHTML = '';
    document.getElementById('upload-placeholder').style.display = 'flex';
}

function toggleTicketType() {
    const type = document.getElementById('ticketTypeSelect').value;
    const reportGroup = document.getElementById('reportedPersonGroup');
    const reportInput = document.getElementById('reportedPersonInput');
    const infoComplaint = document.getElementById('infoBoxComplaint');
    const infoReport = document.getElementById('infoBoxReport');
    const submitText = document.getElementById('submitBtnText');
    const submitBtn = document.getElementById('submitBtn');

    if (type === 'report') {
        reportGroup.style.display = 'block';
        infoComplaint.style.display = 'none';
        infoReport.style.display = 'block';
        submitText.textContent = 'Gửi tố cáo';
        submitBtn.style.background = '#dc2626';
    } else {
        reportGroup.style.display = 'none';
        reportInput.value = '';
        infoComplaint.style.display = 'block';
        infoReport.style.display = 'none';
        submitText.textContent = 'Gửi phản ánh';
        submitBtn.style.background = '';
    }
}

// Khởi tạo trạng thái khi load trang (cho trường hợp old() input)
document.addEventListener('DOMContentLoaded', function() {
    toggleTicketType();
});
</script>

@endsection

