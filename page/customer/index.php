<title>Thông tin khách hàng</title>
<?php
if (!isset($_SESSION['idkh'])) {
    header("Location: index.php?page=login");
    exit;
}

$idkh = $_SESSION['idkh'];
$db = new database();

$kh = $db->xuatdulieu("SELECT * FROM khachhang WHERE idkh = $idkh");
$kh = $kh ? $kh[0] : null;

$donhang = $db->xuatdulieu("
    SELECT db.iddonban, db.ngayban, db.tongtien
    FROM donban db
    WHERE db.idkh = '$idkh'
    ORDER BY db.iddonban DESC
");
?>

<div class="lichsu-container">
    <h2>Thông tin khách hàng</h2>

    <?php if ($kh): ?>
        <div class="khachhang-box">
            <p><b>Tên khách hàng:</b> <?= htmlspecialchars($kh['tenkh']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($kh['email']) ?></p>
            <p><b>Số điện thoại:</b> <?= htmlspecialchars($kh['sdt']) ?></p>
            <p><b>Địa chỉ:</b> <?= htmlspecialchars($kh['diachi']) ?></p>
        </div>
    <?php endif; ?>

    <h3>Lịch sử đặt hàng</h3>

    <?php if ($donhang && count($donhang) > 0): ?>
        <table class="lichsu-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donhang as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['iddonban']) ?></td>
                        <td><?= date('d/m/Y', strtotime($d['ngayban'])) ?></td>
                        <td><?= number_format($d['tongtien'], 0, ',', '.') ?>₫</td>
                        <td>
                            <a href="index.php?page=history&iddonban=<?= urlencode($d['iddonban']) ?>" class="btn-view">Xem chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-order">Bạn chưa có đơn hàng nào.</p>
    <?php endif; ?>
</div>

<style>
.lichsu-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #fffaf2;
    font-family: Arial, sans-serif;
    color: #333;
}

.lichsu-container h2 {
    font-size: 26px;
    font-weight: bold;
    color: #ff6600;
    text-align: center;
    border-bottom: 3px solid #ff6600;
    padding-bottom: 10px;
    margin-bottom: 25px;
}

.khachhang-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 35px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.khachhang-box p {
    margin: 8px 0;
    font-size: 16px;
}

.lichsu-container h3 {
    font-size: 22px;
    color: #ff6600;
    margin-bottom: 15px;
}

.lichsu-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.lichsu-table th, .lichsu-table td {
    border: 1px solid #ddd;
    padding: 12px 15px;
    text-align: left;
}

.lichsu-table thead {
    background: #ffe0b3;
    color: #333;
}

.lichsu-table tbody tr:nth-child(even) {
    background: #fff7e6;
}

.lichsu-table tbody tr:hover {
    background: #fff0d6;
}

.btn-view {
    background: #ff6600;
    color: white;
    padding: 6px 10px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background 0.2s ease;
}

.btn-view:hover {
    background: #e05500;
}

.no-order {
    text-align: center;
    font-size: 16px;
    color: #888;
    margin-top: 30px;
}
</style>
