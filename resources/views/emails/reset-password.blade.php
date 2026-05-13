<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Apple Store - Reset Password</title>
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
            <h1>Khôi phục mật khẩu tài khoản</h1>
            <p>Kính thưa Quý khách,</p>
            <p>Chúng tôi ghi nhận yêu cầu khôi phục mật khẩu cho tài khoản của Quý khách tại Apple Store. Để tiếp tục quy
                trình, xin vui lòng nhấn vào nút bên dưới nhằm kích hoạt quá trình đặt lại mật khẩu một cách an toàn và
                nhanh chóng.</p>
            <a href="{{ $resetUrl }}" class="button">Khôi phục ngay</a>
            <p>Liên kết này sẽ có hiệu lực trong vòng 24 giờ. Nếu Quý khách không khởi tạo yêu cầu này, xin vui lòng bỏ
                qua email này hoặc liên hệ với đội ngũ hỗ trợ của chúng tôi để được giải đáp chi tiết.</p>
            <p>Trân trọng và cảm ơn Quý khách đã tin tưởng,<br>Đội ngũ Apple Store</p>
        </div>
        <div class="footer">
            <p>
                Apple Store | 123, Trần Phú, Hoàn Kiếm, Hà Nội<br>
                Email: <a href="mailto:applestore05082005@gmail.com">applestore05082005@gmail.com</a> | Hotline: <a
                    href="tel:0368706552">0368706552</a>
            </p>
            <p>Email này được gửi tự động, xin vui lòng không phản hồi trực tiếp. Hỗ trợ 24/7 tại [đường dẫn hỗ trợ nếu
                có].</p>
        </div>
    </div>
</body>

</html>
