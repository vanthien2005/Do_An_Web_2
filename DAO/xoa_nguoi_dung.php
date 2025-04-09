<?php
if (isset($_POST['id'])) {
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $nameDataBase = 'web2';
    
    $conn = new mysqli($server, $user, $password, $nameDataBase);
    
    if ($conn->connect_error) {
        die('Lỗi kết nối: ' . $conn->connect_error);
    }
    
    $id = intval($_POST['id']); // Đảm bảo ID là số nguyên để tránh SQL Injection
    
    $sql = "DELETE FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Xóa thành công!";
    } else {
        echo "Lỗi khi xóa: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>