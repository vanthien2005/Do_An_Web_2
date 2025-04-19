<?php
$server = 'localhost';
$user = 'root';
$password = '';
$nameDataBase = 'project_web2';
$conn = new mysqli($server, $user, $password, $nameDataBase);

if ($conn->connect_error) {
    die('Lỗi kết nối: ' . $conn->connect_error);
}

$conn->set_charset("utf8");

// Kiểm tra dữ liệu gửi lên
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $userName = $_POST['userName'];
    $passWord = $_POST['passWord'];
    $numberPhone = $_POST['numberPhone'];
    $name = $_POST['name'];
    $level = $_POST['level'];

    // Cập nhật dữ liệu
    $sql = "UPDATE accounts SET userName=?, passWord=?, numberPhone=?, name=?, level=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $userName, $passWord, $numberPhone, $name, $level, $id);

    if ($stmt->execute()) {
        echo "sửa thành công";
    } else echo " sửa thất bại";
    $stmt->close();
}

$conn->close();
?>