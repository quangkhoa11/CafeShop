<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
<title>Chi tiết đơn hàng</title>
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
        header("Location: index.php?page=payment");
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
                                                <td class="flex items-center gap-2">
                            <?php
                            $shopName = '';
                            $logoShop = 'assets/images/no-shop.png'; 

                            if (isset($item['idshop'])) {
                                $shop = $obj->xuatdulieu("SELECT tenshop, logo FROM shop WHERE idshop = ".$item['idshop']);
                                if (!empty($shop)) {
                                    $shopName = $shop[0]['tenshop'];
                                    if (!empty($shop[0]['logo'])) {
                                        $logoShop = $shop[0]['logo'];
                                    }
                                }
                            }
                            ?>
                            
                            <img src="assets/images/<?= htmlspecialchars($logoShop) ?>" 
                                alt="Logo shop"
                                class="w-8 h-8 rounded-full shadow border object-cover">

                            <span><?= htmlspecialchars($shopName) ?></span>
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
    padding: 25px;
    background: white;
    border-radius: 14px;
    border: 1px solid #eee;
    box-shadow: 0 4px 18px rgba(0,0,0,0.05);
}

.order-title {
    text-align: center;
    font-size: 32px;
    font-weight: bold;
    letter-spacing: 1px;
    color: #ff6600;
    padding-bottom: 14px;
    margin-bottom: 30px;
    border-bottom: 4px solid #ff6600;
}

.order-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.customer-info {
    background: linear-gradient(to bottom right, #fff4e6, #ffe8d1);
    border: 1px solid #ffcc99;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(255, 150, 60, 0.15);
}

.customer-info h2 {
    font-size: 22px;
    margin-bottom: 15px;
    color: #ff6600;
    font-weight: bold;
}

.customer-info input {
    width: 100%;
    margin-top: 6px;
    padding: 10px 12px;
    border: 1px solid #d7d7d7;
    background: #fff;
    border-radius: 8px;
    transition: 0.25s;
}

.customer-info input:focus {
    border-color: #ff8800;
    box-shadow: 0 0 0 3px rgba(255,140,0,0.2);
}

.note {
    margin-top: 15px;
    background: #fff6e9;
    padding: 12px;
    border-left: 4px solid #ff8800;
    border-radius: 8px;
    color: #555;
}

.btn-save-order {
    margin-top: 25px;
    width: 100%;
    padding: 14px;
    font-size: 18px;
    background: linear-gradient(to right, #28a745, #34c759);
    border: none;
    color: white;
    border-radius: 10px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}
.btn-save-order:hover {
    transform: translateY(-2px);
    background: linear-gradient(to right, #23963e, #2ebf53);
}

.order-table-wrapper {
    background: white;
    border: 1px solid #eee;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.05);
}

.order-table-wrapper h2 {
    font-size: 22px;
    color: #ff6600;
    margin-bottom: 12px;
    font-weight: bold;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 12px;
    overflow: hidden;
    font-size: 15px;
}

thead {
    background: linear-gradient(to right, #ffd1a1, #ffbb77);
}

thead th {
    padding: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

tbody tr {
    transition: background 0.2s;
}

tbody tr:hover {
    background: #fff9f1;
}

td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

tfoot td {
    padding: 12px;
    background: #fff4e2;
    font-weight: bold;
    font-size: 16px;
    border-top: 2px solid #ffca8a;
}

.total-price {
    color: #ff6600;
    font-size: 20px;
    font-weight: bold;
}
</style>
