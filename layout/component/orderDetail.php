<?php
include("./connect.php");
include("./DAO/order_detail_function.php");

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
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
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');

    .containerr {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Roboto', sans-serif;
    }

    main {
        position: relative;
        width: 100%;
    }

    section {
        min-height: 100vh;
        margin-bottom: 20px;
        margin-left: 250px;
        padding: 20px 40px;
        width: calc(100% - 250px);
    }

    h1 {
        text-align: center;
        font-weight: 400;
        font-size: 30px;
    }

    h2 {
        margin: 10px 0 20px 0;
    }

    hr {
        margin: 16px 0;
    }

    table {
        margin: 10px auto;
        width: 100%;
        text-align: center;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    th {
        font-size: 20px;
    }

    .product img {
        width: 100px;
        height: 70px;
    }

    .product {
        font-size: 18px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .product td {
        padding: 10px;
    }

    .info label {
        display: inline-block;
        width: 150px;
        text-align: right;
        font-size: 18px;
        margin: 10px 0;
        font-weight: bold;
    }

    .info input[type="text"] {
        font-size: 16px;
        width: 70%;
        height: 40px;
        padding: 10px;
        margin-top: 5px;
        margin-left: 20px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .buttons {
        width: 200px;
        margin: 30px 0 0 175px;
        display: flex;
        gap: 10px;
    }

    .buttons i {
        margin-right: 4px;
    }

    .buttons a {
        color: black;
        border: 1px solid black;
        padding: 5px 10px;
        border-radius: 4px;
        text-decoration: none;
        cursor: pointer;
    }

    .buttons a:hover {
        border: 1px solid red;
        color: red;
    }

    .status-links a {
        margin: 0 5px;
        padding: 5px 10px;
        border-radius: 4px;
        text-decoration: none;
        color: white;
        font-size: 14px;
    }

    .status-links a.confirmed {
        background-color: #007bff;
    }

    .status-links a.delivered {
        background-color: #28a745;
    }

    .status-links a.canceled {
        background-color: #dc3545;
    }

    .status-links a:hover {
        opacity: 0.8;
    }
</style>

<div class="container-content">
    <div class="containerr">
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
            <?php 
            $total = 0;
            foreach ($orderItems as $item): 
                $total += $item['price'] * $item['quantity'];
            ?>
                <tr class="product">
                    <td><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="product"></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo htmlspecialchars($item['size']); ?></td>
                    <td><?php echo number_format($item['price'], 0, ',', '.') . 'đ'; ?></td>
                    <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.') . 'đ'; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div style="padding-left: 20px">
            <h2>Tổng cộng: <?php echo number_format($total, 0, ',', '.') . 'đ'; ?></h2>
        </div>

        <div id="main_infor">
            <h1>Thông tin đơn hàng</h1>
            <hr>
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
            <div class="info">
                <label for="status">Trạng thái</label>
                <!-- Sửa: Thay form POST bằng các liên kết để cập nhật trạng thái -->
                <span>
                    <?php 
                    switch ($order['status']) {
                        case 'Chưa xác nhận':
                            echo '<span style="color: #ffc107;">Chưa xác nhận</span>';
                            break;
                        case 'Đã xác nhận':
                            echo '<span style="color: #007bff;">Đã xác nhận</span>';
                            break;
                        case 'Đã giao thành công':
                            echo '<span style="color: #28a745;">Đã giao thành công</span>';
                            break;
                        case 'Đã hủy đơn':
                            echo '<span style="color: #dc3545;">Đã hủy đơn</span>';
                            break;
                    }
                    ?>
                </span>
            </div>
            <div class="info">
                <label>Cập nhật trạng thái</label>
                <div class="status-links">
                    <?php 
                    $nextStatuses = getNextValidStatuses($order['status']);
                    foreach ($nextStatuses as $status) {
                        echo "<a href='index.php?page=update_status&order=" . urlencode($status) . "&id=$orderId' class='";
                        switch ($status) {
                            case 'Đã xác nhận':
                                echo "confirmed'>Xác nhận";
                                break;
                            case 'Đã giao thành công':
                                echo "delivered'>Giao thành công";
                                break;
                            case 'Đã hủy đơn':
                                echo "canceled'>Hủy đơn";
                                break;
                        }
                        echo "</a>";
                    }
                    ?>
                </div>
            </div>
            <div class="buttons">
                <a href="index.php?page=DonHang"><i class="fa-solid fa-x"></i> Đóng</a>
            </div>
        </div>
    </div>
</div>