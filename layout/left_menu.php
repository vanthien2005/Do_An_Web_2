<div class="left-menu">
    <div class="heading">
        <div
            style="
              display: flex;
              flex-direction: row;
              justify-content: center;
              width: 100%;
            ">
            <span style="width: 95%">GE STORE</span>
            <span
                style="
                display: flex;
                flex-direction: row;
                justify-content: right;
                width: 5%;
              "><i
                    class="fa-solid fa-chevron-left icon-hover"
                    onclick="closeLeftMenu()"></i></span>
        </div>
    </div>
    <hr />
    <ul class="content-left-menu">
        <a href="index.php?page=home">
            <li class="icon-hover">
                <i class="fa-solid fa-landmark"></i><span class="menu-text">Tổng quan</span>
            </li>
        </a>

        <a href="index.php?page=KhachHang">
            <li class="icon-hover">
            <i class="fa-solid fa-users"></i><span class="menu-text">Người Dùng</span>
            </li>
        </a>

        <a href="index.php?page=DonHang">
            <li class="icon-hover">
            <i class="fa-solid fa-receipt"></i><span class="menu-text">Đơn Hàng</span>
            </li>
        </a>

        <a href="index.php?page=DoanhThu">
            <li class="icon-hover">
            <i class="fa-solid fa-chart-simple"></i><span class="menu-text">doanh thu</span>
            </li>
        </a>

        <!-- <a href="index.php?page=export_invoice">
            <li class="icon-hover">
                <i class="fa-solid fa-file-export"></i><span class="menu-text">Phiếu xuất kho</span>
            </li>
        </a> -->
    </ul>
    <script>
        function showLeftMenu() {
            let menu = document.getElementsByClassName("left-menu")[0];
            menu.classList.add("open");
        }

        function closeLeftMenu() {
            let menu = document.getElementsByClassName("left-menu")[0];
            menu.classList.remove("open");
        }

        function showContentHeader() {
            let menu = document.getElementsByClassName("content-header")[0];
            menu.classList.add("open");
        }

        function closeContentHeader() {
            let menu = document.getElementsByClassName("content-header")[0];
            menu.classList.remove("open");
        }

        function showDropDownMenu(element) {
            // element.style.display = "block";
            // console.log(element);
            let dd_menu = element.getElementsByClassName("drop-down-menu")[0];
            // console.log(dd_menu);
            dd_menu.style.display =
                dd_menu.style.display === "block" ? "none" : "block";
        }
    </script>
</div>

<script>
    let left_menu = document.querySelectorAll(".content-left-menu li");

    let tongQuan = left_menu[0];
    let sp = left_menu[1];
    let ncc = left_menu[2];
    let pnk = left_menu[3];
    let pxk = left_menu[4];
    console.log("Tổng quan:", tongQuan);
    console.log("Sản phẩm:", sp);
    console.log("Nhà cung cấp:", ncc);
    console.log("Phiếu nhập kho:", pnk);
    console.log("Phiếu xuất kho:", pxk);
    // console.log(document.querySelectorAll(".content-left-menu li"));

    tongQuan.addEventListener("click", function() {
        tongQuan.classList.add("active");
        sp.classList.remove("active");
        ncc.classList.remove("active");
        pxk.classList.remove("active");
        pnk.classList.remove("active");
    });

    sp.addEventListener("click", function() {
        sp.classList.add("active");
        tongQuan.classList.remove("active");
        ncc.classList.remove("active");
        pxk.classList.remove("active");
        pnk.classList.remove("active");
    });

    ncc.addEventListener("click", function() {
        ncc.classList.add("active");
        sp.classList.remove("active");
        tongQuan.classList.remove("active");
        pxk.classList.remove("active");
        pnk.classList.remove("active");
    });

    pxk.addEventListener("click", function() {
        pxk.classList.add("active");
        sp.classList.remove("active");
        ncc.classList.remove("active");
        tongQuan.classList.remove("active");
        pnk.classList.remove("active");
    });

    pnk.addEventListener("click", function() {
        pnk.classList.add("active");
        sp.classList.remove("active");
        ncc.classList.remove("active");
        pxk.classList.remove("active");
        tongQuan.classList.remove("active");
    })

    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    // const page = isset($_GET['page']) ? $_GET['page'] : 'home';
    switch (page) {
        case 'home':
            tongQuan.classList.add('active');
            break;
        case 'product':
            sp.classList.add('active');
            break;
        case 'provider':
            ncc.classList.add('active');
            break;
        case 'import_invoice':
            pnk.classList.add('active');
            break;
        case 'export_invoice':
            pxk.classList.add('active');
            break;
        default:
            tongQuan.classList.add('active'); // Default to 'Tổng quan'
    }
</script>