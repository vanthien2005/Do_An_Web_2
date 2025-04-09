<?php
include("../../connect.php");
include("../../DAO/order_detail_function.php");

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
if ($orderId == 0) {
    die("Không có order_id được gửi.");
}

$order = getOrderById($orderId, $conn);
if (!$order) {
    die("Đơn hàng không tồn tại.");
}

$orderedInfo = getOrderedInfo($orderId, $conn);
$fullAddress = getFullAddress($order['address_id'], $conn);
$orderItems = getOrderItems($orderId, $conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $conn->real_escape_string($_POST['status']);
    $currentStatus = $order['status'];

    if (isValidStatusTransition($currentStatus, $newStatus)) {
        updateOrderStatus($orderId, $newStatus, $conn);
    }

    echo '<form id="redirect" method="POST" action="orderDetail.php"><input type="hidden" name="order_id" value="' . $orderId . '"></form>';
    echo '<script>document.getElementById("redirect").submit();</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style_OrderDetail.css">
    <title>Chi tiết đơn hàng #<?php echo $orderId; ?></title>
</head>

<body>
    <main>
        <section>
            <h2>Chi Tiết Đơn Hàng</h2>
            <h1>Danh sách sản phẩm</h1>
            <hr>
            <table>
                <tr class="title">
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Size</th>
                    <th>Đơn giá</th>
                    <th>Tổng</th>
                </tr>
                <?php foreach ($orderItems as $item): ?>
                    <tr class="product">
                        <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="product"></td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo $item['size']; ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', '.') . 'đ'; ?></td>
                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.') . 'đ'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div id="main_infor">
                <h1>Thông tin đơn hàng</h1>
                <hr>
                <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn không?');">
                    <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                    <div class="info">
                        <label for="customer_name">Họ tên</label>
                        <input type="text" value="<?php echo htmlspecialchars($orderedInfo['nameReceiver'] ?? ''); ?>" disabled>
                    </div>
                    <div class="info">
                        <label for="email">Email</label>
                        <input type="text" value="<?php echo htmlspecialchars($orderedInfo['email'] ?? ''); ?>" disabled>
                    </div>
                    <div class="info">
                        <label for="phone">Điện thoại</label>
                        <input type="text" value="<?php echo htmlspecialchars($orderedInfo['numberPhone'] ?? ''); ?>" disabled>
                    </div>
                    <div class="info">
                        <label for="address">Địa chỉ</label>
                        <input type="text" value="<?php echo htmlspecialchars($fullAddress); ?>" disabled>
                    </div>
                    <div class="info">
                        <label for="purchase_date">Ngày mua</label>
                        <input type="text" value="<?php echo htmlspecialchars($order['creDate']); ?>" disabled>
                    </div>
                    <div class="option">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status">
                            <option value="" disabled>Chọn trạng thái</option>
                            <?php
                            $nextStatuses = getNextValidStatuses($order['status']);
                            $allStatuses = ['Chưa xác nhận', 'Đã xác nhận', 'Đã giao thành công', 'Đã hủy đơn'];
                            foreach ($allStatuses as $statusOption) {
                                if ($statusOption === $order['status'] || in_array($statusOption, $nextStatuses)) {
                                    echo "<option value=\"$statusOption\" " . ($order['status'] === $statusOption ? 'selected' : '') . ">$statusOption</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="buttons">
                        <form method="POST" action="DonHang.php" style="display: inline;">
                            <a href="DonHang.php"><i class="fa-solid fa-x"></i> Đóng</a>
                        </form>
                        <button type="submit" style="
                            background-color:rgb(6, 243, 22); /* Màu xanh */
                            color: white;
                            border: none;
                            padding: 10px 15px;
                            font-size: 14px;
                            border-radius: 5px;
                            cursor: pointer;
                            align-items: center;
                            gap: 5px;
                            transition: background-color 0.3s ease, transform 0.2s;">
                            <i class="fa-solid fa-download"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>