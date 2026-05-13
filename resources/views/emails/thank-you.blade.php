<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư cảm ơn</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4a90e2; padding: 20px; text-align: center;">
                            <img src="https://via.placeholder.com/150x50?text=Your+Logo" alt="Logo" style="max-width: 150px; height: auto;">
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px; color: #333333;">
                            <h2 style="font-size: 24px; margin: 0 0 20px; color: #333333;">Xin chào {{ $contact->first_name }} {{ $contact->last_name }},</h2>
                            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Cảm ơn bạn đã liên hệ với chúng tôi! Chúng tôi đã nhận được thông tin của bạn và đội ngũ của chúng tôi sẽ xem xét, phản hồi trong thời gian sớm nhất có thể.
                            </p>
                            <p style="font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                Nếu bạn có thêm câu hỏi hoặc cần hỗ trợ ngay, vui lòng liên hệ qua email <a href="mailto:support@yourdomain.com" style="color: #4a90e2; text-decoration: none;">support@yourdomain.com</a> hoặc số điện thoại <a href="tel:+1234567890" style="color: #4a90e2; text-decoration: none;">+123-456-7890</a>.
                            </p>
                            <p style="font-size: 16px; line-height: 1.6; margin: 0;">
                                Trân trọng,<br>
                                <strong>Đội ngũ hỗ trợ</strong>
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f8f8; padding: 20px; text-align: center; font-size: 14px; color: #666666;">
                            <p style="margin: 0 0 10px;">© 2025 Công ty của bạn. Mọi quyền được bảo lưu.</p>
                            <p style="margin: 0;">
                                <a href="https://yourdomain.com" style="color: #4a90e2; text-decoration: none;">Trang web</a> | 
                                <a href="https://yourdomain.com/privacy" style="color: #4a90e2; text-decoration: none;">Chính sách bảo mật</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>