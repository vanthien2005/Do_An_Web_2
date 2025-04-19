<?php
$server = 'localhost';
$user = 'root';
$password = '';
$nameDataBase = 'project_web2';
$conn = new mysqli($server, $user, $password, $nameDataBase);
$conn->set_charset("utf8");

$userId = $_POST['userId'];
$from = $_POST['fromDate'] ?? '2000-01-01';
$to = $_POST['toDate'] ?? date('Y-m-d');

$sql = "
SELECT 
    p.name AS product_name,
    do.quantity,
    do.size,
    p.price,
    pm.image_path1,
    (do.quantity * p.price) AS total
FROM orders o
JOIN detailorder do ON o.id = do.order_id
JOIN products p ON do.product_id = p.id
JOIN product_images pm ON pm.product_id = p.id
WHERE o.status = 'đã giao thành công'
AND o.user_id = ?
AND o.creDate BETWEEN ? AND ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $userId, $from, $to);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td><img src="'. htmlspecialchars($row['image_path1']) . '" alt="' . htmlspecialchars($row['product_name']) . '" style="width: 80px; height: auto;justify-content: center; "></td>';
    echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
    echo '<td>' . htmlspecialchars($row['size']) . '</td>';
    echo '<td>' . htmlspecialchars($row['quantity']) . '</td>';
    echo '<td>' . number_format($row['price'], 0, ',', '.') . '₫</td>';
    echo '<td>' . number_format($row['total'], 0, ',', '.') . '₫</td>';
    echo '</tr>';
}
?>
