<?php
$obj = new database();

if (isset($_POST['confirm_order']) && !empty($_SESSION['cart'])) {
    $_SESSION['order'] = [
        'tenkh' => $_POST['tenkh'],
        'diachi' => $_POST['diachi'],
        'sdt' => $_POST['sdt'],
        'cart' => $_SESSION['cart'],
        'tongtien' => array_sum(array_map(fn($i) => $i['gia'] * $i['soluong'], $_SESSION['cart']))
    ];
    unset($_SESSION['cart']);
}

function generateOrderID($obj) {
    $result = $obj->xuatdulieu("SELECT iddonban FROM donban ORDER BY iddonban DESC LIMIT 1");
    if (!empty($result)) {
        $lastID = $result[0]['iddonban'];
        $num = (int)substr($lastID, 2) + 1;
        return 'DB' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    return 'DB001';
}

if (isset($_POST['save_order']) && isset($_SESSION['order'])) {
    $order = $_SESSION['order'];
    $idkh = $_SESSION['idkh'] ?? 1; 

    $last = $obj->xuatdulieu("SELECT iddonban FROM donban ORDER BY iddonban DESC LIMIT 1");
    if (!empty($last)) {
        $lastID = $last[0]['iddonban'];
        $num = (int)substr($lastID, 2) + 1;
        $newID = 'DB' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $newID = 'DB001';
    }

    $ten = $order['tenkh'];
    $dc = $order['diachi'];
    $sdt = $order['sdt'];
    $tong = $order['tongtien'];
    $ngayban = date('Y-m-d H:i:s');

    $obj->xuatdulieu("
        INSERT INTO donban (iddonban, idkh, ngayban, tennguoinhan, sdtnguoinhan, diachinhan, tongtien)
        VALUES ('$newID', '$idkh', '$ngayban', '$ten', '$sdt', '$dc', '$tong')
    ");

    foreach ($order['cart'] as $item) {
        $idsp = $item['idsp'];
        $soluong = $item['soluong'];
        $dongia = $item['gia'];
        $thanhtien = $dongia * $soluong;

        $obj->xuatdulieu("
            INSERT INTO chitietdonban (iddonban, idsp, soluong, dongia, thanhtien)
            VALUES ('$newID', '$idsp', '$soluong', '$dongia', '$thanhtien')
        ");
    }

    unset($_SESSION['order']);
    echo "<script>alert('Đặt hàng thành công!'); window.location='index.php';</script>";
}


if (isset($_SESSION['order'])) {
    $order = $_SESSION['order'];
?>
<div class="order-container">
    <h1 class="order-title">CHI TIẾT ĐƠN HÀNG</h1>

    <div class="customer-info">
        <p><b>Họ tên:</b> <?= htmlspecialchars($order['tenkh']) ?></p>
        <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['diachi']) ?></p>
        <p><b>Số điện thoại:</b> <?= htmlspecialchars($order['sdt']) ?></p>
    </div>

    <div class="order-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Tùy chọn</th>
                    <th class="text-right">Giá</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['cart'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['tensp']) ?></td>
                    <td class="options">
                        <?php
                        $opts = [];
                        if(!empty($item['da'])) $opts[] = "Đá: ".htmlspecialchars($item['da']);
                        if(!empty($item['duong'])) $opts[] = "Đường: ".htmlspecialchars($item['duong']);
                        if(!empty($item['size'])) $opts[] = "Size: ".htmlspecialchars($item['size']);
                        if(!empty($item['topping'])) $opts[] = "Topping: ".htmlspecialchars($item['topping']);
                        if(!empty($item['ghichu'])) $opts[] = "Ghi chú: ".htmlspecialchars($item['ghichu']);
                        echo !empty($opts) ? implode("<br>", $opts) : "<i style='color:#999;'>Không có</i>";
                        ?>
                    </td>
                    <td class="text-right"><?= number_format($item['gia']) ?>₫</td>
                    <td class="text-center"><?= $item['soluong'] ?></td>
                    <td class="text-right"><?= number_format($item['gia'] * $item['soluong']) ?>₫</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Tổng cộng</td>
                    <td><?= number_format($order['tongtien']) ?>₫</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <form method="POST">
    <button type="submit" name="save_order" class="btn-save-order">Xác nhận đặt hàng</button>
</form>
</div>
<?php } ?>

<style>
.order-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
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
.customer-info, .order-table-wrapper {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.customer-info p {
    margin: 6px 0;
    font-size: 16px;
}
table { width: 100%; border-collapse: collapse; }
thead { background: #ffe0b3; color: #333; }
th, td {
    padding: 12px 10px;
    border: 1px solid #ddd;
    vertical-align: top;
}
tfoot td {
    font-weight: bold;
    background: #f2f2f2;
    color: #ff6600;
    text-align: right;
}
.btn-save-order {
    display: block;
    margin: 30px auto 0;
    background-color: #28a745;
    color: white;
    font-weight: bold;
    font-size: 18px;
    padding: 12px 40px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
.btn-save-order:hover { background-color: #218838; }
</style>
