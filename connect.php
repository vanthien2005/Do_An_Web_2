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

?>