<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phiếu lương - {{ $bangLuong->user->name }} (Tháng {{ $bangLuong->thang }}/{{ $bangLuong->nam }})</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #1e293b; margin: 0; padding: 20px; }
        .payslip-wrap { max-width: 750px; margin: 0 auto; border: 1px solid #cbd5e1; padding: 30px; border-radius: 8px; }
        .company-header { display: flex; justify-content: space-between; border-bottom: 2px solid #0f172a; padding-bottom: 16px; margin-bottom: 20px; }
        .title { text-align: center; font-size: 20px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 8px 10px; text-align: left; }
        table.bordered th, table.bordered td { border: 1px solid #cbd5e1; }
        table.bordered th { background-color: #f8fafc; font-weight: bold; }
        .total-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 14px 18px; border-radius: 6px; display: flex; justify-content: space-between; font-size: 16px; font-weight: bold; color: #1d4ed8; }
        .footer-sig { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .payslip-wrap { border: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width:750px; margin:0 auto 16px auto; text-align:right;">
        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:bold; cursor:pointer;">
            🖨 In phiếu lương
        </button>
    </div>

    <div class="payslip-wrap">
        <div class="company-header">
            <div>
                <strong style="font-size:16px;">DOMUSHUB MANAGEMENT</strong><br>
                <span>Hệ thống Quản lý Chung cư Cao cấp</span>
            </div>
            <div style="text-align:right;">
                <span>Ngày xuất: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="title">PHIẾU LƯƠNG NHÂN VIÊN</div>

        <table>
            <tr>
                <td style="width:15%;">Họ và tên:</td>
                <td style="width:35%;"><strong>{{ $bangLuong->user->name }}</strong></td>
                <td style="width:15%;">Mã nhân viên:</td>
                <td style="width:35%;">NV-{{ str_pad($bangLuong->user->id, 4, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $bangLuong->user->email }}</td>
                <td>Chức vụ:</td>
                <td>{{ strtoupper($bangLuong->user->role) }}</td>
            </tr>
            <tr>
                <td>Kỳ lương:</td>
                <td><strong>Tháng {{ sprintf('%02d/%04d', $bangLuong->thang, $bangLuong->nam) }}</strong></td>
                <td>Ngày công chuẩn:</td>
                <td>{{ $bangLuong->so_ngay_cong_chuan }} ngày</td>
            </tr>
        </table>

        <table class="bordered">
            <thead>
                <tr>
                    <th>DIỄN GIẢI KHOẢN MỤC</th>
                    <th style="text-align:right;">CÔNG / GIỜ</th>
                    <th style="text-align:right;">THÀNH TIỀN (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lương cơ bản theo hợp đồng</td>
                    <td>-</td>
                    <td style="text-align:right;">{{ number_format($bangLuong->luong_co_ban) }}</td>
                </tr>
                <tr>
                    <td>Lương theo ngày công thực tế</td>
                    <td style="text-align:right;">{{ $bangLuong->so_ngay_cong_thuc_te }} ngày</td>
                    <td style="text-align:right;">{{ number_format($bangLuong->tien_luong_theo_cong) }}</td>
                </tr>
                <tr>
                    <td>Lương làm thêm giờ (OT)</td>
                    <td style="text-align:right;">{{ $bangLuong->so_gio_ot }} giờ</td>
                    <td style="text-align:right;">{{ number_format($bangLuong->tien_ot) }}</td>
                </tr>

                {{-- Phụ cấp --}}
                @foreach($bangLuong->chiTietPhuCaps as $pc)
                    <tr>
                        <td>Phụ cấp: {{ $pc->danhMuc->ten_phu_cap }}</td>
                        <td>-</td>
                        <td style="text-align:right; color:#16a34a;">+{{ number_format($pc->so_tien) }}</td>
                    </tr>
                @endforeach

                {{-- Thưởng --}}
                @foreach($bangLuong->chiTietThuongs as $th)
                    <tr>
                        <td>Thưởng: {{ $th->danhMuc->ten_thuong }} {{ $th->ly_do ? '('.$th->ly_do.')' : '' }}</td>
                        <td>-</td>
                        <td style="text-align:right; color:#16a34a;">+{{ number_format($th->so_tien) }}</td>
                    </tr>
                @endforeach

                {{-- Khấu trừ --}}
                @foreach($bangLuong->chiTietKhauTrus as $kt)
                    <tr>
                        <td>Khấu trừ: {{ $kt->danhMuc->ten_khau_tru }} {{ $kt->ly_do ? '('.$kt->ly_do.')' : '' }}</td>
                        <td>-</td>
                        <td style="text-align:right; color:#dc2626;">-{{ number_format($kt->so_tien) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <span>THỰC LĨNH CHUYỂN KHOẢN:</span>
            <span>{{ number_format($bangLuong->thuc_linh) }} VNĐ</span>
        </div>

        <div class="footer-sig">
            <div>
                <strong>Người lập phiếu</strong><br><br><br><br>
                <span>{{ optional($bangLuong->recorder)->name ?? 'Hệ thống' }}</span>
            </div>
            <div>
                <strong>Người duyệt</strong><br><br><br><br>
                <span>{{ optional($bangLuong->approver)->name ?? 'Ban Quản trị' }}</span>
            </div>
            <div>
                <strong>Người nhận lương</strong><br><br><br><br>
                <span>{{ $bangLuong->user->name }}</span>
            </div>
        </div>
    </div>

</body>
</html>
