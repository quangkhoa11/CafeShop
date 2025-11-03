<title>Hồ sơ cửa hàng</title>
<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=shop_login");
    exit;
}

$idshop = $_SESSION['idshop'];
$db = new database();

// Lấy thông tin shop hiện tại
$shop = $db->xuatdulieu("SELECT * FROM shop WHERE idshop = '$idshop'")[0];

// Cập nhật thông tin nếu form gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $tenshop = trim($_POST['tenshop']);
    $email = trim($_POST['email']);
    $sdt = trim($_POST['sdt']);
    $diachi = trim($_POST['diachi']);

    // Xử lý upload logo
    $logo = $shop['logo'];
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = "assets/images/";
        $target_file = $target_dir . basename($_FILES["logo"]["name"]);
        move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
        $logo = $target_file; // ✅ Lưu toàn bộ đường dẫn (VD: assets/images/logocafe.png)
    }

    $sql = "UPDATE shop SET 
                tenshop = '$tenshop', 
                email = '$email', 
                sdt = '$sdt', 
                diachi = '$diachi', 
                logo = '$logo'
            WHERE idshop = '$idshop'";
    $db->themxoasua($sql);

    echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location='index.php?page=shop_profile';</script>";
    exit;
}
?>

<div class="profile-container">
    <h2>Hồ sơ cửa hàng</h2>

    <form method="POST" enctype="multipart/form-data" class="profile-form">
        <div class="profile-header">
            <img src="<?= htmlspecialchars($shop['logo']) ?>" class="profile-logo" alt="Logo Shop">
            <label class="upload-btn">
                <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)">
                Thay đổi logo
            </label>
            <div id="preview" style="margin-top:10px;"></div>
        </div>

        <div class="form-group">
            <label>Tên cửa hàng:</label>
            <input type="text" name="tenshop" value="<?= htmlspecialchars($shop['tenshop']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($shop['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại:</label>
            <input type="text" name="sdt" value="<?= htmlspecialchars($shop['sdt']) ?>" required>
        </div>

        <div class="form-group">
            <label>Địa chỉ:</label>
            <textarea name="diachi" rows="3" required><?= htmlspecialchars($shop['diachi']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" name="update" class="btn-save">💾 Lưu thay đổi</button>
            <a href="index.php?page=shop_dashboard" class="btn-cancel">← Quay lại</a>
        </div>
    </form>
</div>

<script>
function previewLogo(input) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width:120px;height:120px;border-radius:50%;margin-top:10px;border:3px solid #ff9933;">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.profile-container {
    width: 700px;
    margin: 50px auto;
    background: #fffaf2;
    border-radius: 16px;
    padding: 30px 40px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
}

.profile-container h2 {
    text-align: center;
    color: #ff6600;
    font-weight: 700;
    border-bottom: 3px solid #ff6600;
    padding-bottom: 10px;
    margin-bottom: 25px;
}

.profile-header {
    text-align: center;
    margin-bottom: 25px;
}

.profile-logo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #ffcc80;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    margin-bottom: 10px;
}

.upload-btn input {
    display: none;
}

.upload-btn {
    display: inline-block;
    background: #ff9933;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

.upload-btn:hover {
    background: #e67e00;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 8px;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    transition: 0.3s;
    background: #fff;
    font-size: 15px;
}

input:focus, textarea:focus {
    border-color: #ff9933;
    box-shadow: 0 0 5px rgba(255,153,51,0.5);
    outline: none;
}

textarea {
    resize: none;
}

.form-actions {
    text-align: center;
    margin-top: 25px;
}

.btn-save {
    background: #ff6600;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    margin-right: 10px;
}

.btn-save:hover {
    background: #e05500;
}

.btn-cancel {
    text-decoration: none;
    background: #999;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}

.btn-cancel:hover {
    background: #777;
}
</style>
