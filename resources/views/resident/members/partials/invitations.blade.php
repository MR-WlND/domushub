{{-- Tab Mã mời gia đình – Chỉ dành cho Chủ hộ (Owner) --}}
<div class="declared-layout">

    {{-- === FORM TẠO MÃ MỜI === --}}
    <div class="card card--form">
        <div class="card__header">
            <h2 class="card__title"><i class="fa-solid fa-key"></i> Tạo mã mời mới</h2>
            <p class="card__description">
                Tạo mã mời nội bộ để người thân hoặc người thuê dùng khi đăng ký tài khoản —
                hệ thống sẽ tự động liên kết họ vào căn hộ của bạn.
            </p>
        </div>
        <div class="card__body">
            <form action="{{ route('resident.members.invitations.store') }}" method="POST" class="member-form">
                @csrf

                {{-- Bước 1: Chọn tòa nhà --}}
                <div class="form-group">
                    <label for="block_select" class="form-label">
                        <i class="fa-solid fa-building"></i> Tòa nhà
                    </label>
                    <select id="block_select" class="form-input" onchange="filterApartmentsByBlock(this.value)" required>
                        <option value="">-- Chọn tòa nhà --</option>
                        @foreach ($blocks as $block)
                            <option value="{{ $block->id }}"
                                {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Bước 2: Chọn căn hộ (lọc theo tòa) --}}
                <div class="form-group">
                    <label for="apartment_id_invite" class="form-label">
                        <i class="fa-solid fa-door-open"></i> Căn hộ
                    </label>
                    <select name="apartment_id" id="apartment_id_invite" class="form-input" required>
                        <option value="">-- Chọn tòa trước --</option>
                        @foreach ($apartments as $apartment)
                            <option value="{{ $apartment->id }}"
                                    data-block-id="{{ $apartment->floor->block->id ?? '' }}"
                                    data-floor="{{ $apartment->floor->name ?? ('Tầng ' . ($apartment->floor->floor_number ?? '?')) }}"
                                    style="display:none;"
                                    {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                {{ $apartment->apartment_number }}
                                – {{ $apartment->floor->name ?? ('Tầng ' . $apartment->floor->floor_number) }}
                            </option>
                        @endforeach
                    </select>
                    @error('apartment_id')
                        <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Vai trò dự kiến --}}
                <div class="form-group">
                    <label for="intended_relationship" class="form-label">
                        <i class="fa-solid fa-user-tag"></i> Vai trò dự kiến
                    </label>
                    <div class="role-options">
                        <label class="role-option {{ old('intended_relationship') === 'family_member' ? 'role-option--selected' : '' }}">
                            <input type="radio" name="intended_relationship" value="family_member"
                                   {{ old('intended_relationship') === 'family_member' ? 'checked' : '' }}
                                   onchange="this.closest('.role-options').querySelectorAll('.role-option').forEach(el => el.classList.remove('role-option--selected')); this.closest('.role-option').classList.add('role-option--selected');">
                            <span class="role-option__icon role-option__icon--family"><i class="fa-solid fa-house-user"></i></span>
                            <div class="role-option__content">
                                <strong>Thành viên gia đình</strong>
                                <small>Người thân sống cùng (vợ/chồng, con, bố mẹ...)</small>
                            </div>
                        </label>
                        <label class="role-option {{ old('intended_relationship') === 'tenant' ? 'role-option--selected' : '' }}">
                            <input type="radio" name="intended_relationship" value="tenant"
                                   {{ old('intended_relationship') === 'tenant' ? 'checked' : '' }}
                                   onchange="this.closest('.role-options').querySelectorAll('.role-option').forEach(el => el.classList.remove('role-option--selected')); this.closest('.role-option').classList.add('role-option--selected');">
                            <span class="role-option__icon role-option__icon--tenant"><i class="fa-solid fa-key"></i></span>
                            <div class="role-option__content">
                                <strong>Người thuê</strong>
                                <small>Người thuê trọ ngắn hạn hoặc dài hạn</small>
                            </div>
                        </label>
                    </div>
                    @error('intended_relationship')
                        <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Ghi chú --}}
                <div class="form-group">
                    <label for="invite_note" class="form-label">
                        <i class="fa-solid fa-pen-to-square"></i> Ghi chú <span class="optional-tag">(tùy chọn)</span>
                    </label>
                    <input type="text" name="note" id="invite_note" class="form-input"
                           placeholder="Ví dụ: Mã dành cho bạn Minh Quân" value="{{ old('note') }}" maxlength="200">
                    @error('note')
                        <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                    <small class="hint-text">Ghi chú để nhận biết mã mời này gửi cho ai.</small>
                </div>

                {{-- Ngày hết hạn --}}
                <div class="form-group">
                    <label for="expired_at" class="form-label">
                        <i class="fa-solid fa-calendar-xmark"></i> Ngày hết hạn <span class="optional-tag">(tùy chọn)</span>
                    </label>
                    <input type="datetime-local" name="expired_at" id="expired_at"
                           class="form-input" value="{{ old('expired_at') }}">
                    @error('expired_at')
                        <span class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                    <small class="hint-text">Để trống nếu mã không có thời hạn hết hạn.</small>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-8">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Tạo mã mời
                </button>
            </form>
        </div>
    </div>

    {{-- === DANH SÁCH MÃ MỜI === --}}
    <div class="card card--table">
        <div class="card__header card__header--flex">
            <div>
                <h2 class="card__title">Danh sách mã mời đã tạo</h2>
                <p class="card__description">
                    Mỗi mã mời chỉ dùng được <strong>1 lần</strong>. Hủy kích hoạt nếu muốn thu hồi quyền đăng ký.
                </p>
            </div>
        </div>
        <div class="card__body p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 52px;">STT</th>
                            <th>Mã mời</th>
                            <th>Ghi chú</th>
                            <th>Căn hộ</th>
                            <th>Vai trò</th>
                            <th>Hạn sử dụng</th>
                            <th style="width: 130px; text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invitations as $invite)
                            <tr class="{{ $invite->invite_code === session('new_invite_code') ? 'table-row--new-invite' : '' }}">
                                <td>{{ $loop->iteration + ($invitations->currentPage() - 1) * $invitations->perPage() }}</td>
                                <td>
                                    <div class="invite-code-cell">
                                        <code class="invite-code {{ $invite->status !== 'active' ? 'invite-code--inactive' : '' }}">
                                            {{ $invite->invite_code }}
                                        </code>
                                        @if ($invite->status === 'active')
                                            <button type="button" class="btn-copy-code"
                                                    onclick="copyInviteCode('{{ $invite->invite_code }}', this)"
                                                    title="Sao chép mã">
                                                <i class="fa-regular fa-copy"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted-sm">{{ $invite->note ?: '—' }}</span>
                                </td>
                                <td>
                                    <span class="apartment-tag">{{ $invite->apartment->apartment_number ?? '—' }}</span>
                                </td>
                                <td>
                                    @if ($invite->intended_relationship === 'family_member')
                                        <span class="badge badge--family"><i class="fa-solid fa-house-user"></i> Thành viên gia đình</span>
                                    @elseif ($invite->intended_relationship === 'tenant')
                                        <span class="badge badge--tenant"><i class="fa-solid fa-key"></i> Người thuê</span>
                                    @else
                                        <span class="badge badge--secondary">{{ $invite->intended_relationship }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($invite->expired_at)
                                        <span class="fs-sm {{ $invite->expired_at->isPast() ? 'text-danger' : 'text-muted' }}">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $invite->expired_at->format('d/m/Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-sm">Vô thời hạn</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($invite->status === 'active')
                                        <form action="{{ route('resident.members.invitations.destroy', $invite->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hủy kích hoạt mã mời này? Người có mã sẽ không thể đăng ký nữa.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-outline btn-sm">
                                                <i class="fa-solid fa-ban"></i> Hủy
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted fs-sm">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state-cell">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-ticket empty-state__icon"></i>
                                        <p class="empty-state__title">Chưa có mã mời nào</p>
                                        <p class="empty-state__subtitle">Tạo mã mời để người thân hoặc người thuê đăng ký tài khoản.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($invitations->hasPages())
                <div class="pagination-wrapper px-24 py-16">
                    {{ $invitations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- === MODAL HIỂN THỊ MÃ MỜI VỪA TẠO === --}}
@if (session('new_invite_code'))
    <div class="invite-modal-overlay" id="inviteModal">
        <div class="invite-modal">
            <div class="invite-modal__header">
                <div class="invite-modal__icon-wrap">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 class="invite-modal__title">Mã mời đã được tạo!</h3>
                <p class="invite-modal__subtitle">Sao chép và gửi mã này cho người thân hoặc người thuê để họ dùng khi đăng ký tài khoản.</p>
            </div>

            <div class="invite-modal__code-block">
                <code class="invite-modal__code" id="modalInviteCode">{{ session('new_invite_code') }}</code>
                <button type="button" class="invite-modal__copy-btn" id="modalCopyBtn"
                        onclick="copyModalCode()" title="Sao chép mã">
                    <i class="fa-regular fa-copy"></i>
                    <span>Sao chép</span>
                </button>
            </div>

            <div class="invite-modal__note">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Mã này chỉ sử dụng được <strong>1 lần</strong>.
                Sau khi có người đăng ký bằng mã này, mã sẽ tự động vô hiệu.
            </div>

            <button type="button" class="btn btn-secondary w-100" onclick="closeInviteModal()">
                <i class="fa-solid fa-xmark"></i> Đóng
            </button>
        </div>
    </div>
@endif

@push('scripts')
<script>
    // ── Lọc căn hộ theo tòa nhà ──────────────────────────────────────
    function filterApartmentsByBlock(blockId) {
        const aptSelect  = document.getElementById('apartment_id_invite');
        const options    = aptSelect.querySelectorAll('option[data-block-id]');
        const placeholder = aptSelect.options[0]; // "-- Chọn tòa trước --"

        // Reset chọn
        aptSelect.value = '';
        placeholder.textContent = blockId ? '-- Chọn căn hộ --' : '-- Chọn tòa trước --';

        options.forEach(opt => {
            const match = opt.dataset.blockId === blockId;
            opt.style.display = match ? '' : 'none';
            if (!match && opt.selected) opt.selected = false;
        });
    }

    // ── Copy mã trong bảng ───────────────────────────────────────────
    function copyInviteCode(code, button) {
        navigator.clipboard.writeText(code).then(() => {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fa-solid fa-check" style="color:#10b981;"></i>';
            button.classList.add('copied');
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('copied');
            }, 2000);
        });
    }

    // ── Copy mã trong modal ──────────────────────────────────────────
    function copyModalCode() {
        const code = document.getElementById('modalInviteCode').textContent.trim();
        const btn  = document.getElementById('modalCopyBtn');
        navigator.clipboard.writeText(code).then(() => {
            btn.innerHTML = '<i class="fa-solid fa-check"></i><span>Đã sao chép!</span>';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-regular fa-copy"></i><span>Sao chép</span>';
                btn.classList.remove('copied');
            }, 3000);
        });
    }

    // ── Đóng modal ───────────────────────────────────────────────────
    function closeInviteModal() {
        const modal = document.getElementById('inviteModal');
        if (modal) {
            modal.classList.add('invite-modal-overlay--closing');
            setTimeout(() => modal.remove(), 300);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Khôi phục trạng thái khi có old('apartment_id') (form bị lỗi validation)
        const oldApartmentId = '{{ old("apartment_id") }}';
        if (oldApartmentId) {
            const aptOption = document.querySelector(`#apartment_id_invite option[value="${oldApartmentId}"]`);
            if (aptOption) {
                const blockId = aptOption.dataset.blockId;
                const blockSelect = document.getElementById('block_select');
                if (blockSelect) blockSelect.value = blockId;
                filterApartmentsByBlock(blockId);
                aptOption.selected = true;
            }
        }

        // Đóng modal khi click overlay
        const overlay = document.getElementById('inviteModal');
        if (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) closeInviteModal();
            });
        }

        // Highlight + scroll đến row mã mới
        const newRow = document.querySelector('.table-row--new-invite');
        if (newRow) {
            newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endpush
