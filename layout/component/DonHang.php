<?php
// DonHang.php
include("./connect.php");
include("./DAO/LocDonHang.php");

// Bắt đầu session để lưu trữ tham số lọc
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem có phải refresh thủ công không
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_SESSION['post_redirect'])) {
    // Xóa session filter khi refresh thủ công (F5)
    unset($_SESSION['filter']);
}

// Xử lý dữ liệu từ form lọc hoặc phân trang (chỉ khi có POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lưu tham số lọc vào session
    $_SESSION['filter'] = [
        'city' => isset($_POST['city']) ? $_POST['city'] : '',
        'district' => isset($_POST['district']) ? $_POST['district'] : '',
        'specific-address' => isset($_POST['specific-address']) ? $_POST['specific-address'] : '',
        'start' => isset($_POST['start']) ? $_POST['start'] : '',
        'end' => isset($_POST['end']) ? $_POST['end'] : '',
        'status' => isset($_POST['status']) ? $_POST['status'] : '',
        'page' => isset($_POST['page']) ? (int)$_POST['page'] : 1
    ];
    // Đánh dấu đây là chuyển hướng sau POST
    $_SESSION['post_redirect'] = true;
    // Chuyển hướng để tránh resubmit
    header("Location: DonHang.php");
    exit();
}

// Xóa cờ post_redirect sau khi xử lý xong yêu cầu GET từ chuyển hướng
if (isset($_SESSION['post_redirect'])) {
    unset($_SESSION['post_redirect']);
}

// Lấy tham số lọc từ session (nếu không có thì mặc định rỗng)
$filter = isset($_SESSION['filter']) ? $_SESSION['filter'] : [];
$city = $filter['city'] ?? '';
$district = $filter['district'] ?? '';
$specificAddress = $filter['specific-address'] ?? '';
$startDate = $filter['start'] ?? '';
$endDate = $filter['end'] ?? '';
$status = $filter['status'] ?? '';

// Phân trang
$limit = 1;
$current_page = $filter['page'] ?? 1;
$offset = ($current_page - 1) * $limit;

// Lấy danh sách đơn hàng đã lọc
$orders = getFilteredOrders($conn, $city, $district, $specificAddress, $startDate, $endDate, $status, $limit, $offset);

// Đếm tổng số đơn hàng
$tongsp = countFilteredOrders($conn, $city, $district, $specificAddress, $startDate, $endDate, $status);
$tongtrang = ceil($tongsp / $limit);

// Lấy danh sách thành phố và quận/huyện từ database
$cities = getCities($conn);
$districts = getDistricts($conn);

