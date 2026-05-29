@extends('layouts.admin.master')

@section('page_title', 'Mã mời đăng ký')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
    <div class="dashboard-grid" style="grid-template-columns: 1.1fr 0.9fr; align-items: start;">
        <article class="dashboard-card dashboard-card--primary">
            <p class="dashboard-card__label">Tạo mã mời</p>
            <h2>Tự tạo mã mời đăng ký cư dân</h2>
            <p>Chọn căn hộ, số người tối đa và thời hạn sử dụng. Hệ thống sẽ sinh mã mời ngẫu nhiên để bạn gửi cho cư dân.</p>

            @if (session('status'))
                <div style="margin-top: 16px; padding: 12px 14px; border-radius: 10px; background: #e7f6ec; color: #146c43; font-weight: 600;">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('admin.invitations.store') }}" method="POST" style="display: grid; gap: 14px; margin-top: 18px;">
                @csrf
                <label style="display: grid; gap: 6px; font-weight: 600; color: #00236f;">
                    Căn hộ
                    <select name="apartment_id" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; background: #fff;">
                        <option value="">-- Chọn căn hộ --</option>
                        @foreach($apartments as $apartment)
                            <option value="{{ $apartment->id }}">{{ $apartment->block_name }} - Tầng {{ $apartment->floor_number }} - Căn {{ $apartment->apartment_number }} ({{ $apartment->area }} m²)</option>
                        @endforeach
                    </select>
                </label>

                <label style="display: grid; gap: 6px; font-weight: 600; color: #00236f;">
                    Số người tối đa
                    <input type="number" name="max_residents" min="1" max="20" value="1" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                </label>

                <label style="display: grid; gap: 6px; font-weight: 600; color: #00236f;">
                    Quan hệ mời
                    <select name="relationship" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; background: #fff;">
                        <option value="tenant">Người Thuê Nhà</option>
                        <option value="owner">Người Sở Hữu</option>
                        <option value="family_member">Thành Viên Gia Đình</option>
                    </select>
                </label>

                <label style="display: grid; gap: 6px; font-weight: 600; color: #00236f;">
                    Hạn sử dụng (ngày)
                    <input type="number" name="expired_days" min="1" max="90" value="7" required style="padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px;">
                </label>

                <button type="submit" style="padding: 12px 14px; border: none; border-radius: 10px; background: #00236f; color: #fff; font-weight: 700; cursor: pointer;">Tạo mã mời</button>
            </form>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Danh sách mã mời</p>
            <h2>Mã mời gần đây</h2>
            <p>Danh sách mã mời đã tạo sẽ giúp bạn theo dõi số người còn lại và thời hạn sử dụng.</p>

            <div style="display: grid; gap: 12px; margin-top: 16px;">
                @forelse($invites as $invite)
                    <article style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; background: #fafcff;">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <strong style="color: #00236f;">{{ $invite->invite_code }}</strong>
                            <span style="padding: 4px 8px; border-radius: 999px; background: {{ $invite->status === 'active' ? '#e6f4ea' : '#fff4e5' }}; color: {{ $invite->status === 'active' ? '#137333' : '#b06000' }}; font-size: 12px; font-weight: 700; text-transform: uppercase;">{{ $invite->status }}</span>
                        </div>
                        <p style="margin: 0 0 4px; color: #475569; font-size: 14px;">{{ $invite->block_name }} - Tầng {{ $invite->floor_number }} - Căn {{ $invite->apartment_number }}</p>
                        <p style="margin: 0; color: #475569; font-size: 13px;">Số người tối đa: {{ data_get($invite, 'max_residents', 1) }} · Đã dùng: {{ data_get($invite, 'used_count', 0) }} · Hết hạn: {{ \Carbon\Carbon::parse($invite->expired_at)->format('d/m/Y H:i') }}</p>
                    </article>
                @empty
                    <p style="color: #757682; margin: 0;">Chưa có mã mời nào được tạo.</p>
                @endforelse
            </div>
        </article>
    </div>
@endsection
