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
        .apt-system-ready {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 16px;
            border-radius: 8px;
            font-size: 13px;
            color: #475569;
            font-weight: 600;
        }
        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse-green 2s infinite;
        }
        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>
@endpush

@section('content')
<div class="apartments-page">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav" style="margin-bottom: 8px;">
        <a href="{{ portal_route('dashboard') }}">Quản lý hạ tầng</a>
        <span class="divider">›</span>
        <a href="{{ portal_route('apartments.index') }}">Danh sách tòa nhà</a>
        <span class="divider">›</span>
        <span class="current">Thêm căn hộ mới</span>
    </nav>

    <form action="{{ portal_route('apartments.store') }}" method="POST" id="apartment_create_form">
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
                <div class="apt-reference-card">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=2070&auto=format&fit=crop" alt="Mẫu tham khảo">
                    <div class="apt-reference-overlay">
                        <h4>Mẫu tham khảo</h4>
                        <p>Căn hộ 2 phòng ngủ - Hiện đại</p>
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

                <div class="apt-system-ready">
                    <span class="pulse-dot"></span>
                    Hệ thống sẵn sàng tiếp nhận dữ liệu
                </div>
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
    });
</script>
@endpush
