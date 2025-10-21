<title>Thông tin cá nhân</title>
<?php

if (!isset($_SESSION['idkh'])) {
    header("Location: index.php?page=login");
    exit;
}

$idkh = $_SESSION['idkh'];
$db = new database();

$kh = $db->xuatdulieu("SELECT * FROM khachhang WHERE idkh = $idkh");
$kh = $kh ? $kh[0] : null;
?>

<div class="profile-container">
    <h2>Thông tin cá nhân</h2>

    <?php if ($kh): ?>
        <div class="profile-card">
            <div class="profile-left">
                <img src="assets/images/icon.png" alt="Avatar" class="avatar">
            </div>
            <div class="profile-right">
                <div class="info-item">
                    <label>Họ và tên:</label>
                    <span><?= htmlspecialchars($kh['tenkh']) ?></span>
                </div>
                <div class="info-item">
                    <label>Email:</label>
                    <span><?= htmlspecialchars($kh['email']) ?></span>
                </div>
                <div class="info-item">
                    <label>Số điện thoại:</label>
                    <span><?= htmlspecialchars($kh['sdt']) ?></span>
                </div>
                <div class="info-item">
                    <label>Địa chỉ:</label>
                    <span><?= htmlspecialchars($kh['diachi']) ?></span>
                </div>
            </div>
        </div>

        <div class="btn-group">
            <a href="#" class="btn-edit">✏️ Chỉnh sửa thông tin</a>
        </div>
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
    transition: all 0.3s ease;
}

.profile-container:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
    align-items: center;
    background: #fff;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    border: 1px solid #f3d9b1;
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
    margin-bottom: 3px;
}

.info-item span {
    display: inline-block;
    font-size: 16px;
    color: #333;
    background: #fff8ec;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ffe1b3;
}

.btn-group {
    text-align: center;
    margin-top: 30px;
}

.btn-view, .btn-edit {
    display: inline-block;
    padding: 12px 25px;
    margin: 0 10px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-edit {
    background: #ff6600;
    color: white;
}

.btn-edit:hover {
    background: #e85d00;
    transform: scale(1.05);
}

.btn-view {
    background: #fff;
    color: #ff6600;
    border: 2px solid #ff6600;
}

.btn-view:hover {
    background: #ff6600;
    color: white;
    transform: scale(1.05);
}

.no-info {
    text-align: center;
    color: #777;
    font-size: 16px;
    margin-top: 20px;
}
</style>
