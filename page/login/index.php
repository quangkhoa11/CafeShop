<?php
$obj = new database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'khachhang';
    $tk = trim($_POST['tk']);
    $mk = trim($_POST['mk']);

    if ($role === 'khachhang') {
        $user = $obj->dangnhap($tk, $mk);
        if ($user) {
            $_SESSION['dangnhap'] = true;
            $_SESSION['role'] = 'khachhang';
            $_SESSION['idkh'] = $user['idkh'];
            $_SESSION['username'] = $user['tenkh'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.php?page=home");
            exit;
        } else {
            echo '<script>alert("Email hoặc mật khẩu khách hàng không đúng!");</script>';
        }
    } elseif ($role === 'shop') {
        // Đăng nhập shop
        $link = new mysqli("localhost", "root", "", "cafeshop");
        $result = $link->query("SELECT * FROM shop WHERE email='$tk'");
        if ($result && $result->num_rows > 0) {
            $shop = $result->fetch_assoc();
            if (password_verify($mk, $shop['matkhau'])) {
                $_SESSION['dangnhap'] = true;
                $_SESSION['role'] = 'shop';
                $_SESSION['idshop'] = $shop['idshop'];
                $_SESSION['tenshop'] = $shop['tenshop'];
                header("Location: index.php?page=shop_dashboard");
                exit;
            } else {
                echo '<script>alert("Sai mật khẩu Shop!");</script>';
            }
        } else {
            echo '<script>alert("Không tìm thấy tài khoản Shop này!");</script>';
        }
    }
}
?>

<title>Đăng nhập</title>

<div class="form-wrapper">
  <div class="form-box">
    <h1>Đăng nhập</h1>

    <!-- Nút chọn vai trò -->
    <div class="role-toggle">
      <button type="button" id="btnCustomer" class="active">Khách hàng</button>
      <button type="button" id="btnShop">Shop</button>
    </div>

    <form method="post" class="form-content">
      <input type="hidden" name="role" id="role" value="khachhang">
      
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

<script>
  const btnCustomer = document.getElementById('btnCustomer');
  const btnShop = document.getElementById('btnShop');
  const roleInput = document.getElementById('role');

  btnCustomer.addEventListener('click', () => {
    btnCustomer.classList.add('active');
    btnShop.classList.remove('active');
    roleInput.value = 'khachhang';
  });

  btnShop.addEventListener('click', () => {
    btnShop.classList.add('active');
    btnCustomer.classList.remove('active');
    roleInput.value = 'shop';
  });
</script>

<style>
.role-toggle {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 15px;
}

.role-toggle button {
  padding: 8px 16px;
  border: 1px solid #ccc;
  background: #f7f7f7;
  cursor: pointer;
  border-radius: 6px;
  transition: 0.3s;
}

.role-toggle button.active {
  background: #2d89ef;
  color: #fff;
  border-color: #2d89ef;
}
</style>
