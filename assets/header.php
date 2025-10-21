<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./src/output.css" />
    <link rel="stylesheet" href="assets/css/header.css">
  </head>
  <body class="bg-amber-100 text-gray-800">
    <div class="min-h-screen flex flex-col">
      <header class="bg-orange-50 shadow-sm">
        <div class="container mx-auto px-4">
          <div class="flex items-center justify-between h-16">
            <a href="index.php?page=home" class="flex items-center gap-3">
              <img src="./assets/images/logo.jpg" class="w-10 h-10 rounded-lg object-cover">
              <span class="font-semibold text-lg">The Dream</span>
            </a>
            <nav class="hidden md:flex items-center gap-6 ">
              <a href="index.php?page=home" class="hover:text-orange-500 font-semibold pr-3">Trang chủ</a>
              <a href="index.php?page=menu" class="hover:text-orange-500 font-semibold pr-3">Menu</a>
              <a href="index.php?page=cart" class="hover:text-orange-500 font-semibold pr-3">Giỏ hàng</a>
              <a href="index.php?page=contact" class="hover:text-orange-500 font-semibold pr-3">Liên hệ</a>
              <?php
                if (isset($_SESSION['idkh'])) {
                    $idkh = $_SESSION['idkh'];
                    $db = new database();
                    $sql = "SELECT tenkh FROM khachhang WHERE idkh = $idkh";
                    $result = $db->xuatdulieu($sql);
                    $tenkh = ($result && count($result) > 0) ? $result[0]['tenkh'] : 'Khách hàng';
                ?>
                    <div class="user-dropdown">
                        <button class="user-btn">
                            <img src="./assets/images/icon.png" alt="user" class="user-icon">
                            <span class="user-name"><?= htmlspecialchars($tenkh) ?></span>
                            <span class="arrow">&#9662;</span>
                        </button>
                        <div class="dropdown-content">
                            <a href="index.php?page=customer">Thông tin cá nhân</a>
                            <a href="index.php?page=order-history">Lịch sử đặt hàng</a>
                            <a href="index.php?page=password">Đổi mật khẩu</a>
                            <a href="index.php?page=dangxuat" class="logout">Đăng xuất</a>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="user-dropdown">
                        <button class="user-btn">
                            <img src="./assets/images/icon_setting.png" alt="user" class="user-icon">
                            <span class="user-name"><?= htmlspecialchars($tenkh) ?></span>
                            <span class="arrow">&#9662;</span>
                        </button>
                        <div class="dropdown-content">
                            <a href="index.php?page=login">Đăng nhập</a>
                            <a href="index.php?page=register">Đăng ký</a>
                        </div>
                    </div>
                <?php
                }
                ?>

            </nav>
          </div>
        </div>
      </header>
      