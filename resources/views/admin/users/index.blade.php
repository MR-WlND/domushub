@extends('layouts.admin.master')

@section('page_title', 'Danh sách nhân viên')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/users/index.css'])
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý BQL',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật viên',
        'security'   => 'Bảo vệ',
        'cleaning'   => 'Vệ sinh',
    ];

    $statusLabels = [
        'pending' => 'Chờ kích hoạt',
        'active'  => 'Đang hoạt động',
        'banned'  => 'Đã khóa',
    ];
@endphp

@section('content')
<div class="users-page">
    <div class="users-page__header">
        <div>
            <p class="users-page__eyebrow">Nhân sự</p>
            <h1>Danh sách nhân viên</h1>
        </div>
        <div class="users-page__actions">
            <a href="{{ portal_route('system-logs.index') }}" class="users-button users-button--secondary" style="background:#fff; color:#475569; border:1px solid #cbd5e1; display:inline-flex; align-items:center; gap:6px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Lịch sử hệ thống
            </a>
            <a href="{{ portal_route('users.create') }}" class="users-button users-button--primary">Thêm nhân sự</a>
        </div>
    </div>

    @if (session('success'))
        <div class="users-alert users-alert--success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="users-alert users-alert--danger">{{ $errors->first() }}</div>
    @endif

    <form class="users-filter" method="GET" action="{{ portal_route('users.index') }}">
        <label class="users-filter__field">
            <span>Tìm kiếm</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email">
        </label>

        <label class="users-filter__field">
            <span>Vai trò</span>
            <select name="role">
                <option value="">Tất cả vai trò</option>
                @foreach ($roleLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="users-filter__field">
            <span>Trạng thái</span>
            <select name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <div class="users-filter__actions">
            <button type="submit" class="users-button users-button--primary">Lọc</button>
            <a href="{{ portal_route('users.index') }}" class="users-button users-button--secondary">Xóa lọc</a>
        </div>
    </form>

    <div class="users-table-card">
        <div class="users-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        @php
                            $displayRole = $roleLabels[$user->role] ?? $user->role;
                        @endphp
                        <tr>
                            <td>
                                <div class="users-identity">
                                    @if ($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="users-avatar-img">
                                    @else
                                        <span class="users-avatar-initial">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                                    @endif
                                    <div class="users-identity__details">
                                        <span class="users-identity__name">{{ $user->name }}</span>
                                        <span class="users-identity__email">{{ $user->email }}</span>
                                        <span class="users-identity__joined">Ngày tham gia: {{ optional($user->created_at)->format('d/m/Y') ?? 'Chưa có' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="users-role-text">{{ $displayRole }}</span>
                            </td>
                            <td>
                                <span class="status-pill status-pill--{{ $user->status }}">
                                    <span class="status-dot"></span>
                                    {{ $statusLabels[$user->status] ?? $user->status }}
                                </span>
                                @if($user->hasFaceId())
                                    <span style="font-size:10px; background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; padding:2px 8px; border-radius:50px; font-weight:700; display:block; margin-top:4px; width:fit-content;" title="Đăng ký lúc: {{ optional($user->face_registered_at)->format('d/m/Y H:i') }}">
                                        ✓ Face ID Active
                                    </span>
                                @else
                                    <span style="font-size:10px; background:#f8fafc; color:#94a3b8; border:1px solid #e2e8f0; padding:2px 8px; border-radius:50px; font-weight:600; display:block; margin-top:4px; width:fit-content;">
                                        ⚪ Chưa có Face ID
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                    <button type="button" class="users-button users-button--secondary" style="height:32px; padding:0 10px; font-size:12px; display:inline-flex; align-items:center; gap:5px; background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;"
                                            onclick="openRegisterFaceModal({{ json_encode($user) }})">
                                        📸 Đăng ký Face ID
                                    </button>

                                    @if(auth()->user()->role !== 'admin')
                                        <button type="button" class="users-button users-button--secondary" style="height:32px; padding:0 10px; font-size:12px; display:inline-flex; align-items:center; gap:5px;"
                                                onclick="openQrCardModal({{ json_encode($user) }}, '{{ $displayRole }}')">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                                            </svg>
                                            Thẻ QR
                                        </button>
                                    @endif

                                    <a href="{{ portal_route('users.edit', $user) }}" class="btn-edit-permissions">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                        </svg>
                                        Sửa hồ sơ
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="users-empty">Không tìm thấy tài khoản phù hợp.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="users-table-footer">
            <div class="users-table-footer__stats">
                Hiển thị {{ $users->count() }} trên {{ $users->total() }} nhân sự
            </div>
            <div>
                {{ $users->appends(request()->query())->links('admin.users.pagination') }}
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Thẻ QR Nhân Viên ── --}}
<div id="qrCardModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:20px; width:100%; max-width:400px; padding:28px; box-shadow:0 20px 50px rgba(0,0,0,0.25); text-align:center; position:relative;">
        <div style="background:linear-gradient(135deg,#0f172a,#1e3a8a); margin:-28px -28px 20px; padding:24px; border-radius:20px 20px 0 0; color:#fff; text-align:center;">
            <div style="font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#93c5fd;">DOMUSHUB PROPERTY MANAGEMENT</div>
            <div style="font-size:18px; font-weight:800; margin-top:2px;">THẺ NHÂN VIÊN CHẤM CÔNG</div>
        </div>
        <div style="margin-bottom:16px;">
            <div id="qrUserAvatar" style="width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#7c3aed); color:#fff; font-size:28px; font-weight:800; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; border:3px solid #eff6ff; box-shadow:0 4px 12px rgba(37,99,235,0.2);">U</div>
            <div id="qrUserName" style="font-size:18px; font-weight:800; color:#0f172a;">Nguyễn Văn A</div>
            <div id="qrUserRole" style="font-size:13px; font-weight:700; color:#2563eb; margin-top:2px;">Quản lý Ban Quản Lý</div>
            <div id="qrUserEmail" style="font-size:12px; color:#64748b; margin-top:2px;">user@example.com</div>
        </div>
        <div style="background:#f8fafc; border:2px dashed #cbd5e1; border-radius:16px; padding:16px; display:inline-block; margin-bottom:16px;">
            <img id="qrImage" src="" alt="Mã QR Chấm Công" style="width:180px; height:180px; display:block;">
            <div id="qrCodeText" style="font-size:11px; font-weight:700; color:#475569; margin-top:8px; font-family:monospace;">DOMUSHUB_STAFF:1</div>
        </div>
        <p style="font-size:12px; color:#64748b; margin:0 0 20px;">Sử dụng thẻ này để quét tại Máy Quét QR ở Sảnh BQL để Check-In / Check-Out ca làm việc.</p>
        <div style="display:flex; justify-content:center; gap:10px;">
            <button type="button" class="users-button users-button--secondary" onclick="document.getElementById('qrCardModal').style.display='none'">Đóng</button>
            <button type="button" class="users-button users-button--primary" onclick="window.print()">🖨 In Thẻ</button>
        </div>
    </div>
</div>

{{-- ── Modal Đăng Ký Face ID ── --}}
<div id="registerFaceModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.65); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:20px; width:100%; max-width:480px; padding:28px; box-shadow:0 20px 60px rgba(0,0,0,0.3); text-align:center; position:relative;">
        <h3 style="margin-top:0; margin-bottom:6px; font-size:19px; color:#0f172a;" id="faceRegModalTitle">Đăng Ký Khuôn Mặt Face ID</h3>
        <p style="font-size:13px; color:#64748b; margin-top:0; margin-bottom:20px;" id="faceRegModalSub">Yêu cầu nhân viên nhìn thẳng vào webcam để đăng ký Face ID.</p>
        <div style="position:relative; width:100%; height:280px; background:#000; border-radius:16px; overflow:hidden; border:2px solid #2563eb; margin-bottom:20px; display:flex; align-items:center; justify-content:center;">
            <video id="faceRegVideo" style="width:100%; height:100%; object-fit:cover;" autoplay playsinline muted></video>
            <canvas id="faceRegCanvas" style="display:none;"></canvas>
            <div style="position:absolute; top:0; left:0; right:0; bottom:0; pointer-events:none; display:flex; align-items:center; justify-content:center;">
                <div style="width:170px; height:210px; border:2px dashed #38bdf8; border-radius:100px/120px; box-shadow:0 0 0 9999px rgba(15,23,42,0.45);"></div>
            </div>
        </div>
        <div id="faceRegAlert" style="display:none; padding:12px; border-radius:10px; font-size:13px; font-weight:700; margin-bottom:16px;"></div>
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="users-button users-button--secondary" onclick="closeRegisterFaceModal()">Hủy</button>
            <button type="button" class="users-button users-button--primary" id="saveFaceIdBtn" onclick="saveEmployeeFaceId()" style="background:#16a34a; border-color:#16a34a;">📸 Quét & Lưu Face ID</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentFaceRegUser = null;
let faceRegStream = null;

async function openRegisterFaceModal(user) {
    currentFaceRegUser = user;
    document.getElementById('faceRegModalTitle').textContent = `Đăng Ký Face ID: ${user.name}`;
    document.getElementById('registerFaceModal').style.display = 'flex';
    document.getElementById('faceRegAlert').style.display = 'none';

    const video = document.getElementById('faceRegVideo');
    try {
        faceRegStream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } });
        video.srcObject = faceRegStream;
    } catch (err) {
        alert('Không thể mở Webcam. Vui lòng cho phép truy cập camera trên trình duyệt.');
    }
}

