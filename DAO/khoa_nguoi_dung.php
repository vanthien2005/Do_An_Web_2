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

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Lấy trạng thái hiện tại
    $query = "SELECT block FROM accounts WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Người dùng không tồn tại']);
        exit;
    }

    // Đảo trạng thái
    $newStatus = ($user['block'] == 1) ? 0 : 1;

    // Cập nhật trạng thái trong database
    $updateQuery = "UPDATE accounts SET block = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $newStatus, $id);
    $updateSuccess = $updateStmt->execute();

    // Trả về kết quả dưới dạng JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $updateSuccess,
        'newStatus' => $newStatus
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Thiếu tham số ID']);
?>
