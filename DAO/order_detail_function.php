<?php

function getNextValidStatuses($currentStatus)
{
    $validTransitions = [
        'Chưa xác nhận' => ['Đã xác nhận', 'Đã hủy đơn'],
        'Đã xác nhận' => ['Đã giao thành công', 'Đã hủy đơn'],
        'Đã giao thành công' => [], // Không có trạng thái tiếp theo
        'Đã hủy đơn' => [] // Không có trạng thái tiếp theo
    ];

    return isset($validTransitions[$currentStatus]) ? $validTransitions[$currentStatus] : [];
}

function isValidStatusTransition($currentStatus, $newStatus)
{
    $nextStatuses = getNextValidStatuses($currentStatus);
    return in_array($newStatus, $nextStatuses);
}

function getOrderById($orderId, $conn)
{
    $query = "SELECT * FROM orders WHERE id = '$orderId'";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

function getOrderedInfo($orderId, $conn)
{
    $query = "SELECT nameReceiver, email, numberPhone FROM orderedinfo WHERE id = '" . $conn->real_escape_string($orderId) . "'";
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

function getFullAddress($addressId, $conn)
{
    $query = "SELECT numberHouse, streetName, ward, district, city FROM user_address WHERE id = '" . $conn->real_escape_string($addressId) . "'";
    $result = $conn->query($query);
    $address = $result->fetch_assoc();
    return $address ? implode(", ", [$address['numberHouse'], $address['streetName'], $address['ward'], $address['district'], $address['city']]) : 'Không có địa chỉ';
}

/*function getOrderItems($orderId, $conn) hiển thị 5 ảnh
{
    $query = "SELECT d.quantity, p.id AS product_id, p.name, p.price, 
                     pi.image_path1, pi.image_path2, pi.image_path3, pi.image_path4, pi.image_path5
              FROM detailorder d 
              JOIN products p ON d.product_id = p.id 
              LEFT JOIN product_images pi ON p.id = pi.product_id 
              WHERE d.order_id = '$orderId'";
    
    $result = $conn->query($query);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        // Đưa tất cả ảnh vào một mảng để dễ xử lý
        $row['images'] = array_filter([
            $row['image_path1'],
            $row['image_path2'],
            $row['image_path3'],
            $row['image_path4'],
            $row['image_path5']
        ]); // Lọc bỏ ảnh NULL hoặc rỗng
        $items[] = $row;
    }
    return $items;
}*/

function getOrderItems($orderId, $conn)
{
    $query = "SELECT d.quantity, d.size, p.id AS product_id, p.name, p.price, 
                     COALESCE(pi.image_path1, 'default_image.jpg') AS image
              FROM detailorder d 
              JOIN products p ON d.product_id = p.id 
              LEFT JOIN product_images pi ON p.id = pi.product_id 
              WHERE d.order_id = '$orderId'";
    
    $result = $conn->query($query);
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

function updateOrderStatus($orderId, $newStatus, $conn)
{
    $query = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
    return $conn->query($query);
}
