<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn {{ $invoice->invoice_code }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 30px;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .bql-info h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .bql-info p { margin: 0; font-size: 13px; }
        .invoice-meta { text-align: right; font-size: 13px; }
        .invoice-meta p { margin: 0 0 4px 0; }
        .invoice-title { text-align: center; margin: 20px 0; }
        .invoice-title h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .invoice-title p { margin: 5px 0 0 0; font-style: italic; }
        .info-table { width: 100%; margin: 20px 0; border-collapse: collapse; }
        .info-table td { padding: 5px 10px; }
        .info-table td:first-child { font-weight: bold; width: 200px; white-space: nowrap; }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
        }
        .details-table th {
            background: #00236f;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
        }
        .details-table th:last-child, .details-table td:last-child { text-align: right; }
        .details-table th:nth-child(2), .details-table td:nth-child(2) { text-align: center; }
        .details-table th:nth-child(3), .details-table td:nth-child(3) { text-align: center; }
        .details-table td { padding: 7px 10px; border-bottom: 1px solid #ddd; }
        .details-table tr:last-child td { border-bottom: none; }
        .details-table tr.pending-row td { color: #888; font-style: italic; }
        .total-row { background: #f0f4ff; font-weight: bold; }
        .total-row td { padding: 10px; font-size: 15px; }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-unpaid    { background: #fff3cd; color: #856404; }
        .status-partial_paid { background: #cce5ff; color: #004085; }
        .status-paid      { background: #d4edda; color: #155724; }
        .status-overdue   { background: #f8d7da; color: #721c24; }
        .status-cancelled { background: #e2e3e5; color: #383d41; }
        .payments-section { margin-top: 30px; }
        .payments-section h3 { font-size: 15px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .payments-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 10px; }
        .payments-table th { background: #f8f9fa; padding: 7px 10px; text-align: left; border-bottom: 2px solid #dee2e6; }
        .payments-table td { padding: 7px 10px; border-bottom: 1px solid #eee; }
        .payments-table td:last-child, .payments-table th:last-child { text-align: right; }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            text-align: center;
        }
        .sig-block { width: 45%; }
        .sig-title { font-weight: bold; margin-bottom: 60px; }
        .sig-name { font-weight: bold; text-decoration: underline; }
        .no-print-actions {
            max-width: 800px;
            margin: 20px auto 0 auto;
            text-align: right;
        }
        .btn-action {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            border: 1px solid #ccc;
            background: #fff;
            text-decoration: none;
            color: #000;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-action-primary {
            background: #00236f;
            color: #fff;
            border-color: #00236f;
        }
        @media print {
            body { padding: 0; }
            .invoice-container { border: none; padding: 0; }
            .no-print-actions { display: none; }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    {{-- Header --}}
    <div class="header-row">
        <div class="bql-info">
            <h3>Ban Quản Lý Chung Cư DomusHub</h3>
            <p>Địa chỉ: Đường số 10, Khu đô thị mới, TP. Hồ Chí Minh</p>
            <p>Hotline: 1900 1234 &mdash; Email: support@domushub.vn</p>
        </div>
        <div class="invoice-meta">
            <p><strong>Mã HĐ:</strong> {{ $invoice->invoice_code }}</p>
            <p><strong>Kỳ:</strong> Tháng {{ $invoice->billing_month->format('m/Y') }}</p>
            <p><strong>Ngày phát hành:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
            <p><strong>Hạn thanh toán:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Title --}}
    <div class="invoice-title">
        <h1>HÓA ĐƠN DỊCH VỤ CHUNG CƯ</h1>
        <p>Kỳ tháng {{ $invoice->billing_month->format('m/Y') }}</p>
    </div>

    {{-- Resident Info --}}
    <table class="info-table">
        <tr>
            <td>Căn hộ:</td>
            <td>
                Căn {{ $invoice->apartment->apartment_number ?? '—' }}
                (Tầng {{ $invoice->apartment->floor->floor_number ?? '—' }},
                Block {{ $invoice->apartment->floor->block->name ?? '—' }})
            </td>
        </tr>
        <tr>
            <td>Chủ hộ:</td>
            <td>{{ $invoice->apartment->owner_name ?? 'Cư dân căn hộ' }}</td>
        </tr>
        <tr>
            <td>Trạng thái:</td>
            <td>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ \App\Models\Invoice::statusLabel($invoice->status) }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Details Table --}}
    <table class="details-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Dịch vụ</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->details as $i => $detail)
                <tr class="{{ $detail->amount == 0 ? 'pending-row' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $detail->servicePrice->name ?? '—' }}
                        @if($detail->note)
                            <br><small style="color:#888;">({{ $detail->note }})</small>
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $detail->quantity }}</td>
                    <td style="text-align:right;">
                        {{ $detail->quantity > 0 ? number_format($detail->servicePrice->unit_price ?? 0) . ' đ' : '—' }}
                    </td>
                    <td style="text-align:right;">
                        @if($detail->amount == 0 && $detail->note)
                            <em style="color:#888;">Chờ bổ sung</em>
                        @else
                            {{ number_format($detail->amount) }} đ
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#888; padding:20px;">
                        Không có khoản phí nào.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row" style="background: #fff;">
                <td colspan="4" style="text-align:right;">Nợ kỳ trước:</td>
                <td style="text-align:right; font-size:15px; font-weight:normal;">
                    {{ number_format($invoice->previous_debt) }} đ
                </td>
            </tr>
            <tr class="total-row" style="background: #fff; border-bottom: 1px dashed #ccc;">
                <td colspan="4" style="text-align:right;">Phát sinh kỳ này:</td>
                <td style="text-align:right; font-size:15px; font-weight:normal;">
                    {{ number_format($invoice->current_amount) }} đ
                </td>
            </tr>
            <tr class="total-row">
                <td colspan="4" style="text-align:right; font-size: 16px;">TỔNG CỘNG THANH TOÁN:</td>
                <td style="text-align:right; font-size:18px; color: #00236f;">
                    {{ number_format($invoice->total_due_at_issue) }} đ
                </td>
            </tr>
            @if($invoice->paid_amount > 0)
            <tr>
                <td colspan="4" style="text-align:right; font-size:13px;">Đã thanh toán:</td>
                <td style="text-align:right; font-size:13px; color:#155724;">
                    {{ number_format($invoice->paid_amount) }} đ
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align:right; font-size:13px; font-weight:bold;">Còn lại:</td>
                <td style="text-align:right; font-size:13px; color:#721c24; font-weight:bold;">
                    {{ number_format($invoice->remaining_amount) }} đ
                </td>
            </tr>
            @endif
        </tfoot>
    </table>

    {{-- Payments History (if any) --}}
    @if($invoice->payments->where('status', 'success')->count() > 0)
    <div class="payments-section">
        <h3>Lịch sử thanh toán</h3>
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Phương thức</th>
                    <th>Ghi chú</th>
                    <th>Số tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments->where('status', 'success') as $payment)
                <tr>
                    <td>{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y') : '—' }}</td>
                    <td>
                        @switch($payment->payment_method)
                            @case('cash') Tiền mặt @break
                            @case('bank_transfer') Chuyển khoản @break
                            @case('momo') MoMo @break
                            @case('vnpay') VNPay @break
                            @default {{ $payment->payment_method }}
                        @endswitch
                    </td>
                    <td>{{ $payment->note ?? '—' }}</td>
                    <td>{{ number_format($payment->amount) }} đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Signatures --}}
    <div class="signatures">
        <div class="sig-block">
            <div class="sig-title">Cư dân / Chủ hộ</div>
            <div style="font-size:11px; margin-top:-55px; font-style:italic; color:#555; margin-bottom:50px;">(Ký và ghi rõ họ tên)</div>
            <div class="sig-name">{{ $invoice->apartment->owner_name ?? '—' }}</div>
        </div>
        <div class="sig-block">
            <div class="sig-title">Ban Quản Lý</div>
            <div style="font-size:11px; margin-top:-55px; font-style:italic; color:#555; margin-bottom:50px;">(Ký, đóng dấu)</div>
            <div class="sig-name">DomusHub</div>
        </div>
    </div>

    <p style="text-align:center; margin-top:30px; font-size:12px; color:#888; font-style:italic;">
        Hóa đơn được xuất tự động bởi hệ thống DomusHub. Vui lòng giữ lại làm bằng chứng thanh toán.
    </p>
</div>

<div class="no-print-actions">
    <a href="javascript:window.history.back();" class="btn-action">🡰 Quay lại</a>
    <button onclick="window.print()" class="btn-action btn-action-primary">🖨 In hóa đơn</button>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => { window.print(); }, 500);
    });
</script>
</body>
</html>
