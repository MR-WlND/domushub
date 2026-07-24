@extends('layouts.admin.master')

@section('page_title', 'Quản lý Hóa đơn')

@section('content')
    <div class="inv-admin">

        {{-- Header --}}
        <div class="inv-admin__header">
            <div>
                <p class="inv-admin__eyebrow">Tài chính</p>
                <h1 class="inv-admin__title">Quản lý Hóa đơn</h1>
            </div>
            <div style="display:flex;gap:10px">
                <a href="{{ portal_route('invoices.batch') }}" class="inv-admin__btn inv-admin__btn--primary">Xuất hàng
                    loạt</a>
                <a href="{{ portal_route('invoices.create') }}" class="inv-admin__btn"
                    style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">Tạo đơn lẻ</a>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="inv-admin__alert inv-admin__alert--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="inv-admin__alert inv-admin__alert--error">{{ session('error') }}</div>
        @endif

        {{-- Summary Banner --}}
        @php
            $totalIssued = \App\Models\Invoice::sum('total_amount');
            $totalPaid = \App\Models\Invoice::sum('paid_amount');
            $totalDebt = max(0, $totalIssued - $totalPaid);
            $totalOverdue = \App\Models\Invoice::where('status', 'overdue')->count();
        @endphp
        <div class="inv-summary-row">
            <div class="inv-summary-card">
                <span class="inv-summary-label">Tổng phát hành</span>
                <span class="inv-summary-val">{{ number_format($totalIssued) }}đ</span>
            </div>
            <div class="inv-summary-card inv-summary-card--success">
                <span class="inv-summary-label">Đã thu</span>
                <span class="inv-summary-val">{{ number_format($totalPaid) }}đ</span>
            </div>
            <div class="inv-summary-card {{ $totalDebt > 0 ? 'inv-summary-card--danger' : 'inv-summary-card--ok' }}">
                <span class="inv-summary-label">Tổng dư nợ</span>
                <span class="inv-summary-val">{{ number_format($totalDebt) }}đ</span>
            </div>
            <div class="inv-summary-card inv-summary-card--warning">
                <span class="inv-summary-label">HĐ quá hạn</span>
                <span class="inv-summary-val">{{ $totalOverdue }}</span>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="inv-admin__filter-bar">
            <form method="GET" action="{{ portal_route('invoices.index') }}" class="inv-admin__filter-form">
                
                <div class="inv-admin__filter-group">
                    <svg class="inv-admin__filter-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm căn hộ, tòa..." class="inv-admin__input inv-admin__input--with-icon">
                </div>

                <div class="inv-admin__filter-group">
                    <svg class="inv-admin__filter-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <input type="month" name="month" value="{{ request('month') }}" class="inv-admin__input inv-admin__input--with-icon" style="flex:0;min-width:160px;" placeholder="Tháng/Năm">
                </div>

                <div class="inv-admin__filter-group">
                    <svg class="inv-admin__filter-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <select name="apartment_id" class="inv-admin__select inv-admin__select--with-icon">
                        <option value="">Tất cả căn hộ</option>
                        @foreach ($apartments as $apt)
                            <option value="{{ $apt->id }}" {{ request('apartment_id') == $apt->id ? 'selected' : '' }}>
                                Căn {{ $apt->apartment_number }}
                                @if (optional(optional($apt->floor)->block)->name)
                                    ({{ $apt->floor->block->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="inv-admin__filter-group">
                    <svg class="inv-admin__filter-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <select name="status" class="inv-admin__select inv-admin__select--with-icon">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                {{ \App\Models\Invoice::statusLabel($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; gap:8px;">
                    <button type="submit" class="inv-admin__btn inv-admin__btn--primary">Lọc</button>
                    @if (request()->hasAny(['search', 'status', 'month', 'apartment_id']))
                        <a href="{{ portal_route('invoices.index') }}" class="inv-admin__btn inv-admin__btn--clear">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Xóa
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Invoice Table --}}
        <div class="inv-admin__card">
            <div class="inv-admin__card-header">
                <span>{{ $apartmentsPaginated->total() }} căn hộ</span>
                <span class="inv-admin__muted">Trang {{ $apartmentsPaginated->currentPage() }}/{{ $apartmentsPaginated->lastPage() }}</span>
            </div>

            <table class="inv-admin__table">
                <thead>
                    <tr>
                        <th>Căn hộ</th>
                        <th>Tòa/Tầng</th>
                        <th>Tình trạng</th>
                        <th>Số lượng HĐ</th>
                        <th>Tổng tiền HĐ</th>
                        <th>Đã thu</th>
                        <th>Tổng dư nợ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apartmentsPaginated as $apt)
                        @php
                            $blockName = optional(optional($apt->floor)->block)->name ?? '—';
                            $floorName = optional($apt->floor)->name ?? '—';
                            $totalAmount = $apt->invoices_sum_total_amount ?? 0;
                            $paidAmount = $apt->invoices_sum_paid_amount ?? 0;
                            $debt = max(0, $totalAmount - $paidAmount);
                            $highlightIds = session('highlightAptIds', []);
                            $errorIds = session('errorAptIds', []);
                            $isFresh = in_array($apt->id, $highlightIds);
                            $isError = in_array($apt->id, $errorIds);
                        @endphp
                        <tr onclick="window.location='{{ portal_route('invoices.apartment', $apt) }}'" style="cursor:pointer;" class="{{ $isFresh ? 'inv-admin__row--fresh' : '' }} {{ $isError ? 'inv-admin__row--error' : '' }}">
                            <td>
                                <div style="font-weight:600; color:#0f172a; font-size:0.88rem;">
                                    Căn {{ $apt->apartment_number ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="inv-admin__muted">{{ $blockName }} - Tầng {{ $floorName }}</div>
                            </td>
                            <td>
                                @if($apt->status === 'occupied')
                                    <span class="inv-admin__badge inv-admin__badge--paid" style="font-weight:600;font-size:0.75rem;">{{ $apt->status_label }}</span>
                                @elseif($apt->status === 'vacant')
                                    <span class="inv-admin__badge inv-admin__badge--unpaid" style="font-weight:600;font-size:0.75rem;">{{ $apt->status_label }}</span>
                                @else
                                    <span class="inv-admin__badge inv-admin__badge--cancelled" style="font-weight:600;font-size:0.75rem;">{{ $apt->status_label }}</span>
                                @endif
                            </td>
                            <td style="font-weight:600;">
                                {{ $apt->invoices_count ?? 0 }}
                            </td>
                            <td style="font-weight:600;">{{ number_format($totalAmount) }}đ</td>
                            <td style="color:#16a34a; font-weight:600;">{{ number_format($paidAmount) }}đ</td>
                            <td style="{{ $debt > 0 ? 'color:#b91c1c;font-weight:700;' : 'color:#16a34a;font-weight:600;' }}">
                                {{ number_format($debt) }}đ
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="inv-admin__empty">Không tìm thấy căn hộ nào phù hợp</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($apartmentsPaginated->hasPages())
                <div class="inv-admin__pagination">
                    @if (!$apartmentsPaginated->onFirstPage())
                        <a href="{{ $apartmentsPaginated->previousPageUrl() }}" class="inv-admin__page-btn">‹ Trước</a>
                    @endif
                    @foreach ($apartmentsPaginated->getUrlRange(max(1, $apartmentsPaginated->currentPage() - 2), min($apartmentsPaginated->lastPage(), $apartmentsPaginated->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                            class="inv-admin__page-btn {{ $page == $apartmentsPaginated->currentPage() ? 'inv-admin__page-btn--active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if ($apartmentsPaginated->hasMorePages())
                        <a href="{{ $apartmentsPaginated->nextPageUrl() }}" class="inv-admin__page-btn">Sau ›</a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        function confirmCancel(id) {
            if (confirm('Bạn có chắc muốn hủy hóa đơn này? Hành động này không thể hoàn tác.')) {
                document.getElementById('cancel-form-' + id).submit();
            }
        }
    </script>

    <style>
        .inv-admin {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        /* Header */
        .inv-admin__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .inv-admin__eyebrow {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin: 0 0 4px;
            font-weight: 600;
        }

        .inv-admin__title {
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        /* Alert */
        .inv-admin__alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .inv-admin__alert--success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .inv-admin__alert--error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        /* Summary Cards */
        .inv-summary-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .inv-summary-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
        }

        .inv-summary-card--success {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .inv-summary-card--danger {
            border-color: #fca5a5;
            background: #fef2f2;
        }

        .inv-summary-card--ok {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .inv-summary-card--warning {
            border-color: #fde68a;
            background: #fffbeb;
        }

        .inv-summary-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
        }

        .inv-summary-val {
            display: block;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }

        .inv-summary-card--success .inv-summary-val {
            color: #166534;
        }

        .inv-summary-card--danger .inv-summary-val {
            color: #991b1b;
        }

        .inv-summary-card--warning .inv-summary-val {
            color: #92400e;
        }

        /* Filter */
        .inv-admin__filter-bar {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .inv-admin__filter-form {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .inv-admin__filter-group {
            position: relative;
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 200px;
        }

        .inv-admin__filter-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .inv-admin__filter-group:focus-within .inv-admin__filter-icon {
            color: #3b82f6;
        }

        .inv-admin__input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .inv-admin__input:focus, .inv-admin__select:focus {
            outline: none;
            border-color: #3b82f6;
            background: #fff;
        }

        .inv-admin__select {
            width: 100%;
            padding: 10px 14px;
            padding-right: 36px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #0f172a;
            background: #f8fafc;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            transition: all 0.2s ease;
        }

        .inv-admin__input--with-icon, .inv-admin__select--with-icon {
            padding-left: 42px;
        }
        
        .inv-admin__select option {
            padding: 8px;
            font-size: 0.9rem;
        }

        /* Buttons */
        .inv-admin__btn {
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .inv-admin__btn--primary {
            background: #2563eb;
            color: #fff;
        }

        .inv-admin__btn--primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .inv-admin__btn--clear {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .inv-admin__btn--clear:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .inv-admin__btn--ghost {
            background: none;
            color: #ef4444;
            border: 1px solid #fecaca;
        }

        .inv-admin__btn--sm {
            padding: 5px 10px;
            font-size: 0.78rem;
        }

        .inv-admin__btn--detail {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .inv-admin__btn--detail:hover {
            background: #dbeafe;
        }

        /* Card / Table */
        .inv-admin__card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .inv-admin__card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
        }

        .inv-admin__muted {
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .inv-admin__table {
            width: 100%;
            border-collapse: collapse;
        }

        .inv-admin__table th {
            text-align: left;
            padding: 10px 14px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .inv-admin__table td {
            padding: 11px 14px;
            font-size: 0.85rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .inv-admin__table tr:hover td {
            background: #f8fafc;
        }

        .inv-admin__row--fresh td {
            background: #dcfce7 !important;
            animation: invFreshPulse 60s linear 1 forwards;
        }

        @keyframes invFreshPulse {
            0% {
                background: #dcfce7;
            }

            100% {
                background: transparent;
            }
        }

        .inv-admin__row--error td {
            background: #fee2e2 !important;
            animation: invErrorPulse 60s linear 1 forwards;
        }

        @keyframes invErrorPulse {
            0% {
                background: #fee2e2;
            }

            100% {
                background: transparent;
            }
        }

        .inv-apt-code {
            font-family: monospace;
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
        }

        /* Badges */
        .inv-admin__badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 4px;
            white-space: nowrap;
        }

        .inv-admin__badge--paid {
            background: #dcfce7;
            color: #15803d;
        }

        .inv-admin__badge--unpaid {
            background: #fef3c7;
            color: #b45309;
        }

        .inv-admin__badge--partial {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .inv-admin__badge--overdue {
            background: #fee2e2;
            color: #b91c1c;
        }

        .inv-admin__badge--cancelled {
            background: #e2e3e5;
            color: #383d41;
        }

        /* Empty */
        .inv-admin__empty {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Pagination */
        .inv-admin__pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            padding: 14px;
            border-top: 1px solid #e2e8f0;
        }

        .inv-admin__page-btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            color: #475569;
            background: #fff;
            text-decoration: none;
        }

        .inv-admin__page-btn:hover {
            background: #f1f5f9;
        }

        .inv-admin__page-btn--active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        @media (max-width: 768px) {
            .inv-summary-row {
                grid-template-columns: 1fr 1fr;
            }

            .inv-admin__filter-form {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

@endsection
