<title>Lịch sử đặt hàng</title>
<?php

if (!isset($_SESSION['idkh'])) {
    header("Location: index.php?page=login");
    exit;
}

$idkh = $_SESSION['idkh'];
$db = new database();

$donhang = $db->xuatdulieu("
    SELECT iddonban, ngayban, tongtien
    FROM donban
    WHERE idkh = '$idkh'
    ORDER BY iddonban DESC
");
?>

<div class="history-container">
    <h2>Lịch sử đặt hàng</h2>

    <?php if ($donhang && count($donhang) > 0): ?>
        <table class="history-table">
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

    <div class="back-btn">
        <a href="index.php?page=menu" class="btn-back">← Đặt hàng ngay</a>
    </div>
</div>

<style>
.history-container {
    width: 950px;
    margin: 60px auto;
    background: #fffaf2;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    font-family: 'Segoe UI', Arial, sans-serif;
    transition: all 0.3s ease;
}

.history-container:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.history-container h2 {
    text-align: center;
    font-size: 28px;
    color: #ff6600;
    border-bottom: 3px solid #ff6600;
    padding-bottom: 12px;
    margin-bottom: 30px;
    font-weight: 700;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.history-table th, .history-table td {
    border: 1px solid #f0d6b3;
    padding: 14px 15px;
    text-align: left;
}

.history-table thead {
    background: #ffe0b3;
    color: #333;
    font-weight: bold;
}

.history-table tbody tr:nth-child(even) {
    background: #fff7e6;
}

.history-table tbody tr:hover {
    background: #fff0d6;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

.btn-view {
    display: inline-block;
    background: #ff6600;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: #e05500;
    transform: scale(1.05);
}

.no-order {
    text-align: center;
    color: #777;
    font-size: 16px;
    margin-top: 30px;
}

.back-btn {
    text-align: center;
    margin-top: 30px;
}

.btn-back {
    display: inline-block;
    padding: 12px 25px;
    background: #999;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #777;
    transform: scale(1.05);
}
</style>
