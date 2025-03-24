<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form Đăng Nhập</title>
    <link rel="stylesheet" href="/css/log_in.css" />
</head>

<body>
    <div class="wrapper">
        <div class="img-homepage"></div>
        <h1 class="heading">Quản Lí Bán Hàng</h1>
        <div class="login-container">
            <form class="login-form" action="/process/login.php" method="POST">
                <h2>Đăng Nhập</h2>
                <div class="form-group">
                    <label for="username">Tên Đăng Nhập</label>
                    <input type="text" id="username" name="username" required />
                </div>
                <div class="form-group">
                    <label for="password">Mật Khẩu</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="form-group">
                    <span><a href="">Quên mật khẩu</a></span>
                    <span> / </span>
                    <span><a href="">Đăng kí</a></span>
                </div>
                <button type="submit">Đăng Nhập</button>
            </form>
        </div>
    </div>
</body>

</html>