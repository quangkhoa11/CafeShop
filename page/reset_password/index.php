<?php
$obj = new database();
$errors = [];
$success = '';

if (!isset($_SESSION['reset_allowed']) || !isset($_SESSION['forgot_data'])) {
    header("Location: index.php?page=forgot");
    exit;
}

$email = $_SESSION['forgot_data']['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $matkhau = $_POST['matkhau'] ?? '';
    $matkhau2 = $_POST['matkhau2'] ?? '';

    if (!$matkhau) {
        $errors['matkhau'] = "Vui lòng nhập mật khẩu mới.";
    } elseif (strlen($matkhau) < 8) {
        $errors['matkhau'] = "Mật khẩu phải có ít nhất 8 ký tự.";
    } elseif (!preg_match('/[A-Z]/', $matkhau) || !preg_match('/[0-9]/', $matkhau)) {
        $errors['matkhau'] = "Mật khẩu phải chứa ít nhất 1 chữ hoa và 1 chữ số.";
    }

    if (!$matkhau2) {
        $errors['matkhau2'] = "Vui lòng xác nhận mật khẩu.";
    } elseif ($matkhau !== $matkhau2) {
        $errors['matkhau2'] = "Mật khẩu xác nhận không trùng khớp.";
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($matkhau, PASSWORD_DEFAULT);
        $update = $obj->themxoasua("UPDATE khachhang SET matkhau='$hashedPassword' WHERE email='$email'");

        if ($update) {
            unset($_SESSION['forgot_data']);
            unset($_SESSION['reset_allowed']);
            
            $success = "Cập nhật mật khẩu thành công! Bạn có thể đăng nhập ngay.";
        } else {
            $errors['matkhau'] = "Không thể cập nhật mật khẩu. Vui lòng thử lại.";
        }
    }
}
?>

<title>Đặt lại mật khẩu</title>

<div class="register-wrapper">
  <div class="register-box">
    <h1>Đặt lại mật khẩu</h1>
    <p>Đặt lại mật khẩu cho tài khoản: <strong><?= htmlspecialchars($email) ?></strong></p>

    <?php if ($success): ?>
      <div class="alert-success"><?= $success ?></div>
      <p><a href="index.php?page=login">👉 Đăng nhập ngay</a></p>
    <?php else: ?>
      <form action="" method="post">
        <div class="form-group">
          <div class="label-row">
            <label>Mật khẩu mới:</label>
            <small class="error-msg">* <?= $errors['matkhau'] ?? '' ?></small>
          </div>
          <input type="password" name="matkhau" required>
        </div>

        <div class="form-group">
          <div class="label-row">
            <label>Xác nhận mật khẩu:</label>
            <small class="error-msg">* <?= $errors['matkhau2'] ?? '' ?></small>
          </div>
          <input type="password" name="matkhau2" required>
        </div>

        <button type="submit" name="reset_password">Cập nhật mật khẩu</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<link rel="stylesheet" href="assets/css/register.css?v=2">
