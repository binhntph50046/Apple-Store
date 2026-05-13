<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Apple Store - Xác minh tài khoản</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #000000;
            text-align: center;
            padding: 10px;
        }

        .header img {
            max-width: 115px;
            height: auto;
        }

        .content {
            padding: 40px;
            font-size: 16px;
            line-height: 1.8;
            color: #444;
        }

        .content h1 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .button {
            display: inline-block;
            padding: 14px 35px;
            background-color: #d4af37;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            margin: 25px 0;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #b8860b;
        }

        .footer {
            background-color: #f8f8f8;
            padding: 20px;
            font-size: 14px;
            color: #777;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .footer a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 500;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 0;
            }

            .content {
                padding: 20px;
            }

            .button {
                display: block;
                width: 100%;
                max-width: 100%;
                text-align: center;
                padding: 12px 20px;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logo/apple-removebg-preview.png')) }}" alt="Apple Logo">
        </div>
        <div class="content">
            <h1>Xác minh tài khoản của bạn</h1>
            <p>Xin chào {{ $user->name }},</p>
            <p>Chúng tôi rất vui khi bạn đã đăng ký tài khoản tại Apple Store. Để hoàn tất quá trình đăng ký, vui lòng nhấn vào nút bên dưới để xác minh địa chỉ email của bạn.</p>
            <a href="{{ $verificationUrl }}" class="button">Xác minh ngay</a>
            <p>Liên kết xác minh có hiệu lực trong 60 phút. Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email này.</p>
            <p>Trân trọng cảm ơn,<br>Đội ngũ Apple Store</p>
        </div>
        <div class="footer">
            <p>
                Apple Store | 123, Trần Phú, Hoàn Kiếm, Hà Nội<br>
                Email: <a href="mailto:applestore05082005@gmail.com">applestore05082005@gmail.com</a> | Hotline: <a
                    href="tel:0368706552">0368706552</a>
            </p>
            <p>Email này được gửi tự động, xin vui lòng không phản hồi trực tiếp. Cần hỗ trợ? Hãy truy cập [đường dẫn hỗ trợ nếu có].</p>
        </div>
    </div>
</body>

</html>
