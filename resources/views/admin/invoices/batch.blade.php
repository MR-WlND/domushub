@extends('layouts.admin.master')

@section('page_title', 'Xuất hóa đơn hàng loạt')

@section('content')
    <div class="batch-page">

        {{-- Header --}}
        <div class="batch-header">
            <div>
                <p class="batch-eyebrow">Tài chính</p>
                <h1 class="batch-title">Xuất hóa đơn hàng loạt</h1>
                <p class="batch-sub">Tự động tạo hóa đơn cho tất cả căn hộ đang hoạt động theo biểu giá hiện tại</p>
            </div>
            <a href="{{ portal_route('invoices.index') }}" class="batch-btn batch-btn--ghost">Danh sách hóa đơn</a>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="batch-alert batch-alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="batch-alert batch-alert--error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="batch-alert batch-alert--error">
                @foreach ($errors->all() as $e)
                    <div>• {{ $e }}</div>
                @endforeach
            </div>
        @endif

        @php
            $selectedMonth = old('billing_month', request('billing_month', now()->format('Y-m')));
        @endphp

        <form method="POST" action="{{ portal_route('invoices.batch.store') }}" id="batchForm">
            @csrf
            <div class="batch-layout">

                {{-- Cấu hình hóa đơn --}}
                <div class="batch-config">
                    <div class="batch-section">
                        <h3 class="batch-section-title">Thông số chung</h3>
                        <div class="batch-fields">
                            <div class="batch-field">
                                <label>Tháng phát hành <span class="batch-req">*</span></label>
                                <input type="month" name="billing_month" id="billing_month" value="{{ $selectedMonth }}"
                                    onchange="window.location='{{ portal_route('invoices.batch') }}?billing_month=' + encodeURIComponent(this.value)"
                                    required>
                            </div>
                            <div class="batch-field">
                                <label>Hạn thanh toán <span class="batch-req">*</span></label>
                                <input type="date" name="due_date"
                                    value="{{ old('due_date', now()->addDays(20)->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Chọn loại phí --}}
                    <div class="batch-section">
                        <h3 class="batch-section-title">Chọn loại phí phát hành</h3>
                        <p class="batch-hint">Chọn các phí cần tạo hóa đơn. Hệ thống sẽ áp dụng đơn giá đang <strong>Hoạt
                                động</strong>.</p>

                        @php
                            $baseTypes = [
                                'water' => 'Tiền nước',
                                'internet' => 'Internet',
                                'service' => 'Dịch vụ'
                            ];
                            $activeTypesLookup = $activePrices->pluck('type')->toArray();
                        @endphp

                        <div class="batch-price-list">
                            {{-- Hiển thị tất cả các loại giá đang hoạt động (bao gồm nhiều loại xe của parking_fee, bỏ qua xe đạp) --}}
                            @foreach ($activePrices as $price)
                                @if($price->vehicle_type === 'bicycle') @continue @endif
                                @php
                                    $checkboxValue = $price->type === 'parking_fee' && $price->vehicle_type
                                        ? 'parking_fee_' . $price->vehicle_type
                                        : $price->type;
                                @endphp
                                <label class="batch-price-item">
                                    <input type="checkbox" name="types[]" value="{{ $checkboxValue }}"
                                        data-price="{{ $price->unit_price }}"
                                        {{ old('types') && in_array($checkboxValue, old('types')) ? 'checked' : '' }}
                                        class="batch-check"
                                        onchange="updateEstimatedCost()">
                                    <div class="batch-price-info">
                                        <div>
                                            <div class="batch-price-name">{{ $price->name }}</div>
                                            <div class="batch-price-val">{{ number_format($price->unit_price) }}đ / đơn vị</div>
                                        </div>
                                    </div>
                                    <span class="batch-price-active">Hoạt động</span>
                                </label>
                            @endforeach

                            {{-- Hiển thị các loại phí cơ bản chưa có cấu hình giá --}}
                            @foreach ($baseTypes as $type => $label)
                                @if (!in_array($type, $activeTypesLookup))
                                <label class="batch-price-item batch-price-item--disabled">
                                    <input type="checkbox" disabled class="batch-check">
                                    <div class="batch-price-info">
                                        <div>
                                            <div class="batch-price-name">{{ $label }}</div>
                                            <div class="batch-price-na">Chưa có đơn giá</div>
                                        </div>
                                    </div>
                                </label>
                                @endif
                            @endforeach
                        </div>
                        @error('types')
                            <span class="batch-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tùy chọn nâng cao --}}
                    <div class="batch-section">
                        <h3 class="batch-section-title">Tùy chọn áp dụng</h3>
                        <div class="batch-options">
                            <label class="batch-option">
                                <input type="checkbox" name="skip_existing" value="1" checked
                                    onchange="updateEstimatedCost()">
                                <span>Bỏ qua nếu đã có hóa đơn cùng tháng + loại phí</span>
                            </label>
                            <label class="batch-option">
                                <input type="checkbox" name="only_occupied" id="only_occupied" value="1" checked
                                    onchange="updateApartmentCount()">
                                <span>Chỉ căn hộ đang có người ở (occupied)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Preview danh sách căn hộ và Ước tính --}}
                <div class="batch-preview">
                    <div class="batch-section">
                        <h3 class="batch-section-title">Ước tính kết quả phát hành</h3>
                        <div class="batch-est-grid">
                            <div class="batch-est-card">
                                <span class="batch-est-label">Căn hộ áp dụng</span>
                                <span class="batch-est-val" id="est_apts_count">0</span>
                            </div>
                            <div class="batch-est-card">
                                <span class="batch-est-label">Kỳ phát hành</span>
                                <span class="batch-est-val" id="est_period">Tháng --/----</span>
                            </div>
                            <div class="batch-est-card batch-est-card--primary">
                                <span class="batch-est-label">Dự kiến doanh thu / căn hộ</span>
                                <span class="batch-est-val" id="est_revenue_per_apt">0đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="batch-section">
                        <h3 class="batch-section-title">Danh sách căn hộ mục tiêu</h3>
                        <div class="batch-apt-scroll">
                            <table class="batch-apt-table" id="aptTable">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAllApartments" class="batch-check" checked>
                                        </th>
                                        <th>Căn hộ</th>
                                        <th>Tòa</th>
                                        <th>Tầng</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apartments as $apt)
                                        @php
                                            $hasInvoice = $apt->invoices->isNotEmpty();
                                        @endphp
                                        <tr data-status="{{ $apt->status }}" class="{{ $hasInvoice ? 'batch-row-highlight' : '' }}">
                                            <td>
                                                <input type="checkbox" name="apartment_ids[]" value="{{ $apt->id }}"
                                                    class="batch-target-check batch-check" {{ $hasInvoice ? '' : 'checked' }}>
                                            </td>
                                            <td class="batch-apt-num">{{ $apt->apartment_number }}</td>
                                            <td>{{ optional(optional($apt->floor)->block)->name ?? '—' }}</td>
                                            <td>Tầng {{ optional($apt->floor)->floor_number ?? '—' }}</td>
                                            <td>
                                                @if ($hasInvoice)
                                                    <span class="batch-badge batch-badge--success">Đã xuất</span>
                                                @elseif ($apt->status === 'occupied')
                                                    <span class="batch-badge batch-badge--occupied">Đang ở</span>
                                                @elseif($apt->status === 'vacant')
                                                    <span class="batch-badge batch-badge--vacant">Trống</span>
                                                @else
                                                    <span class="batch-badge batch-badge--other">{{ $apt->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="batch-empty">Không có căn hộ nào đang ở và chưa có hóa
                                                đơn cho tháng đã chọn</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="batch-apt-summary" id="aptSummaryText">
                            Tổng: <strong>{{ $totalCount }}</strong> căn hộ —
                            Đang ở: <strong>{{ $occupiedCount }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="batch-submit-bar">
                <div class="batch-submit-info">
                    ⚠️ Vui lòng xem kỹ thông số và ước tính hóa đơn trước khi tiến hành xuất hàng loạt.
                </div>
                <button type="submit" class="batch-btn batch-btn--primary"
                    onclick="return confirm('Xác nhận xuất hóa đơn hàng loạt?')">
                    Xuất hóa đơn hàng loạt
                </button>
            </div>
        </form>
    </div>

    <style>
        .batch-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        .batch-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .batch-eyebrow {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin: 0 0 4px;
            font-weight: 600;
        }

        .batch-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px;
        }

        .batch-sub {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }

        .batch-alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .batch-alert--success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .batch-alert--error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .batch-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 860px) {
            .batch-layout {
                grid-template-columns: 1fr;
            }
        }

        .batch-section {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
        }

        .batch-section:last-child {
            margin-bottom: 0;
        }

        .batch-section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 14px;
        }

        .batch-hint {
            font-size: 0.8rem;
            color: #64748b;
            margin: -8px 0 14px;
        }

        .batch-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .batch-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .batch-field label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
        }

        .batch-field input,
        .batch-field select {
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.875rem;
            background: #f8fafc;
            outline: none;
        }

        .batch-field input:focus {
            border-color: #3b82f6;
            background: #fff;
        }

        .batch-req {
            color: #ef4444;
        }

        .batch-price-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .batch-price-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: border-color .15s, background .15s;
        }

        .batch-price-item:has(.batch-check:checked) {
            border-color: #2563eb;
            background: #f0f9ff;
        }

        .batch-price-item:hover:not(.batch-price-item--disabled) {
            border-color: #93c5fd;
        }

        .batch-price-item--disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .batch-check {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2563eb;
        }

        .batch-price-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .batch-price-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1e293b;
        }

        .batch-price-val {
            font-size: 0.78rem;
            color: #2563eb;
            font-family: monospace;
            font-weight: 600;
        }

        .batch-price-na {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .batch-price-active {
            font-size: 0.7rem;
            font-weight: 600;
            background: #dcfce7;
            color: #15803d;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .batch-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .batch-option {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.875rem;
            color: #334155;
            cursor: pointer;
        }

        .batch-option input {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
        }

        /* Est block */
        .batch-est-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .batch-est-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            background: #f8fafc;
        }

        .batch-est-card--primary {
            border-color: #bae6fd;
            background: #f0f9ff;
        }

        .batch-est-card--primary .batch-est-val {
            color: #0369a1;
        }

        .batch-est-label {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .batch-est-val {
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .batch-apt-scroll {
            max-height: 250px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .batch-apt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .batch-apt-table th {
            position: sticky;
            top: 0;
            background: #f8fafc;
            padding: 8px 12px;
            font-size: 0.72rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .batch-apt-table td {
            padding: 10px 12px;
            font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .batch-apt-table tr:last-child td {
            border-bottom: none;
        }

        .batch-apt-num {
            font-weight: 600;
            color: #1e40af;
        }

        .batch-apt-summary {
            font-size: 0.8rem;
            color: #64748b;
            padding: 10px 4px 0;
        }

        .batch-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .batch-badge--occupied {
            background: #dcfce7;
            color: #15803d;
        }

        .batch-badge--success {
            background: #bbf7d0;
            color: #166534;
        }

        .batch-row-highlight td {
            background-color: #f0fdf4 !important;
        }

        .batch-badge--vacant {
            background: #f1f5f9;
            color: #64748b;
        }

        .batch-badge--other {
            background: #fef3c7;
            color: #b45309;
        }

        .batch-empty {
            text-align: center;
            padding: 24px;
            color: #94a3b8;
        }

        .batch-submit-bar {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .batch-submit-info {
            font-size: 0.85rem;
            color: #b45309;
            background: #fef3c7;
            padding: 8px 14px;
            border-radius: 6px;
        }

        .batch-btn {
            padding: 9px 18px;
            border-radius: 7px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .batch-btn--primary {
            background: #2563eb;
            color: #fff;
        }

        .batch-btn--primary:hover {
            background: #1d4ed8;
        }

        .batch-btn--ghost {
            background: none;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .batch-btn--ghost:hover {
            background: #f8fafc;
        }

        .batch-error {
            font-size: 0.75rem;
            color: #dc2626;
            display: block;
            margin-top: 6px;
        }
    </style>

    <script>
        function updateApartmentCount() {
            const checkedBoxes = document.querySelectorAll('.batch-target-check:checked');
            const activeApts = checkedBoxes.length;

            document.getElementById('est_apts_count').innerText = activeApts;
            updateEstimatedCost();
        }

        function updateEstimatedCost() {
            const checkboxes = document.querySelectorAll('.batch-check:checked');
            let totalPerApt = 0;
            checkboxes.forEach(cb => {
                totalPerApt += parseFloat(cb.getAttribute('data-price')) || 0;
            });

            document.getElementById('est_revenue_per_apt').innerText =
                totalPerApt.toLocaleString('vi-VN') + 'đ';

            const dateInput = document.getElementById('billing_month').value;
            if (dateInput) {
                const parts = dateInput.split('-');
                document.getElementById('est_period').innerText = `Tháng ${parts[1]}/${parts[0]}`;
            }
        }

        document.getElementById('billing_month').addEventListener('input', updateEstimatedCost);
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('batch-target-check')) {
                updateApartmentCount();
            }
        });
        document.getElementById('selectAllApartments').addEventListener('change', function() {
            document.querySelectorAll('.batch-target-check').forEach(function(checkbox) {
                checkbox.checked = this.checked;
            }, this);
            updateApartmentCount();
        });

        // Chạy lần đầu
        document.addEventListener('DOMContentLoaded', () => {
            updateApartmentCount();
        });
    </script>
@endsection
