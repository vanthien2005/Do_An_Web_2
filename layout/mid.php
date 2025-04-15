<div class="main-content">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    switch ($page) {
        case 'DoanhThu':
            include "layout/component/DoanhThu.php";
            break;
        case 'DonHang':
            include "layout/component/DonHang.php";
            break;
        case 'orderDetail':
            include "layout/component/orderDetail.php";
            break;
         // Sửa: Thêm case cho update_status để nhúng file update_status.php
        case 'update_status':
            include "DAO/update_status.php";
            break;
        case 'KhachHang':
            include "layout/component/KhachHang.php";
            break;
        default:
            include "layout/component/home.php";
            break;
    }
    ?>
</div>