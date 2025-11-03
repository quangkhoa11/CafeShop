<title>Chi tiết đươn hàng</title>
<?php
$title = "Chi tiết đơn hàng";
$obj = new database();

if (isset($_POST['confirm_order_orderdetails'])) {
    $cart = $_SESSION['cart'] ?? [];
    if (!empty($cart)) {
        $_SESSION['order'] = [
            'tenkh' => $_POST['tenkh'] ?? '',
            'diachi' => $_POST['diachi'] ?? '',
            'sdt' => $_POST['sdt'] ?? '',
            'cart' => array_map(fn($i) => [
                'idsp' => $i['idsp'],
                'idshop' => $i['idshop'] ?? 1,
                'tensp' => $i['tensp'],
                'gia' => $i['gia'],
                'soluong' => $i['soluong'],
                'da' => $i['da'] ?? '',
                'duong' => $i['duong'] ?? '',
                'size' => $i['size'] ?? '',
                'topping' => $i['topping'] ?? '',
                'ghichu' => $i['ghichu'] ?? ''
            ], $cart),
            'tongtien' => array_sum(array_map(fn($i) => $i['gia'] * $i['soluong'], $cart))
        ];
        unset($_SESSION['cart']);
        header('Location: index.php?page=payment');
        exit();
    } else {
        $error = "Giỏ hàng rỗng, không thể thanh toán!";
    }
}

$order = $_SESSION['order'] ?? null;
?>

<?php if ($order || !empty($_SESSION['cart'])): ?>
<div class="order-container">
    <h1 class="order-title">CHI TIẾT ĐƠN HÀNG</h1>

    <form method="POST" class="order-grid">
        <div class="customer-info">
            <h2>Thông tin khách hàng</h2>
            <p>
                <b>Họ tên:</b> 
                <input type="text" name="tenkh" value="<?= htmlspecialchars($order['tenkh'] ?? '') ?>" required>
            </p>
            <p>
                <b>Địa chỉ:</b> 
                <input type="text" name="diachi" value="<?= htmlspecialchars($order['diachi'] ?? '') ?>" required>
            </p>
            <p>
                <b>Số điện thoại:</b> 
                <input type="text" name="sdt" value="<?= htmlspecialchars($order['sdt'] ?? '') ?>" required>
            </p>
            <div class="note">
                <p>🛈 Vui lòng kiểm tra kỹ thông tin trước khi xác nhận thanh toán.</p>
            </div>
            <button type="submit" name="confirm_order_orderdetails" class="btn-save-order">Xác nhận thanh toán</button>
        </div>

        <div class="order-table-wrapper">
            <h2>Danh sách món</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Shop</th>
                        <th>Đá</th>
                        <th>Đường</th>
                        <th>Size</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $cart = $order['cart'] ?? $_SESSION['cart'] ?? [];
                    foreach ($cart as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['tensp']) ?></td>
                        <td>
                            <?php
                            $shopName = '';
                            if (isset($item['idshop'])) {
                                $shop = $obj->xuatdulieu("SELECT tenshop FROM shop WHERE idshop = ".$item['idshop']);
                                $shopName = !empty($shop) ? $shop[0]['tenshop'] : '-';
                            }
                            echo htmlspecialchars($shopName);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($item['da'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($item['duong'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($item['size'] ?? 'M') ?></td>
                        <td class="text-center"><?= $item['soluong'] ?></td>
                        <td class="text-right"><?= number_format($item['gia'] * $item['soluong']) ?>₫</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;">Tổng cộng</td>
                        <td class="text-right total-price">
                            <?= number_format(array_sum(array_map(fn($i) => $i['gia'] * $i['soluong'], $cart))) ?>₫
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </form>
</div>
<?php endif; ?>

<style>
.order-container {
    max-width: 1100px;
    margin: 40px auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, sans-serif;
    color: #333;
}
.order-title {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    color: #ff6600;
    border-bottom: 3px solid #ff6600;
    padding-bottom: 10px;
    margin-bottom: 30px;
}
.order-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 25px;
}
.customer-info {
    background: #fff8f0;
    border: 1px solid #ffc299;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(255, 102, 0, 0.1);
}
.customer-info h2 {
    color: #ff6600;
    font-size: 20px;
    margin-bottom: 15px;
}
.customer-info p {
    margin: 8px 0;
    font-size: 16px;
}
.customer-info input {
    width: 100%;
    padding: 8px 10px;
    margin-top: 4px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.note {
    margin-top: 15px;
    background: #fff0e0;
    padding: 10px;
    border-left: 4px solid #ff6600;
    font-size: 14px;
    color: #555;
}
.btn-save-order {
    display: block;
    margin: 25px auto 0;
    background-color: #28a745;
    color: white;
    font-weight: bold;
    font-size: 18px;
    padding: 12px 30px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    transition: background 0.3s;
}
.btn-save-order:hover {
    background-color: #218838;
}

.order-table-wrapper {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.order-table-wrapper h2 {
    color: #ff6600;
    font-size: 20px;
    margin-bottom: 10px;
}
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
}
thead {
    background: #ffe0b3;
}
th, td {
    padding: 10px 8px;
    border: 1px solid #ddd;
}
th {
    text-align: center;
}
.text-right { text-align: right; }
.text-center { text-align: center; }
tfoot td {
    font-weight: bold;
    background: #fff6e6;
}
.total-price {
    color: #ff6600;
    font-size: 18px;
    font-weight: bold;
}
</style>
