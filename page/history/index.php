<?php

if (!isset($_GET['iddonban'])) {
    echo "<p>Không tìm thấy đơn hàng.</p>";
    exit;
}

$iddonban = $_GET['iddonban'];
$db = new database();

$donban = $db->xuatdulieu("
    SELECT db.*, kh.tenkh, kh.sdt, kh.diachi, kh.email
    FROM donban db
    JOIN khachhang kh ON db.idkh = kh.idkh
    WHERE db.iddonban = '$iddonban'
");

if (empty($donban)) {
    echo "<p>Không tìm thấy đơn hàng.</p>";
    exit;
}

$don = $donban[0];

$chitiet = $db->xuatdulieu("
    SELECT sp.tensp, sp.hinhanh, ctdb.soluong, ctdb.dongia, ctdb.thanhtien
    FROM chitietdonban ctdb
    JOIN sanpham sp ON ctdb.idsp = sp.idsp
    WHERE ctdb.iddonban = '$iddonban'
");
?>

<div class="container">
    <h2 class="text-center mt-4">CHI TIẾT ĐƠN HÀNG: <?= htmlspecialchars($iddonban) ?></h2>

    <div class="box">
        <h3><b>Thông tin người nhận</b></h3>
        <p><strong>Tên người nhận:</strong> <?= htmlspecialchars($don['tennguoinhan']) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($don['sdtnguoinhan']) ?></p>
        <p><strong>Địa chỉ nhận:</strong> <?= htmlspecialchars($don['diachinhan']) ?></p>
        <p><strong>Ngày bán:</strong> <?= htmlspecialchars($don['ngayban']) ?></p>
    </div>

    <div class="box">
        <h3><b>Sản phẩm trong đơn hàng</b></h3>
        <table>
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th style="text-align:center;">Số lượng</th>
                    <th style="text-align:right;">Đơn giá</th>
                    <th style="text-align:right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chitiet as $row): ?>
                    <tr>
                        <td><img src="assets/images/<?= htmlspecialchars($row['hinhanh']) ?>" alt=""></td>
                        <td><?= htmlspecialchars($row['tensp']) ?></td>
                        <td style="text-align:center;"><?= $row['soluong'] ?></td>
                        <td style="text-align:right;"><?= number_format($row['dongia'], 0, ',', '.') ?>₫</td>
                        <td style="text-align:right;"><?= number_format($row['thanhtien'], 0, ',', '.') ?>₫</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="box total">
        Tổng tiền: <span><?= number_format($don['tongtien'], 0, ',', '.') ?>₫</span>
    </div>

    <div style="text-align:center; margin-top: 20px; margin-bottom: 20px;">
        <a href="index.php?page=re-order&iddonban=<?= urlencode($iddonban) ?>" class="reorder-btn">🔁 Đặt lại đơn hàng</a>
    </div>
</div>
<style>
h2 {
    color: #e65c00;
    margin-bottom: 25px;
    font-size: 24px;
}

.box {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 25px;
}

.box h3 {
    font-size: 18px;
    color: #444;
    margin-bottom: 15px;
    border-left: 4px solid #e65c00;
    padding-left: 8px;
}

.box p {
    line-height: 1.6;
    color: #333;
    margin: 5px 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px 12px;
    text-align: left;
}

th {
    background: #ffe9d6;
    color: #333;
    font-weight: 600;
}

tr:hover {
    background: #fff5ee;
}

td img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 6px;
}

.total {
    text-align: right;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.total span {
    color: #e65c00;
    font-size: 22px;
}

.reorder-btn {
    display: inline-block;
    background: #e65c00;
    color: white;
    font-weight: bold;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    transition: background 0.3s, transform 0.2s;
    text-decoration: none;
}

.reorder-btn:hover {
    background: #cc5200;
    transform: translateY(-2px);
}
</style>