<title>Đăng nhập</title>
<?php
    $obj=new database();
    if(isset($_POST['btnDangNhap'])){
        $tk=$_POST['tk'];
        $mk=$_POST['mk'];

        $tkmk = $obj->dangnhap($tk, $mk);
        if ($tkmk) {
            $_SESSION['dangnhap'] = $tkmk;
            $_SESSION['username'] = $tk;
            header("location:index.php?page=home");
            exit;
        }
        echo '<script>alert("Đăng nhập không thành công!");</script>';
    }
?>

<main class="flex-1 flex items-center justify-center py-12">
      <div class="w-full max-w-sm bg-white shadow-xl rounded-2xl p-6">
        <h2 class="text-xl font-bold text-center text-orange-600 mb-6">Đăng nhập</h2>
        
        <form id="loginForm" method="post" enctype="multipart/form-data" class="space-y-4 flex flex-col">
          <input type="text" name="tk" placeholder="Số điện thoại" required
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 border-gray-300 placeholder-gray-400 text-sm">
          
          <input type="password" name="mk" placeholder="Mật khẩu" required
            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 border-gray-300 placeholder-gray-400 text-sm">
          
          <button type="submit" name="btnDangNhap" class="w-full bg-orange-500 text-white font-semibold py-2 rounded-lg hover:bg-orange-600 transition shadow-md hover:shadow-lg text-sm">
            Đăng nhập
          </button>
        </form>

        <div class="flex flex-col items-center gap-2 mt-5 text-xs text-gray-600">
          <a href="index.php?page=forgot" class="hover:text-orange-500">Quên mật khẩu?</a>
        </div>
      </div>
    </main>