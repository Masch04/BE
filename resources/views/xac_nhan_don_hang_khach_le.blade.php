<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đặt phòng</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 25px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; }
        .content { padding: 30px; line-height: 1.6; }
        .info { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 15px 0; }
        .info table { width: 100%; }
        .info td { padding: 8px 0; }
        .info td:first-child { font-weight: bold; color: #495057; width: 40%; }
        .highlight { color: #28a745; font-weight: bold; }
        .btn { display: inline-block; background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 50px; margin: 20px 0; font-weight: bold; }
        .footer { background: #343a40; color: #adb5bd; padding: 20px; text-align: center; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ĐẶT PHÒNG THÀNH CÔNG!</h1>
        </div>
        <div class="content">
            <p>Xin chào <strong>{{ $ho_va_ten }}</strong>,</p>
            <p>Chúng tôi đã nhận đơn đặt phòng của bạn. Dưới đây là thông tin chi tiết:</p>

            <div class="info">
                <table>
                    <tr><td>Mã đặt phòng</td><td><span class="highlight">{{ $ma_hoa_don }}</span></td></tr>
                    <tr><td>Loại phòng</td><td>{{ $ten_phong }}</td></tr>
                    <tr><td>Nhận phòng</td><td>{{ $ngay_den }}</td></tr>
                    <tr><td>Trả phòng</td><td>{{ $ngay_di }}</td></tr>
                    <tr><td>Tổng tiền</td><td><strong style="color: #dc3545;">{{ $tong_tien }} VNĐ</strong></td></tr>
                    @if($ghi_chu)
                    <tr><td>Ghi chú</td><td>{{ $ghi_chu }}</td></tr>
                    @endif
                </table>
            </div>

            <p>Chúng tôi sẽ <strong>liên hệ xác nhận trong 30 phút</strong> qua SĐT hoặc email.</p>
            <p>Nếu cần hỗ trợ ngay, vui lòng gọi <strong>1900 1234</strong>.</p>

            <center>
                <a href="tel:19001234" class="btn">Gọi ngay</a>
            </center>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Khách Sạn ABC<br>
            Địa chỉ: 123 Đường ABC, TP.HCM | Email: support@hotel.com
        </div>
    </div>
</body>
</html>