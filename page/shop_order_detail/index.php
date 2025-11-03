<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];
$iddonban = $_GET['iddonban'] ?? 0;

$sqlDon = "
    SELECT db.*, kh.tenkh, kh.email AS emailkh
    FROM donban db
    JOIN khachhang kh ON db.idkh = kh.idkh
    WHERE db.iddonban = '$iddonban' AND db.idshop = '$idshop'
";
$donhangArr = $db->xuatdulieu($sqlDon);

if (!$donhangArr || count($donhangArr) == 0) {
    echo "<div class='error-msg'>❌ Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập.</div>";
    exit;
}

$donhang = $donhangArr[0];

$sqlCT = "
    SELECT ct.*, sp.tensp, sp.hinhanh
    FROM chitietdonban ct
    JOIN sanpham sp ON ct.idsp = sp.idsp
    WHERE ct.iddonban = '$iddonban'
";
$chitiet = $db->xuatdulieu($sqlCT);
?>

<title>Chi tiết đơn hàng #<?= $donhang['iddonban'] ?></title>

<div class="order-detail-container">
    <a href="index.php?page=shop_orders" class="back-link">← Quay lại danh sách đơn hàng</a>

    <h2 class="order-title">🧾 Chi tiết đơn hàng #<?= $donhang['iddonban'] ?></h2>

    <div class="info-section">
        <div class="info-box">
            <h3>👤 Thông tin khách hàng</h3>
            <p><strong>Tên KH:</strong> <?= htmlspecialchars($donhang['tenkh']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($donhang['emailkh']) ?></p>
            <p><strong>Ngày đặt:</strong> <?= date("d/m/Y H:i", strtotime($donhang['ngayban'])) ?></p>
        </div>
        <div class="info-box">
            <h3>🚚 Thông tin giao hàng</h3>
            <p><strong>Người nhận:</strong> <?= htmlspecialchars($donhang['tennguoinhan']) ?></p>
            <p><strong>SĐT:</strong> <?= htmlspecialchars($donhang['sdtnguoinhan']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($donhang['diachinhan']) ?></p>
        </div>
    </div>

    <div class="status-section">
        <h3>📌 Trạng thái</h3>
        <?php
        $statusColors = [
            'Đã đặt hàng' => 'status-blue',
            'Đang xử lý' => 'status-yellow',
            'Đã giao cho đơn vị vận chuyển' => 'status-purple',
            'Hoàn thành' => 'status-green',
            'Đã hủy' => 'status-red'
        ];
        $color = $statusColors[$donhang['trangthai']] ?? 'status-gray';
        ?>
        <span class="status-badge <?= $color ?>">
            <?= htmlspecialchars($donhang['trangthai']) ?>
        </span>
    </div>

    <div class="table-wrapper">
        <table class="order-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Tùy chọn</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chitiet as $i => $ct): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td class="product-cell">
                            <img src="assets/images/<?= htmlspecialchars($ct['hinhanh']) ?>" alt="" class="product-img">
                            <div>
                                <p class="product-name"><?= htmlspecialchars($ct['tensp']) ?></p>
                                <?php if (!empty($ct['ghichu'])): ?>
                                    <p class="note">Ghi chú: <?= htmlspecialchars($ct['ghichu']) ?></p>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="option-cell">
                            <p>Đường: <?= htmlspecialchars($ct['duong']) ?>%</p>
                            <p>Đá: <?= htmlspecialchars($ct['da']) ?>%</p>
                            <p>Size: <?= htmlspecialchars($ct['size']) ?></p>
                        </td>
                        <td class="center"><?= $ct['soluong'] ?></td>
                        <td class="center"><?= number_format($ct['dongia'], 0, ',', '.') ?> ₫</td>
                        <td class="center total-cell"><?= number_format($ct['thanhtien'], 0, ',', '.') ?> ₫</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="total-section">
        💰 <strong>Tổng tiền:</strong>
        <span class="total-price"><?= number_format($donhang['tongtien'], 0, ',', '.') ?> ₫</span>
    </div>
</div>
<style>

.order-detail-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.back-link {
    display: inline-block;
    margin-bottom: 15px;
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
}
.back-link:hover {
    text-decoration: underline;
}

.order-title {
    text-align: center;
    color: #e86b1f;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 25px;
}

.info-section {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 25px;
}

.info-box {
    width: 48%;
    background: #fdf8f3;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #ffa54f;
}

.info-box h3 {
    color: #333;
    font-size: 18px;
    margin-bottom: 10px;
}

.status-section {
    margin-bottom: 20px;
}
.status-section h3 {
    font-size: 17px;
    margin-bottom: 8px;
    color: #333;
}

.status-badge {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 13px;
}

.status-blue { background: #e0f0ff; color: #0077cc; }
.status-yellow { background: #fff8e0; color: #b58900; }
.status-purple { background: #f3e8ff; color: #7c3aed; }
.status-green { background: #e6f7e9; color: #0b8a34; }
.status-red { background: #ffe5e5; color: #c53030; }
.status-gray { background: #f0f0f0; color: #555; }

.table-wrapper {
    overflow-x: auto;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

.order-table thead {
    background: linear-gradient(90deg, #f97316, #ff8c42);
    color: #fff;
}

.order-table th, 
.order-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #eee;
    text-align: left;
    vertical-align: middle;
}

.order-table tr:hover {
    background-color: #fff7f2;
    transition: 0.3s;
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #eee;
}

.product-name {
    font-weight: 600;
    color: #333;
}

.note {
    color: #777;
    font-size: 13px;
    font-style: italic;
}

.option-cell p {
    margin: 2px 0;
    color: #555;
    font-size: 14px;
}

.center {
    text-align: center;
}

.total-cell {
    color: #e86b1f;
    font-weight: bold;
}

.total-section {
    text-align: right;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.total-price {
    color: #e86b1f;
    font-size: 20px;
    margin-left: 8px;
}

.error-msg {
    text-align: center;
    color: #d93025;
    font-size: 18px;
    margin-top: 40px;
    font-weight: 600;
}

</style>