<?php
// login.php  
session_start(); // Bắt đầu phiên làm việc  

// Kiểm tra xem người dùng đã gửi form hay chưa  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin tên đăng nhập và mật khẩu  
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ở đây bạn có thể thay thế bằng cách kiểm tra dữ liệu trong cơ sở dữ liệu  
    // Ví dụ sử dụng thông tin người dùng giả định  
    $correct_username = 'admin'; // Tên đăng nhập đúng  
    $correct_password = '123456'; // Mật khẩu đúng  

    // Kiểm tra thông tin đăng nhập  
    if ($username === $correct_username && $password === $correct_password) {
        // Đăng nhập thành công  
        $_SESSION['username'] = $username; // Lưu thông tin vào phiên  

        // header("Location: /index.php"); // Chuyển hướng đến trang index.php  
        // echo "<script>alert(\"Welcome " . $_SESSION['username'] . "\");</script>";
        echo "<script>
        alert('Welcome " . $_SESSION['username'] . "');
        window.location.href = '../index.php';
        </script>";
        exit(); // Kết thúc script để đảm bảo không có mã nào chạy sau header  
    } else {
        // Đăng nhập thất bại  
        echo "<script>alert('Tên đăng nhập hoặc mật khẩu sai.'); window.history.back();</script>";
    }
}
