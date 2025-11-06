<title>Lịch sử đặt hàng</title>
<?php

if (!isset($_SESSION['idkh'])) {
    header("Location: index.php?page=login");
    exit;
}

$idkh = $_SESSION['idkh'];
$db = new database();

$donhang = $db->xuatdulieu("
    SELECT iddonban, ngayban, tongtien, trangthai
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
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donhang as $d): 
                    $statusClass = '';
                    switch ($d['trangthai']) {
                        case 'Chờ thanh toán': $statusClass = 'status-orange'; break;
                        case 'Đã đặt hàng': $statusClass = 'status-blue'; break;
                        case 'Đang xử lý': $statusClass = 'status-yellow'; break;
                        case 'Đã giao cho đơn vị vận chuyển': $statusClass = 'status-purple'; break;
                        case 'Hoàn thành': $statusClass = 'status-green'; break;
                        case 'Đã hủy': $statusClass = 'status-red'; break;
                        default: $statusClass = 'status-gray';
                    }
                ?>
                    <tr>
                        <td><?= htmlspecialchars($d['iddonban']) ?></td>
                        <td><?= date('d/m/Y', strtotime($d['ngayban'])) ?></td>
                        <td><?= number_format($d['tongtien'], 0, ',', '.') ?>₫</td>
                        <td><span class="status <?= $statusClass ?>"><?= htmlspecialchars($d['trangthai']) ?></span></td>
                        <td>
                            <a href="index.php?page=history&iddonban=<?= urlencode($d['iddonban']) ?>" class="btn-view">Xem</a>
                            <?php if ($d['trangthai'] === 'Chờ thanh toán'): ?>
                                <a href="index.php?page=payment&iddonban=<?= urlencode($d['iddonban']) ?>" class="btn-pay">Thanh toán</a>
                            <?php endif; ?>
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

.btn-view, .btn-pay {
    display: inline-block;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.btn-view {
    background: #ff6600;
    color: white;
}

.btn-view:hover {
    background: #e05500;
    transform: scale(1.05);
}

.btn-pay {
    background: #0099ff;
    color: white;
    margin-left: 5px;
}

.btn-pay:hover {
    background: #007acc;
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

.status {
    display: inline-block;
    padding: 6px 12px;
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
.status-orange { background: #fff3e0; color: #ff6600; }
</style>
