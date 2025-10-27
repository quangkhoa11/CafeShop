<title>Đổi mật khẩu</title>
<?php
$obj = new database();
$thongbao = "";

if (!isset($_SESSION['idkh'])) {
    die("Vui lòng đăng nhập trước khi đổi mật khẩu.");
}

$idkh = $_SESSION['idkh'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matkhaucu = trim($_POST['matkhaucu']);
    $matkhaumoi = trim($_POST['matkhaumoi']);
    $xacnhan = trim($_POST['xacnhan']);

    if ($matkhaucu === '' || $matkhaumoi === '' || $xacnhan === '') {
        $thongbao = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $query = "SELECT matkhau FROM khachhang WHERE idkh = $idkh";
        $data = $obj->xuatdulieu($query);

        if ($data && password_verify($matkhaucu, $data[0]['matkhau'])) {
            if ($matkhaumoi === $xacnhan) {
                $matkhaumoi_hash = password_hash($matkhaumoi, PASSWORD_DEFAULT);
                $obj->xuatdulieu("UPDATE khachhang SET matkhau = '$matkhaumoi_hash' WHERE idkh = $idkh");
                $thongbao = "Đổi mật khẩu thành công!";
            } else {
                $thongbao = "Mật khẩu xác nhận không khớp.";
            }
        } else {
            $thongbao = "Mật khẩu cũ không đúng.";
        }
    }
}
?>

<div class="change-password-container">
    <form method="POST" class="change-password-form">
        <h2>Đổi mật khẩu</h2>

        <label>Mật khẩu cũ:</label>
        <input type="password" name="matkhaucu" placeholder="Nhập mật khẩu cũ" required>

        <label>Mật khẩu mới:</label>
        <input type="password" name="matkhaumoi" placeholder="Nhập mật khẩu mới" required>

        <label>Xác nhận mật khẩu mới:</label>
        <input type="password" name="xacnhan" placeholder="Nhập lại mật khẩu mới" required>

        <button type="submit">Đổi mật khẩu</button>

        <?php if ($thongbao != ""): ?>
            <p class="msg"><?= htmlspecialchars($thongbao) ?></p>
        <?php endif; ?>
    </form>
</div>

<style>
.change-password-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px 15px;
}

.change-password-form {
    width: 100%;
    max-width: 360px;
    padding: 30px 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', sans-serif;
}

.change-password-form h2 {
    text-align: center;
    color: #e67e22;
    margin-bottom: 20px;
    font-size: 22px;
}

.change-password-form label {
    display: block;
    font-weight: 600;
    margin: 12px 0 6px;
}

.change-password-form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    margin-bottom: 10px;
    transition: border-color 0.3s;
}

.change-password-form input:focus {
    border-color: #e67e22;
    outline: none;
    background-color: #fff;
}

.change-password-form button {
    width: 100%;
    background-color: #e67e22;
    color: white;
    border: none;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 10px;
}

.change-password-form button:hover {
    background-color: #cf6d17;
}

.msg {
    text-align: center;
    color: #d35400;
    font-weight: bold;
    margin-top: 15px;
}
</style>
