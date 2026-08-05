@extends('layouts.admin.master')

@section('page_title', 'Thu tiền thủ công')

@push('styles')
<style>
.mp-wrap { max-width: 1200px; margin: 0 auto; padding: 20px; }
.mp-header { margin-bottom: 24px; }
.mp-title { font-size: 24px; font-weight: 800; color: #1e293b; margin: 0 0 6px 0; }
.mp-subtitle { font-size: 14px; color: #64748b; margin: 0; }

.mp-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }

/* Tra cứu */
.mp-search-box { display: flex; gap: 10px; margin-top: 10px; }
.mp-search-input { flex: 1; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; }
.mp-search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }
.mp-search-btn { background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; }
.mp-search-btn:hover { background: #1d4ed8; }

/* Grouped Apartment Card */
.mp-apt-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
.mp-apt-header { background: #f8fafc; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
.mp-apt-info { display: flex; align-items: center; gap: 12px; }
.mp-apt-badge { background: #2563eb; color: #fff; font-size: 14px; font-weight: 800; padding: 6px 14px; border-radius: 6px; }
.mp-apt-owner { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
.mp-apt-sub { font-size: 13px; color: #64748b; margin: 2px 0 0 0; }

.mp-apt-stats { display: flex; align-items: center; gap: 24px; }
.mp-stat-item { text-align: right; }
.mp-stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px; }
.mp-stat-val { font-size: 17px; font-weight: 800; color: #2563eb; }

/* Table */
.mp-table { width: 100%; border-collapse: collapse; }
.mp-table th { background: #ffffff; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; text-align: left; }
.mp-table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
.mp-table tr:last-child td { border-bottom: none; }

.mp-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block; }
.mp-badge--unpaid { background: #fef3c7; color: #d97706; }
.mp-badge--partial { background: #e0f2fe; color: #0284c7; }
.mp-badge--overdue { background: #fee2e2; color: #dc2626; }

.mp-invoice-code { font-weight: 700; color: #2563eb; text-decoration: none; }
.mp-invoice-code:hover { text-decoration: underline; }
.mp-invoice-desc { font-size: 12px; color: #64748b; margin-top: 2px; }

/* Action Buttons */
.mp-btn { padding: 9px 18px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.mp-btn--primary { background: #16a34a; color: #fff; }
.mp-btn--primary:hover { background: #15803d; }
.mp-btn--outline { background: #fff; border: 1px solid #cbd5e1; color: #334155; }
.mp-btn--outline:hover { border-color: #2563eb; color: #2563eb; }
</style>
@endpush

@section('content')
<div class="mp-wrap">

    {{-- Page Header --}}
    <div class="mp-header">
        <h1 class="mp-title">Thu tiền thủ công</h1>
        <p class="mp-subtitle">Danh sách các hóa đơn chưa thanh toán sắp xếp nhóm theo từng căn hộ.</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Search Card --}}
    <div class="mp-card">
        <label style="font-size:14px; font-weight:700; color:#1e293b;">
            <i class="fas fa-search" style="color:#3b82f6; margin-right:4px;"></i> Tra cứu nhanh Căn hộ / Chủ hộ
        </label>
        <div class="mp-search-box">
            <input type="text" id="searchInput" class="mp-search-input" placeholder="Nhập mã căn hộ, tên chủ hộ, số điện thoại..." autocomplete="off" />
            <button type="button" class="mp-search-btn" id="searchBtn">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </div>
        <div id="searchError" style="display:none; color:#ef4444; font-size:13px; margin-top:8px;"></div>
    </div>

    {{-- Apartments Container (Rendered dynamically by JS) --}}
    <div id="apartmentsContainer">
        <div id="loadingMsg" style="text-align:center; padding:40px; color:#64748b; font-size:15px;">
            <i class="fas fa-spinner fa-spin" style="font-size:24px; color:#2563eb; display:block; margin-bottom:10px;"></i>
            Đang tải danh sách hóa đơn chưa thanh toán theo căn hộ...
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const SEARCH_URL = "{{ portal_route('manual-payment.search') }}";
const PROCESS_URL = "{{ portal_route('manual-payment.process') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

function doSearch() {
    const q = document.getElementById('searchInput').value.trim();
    const errEl = document.getElementById('searchError');
    const container = document.getElementById('apartmentsContainer');
    errEl.style.display = 'none';

    container.innerHTML = `
        <div style="text-align:center; padding:40px; color:#64748b; font-size:15px;">
            <i class="fas fa-spinner fa-spin" style="font-size:24px; color:#2563eb; display:block; margin-bottom:10px;"></i>
            Đang tải dữ liệu...
        </div>
    `;

    fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            errEl.textContent = data.message || 'Không tìm thấy kết quả.';
            errEl.style.display = 'block';
            container.innerHTML = `
                <div class="mp-card" style="text-align:center; padding:40px; color:#94a3b8;">
                    <i class="fas fa-info-circle" style="font-size:32px; margin-bottom:10px; color:#cbd5e1; display:block;"></i>
                    <div>${data.message || 'Không tìm thấy dữ liệu căn hộ nợ nào.'}</div>
                </div>
            `;
            return;
        }

        renderApartmentGroups(data.apartments);
    })
    .catch(() => {
        errEl.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
        errEl.style.display = 'block';
    });
}

function renderApartmentGroups(apartments) {
    const container = document.getElementById('apartmentsContainer');
    container.innerHTML = '';

    if (!apartments || apartments.length === 0) {
        container.innerHTML = `
            <div class="mp-card" style="text-align:center; padding:40px; color:#16a34a;">
                <i class="fas fa-check-circle" style="font-size:36px; margin-bottom:10px; display:block;"></i>
                <div style="font-size:16px; font-weight:700;">Tất cả căn hộ đã thanh toán đầy đủ!</div>
                <div style="font-size:13px; color:#64748b; margin-top:4px;">Không có hóa đơn chưa thanh toán nào trong hệ thống.</div>
            </div>
        `;
        return;
    }

    apartments.forEach(apt => {
        const card = document.createElement('div');
        card.className = 'mp-apt-card';

        let rowsHtml = '';
        apt.invoices.forEach(inv => {
            const badgeClass = inv.status === 'overdue' ? 'mp-badge--overdue' : (inv.status === 'partial_paid' ? 'mp-badge--partial' : 'mp-badge--unpaid');
            rowsHtml += `
                <tr>
                    <td style="width:40px;">
                        <input type="checkbox" class="apt-inv-chk-${apt.apartment_id}" name="invoice_ids[]" value="${inv.id}" data-amount="${inv.total_amount}" checked onchange="recalcAptTotal(${apt.apartment_id})" />
                    </td>
                    <td>
                        <a href="${inv.show_url}" target="_blank" class="mp-invoice-code" title="Xem chi tiết hóa đơn">
                            ${inv.invoice_code} <i class="fas fa-external-link-alt" style="font-size:11px; margin-left:3px;"></i>
                        </a>
                        <div class="mp-invoice-desc">${inv.description}</div>
                    </td>
                    <td>Tháng ${String(inv.billing_month).padStart(2, '0')}/${inv.billing_year}</td>
                    <td>${inv.due_date || '---'}</td>
                    <td><span class="mp-badge ${badgeClass}">${inv.status_label}</span></td>
                    <td style="text-align:right; font-weight:700; color:#1e293b;">${Number(inv.total_amount).toLocaleString('vi-VN')} đ</td>
                </tr>
            `;
        });

        card.innerHTML = `
            <form method="POST" action="${PROCESS_URL}" id="aptForm_${apt.apartment_id}">
                <input type="hidden" name="_token" value="${CSRF_TOKEN}" />
                <input type="hidden" name="apartment_id" value="${apt.apartment_id}" />
                <input type="hidden" name="payment_method" value="cash" />
                <input type="hidden" name="amount_received" id="aptAmountReceived_${apt.apartment_id}" value="${apt.total_debt}" />

                <div class="mp-apt-header">
                    <div class="mp-apt-info">
                        <div class="mp-apt-badge">Căn ${apt.apartment_code || apt.apartment_number}</div>
                        <div>
                            <h3 class="mp-apt-owner">${apt.owner_name} <span style="font-weight:400; color:#64748b; font-size:13px;">(${apt.owner_phone})</span></h3>
                            <p class="mp-apt-sub">${apt.block_name} ${apt.floor_name ? '· ' + apt.floor_name : ''} · Email: ${apt.owner_email}</p>
                        </div>
                    </div>

                    <div class="mp-apt-stats">
                        <div class="mp-stat-item">
                            <span class="mp-stat-label">Số HĐ chưa TT</span>
                            <span style="font-size:15px; font-weight:700; color:#475569;">${apt.unpaid_count} hóa đơn</span>
                        </div>
                        <div class="mp-stat-item">
                            <span class="mp-stat-label">Tổng nợ cần thu</span>
                            <span class="mp-stat-val" id="aptTotalDisplay_${apt.apartment_id}">${Number(apt.total_debt).toLocaleString('vi-VN')} đ</span>
                        </div>
                    </div>
                </div>

                <table class="mp-table">
                    <thead>
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" id="checkAll_${apt.apartment_id}" checked title="Chọn tất cả" onchange="toggleCheckAllApt(${apt.apartment_id}, this.checked)" />
                            </th>
                            <th>Mã HĐ / Dịch vụ</th>
                            <th>Kỳ hóa đơn</th>
                            <th>Hạn thanh toán</th>
                            <th>Trạng thái</th>
                            <th style="text-align:right;">Số tiền (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                </table>

                <div style="padding:14px 20px; background:#fafafa; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end; align-items:center;">
                    <a href="${apt.apartment_url}" class="mp-btn mp-btn--primary" style="padding:10px 22px; font-size:14px; text-decoration:none;">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </a>
                </div>
            </form>
        `;

        container.appendChild(card);
    });
}

function toggleCheckAllApt(aptId, isChecked) {
    document.querySelectorAll(`.apt-inv-chk-${aptId}`).forEach(chk => {
        chk.checked = isChecked;
    });
    recalcAptTotal(aptId);
}

function recalcAptTotal(aptId) {
    let total = 0;
    document.querySelectorAll(`.apt-inv-chk-${aptId}:checked`).forEach(chk => {
        total += parseFloat(chk.dataset.amount || 0);
    });

    const displayEl = document.getElementById(`aptTotalDisplay_${aptId}`);
    const inputEl = document.getElementById(`aptAmountReceived_${aptId}`);
    if (displayEl) displayEl.textContent = Number(total).toLocaleString('vi-VN') + ' đ';
    if (inputEl) inputEl.value = total;
}

document.getElementById('searchBtn').addEventListener('click', doSearch);
document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
});

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const initialQuery = urlParams.get('q');
    if (initialQuery) {
        document.getElementById('searchInput').value = initialQuery;
    }
    doSearch();
});

@if(session('print_receipt_url'))
    window.open("{{ session('print_receipt_url') }}", "_blank");
@endif
</script>
@endpush
