<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>XÁC NHẬN ĐẶT PHÒNG THÀNH CÔNG</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;color:#333}
        .container{max-width:600px;margin:auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.1)}
        .header{background:#FFA73B;padding:30px;text-align:center;color:white}
        .header h1{margin:0;font-size:32px}
        .content{padding:40px;line-height:1.7}
        table{width:100%;border-collapse:collapse;margin:25px 0;background:#fafafa;border-radius:8px;overflow:hidden}
        th{background:#FFA73B;color:white;padding:16px;text-align:left}
        td{padding:16px;border-bottom:1px solid #eee}
        .total td{background:#fff8e1;font-size:20px;font-weight:bold}
        .text-right{text-align:right}
        .text-success{color:#27ae60}
        .text-danger{color:#e74c3c}
        .qr{text-align:center;margin:30px 0}
        .qr img{border:8px solid #fff;box-shadow:0 8px 25px rgba(0,0,0,.15);border-radius:12px}
        small{color:#777}
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h1>XÁC NHẬN ĐẶT PHÒNG</h1></div>
    <div class="content">
        <p>Xin chào <strong>{{ $ho_va_ten }}</strong>,</p>
        <p>Cảm ơn quý khách đã đặt phòng tại khách sạn chúng tôi!</p>
        <p>Thời gian lưu trú: <strong>{{ $tu_ngay }}</strong> → <strong>{{ $den_ngay }}</strong> ({{ $so_dem }} đêm)</p>

        <table>
            <tr><th>Hạng mục</th><th class="text-right">Thành tiền</th></tr>
            <tr><td>Tiền phòng</td><td class="text-right">{{ number_format($tien_phong, 0, ',', '.') }} ₫</td></tr>

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

            <tr class="total">
                <td><strong>TỔNG CỘNG</strong></td>
                <td class="text-right text-danger"><strong>{{ number_format($tong_tien, 0, ',', '.') }} ₫</strong></td>
            </tr>
        </table>

        @if($ma_qr_code)
            <div class="qr">
                <p><strong>Quét mã QR để thanh toán:</strong></p>
                <img src="{{ $ma_qr_code }}" width="260" height="260">
            </div>
        @else
            <p style="background:#fff3cd;padding:15px;border-radius:8px;">
                <strong>Lưu ý:</strong> Tổng tiền lớn, QR không hỗ trợ. Vui lòng chuyển khoản thủ công:<br>
                <strong>MB Bank - 1700116117118</strong><br>
                Nội dung: <strong>TTDP{{ $hoaDon->id ?? '' }}</strong>
            </p>
        @endif

        <p>Liên hệ: <strong>hotel@gmail.com</strong></p>
        <p><em>Email tự động – không reply.</em></p>
        <hr>
        <p>Trân trọng,<br><strong>Đội ngũ Hotel</strong></p>
    </div>
</div>
</body>
</html>