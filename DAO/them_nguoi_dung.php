<?php
// Kết nối đến cơ sở dữ liệu
$server = 'localhost';
$user = 'root';
$password = '';
$nameDataBase = 'web2';

$conn = new mysqli($server, $user, $password, $nameDataBase);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}

// Thiết lập UTF-8 để tránh lỗi tiếng Việt
$conn->set_charset("utf8");

// Kiểm tra nếu dữ liệu được gửi bằng phương thức POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userName = trim($_POST["userName"]);
    $passWord = trim($_POST["passWord"]);
    $numberPhone = trim($_POST["numberPhone"]);
    $name = trim($_POST["name"]);
    $level = trim($_POST["level"]);

    // Kiểm tra xem các trường có rỗng không
    if (empty($userName) || empty($passWord) || empty($numberPhone) || empty($name) || empty($level)) {
        echo "Vui lòng điền đầy đủ thông tin.";
        exit;
    }

    // Mã hóa mật khẩu trước khi lưu vào CSDL (tăng cường bảo mật)
    // $hashedPassword = password_hash($passWork, PASSWORD_BCRYPT);

    // Chuẩn bị truy vấn SQL để tránh SQL Injection
    $sql = "INSERT INTO account (userName, passWord, numberPhone, name, level) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssss", $userName, $passWord, $numberPhone, $name, $level);

        if ($stmt->execute()) {
           echo " thêm người dùng thành công";
        }else {
           echo " thêm người dùng thất bại";
        }
        $stmt->close();
    } else {
        echo "Lỗi truy vấn: " . $conn->error;
    }
}

$conn->close();
?>
