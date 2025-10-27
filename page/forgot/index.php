<?php 
require_once 'mail/sendmail.php';
$obj = new database();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);

    if (!$email) {
        $errors['email'] = "Vui lòng nhập email.";
    } else {
        $checkEmail = $obj->xuatdulieu("SELECT * FROM khachhang WHERE email='$email'");
        if (!$checkEmail) {
            $errors['email'] = "Email này chưa được đăng ký trong hệ thống.";
        }
    }

    if (empty($errors)) {
        $otp = rand(100000, 999999);
        $_SESSION['forgot_data'] = [
            'email' => $email,
            'otp' => $otp,
            'otp_time' => time()
        ];

        $subject = "Mã xác nhận đặt lại mật khẩu - The Dream";
        $body = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #2C3E50;'>🔑 Xin chào,</h2>
            <p>Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản có email: <strong>$email</strong></p>
            <p>Vui lòng sử dụng mã xác nhận (OTP) bên dưới để tiếp tục:</p>
            <div style='background: #f4f6f8; padding: 15px 20px; border-radius: 8px; 
                        font-size: 18px; text-align: center; font-weight: bold; color: #2C3E50;
                        letter-spacing: 3px; border: 1px dashed #3498db;'>
                $otp
            </div>
            <p style='margin-top: 15px;'>⏳ <i>Mã OTP có hiệu lực trong <b>5 phút</b>.</i></p>
            <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
            <p>Trân trọng,<br><strong>Đội ngũ The Dream</strong></p>
        </div>
        ";

        if (sendMail($email, $subject, $body)) {
            header("Location: index.php?page=verify_forgot");
            exit;
        } else {
            $errors['email'] = "Không thể gửi email xác nhận. Vui lòng thử lại.";
        }
    }
}
?>

<title>Quên mật khẩu</title>

<div class="register-wrapper">
  <div class="register-box">
    <h1>Quên mật khẩu</h1>

    <form action="" method="post">
      <div class="form-group">
        <div class="label-row">
          <label>Nhập email đã đăng ký:</label>
          <small class="error-msg">* <?= $errors['email'] ?? '' ?></small>
        </div>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
      </div>

      <button type="submit" name="send_otp">Gửi mã xác nhận</button>
    </form>

    <p>Bạn nhớ lại mật khẩu? <a href="index.php?page=login">Đăng nhập</a></p>
  </div>
</div>

<link rel="stylesheet" href="assets/css/register.css?v=2">
