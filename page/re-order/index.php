<title>Chi tiết đơn hàng</title>
<?php
$obj = new database();
if (isset($_GET['iddonban'])) {
    $iddonban = $_GET['iddonban'];

    $doncu = $obj->xuatdulieu("SELECT * FROM donban WHERE iddonban = '$iddonban'");
    $chitietcu = $obj->xuatdulieu("
        SELECT sp.idsp, sp.tensp, sp.hinhanh, 
               ctdb.da, ctdb.duong, ctdb.size, 
               ctdb.soluong, ctdb.dongia, ctdb.ghichu
        FROM chitietdonban ctdb
        JOIN sanpham sp ON ctdb.idsp = sp.idsp
        WHERE ctdb.iddonban = '$iddonban'
    ");

    if (!empty($doncu) && !empty($chitietcu)) {
        $doncu = $doncu[0];

        $_SESSION['order'] = [
            'tenkh' => $doncu['tennguoinhan'],
            'diachi' => $doncu['diachinhan'],
            'sdt' => $doncu['sdtnguoinhan'],
            'cart' => array_map(function ($item) {
                return [
                    'idsp' => $item['idsp'],
                    'tensp' => $item['tensp'],
                    'gia' => $item['dongia'],
                    'soluong' => $item['soluong'],
                    'da' => $item['da'],
                    'duong' => $item['duong'],
                    'size' => $item['size'],
                    'ghichu' => $item['ghichu'] ?? '',
                    'hinhanh' => $item['hinhanh'] ?? ''
                ];
            }, $chitietcu),
            'tongtien' => array_sum(array_map(fn($i) => $i['dongia'] * $i['soluong'], $chitietcu))
        ];
    }
}

function generateOrderID($obj)
{
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
    $newID = generateOrderID($obj);

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
        $da = $item['da'];
        $duong = $item['duong'];
        $size = $item['size'];
        $soluong = $item['soluong'];
        $dongia = $item['gia'];
        $ghichu = $item['ghichu'] ?? '';
        $thanhtien = $dongia * $soluong;

        $obj->xuatdulieu("
            INSERT INTO chitietdonban (iddonban, idsp, da, duong, size, soluong, dongia, thanhtien, ghichu)
            VALUES ('$newID', '$idsp', '$da', '$duong', '$size', '$soluong', '$dongia', '$thanhtien', '$ghichu')
        ");
    }

    unset($_SESSION['order']);
    echo "<script>alert('Đặt hàng thành công!'); window.location='index.php?page=menu';</script>";
    exit;
}

if (isset($_POST['save_cart_before_menu']) && isset($_SESSION['order']['cart'])) {
    foreach ($_SESSION['order']['cart'] as $item) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$item['idsp']])) {
            $_SESSION['cart'][$item['idsp']]['soluong'] += $item['soluong'];
        } else {
            $_SESSION['cart'][$item['idsp']] = $item;
        }
    }
    header('Location: index.php?page=menu');
    exit;
}

?>

<?php if (isset($_SESSION['order'])): 
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
                    <th>Đá</th>
                    <th>Đường</th>
                    <th>Size</th>
                    <th>Ghi chú</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-right">Giá</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['cart'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['tensp']) ?></td>
                    <td><?= htmlspecialchars($item['da']) ?></td>
                    <td><?= htmlspecialchars($item['duong']) ?></td>
                    <td><?= htmlspecialchars($item['size']) ?></td>
                    <td><?= htmlspecialchars($item['ghichu']) ?></td>
                    <td class="text-center"><?= $item['soluong'] ?></td>
                    <td class="text-right"><?= number_format($item['gia']) ?>₫</td>
                    <td class="text-right"><?= number_format($item['gia'] * $item['soluong']) ?>₫</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">Tổng cộng</td>
                    <td><?= number_format($order['tongtien']) ?>₫</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <form method="POST">
        <input type="hidden" name="save_cart_before_menu" value="1">
        <button type="submit" class="btn-save-order">➕ Thêm món</button>
    </form>

    <form method="POST">
        <button type="submit" name="save_order" class="btn-save-order">Xác nhận đặt hàng</button>
    </form>
</div>
<?php else: ?>
<p style="text-align:center; margin-top:40px;">Không có thông tin đơn hàng để hiển thị.</p>
<?php endif; ?>

<style>
.order-container {
    max-width: 900px;
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
    padding: 10px 8px;
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
    margin: 20px auto 0;
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
