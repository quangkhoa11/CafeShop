<?php 
require_once 'mail/sendmail.php';
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

    if (!$tenkh) $errors['tenkh'] = "Vui lòng nhập họ và tên.";
    if (!$sdt) $errors['sdt'] = "Vui lòng nhập số điện thoại.";
    if (!$diachi) $errors['diachi'] = "Vui lòng nhập địa chỉ.";
    if (!$email) $errors['email'] = "Vui lòng nhập email.";
    if (!$matkhau) $errors['matkhau'] = "Vui lòng nhập mật khẩu.";
    if (!$matkhau2) $errors['matkhau2'] = "Vui lòng xác nhận mật khẩu.";

    if ($matkhau && $matkhau2 && $matkhau !== $matkhau2) {
        $errors['matkhau2'] = "Mật khẩu và xác nhận mật khẩu không trùng nhau.";
    }

    if ($email && $obj->xuatdulieu("SELECT idkh FROM khachhang WHERE email='$email'")) {
        $errors['email'] = "Email này đã được đăng ký.";
    }

    if ($sdt && $obj->xuatdulieu("SELECT idkh FROM khachhang WHERE sdt='$sdt'")) {
        $errors['sdt'] = "Số điện thoại này đã được đăng ký.";
    }

    if (empty($errors)) {
        $otp = rand(100000, 999999);
        $_SESSION['register_data'] = [
            'tenkh' => $tenkh,
            'sdt' => $sdt,
            'diachi' => $diachi,
            'email' => $email,
            'matkhau' => $matkhau,
            'otp' => $otp
        ];

        $subject = "Mã xác nhận đăng ký tài khoản CafeShop";
        $body = "
    <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto;'>
        <h2 style='color: #2C3E50;'>📚 Xin chào $tenKH,</h2>
        <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>The Dream</strong></p>

        <p>Để hoàn tất quá trình đăng ký, vui lòng sử dụng mã xác nhận (OTP) bên dưới:</p>
        
        <div style='background: #f4f6f8; padding: 15px 20px; border-radius: 8px; 
                    font-size: 18px; text-align: center; font-weight: bold; color: #2C3E50;
                    letter-spacing: 3px; border: 1px dashed #3498db;'>
            $otp
        </div>

        <p style='margin-top: 15px;'>⏳ <i>Mã OTP có hiệu lực trong <b>5 phút</b>. 
        Vui lòng không chia sẻ mã này với bất kỳ ai để đảm bảo an toàn tài khoản của bạn.</i></p>

        <p>Trân trọng,<br>
        <strong>Đội ngũ The Dream</strong><br>
    </div>
";


        if (sendMail($email, $subject, $body)) {
            header("Location: index.php?page=verify_otp");
            exit;
        } else {
            $errors['email'] = "Không thể gửi email xác nhận. Vui lòng thử lại.";
        }
    }
}
?>

<title>Đăng ký tài khoản</title>

<div class="register-wrapper">
  <div class="register-box">
    <h1>Đăng ký tài khoản</h1>

    <form action="" method="post">
      <label>Họ và tên:</label>
      <input type="text" name="tenkh" value="<?= htmlspecialchars($tenkh ?? '') ?>" required>
      <small class="error-msg"><?= $errors['tenkh'] ?? '' ?></small>

      <label>Số điện thoại:</label>
      <input type="tel" name="sdt" value="<?= htmlspecialchars($sdt ?? '') ?>" required pattern="[0-9]{10}">
      <small class="error-msg"><?= $errors['sdt'] ?? '' ?></small>

      <label>Địa chỉ:</label>
      <input type="text" name="diachi" value="<?= htmlspecialchars($diachi ?? '') ?>" required>
      <small class="error-msg"><?= $errors['diachi'] ?? '' ?></small>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
      <small class="error-msg"><?= $errors['email'] ?? '' ?></small>

      <label>Mật khẩu:</label>
      <input type="password" name="matkhau" required>
      <small class="error-msg"><?= $errors['matkhau'] ?? '' ?></small>

      <label>Xác nhận mật khẩu:</label>
      <input type="password" name="matkhau2" required>
      <small class="error-msg"><?= $errors['matkhau2'] ?? '' ?></small>

      <button type="submit" name="register">Đăng ký</button>
    </form>

    <p>
      Bạn đã có tài khoản? <a href="index.php?page=login">Đăng nhập</a>
    </p>
  </div>
</div>
<link rel="stylesheet" href="assets/css/register.css">
