@extends('layouts.receptionist.master')

@section('page_title', 'Nhận bưu phẩm mới – Lễ tân DomusHub')

@section('topbar_left')
<nav class="breadcrumb-nav">
    <a href="{{ route('receptionist.dashboard') }}">Dashboard</a>
    <span class="divider">/</span>
    <a href="{{ route('receptionist.parcels.index') }}">Bưu phẩm</a>
    <span class="divider">/</span>
    <span class="current">Nhận bưu phẩm mới</span>
</nav>
@endsection

@section('content')
<div style="max-width:780px; margin:0 auto;">

    {{-- Back link --}}
    <div style="margin-bottom:20px;">
        <a href="{{ route('receptionist.parcels.index') }}"
           style="font-size:14px;color:#0b57d0;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-weight:600;">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại danh sách
        </a>
    </div>

    {{-- Flash errors --}}
    @if ($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="2" style="flex-shrink:0;margin-top:2px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <div>
            <p style="font-size:13px;font-weight:700;color:#dc2626;margin:0 0 4px;">Vui lòng kiểm tra lại thông tin:</p>
            <ul style="margin:0;padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size:13px;color:#dc2626;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">

        {{-- Badge --}}
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
            </svg>
            BƯU PHẨM MỚI
        </div>

        <form method="POST" action="{{ route('receptionist.parcels.store') }}" id="parcelForm">
            @csrf

            {{-- Section 1: Chọn căn hộ --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Chọn căn hộ nhận bưu phẩm</h4>
            </div>

            {{-- Data: encode toàn bộ cấu trúc block > floor > apartment thành JSON --}}
            <div id="blocks-data" style="display:none">{{ $blocks->toJson() }}</div>

            <div class="form-grid-3">
                {{-- Chọn Tòa --}}
                <div class="form-group-custom">
                    <label class="form-label-custom" for="select_block">Tòa nhà</label>

                    <select id="select_block" class="form-input-custom" style="padding-left:14px;">
                        <option value="">-- Chọn tòa --</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}">{{ $block->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Chọn Tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom" for="select_floor">Tầng</label>

                    <select id="select_floor" class="form-input-custom" style="padding-left:14px;" disabled>
                        <option value="">-- Chọn tầng --</option>
                    </select>
                </div>

                {{-- Chọn Căn hộ --}}
                <div class="form-group-custom">
                    <label class="form-label-custom" for="select_apartment">Căn hộ <span class="required">*</span></label>

                    <select id="select_apartment" name="apartment_id"
                            class="form-input-custom @error('apartment_id') input-error @enderror"
                            style="padding-left:14px;" disabled required>
                        <option value="">-- Chọn căn hộ --</option>
                    </select>
                    @error('apartment_id') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>
            </div>



            {{-- Section 2: Thông tin bưu phẩm --}}
            <div class="form-section-header" style="margin-top:8px;">
                <span class="section-number">02</span>
                <h4>Thông tin bưu phẩm</h4>
            </div>

            {{-- Tên người gửi --}}
            <div class="form-group-custom" style="margin-bottom:20px;">
                <label class="form-label-custom">Tên người gửi / Nguồn hàng <span class="required">*</span></label>
                <input type="text" name="sender_name"
                       value="{{ old('sender_name') }}"
                       placeholder="VD: Nguyễn Văn A / Shopee / Lazada..."
                       class="form-input-custom @error('sender_name') input-error @enderror"
                       required>
                @error('sender_name') <p class="form-error-custom">{{ $message }}</p> @enderror
            </div>

            {{-- Mã vận đơn + Đơn vị vận chuyển --}}
            <div class="form-grid-2">
                <div class="form-group-custom">
                    <label class="form-label-custom">Mã vận đơn <small style="color:#94a3b8;font-weight:400">(tuỳ chọn)</small></label>
                    <input type="text" name="tracking_code"
                           value="{{ old('tracking_code') }}"
                           placeholder="VD: GHN12345678"
                           class="form-input-custom">
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Đơn vị vận chuyển <small style="color:#94a3b8;font-weight:400">(tuỳ chọn)</small></label>
                    <input type="text" name="carrier"
                           value="{{ old('carrier') }}"
                           placeholder="VD: GHN, GHTK, VNPost..."
                           class="form-input-custom">
                </div>

            </div>

            {{-- Section 3: Ghi chú --}}
            <div class="form-section-header" style="margin-top:8px;">
                <span class="section-number">03</span>
                <h4>Mô tả & Ghi chú <small style="color:#94a3b8;font-weight:400">(tuỳ chọn)</small></h4>
            </div>

            <div class="form-group-custom" style="margin-bottom:20px;">
                <label class="form-label-custom">Mô tả bưu phẩm</label>
                <textarea name="description" class="form-textarea-custom" rows="3"
                          placeholder="VD: 1 thùng hàng lớn, đóng gói cẩn thận, dễ vỡ...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group-custom" style="margin-bottom:28px;">
                <label class="form-label-custom">Ghi chú nội bộ của lễ tân</label>
                <textarea name="note" class="form-textarea-custom" rows="2"
                          placeholder="Ghi chú thêm dành riêng cho lễ tân (nếu có)...">{{ old('note') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="form-actions-custom">
                <button type="submit" class="btn-new-broadcast" style="width:auto;margin:0;padding:12px 28px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Xác nhận nhận bưu phẩm
                </button>
                <a href="{{ route('receptionist.parcels.index') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:12px 24px;border-radius:8px;background:#f1f5f9;color:#475569;font-weight:600;text-decoration:none;font-size:14px;transition:background .2s;"
                   onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>
</div>

<style>
    select.form-input-custom:disabled {
        background-color: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
        opacity: 1;
    }
    #apt-preview {
        display: none;
    }
    #apt-preview.visible {
        display: flex;
    }
    .form-grid-3 select.form-input-custom {
        padding-left: 14px;
    }
</style>

<script>
(function () {
    const raw     = document.getElementById('blocks-data').textContent;
    const blocks  = JSON.parse(raw);

    const selBlock = document.getElementById('select_block');
    const selFloor = document.getElementById('select_floor');
    const selApt   = document.getElementById('select_apartment');

    // Build lookup maps
    const blockMap = {};  // blockId -> block
    blocks.forEach(b => { blockMap[b.id] = b; });

    function resetSelect(sel, placeholder) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled = true;
    }


    selBlock.addEventListener('change', function () {
        resetSelect(selFloor, '-- Chọn tầng --');
        resetSelect(selApt, '-- Chọn căn hộ --');

        const blockId = parseInt(this.value);
        if (!blockId) return;

        const block = blockMap[blockId];
        if (!block || !block.floors) return;

        block.floors.forEach(floor => {
            const opt = document.createElement('option');
            opt.value = floor.id;
            opt.textContent = 'Tầng ' + floor.floor_number;
            selFloor.appendChild(opt);
        });
        selFloor.disabled = false;
    });

    selFloor.addEventListener('change', function () {
        resetSelect(selApt, '-- Chọn căn hộ --');

        const blockId = parseInt(selBlock.value);
        const floorId = parseInt(this.value);
        if (!blockId || !floorId) return;

        const block = blockMap[blockId];
        const floor = block.floors.find(f => f.id === floorId);
        if (!floor || !floor.apartments) return;

        floor.apartments.forEach(apt => {
            const opt = document.createElement('option');
            opt.value = apt.id;
            opt.textContent = 'Căn ' + apt.apartment_number;
            selApt.appendChild(opt);
        });
        selApt.disabled = false;
    });

    selApt.addEventListener('change', function () {
        // no preview needed
    });

    // Restore old() values after validation error
    @if(old('apartment_id'))
    (function () {
        // Find which block/floor this apartment belongs to
        const oldAptId = {{ old('apartment_id') }};
        let foundBlock, foundFloor, foundApt;

        blocks.forEach(b => {
            b.floors.forEach(f => {
                f.apartments.forEach(a => {
                    if (a.id === oldAptId) {
                        foundBlock = b; foundFloor = f; foundApt = a;
                    }
                });
            });
        });

        if (!foundBlock) return;

        // Set block
        selBlock.value = foundBlock.id;
        selBlock.dispatchEvent(new Event('change'));

        // Set floor after options populated
        setTimeout(() => {
            selFloor.value = foundFloor.id;
            selFloor.dispatchEvent(new Event('change'));
            setTimeout(() => {
                selApt.value = oldAptId;
                selApt.dispatchEvent(new Event('change'));
            }, 0);
        }, 0);
    })();
    @endif
})();
</script>
@endsection
