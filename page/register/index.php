<?php
$obj = new database();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $tenkh = trim($_POST['tenkh']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);
    $email = trim($_POST['email']);
    $matkhau = $_POST['matkhau'];
    $matkhau2 = $_POST['matkhau2'];

    if (!$tenkh || !$sdt || !$diachi || !$email || !$matkhau || !$matkhau2) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    }

    if ($matkhau !== $matkhau2) {
        $errors[] = "Mật khẩu và xác nhận mật khẩu không trùng nhau.";
    }

    $check = $obj->xuatdulieu("SELECT idkh FROM khachhang WHERE email='$email'");
    if ($check) {
        $errors[] = "Email này đã được đăng ký.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO khachhang (tenkh, sdt, diachi, email, matkhau) 
                VALUES ('$tenkh', '$sdt', '$diachi', '$email', '$matkhau')";
        $link = new mysqli("localhost","root","","cafeshop");
        if ($link->query($sql) === TRUE) {
            $success = "Đăng ký thành công! <a href='index.php?page=login'>Đăng nhập ngay</a>.";
        } else {
            $errors[] = "Lỗi khi lưu dữ liệu: " . $link->error;
        }
    }
}
?>

  <title>Đăng ký tài khoản</title>

<div class="register-wrapper">
  <div class="register-box">
    <h1>Đăng ký tài khoản</h1>

    <?php if (!empty($errors)): ?>
      <div class="error-msg">
        <?php foreach ($errors as $err) echo "<p>- $err</p>"; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-msg">
        <?= $success ?>
      </div>
    <?php endif; ?>

    <form action="" method="post">
      <label>Họ và tên:</label>
      <input type="text" name="tenkh" required>

      <label>Số điện thoại:</label>
      <input type="tel" name="sdt" required pattern="[0-9]{10}">

      <label>Địa chỉ:</label>
      <input type="text" name="diachi" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Mật khẩu:</label>
      <input type="password" name="matkhau" required>

      <label>Xác nhận mật khẩu:</label>
      <input type="password" name="matkhau2" required>

      <button type="submit" name="register">Đăng ký</button>
    </form>

    <p>
      Bạn đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a>
    </p>
  </div>
</div>

<style>
    .register-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .register-box {
      background-color: #ffffff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    .register-box h1 {
      text-align: center;
      color: #ff6600;
      margin-bottom: 30px;
      font-size: 24px;
    }

    .register-box label {
      display: block;
      font-weight: bold;
      margin-bottom: 6px;
      color: #333;
    }

    .register-box input[type="text"],
    .register-box input[type="email"],
    .register-box input[type="password"],
    .register-box input[type="tel"] {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .register-box input:focus {
      border-color: #ff6600;
      box-shadow: 0 0 5px rgba(255,102,0,0.3);
      outline: none;
    }

    .register-box button {
      width: 100%;
      padding: 12px;
      background-color: #ff6600;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .register-box button:hover {
      background-color: #e65c00;
    }

    .register-box p {
      text-align: center;
      font-size: 14px;
      color: #555;
      margin-top: 20px;
    }

    .register-box p a {
      color: #ff6600;
      text-decoration: none;
      font-weight: bold;
    }

    .error-msg {
      color: red;
      font-size: 13px;
      margin-bottom: 10px;
    }

    .success-msg {
      color: green;
      font-size: 13px;
      margin-bottom: 10px;
    }
  </style>
