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

        if ($data && $matkhaucu === $data[0]['matkhau']) {
            if ($matkhaumoi === $xacnhan) {
                $obj->xuatdulieu("UPDATE khachhang SET matkhau = '$matkhaumoi' WHERE idkh = $idkh");
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

<div class="container">
    <form method="POST" class="form-box">
        <h2>ĐỔI MẬT KHẨU</h2>
        <label>Mật khẩu cũ:</label>
        <input type="password" name="matkhaucu" placeholder="Nhập mật khẩu cũ" required>

        <label>Mật khẩu mới:</label>
        <input type="password" name="matkhaumoi" placeholder="Nhập mật khẩu mới" required>

        <label>Xác nhận mật khẩu mới:</label>
        <input type="password" name="xacnhan" placeholder="Nhập lại mật khẩu mới" required>

        <button type="submit">Đổi mật khẩu</button>
        <?php if ($thongbao != "") echo "<p class='msg'>$thongbao</p>"; ?>
    </form>
</div>

<style>
.form-box {
    width: 360px;
    margin: 50px auto;
    padding: 35px 30px;
    background: linear-gradient(145deg, #ffffff, #f8f8f8);
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', Roboto, sans-serif;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.form-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.form-box h2 {
    text-align: center;
    color: #e67e22;
    margin-bottom: 25px;
    font-size: 22px;
    letter-spacing: 0.5px;
}

.form-box label {
    font-weight: 600;
    display: block;
    margin: 12px 0 6px;
    color: #333;
}

.form-box input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    background-color: #fafafa;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.form-box input:focus {
    border-color: #e67e22;
    box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.15);
    outline: none;
    background-color: #fff;
}

.form-box button {
    width: 100%;
    background: linear-gradient(135deg, #e67e22, #f39c12);
    color: #fff;
    border: none;
    padding: 12px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.3s, transform 0.2s;
}

.form-box button:hover {
    background: linear-gradient(135deg, #cf6d17, #e67e22);
    transform: scale(1.02);
}

.msg {
    margin-top: 18px;
    text-align: center;
    font-weight: 600;
    color: #d35400;
    background: rgba(230, 126, 34, 0.1);
    border-radius: 8px;
    padding: 10px;
    font-size: 14px;
}

</style>
