<title>Chi tiết đơn hàng <?= htmlspecialchars($_GET['iddonban'] ?? '') ?></title>
<?php

if (!isset($_SESSION['idkh'])) {
    echo "<p>⚠️ Vui lòng <a href='index.php?page=login'>đăng nhập</a> để xem chi tiết đơn hàng.</p>";
    exit;
}

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
    SELECT sp.tensp, sp.hinhanh, ctdb.soluong, da, duong, size, ctdb.dongia, ctdb.thanhtien, ghichu
    FROM chitietdonban ctdb
    JOIN sanpham sp ON ctdb.idsp = sp.idsp
    WHERE ctdb.iddonban = '$iddonban'
");

$statusClass = '';
switch ($don['trangthai']) {
    case 'Đã đặt hàng': $statusClass = 'status-blue'; break;
    case 'Đang xử lý': $statusClass = 'status-yellow'; break;
    case 'Đã giao cho đơn vị vận chuyển': $statusClass = 'status-purple'; break;
    case 'Hoàn thành': $statusClass = 'status-green'; break;
    case 'Đã hủy': $statusClass = 'status-red'; break;
    default: $statusClass = 'status-gray';
}
?>

<div class="content-container">
    <h2 class="text-center mt-4">CHI TIẾT ĐƠN HÀNG: <?= htmlspecialchars($iddonban) ?></h2>

    <div class="box">
        <h3><b>Thông tin người nhận</b></h3>
        <p><strong>Tên người nhận:</strong> <?= htmlspecialchars($don['tennguoinhan']) ?></p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($don['sdtnguoinhan']) ?></p>
        <p><strong>Địa chỉ nhận:</strong> <?= htmlspecialchars($don['diachinhan']) ?></p>
        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($don['ngayban'])) ?></p>
        <p><strong>Trạng thái:</strong> <span class="status <?= $statusClass ?>"><?= htmlspecialchars($don['trangthai']) ?></span></p>
    </div>

    <div class="box">
        <h3><b>Sản phẩm trong đơn hàng</b></h3>
        <table>
            <thead>
                <tr>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th style="text-align:center;">Số lượng</th>
                    <th style="text-align:center;">Đường</th>
                    <th style="text-align:center;">Đá</th>
                    <th style="text-align:center;">Size</th>
                    <th style="text-align:right;">Đơn giá</th>
                    <th style="text-align:right;">Thành tiền</th>
                    <th style="text-align:center;">Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chitiet as $row): ?>
                    <tr>
                        <td><img src="assets/images/<?= htmlspecialchars($row['hinhanh']) ?>" alt=""></td>
                        <td><?= htmlspecialchars($row['tensp']) ?></td>
                        <td style="text-align:center;"><?= $row['soluong'] ?></td>
                        <td style="text-align:center;"><?= $row['duong'] ?></td>
                        <td style="text-align:center;"><?= $row['da'] ?></td>
                        <td style="text-align:center;"><?= $row['size'] ?></td>
                        <td style="text-align:right;"><?= number_format($row['dongia'], 0, ',', '.') ?>₫</td>
                        <td style="text-align:right;"><?= number_format($row['thanhtien'], 0, ',', '.') ?>₫</td>
                        <td style="text-align:center;"><?= htmlspecialchars($row['ghichu']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="box total">
        Tổng tiền: <span><?= number_format($don['tongtien'], 0, ',', '.') ?>₫</span>
    </div>

    <div style="text-align:center; margin-top: 20px; margin-bottom: 20px;">
        <a href="index.php?page=re-order&iddonban=<?= urlencode($iddonban) ?>" class="reorder-btn">Đặt lại đơn hàng</a>
        <a href="index.php?page=order-history" class="reorder-btn">Quay về</a>
    </div>
</div>

<style>
.content-container {
    width: 950px;
    margin: 50px auto;
    background: #fff9f0;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', Arial, sans-serif;
}

h2 {
    color: #e65c00;
    margin-bottom: 30px;
    font-size: 26px;
    text-align: center;
}

.box {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 20px 25px;
    margin-bottom: 25px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

.box h3 {
    font-size: 18px;
    color: #444;
    margin-bottom: 15px;
    border-left: 5px solid #e65c00;
    padding-left: 10px;
}

.box p {
    line-height: 1.6;
    color: #333;
    margin: 6px 0;
    font-size: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    font-size: 14px;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px 10px;
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
    border-radius: 8px;
    transition: transform 0.2s;
}

td img:hover {
    transform: scale(1.05);
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
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    transition: background 0.3s, transform 0.2s;
    text-decoration: none;
    margin: 5px;
}

.reorder-btn:hover {
    background: #cc5200;
    transform: translateY(-2px);
}
.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 13px;
}
.status-blue { background: #e6f2ff; color: #007bff; }
.status-yellow { background: #fff8e1; color: #e6a600; }
.status-purple { background: #f3e5f5; color: #8e24aa; }
.status-green { background: #e8f5e9; color: #2e7d32; }
.status-red { background: #ffebee; color: #c62828; }
.status-gray { background: #f5f5f5; color: #555; }

@media (max-width: 768px) {
    table th, table td {
        padding: 8px 6px;
        font-size: 13px;
    }

    td img {
        width: 55px;
        height: 55px;
    }

    .box {
        padding: 15px 20px;
    }
}
</style>
