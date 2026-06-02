<div class="declared-layout">
    <!-- Form Khai báo nhân khẩu mới (Chỉ hiện đối với Chủ hộ) -->
    @if ($isOwner)
        <div class="card card--form">
            <div class="card__header">
                <h2 class="card__title"><i class="fa-solid fa-user-plus"></i> Khai báo nhân khẩu mới</h2>
                <p class="card__description">Dùng cho người thân sống cùng nhà nhưng không sử dụng ứng dụng (người già, trẻ nhỏ...).</p>
            </div>
            <div class="card__body">
                <form action="{{ route('resident.members.declared.store') }}" method="POST" class="member-form">
                    @csrf

                    {{-- Bước 1: Chọn tòa --}}
                    @if ($blocks->count() > 1)
                    <div class="form-group">
                        <label for="declared_block_select" class="form-label">
                            <i class="fa-solid fa-building"></i> Tòa nhà
                        </label>
                        <select id="declared_block_select" class="form-input"
                                onchange="filterDeclaredApartments(this.value)">
                            <option value="">-- Chọn tòa nhà --</option>
                            @foreach ($blocks as $block)
                                <option value="{{ $block->id }}"
                                    {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Bước 2: Chọn căn hộ --}}
                    <div class="form-group">
                        <label for="apartment_id" class="form-label">
                            <i class="fa-solid fa-door-open"></i> Căn hộ
                        </label>
                        <select name="apartment_id" id="apartment_id_declared" class="form-input" required>
                            @if ($blocks->count() > 1)
                                <option value="">-- Chọn tòa trước --</option>
                                @foreach ($apartments as $apartment)
                                    <option value="{{ $apartment->id }}"
                                            data-block-id="{{ $apartment->floor->block->id ?? '' }}"
                                            style="display:none;"
                                            {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->apartment_number }}
                                        – {{ $apartment->floor->name ?? ('Tầng ' . $apartment->floor->floor_number) }}
                                    </option>
                                @endforeach
                            @else
                                <option value="">-- Chọn căn hộ --</option>
                                @foreach ($apartments as $apartment)
                                    <option value="{{ $apartment->id }}"
                                            {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                        {{ $apartment->apartment_number }}
                                        – {{ $apartment->floor->name ?? ('Tầng ' . $apartment->floor->floor_number) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('apartment_id')
                            <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Họ và tên thành viên</label>
                        <input type="text" name="name" id="name" class="form-input" placeholder="Ví dụ: Nguyễn Văn B" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="birth_year" class="form-label">Năm sinh <span class="optional-tag">(tùy chọn)</span></label>
                        <input type="text" name="birth_year" id="birth_year" class="form-input" placeholder="Ví dụ: 2015" value="{{ old('birth_year') }}">
                        @error('birth_year')
                            <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="relationship" class="form-label">Quan hệ với chủ hộ</label>
                        <select name="relationship" id="relationship" class="form-input" required>
                            <option value="">-- Chọn quan hệ --</option>
                            <option value="Thành viên gia đình" {{ old('relationship') == 'Thành viên gia đình' ? 'selected' : '' }}>Thành viên gia đình</option>
                            <option value="Người thuê" {{ old('relationship') == 'Người thuê' ? 'selected' : '' }}>Người thuê</option>
                        </select>
                        @error('relationship')
                            <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-8">
                        <i class="fa-solid fa-paper-plane"></i> Gửi khai báo
                    </button>
                </form>
            </div>
        </div>
    @endif


    <!-- Bảng danh sách Nhân khẩu khai báo -->
    <div class="card card--table {{ !$isOwner ? 'w-100' : '' }}">
        <div class="card__header">
            <h2 class="card__title">Nhân khẩu gia đình đã khai báo</h2>
            <p class="card__description">Danh sách thành viên do chủ hộ khai báo.</p>
        </div>
        <div class="card__body p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">STT</th>
                            <th>Họ và tên</th>
                            <th>Năm sinh</th>
                            <th>Quan hệ</th>
                            <th>Căn hộ</th>
                            @if ($isOwner)
                                <th style="width: 100px; text-align: center;">Hành động</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($declaredMembers as $member)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="font-semibold">{{ $member->name }}</span>
                                </td>
                                <td>{{ $member->birth_year ?? '—' }}</td>
                                <td>
                                    <span class="badge badge--relationship">{{ $member->relationship ?? 'Khác' }}</span>
                                </td>
                                <td>
                                    <span class="apartment-tag">
                                        {{ $member->apartment->apartment_number ?? '—' }}
                                        ({{ $member->apartment->floor->block->name ?? '' }})
                                    </span>
                                </td>
                                @if ($isOwner)
                                    <td class="text-center">
                                        <form action="{{ route('resident.members.declared.destroy', $member->id) }}" 
                                                method="POST" 
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân khẩu này khỏi hồ sơ gia đình?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-outline btn-sm">
                                                <i class="fa-solid fa-user-xmark"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isOwner ? 6 : 5 }}" class="text-center text-muted py-24">
                                    Chưa có nhân khẩu nào được khai báo cho căn hộ này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if ($isOwner && $blocks->count() > 1)
<script>
    function filterDeclaredApartments(blockId) {
        const aptSelect   = document.getElementById('apartment_id_declared');
        const options     = aptSelect.querySelectorAll('option[data-block-id]');
        const placeholder = aptSelect.options[0];

        aptSelect.value = '';
        placeholder.textContent = blockId ? '-- Chọn căn hộ --' : '-- Chọn tòa trước --';

        options.forEach(opt => {
            const match = opt.dataset.blockId === blockId;
            opt.style.display = match ? '' : 'none';
            if (!match && opt.selected) opt.selected = false;
        });
    }

    // Khôi phục khi old() có sẵn (validation error)
    document.addEventListener('DOMContentLoaded', function () {
        const oldApt = '{{ old("apartment_id") }}';
        if (oldApt) {
            const opt = document.querySelector(`#apartment_id_declared option[value="${oldApt}"]`);
            if (opt) {
                const blockId = opt.dataset.blockId;
                const blockSel = document.getElementById('declared_block_select');
                if (blockSel) blockSel.value = blockId;
                filterDeclaredApartments(blockId);
                opt.selected = true;
            }
        }
    });
</script>
@endif
