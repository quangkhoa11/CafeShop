<title>Thông tin cá nhân</title>
<?php
session_start();
require_once 'class/classdb.php'; // Đảm bảo đường dẫn đúng

if (!isset($_SESSION['idkh'])) {
    header("Location: index.php?page=login");
    exit;
}

$idkh = $_SESSION['idkh'];
$db = new database();

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $tenkh = trim($_POST['tenkh']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);

    if ($tenkh === "" || $email === "" || $sdt === "" || $diachi === "") {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $checkEmail = $db->xuatdulieu("SELECT idkh FROM khachhang WHERE email = '$email' AND idkh != $idkh");
        if ($checkEmail) {
            $error = "Email này đã được sử dụng!";
        } else {
            $sql = "
                UPDATE khachhang 
                SET tenkh = '$tenkh', email = '$email', sdt = '$sdt', diachi = '$diachi'
                WHERE idkh = $idkh
            ";
            if ($db->themxoasua($sql)) {
                $success = "Cập nhật thông tin thành công!";
            } else {
                $error = "Cập nhật thất bại. Vui lòng thử lại.";
            }
        }
    }
}

$kh = $db->xuatdulieu("SELECT * FROM khachhang WHERE idkh = $idkh");
$kh = $kh ? $kh[0] : null;
?>

<div class="profile-container">
    <h2>Thông tin cá nhân</h2>

    <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($kh): ?>
        <form method="POST" class="profile-card" id="profileForm">
            <div class="profile-left">
                <img src="assets/images/icon.png" alt="Avatar" class="avatar">
            </div>

            <div class="profile-right">
                <div class="info-item">
                    <label>Họ và tên:</label>
                    <input type="text" name="tenkh" value="<?= htmlspecialchars($kh['tenkh']) ?>" disabled required>
                </div>

                <div class="info-item">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($kh['email']) ?>" disabled required>
                </div>

                <div class="info-item">
                    <label>Số điện thoại:</label>
                    <input type="text" name="sdt" value="<?= htmlspecialchars($kh['sdt']) ?>" disabled required pattern="[0-9]{10,11}" title="Số điện thoại phải từ 10-11 chữ số">
                </div>

                <div class="info-item">
                    <label>Địa chỉ:</label>
                    <input type="text" name="diachi" value="<?= htmlspecialchars($kh['diachi']) ?>" disabled required>
                </div>
            </div>

            <div class="btn-group">
                <button type="button" id="editBtn" class="btn-edit">✏️ Chỉnh sửa</button>
                <button type="submit" name="update_profile" id="saveBtn" class="btn-save" style="display:none;">💾 Lưu thay đổi</button>
                <a href="index.php?page=customer" id="cancelBtn" class="btn-cancel" style="display:none;">❌ Hủy</a>
            </div>
        </form>
    <?php else: ?>
        <p class="no-info">Không tìm thấy thông tin khách hàng.</p>
    <?php endif; ?>
</div>

<style>
.profile-container {
    max-width: 900px;
    margin: 60px auto;
    background: #fffaf2;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Arial, sans-serif;
}
.profile-container h2 {
    text-align: center;
    font-size: 28px;
    color: #ff6600;
    border-bottom: 3px solid #ff6600;
    padding-bottom: 12px;
    margin-bottom: 30px;
    font-weight: 700;
}
.profile-card {
    display: flex;
    align-items: flex-start;
    background: #fff;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    border: 1px solid #f3d9b1;
    flex-wrap: wrap;
}
.profile-left {
    flex: 0 0 140px;
    text-align: center;
}
.avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid #ff6600;
    object-fit: cover;
    background: #fff;
}
.profile-right {
    flex: 1;
    padding-left: 30px;
}
.info-item {
    margin-bottom: 15px;
}
.info-item label {
    display: block;
    color: #555;
    font-weight: bold;
    margin-bottom: 5px;
}
.info-item input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ffe1b3;
    border-radius: 6px;
    font-size: 15px;
    background: #fff8ec;
    transition: 0.2s;
}
.info-item input:focus {
    border-color: #ff6600;
    outline: none;
    box-shadow: 0 0 5px rgba(255,102,0,0.3);
}
.btn-group {
    text-align: center;
    width: 100%;
    margin-top: 25px;
}
.btn-save, .btn-cancel, .btn-edit {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}
.btn-save {
    background: #ff6600;
    color: white;
}
.btn-save:hover {
    background: #e85d00;
    transform: scale(1.05);
}
.btn-edit {
    background: #ff6600;
    color: white;
}
.btn-edit:hover {
    background: #e85d00;
    transform: scale(1.05);
}
.btn-cancel {
    background: #fff;
    color: #ff6600;
    border: 2px solid #ff6600;
}
.btn-cancel:hover {
    background: #ff6600;
    color: white;
    transform: scale(1.05);
}
.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    text-align: center;
}
.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.no-info {
    text-align: center;
    color: #777;
    font-size: 16px;
    margin-top: 20px;
}
</style>

<script>
document.getElementById('editBtn').addEventListener('click', function() {
    document.querySelectorAll('.profile-right input').forEach(input => {
        input.removeAttribute('disabled');
    });
    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('saveBtn').style.display = 'inline-block';
    document.getElementById('cancelBtn').style.display = 'inline-block';
});
</script>
