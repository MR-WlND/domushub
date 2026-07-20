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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm căn hộ, tòa..."
                    class="inv-admin__input">

                <input type="month" name="month" value="{{ request('month') }}" class="inv-admin__input"
                    style="flex:0;min-width:160px;" placeholder="Tháng/Năm">

                <select name="apartment_id" class="inv-admin__select">
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

                <select name="status" class="inv-admin__select">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ \App\Models\Invoice::statusLabel($s) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="inv-admin__btn inv-admin__btn--primary">Lọc</button>
                @if (request()->hasAny(['search', 'status', 'month', 'apartment_id']))
                    <a href="{{ portal_route('invoices.index') }}" class="inv-admin__btn inv-admin__btn--ghost">Xóa lọc</a>
                @endif
            </form>
        </div>

        {{-- Invoice Table --}}
        <div class="inv-admin__card">
            <div class="inv-admin__card-header">
                <span>{{ $invoices->total() }} hóa đơn</span>
                <span class="inv-admin__muted">Trang {{ $invoices->currentPage() }}/{{ $invoices->lastPage() }}</span>
            </div>

            <table class="inv-admin__table">
                <thead>
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Căn hộ</th>
                        <th>Kỳ</th>
                        <th>Hạn TT</th>
                        <th>Tổng tiền</th>
                        <th>Đã thu</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        @php
                            $apt = $invoice->apartment;
                            $blockName = optional(optional($apt->floor)->block)->name ?? '—';
                            $isRecentlyCreated = $invoice->created_at && $invoice->created_at->diffInMinutes(now()) < 1;
                        @endphp
                        <tr class="{{ $isRecentlyCreated ? 'inv-admin__row--fresh' : '' }}">
                            <td>
                                <span class="inv-apt-code">{{ $invoice->invoice_code }}</span>
                            </td>
                            <td>
                                <div style="font-weight:600; color:#0f172a; font-size:0.88rem;">
                                    Căn {{ $apt->apartment_number ?? '—' }}
                                </div>
                                <div class="inv-admin__muted">{{ $blockName }}</div>
                            </td>
                            <td>
                                Tháng {{ $invoice->billing_month->format('m/Y') }}
                            </td>
                            <td>
                                @php $isOverdue = $invoice->due_date && $invoice->due_date->isPast() && !in_array($invoice->status, ['paid','cancelled']); @endphp
                                <span style="{{ $isOverdue ? 'color:#b91c1c;font-weight:700;' : '' }}">
                                    {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '—' }}
                                </span>
                            </td>
                            <td style="font-weight:600;">{{ number_format($invoice->total_amount) }}đ</td>
                            <td style="color:#16a34a; font-weight:600;">{{ number_format($invoice->paid_amount) }}đ</td>
                            <td>
                                @php
                                    $badgeMap = [
                                        'unpaid' => 'inv-admin__badge--unpaid',
                                        'partial_paid' => 'inv-admin__badge--partial',
                                        'paid' => 'inv-admin__badge--paid',
                                        'overdue' => 'inv-admin__badge--overdue',
                                        'cancelled' => 'inv-admin__badge--cancelled',
                                    ];
                                    $badgeClass = $badgeMap[$invoice->status] ?? 'inv-admin__badge--unpaid';
                                @endphp
                                <span class="inv-admin__badge {{ $badgeClass }}">
                                    {{ \App\Models\Invoice::statusLabel($invoice->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                                    <a href="{{ portal_route('invoices.show', $invoice) }}"
                                        class="inv-admin__btn inv-admin__btn--sm inv-admin__btn--detail">Chi tiết</a>
                                    <a href="{{ portal_route('invoices.print', $invoice) }}" target="_blank"
                                        class="inv-admin__btn inv-admin__btn--sm"
                                        style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">🖨 In</a>
                                    @if (!in_array($invoice->status, ['paid', 'cancelled']))
                                        <form action="{{ portal_route('invoices.resend-notification', $invoice) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="inv-admin__btn inv-admin__btn--sm"
                                                style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;"
                                                title="Gửi lại thông báo">🔔</button>
                                        </form>
                                        <button class="inv-admin__btn inv-admin__btn--sm"
                                            style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;"
                                            onclick="confirmCancel({{ $invoice->id }})" title="Hủy hóa đơn">✕ Hủy</button>
                                    @endif
                                </div>

                                {{-- Hidden cancel form --}}
                                <form id="cancel-form-{{ $invoice->id }}"
                                    action="{{ portal_route('invoices.cancel', $invoice) }}" method="POST"
                                    style="display:none;">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="inv-admin__empty">Không tìm thấy hóa đơn nào phù hợp</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($invoices->hasPages())
                <div class="inv-admin__pagination">
                    @if (!$invoices->onFirstPage())
                        <a href="{{ $invoices->previousPageUrl() }}" class="inv-admin__page-btn">‹ Trước</a>
                    @endif
                    @foreach ($invoices->getUrlRange(max(1, $invoices->currentPage() - 2), min($invoices->lastPage(), $invoices->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                            class="inv-admin__page-btn {{ $page == $invoices->currentPage() ? 'inv-admin__page-btn--active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if ($invoices->hasMorePages())
                        <a href="{{ $invoices->nextPageUrl() }}" class="inv-admin__page-btn">Sau ›</a>
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
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }

        .inv-admin__filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .inv-admin__input {
            flex: 1;
            min-width: 180px;
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #1e293b;
            background: #f8fafc;
        }

        .inv-admin__select {
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #1e293b;
            background: #f8fafc;
            cursor: pointer;
        }

        /* Buttons */
        .inv-admin__btn {
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .inv-admin__btn--primary {
            background: #2563eb;
            color: #fff;
        }

        .inv-admin__btn--primary:hover {
            background: #1d4ed8;
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
