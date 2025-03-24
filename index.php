<?php
session_start(); // Bắt đầu phiên làm việc  

// Kiểm tra xem người dùng đã đăng nhập hay chưa  
if (!isset($_SESSION['username'])) {
    header("Location: log_in.php"); // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập  
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link
        rel="stylesheet"
        href="/assets/icons/fontawesome-free-6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <?php include "layout/header.php"; ?>
        <?php include "layout/left_menu.php"; ?>
        <?php include "layout/mid.php"; ?>
    </div>
</body>

</html>