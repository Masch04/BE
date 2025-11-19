<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" style="padding:0;Margin:0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Xác nhận đặt phòng thành công</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style type="text/css">
        body { margin:0; padding:0; background:#f8f9fa; font-family:'Inter', sans-serif; }
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img { border:0; display:block; margin:0 auto; } /* ĐÃ SỬA: margin:0 auto để căn giữa */
        a { text-decoration:none; }
        .wrapper { width:100%; table-layout:fixed; background:#f8f9fa; padding:30px 0; }
        .main { background:#ffffff; max-width:600px; margin:0 auto; border-radius:18px; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.08); }

        /* Header vàng cam giống mail kích hoạt */
        .header {
            background:linear-gradient(135deg, #DBAB57, #977334);
            padding:50px 30px;
            text-align:center;
            position:relative;
            border-radius:18px 18px 0 0;
        }
        .header h1 {
            font-family:'Playfair Display', serif;
            font-size:42px;
            color:#ffffff;
            margin:0;
            font-weight:700;
            text-shadow:0 4px 15px rgba(0,0,0,0.3);
        }

        /* Nội dung */
        .content {
            background:#ffffff;
            padding:50px 45px;
            text-align:center;
            color:#333;
        }
        .content p {
            font-size:17px;
            line-height:1.8;
            color:#555;
            margin:0 0 25px 0;
        }
        .content strong { color:#1a1a1a; }
        .highlight { background:#fff9e6; padding:20px; border-radius:12px; margin:30px 0; }
        .price-table { width:100%; background:#f8f9fa; border-radius:12px; overflow:hidden; margin:30px 0; }
        .price-table th { background:#DBAB57; color:#fff; padding:18px; text-align:left; font-size:18px; }
        .price-table td { padding:18px; border-bottom:1px solid #eee; }
        .total-row td { background:#fff8e1 !important; font-size:22px; font-weight:bold; }
        .text-right { text-align:right; }
        .text-success { color:#27ae60; }
        .text-danger { color:#e74c3c; font-weight:bold; }

        /* QR Code - CĂN GIỮA HOÀN HẢO */
        .qr-section { margin:40px 0; text-align:center; }
        .qr-section p { margin-bottom:20px; }
        .qr-section img {
            display:block !important;
            margin:0 auto !important;
            border:10px solid #fff;
            box-shadow:0 15px 35px rgba(0,0,0,0.15);
            border-radius:16px;
            max-width:260px;
            height:auto;
        }

        /* Footer giống mail kích hoạt */
        .footer {
            background:#f8f9fa;
            padding:40px 30px;
            text-align:center;
            color:#777;
            font-size:14px;
            border-top:1px solid #eee;
        }
        .footer strong { color:#DBAB57; }

        @media (max-width:600px) {
            .wrapper { padding:15px 0; }
            .header { padding:40px 20px; }
            .header h1 { font-size:34px !important; }
            .content { padding:40px 30px !important; }
            .price-table th, .price-table td { padding:14px; font-size:15px; }
            .total-row td { font-size:20px !important; }
            .qr-section img { max-width:220px; border-width:8px; }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%" cellspacing="0" cellpadding="0">
            <!-- Header vàng cam giống mail kích hoạt -->
            <tr>
                <td class="header">
                    <h1>XÁC NHẬN ĐẶT PHÒNG</h1>
                </td>
            </tr>

            <!-- Nội dung chính -->
            <tr>
                <td class="content">
                    <p><strong>Xin chào {{ $ho_va_ten }},</strong></p>
                    <p>Cảm ơn quý khách đã tin tưởng và đặt phòng tại <strong>AIHOTEL</strong>.</p>
                    <p>Chúng tôi đã nhận được yêu cầu đặt phòng của quý khách với thông tin như sau:</p>

                    <div class="highlight">
                        <p><strong>Thời gian lưu trú:</strong><br>
                        Từ <strong>{{ $tu_ngay }}</strong> → Đến <strong>{{ $den_ngay }}</strong> ({{ $so_dem }} đêm)</p>
                    </div>

                    <!-- Bảng giá -->
                    <table class="price-table" cellspacing="0">
                        <tr>
                            <th>Hạng mục</th>
                            <th class="text-right">Thành tiền</th>
                        </tr>
                        <tr>
                            <td>Tiền phòng</td>
                            <td class="text-right">{{ number_format($tien_phong, 0, ',', '.') }} ₫</td>
                        </tr>

                        @if($tien_dich_vu > 0)
                            <tr>
                                <td>Dịch vụ bổ sung
                                    @foreach($chi_tiet_dich_vu as $dv)
                                        <br><small>• {{ $dv['ten'] }} (x{{ $dv['so_luong'] }}): {{ number_format($dv['thanh_tien'], 0, ',', '.') }} ₫</small>
                                    @endforeach
                                </td>
                                <td class="text-right text-success">+ {{ number_format($tien_dich_vu, 0, ',', '.') }} ₫</td>
                            </tr>
                        @endif

                        <tr class="total-row">
                            <td><strong>TỔNG CỘNG</strong></td>
                            <td class="text-right text-danger"><strong>{{ number_format($tong_tien, 0, ',', '.') }} ₫</strong></td>
                        </tr>
                    </table>

                    @if($ma_qr_code)
                        <div class="qr-section">
                            <p><strong>Quét mã QR để thanh toán nhanh:</strong></p>
                            <img src="{{ $ma_qr_code }}" width="260" height="260" alt="QR Code Thanh Toán">
                        </div>
                    @else
                        <div style="background:#fff3cd; padding:20px; border-radius:12px; margin:30px 0;">
                            <p><strong>Lưu ý:</strong> Tổng tiền lớn, QR không hỗ trợ.</p>
                            <p>Vui lòng chuyển khoản thủ công:<br>
                            <strong>MB Bank - 1700116117118</strong><br>
                            Nội dung: <strong>TTDP{{ $hoaDon->id ?? '' }}</strong></p>
                        </div>
                    @endif

                    <p>Mọi thắc mắc xin liên hệ: <strong>cskhaihotel@gmail.com</strong></p>
                </td>
            </tr>

            <!-- Footer giống mail kích hoạt -->
            <tr>
                <td class="footer">
                    <p>© {{ date('Y') }} <strong>AIHOTEL</strong>. Tất cả quyền được bảo lưu.</p>
                    <p><em>Đây là email tự động — vui lòng không trả lời email này.</em></p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>