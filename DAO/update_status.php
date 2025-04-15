<?php
include("./connect.php");
include("./DAO/order_detail_function.php");
if (isset($_GET['order']) && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $newStatus = $_GET['order'];

    // Kiểm tra trạng thái hợp lệ
    $order = getOrderById($orderId, $conn);
    if ($order && isValidStatusTransition($order['status'], $newStatus)) {
        updateOrderStatus($orderId, $newStatus, $conn);
    }

    // Chuyển hướng về trang chi tiết đơn hàng
    echo "<script>window.history.back();</script>";
    exit();
} else {
    header("Location: index.php?page=DonHang");
    exit();
}
