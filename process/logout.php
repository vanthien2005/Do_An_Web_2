<?php
session_start(); // Bắt đầu phiên làm việc  
session_destroy(); // Hủy phiên  
header("Location: /layout/register-signin.php"); // Chuyển hướng về trang đăng nhập  
exit();
