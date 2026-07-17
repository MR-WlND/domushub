@php
    function convert_number_to_words($number) {
        if ($number == 0) return 'Không';
        
        $dictionary = [
            0 => 'không', 1 => 'một', 2 => 'hai', 3 => 'ba', 4 => 'bốn',
            5 => 'năm', 6 => 'sáu', 7 => 'bảy', 8 => 'tám', 9 => 'chín'
        ];
        
        $units = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ'];
        
        $number = round($number);
        $str = strval($number);
        $len = strlen($str);
        
        $padding = (3 - ($len % 3)) % 3;
        $str = str_repeat('0', $padding) . $str;
        $len = strlen($str);
        
        $chunks = str_split($str, 3);
        $num_chunks = count($chunks);
        
        $words = [];
        
        foreach ($chunks as $i => $chunk) {
            $val = intval($chunk);
            if ($val == 0) continue;
            
            $h = intval($chunk[0]);
            $t = intval($chunk[1]);
            $o = intval($chunk[2]);
            
            $chunk_word = [];
            
            // Hundreds
            if ($h > 0) {
                $chunk_word[] = $dictionary[$h] . ' trăm';
            } elseif (count($words) > 0) {
                $chunk_word[] = 'không trăm';
            }
            
            // Tens
            if ($t == 0) {
                if ($o > 0 && ($h > 0 || count($words) > 0)) {
                    $chunk_word[] = 'lẻ';
                }
            } elseif ($t == 1) {
                $chunk_word[] = 'mười';
            } else {
                $chunk_word[] = $dictionary[$t] . ' mươi';
            }
            
            // Ones
            if ($o > 0) {
                if ($o == 1 && $t > 1) {
                    $chunk_word[] = 'mốt';
                } elseif ($o == 5 && $t > 0) {
                    $chunk_word[] = 'lăm';
                } else {
                    $chunk_word[] = $dictionary[$o];
                }
            }
            
            $unit_name = $units[$num_chunks - 1 - $i];
            $words[] = implode(' ', $chunk_word) . ($unit_name ? ' ' . $unit_name : '');
        }
        
        $final_result = implode(' ', $words);
        return ucfirst(trim($final_result)) . ' đồng chẵn';
    }
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Biên lai thu tiền - {{ $payment->receipt_code }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 30px;
            position: relative;
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
        .bql-info p {
            margin: 0;
            font-size: 13px;
        }
        .receipt-meta {
            text-align: right;
            font-size: 13px;
        }
        .receipt-meta p {
            margin: 0 0 5px 0;
        }
        .receipt-title {
            text-align: center;
            margin: 20px 0;
        }
        .receipt-title h1 {
            margin: 0;
            font-size: 22px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .receipt-title p {
            margin: 5px 0 0 0;
            font-style: italic;
        }
        .receipt-body {
            margin-bottom: 30px;
        }
        .info-line {
            display: flex;
            margin-bottom: 10px;
            align-items: baseline;
        }
        .info-label {
            min-width: 180px;
            font-weight: bold;
        }
        .info-dots {
            flex-grow: 1;
            border-bottom: 1px dotted #000;
            margin-left: 5px;
            margin-right: 5px;
        }
        .info-val {
            font-weight: normal;
        }
        .price-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #000;
            background: #f9f9f9;
        }
        .price-box div {
            font-size: 15px;
            font-weight: bold;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            text-align: center;
        }
        .sig-block {
            width: 45%;
        }
        .sig-title {
            font-weight: bold;
            margin-bottom: 60px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
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
            body {
                padding: 0;
            }
            .receipt-container {
                border: none;
                padding: 0;
            }
            .no-print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <div class="header-row">
            <div class="bql-info">
                <h3>Ban Quản Lý Chung Cư DomusHub</h3>
                <p>Địa chỉ: Đường số 10, Khu đô thị mới, TP. Hồ Chí Minh</p>
                <p>Hotline: 1900 1234 - Email: support@domushub.vn</p>
            </div>
            <div class="receipt-meta">
                <p><strong>Số phiếu:</strong> {{ $payment->receipt_code }}</p>
                <p><strong>Hóa đơn:</strong> {{ $payment->invoice->invoice_code }}</p>
                <p><strong>Ngày thu:</strong> {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="receipt-title">
            <h1>BIÊN LAI THU TIỀN</h1>
            <p>(Phiếu xác nhận thanh toán thành công)</p>
        </div>

        <div class="receipt-body">
            <div class="info-line">
                <span class="info-label">Căn hộ nộp tiền:</span>
                <span class="info-val">Căn {{ $payment->invoice->apartment->apartment_number ?? '—' }} (Tầng {{ $payment->invoice->apartment->floor->floor_number ?? '—' }}, Block {{ $payment->invoice->apartment->floor->block->name ?? '—' }})</span>
                <span class="info-dots"></span>
            </div>
            <div class="info-line">
                <span class="info-label">Chủ hộ / Cư dân đại diện:</span>
                <span class="info-val">{{ $payment->invoice->apartment->owner_name ?? 'Cư dân căn hộ' }}</span>
                <span class="info-dots"></span>
            </div>
            @if($payment->payer_name && $payment->payer_name !== ($payment->invoice->apartment->owner_name ?? ''))
            <div class="info-line">
                <span class="info-label">Người nộp tiền:</span>
                <span class="info-val">{{ $payment->payer_name }}</span>
                <span class="info-dots"></span>
            </div>
            @endif
            <div class="info-line">
                <span class="info-label">Nội dung thanh toán:</span>
                <span class="info-val">{{ $payment->note ?? 'Đóng phí dịch vụ chung cư' }}</span>
                <span class="info-dots"></span>
            </div>
            <div class="info-line">
                <span class="info-label">Phương thức thanh toán:</span>
                <span class="info-val">
                    @if($payment->payment_method === 'cash')
                        Tiền mặt
                    @elseif($payment->payment_method === 'bank_transfer')
                        Chuyển khoản ngân hàng
                    @elseif($payment->payment_method === 'vnpay')
                        VNPay (Cổng thanh toán điện tử)
                    @else
                        {{ ucfirst($payment->payment_method) }}
                    @endif
                </span>
                <span class="info-dots"></span>
            </div>

            <div class="price-box">
                <div>Số tiền thực thu:</div>
                <div style="font-size: 18px; color: #000;">{{ number_format($payment->amount) }} VNĐ</div>
            </div>

            <div class="info-line" style="margin-top: 15px;">
                <span class="info-label">Bằng chữ:</span>
                <span class="info-val" style="font-style: italic; font-weight: bold;">{{ convert_number_to_words($payment->amount) }}</span>
                <span class="info-dots"></span>
            </div>
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-title">Người nộp tiền</div>
                <div style="font-size: 11px; margin-top:-55px; font-style:italic; color:#555; margin-bottom: 50px;">(Ký và ghi rõ họ tên)</div>
                <div class="sig-name">{{ $payment->payer_name ?: ($payment->invoice->apartment->owner_name ?? '—') }}</div>
            </div>
            <div class="sig-block">
                <div class="sig-title">Người thu tiền / Thủ quỹ</div>
                <div style="font-size: 11px; margin-top:-55px; font-style:italic; color:#555; margin-bottom: 50px;">(Ký và ghi rõ họ tên)</div>
                <div class="sig-name">{{ $payment->recorder->name ?? 'Hệ thống DomusHub' }}</div>
            </div>
        </div>
    </div>

    <div class="no-print-actions">
        <a href="javascript:window.history.back();" class="btn-action">🡰 Quay lại</a>
        <button onclick="window.print()" class="btn-action btn-action-primary">🖨 In biên lai này</button>
    </div>

    <script>
        // Tự động kích hoạt hộp thoại in sau khi tải trang
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
