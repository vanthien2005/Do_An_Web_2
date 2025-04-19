<?php

// Hàm xây dựng câu lệnh WHERE dựa trên các tham số lọc
function buildFilterQuery($city, $district, $specificAddress, $startDate, $endDate, $status)
{
    $conditions = [];

    // Lọc theo thành phố
    if (!empty($city)) {
        $conditions[] = "ua.city = '" . addslashes($city) . "'";
    }

    // Lọc theo quận/huyện
    if (!empty($district)) {
        $conditions[] = "ua.district = '" . addslashes($district) . "'";
    }

    // Lọc theo địa chỉ cụ thể (tìm kiếm gần đúng)
    if (!empty($specificAddress)) {
        $conditions[] = "CONCAT(ua.numberHouse, ' ', ua.streetName, ', ', ua.ward, ', ', ua.district, ', ', ua.city) LIKE '%" . addslashes($specificAddress) . "%'";
    }

    // Lọc theo khoảng thời gian
    if (!empty($startDate) && !empty($endDate)) {
        $conditions[] = "o.creDate BETWEEN '" . addslashes($startDate) . "' AND '" . addslashes($endDate) . "'";
    } elseif (!empty($startDate)) {
        $conditions[] = "o.creDate >= '" . addslashes($startDate) . "'";
    } elseif (!empty($endDate)) {
        $conditions[] = "o.creDate <= '" . addslashes($endDate) . "'";
    }

    // Lọc theo trạng thái
    if ($status !== '') {
        $conditions[] = "o.status = '" . addslashes($status) . "'";
    }

    // Nếu có điều kiện, thêm WHERE vào câu lệnh
    if (!empty($conditions)) {
        return " WHERE " . implode(" AND ", $conditions);
    }
    return "";
}

// Hàm lấy danh sách đơn hàng đã lọc
function getFilteredOrders($conn, $city = "", $district = "", $specificAddress = "", $startDate = "", $endDate = "", $status = "", $limit, $offset)
{
    // Câu lệnh SQL cơ bản
    $baseQuery = "SELECT 
                    o.id AS order_id,
                    u.name AS customer_name,
                    CONCAT(ua.numberHouse, ' ', ua.streetName, ', ', ua.ward, ', ', ua.district, ', ', ua.city) AS address,
                    o.creDate AS purchase_date,
                    o.total AS total,
                    o.status AS status
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  LEFT JOIN user_address ua ON o.user_id = ua.user_id";

    // Thêm điều kiện lọc
    $whereClause = buildFilterQuery($city, $district, $specificAddress, $startDate, $endDate, $status);
    $query = $baseQuery . $whereClause;

    // Thêm phân trang
    $query .= " LIMIT $limit OFFSET $offset";

    // Thực thi truy vấn
    $result = $conn->query($query);
    $orders = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }

    return $orders;
}

// Hàm đếm số đơn hàng sau khi lọc
function countFilteredOrders($conn, $city = "", $district = "", $specificAddress = "", $startDate = "", $endDate = "", $status = "")
{
    $baseQuery = "SELECT COUNT(*) as total 
                  FROM orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  LEFT JOIN user_address ua ON o.user_id = ua.user_id";

    $whereClause = buildFilterQuery($city, $district, $specificAddress, $startDate, $endDate, $status);
    $query = $baseQuery . $whereClause;

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
    return 0;
}

// Hàm hiển thị bảng đơn hàng
function displayOrdersTable($orders)
{
    if (empty($orders)) {
        echo "<tr><td colspan='6'>Không có đơn hàng nào phù hợp.</td></tr>";
    } else {
        // Thêm ánh xạ trạng thái để hiển thị tên trạng thái bằng tiếng Việt
        $statusMap = [
            2 => 'Đã đặt',
            3 => 'Đang giao',
            4 => 'Thành công'
        ];
        foreach ($orders as $order) {
            echo "<tr>";
            // Sửa cách chuyển hướng đến orderDetail.php, thay form bằng link trực tiếp
            echo "<td>";
            echo "<a href='index.php?page=orderDetail&order_id=" . $order['order_id'] . "' style='background-color:rgb(131, 246, 8)!important;
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;'>" . $order['order_id'] . "</a>";
            echo "</td>";
            echo "<td>" . htmlspecialchars($order['customer_name']) . "</td>";
            echo "<td>" . htmlspecialchars($order['address']) . "</td>";
            echo "<td>" . $order['purchase_date'] . "</td>";
            echo "<td>" . number_format($order['total'], 0, ',', '.') . "đ</td>";
            // Hiển thị trạng thái bằng tên tiếng Việt
            echo "<td>" . htmlspecialchars($statusMap[$order['status']] ?? 'Không xác định') . "</td>";
            echo "</tr>";
        }
    }
}

function getCities($conn)
{
    $query = "SELECT DISTINCT city FROM user_address WHERE city IS NOT NULL ORDER BY city ASC";
    $result = $conn->query($query);
    $cities = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cities[] = $row['city'];
        }
    }
    return $cities;
}

// Hàm lấy danh sách quận/huyện từ bảng user_address
function getDistricts($conn)
{
    $query = "SELECT DISTINCT district FROM user_address WHERE district IS NOT NULL ORDER BY district ASC";
    $result = $conn->query($query);
    $districts = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $districts[] = $row['district'];
        }
    }
    return $districts;
}