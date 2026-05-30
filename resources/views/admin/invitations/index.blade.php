@extends('layouts.admin.master')

@section('page_title', 'Quản lý mã mời Cư dân')
@section('home_route', route('home'))
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
    @if(session('success'))
        <div class="invitations-alert invitations-alert--success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
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
            <form action="{{ route('admin.invitations.store') }}" method="POST" class="invitations-form">
                @csrf

                <div class="invitations-form__field">
                    <label class="invitations-form__label">
                        Số lượt sử dụng tối đa <span>*</span>
                    </label>
                    <input
                        type="number"
                        name="max_uses"
                        class="invitations-form__input"
                        value="{{ old('max_uses', 1) }}"
                        min="1"
                        placeholder="Ví dụ: 4"
                        required
                    >
                    <small class="invitations-form__hint">Mã có thể dùng cho bao nhiêu tài khoản cư dân.</small>
                </div>

                <div class="invitations-form__field">
                    <label class="invitations-form__label">Ngày hết hạn</label>
                    <input
                        type="datetime-local"
                        name="expires_at"
                        class="invitations-form__input"
                        value="{{ old('expires_at') }}"
                    >
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
                        <th>Lượt sử dụng</th>
                        <th>Ngày hết hạn</th>
                        <th>Người tạo</th>
                        <th>Trạng thái</th>
                        <th style="text-align:right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invitations as $invite)
                        @php
                            $pct = $invite->max_uses > 0 ? ($invite->uses_count / $invite->max_uses) * 100 : 0;
                            $isFull = $pct >= 100;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration + ($invitations->currentPage() - 1) * $invitations->perPage() }}</td>

                            <td>
                                <span
                                    class="inv-code-badge"
                                    title="Nhấn để sao chép"
                                    onclick="copyCode('{{ $invite->code }}')"
                                >
                                    {{ $invite->code }}
                                    <i class="fas fa-copy inv-code-badge__copy-icon"></i>
                                </span>
                            </td>

                            <td>
                                <div class="inv-usage">
                                    <span class="inv-usage__text">
                                        {{ $invite->uses_count }} / {{ $invite->max_uses }}
                                    </span>
                                    <div class="inv-usage__bar-track">
                                        <div
                                            class="inv-usage__bar-fill {{ $isFull ? 'inv-usage__bar-fill--full' : '' }}"
                                            style="width: {{ min($pct, 100) }}%"
                                        ></div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($invite->expires_at)
                                    <span class="inv-expiry">{{ $invite->expires_at->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="inv-expiry--none">Vô thời hạn</span>
                                @endif
                            </td>

                            <td>
                                <span class="inv-creator">
                                    <span class="inv-creator__avatar">#</span>
                                    ID: {{ $invite->created_by ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if($invite->isValid())
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
                                <form
                                    action="{{ route('admin.invitations.destroy', $invite->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã mời này?')"
                                    style="display:inline"
                                >
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
                            <td colspan="7">
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

        @if($invitations->hasPages())
            <div class="invitations-table-footer">
                <span class="invitations-table-footer__stats">
                    Hiển thị {{ $invitations->firstItem() }}–{{ $invitations->lastItem() }} / {{ $invitations->total() }} mã
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
    navigator.clipboard.writeText(code).then(function () {
        const toast = document.getElementById('copyToast');
        toast.classList.add('visible');
        setTimeout(() => toast.classList.remove('visible'), 2000);
    });
}
</script>
@endpush
