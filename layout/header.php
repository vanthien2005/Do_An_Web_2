<div class="header container-content">
    <div class="heading">
        <i
            class="fa-solid fa-sliders icon-hover"
            onclick="showLeftMenu()"></i>
        <span>Tổng quan</span>
    </div>
    <div class="content-header">
        <div
            class="icon-hover has-drop-down-menu"
            onclick="showDropDownMenu(this)">
            <i class="fa-regular fa-envelope"></i>
            <div class="drop-down-menu"></div>
        </div>
        <div
            class="icon-hover has-drop-down-menu"
            onclick="showDropDownMenu(this)">
            <i class="fa-regular fa-bell"></i>
            <div class="drop-down-menu"></div>
        </div>
        <div class="icon-hover"><i class="fa-solid fa-gear"></i></div>
        <div
            class="icon-hover has-drop-down-menu"
            onclick="showDropDownMenu(this)">
            <i class="fa-solid fa-circle-user"></i>
            <span class="name-user">&nbsp;<?php echo $_SESSION['username'] ?></span>
            <div class="drop-down-menu">
                <div class="uppercase"><?php echo $_SESSION['username'] ?></div>
                <hr />
                <div><a href="">Thông tin tài khoản</a></div>
                <div><a href="/process/logout.php">Đăng xuất</a></div>
            </div>
        </div>
        <div class="icon-hover" onclick="closeContentHeader()">
            <i class="fa-solid fa-chevron-right"></i>
        </div>
    </div>
    <div class="content-header icon-hover" onclick="showContentHeader()">
        <div class="more-info">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </div>
    </div>
</div>