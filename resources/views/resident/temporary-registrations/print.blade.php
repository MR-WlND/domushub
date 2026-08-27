<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mẫu CT01 - Tờ khai thay đổi thông tin cư trú</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: #fff;
            color: #000;
        }
        .container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
            text-align: center;
        }
        .col-left {
            width: 40%;
        }
        .col-right {
            width: 60%;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin-top: 20px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-style: italic;
            font-size: 12pt;
            margin-bottom: 20px;
        }
        .content {
            margin-top: 10px;
        }
        .form-row {
            margin-bottom: 5px;
            display: flex;
        }
        .label {
            font-weight: bold;
            margin-right: 5px;
            white-space: nowrap;
        }
        .value {
            border-bottom: 1px dotted #000;
            flex-grow: 1;
            padding-left: 5px;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .footer-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        @media print {
            body {
                background: none;
            }
            .container {
                width: 100%;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #00236f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button class="no-print btn-print" onclick="window.print()">🖨 In Mẫu CT01</button>
    <div class="container">
        <table class="header-table">
            <tr>
                <td class="col-left">
                    <div style="font-size: 11pt; text-align: left;">
                        <strong>Mẫu CT01</strong> ban hành<br>
                        kèm theo Thông tư số 56/2021/TT-BCA<br>
                        ngày 15/05/2021 của Bộ trưởng Bộ Công an
                    </div>
                </td>
                <td class="col-right">
                    <div style="font-weight: bold;">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                    <div style="font-weight: bold; text-decoration: underline;">Độc lập - Tự do - Hạnh phúc</div>
                </td>
            </tr>
        </table>

        <div class="title">TỜ KHAI THAY ĐỔI THÔNG TIN CƯ TRÚ</div>
        <div class="subtitle">Kính gửi: Công an phường/xã ................................................................</div>

        <div class="content">
            <div class="form-row">
                <span class="label">1. Họ, chữ đệm và tên:</span>
                <span class="value" style="text-transform: uppercase;">{{ $temporaryRegistration->type == 'residence' ? $temporaryRegistration->guest_name : $temporaryRegistration->user->name }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <div style="display: flex; width: 60%;">
                    <span class="label">2. Ngày, tháng, năm sinh:</span>
                    <span class="value">
                        @if($temporaryRegistration->type == 'residence')
                            {{ $temporaryRegistration->guest_dob ? $temporaryRegistration->guest_dob->format('d/m/Y') : '..../..../........' }}
                        @else
                            {{ $temporaryRegistration->user->dob ?? '..../..../........' }}
                        @endif
                    </span>
                </div>
                <div style="display: flex; width: 35%;">
                    <span class="label">3. Giới tính:</span>
                    <span class="value">
                        @if($temporaryRegistration->type == 'residence')
                            {{ $temporaryRegistration->guest_gender == 'male' ? 'Nam' : ($temporaryRegistration->guest_gender == 'female' ? 'Nữ' : '...........') }}
                        @else
                            {{ $temporaryRegistration->user->gender == 'male' ? 'Nam' : 'Nữ' }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="form-row">
                <span class="label">4. Số định danh cá nhân/CMND:</span>
                <span class="value">
                    {{ $temporaryRegistration->type == 'residence' ? $temporaryRegistration->guest_cccd : $temporaryRegistration->user->cccd }}
                </span>
            </div>

            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <div style="display: flex; width: 50%;">
                    <span class="label">5. Số điện thoại liên hệ:</span>
                    <span class="value">
                        {{ $temporaryRegistration->type == 'residence' ? $temporaryRegistration->guest_phone : $temporaryRegistration->user->phone }}
                    </span>
                </div>
                <div style="display: flex; width: 45%;">
                    <span class="label">6. Email:</span>
                    <span class="value">
                        {{ $temporaryRegistration->type == 'residence' ? $temporaryRegistration->guest_email : $temporaryRegistration->user->email }}
                    </span>
                </div>
            </div>

            <div class="form-row">
                <span class="label">7. Nơi thường trú:</span>
                <span class="value">
                    {{ $temporaryRegistration->type == 'residence' ? $temporaryRegistration->guest_hometown : ($temporaryRegistration->user->address ?? '........................................................................................') }}
                </span>
            </div>

            <div class="form-row">
                <span class="label">8. Nơi tạm trú (Chỗ ở hiện tại):</span>
                <span class="value">
                    Căn hộ {{ $temporaryRegistration->apartment->apartment_number }}, Tòa {{ $temporaryRegistration->apartment->floor->block->name ?? '' }}
                </span>
            </div>

            <div class="form-row">
                <span class="label">9. Nghề nghiệp, nơi làm việc:</span>
                <span class="value">..............................................................................................................................</span>
            </div>

            <div class="form-row">
                <span class="label">10. Họ, chữ đệm và tên chủ hộ:</span>
                <span class="value" style="text-transform: uppercase;">
                    {{ $temporaryRegistration->user->name }}
                </span>
            </div>
            
            <div class="form-row">
                <span class="label">11. Mối quan hệ với chủ hộ:</span>
                <span class="value">
                    {{ $temporaryRegistration->relationship ?? ($temporaryRegistration->type == 'absence' ? 'Chủ hộ / Thành viên' : '...........................') }}
                </span>
            </div>

            <div class="form-row">
                <span class="label">12. Số định danh cá nhân/CMND của chủ hộ:</span>
                <span class="value">{{ $temporaryRegistration->user->cccd ?? '.........................................' }}</span>
            </div>

            <div class="form-row">
                <span class="label">13. Nội dung đề nghị:</span>
                <span class="value">
                    @if($temporaryRegistration->type == 'residence')
                        Đăng ký tạm trú từ ngày {{ $temporaryRegistration->start_date->format('d/m/Y') }} 
                        đến {{ $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('d/m/Y') : '..../..../........' }}
                    @else
                        Khai báo tạm vắng từ ngày {{ $temporaryRegistration->start_date->format('d/m/Y') }} 
                        đến {{ $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('d/m/Y') : '..../..../........' }}
                    @endif
                </span>
            </div>

            <div class="form-row">
                <span class="label">Lý do:</span>
                <span class="value">{{ $temporaryRegistration->reason ?? '..........................................................................................................................' }}</span>
            </div>

            <div class="form-row">
                <span class="label">14. Những thành viên trong hộ gia đình cùng thay đổi:</span>
            </div>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 20px; text-align: center; font-size: 12pt;" border="1">
                <thead>
                    <tr>
                        <th style="width: 5%;">TT</th>
                        <th style="width: 30%;">Họ, chữ đệm và tên</th>
                        <th style="width: 15%;">Ngày tháng năm sinh</th>
                        <th style="width: 10%;">Giới tính</th>
                        <th style="width: 20%;">Số ĐDCN/CMND</th>
                        <th style="width: 20%;">Quan hệ với người có thay đổi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="height: 30px;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr style="height: 30px;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div style="text-align: right; margin-right: 20px; font-style: italic;">
                Ngày ....... tháng ....... năm .......
            </div>

            <table class="footer-table">
                <tr>
                    <td>
                        <strong>Ý KIẾN CỦA CHỦ HỘ</strong><br>
                        <span style="font-size: 12pt; font-style: italic;">(Ký, ghi rõ họ tên)</span>
                        <div style="height: 80px;"></div>
                        {{ $temporaryRegistration->user->name }}
                    </td>
                    <td>
                        <strong>NGƯỜI KÊ KHAI</strong><br>
                        <span style="font-size: 12pt; font-style: italic;">(Ký, ghi rõ họ tên)</span>
                        <div style="height: 80px;"></div>
                        {{ $temporaryRegistration->type == 'residence' ? $temporaryRegistration->guest_name : $temporaryRegistration->user->name }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
