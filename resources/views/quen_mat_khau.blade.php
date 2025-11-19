<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" style="padding:0;Margin:0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đặt lại mật khẩu</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style type="text/css">
        body { margin:0; padding:0; background:#f8f9fa; font-family:'Inter', sans-serif; }
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
        img { border:0; display:block; }
        a { text-decoration:none; }
        .wrapper { width:100%; table-layout:fixed; background:#f8f9fa; padding:30px 0; }
        .main { background:#ffffff; max-width:600px; margin:0 auto; border-radius:18px; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.08); }

        /* Header vàng cam giống hệt 2 mail kia */
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

        /* Danh sách hướng dẫn */
        .steps {
            text-align:left;
            background:#fff9e6;
            padding:25px 35px;
            border-radius:12px;
            margin:30px 0;
        }
        .steps ol {
            padding-left:20px;
            margin:0;
        }
        .steps li {
            font-size:17px;
            line-height:1.8;
            margin-bottom:12px;
        }

        /* Nút đặt lại mật khẩu */
        .btn {
            display:inline-block;
            background:linear-gradient(135deg, #DBAB57, #e68a00);
            color:#ffffff !important;
            font-size:20px;
            font-weight:600;
            padding:18px 55px;
            border-radius:50px;
            box-shadow:0 12px 35px rgba(219,171,87,0.4);
            transition:all 0.4s ease;
            margin:30px 0 35px;
            letter-spacing:0.5px;
        }
        .btn:hover {
            transform:translateY(-6px);
            box-shadow:0 18px 40px rgba(219,171,87,0.5);
            background:linear-gradient(135deg, #e68a00, #cc7700);
        }

        /* Link dự phòng */
        .link {
            color:#DBAB57;
            word-break:break-all;
            font-size:15px;
            background:#f8f9fa;
            padding:15px;
            border-radius:10px;
            margin:25px 0;
            display:inline-block;
            font-family:monospace;
        }

        /* Footer giống mail kia */
        .footer {
            background:#f8f9fa;
            padding:40px 30px;
            text-align:center;
            color:#777;
            font-size:14px;
            border-top:1px solid #eee;
        }
        .footer strong { color:#DBAB57; }
        .footer em { font-style:italic; color:#999; }

        @media (max-width:600px) {
            .wrapper { padding:15px 0; }
            .header { padding:40px 20px; }
            .header h1 { font-size:34px !important; }
            .content { padding:40px 30px !important; }
            .btn { font-size:18px; padding:16px 45px; }
            .steps { padding:20px; }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%" cellspacing="0" cellpadding="0">
            <!-- Header vàng cam giống mail kích hoạt & xác nhận đặt phòng -->
            <tr>
                <td class="header">
                    <h1>ĐẶT LẠI MẬT KHẨU</h1>
                </td>
            </tr>

            <!-- Nội dung chính -->
            <tr>
                <td class="content">
                    <p><strong>Chào bạn {{ $data['ho_va_ten'] }},</strong></p>
                    <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
                    <p>Để tiếp tục, vui lòng thực hiện các bước sau:</p>

                    <div class="steps">
                        <ol>
                            <li>Nhập mật khẩu mới</li>
                            <li>Xác nhận lại mật khẩu mới</li>
                            <li>Nhấn nút xác nhận</li>
                        </ol>
                    </div>

                    <a href="{{ $data['link_ne'] }}" class="btn">ĐẶT LẠI MẬT KHẨU CỦA BẠN</a>

                    <p><strong>Lưu ý:</strong> Liên kết này chỉ có giá trị cho một lần sử dụng.</p>

                    <p>Nếu nút không hoạt động, bạn có thể copy đường link sau và dán vào trình duyệt:</p>
                    <div class="link">{{ $data['link_ne'] }}</div>

                    <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này hoặc liên hệ với chúng tôi qua <strong>cskhaihotel@gmail.com</strong>.</p>
                </td>
            </tr>

            <!-- Footer giống hệt mail kia -->
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