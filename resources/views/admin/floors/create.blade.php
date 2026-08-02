@extends('layouts.admin.master')

@section('page_title', 'Tạo Tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/floors/index.css'])
    <style>
        .floors-create-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (min-width: 992px) {
            .floors-create-layout {
                grid-template-columns: 2fr 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="floors-page">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('blocks.index') }}">Tầng</a>
        <span class="divider">/</span>
        <span class="current">Thêm mới</span>
    </nav>

    {{-- Header --}}
    <div class="floors-page__header">
        <div>
            <h1>Tạo Tầng mới</h1>
            <p class="floors-page__subtitle">Thêm tầng mới vào một tòa nhà</p>
        </div>
        <div class="floors-page__actions">
            <a href="{{ portal_route('blocks.index') }}" class="floors-button floors-button--light">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form Layout --}}
    <div class="floors-create-layout">
        {{-- Form Area (Left) --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <form action="{{ portal_route('floors.store') }}" method="POST">
                @csrf
                
                {{-- Card 1: Thông tin cơ bản --}}
                <article class="dashboard-card shadow-sm border-light" style="padding: 24px; border-radius: 12px; background: #fff; margin: 0;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 24px;">Thông tin cơ bản</h3>
                    
                    <div class="form-grid-2">
                        {{-- Tên tầng --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Tên tầng <span class="required">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Tầng 5, Tầng Hầm 1..." class="form-input-custom @error('name') input-error @enderror" required style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px;">
                            @error('name') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>

                        {{-- Chọn Tòa nhà --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Block / Tòa nhà <span class="required">*</span></label>
                            <select name="block_id" id="block_select" class="form-input-custom @error('block_id') input-error @enderror" required style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px;">
                                <option value="">-- Chọn tòa nhà --</option>
                                @foreach($blocks as $block)
                                    <option value="{{ $block->id }}" data-apartments="{{ $block->apartments_per_floor }}" {{ old('block_id', $selectedBlockId ?? '') == $block->id ? 'selected' : '' }}>
                                        {{ $block->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('block_id') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>

                        {{-- Số lượng căn hộ --}}
                        <div class="form-group-custom" id="apartments_count_group">
                            <label class="form-label-custom">Số lượng căn hộ</label>
                            <input type="number" name="number_of_apartments" value="{{ old('number_of_apartments') }}" placeholder="VD: 10, 5..." min="0" max="100" class="form-input-custom @error('number_of_apartments') input-error @enderror" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px;">
                            @error('number_of_apartments') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>

                        {{-- Loại tầng --}}
                        <div class="form-group-custom">
                            <label class="form-label-custom">Loại tầng <span class="required">*</span></label>
                            <select name="floor_type" id="floor_type_select" class="form-input-custom @error('floor_type') input-error @enderror" required style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 16px;">
                                <option value="above_ground" {{ old('floor_type', 'above_ground') == 'above_ground' ? 'selected' : '' }}>Tầng nổi</option>
                                <option value="basement"     {{ old('floor_type') == 'basement' ? 'selected' : '' }}>Tầng hầm</option>
                            </select>
                            @error('floor_type') <p class="form-error-custom">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </article>

                {{-- Card 2: Ghi chú bổ sung --}}
                <article class="dashboard-card shadow-sm border-light" style="padding: 24px; border-radius: 12px; background: #fff; margin: 24px 0;">
                    <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Ghi chú bổ sung</h3>
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Thêm các thông tin mô tả chi tiết hoặc tiện ích của tầng này.</p>
                    
                    <div class="form-group-custom">
                        <textarea name="description" placeholder="Nhập ghi chú..." class="form-textarea-custom" rows="3" style="width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px;">{{ old('description') }}</textarea>
                    </div>
                    <input type="hidden" name="status" value="active">
                </article>

                {{-- Actions --}}
                <div style="display: flex; justify-content: flex-end; gap: 12px; align-items: center;">
                    <a href="{{ portal_route('blocks.index') }}" style="padding: 10px 32px; border: 1px solid #cbd5e1; border-radius: 8px; color: #475569; font-weight: 600; text-decoration: none; background: #fff;">
                        Hủy
                    </a>
                    <button type="submit" style="padding: 10px 32px; background: #0f172a; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        Lưu tầng
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            {{-- Preview Card (Right) --}}
            <article class="dashboard-card shadow-sm border-light" style="position: sticky; top: 24px; padding: 24px; border-radius: 12px; background: #fff; margin: 0;">
                <div class="preview-container">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px; color: #1e293b;">Cấu trúc tòa nhà</h3>
                    
                    <div id="preview_building_wrapper" style="display: none;">
                        <div style="background: #f1f5f9; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px; font-weight: 500; color: #0f172a; margin-bottom: 16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color: #0f172a;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span id="preview_building_name">Tòa nhà</span>
                        </div>

                        <div style="position: relative; padding-left: 16px;">
                            <!-- Timeline line -->
                            <div style="position: absolute; left: 24px; top: 10px; bottom: 10px; width: 2px; background: #cbd5e1; z-index: 1;"></div>
                            
                            <div id="preview_floors_list" style="position: relative; z-index: 2; display: flex; flex-direction: column; gap: 16px;">
                                <!-- Floors will be rendered here by JS -->
                            </div>
                        </div>
                        
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px; border-radius: 8px; margin-top: 24px;">
                            <h4 style="font-size: 14px; font-weight: 600; color: #166534; margin-bottom: 12px;">Tóm tắt cấu trúc:</h4>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: #374151;">
                                <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 4px; height: 4px; border-radius: 50%; background: #166534;"></span> Số lượng căn hộ:</span>
                                <strong id="preview_apts" style="color: #111827;">0</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; color: #374151;">
                                <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 4px; height: 4px; border-radius: 50%; background: #166534;"></span> Dân cư dự kiến:</span>
                                <strong style="color: #111827;"><span id="preview_residents">0</span> người</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px; color: #374151;">
                                <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 4px; height: 4px; border-radius: 50%; background: #166534;"></span> Mã định danh:</span>
                                <strong id="preview_code" style="color: #111827;">-</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div id="preview_empty_state" style="text-align: center; padding: 30px 10px; color: #64748b;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" style="margin: 0 auto 12px; color: #94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        <p>Vui lòng chọn tòa nhà và nhập tên tầng để xem trước cấu trúc.</p>
                    </div>
                </div>
            </article>

            {{-- 3D Model Card (Right Bottom) --}}
            <article class="dashboard-card shadow-sm" style="padding: 24px; border-radius: 12px; background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); margin: 0; color: white; overflow: hidden; position: relative;">
                <div style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; opacity: 0.1; background-image: repeating-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff), repeating-linear-gradient(45deg, #fff 25%, #0f172a 25%, #0f172a 75%, #fff 75%, #fff); background-position: 0 0, 10px 10px; background-size: 20px 20px;"></div>
                <div style="position: relative; z-index: 2;">
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Mô hình 3D</h3>
                    <p style="font-size: 13px; color: #cbd5e1; line-height: 1.5; margin: 0;">Cấu trúc mặt bằng sẽ được tự động tạo sau khi lưu.</p>
                </div>
            </article>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const floorTypeSelect = document.getElementById('floor_type_select');
        const apartmentsCountGroup = document.getElementById('apartments_count_group');
        const apartmentsCountInput = apartmentsCountGroup.querySelector('input');
        const blockSelect = document.getElementById('block_select');
        const floorNameInput = document.querySelector('input[name="name"]');

        const blocksData = {!! json_encode($blocksData) !!};

        function parseFloorNumber(name) {
            if (!name) return null;
            let lowerName = name.toLowerCase().trim();
            if (lowerName.includes('trệt') || lowerName === 'g' || lowerName === 'tầng g') return 0;
            if (lowerName.includes('hầm') || lowerName.includes('basement') || lowerName.startsWith('b')) {
                let match = lowerName.match(/\d+/);
                return match ? -parseInt(match[0]) : -1;
            }
            let match = lowerName.match(/\d+/);
            if (match) return parseInt(match[0]);
            return null;
        }

        function updatePreview() {
            const blockId = parseInt(blockSelect.value);
            const floorName = floorNameInput ? floorNameInput.value.trim() : '';
            const aptCount = parseInt(apartmentsCountInput ? apartmentsCountInput.value : 0) || 0;
            
            const wrapper = document.getElementById('preview_building_wrapper');
            const emptyState = document.getElementById('preview_empty_state');
            
            if (!blockId) {
                wrapper.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }
            
            const block = blocksData.find(b => b.id === blockId);
            if (!block) return;
            
            wrapper.style.display = 'block';
            emptyState.style.display = 'none';
            
            document.getElementById('preview_building_name').textContent = block.name + ' (Hiện tại)';
            document.getElementById('preview_apts').textContent = aptCount;
            document.getElementById('preview_residents').textContent = '~' + (aptCount * 3);
            
            let fNum = parseFloorNumber(floorName);
            let floorCodePart = 'F' + (fNum !== null ? fNum : 'X');
            if (fNum < 0) floorCodePart = 'B' + Math.abs(fNum);
            else if (fNum === 0) floorCodePart = 'G';
            
            document.getElementById('preview_code').textContent = (block.code || block.name) + '-' + floorCodePart;
            
            let allFloors = [...block.floors];
            let newFloorItem = null;
            if (floorName) {
                newFloorItem = { is_new: true, name: floorName, floor_number: fNum !== null ? fNum : 999 };
                allFloors.push(newFloorItem);
            }
            
            allFloors.sort((a, b) => b.floor_number - a.floor_number);
            
            let newIndex = allFloors.findIndex(f => f.is_new);
            let displayFloors = allFloors;
            
            if (allFloors.length > 5 && newIndex !== -1) {
                let start = Math.max(0, newIndex - 2);
                let end = Math.min(allFloors.length, newIndex + 3);
                displayFloors = allFloors.slice(start, end);
            } else if (allFloors.length > 5) {
                displayFloors = allFloors.slice(0, 5);
            }
            
            const listEl = document.getElementById('preview_floors_list');
            listEl.innerHTML = '';
            
            displayFloors.forEach(f => {
                const item = document.createElement('div');
                item.style.display = 'flex';
                item.style.alignItems = 'center';
                item.style.padding = '8px 16px';
                item.style.marginLeft = '16px';
                item.style.position = 'relative';
                
                if (f.is_new) {
                    item.style.background = '#dbeafe';
                    item.style.borderRadius = '8px';
                    item.style.border = '1px solid #bfdbfe';
                    item.style.color = '#1e3a8a';
                    item.style.fontWeight = '600';
                    item.style.zIndex = '10';
                    item.innerHTML = `
                        <div style="background: #fff; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; position: absolute; left: -12px; border: 2px solid #2563eb; color: #2563eb;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        </div>
                        <span style="flex: 1; padding-left: 16px;">${f.name} (Đang tạo)</span>
                        <span style="background: #166534; color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold; margin-left: 8px;">MỚI</span>
                    `;
                } else {
                    item.style.color = '#64748b';
                    item.innerHTML = `
                        <div style="background: #cbd5e1; width: 10px; height: 10px; border-radius: 50%; position: absolute; left: -5px;"></div>
                        <div style="padding-left: 16px; display: flex; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                            <span>${f.name}</span>
                        </div>
                    `;
                }
                listEl.appendChild(item);
            });
        }

        function toggleApartmentsCount() {
            if (floorTypeSelect.value === 'basement') {
                apartmentsCountGroup.style.display = 'none';
                if (apartmentsCountInput) {
                    apartmentsCountInput.value = '';
                }
            } else {
                apartmentsCountGroup.style.display = 'block';
                // Auto-fill if empty
                if (apartmentsCountInput && !apartmentsCountInput.value && blockSelect.selectedIndex > 0) {
                    const defaultApts = blockSelect.options[blockSelect.selectedIndex].getAttribute('data-apartments');
                    if (defaultApts) {
                        apartmentsCountInput.value = defaultApts;
                    }
                }
            }
            updatePreview();
        }

        function handleBlockChange() {
            if (floorTypeSelect.value !== 'basement' && blockSelect.selectedIndex > 0) {
                const defaultApts = blockSelect.options[blockSelect.selectedIndex].getAttribute('data-apartments');
                if (defaultApts) {
                    apartmentsCountInput.value = defaultApts;
                }
            }
            updatePreview();
        }

        toggleApartmentsCount();
        if(floorTypeSelect) {
            floorTypeSelect.addEventListener('change', toggleApartmentsCount);
        }
        if(blockSelect) {
            blockSelect.addEventListener('change', handleBlockChange);
        }
        if(floorNameInput) {
            floorNameInput.addEventListener('input', updatePreview);
        }
        if(apartmentsCountInput) {
            apartmentsCountInput.addEventListener('input', updatePreview);
        }
        
        // Initial preview render
        updatePreview();
    });
</script>
@endpush