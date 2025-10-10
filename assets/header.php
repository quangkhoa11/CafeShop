<!doctype html>
<html lang="vi">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="./src/output.css" />
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
                if (isset($_SESSION['dangnhap'])) {
                echo '<div class="text-orange-600"> <b>Chào, </b>'. $_SESSION['username'].'</div>';
                echo '<a href="index.php?page=dangxuat" class="text-orange-500 font-semibold pr-3">Đăng xuất</a>';
            } else {
             echo '<a href="index.php?page=login" class="text-orange-500 font-semibold pr-3">Đăng nhập</a>';
            }
            ?>
            </nav>
          </div>
        </div>
      </header>
      
