<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $iddonban = $_POST['iddonban'] ?? 0;
    $trangthai = $_POST['trangthai'] ?? '';

    if ($iddonban && $trangthai) {
        $sqlUpdate = "
            UPDATE donban
            SET trangthai = '$trangthai'
            WHERE iddonban = '$iddonban' AND idshop = '$idshop'
        ";
        if ($db->themxoasua($sqlUpdate)) {
            echo "<script>alert('✅ Cập nhật trạng thái thành công!'); window.location='index.php?page=shop_orders';</script>";
            exit;
        } else {
            echo "<script>alert('❌ Lỗi khi cập nhật trạng thái!');</script>";
        }
    }
}

$sql = "
    SELECT db.*, kh.tenkh 
    FROM donban db
    JOIN khachhang kh ON db.idkh = kh.idkh
    WHERE db.idshop = '$idshop'
    ORDER BY db.ngayban DESC
";
$donhangs = $db->xuatdulieu($sql);
?>

<title>Quản lý đơn hàng</title>

<div class="orders-container">
    <h2 class="orders-title">📦 Quản lý đơn hàng</h2>

    <?php if (!$donhangs || count($donhangs) == 0): ?>
        <div class="no-orders">😢 Hiện chưa có đơn hàng nào.</div>
    <?php else: ?>
        <div class="orders-table-wrapper">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Người nhận</th>
                        <th>SĐT nhận</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donhangs as $i => $dh): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($dh['tenkh']) ?></td>
                            <td><?= date("d/m/Y", strtotime($dh['ngayban'])) ?></td>
                            <td><?= htmlspecialchars($dh['tennguoinhan']) ?></td>
                            <td><?= htmlspecialchars($dh['sdtnguoinhan']) ?></td>
                            <td class="price"><?= number_format($dh['tongtien'], 0, ',', '.') ?>₫</td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="iddonban" value="<?= $dh['iddonban'] ?>">
                                    <select name="trangthai" class="status-select">
                                        <?php
                                        $statuses = [
                                            'Đã đặt hàng',
                                            'Đang xử lý',
                                            'Đã giao cho đơn vị vận chuyển',
                                            'Hoàn thành',
                                            'Đã hủy'
                                        ];
                                        foreach ($statuses as $status):
                                        ?>
                                            <option value="<?= $status ?>" <?= ($dh['trangthai'] === $status) ? 'selected' : '' ?>>
                                                <?= $status ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-save">💾</button>
                                </form>
                            </td>
                            <td class="action">
                                <a href="index.php?page=shop_order_detail&iddonban=<?= $dh['iddonban'] ?>" class="btn-view">Xem</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<style>
    .orders-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', sans-serif;
}

.orders-title {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
}

.no-orders {
    text-align: center;
    color: #888;
    padding: 40px 0;
    background: #f8f8f8;
    border-radius: 10px;
    font-size: 16px;
}

.orders-table-wrapper {
    overflow-x: auto;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.orders-table thead {
    background: linear-gradient(90deg, #f97316, #ff8c42);
    color: white;
    font-weight: bold;
}

.orders-table th, 
.orders-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #eee;
}

.price {
    color: #e86b1f;
    font-weight: 700;
}

.status-form {
    display: flex;
    align-items: center;
    gap: 6px;
}

.status-select {
    padding: 5px 6px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 13px;
}

.btn-save {
    background: #ff7b00;
    border: none;
    color: white;
    padding: 6px 8px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: 0.2s;
}

.btn-save:hover {
    background: #e86b1f;
}

.btn-view {
    background: #007bff;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: 0.2s;
}

.btn-view:hover {
    background: #0056b3;
}

.action {
    text-align: center;
}

</style>