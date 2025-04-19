<?php
$server = 'localhost';
$user = 'root';
$password = '';
$nameDataBase = 'project_web2';
$conn = new mysqli($server, $user, $password, $nameDataBase);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL']));
}

$conn->set_charset("utf8");

// Gán mặc định nếu chưa có POST
$from = isset($_POST['fromDate']) ? $_POST['fromDate'] : '2000-01-01';
$to = isset($_POST['toDate']) ? $_POST['toDate'] : date('Y-m-d');

// SQL truy vấn
$sql = "
SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.brand,
    p.price,
    pm.image_path1,
    SUM(do.quantity) AS total_quantity_sold
FROM orders o
JOIN detailorder do ON o.id = do.order_id
JOIN products p ON do.product_id = p.id
JOIN product_images pm ON pm.product_id = p.id
WHERE o.status = 'đã giao thành công'
AND o.creDate BETWEEN ? AND ?
GROUP BY p.id, p.name, p.brand, p.price
ORDER BY  total_quantity_sold DESC
";

$ptm = $conn->prepare($sql);
$ptm->bind_param("ss", $from, $to); // Sử dụng prepared statement an toàn hơn
$ptm->execute();
$results = $ptm->get_result();
$tongDoanhThu = 0;
ob_start();
if ($results->num_rows > 0) {
    while ($num = $results->fetch_assoc()) {
        $total = $num['total_quantity_sold'] * $num['price'];
        $tongDoanhThu += $total;
        echo '<tr>';
        echo '<td><img src="'. htmlspecialchars($num['image_path1']) . '" alt="' . htmlspecialchars($num['product_name']) . '" style="width: 80px; height: auto;justify-content: center; "></td>';
        echo '<td>' . htmlspecialchars($num['product_name']) . '</td>';
        echo '<td>' . htmlspecialchars($num['total_quantity_sold']) . '</td>';
        echo '<td>' . number_format($total, 0, ',', '.') . '₫</td>';
        echo '</tr>';
    }
    echo '<tr style="font-weight: bold; background-color: #f0f0f0;">';
    echo '<td colspan="3" style="text-align:right;">Tổng doanh thu:</td>';
    echo '<td>' . number_format($tongDoanhThu, 0, ',', '.') . '₫</td>';
    echo '</tr>';
} else {
    echo '<tr><td colspan="4">Không có dữ liệu trong khoảng ngày đã chọn</td></tr>';
}
echo ob_get_clean();
?>

