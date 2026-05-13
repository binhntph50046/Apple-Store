<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Error</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .content h1 {
            font-size: 90px;
            margin: 0 20px 0 20px;
        }
        .content img {
            width: 500px;
            max-width: 100%;
            height: auto;
        }
        .text {
            font-family: "Poppins", sans-serif;
        }
        .text h2 {
            font-weight: 600;
            font-size: 30px;
        }
        .text p {
            font-size: 14px;
            font-weight: 300;
        }
        .btn {
            background: #009ec5;
            width: 200px;
            height: 40px;
            margin: 20px auto;
            border-radius: 20px;
            color: #fff;
            border: none;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #007a99;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>403</h1>
        <img src="{{ asset('images/gif/error.gif') }}" alt="403 Forbidden">
        <div class="text">
            <h2>Có vẻ như bạn đã bị lạc</h2>
            <p>Trang bạn tìm kiếm không có sẵn hoặc bạn không có quyền truy cập</p>
            <a href="{{ route('admin.dashboard') }}" class="btn">Trang Chủ</a>
        </div>
    </div>
</body>
</html> 