<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="vi">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">
    <title>Thanh Toán Thành Công</title>
    <style type="text/css">
        #outlook a { padding: 0; }
        .es-button { mso-style-priority: 100 !important; text-decoration: none !important; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
        table, td { border-collapse: collapse; }
        @media only screen and (max-width: 600px) {
            .es-m-p0r { padding-right: 0 !important; }
        }
    </style>
</head>

<body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
    <div dir="ltr" class="es-wrapper-color" lang="vi" style="background-color:#FAFAFA">
        <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" role="none"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#FAFAFA">
            <tr>
                <td valign="top" style="padding:0;Margin:0">

                    <!-- HEADER -->
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" role="none"
                                    style="background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:20px;Margin:0">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none">
                                                <tr>
                                                    <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                                                            <tr>
                                                                <td align="center" style="padding:10px 0;font-size:0px">
                                                                    <img src="https://dzfullstack.com/assets/images/logo-img.png"
                                                                        alt="Logo" style="display:block;border:0;outline:none;text-decoration:none" width="100">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" style="padding-bottom:10px">
                                                                    <h1 style="Margin:0;line-height:46px;font-size:46px;font-weight:bold;color:#333333">
                                                                        Thanh Toán Thành Công
                                                                    </h1>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" style="padding:5px 40px 20px">
                                                                    <p style="Margin:0;line-height:21px;color:#333333;font-size:14px">
                                                                        Chân thành cảm ơn quý khách <strong>{{ $bien_1['ten_nguoi_nhan'] }}</strong><br>
                                                                        đã hoàn thành thanh toán hóa đơn <strong>{{ $bien_1['ma_hoa_don'] }}</strong><br>
                                                                        với số tiền <strong>{{ number_format($bien_1['tong_tien'], 0, ',', '.') }} ₫</strong> thành công.
                                                                        <br><br>Xin cảm ơn và chúc quý khách một ngày thật tuyệt vời!
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- DANH SÁCH PHÒNG & DỊCH VỤ -->
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" role="none"
                                    style="background-color:#FFFFFF;width:600px">

                                    <!-- Tiêu đề danh sách phòng -->
                                    <tr>
                                        <td align="left" style="padding:20px 20px 10px">
                                            <h2 style="Margin:0;font-size:26px;font-weight:bold;color:#333333">Chi Tiết Đặt Phòng</h2>
                                        </td>
                                    </tr>

                                    <!-- Header bảng -->
                                    <tr>
                                        <td align="left" style="padding:10px 20px">
                                            <table width="100%" cellspacing="0" cellpadding="8" style="border-bottom:2px solid #efefef;font-size:14px;color:#333333">
                                                <tr>
                                                    <td width="20%"><strong>Hình Ảnh</strong></td>
                                                    <td width="30%"><strong>Tên Phòng</strong></td>
                                                    <td width="15%" align="center"><strong>Nhận Phòng</strong></td>
                                                    <td width="15%" align="center"><strong>Trả Phòng</strong></td>
                                                    <td width="20%" align="right"><strong>Giá Thuê</strong></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- Danh sách phòng -->
                                    @foreach ($data as $value)
                                    <tr>
                                        <td align="left" style="padding:0 20px 15px">
                                            <table width="100%" cellspacing="0" cellpadding="8" style="border-bottom:1px solid #f0f0f0;font-size:14px">
                                                <tr>
                                                    <td width="20%" valign="top">
                                                        <img src="{{ $value->hinh_anh }}" alt="Phòng" width="105" style="border-radius:6px;display:block">
                                                    </td>
                                                    <td width="30%" valign="top">
                                                        <strong>{{ $value->ten_phong }}</strong><br>
                                                        <small style="color:#666">{{ $value->ten_loai_phong }}</small>
                                                    </td>
                                                    <td width="15%" align="center" valign="top">
                                                        {{ \Carbon\Carbon::parse($value->ngay_den)->format('d/m/Y') }}
                                                    </td>
                                                    <td width="15%" align="center" valign="top">
                                                        {{ \Carbon\Carbon::parse($value->ngay_di)->format('d/m/Y') }}
                                                    </td>
                                                    <td width="20%" align="right" valign="top" style="color:#e67e22;font-weight:bold">
                                                        {{ number_format($value->tong_gia_thue, 0, ',', '.') }} ₫
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    @endforeach

                                    <!-- Dịch vụ bổ sung -->
                                    @if(isset($ds_dich_vu) && $ds_dich_vu->count() > 0)
                                    <tr>
                                        <td align="left" style="padding:20px 20px 10px">
                                            <h2 style="Margin:0;font-size:22px;font-weight:bold;color:#333333">Dịch Vụ Bổ Sung</h2>
                                        </td>
                                    </tr>
                                    @foreach($ds_dich_vu as $dv)
                                    <tr>
                                        <td align="left" style="padding:5px 40px">
                                            <table width="100%" cellspacing="0" cellpadding="5">
                                                <tr>
                                                    <td style="font-size:14px;color:#333333">
                                                        • {{ $dv->dichVu->ten_dich_vu }}
                                                        @if($dv->so_luong > 1) <small style="color:#666">(x{{ $dv->so_luong }})</small> @endif
                                                    </td>
                                                    <td align="right" style="font-size:14px;color:#333333;font-weight:bold">
                                                        {{ number_format($dv->don_gia * $dv->so_luong, 0, ',', '.') }} ₫
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif

                                    <!-- TỔNG TIỀN -->
                                    <tr>
                                        <td align="left" style="padding:20px">
                                            <table width="100%" cellspacing="0" cellpadding="10" style="border-top:3px double #ddd;margin-top:10px">
                                                <tr>
                                                    <td align="right" style="font-size:15px;color:#666">
                                                        Tiền phòng:
                                                    </td>
                                                    <td align="right" width="200" style="font-weight:bold">
                                                        {{ number_format($data->sum('tong_gia_thue'), 0, ',', '.') }} ₫
                                                    </td>
                                                </tr>
                                                @if(isset($ds_dich_vu) && $ds_dich_vu->count() > 0)
                                                <tr>
                                                    <td align="right" style="font-size:15px;color:#666">
                                                        Tiền dịch vụ:
                                                    </td>
                                                    <td align="right" style="font-weight:bold">
                                                        {{ number_format($ds_dich_vu->sum(fn($dv) => $dv->don_gia), 0, ',', '.') }} ₫
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td align="right">
                                                        <h2 style="Margin:0;font-size:24px;color:#e67e22;font-weight:bold">
                                                            Tổng cộng:
                                                        </h2>
                                                    </td>
                                                    <td align="right">
                                                        <h2 style="Margin:0;font-size:28px;color:#e67e22;font-weight:bold">
                                                            {{ number_format($bien_1['tong_tien'], 0, ',', '.') }} ₫
                                                        </h2>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <!-- FOOTER -->
                    <table cellpadding="0" cellspacing="0" class="es-footer" align="center" role="none">
                        <tr>
                            <td align="center" style="padding:20px 0">
                                <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0"
                                    style="background-color:transparent;width:600px" role="none">
                                    <tr>
                                        <td align="center" style="padding:20px">
                                            <p style="Margin:0;line-height:18px;color:#999999;font-size:12px">
                                                Copyright © {{ date('Y') }} DZFullStack. All rights reserved.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>