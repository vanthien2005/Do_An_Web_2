<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/doanhThu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <div id="locTheoNgay">
    <form id="filterForm" method="POST" action="/layout/component/DoanhThu.php">
    <label for="fromDate">Từ ngày:</label>
    <input type="date" id="fromDate" name="fromDate" required>

    <label for="toDate">Đến ngày:</label>
    <input type="date" id="toDate" name="toDate" required>

    <button type="submit">Lọc</button>
</form>
    </div>
    <h2 style="margin-left: 600px; margin-bottom:10px">THỐNG KÊ TRÊN TỪNG SẢN PHẨM</h2>
    <table class="table" id="bangSanPham">
    <th>Ảnh</th>
    <th>Tên sản Phẩm</th>
    <th>Số lượng</th>
    <th>Đã bán</th>

    </table>
    <h2 style="margin-left: 600px; margin-bottom:30px;margin-top: 40px;">THỐNG KÊ TRÊN KHÁCH HÀNG</h2>
    <table class="table" id="bangKhachHang">
        <th>Họ Và Tên</th>
        <th>Số tiền đã mua</th>
        <th>Chi tiết</th>
    </table>


 <div id="overlayChiTiet" onclick="dongChiTiet()"></div>
<div id="chiTietMuaHang">
    <div style="text-align: right;">
        <button onclick="dongChiTiet()" style="font-size: 18px; background: none; border: none; cursor: pointer;">
            ❌
        </button>
    </div>
    <h3 style="text-align: center;">Chi tiết mua hàng của khách</h3>
    <table class="tableDetail" id="bangChiTiet">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Kích cỡ</th>
                <th>Số lượng đã mua</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <!-- Nội dung sẽ được load ở đây -->
        </tbody>
    </table>
</div>

    
  
</body>
<script>

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("filterForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const fromDate = document.getElementById("fromDate").value;
        const toDate = document.getElementById("toDate").value;

        const formData = new FormData();
        formData.append("fromDate", fromDate);
        formData.append("toDate", toDate);

        // Gọi AJAX để lọc bảng sản phẩm
        fetch("/DAO/filter_SanPham.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector("#bangSanPham").innerHTML = `
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản Phẩm</th>
                    <th>Số lượng</th>
                    <th>Đã bán</th>
                </tr>
            ` + html;
        })
        .catch(error => {
            console.error("Lỗi gửi form sản phẩm:", error);
        });

        // Gọi AJAX để lọc bảng khách hàng
        fetch("/DAO/filter_KhachHang.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector("#bangKhachHang").innerHTML = `
                <tr>
                    <th>Họ Và Tên</th>
                    <th>Số tiền đã mua</th>
                    <th>Chi tiết</th>
                </tr>
            ` + html;
        })
        .catch(error => {
            console.error("Lỗi gửi form khách hàng:", error);
        });
    });
});

function xemChiTiet(userId) {
    const fromDate = document.getElementById("fromDate").value;
    const toDate = document.getElementById("toDate").value;

    const formData = new FormData();
    formData.append("userId", userId);
    formData.append("fromDate", fromDate);
    formData.append("toDate", toDate);

    fetch("/DAO/chiTiet.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        document.querySelector("#bangChiTiet tbody").innerHTML = html;
        document.getElementById("chiTietMuaHang").style.display = "block";
        document.getElementById("overlayChiTiet").style.display = "block";
    })
    .catch(error => {
        console.error("Lỗi khi lấy chi tiết mua hàng:", error);
    });
}
function dongChiTiet() {
    document.getElementById("chiTietMuaHang").style.display = "none";
    document.getElementById("overlayChiTiet").style.display = "none";
}
</script>
</html>


