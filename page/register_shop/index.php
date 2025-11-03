<?php 
require_once 'mail/sendmail.php';
$obj = new database();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $tenshop = trim($_POST['tenshop']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);
    $email = trim($_POST['email']);
    $matkhau = $_POST['matkhau'];
    $matkhau2 = $_POST['matkhau2'];
    $logo = $_FILES['logo']['name'] ?? '';

    if (!$tenshop) $errors['tenshop'] = "Vui lòng nhập tên shop.";
    
    if (!$sdt) {
        $errors['sdt'] = "Vui lòng nhập số điện thoại.";
    } elseif (!preg_match('/^(0[3|5|7|8|9])[0-9]{8}$/', $sdt)) {
        $errors['sdt'] = "Số điện thoại không hợp lệ!";
    }

    if (!$diachi) $errors['diachi'] = "Vui lòng nhập địa chỉ.";
    if (!$email) $errors['email'] = "Vui lòng nhập email.";
    if (!$matkhau) $errors['matkhau'] = "Vui lòng nhập mật khẩu.";
    if (!$matkhau2) $errors['matkhau2'] = "Vui lòng xác nhận mật khẩu.";

    if ($matkhau && strlen($matkhau) < 8) {
        $errors['matkhau'] = "Mật khẩu phải có ít nhất 8 ký tự.";
    } elseif (!preg_match('/[A-Z]/', $matkhau) || !preg_match('/[0-9]/', $matkhau)) {
        $errors['matkhau'] = "Mật khẩu phải chứa ít nhất 1 chữ hoa và 1 chữ số.";
    }

    if ($matkhau && $matkhau2 && $matkhau !== $matkhau2) {
        $errors['matkhau2'] = "Mật khẩu và xác nhận không trùng nhau.";
    }

    if ($email && $obj->xuatdulieu("SELECT idshop FROM shop WHERE email='$email'")) {
        $errors['email'] = "Email này đã được đăng ký.";
    }

    if ($sdt && $obj->xuatdulieu("SELECT idshop FROM shop WHERE sdt='$sdt'")) {
        $errors['sdt'] = "Số điện thoại này đã được đăng ký.";
    }

    if ($logo) {
        $target_dir = "assets/images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $target_file = $target_dir . basename($logo);
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors['logo'] = "Chỉ chấp nhận file hình ảnh (jpg, jpeg, png, gif).";
        } else {
            move_uploaded_file($_FILES['logo']['tmp_name'], $target_file);
        }
    } else {
        $errors['logo'] = "Vui lòng tải lên logo cửa hàng.";
    }

    if (empty($errors)) {
        $otp = rand(100000, 999999);
        $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT); 

        $_SESSION['register_shop'] = [
            'tenshop' => $tenshop,
            'sdt' => $sdt,
            'diachi' => $diachi,
            'email' => $email,
            'matkhau' => $hashedPassword,
            'logo' => $target_file ?? '',
            'otp' => $otp
        ];

        $subject = "Mã xác nhận đăng ký Shop - The Dream";
        $body = "
        <div style='font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #2C3E50;'>🛍️ Xin chào $tenshop,</h2>
            <p>Cảm ơn bạn đã đăng ký cửa hàng tại <strong>The Dream</strong>.</p>
            <p>Vui lòng nhập mã OTP sau để hoàn tất đăng ký:</p>
            <div style='background: #f4f6f8; padding: 15px 20px; border-radius: 8px; 
                        font-size: 18px; text-align: center; font-weight: bold; color: #2C3E50;
                        letter-spacing: 3px; border: 1px dashed #3498db;'>
                $otp
            </div>
            <p style='margin-top: 15px;'>⏳ <i>Mã OTP có hiệu lực trong <b>5 phút</b>. 
            Vui lòng không chia sẻ mã này với bất kỳ ai.</i></p>
            <p>Trân trọng,<br>
            <strong>Đội ngũ The Dream</strong><br>
        </div>
        ";

        if (sendMail($email, $subject, $body)) {
            header("Location: index.php?page=verify_shop_otp");
exit;

        } else {
            $errors['email'] = "Không thể gửi email xác nhận. Vui lòng thử lại.";
        }
    }
}
?>

<title>Đăng ký Shop</title>

<div class="register-wrapper">
  <div class="register-box">
    <h1>Đăng ký cửa hàng</h1>

    <form action="" method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>Tên shop:</label>
        <small class="error-msg"><?= $errors['tenshop'] ?? '' ?></small>
        <input type="text" name="tenshop" value="<?= htmlspecialchars($tenshop ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Số điện thoại:</label>
        <small class="error-msg"><?= $errors['sdt'] ?? '' ?></small>
        <input type="text" name="sdt" value="<?= htmlspecialchars($sdt ?? '') ?>" maxlength="10"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
      </div>

      <div class="form-group">
        <label>Địa chỉ:</label>
        <small class="error-msg"><?= $errors['diachi'] ?? '' ?></small>
        <input type="text" name="diachi" value="<?= htmlspecialchars($diachi ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Email:</label>
        <small class="error-msg"><?= $errors['email'] ?? '' ?></small>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Logo cửa hàng:</label>
        <small class="error-msg"><?= $errors['logo'] ?? '' ?></small>
        <input type="file" name="logo" accept="image/*" required>
      </div>

      <div class="form-group">
        <label>Mật khẩu:</label>
        <small class="error-msg"><?= $errors['matkhau'] ?? '' ?></small>
        <input type="password" name="matkhau" required>
      </div>

      <div class="form-group">
        <label>Xác nhận mật khẩu:</label>
        <small class="error-msg"><?= $errors['matkhau2'] ?? '' ?></small>
        <input type="password" name="matkhau2" required>
      </div>

      <button type="submit" name="register">Đăng ký</button>
    </form>

    <p>Đã có tài khoản? <a href="index.php?page=login_shop">Đăng nhập</a></p>
  </div>
</div>

<link rel="stylesheet" href="assets/css/register.css?v=2">
