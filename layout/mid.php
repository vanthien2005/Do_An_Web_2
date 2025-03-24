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
        case 'KhachHang':
            include "layout/component/KhachHang.php";
            break;
        default:
            include "layout/component/home.php";
            break;
    }
    ?>
</div>