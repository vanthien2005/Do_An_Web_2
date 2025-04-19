<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách người dùng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link
        rel="stylesheet"
        href="./assets/icons/fontawesome-free-6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="/css/KhachHang.css">
</head>

<body>

    <div class="kh">
        <form id="form_tk" action="/layout/component/KhachHang.php" method="GET">
            <div id="tk" style="display: flex;">
                <input type="text" id="tim_kiem" name="tim_kiem" placeholder="Tìm kiếm theo tên...">
                <button type="submit" style="border: solid 1px; background-color:aquamarine ;  border-radius:5px">
                    <i class="fa-solid fa-magnifying-glass" style="padding-top: 10px;padding-right:10px ; padding-left:10px"></i>
                </button>
            </div>
        </form>
        <div class="icon">
            <i class="fa fa-plus-circle btn-them" style="color: green; padding: 10px; font-size: 20px; cursor: pointer;"></i>
        </div>
    </div>

    <table id="table">
        <tr>
            <th>ID</th>
            <th>UserName</th>
            <th>PassWord</th>
            <th>NumberPhone</th>
            <th>Name</th>
            <th>Level</th>
            <th>Tùy chỉnh</th>
        </tr>

        <?php
        $soLuongMoiTrang = 5;
        $trangHienTai = isset($_GET['current']) ? (int)$_GET['current'] : 1;
        if ($trangHienTai < 1) $trangHienTai = 1;
        $offset = ($trangHienTai - 1) * $soLuongMoiTrang;

        $server = 'localhost';
        $user = 'root';
        $password = '';
        $nameDataBase = 'project_web2';
        $conn = new mysqli($server, $user, $password, $nameDataBase);

        if ($conn->connect_error) {
            die('Lỗi kết nối: ' . $conn->connect_error);
        }

        $conn->set_charset("utf8");

        $search = "";
        if (isset($_GET['tim_kiem'])) {
            $search = trim($_GET['tim_kiem']);
        }

        $sql = "SELECT * FROM accounts WHERE status = 'active' ";
        if ($search !== "") {
            $sql .= " AND name LIKE ? ";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);

        if ($search !== "") {
            $search_param = "%$search%";
            $stmt->bind_param("sii", $search_param, $soLuongMoiTrang, $offset);
        } else {
            $stmt->bind_param("ii", $soLuongMoiTrang, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($num = $result->fetch_assoc()) {
                $statusIcon = ($num['block'] == 1) ? 'fa-unlock' : 'fa-lock';
                $statusColor = ($num['block'] == 1) ? 'green' : 'gray';
                echo '<tr>';
                echo '<td>' . htmlspecialchars($num['id']) . '</td>';
                echo '<td>' . htmlspecialchars($num['userName']) . '</td>';
                echo '<td>' . htmlspecialchars($num['passWord']) . '</td>';
                echo '<td>' . htmlspecialchars($num['numberPhone']) . '</td>';
                echo '<td>' . htmlspecialchars($num['name']) . '</td>';
                echo '<td>' . htmlspecialchars($num['level']) . '</td>';
                echo '<td>
                        <div class="item">
                            <div class="icon">
                                <i class="fa-regular fa-pen-to-square btn-sua btn-option warning-text" 
                                    data-id="' . htmlspecialchars($num['id']) . '" 
                                    data-username="' . htmlspecialchars($num['userName']) . '" 
                                    data-password="' . htmlspecialchars($num['passWord']) . '" 
                                    data-phone="' . htmlspecialchars($num['numberPhone']) . '" 
                                    data-name="' . htmlspecialchars($num['name']) . '" 
                                    data-level="' . htmlspecialchars($num['level']) . '" 
                                     style=background-color: #fff2cf ">
                                </i>
                            </div>
                            <div class="icon">
                                <i class="fa-regular fa-trash-can btn-xoa btn-option wrong" data-id="' . htmlspecialchars($num['id']) . '"
                                style=" background-color: #ffebeb !important;color: #fd6d6d !important;" >
                                </i>
                            </div>
                            <div class="icon">
                                <i class="fa ' . $statusIcon . ' btn-khoa" data-id="' . htmlspecialchars($num['id']) . '" 
                                    data-status="' . htmlspecialchars($num['block']) . '" 
                                    style="color:' . $statusColor . '; margin-top: 10px; cursor: pointer;">
                                </i>
                            </div>
                        </div>
                      </td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">Không tìm thấy dữ liệu</td></tr>';
        }
        ?>
    </table>

    <?php
    // Tính tổng số người dùng
    if ($search !== "") {
        $stmt_total = $conn->prepare("SELECT COUNT(*) AS total FROM accounts WHERE name LIKE ?");
        $search_param = "%$search%";
        $stmt_total->bind_param("s", $search_param);
    } else {
        $stmt_total = $conn->prepare("SELECT COUNT(*) AS total FROM accounts");
    }

    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $row_total = $result_total->fetch_assoc();
    $tongSoNguoiDung = $row_total['total'];
    $stmt_total->close();

    $tongSoTrang = ceil($tongSoNguoiDung / $soLuongMoiTrang);
    $searchQuery = isset($_GET['tim_kiem']) ? '&tim_kiem=' . urlencode($_GET['tim_kiem']) : '';

    echo '<div style="text-align: center; margin-top: 20px;">';
    for ($i = 1; $i <= $tongSoTrang; $i++) {
        echo '<a href="index.php?page=KhachHang&current=' . $i . $searchQuery . '" 
              style="margin: 5px; padding: 8px 12px; border: 1px solid #ccc; text-decoration: none; background: ' 
              . ($i == $trangHienTai ? 'lightblue' : '#fff') . ';">
              ' . $i . '
          </a>';
    }
    echo '</div>';

    $stmt->close();
    $conn->close();
    ?>
    <!-- 
    form thêm -->
    <div id="formThem" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Thêm Người Dùng</h2>
            <form id="themForm" action="" method="POST">
                <label for="userName">UserName:</label>
                <input type="text" id="userName" name="userName" required>
                <br>

                <label for="passWork">PassWord:</label>
                <input type="password" id="passWord" name="passWord" required>
                <br>

                <label for="NumberPhone">NumberPhone:</label>
                <input type="text" id="numberPhone" name="numberPhone" required>
                <br>

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <br>

                <label for="level">Level:</label>
                <select id="level" name="level">
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                    <option value="seller">Seller</option>
                    <option value="inventoryStaff">inventoryStaff</option>
                </select>

                <button type="submit">Thêm</button>
            </form>
        </div>
    </div>
    </div>
    <!-- form sửa  -->
    <div id="formSua" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Sửa Người Dùng</h2>
            <form id="suaForm" action="" method="POST">
                <input type="hidden" id="editID" name="id">

                <label for="editUserName">UserName:</label>
                <input type="text" id="editUserName" name="userName" required>
                <br>

                <label for="editPassWork">PassWord:</label>
                <div style="display: flex; align-items: center; position: relative;">
                    <input type="password" id="editPassWork" name="passWord" required>
                    <i class="fa fa-eye" id="togglePassword"
                        style="position: absolute; right: 10px; cursor: pointer;">
                    </i>
                </div>
                <br>

                <label for="editNumberPhone">NumberPhone:</label>
                <input type="text" id="editNumberPhone" name="numberPhone" required>
                <br>

                <label for="editName">Name:</label>
                <input type="text" id="editName" name="name" required>
                <br>

                <label for="editLevel">Level:</label>
                <select id="editLevel" name="level">
                <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                    <option value="seller">Seller</option>
                    <option value="inventoryStaff">inventoryStaff</option>
                </select>
                <button type="submit">Cập Nhật</button>
            </form>
        </div>
    </div>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById("formThem"); // Lấy modal form
        var btns = document.querySelectorAll(".btn-them"); // Lấy tất cả nút "Thêm"
        var closeBtn = document.querySelector(".close"); // Lấy nút đóng

        // Ẩn modal mặc định
        modal.style.display = "none";

        // Gán sự kiện click cho từng nút "Thêm"
        btns.forEach(function(btn) {
            btn.addEventListener("click", function() {
                modal.style.display = "block";
            });
        });

        // Ẩn form khi nhấn nút đóng
        closeBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });

        // Ẩn form khi click ra ngoài
        window.addEventListener("click", function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("themForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của form

            let formData = new FormData(this); // Lấy dữ liệu từ form

            fetch("/DAO/them_nguoi_dung.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Hiển thị thông báo
                    location.reload(); // Tải lại trang sau khi thêm thành công
                })
                .catch(error => console.error("Lỗi:", error));
        });
    });
    ///////////   SỬA NGƯỜI DÙNG
    document.addEventListener("DOMContentLoaded", function() {
        var formSua = document.getElementById("formSua");
        var closeBtn = formSua.querySelector(".close");

/////      khóa người dùng ////
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("btn-khoa")) {
        let iconElement = e.target;
        let userId = iconElement.getAttribute("data-id");

        fetch("/DAO/khoa_nguoi_dung.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded"},
            body: `id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let newStatus = data.newStatus;
                iconElement.setAttribute("data-status", newStatus);

                if (newStatus == 1) {
                    iconElement.classList.remove('fa-lock');
                    iconElement.classList.add('fa-unlock');
                    iconElement.style.color = 'green';
                } else {
                    iconElement.classList.remove('fa-unlock');
                    iconElement.classList.add('fa-lock');
                    iconElement.style.color = 'gray';
                }
            } else {
                alert("Cập nhật trạng thái thất bại: " + data.message);
            }
        })
        .catch(error => console.error('Lỗi:', error));
    }
});


        // Ẩn form mặc định
        formSua.style.display = "none";

        // Khi click vào icon sửa
        document.querySelectorAll(".btn-sua").forEach(function(btn) {
            btn.addEventListener("click", function() {
                // Lấy dữ liệu từ icon
                document.getElementById("editID").value = this.getAttribute("data-id");
                document.getElementById("editUserName").value = this.getAttribute("data-username");
                document.getElementById("editPassWork").value = this.getAttribute("data-password");
                document.getElementById("editNumberPhone").value = this.getAttribute("data-phone");
                document.getElementById("editName").value = this.getAttribute("data-name");
                document.getElementById("editLevel").value = this.getAttribute("data-level");

                // Hiện form sửa
                formSua.style.display = "block";
            });
        });

        // Đóng form khi nhấn nút đóng
        closeBtn.addEventListener("click", function() {
            formSua.style.display = "none";
        });

        // Đóng form khi click ra ngoài
        window.addEventListener("click", function(event) {
            if (event.target == formSua) {
                formSua.style.display = "none";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        var passwordInput = document.getElementById("editPassWork");
        var togglePassword = document.getElementById("togglePassword");

        togglePassword.addEventListener("click", function() {
            // Đổi type giữa password và text
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePassword.classList.remove("fa-eye");
                togglePassword.classList.add("fa-eye-slash"); // Đổi icon thành "ẩn"
            } else {
                passwordInput.type = "password";
                togglePassword.classList.remove("fa-eye-slash");
                togglePassword.classList.add("fa-eye"); // Đổi icon thành "hiện"
            }
        });
    });


    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("suaForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của form

            let formData = new FormData(this); // Lấy dữ liệu từ form

            fetch("/DAO/sua_nguoi_dung.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data); // Hiển thị thông báo cập nhật
                    location.reload(); // Tải lại trang sau khi sửa thành công
                })
                .catch(error => console.error("Lỗi:", error));
        });
    });

    ////////////////    xóa người dùng

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".fa-trash-can").forEach(function(btn) {
            btn.addEventListener("click", function() {
                if (confirm("Bạn có chắc chắn muốn xóa người dùng này?")) {
                    let userId = this.closest("tr").querySelector("td").textContent;

                    fetch("/DAO/xoa_nguoi_dung.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "id=" + encodeURIComponent(userId)
                        })
                        .then(response => response.text())
                        .then(data => {
                            alert(data);
                            location.reload(); // Tải lại trang sau khi xóa
                        })
                        .catch(error => console.error("Lỗi:", error));
                }
            });
        });
    });


    ///////////// tìm kiếm /////////

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("form_tk").addEventListener("submit", function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của form

            let searchValue = document.getElementById("tim_kiem").value.trim(); // Lấy giá trị nhập vào
            let urlParams = new URLSearchParams(window.location.search); // Lấy query hiện tại

            if (searchValue !== "") {
                urlParams.set("tim_kiem", searchValue); // Cập nhật query "tim_kiem"
            } else {
                urlParams.delete("tim_kiem"); // Xóa query nếu rỗng
            }

            window.location.search = urlParams.toString(); // Reload trang với query mới
        });
    });
</script>

</html>