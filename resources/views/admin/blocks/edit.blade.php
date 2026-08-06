@extends('layouts.admin.master')

@section('page_title', 'Sửa Toà nhà')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/blocks/index.css'])
@endpush

@section('content')
<div class="blocks-page">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('blocks.index') }}">Toà nhà</a>
        <span class="divider">/</span>
        <span class="current">Cập nhật</span>
    </nav>

    {{-- Header --}}
    <div class="blocks-page__header">
        <div>
            <h1>Sửa Toà nhà</h1>
            <p class="blocks-page__subtitle">Đang chỉnh sửa tòa nhà: <strong style="color: #0b57d0;">{{ $block->name }}</strong></p>
        </div>
        <div class="blocks-page__actions">
            <a href="{{ portal_route('blocks.show', $block) }}" class="blocks-button blocks-button--view">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                Xem chi tiết
            </a>
            <a href="{{ portal_route('blocks.index') }}" class="blocks-button blocks-button--light">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Main Layout --}}
    <div class="infra-layout">
        {{-- Cột trái (Main Form) --}}
        <div class="infra-layout__main">
            <form action="{{ portal_route('blocks.update', $block) }}" method="POST" enctype="multipart/form-data" id="editBlockForm">
                @csrf
                @method('PUT')

                {{-- Phần 1: Thông tin cơ bản --}}
                <article class="dashboard-card form-card-custom shadow-sm border-light" style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 700; color: #00236f; text-transform: uppercase; letter-spacing: 0.5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        THÔNG TIN CƠ BẢN
                    </div>

                    <div class="form-grid-2">
                        {{-- Tên Toà --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tên tòa nhà <span class="required">*</span></label>
                            <div class="input-wrapper-custom">
                                <input type="text" name="name" value="{{ old('name', $block->name) }}" placeholder="Ví dụ: Tòa A1, Block B..." class="form-input-custom @error('name') input-error @enderror" style="padding-left: 16px !important;" required>
                            </div>
                            @error('name') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>

                        {{-- Mã Toà --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Mã tòa nhà <span class="required">*</span></label>
                            <div class="input-wrapper-custom">
                                <input type="text" name="code" value="{{ old('code', $block->code) }}" placeholder="VÍ DỤ: BUILDING-A1" class="form-input-custom @error('code') input-error @enderror" style="padding-left: 16px !important;" required>
                            </div>
                            @error('code') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    {{-- Trạng thái --}}
                    <div class="form-grid-2" style="margin-top: 15px;">
                        <div class="form-group-custom">
                            <label class="form-label-custom">Trạng thái hoạt động</label>
                            <div class="input-wrapper-custom">
                                <span class="input-icon-custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </span>
                                <select name="status" class="form-input-custom">
                                    <option value="active"       {{ old('status', $block->status) == 'active'       ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="maintenance"  {{ old('status', $block->status) == 'maintenance'  ? 'selected' : '' }}>Bảo trì</option>
                                    <option value="inactive"     {{ old('status', $block->status) == 'inactive'     ? 'selected' : '' }}>Ngưng hoạt động</option>
                                </select>
                            </div>
                        </div>
                        <div></div>
                    </div>

                    <div class="form-grid-2" style="margin-top: 15px;">
                        {{-- Số tầng nổi --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Số tầng nổi</label>
                            <div class="input-wrapper-custom">
                                <span class="input-icon-custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                                </span>
                                <input type="number" name="total_floors" value="{{ old('total_floors', $block->total_floors) }}" placeholder="VD: 25" min="0" class="form-input-custom @error('total_floors') input-error @enderror" required>
                            </div>
                            @error('total_floors') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>

                        {{-- Số tầng hầm --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Số tầng hầm</label>
                            <div class="input-wrapper-custom">
                                <span class="input-icon-custom">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                                </span>
                                <input type="number" name="total_basements" value="{{ old('total_basements', $block->total_basements) }}" placeholder="VD: 2" min="0" class="form-input-custom @error('total_basements') input-error @enderror" required>
                            </div>
                            @error('total_basements') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>
                    </div>

                </article>

                {{-- Phần 2: Cấu trúc tầng mặc định --}}
                <article class="dashboard-card form-card-custom shadow-sm border-light" style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 700; color: #00236f; text-transform: uppercase; letter-spacing: 0.5px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                        CẤU TRÚC TẦNG MẶC ĐỊNH
                    </div>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">Thiết lập này sẽ được áp dụng cho toàn bộ các tầng chưa được định nghĩa riêng biệt.</p>
                    
                    <div style="background: #f1f5f9; padding: 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600; font-size: 14px; color: #0f172a;">Số căn hộ mỗi tầng</div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Hệ thống sẽ tự tạo số phòng dựa trên con số này (101, 102...)</div>
                        </div>
                        <input type="number" id="input-apts-per-floor" name="apartments_per_floor" value="{{ old('apartments_per_floor', $block->apartments_per_floor ?? 12) }}" style="width: 80px; text-align: center; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; font-weight: 700; color: #0b57d0;">
                    </div>
                    
                    <div style="margin-top: 16px; font-size: 12px; color: #94a3b8;">
                        <span>* Có thể tùy chỉnh chi tiết từng tầng sau khi lưu tòa nhà.</span>
                    </div>
                </article>

            </form>
        </div>

        {{-- Cột phải (Sidebar) --}}
        <div class="infra-layout__sidebar infra-sidebar">
            
            {{-- Ảnh phối cảnh --}}
            <article class="dashboard-card shadow-sm border-light" style="padding: 20px; border-radius: 10px; background: #fff;">
                <h4 style="font-size: 15px; font-weight: 700; color: #00236f; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Ảnh phối cảnh
                </h4>
                
                <div onclick="document.getElementById('blockImage').click()" style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 30px 20px; text-align: center; cursor: pointer; background: #f8fafc; margin-bottom: 16px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2" style="margin-bottom: 8px; margin-left: auto; margin-right: auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    <div style="font-size: 13px; font-weight: 600; color: #334155;">Click để tải ảnh lên</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">PNG, JPG TỐI ĐA 5MB (16:9 KHUYÊN DÙNG)</div>
                </div>
                
                <input type="file" name="image" id="blockImage" style="display: none;" accept="image/png, image/jpeg" onchange="previewImage(event)">

                <div style="font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 8px;">Xem trước (Nếu có):</div>
                <div id="imagePreviewContainer" style="border-radius: 6px; overflow: hidden; background: #f1f5f9; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
                    @if($block->image)
                        <img src="{{ asset('storage/' . $block->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span style="color: #94a3b8; font-size: 12px;">Chưa có ảnh</span>
                    @endif
                </div>
            </article>

            {{-- Thông tin tóm tắt --}}
            <article class="dashboard-card shadow-sm border-light" style="background: #eef2ff; border-color: #c7d2fe; padding: 20px; border-radius: 10px;">
                <h4 style="font-size: 12px; font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 16px 0; border-bottom: 1px solid #c7d2fe; padding-bottom: 8px;">Thông tin tóm tắt</h4>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; color: #334155;">
                    <span>Căn hộ / Tầng:</span>
                    <strong id="summary-apts-per-floor" style="color: #0f172a;">{{ old('apartments_per_floor', $block->apartments_per_floor ?? 12) }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 13px; color: #334155;">
                    <span>Tổng số tầng:</span>
                    <strong id="summary-floors" style="color: #0f172a;">{{ (old('total_floors', $block->total_floors) ?? 0) + (old('total_basements', $block->total_basements) ?? 0) }}</strong>
                </div>
                
                <div style="display: flex; justify-content: space-between; padding-top: 16px; border-top: 1px solid #c7d2fe; font-size: 14px;">
                    <span style="font-weight: 600; color: #475569;">Quy mô căn hộ:</span>
                    <strong id="summary-total-apts" style="font-size: 16px; color: #00236f;">{{ ((old('total_floors', $block->total_floors) ?? 0) + (old('total_basements', $block->total_basements) ?? 0)) * old('apartments_per_floor', $block->apartments_per_floor ?? 12) }}</strong>
                </div>
            </article>

        </div>
    </div>
    
    {{-- Actions --}}
    <div class="blocks-page__actions" style="justify-content: flex-end; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
        <a href="{{ portal_route('blocks.index') }}" class="blocks-button blocks-button--light" style="margin-right: 12px;">Hủy</a>
        <button type="submit" form="editBlockForm" class="blocks-button blocks-button--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
            Xác nhận cập nhật
        </button>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputTotalFloors = document.querySelector('input[name="total_floors"]');
        const inputTotalBasements = document.querySelector('input[name="total_basements"]');
        const inputAptsPerFloor = document.getElementById('input-apts-per-floor');
        
        const summaryFloors = document.getElementById('summary-floors');
        const summaryAptsPerFloor = document.getElementById('summary-apts-per-floor');
        const summaryTotalApts = document.getElementById('summary-total-apts');

        function calculateTotals() {
            const above = parseInt(inputTotalFloors?.value) || 0;
            const below = parseInt(inputTotalBasements?.value) || 0;
            const totalFloors = above + below;
            
            const apts = parseInt(inputAptsPerFloor?.value) || 0;
            const total = totalFloors * apts;

            // Update summary
            if(summaryFloors) summaryFloors.textContent = totalFloors;
            if(summaryAptsPerFloor) summaryAptsPerFloor.textContent = apts;
            if(summaryTotalApts) summaryTotalApts.textContent = total;
        }

        if(inputTotalFloors && inputTotalBasements && inputAptsPerFloor) {
            inputTotalFloors.addEventListener('input', calculateTotals);
            inputTotalBasements.addEventListener('input', calculateTotals);
            inputAptsPerFloor.addEventListener('input', calculateTotals);
            
            // Initial calculation
            calculateTotals();
        }
    });

    function previewImage(event) {
        const file = event.target.files[0];
        const container = document.getElementById('imagePreviewContainer');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                container.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
            }
            reader.readAsDataURL(file);
        } else {
            container.innerHTML = '<span style="color: #94a3b8; font-size: 12px;">Chưa có ảnh</span>';
        }
    }
</script>
@endpush
@endsection