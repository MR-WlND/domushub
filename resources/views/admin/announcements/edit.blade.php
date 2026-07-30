@extends('layouts.admin.master')

@section('page_title', 'Chỉnh sửa thông báo')

@push('styles')
    @vite(['resources/css/pages/admin/announcements/index.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="announcements-page">
        {{-- Header --}}
        <div class="announcements-page__header">
            <div>
                <p class="announcements-page__eyebrow">Tương tác & Bảng tin</p>
                <h1>Chỉnh sửa thông báo</h1>
            </div>
            <div>
                <a href="{{ portal_route('announcements.index') }}" class="announcements-btn">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="announcements-alert announcements-alert--success" style="background: #fef2f2; color: #b91c1c; border-color: #fee2e2;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form --}}
        <div class="announcement-form-card">
            <form action="{{ portal_route('announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    {{-- Left column - Main details --}}
                    <div>
                        <div class="form-group">
                            <label for="title">Tiêu đề thông báo <span style="color: red;">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Nhập tiêu đề thông báo..." value="{{ old('title', $announcement->title) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="editor-announcement-content">Nội dung chi tiết <span style="color: red;">*</span></label>
                            <textarea name="content" id="editor-announcement-content" rows="12" style="display:none;">{{ old('content', $announcement->content) }}</textarea>
                        </div>
                    </div>

                    {{-- Right column - Settings --}}
                    <div>
                        <div class="form-group">
                            <label for="category">Phân loại thông báo <span style="color: red;">*</span></label>
                            <select name="category" id="category" class="form-control" required>
                                <option value="general" @selected(old('category', $announcement->category) === 'general')>Tin tức chung</option>
                                <option value="maintenance" @selected(old('category', $announcement->category) === 'maintenance')>Bảo trì kỹ thuật (điện, nước, thang máy...)</option>
                                <option value="warning" @selected(old('category', $announcement->category) === 'warning')>Cảnh báo khẩn cấp (cháy nổ, an ninh...)</option>
                                <option value="event" @selected(old('category', $announcement->category) === 'event')>Sự kiện chung cư (họp, lễ hội...)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Trạng thái phát hành <span style="color: red;">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="published" @selected(old('status', $announcement->status) === 'published')>Công bố ngay lập tức</option>
                                <option value="draft" @selected(old('status', $announcement->status) === 'draft')>Bản nháp</option>
                                <option value="archived" @selected(old('status', $announcement->status) === 'archived')>Lưu trữ ẩn</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="pinned" value="1" @checked(old('pinned', $announcement->pinned))>
                                <span>Ghim lên đầu bảng tin</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_popup" value="1" @checked(old('is_popup', $announcement->is_popup))>
                                <span>Hiển thị dạng Popup khẩn cấp</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Ảnh đính kèm thông báo</label>
                            <div class="image-upload-wrapper">
                                <input type="file" name="image" id="image" accept="image/*" style="position: absolute; top:0; left:0; width:100%; height:100%; opacity:0; cursor:pointer;" onchange="previewImage(event)">
                                
                                @if($announcement->image_path)
                                    <div class="image-upload-placeholder" id="upload-placeholder" style="display: none;">
                                        <i class="fa-regular fa-image"></i>
                                        <span>Kéo thả hoặc click để chọn ảnh mới</span>
                                    </div>
                                    <img id="image-preview" src="{{ asset('storage/' . $announcement->image_path) }}" class="image-upload-preview" alt="Preview image">
                                    <div style="margin-top: 10px; text-align: center;" id="remove-image-container">
                                        <label style="font-weight: 500; font-size: 13px; color: #ef4444; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                            <input type="checkbox" name="remove_image" value="1" id="remove-image-checkbox" onchange="toggleRemoveImage(this)"> Xóa hình ảnh hiện tại
                                        </label>
                                    </div>
                                @else
                                    <div class="image-upload-placeholder" id="upload-placeholder">
                                        <i class="fa-regular fa-image"></i>
                                        <span>Kéo thả hoặc click để chọn ảnh</span>
                                        <span style="font-size: 11px; color:#94a3b8;">Dung lượng tối đa 10MB</span>
                                    </div>
                                    <img id="image-preview" class="image-upload-preview" style="display: none;" alt="Preview image">
                                @endif
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: flex-end;">
                            <a href="{{ portal_route('announcements.index') }}" class="announcements-btn">Hủy bỏ</a>
                            <button type="submit" class="announcements-btn announcements-btn--primary">
                                <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#editor-announcement-content'), {
                    toolbar: [
                        'heading', '|', 'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', 'undo', 'redo'
                    ],
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                        ]
                    }
                })
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#editor-announcement-content').value = editor.getData();
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        });

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('upload-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                    
                    // Uncheck remove_image if they select a new file
                    const removeCheckbox = document.getElementById('remove-image-checkbox');
                    if (removeCheckbox) {
                        removeCheckbox.checked = false;
                        preview.style.opacity = '1';
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleRemoveImage(checkbox) {
            const preview = document.getElementById('image-preview');
            if (checkbox.checked) {
                preview.style.opacity = '0.3';
            } else {
                preview.style.opacity = '1';
            }
        }
    </script>
@endpush
