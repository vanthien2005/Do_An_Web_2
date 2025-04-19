<?php
$server = 'localhost';
$user = 'root';
$password = '';
$nameDataBase = 'project_web2';
$conn = new mysqli($server, $user, $password, $nameDataBase);
$conn->set_charset("utf8");

$from = isset($_POST['fromDate']) ? $_POST['fromDate'] : '2000-01-01';
$to = isset($_POST['toDate']) ? $_POST['toDate'] : date('Y-m-d');

$sql = "
    SELECT 
        u.id AS user_id,
        u.name AS nameUser,
        SUM(do.quantity) AS total_quantity_sold,
        SUM(do.quantity * p.price) AS total_revenue
    FROM orders o
    JOIN users u ON u.id = o.user_id
    JOIN detailorder do ON o.id = do.order_id
    JOIN products p ON do.product_id = p.id
    WHERE o.status = 'đã giao thành công'
    AND o.creDate BETWEEN ? AND ?
    GROUP BY u.id, u.name
    ORDER BY total_revenue DESC
";

$ptm = $conn->prepare($sql);
$ptm->bind_param("ss", $from, $to);
$ptm->execute();
$results = $ptm->get_result();
$tongDoanhThu = 0;

ob_start(); // Bắt đầu buffer

if ($results->num_rows > 0) {
    while ($num = $results->fetch_assoc()) {
        $tongDoanhThu += $num['total_revenue'];
        echo '<tr>';
        echo '<td>' . htmlspecialchars($num['nameUser']) . '</td>';
        echo '<td>' . number_format($num['total_revenue'], 0, ',', '.') . '₫</td>';
        echo '<td><i class="fas fa-eye" style="cursor:pointer" onclick="xemChiTiet('.$num['user_id'].')"></i></td>';

        echo '</tr>';
    }
    echo '<tr style="font-weight: bold; background-color: #f0f0f0;">';
    echo '<td colspan="2" style="text-align:right;">Tổng doanh thu:</td>';
    echo '<td>' . number_format($tongDoanhThu, 0, ',', '.') . '₫</td>';
    echo '</tr>';
} else {
    echo '<tr><td colspan="3">Không có dữ liệu trong khoảng ngày đã chọn</td></tr>';
}

echo ob_get_clean();
?>