function closeRegisterFaceModal() {
    if (faceRegStream) {
        faceRegStream.getTracks().forEach(track => track.stop());
        faceRegStream = null;
    }
    document.getElementById('registerFaceModal').style.display = 'none';
}

async function saveEmployeeFaceId() {
    if (!currentFaceRegUser) return;

    const btn = document.getElementById('saveFaceIdBtn');
    btn.disabled = true;
    btn.textContent = 'Đang lưu mẫu khuôn mặt...';

    const video = document.getElementById('faceRegVideo');
    const canvas = document.getElementById('faceRegCanvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const faceBase64 = canvas.toDataURL('image/jpeg', 0.9);

    try {
        const url = `/admin/users/${currentFaceRegUser.id}/register-faceid`;
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ face_data: faceBase64 })
        });

        const json = await res.json();
        const alertBox = document.getElementById('faceRegAlert');
        alertBox.style.display = 'block';

        if (json.success) {
            alertBox.style.background = '#f0fdf4';
            alertBox.style.color = '#15803d';
            alertBox.textContent = `✓ ${json.message}`;

            setTimeout(() => {
                closeRegisterFaceModal();
                window.location.reload();
            }, 1200);
        } else {
            alertBox.style.background = '#fef2f2';
            alertBox.style.color = '#b91c1c';
            alertBox.textContent = `⚠ ${json.message}`;
            btn.disabled = false;
            btn.textContent = '📸 Quét & Lưu Face ID';
        }
    } catch(err) {
        alert('Lỗi kết nối máy chủ khi lưu Face ID.');
        btn.disabled = false;
        btn.textContent = '📸 Quét & Lưu Face ID';
    }
}

function openQrCardModal(user, displayRole) {
    const modal = document.getElementById('qrCardModal');
    document.getElementById('qrUserName').textContent = user.name || 'Nhân viên';
    document.getElementById('qrUserRole').textContent = displayRole || user.role;
    document.getElementById('qrUserEmail').textContent = user.email || '';
    
    const qrData = `DOMUSHUB_STAFF:${user.id}`;
    document.getElementById('qrCodeText').textContent = qrData;
    
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrData)}`;
    document.getElementById('qrImage').src = qrUrl;
    
    const avatarEl = document.getElementById('qrUserAvatar');
    if (user.avatar) {
        avatarEl.innerHTML = `<img src="/storage/${user.avatar}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">`;
    } else {
        avatarEl.textContent = (user.name || 'U').substring(0, 1).toUpperCase();
    }
    
    modal.style.display = 'flex';
}
</script>
@endpush
