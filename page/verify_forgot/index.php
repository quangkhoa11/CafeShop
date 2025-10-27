<?php
$obj = new database();
$errors = [];

if (!isset($_SESSION['forgot_data'])) {
    header("Location: index.php?page=forgot");
    exit;
}

$email = $_SESSION['forgot_data']['email'];
$otp_saved = $_SESSION['forgot_data']['otp'];
$otp_time = $_SESSION['forgot_data']['otp_time'];
$time_now = time();

$remaining = max(0, 300 - ($time_now - $otp_time)); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otp_input = trim($_POST['otp']);

    if (!$otp_input) {
        $errors['otp'] = "Vui lòng nhập mã OTP.";
    } elseif ($time_now - $otp_time > 300) { 
        $errors['otp'] = "Mã OTP đã hết hạn. Vui lòng yêu cầu gửi lại.";
    } elseif ($otp_input != $otp_saved) {
        $errors['otp'] = "Mã OTP không chính xác.";
    }

    if (empty($errors)) {
        $_SESSION['reset_allowed'] = true;
        header("Location: index.php?page=reset_password");
        exit;
    }
}
?>

<title>Xác nhận mã OTP</title>

<div class="otp-wrapper">
  <div class="otp-box">
    <h1>Xác nhận mã OTP</h1>
    <p>Mã OTP đã được gửi đến email: <strong><?= htmlspecialchars($email) ?></strong></p>
    
    <p style="margin-top: 10px; color: #555;">
      Thời gian còn lại: 
      <span id="countdown" style="font-weight:bold; color:#ff6600;">
        <?= gmdate("i:s", $remaining) ?>
      </span>
    </p>

    <?php if (isset($errors['otp'])): ?>
      <div class="error-msg"><?= $errors['otp'] ?></div>
    <?php endif; ?>

    <form action="" method="post">
      <label>Nhập mã OTP:</label>
      <input type="text" name="otp" maxlength="6" required 
             onkeypress="return event.charCode >= 48 && event.charCode <= 57"
             oninput="this.value = this.value.replace(/[^0-9]/g, '')">

      <button type="submit" name="verify_otp">Xác nhận</button>
    </form>

    <p>Không nhận được mã? <a href="index.php?page=forgot">Gửi lại</a></p>
  </div>
</div>

<link rel="stylesheet" href="assets/css/otp.css">

<script>
  let remaining = <?= $remaining ?>;
  const countdownEl = document.getElementById('countdown');

  const timer = setInterval(() => {
    if (remaining <= 0) {
      clearInterval(timer);
      countdownEl.textContent = "Hết hạn";
      countdownEl.style.color = "#c0392b";
      return;
    }

    remaining--;
    const minutes = Math.floor(remaining / 60);
    const seconds = remaining % 60;
    countdownEl.textContent = 
      `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  }, 1000);
  
</script>