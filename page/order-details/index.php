<?php
if(isset($_POST['confirm_order']) && !empty($_SESSION['cart'])){
    $_SESSION['order'] = [
        'tenkh' => $_POST['tenkh'],
        'diachi' => $_POST['diachi'],
        'sdt' => $_POST['sdt'],
        'cart' => $_SESSION['cart'],
        'tongtien' => array_sum(array_map(fn($item) => $item['gia'] * $item['soluong'], $_SESSION['cart']))
    ];

    unset($_SESSION['cart']);
}

if(isset($_SESSION['order'])){
    $order = $_SESSION['order'];
?>
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
        font-size: 32px;
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
    table {
        width: 100%;
        border-collapse: collapse;
    }
    thead {
        background: #ffe0b3;
        color: #333;
    }
    th, td {
        padding: 12px 10px;
        border: 1px solid #ddd;
        text-align: left;
        vertical-align: top;
    }
    th {
        font-weight: bold;
    }
    td.options span {
        display: inline-block;
        background: #fff3e6;
        color: #ff6600;
        padding: 2px 8px;
        margin: 2px 2px 2px 0;
        border-radius: 12px;
        font-size: 13px;
    }
    tr:hover {
        background: #fff7f0;
    }
    tfoot td {
        font-weight: bold;
        background: #f2f2f2;
        color: #ff6600;
        text-align: right;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
</style>

<div class="order-container">
    <h1 class="order-title">Chi Tiết Đơn Hàng</h1>

    <div class="customer-info">
        <p><b>Họ tên:</b> <?= htmlspecialchars($order['tenkh']) ?></p>
        <p><b>Địa chỉ:</b> <?= htmlspecialchars($order['diachi']) ?></p>
        <p><b>SĐT:</b> <?= htmlspecialchars($order['sdt']) ?></p>
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
                <?php foreach($order['cart'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['tensp']) ?></td>
                    <td class="options">
                        <?php
                        $options = [];
                        if(!empty($item['da'])) $options[] = "Đá: ".htmlspecialchars($item['da']);
                        if(!empty($item['duong'])) $options[] = "Đường: ".htmlspecialchars($item['duong']);
                        if(!empty($item['size'])) $options[] = "Size: ".htmlspecialchars($item['size']);
                        if(!empty($item['topping'])) $options[] = "Topping: ".htmlspecialchars($item['topping']);
                        if(!empty($item['ghichu'])) $options[] = "Ghi chú: ".htmlspecialchars($item['ghichu']);

                        if(!empty($options)){
                            foreach($options as $opt){
                                echo "<span>$opt</span>";
                            }
                        } else {
                            echo "<i style='color:#999;'>Không có</i>";
                        }
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
</div>
<?php } ?>
