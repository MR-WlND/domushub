@extends('layouts.resident.master')

@section('title', 'Resident Dashboard')

@section('content')
@push('styles')
    @vite(['resources/css/pages/resident/members.css'])
@endpush
    <section class="resident-dashboard">
        <div class="resident-dashboard__hero">
            <div>
                <p class="resident-dashboard__eyebrow">Cư dân</p>
                <h2 class="resident-dashboard__title">Bảng điều khiển Cư dân</h2>
                <p class="resident-dashboard__intro">
                    Chào mừng bạn trở lại, {{ auth()->user()->name ?? 'Cư dân' }}. Đây là trang tổng quan đơn giản dành cho
                    cư dân.
                </p>
            </div>
            <span class="resident-dashboard__badge">Hoạt động</span>
        </div>
        @if(auth()->check() && auth()->user()->role === 'owner')
            <div class="resident-dashboard__quick">
                <a href="{{ route('resident.members.index') }}" class="btn btn-primary">Quản lý thành viên hộ</a>
            </div>
            @php
                $ownerMember = \App\Models\ApartmentMember::where('user_id', auth()->id())
                    ->where('relationship', 'owner')
                    ->first();

                $memInvites = collect();
                if ($ownerMember) {
                    $memInvites = \App\Models\Invitation::with('apartmentMember')
                        ->where('type', 'member_invite')
                        ->where('apartment_id', $ownerMember->apartment_id)
                        ->where('status', '!=', 'cancelled')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            @endphp

            @if($memInvites->isNotEmpty())
                <div class="member-card">
                    <h2>Mã mời thành viên</h2>
                    <p>Những mã mời dành cho nhân khẩu trong căn hộ của bạn.</p>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Mã</th>
                                <th>Thành viên</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memInvites as $invite)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invite->code }}</td>
                                    <td>{{ optional($invite->apartmentMember)->name ?? '—' }}</td>
                                    <td>{{ ucfirst($invite->status) }}</td>
                                    <td>{{ $invite->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </section>
@endsection
