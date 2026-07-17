<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background: #f1f5f9; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #f1f5f9; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 800;">
                                DomusHub
                            </h1>
                            <p style="margin: 8px 0 0; color: #fecaca; font-size: 14px;">
                                Thông báo tố cáo
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 36px 40px;">
                            <p style="margin: 0 0 16px; font-size: 16px; color: #0f172a;">
                                Xin chào <strong>{{ $accusedUser->name }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px; font-size: 15px; color: #334155; line-height: 1.7;">
                                Ban quản lý chung cư thông báo rằng bạn đã bị tố cáo liên quan đến sự việc sau đây. Vui lòng đăng nhập hệ thống DomusHub để xem chi tiết và phản hồi.
                            </p>

                            {{-- Ticket Info Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 8px; font-size: 13px; color: #991b1b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Nội dung tố cáo
                                        </p>
                                        <p style="margin: 0 0 6px; font-size: 16px; color: #0f172a; font-weight: 700;">
                                            {{ $ticket->title }}
                                        </p>
                                        <p style="margin: 0 0 12px; font-size: 14px; color: #475569; line-height: 1.6;">
                                            {{ Str::limit($ticket->description, 200) }}
                                        </p>
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 13px; color: #64748b; padding-right: 20px;">
                                                    Mã phản ánh: <strong style="color: #0f172a;">#{{ $ticket->id }}</strong>
                                                </td>
                                                <td style="font-size: 13px; color: #64748b;">
                                                    Ngày gửi: <strong style="color: #0f172a;">{{ $ticket->created_at->format('d/m/Y H:i') }}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 8px 0 24px;">
                                        <a href="{{ url('/resident/tickets/' . $ticket->id) }}"
                                           style="display: inline-block; padding: 14px 36px; background: #dc2626; color: #ffffff; font-size: 15px; font-weight: 700; text-decoration: none; border-radius: 10px;">
                                            Xem chi tiết &amp; Phản hồi
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                Bạn có thể chọn <strong>"Tôi xác nhận"</strong> hoặc <strong>"Tôi phản đối"</strong> khi xem chi tiết tố cáo.
                                Ban quản lý sẽ xem xét phản hồi của bạn trước khi đưa ra quyết định cuối cùng.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background: #f8fafc; padding: 20px 40px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                Email này được gửi tự động từ hệ thống DomusHub. Vui lòng không trả lời email này.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
