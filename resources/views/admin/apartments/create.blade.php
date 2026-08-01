@extends('layouts.admin.master')

@section('page_title', 'Tạo Căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/apartments/index.css'])
    <style>
        /* New Layout Styles */
        .apt-create-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 992px) {
            .apt-create-layout {
                grid-template-columns: 1fr;
            }
        }
        .apt-create-main, .apt-create-sidebar {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .section-icon-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #eef2ff;
            color: #0b57d0;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .input-suffix-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-suffix-wrapper input {
            padding-right: 40px !important;
        }
        .input-suffix-wrapper .suffix {
            position: absolute;
            right: 14px;
            color: #64748b;
            font-size: 14px;
            pointer-events: none;
        }
        .form-input-noicon {
            padding-left: 14px !important;
        }
        .apt-reference-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 220px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .apt-reference-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .apt-reference-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px 16px 16px;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
        }
        .apt-reference-overlay h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }
        .apt-reference-overlay p {
            margin: 4px 0 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .apt-image-upload-wrapper {
            margin-bottom: 24px;
        }
        .upload-area {
            position: relative;
            width: 100%;
            height: 220px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .upload-area:hover, .upload-area.dragover {
            border-color: #0b57d0;
            background-color: #eff6ff;
        }
        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: none;
        }
        .upload-text {
            margin: 12px 0 4px;
            font-weight: 600;
            color: #475569;
            font-size: 15px;
        }
        .upload-subtext {
            margin: 0;
            color: #94a3b8;
            font-size: 13px;
        }
        .upload-area img#image_preview {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            z-index: 10;
        }
        .remove-image-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            color: #ef4444;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .remove-image-btn:hover {
            background: #ef4444;
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="apartments-page">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('apartments.index') }}">Căn hộ</a>
        <span class="divider">/</span>
        <span class="current">Thêm mới</span>
    </nav>

    <form action="{{ portal_route('apartments.store') }}" method="POST" id="apartment_create_form" enctype="multipart/form-data">
        @csrf

        @php
            $selectedFloorId = old('floor_id', $selectedFloorId ?? null);
            $selectedBlockId = null;
            if ($selectedFloorId) {
                $selectedFloor = $floors->firstWhere('id', $selectedFloorId);
                if ($selectedFloor) {
                    $selectedBlockId = $selectedFloor->block_id;
                }
            }
        @endphp

        {{-- Header --}}
        <div class="apartments-page__header" style="margin-bottom: 24px;">
            <div>
                <h1>Thêm Căn Hộ Mới</h1>
                <p class="apartments-page__subtitle">Vui lòng điền đầy đủ thông tin chi tiết cho căn hộ.</p>
            </div>
            <div class="apartments-page__actions">
                <a href="{{ portal_route('apartments.index') }}" class="apts-button apts-button--edit" style="background: white; border: 1px solid #cbd5e1;">
                    Hủy
                </a>
                <button type="submit" class="apts-button apts-button--primary">
                    Lưu căn hộ
                </button>
            </div>
        </div>

        <div class="apt-create-layout">
            {{-- Cột trái --}}
            <div class="apt-create-main">
                <article class="dashboard-card form-card-custom shadow-sm border-light">
                    <div class="form-section-header" style="border-bottom: none; margin-bottom: 8px;">
                        <span class="section-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </span>
                        <h4>Thông tin cơ bản</h4>
                    </div>

                    <div class="form-grid-2">
                        {{-- Tòa nhà --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tòa nhà</label>
                            <select id="block_select" class="form-input-custom form-input-noicon" required>
                                <option value="">-- Chọn Tòa nhà --</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" {{ $selectedBlockId == $block->id ? 'selected' : '' }}>
                                        {{ $block->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tầng --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tầng</label>
                            <select name="floor_id" id="floor_select" class="form-input-custom form-input-noicon @error('floor_id') input-error @enderror" required disabled>
                                <option value="">-- Chọn Tầng --</option>
                                @foreach($floors as $floor)
                                    <option value="{{ $floor->id }}" data-block-id="{{ $floor->block_id }}" {{ old('floor_id', $selectedFloorId ?? '') == $floor->id ? 'selected' : '' }}>
                                        {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('floor_id')
                                <p class="form-error-custom">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Mã căn hộ --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Mã căn hộ</label>
                            <input type="text" name="apartment_number" value="{{ old('apartment_number') }}" placeholder="VD: A1-1205" class="form-input-custom form-input-noicon @error('apartment_number') input-error @enderror" required>
                            @error('apartment_number')
                                <p class="form-error-custom">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Loại căn hộ --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Loại căn hộ</label>
                            <select name="apartment_type_id" class="form-input-custom form-input-noicon @error('apartment_type_id') input-error @enderror">
                                <option value="">-- Chọn Loại căn hộ --</option>
                                @foreach($apartmentTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('apartment_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('apartment_type_id')
                                <p class="form-error-custom">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Diện tích --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Diện tích (m²)</label>
                            <div class="input-suffix-wrapper">
                                <input type="number" step="0.01" name="area" value="{{ old('area') }}" placeholder="0.0" class="form-input-custom form-input-noicon @error('area') input-error @enderror" required>
                                <span class="suffix">m²</span>
                            </div>
                            @error('area')
                                <p class="form-error-custom">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Empty slot to align with 2-column grid visually --}}
                        <div class="form-group-custom hidden-mobile"></div>
                    </div>
                </article>
            </div>

            {{-- Cột phải --}}
            <div class="apt-create-sidebar">
                <div class="apt-image-upload-wrapper">
                    <div class="form-section-header" style="border-bottom: none; margin-bottom: 12px; padding: 0;">
                        <span class="section-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </span>
                        <h4>Ảnh đại diện (Tùy chọn)</h4>
                    </div>
                    <div class="upload-area" id="upload-area">
                        <input type="file" name="image" id="apartment_image" class="file-input" accept="image/*" hidden>
                        <div class="upload-placeholder" id="upload-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="upload-text">Nhấn để tải ảnh lên</p>
                            <p class="upload-subtext">hoặc kéo thả ảnh vào đây</p>
                        </div>
                        <img id="image_preview" src="" alt="Preview">
                        <button type="button" class="remove-image-btn" id="remove_image" title="Xóa ảnh">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <article class="dashboard-card form-card-custom shadow-sm border-light" style="padding: 24px;">
                    <div class="form-section-header" style="border-bottom: none; margin-bottom: 12px;">
                        <span class="section-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </span>
                        <h4>Ghi chú</h4>
                    </div>
                    <div class="form-group-custom">
                        <textarea name="description" placeholder="Nhập ghi chú chi tiết về tình trạng bàn giao, yêu cầu đặc biệt của chủ đầu tư..." class="form-textarea-custom @error('description') input-error @enderror" rows="5">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="form-error-custom">{{ $message }}</p>
                        @enderror
                    </div>
                </article>


            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const blockSelect = document.getElementById('block_select');
        const floorSelect = document.getElementById('floor_select');
        
        const floorOptions = Array.from(floorSelect.querySelectorAll('option[data-block-id]'));
        
        function updateFloors() {
            const selectedBlockId = blockSelect.value;
            
            floorSelect.innerHTML = '<option value="">-- Chọn Tầng --</option>';
            
            if (selectedBlockId) {
                const filteredOptions = floorOptions.filter(opt => opt.getAttribute('data-block-id') === selectedBlockId);
                
                filteredOptions.forEach(opt => {
                    floorSelect.appendChild(opt);
                });
                
                floorSelect.disabled = false;
            } else {
                floorSelect.disabled = true;
            }
        }
        
        blockSelect.addEventListener('change', function () {
            floorSelect.value = "";
            updateFloors();
        });
        
        if (blockSelect.value) {
            const currentFloorValue = "{{ old('floor_id', $selectedFloorId ?? '') }}";
            updateFloors();
            if (currentFloorValue) {
                floorSelect.value = currentFloorValue;
            }
        }

        // Image upload logic
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('apartment_image');
        const imagePreview = document.getElementById('image_preview');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const removeImageBtn = document.getElementById('remove_image');

        uploadArea.addEventListener('click', () => {
            if (!imagePreview.src || imagePreview.style.display === 'none') {
                fileInput.click();
            }
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                handleFile(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFile(this.files[0]);
            }
        });

        function handleFile(file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadPlaceholder.style.display = 'none';
                    removeImageBtn.style.display = 'flex';
                }
                reader.readAsDataURL(file);
            } else {
                alert('Vui lòng chọn file hình ảnh hợp lệ.');
                fileInput.value = '';
            }
        }

        removeImageBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            fileInput.value = '';
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            uploadPlaceholder.style.display = 'flex';
            removeImageBtn.style.display = 'none';
        });
    });
</script>
@endpush
