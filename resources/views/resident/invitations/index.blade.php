@extends('layouts.resident.master')

@section('title', 'Tạo mã mời thành viên')

@push('styles')
    @vite(['resources/css/pages/resident/invitations/index.css'])
@endpush

@section('content')
    <section class="resident-section">
        <div class="resident-section__header">
            <p class="resident-section__eyebrow">Quản lý thành viên</p>
            <h1>Tạo mã mời cho người thân</h1>
            <p>Chủ hộ có thể tạo mã mời nội bộ để người thân đăng ký và liên kết vào hộ gia đình.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card__body">
                <form action="{{ route('resident.invitations.store') }}" method="POST" class="invite-form">
                    @csrf

                    <div class="invite-form__row">
                        <label>Chọn căn hộ chủ hộ</label>
                        <select name="apartment_id" class="input-field" required>
                            <option value="">-- Chọn căn hộ --</option>
                            @foreach ($apartments as $apartment)
                                <option value="{{ $apartment->id }}"
                                    {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                    {{ $apartment->apartment_number }} -
                                    {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }} /
                                    {{ $apartment->floor->block->name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('apartment_id')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="invite-form__row">
                        <label>Quan hệ với cư dân</label>
                        <select name="intended_relationship" class="input-field" required>
                            <option value="">-- Chọn quan hệ --</option>
                            <option value="family_member"
                                {{ old('intended_relationship') == 'family_member' ? 'selected' : '' }}>Thành viên gia đình
                            </option>
                            <option value="tenant" {{ old('intended_relationship') == 'tenant' ? 'selected' : '' }}>Người
                                thuê</option>
                        </select>
                        @error('intended_relationship')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="invite-form__row">
                        <label>Ngày hết hạn (tuỳ chọn)</label>
                        <input type="datetime-local" name="expired_at" class="input-field" value="{{ old('expired_at') }}">
                        @error('expired_at')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Tạo mã mời thành viên</button>
                </form>
            </div>
        </div>

        @if ($invites->count())
            <div class="card mt-24">
                <div class="card__body">
                    <h2>Danh sách mã mời đã tạo</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã mời</th>
                                <th>Căn hộ</th>
                                <th>Quan hệ</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invites as $invite)
                                <tr>
                                    <td>{{ $loop->iteration + ($invites->currentPage() - 1) * $invites->perPage() }}</td>
                                    <td>{{ $invite->invite_code }}</td>
                                    <td>
                                        {{ optional($invite->apartment)->apartment_number ?? '—' }}
                                        <br>
                                        {{ optional($invite->apartment->floor)->name ?? 'Tầng ' . optional($invite->apartment->floor)->floor_number }}
                                        / {{ optional($invite->block)->name ?? '—' }}
                                    </td>
                                    <td>
                                        @switch($invite->intended_relationship)
                                            @case('family_member')
                                                Thành viên gia đình
                                            @break

                                            @case('tenant')
                                                Người thuê
                                            @break

                                            @default
                                                Khác
                                        @endswitch
                                    </td>
                                    <td>{{ $invite->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}</td>
                                    <td>{{ $invite->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pagination-wrapper">
                        {{ $invites->links() }}
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
