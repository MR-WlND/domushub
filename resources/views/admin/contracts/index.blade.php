@extends('layouts.admin.master')

@section('page_title', 'Quản lý Hợp đồng Nhân sự')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/payroll/index.css'])
    <style>
        .contract-card { background:#fff; border:1px solid #e8edf5; border-radius:14px; padding:24px; margin-bottom:24px; box-shadow:0 2px 8px rgba(30,58,138,.04); }
        .contract-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:20px; }
        .contract-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:700; }
        .contract-badge--hieu_luc { background:#f0fdf4; color:#16a34a; }
        .contract-badge--sap_het_han { background:#fff7ed; color:#ea580c; animation:pulse-orange 1.5s infinite; }
        .contract-badge--het_han { background:#fef2f2; color:#dc2626; }
        .contract-badge--thanh_ly { background:#f1f5f9; color:#64748b; }
        @keyframes pulse-orange { 0%,100%{box-shadow:0 0 0 2px rgba(234,88,12,.3);} 50%{box-shadow:0 0 0 5px rgba(234,88,12,.1);} }
    </style>
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật',
        'security'   => 'An ninh',
        'cleaning'   => 'Vệ sinh',
    ];
@endphp

@section('content')
<div class="payroll-page">

    {{-- ── Header ── --}}
    <div class="payroll-page__header">
        <div>
            <p class="payroll-page__eyebrow">Nhân sự › Hợp đồng lao động</p>
            <h1>Quản lý Hợp đồng & Chứng chỉ</h1>
        </div>
        <div class="payroll-page__actions">
            <button type="button" class="pr-btn pr-btn--primary" onclick="document.getElementById('createContractModal').style.display='flex'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tạo hợp đồng mới
            </button>
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if (session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ⚠ {{ $errors->first() }}
        </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="payroll-stats-grid">
        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--blue">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val">{{ $stats['total'] }}</div>
                <div class="pr-stat-card__lbl">Tổng hợp đồng</div>
            </div>
        </div>

        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--green">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val">{{ $stats['active'] }}</div>
                <div class="pr-stat-card__lbl">Đang hiệu lực</div>
            </div>
        </div>

        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--amber">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val" style="color:#ea580c;">{{ $stats['expiring_soon'] }}</div>
                <div class="pr-stat-card__lbl">Sắp hết hạn (30 ngày)</div>
            </div>
        </div>

        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--purple">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val" style="color:#dc2626;">{{ $stats['expired'] }}</div>
                <div class="pr-stat-card__lbl">Đã hết hạn</div>
            </div>
        </div>
    </div>

    {{-- ── Form Lọc ── --}}
    <form class="payroll-filter" method="GET" action="{{ portal_route('contracts.index') }}">
        <div class="payroll-filter__field" style="flex:2;">
            <span>Tìm kiếm</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Mã HĐ, tên nhân viên..." class="pr-form-control">
        </div>

        <div class="payroll-filter__field">
            <span>Trạng thái</span>
            <select name="status" class="pr-form-control">
                <option value="">Tất cả trạng thái</option>
                <option value="hieu_luc" @selected(request('status') === 'hieu_luc')>Đang hiệu lực</option>
                <option value="sap_het_han" @selected(request('status') === 'sap_het_han')>Sắp hết hạn</option>
                <option value="het_han" @selected(request('status') === 'het_han')>Đã hết hạn</option>
                <option value="thanh_ly" @selected(request('status') === 'thanh_ly')>Đã thanh lý</option>
            </select>
        </div>

        <div class="payroll-filter__field">
            <span>Loại hợp đồng</span>
            <select name="type" class="pr-form-control">
                <option value="">Tất cả loại HĐ</option>
                <option value="thu_viec" @selected(request('type') === 'thu_viec')>Thử việc</option>
                <option value="xac_dinh_thoi_han" @selected(request('type') === 'xac_dinh_thoi_han')>Xác định thời hạn</option>
                <option value="khong_thoi_han" @selected(request('type') === 'khong_thoi_han')>Không xác định thời hạn</option>
                <option value="vendor_thue_ngoai" @selected(request('type') === 'vendor_thue_ngoai')>Vendor / Thuê ngoài</option>
                <option value="thoi_vu" @selected(request('type') === 'thoi_vu')>Thời vụ / Dự án</option>
            </select>
        </div>

        <div style="display:flex; gap:8px;">
            <button type="submit" class="pr-btn pr-btn--primary" style="height:40px;">Lọc</button>
            <a href="{{ portal_route('contracts.index') }}" class="pr-btn pr-btn--secondary" style="height:40px;">Xóa lọc</a>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="payroll-table-card">
        <div class="payroll-table-wrap">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Nhân viên</th>
                        <th>Loại hợp đồng</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày hết hạn</th>
                        <th>Lương cơ bản</th>
                        <th>Trạng thái</th>
                        <th style="text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $ct)
                        <tr>
                            <td><strong>{{ $ct->ma_hop_dong }}</strong></td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="width:34px; height:34px; border-radius:50%; background:#e2e8f0; color:#334155; font-weight:600; display:flex; align-items:center; justify-content:center; font-size:13px;">
                                        {{ strtoupper(mb_substr($ct->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                    <div>
                                        <div style="font-weight:600; color:#0f172a;">{{ $ct->user->name ?? '—' }}</div>
                                        <div style="font-size:12px; color:#64748b;">{{ $roleLabels[$ct->user->role ?? ''] ?? ($ct->user->role ?? '—') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $ct->loai_hop_dong_label }}</td>
                            <td>{{ optional($ct->ngay_bat_dau)->format('d/m/Y') }}</td>
                            <td>
                                @if($ct->ngay_ket_thuc)
                                    <span @if($ct->isExpiringSoon(30)) style="color:#ea580c; font-weight:700;" @endif>
                                        {{ $ct->ngay_ket_thuc->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span style="color:#94a3b8; font-style:italic;">Không thời hạn</span>
                                @endif
                            </td>
                            <td><strong>{{ number_format($ct->luong_co_ban) }} đ</strong></td>
                            <td>
                                <span class="contract-badge contract-badge--{{ $ct->trang_thai }}">
                                    {{ $ct->trang_thai_label }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:6px;">
                                    <button type="button" class="pr-btn pr-btn--secondary" style="height:32px; padding:0 10px; font-size:12px;"
                                            onclick="editContract({{ json_encode($ct) }})">
                                        Sửa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:40px; color:#64748b;">
                                Không tìm thấy hợp đồng lao động nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
            <div style="padding:16px 20px; border-top:1px solid #f1f5f9;">
                {{ $contracts->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ── Modal Tạo Hợp đồng mới ── --}}
<div id="createContractModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:540px; padding:28px; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; margin-bottom:20px; font-size:18px;">Tạo Hợp đồng lao động mới</h3>
        <form method="POST" action="{{ portal_route('contracts.store') }}">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Nhân viên <span style="color:#ef4444;">*</span></label>
                    <select name="user_id" class="pr-form-control" style="width:100%;" required>
                        <option value="">-- Chọn nhân viên --</option>
                        @foreach($staffList as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $roleLabels[$st->role] ?? $st->role }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Mã hợp đồng <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="ma_hop_dong" class="pr-form-control" style="width:100%;" value="HĐ-{{ strtoupper(Str::random(5)) }}" required>
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Loại hợp đồng <span style="color:#ef4444;">*</span></label>
                <select name="loai_hop_dong" class="pr-form-control" style="width:100%;" required>
                    <option value="thu_viec">Thử việc (2 tháng)</option>
                    <option value="xac_dinh_thoi_han" selected>Hợp đồng xác định thời hạn</option>
                    <option value="khong_thoi_han">Hợp đồng không xác định thời hạn</option>
                    <option value="vendor_thue_ngoai">Hợp đồng Vendor / Thuê ngoài (Bảo vệ, Vệ sinh...)</option>
                    <option value="thoi_vu">Hợp đồng Thời vụ / Dự án</option>
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ngày bắt đầu <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="ngay_bat_dau" class="pr-form-control" style="width:100%;" value="{{ today()->toDateString() }}" required>
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ngày hết hạn</label>
                    <input type="date" name="ngay_ket_thuc" class="pr-form-control" style="width:100%;">
                </div>
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Lương cơ bản hợp đồng (VNĐ) <span style="color:#ef4444;">*</span></label>
                <input type="number" name="luong_co_ban" class="pr-form-control" style="width:100%;" placeholder="Ví dụ: 9000000" min="0" step="100000" required>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ghi chú</label>
                <textarea name="ghi_chu" class="pr-form-control" style="width:100%; height:70px;" placeholder="Ghi chú thêm về điều khoản, phụ lục..."></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="pr-btn pr-btn--secondary" onclick="document.getElementById('createContractModal').style.display='none'">Hủy</button>
                <button type="submit" class="pr-btn pr-btn--primary">Lưu hợp đồng</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Sửa Hợp đồng ── --}}
<div id="editContractModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:540px; padding:28px; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; margin-bottom:20px; font-size:18px;">Cập nhật Hợp đồng lao động</h3>
        <form method="POST" id="editContractForm">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Loại hợp đồng</label>
                <select name="loai_hop_dong" id="edit_loai_hop_dong" class="pr-form-control" style="width:100%;" required>
                    <option value="thu_viec">Thử việc (2 tháng)</option>
                    <option value="xac_dinh_thoi_han">Hợp đồng xác định thời hạn</option>
                    <option value="khong_thoi_han">Hợp đồng không xác định thời hạn</option>
                    <option value="vendor_thue_ngoai">Hợp đồng Vendor / Thuê ngoài</option>
                    <option value="thoi_vu">Hợp đồng Thời vụ / Dự án</option>
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ngày bắt đầu</label>
                    <input type="date" name="ngay_bat_dau" id="edit_ngay_bat_dau" class="pr-form-control" style="width:100%;" required>
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ngày hết hạn</label>
                    <input type="date" name="ngay_ket_thuc" id="edit_ngay_ket_thuc" class="pr-form-control" style="width:100%;">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Lương cơ bản (VNĐ)</label>
                    <input type="number" name="luong_co_ban" id="edit_luong_co_ban" class="pr-form-control" style="width:100%;" required min="0">
                </div>
                <div>
                    <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Trạng thái</label>
                    <select name="trang_thai" id="edit_trang_thai" class="pr-form-control" style="width:100%;" required>
                        <option value="hieu_luc">Đang hiệu lực</option>
                        <option value="sap_het_han">Sắp hết hạn</option>
                        <option value="het_han">Đã hết hạn</option>
                        <option value="thanh_ly">Đã thanh lý</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ghi chú</label>
                <textarea name="ghi_chu" id="edit_ghi_chu" class="pr-form-control" style="width:100%; height:70px;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="pr-btn pr-btn--secondary" onclick="document.getElementById('editContractModal').style.display='none'">Hủy</button>
                <button type="submit" class="pr-btn pr-btn--primary">Cập nhật hợp đồng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editContract(ct) {
    const modal = document.getElementById('editContractModal');
    const form  = document.getElementById('editContractForm');

    form.action = `{{ url(auth()->user()->role) }}/contracts/${ct.id}`;
    document.getElementById('edit_loai_hop_dong').value = ct.loai_hop_dong;
    document.getElementById('edit_ngay_bat_dau').value  = ct.ngay_bat_dau ? ct.ngay_bat_dau.substring(0, 10) : '';
    document.getElementById('edit_ngay_ket_thuc').value = ct.ngay_ket_thuc ? ct.ngay_ket_thuc.substring(0, 10) : '';
    document.getElementById('edit_luong_co_ban').value  = ct.luong_co_ban;
    document.getElementById('edit_trang_thai').value    = ct.trang_thai;
    document.getElementById('edit_ghi_chu').value       = ct.ghi_chu || '';

    modal.style.display = 'flex';
}
</script>
@endpush
