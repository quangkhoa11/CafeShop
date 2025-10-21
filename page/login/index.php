<title>Đăng nhập</title>
<?php
$obj = new database();

if (isset($_POST['btnDangNhap'])) {
    $tk = trim($_POST['tk']);
    $mk = trim($_POST['mk']);

    $user = $obj->dangnhap($tk, $mk);

    if ($user) {
        $_SESSION['dangnhap'] = true;
        $_SESSION['idkh'] = $user['idkh'];
        $_SESSION['username'] = $user['tenkh'];
        $_SESSION['email'] = $user['email'];

        header("Location: index.php?page=home");
        exit;
    } else {
        echo '<script>alert("Email hoặc mật khẩu không đúng!");</script>';
    }
}
?>


<main class="flex-1 flex items-center justify-center py-12">
      <div class="bg-white shadow-xl rounded-2xl p-6 " style="width: 550px">
        <h2 class="text-xl font-bold text-center text-orange-600 mb-6">Đăng nhập</h2>
        
        <form id="loginForm" method="post" enctype="multipart/form-data" class="space-y-4 flex flex-col">
          <input type="email" name="tk" placeholder="Email" required
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