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
        /* (Giữ nguyên toàn bộ CSS cũ) */
        #outlook a { padding: 0; }
        .es-button { mso-style-priority: 100 !important; text-decoration: none !important; }
        /* ... (toàn bộ CSS bạn đã có) ... */
    </style>
</head>

<body style="width:100%;font-family:arial, 'helvetica neue', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
    <div dir="ltr" class="es-wrapper-color" lang="vi" style="background-color:#FAFAFA">
        <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" role="none"
            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#FAFAFA">
            <tr>
                <td valign="top" style="padding:0;Margin:0">

                    <!-- HEADER -->
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0"
                                    cellspacing="0" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left" style="padding:20px;Margin:0">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none">
                                                <tr>
                                                    <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                                                            <tr>
                                                                <td align="center"
                                                                    style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0px">
                                                                    <img src="https://dzfullstack.com/assets/images/logo-img.png"
                                                                        alt style="display:block;border:0;outline:none;text-decoration:none" width="100">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="es-m-txt-c"
                                                                    style="padding:0;Margin:0;padding-bottom:10px">
                                                                    <h1 style="Margin:0;line-height:46px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:46px;font-weight:bold;color:#333333">
                                                                        Thanh Toán Thành Công
                                                                    </h1>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center"
                                                                    style="Margin:0;padding-top:5px;padding-bottom:20px;padding-left:40px;padding-right:40px">
                                                                    <p style="Margin:0;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                                                        Chân thành cảm ơn quý khách <strong>{{ $bien_1['ten_nguoi_nhan'] }}</strong>
                                                                        đã hoàn thành thanh toán hóa đơn <strong>{{ $bien_1['ma_hoa_don'] }}</strong>
                                                                        với số tiền <strong>{{ number_format($bien_1['tong_tien']) }}đ</strong> thành công.
                                                                        <br>Xin cảm ơn và chúc quý khách một ngày tuyệt vời.
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

                    <!-- DANH SÁCH PHÒNG -->
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0"
                                    cellspacing="0" role="none"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                                    <tr>
                                        <td align="left"
                                            style="Margin:0;padding-bottom:10px;padding-top:20px;padding-left:20px;padding-right:20px">
                                            <h2 style="Margin:0;line-height:31px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:26px;font-weight:bold;color:#333333">
                                                Danh Sách Phòng
                                            </h2>
                                        </td>
                                    </tr>

                                    <!-- Header bảng phòng -->
                                    <tr>
                                        <td class="esdev-adapt-off" align="left"
                                            style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                                            <table cellpadding="0" cellspacing="0" class="esdev-mso-table" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px">
                                                <tr>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:105px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333"><strong>Hình Ảnh</strong></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td align="center" style="padding:0;Margin:0;width:153px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333"><b>Phòng</b></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td align="center" style="padding:0;Margin:0;width:75px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333"><strong>Ngày Đến</strong></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td align="left" style="padding:0;Margin:0;width:79px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333"><strong>Ngày Đi</strong></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-right" align="right" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                                            <tr>
                                                                <td align="left" style="padding:0;Margin:0;width:68px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333"><strong>Tổng Giá</strong></p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- Danh sách phòng -->
                                    @foreach ($data as $value)
                                    <tr>
                                        <td class="esdev-adapt-off" align="left"
                                            style="Margin:0;padding-top:10px;padding-bottom:10px;padding-left:20px;padding-right:20px">
                                            <table cellpadding="0" cellspacing="0" class="esdev-mso-table" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px">
                                                <tr>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:105px">
                                                                    <img class="adapt-img" src="{{ $value->hinh_anh }}" alt
                                                                        style="display:block;border:0;outline:none;text-decoration:none" width="105">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td align="center" style="padding:0;Margin:0;width:151px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333">
                                                                        <strong>{{ $value->ten_phong }} - {{ $value->ten_loai_phong }}</strong>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td align="center" style="padding:0;Margin:0;width:76px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333">
                                                                        {{ \Carbon\Carbon::parse($value->ngay_den)->format('d/m/Y') }}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                                            <tr>
                                                                <td align="left" style="padding:0;Margin:0;width:81px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333">
                                                                        {{ \Carbon\Carbon::parse($value->ngay_di)->format('d/m/Y') }}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding:0;Margin:0;width:20px"></td>
                                                    <td class="esdev-mso-td" valign="top" style="padding:0;Margin:0">
                                                        <table cellpadding="0" cellspacing="0" class="es-right" align="right" role="none"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                                            <tr>
                                                                <td align="left" style="padding:0;Margin:0;width:67px">
                                                                    <p style="Margin:0;font-size:14px;color:#333333">
                                                                        {{ number_format($value->tong_gia_thue, 0, '.', ',') }}đ
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    @endforeach

                                    <!-- DỊCH VỤ BỔ SUNG -->
                                    @if(isset($ds_dich_vu) && $ds_dich_vu->count() > 0)
                                    <tr>
                                        <td align="left"
                                            style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                                            <h2 style="Margin:0;line-height:31px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:26px;font-weight:bold;color:#333333">
                                                Dịch Vụ Bổ Sung
                                            </h2>
                                        </td>
                                    </tr>
                                    @foreach($ds_dich_vu as $dv)
                                    <tr>
                                        <td align="left"
                                            style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:40px;padding-right:40px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none">
                                                <tr>
                                                    <td align="left" style="padding:0;Margin:0;width:400px">
                                                        <p style="Margin:0;font-size:14px;color:#333333">
                                                            <strong>{{ $dv->dichVu->ten_dich_vu }}</strong>
                                                            @if($dv->so_luong > 1) (x{{ $dv->so_luong }}) @endif
                                                        </p>
                                                    </td>
                                                    <td align="right" style="padding:0;Margin:0;width:120px">
                                                        <p style="Margin:0;font-size:14px;color:#333333">
                                                            {{ number_format($dv->don_gia * $dv->so_luong, 0, '.', ',') }}đ
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif

                                    <!-- TỔNG TIỀN -->
                                    <tr>
                                        <td class="esdev-adapt-off" align="left"
                                            style="padding:0;Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none"
                                                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                <tr>
                                                    <td align="center" style="padding:0;Margin:0;width:560px">
                                                        <table cellpadding="0" cellspacing="0" width="100%"
                                                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;border-top:2px solid #efefef"
                                                            role="presentation">
                                                            <tr>
                                                                <td align="right" style="padding:0;Margin:0;padding-top:10px">
                                                                    <p style="Margin:0;font-size:14px;color:#666666">
                                                                        Tiền phòng:
                                                                        <strong>{{ number_format($data->sum('tong_gia_thue'), 0, '.', ',') }}đ</strong>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            @if(isset($ds_dich_vu) && $ds_dich_vu->count() > 0)
                                                            <tr>
                                                                <td align="right" style="padding:0;Margin:0;padding-top:5px">
                                                                    <p style="Margin:0;font-size:14px;color:#666666">
                                                                        Tiền dịch vụ:
                                                                        <strong>{{ number_format($ds_dich_vu->sum(fn($dv) => $dv->don_gia * $dv->so_luong), 0, '.', ',') }}đ</strong>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            <tr>
                                                                <td align="right" style="padding:0;Margin:0;padding-top:10px">
                                                                    <h3 style="Margin:0;line-height:24px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:20px;font-weight:bold;color:#333333">
                                                                        Tổng cộng: <strong style="color:#e67e22">{{ number_format($bien_1['tong_tien'], 0, '.', ',') }}đ</strong>
                                                                    </h3>
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

                    <!-- FOOTER -->
                    <table cellpadding="0" cellspacing="0" class="es-footer" align="center" role="none"
                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent">
                        <tr>
                            <td align="center" style="padding:0;Margin:0">
                                <table class="es-footer-body" align="center" cellpadding="0" cellspacing="0"
                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:640px" role="none">
                                    <tr>
                                        <td align="left"
                                            style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px">
                                            <table cellpadding="0" cellspacing="0" width="100%" role="none">
                                                <tr>
                                                    <td align="left" style="padding:0;Margin:0;width:600px">
                                                        <table cellpadding="0" cellspacing="0" width="100%" role="presentation">
                                                            <tr>
                                                                <td align="center" style="padding:0;Margin:0;padding-bottom:35px">
                                                                    <p style="Margin:0;line-height:18px;color:#333333;font-size:12px">
                                                                        Copyright DZFullStack © 2025. All rights reserved.
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
                </td>
            </tr>
        </table>
    </div>
</body>
</html>