<title>Thanh toán</title>
<?php
ob_start();
$title = "Thanh toán";
$obj = new database();

if (isset($_GET['rePay'])) {
    $iddonban = $_GET['rePay'];

    $don = $obj->xuatdulieu("SELECT * FROM donban WHERE iddonban = '$iddonban' LIMIT 1");
    $ct = $obj->xuatdulieu("SELECT * FROM chitietdonban WHERE iddonban = '$iddonban'");

    if ($don && $ct) {

        $_SESSION['repay_order_id'] = $iddonban;
        $_SESSION['order'] = [
            'tenkh' => $don[0]['tennguoinhan'],
            'sdt'   => $don[0]['sdtnguoinhan'],
            'diachi'=> $don[0]['diachinhan'],
            'tongtien'=> $don[0]['tongtien'],
            'cart'  => []
        ];

        foreach ($ct as $item) {
            $sp = $obj->xuatdulieu("SELECT tensp, idshop FROM sanpham WHERE idsp = '{$item['idsp']}' LIMIT 1");

            $_SESSION['order']['cart'][] = [
                'idsp' => $item['idsp'],
                'tensp'=> $sp[0]['tensp'],
                'idshop'=> $sp[0]['idshop'],
                'gia'   => $item['dongia'],
                'soluong'=> $item['soluong'],
                'size'  => $item['size'],
                'duong' => $item['duong'],
                'da'    => $item['da'],
                'ghichu'=> $item['ghichu']
            ];
        }

        header("Location: index.php?page=payment_back");
        exit();
    }
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

if (isset($_POST['pay_zalopay'])) {
    date_default_timezone_set('Asia/Ho_Chi_Minh');

    $idkh = $_SESSION['idkh'] ?? 1;
    $orderData = $_SESSION['order'];

    if (isset($_SESSION['repay_order_id'])) {
        $oldID = $_SESSION['repay_order_id'];
        $don = $obj->xuatdulieu("SELECT * FROM donban WHERE iddonban = '$oldID' LIMIT 1");
        if (!$don) die("Đơn hàng không tồn tại!");

        $totalAmount = $don[0]['tongtien'];

        $createdOrders = [$oldID];

    } else {
        $ordersByShop = [];
        foreach ($orderData['cart'] as $item) {
            $shopId = $item['idshop'];
            $ordersByShop[$shopId][] = $item;
        }

        $createdOrders = [];
        $totalAmount = 0;

        foreach ($ordersByShop as $shopId => $items) {
            $newID = generateOrderID($obj);
            $tongtien = array_sum(array_map(fn($i) => $i['gia'] * $i['soluong'], $items));
            $totalAmount += $tongtien;

            $sql_donban = "
                INSERT INTO donban (iddonban, idkh, ngayban, tennguoinhan, sdtnguoinhan, diachinhan, tongtien, idshop, trangthai)
                VALUES ('$newID', '$idkh', NOW(), '{$orderData['tenkh']}', '{$orderData['sdt']}', '{$orderData['diachi']}', '$tongtien', '$shopId', 'Chờ thanh toán')
            ";
            $obj->themxoasua($sql_donban);

            foreach ($items as $item) {
                $dongia = $item['gia'];
                $soluong = $item['soluong'];
                $thanhtien = $dongia * $soluong;

                $sql_chitiet = "
                    INSERT INTO chitietdonban (iddonban, idsp, duong, da, size, soluong, dongia, thanhtien, ghichu)
                    VALUES ('$newID', '{$item['idsp']}', '{$item['duong']}', '{$item['da']}', '{$item['size']}', $soluong, $dongia, $thanhtien, '{$item['ghichu']}')
                ";
                $obj->themxoasua($sql_chitiet);
            }

            $createdOrders[] = $newID;
        }
    }

    $config = [
        "app_id" => 2554,
        "key1" => "sdngKKJmqEMzvh5QQcdD2A9XBSKUNaYn",
        "key2" => "trMrHtvjo6myautxDUiAcYsVtaeQ8nhf",
        "endpoint" => "https://sb-openapi.zalopay.vn/v2/create"
    ];

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($basePath === "/" || $basePath === "\\") $basePath = "";

    $redirect = "$protocol://$host$basePath/index.php?page=return";
    $callback = "$protocol://$host$basePath/index.php?page=callback";

    $transID = rand(1000000, 9999999);
    $orderID = date("ymd") . "_" . $transID;

    $embeddata = json_encode([
        "redirecturl" => $redirect,
        "orders" => $createdOrders
    ]);

    $itemsZalo = json_encode([
        [
            "itemid" => "multi",
            "itemname" => "Thanh toán nhiều shop",
            "itemprice" => $totalAmount,
            "itemquantity" => 1
        ]
    ]);

    $orderZalo = [
        "app_id" => $config["app_id"],
        "app_trans_id" => $orderID,
        "app_user" => "user_the_dream",
        "app_time" => round(microtime(true) * 1000),
        "item" => $itemsZalo,
        "embed_data" => $embeddata,
        "amount" => $totalAmount,
        "description" => "Thanh toán tổng đơn hàng The Dream",
        "bank_code" => "",
        "callback_url" => $callback
    ];

    $dataMAC = $orderZalo["app_id"] . "|" . $orderZalo["app_trans_id"] . "|" . $orderZalo["app_user"] . "|" .
               $orderZalo["amount"] . "|" . $orderZalo["app_time"] . "|" . $orderZalo["embed_data"] . "|" . $orderZalo["item"];

    $orderZalo["mac"] = hash_hmac("sha256", $dataMAC, $config["key1"]);

    $context = stream_context_create([
        "http" => [
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "method" => "POST",
            "content" => http_build_query($orderZalo)
        ]
    ]);

    $response = file_get_contents($config["endpoint"], false, $context);
    $result = json_decode($response, true);

    if ($result["return_code"] == 1) {
        unset($_SESSION['order']);
        unset($_SESSION['repay_order_id']); 
        header("Location: " . $result["order_url"]);
        exit;
    } else {
        $error = "Không thể tạo thanh toán ZaloPay.";
    }
}
?>

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
