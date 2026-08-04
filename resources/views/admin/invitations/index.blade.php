@extends('layouts.admin.master')

@section('page_title', 'Quản lý mã mời Cư dân')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/invitations/index.css'])
@endpush

@section('content')
    <div class="invitations-page">

        {{-- Header --}}
        <div class="invitations-page__header">
            <div>
                <p class="invitations-page__eyebrow">Quản lý hệ thống</p>
                <h1>Mã Mời Cư Dân</h1>
            </div>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="invitations-alert invitations-alert--success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="invitations-alert invitations-alert--danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form tạo mã --}}
        <div class="invitations-create-card">
            <div class="invitations-create-card__header">
                <span class="invitations-create-card__icon">
                    <i class="fas fa-plus"></i>
                </span>
                <h2 class="invitations-create-card__title">Tạo Mã Đăng Ký Cư Dân Mới</h2>
            </div>
            <div class="invitations-create-card__body">
                <form action="{{ portal_route('invitations.store') }}" method="POST" class="invitations-form">
                    @csrf

                    <div class="invitations-form__field">
                        <label class="invitations-form__label">
                            Chọn tòa nhà <span>*</span>
                        </label>
                        <select name="block_id" class="invitations-form__input" required>
                            <option value="">Chọn tòa nhà</option>
                            @foreach ($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('block_id')
                            <small class="invitations-form__hint invitations-form__hint--error">{{ $message }}</small>
                        @else
                            <small class="invitations-form__hint">Chọn tòa nhà để phát mã mời chung cho cư dân.</small>
                        @enderror
                    </div>

                    <div class="invitations-form__field">
                        <label class="invitations-form__label">
                            Chọn tầng (tuỳ chọn)
                        </label>
                        <select name="floor_id" class="invitations-form__input">
                            <option value="">Không chọn tầng cụ thể</option>
                            @foreach ($floors as $floor)
                                <option value="{{ $floor->id }}" data-block-id="{{ $floor->block_id }}"
                                    {{ old('floor_id') == $floor->id ? 'selected' : '' }}>
                                    {{ $floor->display_name }} - {{ $floor->block->name ?? 'Tòa' }}
                                </option>
                            @endforeach
                        </select>
                        @error('floor_id')
                            <small class="invitations-form__hint invitations-form__hint--error">{{ $message }}</small>
                        @else
                            <small class="invitations-form__hint">Chọn tầng để lọc danh sách căn hộ dễ dàng hơn.</small>
                        @enderror
                    </div>

                    <div class="invitations-form__field">
                        <label class="invitations-form__label">
                            Chọn căn hộ (tuỳ chọn)
                        </label>
                        <select name="apartment_id" class="invitations-form__input">
                            <option value="">Không chọn căn hộ cụ thể</option>
                            @foreach ($apartments as $apartment)
                                <option value="{{ $apartment->id }}" data-block-id="{{ $apartment->floor->block_id ?? '' }}" data-floor-id="{{ $apartment->floor_id }}"
                                    {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                    {{ $apartment->apartment_number }} -
                                    {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }} /
                                    {{ $apartment->floor->block->name ?? 'Tòa' }}
                                </option>
                            @endforeach
                        </select>
                        @error('apartment_id')
                            <small class="invitations-form__hint invitations-form__hint--error">{{ $message }}</small>
                        @else
                            <small class="invitations-form__hint">Nếu muốn gắn mã vào căn hộ cụ thể, chọn ở đây.</small>
                        @enderror
                    </div>

                    <input type="hidden" name="max_uses" value="1">

                    <input type="hidden" name="intended_relationship" value="owner">

                    <div class="invitations-form__field">
                        <label class="invitations-form__label">Ngày hết hạn</label>
                        <input type="datetime-local" name="expires_at" class="invitations-form__input"
                            value="{{ old('expires_at') }}">
                        <small class="invitations-form__hint">Bỏ trống nếu mã không giới hạn thời gian.</small>
                    </div>

                    <div class="invitations-form__action">
                        <button type="submit" class="invitations-btn-create">
                            <i class="fas fa-key"></i>
                            Tạo mã ngay
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách mã --}}
        <div class="invitations-table-card">
            <div class="invitations-table-card__header">
                <h2 class="invitations-table-card__title">Danh Sách Mã Mời Đã Phát Hành</h2>
                <span class="invitations-table-card__count">{{ $invitations->total() }} mã</span>
            </div>

            <div class="invitations-table-wrap">
                <table class="invitations-table">
                    <thead>
                        <tr>
                            <th style="width:52px">STT</th>
                            <th>Mã Mời</th>
                            <th>Tòa / Căn Hộ</th>
                            <th>Quan Hệ</th>
                            <th>Người Tạo</th>
                            <th>Sử dụng</th>
                            <th>Ngày hết hạn</th>
                            <th>Trạng thái</th>
                            <th style="text-align:right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invitations as $invite)
                            <tr>
                                <td>{{ $loop->iteration + ($invitations->currentPage() - 1) * $invitations->perPage() }}
                                </td>

                                <td>
                                    <span class="inv-code-badge" title="Nhấn để sao chép"
                                        onclick="copyCode('{{ $invite->invite_code }}')">
                                        {{ $invite->invite_code }}
                                        <i class="fas fa-copy inv-code-badge__copy-icon"></i>
                                    </span>
                                </td>

                                <td>
                                    <div class="inv-apartment">
                                        <strong>{{ $invite->block?->name ?? '—' }}</strong>
                                        <span class="inv-apartment__meta">
                                            @if ($invite->apartment)
                                                {{ $invite->apartment->apartment_number }} -
                                                {{ optional($invite->apartment->floor)->name ? optional($invite->apartment->floor)->name : 'Tầng ' . optional($invite->apartment->floor)->floor_number }}
                                            @else
                                                Mã chỉ theo tòa nhà
                                            @endif
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <span
                                        class="badge-relationship {{ str_replace('_', '-', strtolower($invite->intended_relationship ?? 'other')) }}">
                                        @switch($invite->intended_relationship)
                                            @case('owner')
                                                Chủ hộ
                                            @break

                                            @case('family_member')
                                                Thành viên gia đình
                                            @break

                                            @case('tenant')
                                                Người thuê
                                            @break

                                            @default
                                                Khác
                                        @endswitch
                                    </span>
                                </td>

                                <td>
                                    <span class="inv-creator">
                                        <span class="inv-creator__avatar">#</span>
                                        {{ $invite->creator?->name ?? 'ID: ' . $invite->created_by }}
                                    </span>
                                </td>

                                <td>
                                    {{ $invite->uses_count }} / {{ $invite->max_uses }}
                                </td>

                                <td>
                                    @if ($invite->expired_at)
                                        <span class="inv-expiry">{{ $invite->expired_at->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="inv-expiry--none">Vô thời hạn</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($invite->isValid())
                                        <span class="inv-status inv-status--valid">
                                            <span class="inv-status__dot"></span>
                                            Hợp lệ
                                        </span>
                                    @else
                                        <span class="inv-status inv-status--expired">
                                            <span class="inv-status__dot"></span>
                                            Hết hạn
                                        </span>
                                    @endif
                                </td>

                                <td style="text-align:right">
                                    <form action="{{ portal_route('invitations.destroy', $invite->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã mời này?')"
                                        style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inv-btn-delete">
                                            <i class="fas fa-trash-alt"></i>
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="invitations-empty">
                                            <div class="invitations-empty__icon">
                                                <i class="fas fa-envelope-open-text"></i>
                                            </div>
                                            <p class="invitations-empty__text">Chưa có mã mời nào được tạo.</p>
                                            <p class="invitations-empty__sub">Sử dụng form ở trên để tạo mã mời đầu tiên.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($invitations->hasPages())
                    <div class="invitations-table-footer">
                        <span class="invitations-table-footer__stats">
                            Hiển thị {{ $invitations->firstItem() }}–{{ $invitations->lastItem() }} /
                            {{ $invitations->total() }} mã
                        </span>
                        {{ $invitations->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- Toast thông báo copy --}}
        <div class="inv-copy-toast" id="copyToast">
            <i class="fas fa-check-circle"></i>
            Đã sao chép mã mời!
        </div>
    @endsection

    @push('scripts')
        <script>
            function copyCode(code) {
                navigator.clipboard.writeText(code).then(function() {
                    const toast = document.getElementById('copyToast');
                    toast.classList.add('visible');
                    setTimeout(() => toast.classList.remove('visible'), 2000);
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const blockSelect = document.querySelector('select[name="block_id"]');
                const floorSelect = document.querySelector('select[name="floor_id"]');
                const apartmentSelect = document.querySelector('select[name="apartment_id"]');

                if (!blockSelect || !apartmentSelect) {
                    return;
                }

                function filterSelect(selectElement, attribute, matchValue) {
                    Array.from(selectElement.options).forEach(option => {
                        if (!option.value) {
                            option.hidden = false;
                            return;
                        }
                        const dataValue = option.dataset[attribute];
                        option.hidden = matchValue && dataValue !== matchValue;
                    });
                    
                    // Reset value if currently selected option is now hidden
                    const selectedOption = selectElement.selectedOptions[0];
                    if (selectedOption && selectedOption.hidden) {
                        selectElement.value = '';
                    }
                }

                blockSelect.addEventListener('change', function() {
                    const selectedBlockId = this.value;
                    
                    if (floorSelect) {
                        filterSelect(floorSelect, 'blockId', selectedBlockId);
                    }
                    
                    filterSelect(apartmentSelect, 'blockId', selectedBlockId);
                });
                
                if (floorSelect) {
                    floorSelect.addEventListener('change', function() {
                        const selectedFloorId = this.value;
                        const selectedBlockId = blockSelect.value;
                        
                        if (selectedFloorId) {
                            filterSelect(apartmentSelect, 'floorId', selectedFloorId);
                        } else {
                            // If floor is unselected, fallback to filtering by block
                            filterSelect(apartmentSelect, 'blockId', selectedBlockId);
                        }
                    });
                }
            });
        </script>
    @endpush
