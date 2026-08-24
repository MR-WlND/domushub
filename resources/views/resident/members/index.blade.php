@extends('layouts.resident.master')

@section('title', 'Quản lý thành viên căn hộ')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/pages/resident/members/index.css'])
@endpush

@section('content')
    <section class="resident-members-page">

        <!-- Mobile Header (Visible only on mobile) -->
        <div class="mobile-page-header">
            <h1 class="mobile-page-title">Quản lý thành viên</h1>
            @if ($isOwner)
                <button class="btn btn-add-member btn-add-member--mobile" onclick="openDeclareModal()">
                    <i class="fa-solid fa-user-plus"></i> Khai báo nhân khẩu
                </button>
            @endif
        </div>

        <!-- Thống kê nhanh (Stats Cards) -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Tổng số nhân khẩu</span>
                    <h3 class="stats-card__value">{{ $totalMembersCount }}</h3>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--green">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Số nhân khẩu khai báo</span>
                    <h3 class="stats-card__value">{{ $declaredMembers->count() }}</h3>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--light-blue">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Mã mời hoạt động</span>
                    <h3 class="stats-card__value">{{ $activeInvitesCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Banner cảnh báo Cư dân thường -->
        @if (!$isOwner)
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Lưu ý quan trọng:</strong> Bạn đang truy cập với vai trò <strong>Cư dân thường / Người thuê</strong>. Chỉ có <strong>Chủ hộ (Owner)</strong> mới có quyền khai báo nhân khẩu gia đình, xóa thành viên hoặc tạo mã mời mới.
                </div>
            </div>
        @endif

        @if ($isOwner && isset($pendingMembers) && $pendingMembers->count() > 0)
            <div class="card card--tab-content" style="margin-bottom: 24px;">
                <div class="card__header" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08); padding-bottom: 12px;">
                    <h2 class="card__title" style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Yêu cầu gia nhập đang chờ duyệt
                    </h2>
                    <p class="card__description">Danh sách cư dân đăng ký qua mã mời đang chờ bạn phê duyệt để gia nhập căn hộ.</p>
                </div>
                <div class="card__body p-0">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">STT</th>
                                    <th>Họ và tên</th>
                                    <th>Thông tin liên hệ</th>
                                    <th>Căn hộ</th>
                                    <th>Vai trò dự kiến</th>
                                    <th>Ngày đăng ký</th>
                                    <th style="width: 200px; text-align: center;">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingMembers as $pending)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="user-info-cell__name">{{ $pending->user->name ?? 'Cư dân' }}</span></td>
                                        <td>
                                            <div class="contact-cell">
                                                <span><i class="fa-solid fa-phone"></i> {{ $pending->user->phone ?? '—' }}</span>
                                                <span class="contact-cell__email"><i class="fa-regular fa-envelope"></i> {{ $pending->user->email ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="apartment-tag">
                                                {{ $pending->apartment->apartment_number ?? '—' }} ({{ $pending->apartment->floor->block->name ?? '' }})
                                            </span>
                                        </td>
                                        <td>
                                            @if ($pending->relationship === 'family_member')
                                                <span class="badge badge--family"><i class="fa-solid fa-house-user"></i> Gia đình</span>
                                            @elseif ($pending->relationship === 'tenant')
                                                <span class="badge badge--tenant"><i class="fa-solid fa-key"></i> Người thuê</span>
                                            @else
                                                <span class="badge badge--secondary">{{ $pending->relationship }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $pending->created_at ? $pending->created_at->format('d/m/Y H:i') : '—' }}</td>
                                        <td>
                                            <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                                <form action="{{ route('resident.members.approve', $pending->id) }}" method="POST" style="margin: 0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa-solid fa-check"></i> Duyệt
                                                    </button>
                                                </form>
                                                <form action="{{ route('resident.members.reject', $pending->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối yêu cầu gia nhập này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa-solid fa-xmark"></i> Từ chối
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card card--tab-content">
            <div class="card__header card__header--flex">
                <div>
                    <h2 class="card__title"><i class="fa-solid fa-users"></i> Danh sách thành viên gia đình</h2>
                    <p class="card__description">Danh sách cư dân đã liên kết tài khoản và nhân khẩu đã được khai báo.</p>
                </div>
                @if ($isOwner)
                    <div class="desktop-add-btn">
                        <button class="btn btn-add-member" onclick="openDeclareModal()">
                            <i class="fa-solid fa-user-plus"></i> Khai báo nhân khẩu
                        </button>
                    </div>
                @endif
            </div>
            <div class="card__body p-0">
                <!-- Toolbar Tìm kiếm và Lọc -->
                <div class="members-toolbar">
                    <div class="search-box">
                        <i class="fa-solid fa-search search-icon"></i>
                        <input type="text" id="memberSearchInput" class="search-input" placeholder="Tìm kiếm thành viên...">
                    </div>
                    <div class="filter-box">
                        <i class="fa-solid fa-filter filter-icon-mobile"></i>
                        <select id="statusFilterSelect" class="status-filter-select">
                            <option value="all">Tất cả trạng thái</option>
                            <option value="linked">Đã có tài khoản</option>
                            <option value="unlinked">Chưa liên kết</option>
                        </select>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="table" id="membersTable">
                        <thead>
                            <tr>
                                <th style="width: 60px;">STT</th>
                                <th>Họ và tên</th>
                                <th>Căn hộ</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="membersTableBody">
                            @php $index = 1; @endphp
                            <!-- Self -->
                            <tr class="table-row--self member-row table-row--clickable" onclick="window.location.href='{{ route('resident.profile.index') }}'" data-name="{{ strtolower($user->name) }}" data-phone="{{ $user->phone }}" data-status="linked">
                                <td class="col-stt">{{ $index++ }}</td>
                                <td class="col-user">
                                    <div class="user-info-cell">
                                        <div class="user-avatar" style="background: #e0f2fe; color: #0ea5e9;">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <span class="user-info-cell__name">{{ $user->name }}</span>
                                        <span class="badge badge--self">Bạn</span>
                                    </div>
                                </td>
                                <td class="col-apartment">
                                    @foreach ($apartments as $apartment)
                                        <span class="apartment-tag">
                                            <i class="fa-solid fa-building"></i> {{ $apartment->apartment_number }} ({{ $apartment->floor->block->name ?? '' }})
                                        </span>
                                    @endforeach
                                </td>
                                <td class="col-role">
                                    @php
                                        $selfRelationship = $selfResident->relationship ?? null;
                                    @endphp
                                    @if ($selfRelationship === 'owner')
                                        <span class="badge badge--owner">Chủ hộ</span>
                                    @elseif ($selfRelationship === 'family_member')
                                        <span class="badge badge--family">Gia đình</span>
                                    @elseif ($selfRelationship === 'tenant')
                                        <span class="badge badge--tenant">Người thuê</span>
                                    @else
                                        <span class="badge badge--secondary">{{ $selfRelationship ?? '—' }}</span>
                                    @endif
                                </td>
                                <td class="col-status">
                                    <span class="badge badge-status--verified"><i class="fa-solid fa-check"></i> Đã có tài khoản</span>
                                </td>
                                <td class="col-action"><i class="fa-solid fa-chevron-right"></i></td>
                            </tr>

                            <!-- Registered Members -->
                            @foreach($registeredMembers as $member)
                                <tr class="member-row table-row--clickable" onclick="window.location.href='{{ route('resident.members.show', ['id' => $member->id, 'type' => 'registered']) }}'" data-name="{{ strtolower($member->user->name ?? 'Cư dân') }}" data-phone="{{ $member->user->phone ?? '' }}" data-status="linked">
                                    <td class="col-stt">{{ $index++ }}</td>
                                    <td class="col-user">
                                        <div class="user-info-cell">
                                            <div class="user-avatar" style="background: #f1f5f9; color: #475569;">
                                                {{ mb_substr($member->user->name ?? 'C', 0, 1) }}
                                            </div>
                                            <span class="user-info-cell__name">{{ $member->user->name ?? 'Cư dân' }}</span>
                                        </div>
                                    </td>
                                    <td class="col-apartment">
                                        <span class="apartment-tag">
                                            <i class="fa-solid fa-building"></i> {{ $member->apartment->apartment_number ?? '—' }} 
                                            ({{ $member->apartment->floor->block->name ?? '' }})
                                        </span>
                                    </td>
                                    <td class="col-role">
                                        @if ($member->relationship === 'family_member')
                                            <span class="badge badge--family">Gia đình</span>
                                        @elseif ($member->relationship === 'tenant')
                                            <span class="badge badge--tenant">Người thuê</span>
                                        @else
                                            <span class="badge badge--secondary">{{ $member->relationship }}</span>
                                        @endif
                                    </td>
                                    <td class="col-status">
                                        <span class="badge badge-status--verified"><i class="fa-solid fa-check"></i> Đã có tài khoản</span>
                                    </td>
                                    <td class="col-action"><i class="fa-solid fa-chevron-right"></i></td>
                                </tr>
                            @endforeach

                            <!-- Declared Members -->
                            @foreach($declaredMembers as $member)
                                <tr class="member-row table-row--clickable" onclick="window.location.href='{{ route('resident.members.show', ['id' => $member->id, 'type' => 'declared']) }}'" data-name="{{ strtolower($member->name) }}" data-phone="" data-status="{{ ($member->invite_id && $member->invite && ($member->invite->uses_count > 0 || $member->invite->status == 'expired')) ? 'linked' : 'unlinked' }}">
                                    <td class="col-stt">{{ $index++ }}</td>
                                    <td class="col-user">
                                        <div class="user-info-cell">
                                            <div class="user-avatar" style="background: #fef2f2; color: #ef4444;">
                                                {{ mb_substr($member->name, 0, 1) }}
                                            </div>
                                            <span class="font-semibold user-info-cell__name">{{ $member->name }}</span>
                                        </div>
                                    </td>
                                    <td class="col-apartment">
                                        <span class="apartment-tag">
                                            <i class="fa-solid fa-building"></i> {{ $member->apartment->apartment_number ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="col-role">
                                        <span class="badge badge--relationship">{{ $member->relationship === 'Thành viên gia đình' ? 'Gia đình' : ($member->relationship ?? 'Khác') }}</span>
                                    </td>
                                    <td class="col-status">
                                        @if ($member->invite_id && $member->invite && ($member->invite->uses_count > 0 || $member->invite->status == 'expired'))
                                            <span class="badge badge-status--verified"><i class="fa-solid fa-check"></i> Đã có tài khoản</span>
                                        @else
                                            <span class="badge badge-status--pending"><i class="fa-regular fa-clock"></i> Chưa liên kết</span>
                                        @endif
                                    </td>
                                    <td class="col-action"><i class="fa-solid fa-chevron-right"></i></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Khai báo nhân khẩu -->
    @if ($isOwner)
    <div class="invite-modal-overlay" id="declareModal" style="display: none;">
        <div class="invite-modal">
            <button type="button" class="invite-modal__close-corner" onclick="closeDeclareModal()" title="Đóng">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="invite-modal__header">
                <div class="invite-modal__icon-wrap">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h3 class="invite-modal__title">Khai báo nhân khẩu</h3>
                <p class="invite-modal__subtitle">Thêm người thân vào hồ sơ căn hộ để quản lý.</p>
            </div>

            <form action="{{ route('resident.members.declared.store') }}" method="POST" class="member-form" style="margin-top: 10px;">
                @csrf
                @if ($apartments->count() === 1)
                    <input type="hidden" name="apartment_id" value="{{ $apartments->first()->id }}">
                @else
                    <div class="form-group">
                        <label for="apartment_id_declared" class="form-label">Căn hộ</label>
                        <select name="apartment_id" id="apartment_id_declared" class="form-input" required>
                            <option value="">-- Chọn căn hộ --</option>
                            @foreach ($apartments as $apartment)
                                <option value="{{ $apartment->id }}">
                                    {{ $apartment->apartment_number }} – {{ $apartment->floor->name ?? ('Tầng ' . $apartment->floor->floor_number) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên thành viên</label>
                    <input type="text" name="name" id="name" class="form-input" placeholder="Ví dụ: Nguyễn Văn B" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="birth_year" class="form-label">Năm sinh <span class="optional-tag">(tùy chọn)</span></label>
                    <input type="text" name="birth_year" id="birth_year" class="form-input" placeholder="Ví dụ: 2015" value="{{ old('birth_year') }}">
                </div>

                <div class="form-group">
                    <label for="relationship" class="form-label">Quan hệ với chủ hộ</label>
                    <select name="relationship" id="relationship" class="form-input" required>
                        <option value="">-- Chọn quan hệ --</option>
                        <option value="Thành viên gia đình" {{ old('relationship') == 'Thành viên gia đình' ? 'selected' : '' }}>Gia đình</option>
                        <option value="Người thuê" {{ old('relationship') == 'Người thuê' ? 'selected' : '' }}>Người thuê</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-8">
                    <i class="fa-solid fa-paper-plane"></i> Lưu khai báo
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL HIỂN THỊ MÃ MỜI VỪA TẠO --}}
    @if (session('new_invite_code'))
        <div class="invite-modal-overlay" id="inviteModal">
            <div class="invite-modal">
                <button type="button" class="invite-modal__close-corner" onclick="closeInviteModal()" title="Đóng">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="invite-modal__header">
                    <div class="invite-modal__icon-wrap">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <h3 class="invite-modal__title">Mã mời đã được tạo!</h3>
                    <p class="invite-modal__subtitle">Sao chép mã hoặc gửi mã QR bên dưới cho người thân / người thuê để họ dùng khi đăng ký tài khoản.</p>
                </div>

                <div class="invite-modal__code-block">
                    <code class="invite-modal__code" id="modalInviteCode">{{ session('new_invite_code') }}</code>
                    <button type="button" class="invite-modal__copy-btn" id="modalCopyBtn"
                            onclick="copyModalCode()" title="Sao chép mã">
                        <i class="fa-regular fa-copy"></i>
                        <span>Sao chép</span>
                    </button>
                </div>

                <div class="invite-modal__qr-card">
                    <div class="invite-modal__qr-img">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->generate(route('resident.register') . '?invite_code=' . urlencode(session('new_invite_code'))) !!}
                    </div>
                    <p class="invite-modal__qr-text"><i class="fa-solid fa-qrcode"></i> Quét QR để đăng ký tài khoản nhanh</p>
                </div>

                <button type="button" class="btn btn-secondary w-100 mt-8" onclick="closeInviteModal()">
                    Đóng cửa sổ
                </button>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        function openDeclareModal() {
            document.getElementById('declareModal').style.display = 'flex';
        }

        function closeDeclareModal() {
            document.getElementById('declareModal').style.display = 'none';
        }

        function closeInviteModal() {
            const modal = document.getElementById('inviteModal');
            if (modal) {
                modal.classList.add('invite-modal-overlay--closing');
                setTimeout(() => modal.remove(), 300);
            }
        }

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

        document.addEventListener('DOMContentLoaded', function() {
            // Hiển thị lại modal declare nếu có lỗi validation
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                // Kiểm tra xem có phải form khai báo không
                if (!document.getElementById('inviteModal')) {
                    openDeclareModal();
                }
            }

            const declareModal = document.getElementById('declareModal');
            if (declareModal) {
                declareModal.addEventListener('click', function(e) {
                    if (e.target === declareModal) closeDeclareModal();
                });
            }

            // Search and Filter Logic
            const searchInput = document.getElementById('memberSearchInput');
            const statusFilter = document.getElementById('statusFilterSelect');
            const tableRows = document.querySelectorAll('#membersTableBody .member-row');

            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const filterValue = statusFilter.value;

                tableRows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const phone = row.getAttribute('data-phone') || '';
                    const status = row.getAttribute('data-status') || '';

                    const matchesSearch = name.includes(searchTerm) || phone.includes(searchTerm);
                    const matchesStatus = filterValue === 'all' || status === filterValue;

                    if (matchesSearch && matchesStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            if (searchInput && statusFilter) {
                searchInput.addEventListener('input', filterTable);
                statusFilter.addEventListener('change', filterTable);
            }
        });

        // Toggling invite code visibility
        function toggleInviteCode(memberId) {
            const codeEl = document.getElementById('invite-code-' + memberId);
            const eyeIcon = document.getElementById('eye-icon-' + memberId);
            const realCode = codeEl.getAttribute('data-real-code');

            if (codeEl.classList.contains('obscured')) {
                codeEl.textContent = realCode;
                codeEl.classList.remove('obscured');
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                codeEl.textContent = 'INV-****-****';
                codeEl.classList.add('obscured');
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Copy inline invite code
        function copyInviteCode(memberId) {
            const codeEl = document.getElementById('invite-code-' + memberId);
            const copyIcon = document.getElementById('copy-icon-' + memberId);
            const realCode = codeEl.getAttribute('data-real-code');

            navigator.clipboard.writeText(realCode).then(() => {
                copyIcon.classList.remove('fa-copy');
                copyIcon.classList.add('fa-check');
                copyIcon.style.color = '#10b981';
                setTimeout(() => {
                    copyIcon.classList.remove('fa-check');
                    copyIcon.classList.add('fa-copy');
                    copyIcon.style.color = '';
                }, 2000);
            });
        }
    </script>
    @endpush
@endsection
