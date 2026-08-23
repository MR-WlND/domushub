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
                                        <span class="badge badge--family"><i class="fa-solid fa-house-user"></i> Thành viên gia đình</span>
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
    <div class="card__header">
        <h2 class="card__title">Cư dân có tài khoản liên kết</h2>
        <p class="card__description">Danh sách các tài khoản cư dân chính thức đã xác thực và liên kết vào căn hộ trong hệ thống.</p>
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
                        <th>Quan hệ / Vai trò</th>
                        <th>Ngày gia nhập</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Hiển thị tài khoản của chính mình trước -->
                    <tr class="table-row--self">
                        <td>—</td>
                        <td>
                            <div class="user-info-cell">
                                <span class="user-info-cell__name">{{ $user->name }}</span>
                                <span class="badge badge--self">Bạn</span>
                            </div>
                        </td>
                        <td>
                            <div class="contact-cell">
                                <span><i class="fa-solid fa-phone"></i> {{ $user->phone }}</span>
                                <span class="contact-cell__email"><i class="fa-regular fa-envelope"></i> {{ $user->email }}</span>
                            </div>
                        </td>
                        <td>
                            @foreach ($apartments as $apartment)
                                <span class="apartment-tag">
                                    {{ $apartment->apartment_number }} ({{ $apartment->floor->block->name ?? '' }})
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @php
                                $selfRelationship = $selfResident->relationship ?? null;
                            @endphp
                            @if ($selfRelationship === 'owner')
                                <span class="badge badge--owner">
                                    <i class="fa-solid fa-crown"></i> Chủ hộ
                                </span>
                            @elseif ($selfRelationship === 'family_member')
                                <span class="badge badge--family">
                                    <i class="fa-solid fa-house-user"></i> Thành viên gia đình
                                </span>
                            @elseif ($selfRelationship === 'tenant')
                                <span class="badge badge--tenant">
                                    <i class="fa-solid fa-key"></i> Người thuê
                                </span>
                            @else
                                <span class="badge badge--secondary">
                                    {{ $selfRelationship ?? '—' }}
                                </span>
                            @endif
                        </td>
                        <td>—</td>
                    </tr>

                    <!-- Hiển thị các cư dân liên kết khác -->
                    @forelse($registeredMembers as $member)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="user-info-cell__name">{{ $member->user->name ?? 'Cư dân' }}</span>
                            </td>
                            <td>
                                <div class="contact-cell">
                                    <span><i class="fa-solid fa-phone"></i> {{ $member->user->phone ?? '—' }}</span>
                                    <span class="contact-cell__email"><i class="fa-regular fa-envelope"></i> {{ $member->user->email ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="apartment-tag">
                                    {{ $member->apartment->apartment_number ?? '—' }} 
                                    ({{ $member->apartment->floor->block->name ?? '' }})
                                </span>
                            </td>
                            <td>
                                @if ($member->relationship === 'family_member')
                                    <span class="badge badge--family">
                                        <i class="fa-solid fa-house-user"></i> Thành viên gia đình
                                    </span>
                                @elseif ($member->relationship === 'tenant')
                                    <span class="badge badge--tenant">
                                        <i class="fa-solid fa-key"></i> Người thuê
                                    </span>
                                @else
                                    <span class="badge badge--secondary">
                                        {{ $member->relationship }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $member->created_at ? $member->created_at->format('d/m/Y') : '—' }}</td>
                        </tr>
                    @empty
                        @if ($registeredMembers->isEmpty() && !$isOwner)
                            <tr>
                                <td colspan="6" class="text-center text-muted py-24">
                                    Không có cư dân liên kết khác trong căn hộ của bạn.
                                </td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
