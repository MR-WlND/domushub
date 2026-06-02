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
                        @if ($isOwner)
                            <th style="width: 120px; text-align: center;">Hành động</th>
                        @endif
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
                            <span class="badge badge--owner">
                                <i class="fa-solid fa-crown"></i> Chủ hộ
                            </span>
                        </td>
                        <td>—</td>
                        @if ($isOwner)
                            <td class="text-center">
                                <span class="text-muted fs-sm">—</span>
                            </td>
                        @endif
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
                                        <i class="fa-solid fa-house-user"></i> Thành viên
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
                            @if ($isOwner)
                                <td class="text-center">
                                    <form action="{{ route('resident.members.registered.destroy', $member->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn gỡ tài khoản cư dân này khỏi căn hộ? Họ sẽ mất quyền truy cập.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-user-minus"></i> Gỡ bỏ
                                        </button>
                                    </form>
                                </td>
                            @endif
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
