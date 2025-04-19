<?php
include("./connect.php");
include("./DAO/LocDonHang.php");

// Lấy tham số lọc từ query string (GET)
$city = isset($_GET['city']) ? htmlspecialchars($_GET['city']) : '';
$district = isset($_GET['district']) ? htmlspecialchars($_GET['district']) : '';
$specificAddress = isset($_GET['specific-address']) ? htmlspecialchars($_GET['specific-address']) : '';
$startDate = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : '';
$endDate = isset($_GET['end']) ? htmlspecialchars($_GET['end']) : '';
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$current_page = isset($_GET['current']) ? (int)$_GET['current'] : 1;

// Phân trang
$limit = 5;
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

// Tạo query string cơ bản cho phân trang
$baseQuery = http_build_query([
    'page' => 'DonHang',
    'city' => $city,
    'district' => $district,
    'specific-address' => $specificAddress,
    'start' => $startDate,
    'end' => $endDate,
    'status' => $status
]);
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap');

    .containerr {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Roboto', sans-serif;
    }

    .filter {
        margin: 10px 0;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
    }

    .date {
        display: flex;
        align-items: center;
    }

    .date input {
        margin: 0 10px;
        font-size: 16px;
        padding: 3px;
    }

    .date span {
        margin-right: 10px;
    }

    .show {
        width: 100px;
        padding: 6px 7px;
        text-align: center;
        background-color: #333;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .show:hover {
        background-color: #DCDCDC;
        color: #333;
    }

    table {
        width: 100%;
        text-align: center;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    th {
        font-size: 20px;
    }



    .state {
        font-weight: bold;
    }

    .order-row {
        font-size: 20px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        border-bottom: 1px solid #ccc;
        transition: background-color 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .order-row:hover {
        background-color: #f5f5f5;
        box-shadow: 0 6px 12px 0 rgba(0, 0, 0, 0.3);
        transform: scale(1.02);
    }

    .order-row:hover td {
        color: #007bff;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
        gap: 5px;
    }

    .pagination a {
        background-color: rgb(119, 129, 124);
        border: 1px solid #ddd;
        padding: 8px 12px;
        font-size: 14px;
        color: #fff;
        text-decoration: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s, color 0.3s;
    }

    .pagination a:hover {
        background-color: rgb(1, 255, 90);
        color: #000;
    }

    .pagination a.active {
        background-color: rgb(1, 255, 90);
        color: #fff;
        border-color: rgb(1, 255, 90);
    }

    .pagination span {
        padding: 8px 12px;
        font-size: 14px;
        color: #666;
    }

    @media (max-width: 768px) {
        .filter {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .filter>div,
        .filter .date {
            width: 100%;
        }

        .filter label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .filter input[type="text"],
        .filter select,
        .filter input[type="date"] {
            width: 100%;
            box-sizing: border-box;
            padding: 5px;
        }

        .filter .date {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter .date span {
            margin-right: 0;
            margin-top: 10px;
        }

        .show {
            width: 100%;
        }
    }
</style>

<div class="container-content">
    <div class="containerr">
        <h2>Đơn Hàng</h2>
        <form method="GET" action="index.php" class="filter">
            <input type="hidden" name="page" value="DonHang">
            <div>
                <label for="city">Thành phố</label>
                <select id="city" name="city">
                    <option value="" <?php echo $city == '' ? 'selected' : ''; ?>>Chọn</option>
                    <?php foreach ($cities as $cityOption): ?>
                        <option value="<?php echo htmlspecialchars($cityOption); ?>" <?php echo $city == $cityOption ? 'selected' : ''; ?>><?php echo htmlspecialchars($cityOption); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="district">Quận/Huyện</label>
                <select id="district" name="district">
                    <option value="" <?php echo $district == '' ? 'selected' : ''; ?>>Chọn</option>
                    <?php foreach ($districts as $districtOption): ?>
                        <option value="<?php echo htmlspecialchars($districtOption); ?>" <?php echo $district == $districtOption ? 'selected' : ''; ?>><?php echo htmlspecialchars($districtOption); ?></option>
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
                <!-- Sửa: Cập nhật các trạng thái khớp với cơ sở dữ liệu -->
                <select id="status" name="status">
                    <option value="" <?php echo $status == '' ? 'selected' : ''; ?>>Tất cả trạng thái</option>
                    <option value="Chưa xác nhận" <?php echo $status == 'Chưa xác nhận' ? 'selected' : ''; ?>>Chưa xác nhận</option>
                    <option value="Đã xác nhận" <?php echo $status == 'Đã xác nhận' ? 'selected' : ''; ?>>Đã xác nhận</option>
                    <option value="Đã giao thành công" <?php echo $status == 'Đã giao thành công' ? 'selected' : ''; ?>>Đã giao thành công</option>
                    <option value="Đã hủy đơn" <?php echo $status == 'Đã hủy đơn' ? 'selected' : ''; ?>>Đã hủy đơn</option>
                </select>
            </div>
            <button type="submit" class="show">Lọc</button>
        </form>
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
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="index.php?<?php echo $baseQuery; ?>&current=<?php echo $current_page - 1; ?>">
                    < </a>
                    <?php endif; ?>
                    <?php
                    $range = 2;
                    $start = max(1, $current_page - $range);
                    $end = min($tongtrang, $current_page + $range);
                    ?>
                    <?php if ($start > 1): ?>
                        <a href="index.php?<?php echo $baseQuery; ?>&current=1">1</a>
                        <?php if ($start > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <a href="index.php?<?php echo $baseQuery; ?>&current=<?php echo $i; ?>" class="<?php echo $current_page == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($end < $tongtrang): ?>
                        <?php if ($end < $tongtrang - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="index.php?<?php echo $baseQuery; ?>&current=<?php echo $tongtrang; ?>"><?php echo $tongtrang; ?></a>
                    <?php endif; ?>
                    <?php if ($current_page < $tongtrang): ?>
                        <a href="index.php?<?php echo $baseQuery; ?>&current=<?php echo $current_page + 1; ?>"> > </a>
                    <?php endif; ?>
        </div>
    </div>
</div>