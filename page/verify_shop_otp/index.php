<?php
require_once 'mail/sendmail.php';
$obj = new database();
$errors = [];
$success = '';

if (!isset($_SESSION['register_shop'])) {
    header("Location: index.php?page=register_shop");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $otp_input = trim($_POST['otp']);
    $otp_saved = $_SESSION['register_shop']['otp'];

    if ($otp_input == $otp_saved) {
        $data = $_SESSION['register_shop'];
        $tenshop = $data['tenshop'];
        $sdt = $data['sdt'];
        $diachi = $data['diachi'];
        $email = $data['email'];
        $matkhau = $data['matkhau'];
        $logo = $data['logo'];

        // Kết nối CSDL
        $link = new mysqli("localhost", "root", "", "cafeshop");
        if ($link->connect_error) {
            die("Kết nối thất bại: " . $link->connect_error);
        }

        // Kiểm tra trùng số điện thoại
        $check = $link->query("SELECT idshop FROM shop WHERE sdt='$sdt'");
        if ($check && $check->num_rows > 0) {
            $errors[] = "Số điện thoại này đã được đăng ký!";
        } else {
            $sql = "INSERT INTO shop (tenshop, sdt, diachi, email, matkhau, logo)
                    VALUES ('$tenshop', '$sdt', '$diachi', '$email', '$matkhau', '$logo')";
            if ($link->query($sql) === TRUE) {
                $success = "✅ Xác minh thành công! Shop của bạn đã được đăng ký.<br>
                            <a href='index.php?page=login'>Đăng nhập ngay</a>";
                unset($_SESSION['register_shop']);
            } else {
                $errors[] = "Lỗi khi lưu dữ liệu: " . $link->error;
            }
        }
    } else {
        $errors[] = "Mã OTP không chính xác!";
    }
}
?>

<title>Xác minh OTP Shop</title>

<div class="otp-wrapper">
  <div class="otp-box">
    <h1>Xác minh mã OTP</h1>

    <?php if (!empty($errors)): ?>
      <div class="error-msg">
        <?php foreach ($errors as $err) echo "<p>- $err</p>"; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-msg"><?= $success ?></div>
    <?php else: ?>
      <form method="post">
        <label>Nhập mã OTP đã gửi đến email:</label>
        <input type="text" name="otp" placeholder="Nhập 6 chữ số" required maxlength="6" pattern="\d{6}">
        <button type="submit" name="verify">Xác nhận</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<link rel="stylesheet" href="assets/css/otp.css">
