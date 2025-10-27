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

<title>Đăng nhập</title>

<div class="form-wrapper">
  <div class="form-box">
    <h1>Đăng nhập</h1>

    <form method="post" class="form-content">
      <input type="email" name="tk" placeholder="Email" required>
      <input type="password" name="mk" placeholder="Mật khẩu" required>

      <button type="submit" name="btnDangNhap">Đăng nhập</button>
    </form>

    <div class="form-links">
      <a href="index.php?page=forgot">Quên mật khẩu?</a>
    </div>
  </div>
</div>
<link rel="stylesheet" href="assets/css/login.css">