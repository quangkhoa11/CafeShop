<title>Thanh toán</title>
<?php
ob_start();
$title = "Thanh toán";
$obj = new database();

$idkh = $_SESSION['idkh'] ?? 0;
if (!empty($_SESSION['zalopay_pending'])) {
    unset($_SESSION['cart']);
    unset($_SESSION['order']);
    unset($_SESSION['zalopay_pending']); 
    echo "<script>
        alert('Đơn hàng của bạn đang ở trạng thái Chờ thanh toán!');
        window.location='index.php?page=menu_new';
    </script>";
    exit();
}

if (empty($_SESSION['order'])) {
    header('Location: index.php?page=order-details');
    exit();
}

$order = $_SESSION['order'];

function generateOrderID($obj) {
    $result = $obj->xuatdulieu("SELECT iddonban FROM donban ORDER BY iddonban DESC LIMIT 1");
    if (!empty($result)) {
        $lastID = $result[0]['iddonban'];
        $num = (int)substr($lastID, 2) + 1;
        return 'DB' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
    return 'DB001';
}

// --- Thanh toán COD ---
if (isset($_POST['pay_cod'])) {
    $orderData = $_SESSION['order'];
    $idkh = $_SESSION['idkh'] ?? 1;

    $ordersByShop = [];
    foreach ($orderData['cart'] as $item) {
        $shopId = $item['idshop'] ?? 1;
        $ordersByShop[$shopId][] = $item;
    }

    foreach ($ordersByShop as $shopId => $items) {
        $newID = generateOrderID($obj);
        $tongtien = array_sum(array_map(fn($i) => $i['gia'] * $i['soluong'], $items));
        $ngayban = date('Y-m-d H:i:s');

        $sql_donban = "
            INSERT INTO donban (iddonban, idkh, ngayban, tennguoinhan, sdtnguoinhan, diachinhan, tongtien, idshop, trangthai)
            VALUES ('$newID', '$idkh', '$ngayban', '{$orderData['tenkh']}', '{$orderData['sdt']}', '{$orderData['diachi']}', '$tongtien', '$shopId', 'Chờ xác nhận')
        ";
        $obj->themxoasua($sql_donban);

        foreach ($items as $item) {
            $dongia = (int)$item['gia'];
            $soluong = (int)$item['soluong'];
            $thanhtien = $dongia * $soluong;
            $sql_chitiet = "
                INSERT INTO chitietdonban (iddonban, idsp, duong, da, size, soluong, dongia, thanhtien, ghichu)
                VALUES ('$newID', '{$item['idsp']}', '{$item['duong']}', '{$item['da']}', '{$item['size']}', $soluong, $dongia, $thanhtien, '{$item['ghichu']}')
            ";
            $obj->themxoasua($sql_chitiet);
        }
    }

    unset($_SESSION['order']);
    echo "<script>alert('Đặt hàng thành công!'); window.location='index.php?page=menu_new';</script>";
    exit();
}

// --- Thanh toán ZaloPay tổng ---
if (isset($_POST['pay_zalopay'])) {
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $idkh = $_SESSION['idkh'] ?? 1;
    $orderData = $_SESSION['order'];

    $_SESSION['zalopay_pending'] = true;

    $config = [
        "app_id" => 2554,
        "key1" => "sdngKKJmqEMzvh5QQcdD2A9XBSKUNaYn",
        "key2" => "trMrHtvjo6myautxDUiAcYsVtaeQ8nhf",
        "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
    ];

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($basePath === '/' || $basePath === '\\') $basePath = '';
    $redirect = "$protocol://$host$basePath/index.php?page=return";
    $callback = "$protocol://$host$basePath/index.php?page=callback";

    // --- Tạo đơn shop riêng ---
    $ordersByShop = [];
    foreach ($orderData['cart'] as $item) {
        $shopId = $item['idshop'] ?? 1;
        $ordersByShop[$shopId][] = $item;
    }

    $createdOrders = [];
    $tongtien_all = 0;

    foreach ($ordersByShop as $shopId => $items) {
        $newID = generateOrderID($obj);
        $tongtien = array_sum(array_map(fn($i) => $i['gia'] * $i['soluong'], $items));
        $tongtien_all += $tongtien;

        $sql_donban = "
            INSERT INTO donban (iddonban, idkh, ngayban, tennguoinhan, sdtnguoinhan, diachinhan, tongtien, idshop, trangthai)
            VALUES ('$newID', '$idkh', NOW(), '{$orderData['tenkh']}', '{$orderData['sdt']}', '{$orderData['diachi']}', '$tongtien', '$shopId', 'Chờ thanh toán')
        ";
        $obj->themxoasua($sql_donban);

        foreach ($items as $item) {
            $dongia = (int)$item['gia'];
            $soluong = (int)$item['soluong'];
            $thanhtien = $dongia * $soluong;
            $sql_chitiet = "
                INSERT INTO chitietdonban (iddonban, idsp, duong, da, size, soluong, dongia, thanhtien, ghichu)
                VALUES ('$newID', '{$item['idsp']}', '{$item['duong']}', '{$item['da']}', '{$item['size']}', $soluong, $dongia, $thanhtien, '{$item['ghichu']}')
            ";
            $obj->themxoasua($sql_chitiet);
        }

        $createdOrders[] = $newID;
    }

    // --- Chuẩn bị ZaloPay ---
    $transID = rand(1000000, 9999999);
    $orderID = date("ymd") . "_" . $transID;

    $embeddata = json_encode([
        "redirecturl" => $redirect,
        "orders" => $createdOrders
    ]);

    $itemsZalo = [];
    foreach ($orderData['cart'] as $item) {
        $itemsZalo[] = [
            "itemid" => $item['idsp'],
            "itemname" => $item['tensp'],
            "itemprice" => $item['gia'],
            "itemquantity" => $item['soluong']
        ];
    }

    $orderZalo = [
        "app_id" => $config["app_id"],
        "app_trans_id" => $orderID,
        "app_user" => "user_the_dream",
        "app_time" => round(microtime(true) * 1000),
        "item" => json_encode($itemsZalo),
        "embed_data" => $embeddata,
        "amount" => $tongtien_all,
        "description" => "Thanh toán tổng đơn The Dream Shop",
        "bank_code" => "",
        "callback_url" => $callback
    ];

    $data = $orderZalo["app_id"] . "|" . $orderZalo["app_trans_id"] . "|" . $orderZalo["app_user"] . "|" .
             $orderZalo["amount"] . "|" . $orderZalo["app_time"] . "|" . $orderZalo["embed_data"] . "|" . $orderZalo["item"];
    $orderZalo["mac"] = hash_hmac("sha256", $data, $config["key1"]);

    $context = stream_context_create([
        "http" => [
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "method" => "POST",
            "content" => http_build_query($orderZalo)
        ]
    ]);

    $response = file_get_contents($config["endpoint"], false, $context);
    $result = json_decode($response, true);

    if (isset($result["return_code"]) && $result["return_code"] == 1) {
        header("Location: " . $result["order_url"]);
        exit();
    } else {
        $error = "Không thể tạo đơn thanh toán ZaloPay. Vui lòng thử lại.";
    }
}
?>

<!-- HTML hiển thị giống trước -->
<div class="payment-container">
    <h1 class="payment-title">XÁC NHẬN THANH TOÁN</h1>
    <?php if(isset($error)): ?>
        <p style="color:red; text-align:center;"><?= $error ?></p>
    <?php endif; ?>

    <div class="order-summary">
        <h3>Thông tin đơn hàng</h3>
        <p><b>Họ tên:</b> <?= htmlspecialchars($order['tenkh']) ?></p>
        <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['diachi']) ?></p>
        <p><b>Số điện thoại:</b> <?= htmlspecialchars($order['sdt']) ?></p>
        <p><b>Tổng tiền:</b> <?= number_format($order['tongtien']) ?>₫</p>
    </div>

    <div class="order-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th><th>Đá</th><th>Đường</th><th>Size</th><th>Ghi chú</th>
                    <th class="text-right">Giá</th><th class="text-center">SL</th><th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($order['cart'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['tensp']) ?></td>
                    <td><?= htmlspecialchars($item['da'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['duong'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($item['size'] ?? 'M') ?></td>
                    <td><?= htmlspecialchars($item['ghichu'] ?? '-') ?></td>
                    <td class="text-right"><?= number_format($item['gia']) ?>₫</td>
                    <td class="text-center"><?= $item['soluong'] ?></td>
                    <td class="text-right"><?= number_format($item['gia'] * $item['soluong']) ?>₫</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <form method="POST" class="payment-methods">
        <button type="submit" name="pay_cod" class="btn-pay">Thanh toán khi nhận hàng</button>
        <button type="submit" name="pay_zalopay" class="btn-pay btn-zalopay">Thanh toán ZaloPay</button>
    </form>
</div>

<style>
.payment-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}
.payment-title { 
    text-align: center; 
    font-size: 28px; 
    font-weight: bold; 
    color: #ff6600; 
    margin-bottom: 30px; 
}
.order-summary, .order-table-wrapper, .payment-methods {
    background:#fff; 
    border:1px solid #ddd; 
    border-radius:8px; 
    padding:20px; 
    margin-bottom:20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.order-summary p { margin:6px 0; font-size:16px; }
table { width:100%; border-collapse:collapse; font-size:15px; }
thead { background:#ffe0b3; color:#333; }
th, td { padding:10px 8px; border:1px solid #ddd; vertical-align: middle; }
th { text-align:center; }
.text-right { text-align:right; }
.text-center { text-align:center; }
.payment-methods { text-align:center; }
.btn-pay { display:inline-block; margin:10px; background-color:#28a745; color:#fff; font-weight:bold; font-size:16px; padding:12px 30px; border:none; border-radius:6px; cursor:pointer; }
.btn-pay:hover { background-color:#218838; }
.btn-zalopay { background-color:#ff6600; }
.btn-zalopay:hover { background-color:#e65c00; }
</style>

<?php ob_end_flush(); ?>