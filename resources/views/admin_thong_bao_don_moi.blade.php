<!DOCTYPE html>
<html>
<head>
    <title>ĐƠN MỚI TỪ KHÁCH VÃNG LAI</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .alert { background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; margin-bottom: 20px; }
        .alert strong { color: #856404; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        td:first-child { font-weight: bold; color: #495057; }
        .btn { display: inline-block; background: #dc3545; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            <strong>CÓ ĐƠN ĐẶT PHÒNG MỚI TỪ KHÁCH VÃNG LAI!</strong>
        </div>

        <p><strong>Thời gian:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

        <table>
            <tr><td>Mã đơn</td><td><strong style="color: #dc3545;">{{ $ma_hoa_don }}</strong></td></tr>
            <tr><td>Khách</td><td>{{ $ho_va_ten }}</td></tr>
            <tr><td>SĐT</td><td><a href="tel:{{ $sdt }}">{{ $sdt }}</a></td></tr>
            <tr><td>Email</td><td><a href="mailto:{{ $email_khach }}">{{ $email_khach }}</a></td></tr>
            <tr><td>Phòng</td><td>{{ $ten_phong }}</td></tr>
            <tr><td>Ngày</td><td>{{ $ngay_den }} → {{ $ngay_di }}</td></tr>
            <tr><td>Tổng tiền</td><td><strong>{{ $tong_tien }} VNĐ</strong></td></tr>
        </table>

        <center>
            <a href="{{ url('/admin/hoa-don') }}" class="btn">XỬ LÝ ĐƠN NGAY</a>
        </center>
    </div>
</body>
</html>