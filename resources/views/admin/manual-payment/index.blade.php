@extends('layouts.admin.master')

@section('page_title', 'Thu tiền thủ công')

@push('styles')
<style>
/* ===== Layout Wrapper ===== */
.mp-wrap {
    padding: 24px 28px;
    background: #f4f6f9;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

/* ===== Page Header ===== */
.mp-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 22px;
}
.mp-header__title {
    font-size: 22px;
    font-weight: 700;
    color: #1a2236;
    margin: 0 0 4px;
}
.mp-header__sub {
    font-size: 13px;
    color: #7a8399;
    margin: 0;
}

/* ===== Card Base ===== */
.mp-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8ecf1;
    padding: 20px 22px;
    margin-bottom: 18px;
}

/* ===== Top Row (2 cột) ===== */
.mp-top-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}
.mp-search-card,
.mp-resident-card {
    margin-bottom: 0;
}

/* ===== Alert ===== */
.mp-alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: 500;
}
.mp-alert--success {
    background: #e8f5e9;
    color: #2e7d32;
    border-left: 4px solid #43a047;
}
.mp-alert--error {
    background: #fdecea;
    color: #c62828;
    border-left: 4px solid #e53935;
}

/* ===== Search Box ===== */
.mp-search-label {
    font-size: 14px;
    font-weight: 600;
    color: #3d4a60;
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 12px;
}
.mp-search-label i { color: #3b82f6; }
.mp-search-input-wrap {
    position: relative;
}
.mp-search-input {
    width: 100%;
    height: 44px;
    border: 1.5px solid #d1d9e6;
    border-radius: 8px;
    padding: 0 46px 0 14px;
    font-size: 14px;
    color: #1a2236;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.mp-search-input:focus { border-color: #3b82f6; }
.mp-search-btn {
    position: absolute;
    right: 0; top: 0;
    width: 44px; height: 44px;
    background: #3b82f6;
    border: none;
    border-radius: 0 8px 8px 0;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.mp-search-btn:hover { background: #2563eb; }
.mp-search-hint {
    font-size: 12px;
    color: #a0abc0;
    margin-top: 7px;
}
.mp-search-error {
    font-size: 13px;
    color: #e53935;
    margin-top: 8px;
    display: none;
}

/* ===== Resident Card ===== */
.mp-resident-info {
    display: flex;
    align-items: center;
    gap: 16px;
}
.mp-resident-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    background: #e8ecf1;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    color: #a0abc0;
    border: 2px solid #e0e7ff;
}
.mp-resident-avatar img {
    width: 60px; height: 60px;
    border-radius: 50%; object-fit: cover;
}
.mp-resident-name {
    font-size: 18px;
    font-weight: 700;
    color: #1a2236;
    margin: 0 0 4px;
}
.mp-resident-role {
    font-size: 13px;
    color: #7a8399;
    margin: 0 0 8px;
}
.mp-resident-apt-badge {
    display: inline-block;
    background: #eff6ff;
    color: #2563eb;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    padding: 3px 10px;
    border: 1px solid #bfdbfe;
}
.mp-resident-contacts {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-top: 8px;
    font-size: 13px;
    color: #3d4a60;
}
.mp-resident-contacts span { display: flex; align-items: center; gap: 6px; }
.mp-resident-contacts i { color: #3b82f6; width: 14px; }

/* ===== Invoices Table ===== */
.mp-invoices-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}
.mp-invoices-header__icon {
    width: 30px; height: 30px;
    border-radius: 7px;
    background: #eff6ff;
    display: flex; align-items: center; justify-content: center;
    color: #3b82f6;
    font-size: 14px;
}
.mp-invoices-header__title {
    font-size: 15px;
    font-weight: 700;
    color: #1a2236;
    margin: 0;
}
.mp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.mp-table th {
    background: #f8faff;
    color: #7a8399;
    font-weight: 600;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 10px 12px;
    border-bottom: 2px solid #e8ecf1;
    text-align: left;
}
.mp-table td {
    padding: 13px 12px;
    border-bottom: 1px solid #f0f2f7;
    color: #1a2236;
    vertical-align: middle;
}
.mp-table tr:last-child td { border-bottom: none; }
.mp-table tr:hover td { background: #f8faff; }
.mp-table input[type="checkbox"] {
    width: 16px; height: 16px;
    accent-color: #3b82f6;
    cursor: pointer;
}
.mp-invoice-code { font-weight: 600; color: #2563eb; }
.mp-invoice-desc { font-size: 12px; color: #a0abc0; margin-top: 2px; }
.mp-amount { font-weight: 700; text-align: right; font-size: 14px; }

/* ===== Status Badges ===== */
.mp-badge {
    display: inline-block;
    padding: 3px 11px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.mp-badge--overdue   { background: #fff1f0; color: #e53935; border: 1px solid #ffcdd2; }
.mp-badge--unpaid    { background: #fffde7; color: #f59e0b; border: 1px solid #ffe082; }

/* ===== Payment Form ===== */
.mp-payment-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.mp-payment-title {
    font-size: 15px;
    font-weight: 700;
    color: #1a2236;
    margin: 0;
}
.mp-payment-total-label { font-size: 13px; color: #7a8399; }
.mp-payment-total-value {
    font-size: 17px;
    font-weight: 700;
    color: #2563eb;
}
.mp-payment-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}
.mp-method-label, .mp-field-label {
    font-size: 13px;
    font-weight: 600;
    color: #3d4a60;
    margin-bottom: 10px;
    display: block;
}
.mp-method-group {
    display: flex;
    gap: 12px;
}
.mp-method-btn {
    flex: 1;
    border: 1.5px solid #d1d9e6;
    background: #fff;
    border-radius: 10px;
    padding: 12px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #3d4a60;
    transition: all .2s;
}
.mp-method-btn i { font-size: 20px; color: #a0abc0; }
.mp-method-btn:hover { border-color: #3b82f6; color: #2563eb; }
.mp-method-btn:hover i { color: #3b82f6; }
.mp-method-btn.active {
    border-color: #3b82f6;
    background: #eff6ff;
    color: #2563eb;
}
.mp-method-btn.active i { color: #3b82f6; }
.mp-textarea, .mp-input {
    width: 100%;
    border: 1.5px solid #d1d9e6;
    border-radius: 8px;
    font-size: 13.5px;
    color: #1a2236;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.mp-textarea {
    height: 80px;
    padding: 10px 12px;
    resize: vertical;
}
.mp-input { padding: 0 14px; height: 44px; }
.mp-textarea:focus, .mp-input:focus { border-color: #3b82f6; }
.mp-field-hint { font-size: 11.5px; color: #a0abc0; margin-top: 5px; }
.mp-amount-field { margin-top: 16px; }
.mp-btn-row {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}
.mp-btn {
    padding: 10px 22px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all .2s;
}
.mp-btn--outline {
    background: #fff;
    border: 1.5px solid #d1d9e6;
    color: #3d4a60;
}
.mp-btn--outline:hover { border-color: #3b82f6; color: #2563eb; }
.mp-btn--primary {
    background: #2563eb;
    color: #fff;
}
.mp-btn--primary:hover { background: #1d4ed8; }
</style>
@endpush


@section('content')
<div class="mp-wrap">

    {{-- Page Header --}}
    <div class="mp-header">
        <div>
            <h1 class="mp-header__title">Thu tiền thủ công</h1>
            <p class="mp-header__sub">Tra cứu và ghi nhận thanh toán trực tiếp từ cư dân.</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mp-alert mp-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mp-alert mp-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Top Row: Tra cứu + Thông tin cư dân --}}
    <div class="mp-top-row">
        {{-- Left: Ô tìm kiếm --}}
        <div class="mp-card mp-search-card">
            <div class="mp-search-label">
                <i class="fas fa-search"></i>
                Tra cứu
            </div>
            <p style="font-size:13px;color:#7a8399;margin:0 0 14px;">Mã căn hộ hoặc Tên chủ hộ</p>
            <div class="mp-search-input-wrap">
                <input type="text"
                       id="searchInput"
                       class="mp-search-input"
                       placeholder="Ví dụ: A1-1205 hoặc Nguyễn Văn An"
                       autocomplete="off" />
                <button type="button" class="mp-search-btn" id="searchBtn" title="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <p class="mp-search-hint">Nhập mã căn hộ, tên, số điện thoại hoặc email của chủ hộ.</p>
            <div class="mp-search-error" id="searchError"></div>
        </div>

        {{-- Right: Card thông tin cư dân --}}
        <div class="mp-card mp-resident-card" id="residentCard" style="display:none;">
            <div class="mp-resident-info">
                <div class="mp-resident-avatar" id="residentAvatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="mp-resident-name" id="residentName">---</p>
                    <p class="mp-resident-role">Chủ hộ</p>
                    <span class="mp-resident-apt-badge" id="residentApt"></span>
                    <div class="mp-resident-contacts">
                        <span><i class="fas fa-phone"></i><span id="residentPhone"></span></span>
                        <span><i class="fas fa-envelope"></i><span id="residentEmail"></span></span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Danh sách hóa đơn chưa thanh toán --}}
    <div class="mp-card" id="invoicesSection" style="display:none;">
        <div class="mp-invoices-header">
            <div class="mp-invoices-header__icon">
                <i class="fas fa-file-invoice"></i>
            </div>
            <h2 class="mp-invoices-header__title">Danh sách hóa đơn chưa thanh toán</h2>
        </div>
        <table class="mp-table" id="invoicesTable">
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" id="checkAll" title="Chọn tất cả" />
                    </th>
                    <th>Mã HĐ</th>
                    <th>Kỳ hóa đơn</th>
                    <th>Hạn thanh toán</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right;">Số tiền (VNĐ)</th>
                </tr>
            </thead>
            <tbody id="invoicesTbody">
                {{-- Dữ liệu sẽ được render bởi JavaScript --}}
            </tbody>
        </table>
        <div id="noInvoicesMsg" style="display:none; text-align:center; padding:30px; color:#a0abc0; font-size:14px;">
            <i class="fas fa-check-circle" style="font-size:28px; color:#43a047; display:block; margin-bottom:8px;"></i>
            Căn hộ này không có hóa đơn chưa thanh toán.
        </div>
    </div>

    {{-- Xử lý thanh toán --}}
    <div class="mp-card" id="paymentSection" style="display:none;">
        <form method="POST" action="{{ route('manual-payment.process') }}" id="paymentForm">
            @csrf
            <input type="hidden" name="apartment_id" id="hiddenApartmentId" />

            {{-- Header row --}}
            <div class="mp-payment-title-row">
                <h3 class="mp-payment-title">Xử lý thanh toán</h3>
                <div>
                    <span class="mp-payment-total-label">Tổng tiền cần thanh toán: </span>
                    <span class="mp-payment-total-value" id="totalDisplay">0 VNĐ</span>
                </div>
            </div>

            {{-- Body: left = phương thức + số tiền, right = ghi chú --}}
            <div class="mp-payment-body">
                <div>
                    {{-- Phương thức thanh toán --}}
                    <label class="mp-method-label">Phương thức thanh toán</label>
                    <div class="mp-method-group" id="methodGroup">
                        <button type="button" class="mp-method-btn active" data-method="cash">
                            <i class="fas fa-money-bill-wave"></i>
                            Tiền mặt
                        </button>
                        <button type="button" class="mp-method-btn" data-method="bank_transfer">
                            <i class="fas fa-university"></i>
                            Chuyển khoản
                        </button>
                        <button type="button" class="mp-method-btn" data-method="card">
                            <i class="fas fa-credit-card"></i>
                            Thẻ
                        </button>
                    </div>
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="cash" />

                    {{-- Số tiền thực thu --}}
                    <div class="mp-amount-field">
                        <label class="mp-field-label" for="amountReceived">Số tiền thực thu (VNĐ)</label>
                        <input type="text"
                               id="amountReceived"
                               name="amount_received"
                               class="mp-input"
                               placeholder="0"
                               autocomplete="off" />
                        <p class="mp-field-hint">Mặc định bằng tổng số tiền các hóa đơn đã chọn.</p>
                    </div>
                </div>

                <div>
                    {{-- Ghi chú --}}
                    <label class="mp-field-label" for="paymentNote">Ghi chú (Tùy chọn)</label>
                    <textarea id="paymentNote"
                              name="note"
                              class="mp-textarea"
                              placeholder="Nhập ghi chú hoặc mã giao dịch ngân hàng..."></textarea>

                    {{-- Action buttons --}}
                    <div class="mp-btn-row">
                        <button type="submit" name="action" value="print" class="mp-btn mp-btn--outline" id="btnPrint">
                            <i class="fas fa-print"></i> Xác nhận &amp; In phiếu thu
                        </button>
                        <button type="submit" name="action" value="confirm" class="mp-btn mp-btn--primary" id="btnConfirm">
                            Xác nhận thanh toán
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
const SEARCH_URL = "{{ route('manual-payment.search') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

// --- AJAX Search ---
function doSearch() {
    const q = document.getElementById('searchInput').value.trim();
    const errEl = document.getElementById('searchError');
    errEl.style.display = 'none';
    if (!q) { errEl.textContent = 'Vui lòng nhập từ khóa.'; errEl.style.display = 'block'; return; }

    fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            errEl.textContent = data.message || 'Không tìm thấy kết quả.';
            errEl.style.display = 'block';
            document.getElementById('residentCard').style.display = 'none';
            document.getElementById('invoicesSection').style.display = 'none';
            document.getElementById('paymentSection').style.display = 'none';
            return;
        }
        renderResident(data);
        renderInvoices(data.invoices, data.apartment.id);
    })
    .catch(() => {
        errEl.textContent = 'Lỗi kết nối. Vui lòng thử lại.';
        errEl.style.display = 'block';
    });
}

document.getElementById('searchBtn').addEventListener('click', doSearch);
document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); doSearch(); }
});

// --- Render Resident Card ---
function renderResident(data) {
    const owner = data.owner;
    const apt   = data.apartment;

    if (owner) {
        document.getElementById('residentName').textContent  = owner.name || '---';
        document.getElementById('residentPhone').textContent = owner.phone || '---';
        document.getElementById('residentEmail').textContent = owner.email || '---';
    }
    document.getElementById('residentApt').textContent = 'Căn hộ ' + (apt.apartment_code || '---');
    document.getElementById('hiddenApartmentId').value = apt.id;

    // Avatar
    const avatarEl = document.getElementById('residentAvatar');
    if (owner && owner.avatar) {
        avatarEl.innerHTML = `<img src="${owner.avatar}" alt="avatar" />`;
    } else {
        const initials = owner ? owner.name.split(' ').map(w => w[0]).slice(-2).join('').toUpperCase() : '?';
        avatarEl.innerHTML = `<span style="font-size:22px;font-weight:700;color:#2563eb;">${initials}</span>`;
        avatarEl.style.background = '#eff6ff';
    }

    document.getElementById('residentCard').style.display = 'flex';
}

// --- Render Invoices Table ---
function renderInvoices(invoices, apartmentId) {
    const tbody = document.getElementById('invoicesTbody');
    const noMsg = document.getElementById('noInvoicesMsg');
    const section = document.getElementById('invoicesSection');
    const paySection = document.getElementById('paymentSection');
    tbody.innerHTML = '';

    if (!invoices || invoices.length === 0) {
        section.style.display = 'block';
        noMsg.style.display = 'block';
        paySection.style.display = 'none';
        return;
    }

    noMsg.style.display = 'none';
    invoices.forEach(inv => {
        const badgeClass = inv.status === 'overdue' ? 'mp-badge--overdue' : 'mp-badge--unpaid';
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="checkbox" class="inv-chk" data-id="${inv.id}" data-amount="${inv.total_amount}" checked /></td>
            <td>
                <div class="mp-invoice-code">${inv.invoice_code}</div>
                <div class="mp-invoice-desc">${inv.description}</div>
            </td>
            <td>Tháng ${inv.billing_month}/${inv.billing_year}</td>
            <td>${inv.due_date || '---'}</td>
            <td><span class="mp-badge ${badgeClass}">${inv.status_label}</span></td>
            <td class="mp-amount">${Number(inv.total_amount).toLocaleString('vi-VN')}</td>
        `;
        tbody.appendChild(tr);
    });

    section.style.display = 'block';
    paySection.style.display = 'block';
    recalcTotal();

    // Checkbox logic
    document.querySelectorAll('.inv-chk').forEach(chk => {
        chk.addEventListener('change', recalcTotal);
    });

    // Check all
    document.getElementById('checkAll').addEventListener('change', function() {
        document.querySelectorAll('.inv-chk').forEach(c => { c.checked = this.checked; });
        recalcTotal();
    });
}

// --- Recalculate Total ---
function recalcTotal() {
    let total = 0;
    const selectedIds = [];
    document.querySelectorAll('.inv-chk:checked').forEach(c => {
        total += parseFloat(c.dataset.amount || 0);
        selectedIds.push(c.dataset.id);
    });

    document.getElementById('totalDisplay').textContent = Number(total).toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('amountReceived').value = Number(total).toLocaleString('vi-VN');

    // Cập nhật hidden inputs hóa đơn được chọn
    document.querySelectorAll('input[name="invoice_ids[]"]').forEach(e => e.remove());
    const form = document.getElementById('paymentForm');
    selectedIds.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'invoice_ids[]';
        inp.value = id;
        form.appendChild(inp);
    });
}

// --- Payment Method Selection ---
document.querySelectorAll('.mp-method-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.mp-method-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('paymentMethodInput').value = this.dataset.method;
    });
});

// --- Format amount input ---
document.getElementById('amountReceived').addEventListener('input', function () {
    const raw = this.value.replace(/[^\d]/g, '');
    if (raw) this.value = Number(raw).toLocaleString('vi-VN');
});

// --- Form Validation before Submit ---
document.getElementById('paymentForm').addEventListener('submit', function (e) {
    const checked = document.querySelectorAll('.inv-chk:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert('Vui lòng chọn ít nhất một hóa đơn để thanh toán.');
        return;
    }
    const rawAmount = document.getElementById('amountReceived').value.replace(/[^\d]/g, '');
    if (!rawAmount || parseInt(rawAmount) <= 0) {
        e.preventDefault();
        alert('Vui lòng nhập số tiền thực thu hợp lệ.');
        return;
    }
    // Chuẩn hóa amount_received về số nguyên trước khi gửi
    document.getElementById('amountReceived').value = rawAmount;
});
</script>
@endpush