// Lấy ngày hiện tại để giới hạn
$currentDate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/1acf2d22a5.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/style_OrderAdmin.css">
    <style>
        /* CSS nhúng trực tiếp cho phân trang */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 5px;
        }

        .pagination button {
            background-color: rgb(119, 129, 124) !important;
            border: 1px solid #ddd;
            padding: 8px 12px;
            font-size: 14px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination button:hover {
            background-color: rgb(1, 255, 90);
            color: #000;
        }

        .pagination button.active {
            background-color:rgb(1, 255, 90);
            color: #fff;
            border-color: rgb(1, 255, 90);
        }

        .pagination span {
            padding: 8px 12px;
            font-size: 14px;
            color: #666;
        }
    </style>
    <title>Đơn Hàng</title>
</head>

<body>
    <main>
        <section>
            <h2>Đơn Hàng</h2>
            <!-- Bộ lọc -->
            <form method="POST" action="DonHang.php" class="filter">
                <div>
                    <label for="city">Thành phố</label>
                    <select id="city" name="city">
                        <option value="" <?php echo $city == '' ? 'selected' : ''; ?>>Chọn</option>
                        <?php foreach ($cities as $cityOption): ?>
                            <option value="<?php echo $cityOption; ?>" <?php echo $city == $cityOption ? 'selected' : ''; ?>><?php echo $cityOption; ?></option>
                            <?php endforeach; ?>
                        </select>
                </div>
                <div>
                    <label for="district">Quận/Huyện</label>
                    <select id="district" name="district">
                        <option value="" <?php echo $district == '' ? 'selected' : ''; ?>>Chọn</option>
                        <?php foreach ($districts as $districtOption): ?>
                            <option value="<?php echo $districtOption; ?>" <?php echo $district == $districtOption ? 'selected' : ''; ?>><?php echo $districtOption; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="specific-address">Địa chỉ cụ thể</label>
                        <input type="text" id="specific-address" name="specific-address" value="<?php echo htmlspecialchars($specificAddress); ?>" placeholder="Nhập địa chỉ">
                    </div>
                    <div class="date">
                        <span>Từ:</span>
                        <input type="date" id="start" name="start" value="<?php echo htmlspecialchars($startDate); ?>" max="<?php echo $currentDate; ?>">
                        <span>Đến:</span>
                        <input type="date" id="end" name="end" value="<?php echo htmlspecialchars($endDate); ?>" max="<?php echo $currentDate; ?>">
                    </div>
                    <div>
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status">
                            <option value="" <?php echo $status == '' ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                            <option value="Đã xác nhận" <?php echo $status == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                            <option value="Chưa xác nhận" <?php echo $status == 'Chưa xác nhận' ? 'selected' : ''; ?>>Chưa xác nhận</option>
                            <option value="Đã giao thành công" <?php echo $status == 'Đã giao thành công' ? 'selected' : ''; ?>>Đã giao thành công</option>
                            <option value="Đã hủy đơn" <?php echo $status == 'Đã hủy đơn' ? 'selected' : ''; ?>>Đã hủy đơn</option>
                        </select>
                    </div>
                    <input type="hidden" name="page" value="1">
                    <button type="submit" class="show">Lọc</button>
                </form>
                
                <!-- Danh sách đơn hàng -->
                <hr>
                <table>
                <tr class="title">
                    <th>Mã đơn hàng</th>
                    <th>Khách Hàng</th>
                    <th>Địa chỉ</th>
                    <th>Ngày mua</th>
                    <th>Thành tiền</th>
                    <th>Trạng thái</th>
                </tr>
                <?php displayOrdersTable($orders); ?>
            </table>

            <!-- Phân trang -->
            <div class="pagination">
                <form method="POST" action="DonHang.php" style="display: inline;">
                    <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                    <input type="hidden" name="district" value="<?php echo htmlspecialchars($district); ?>">
                    <input type="hidden" name="specific-address" value="<?php echo htmlspecialchars($specificAddress); ?>">
                    <input type="hidden" name="start" value="<?php echo htmlspecialchars($startDate); ?>">
                    <input type="hidden" name="end" value="<?php echo htmlspecialchars($endDate); ?>">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">

                    <?php
                    $range = 2;
                    $start = max(1, $current_page - $range);
                    $end = min($tongtrang, $current_page + $range);
                    ?>

                    <?php if ($current_page > 1): ?>
                        <button type="submit" name="page" value="<?php echo $current_page - 1; ?>">
                            <</button>
                            <?php endif; ?>

                            <?php if ($start > 1): ?>
                                <button type="submit" name="page" value="1">1</button>
                                <?php if ($start > 2): ?>
                                    <span>...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <button type="submit" name="page" value="<?php echo $i; ?>" class="<?php echo $current_page == $i ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </button>
                            <?php endfor; ?>

                            <?php if ($end < $tongtrang): ?>
                                <?php if ($end < $tongtrang - 1): ?>
                                    <span>...</span>
                                <?php endif; ?>
                                <button type="submit" name="page" value="<?php echo $tongtrang; ?>"><?php echo $tongtrang; ?></button>
                            <?php endif; ?>

                            <?php if ($current_page < $tongtrang): ?>
                                <button type="submit" name="page" value="<?php echo $current_page + 1; ?>">></button>
                            <?php endif; ?>
                </form>
            </div>
        </section>
    </main>
</body>

</html>