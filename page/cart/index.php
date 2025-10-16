<?php
$obj = new database();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
    $idsp = $_POST['idsp'];
    $tensp = $_POST['tensp'];
    $gia = $_POST['gia'];
    $hinhanh = $_POST['hinhanh'];
    $idloai = $_POST['idloai'] ?? 0;
    $soluong = max(1, (int)$_POST['soluong']);
    $da = $_POST['da'] ?? '';
    $duong = $_POST['duong'] ?? '';
    $ghichu = $_POST['ghichu'] ?? '';
    $size = $_POST['size'] ?? '';
    $topping = $_POST['topping'] ?? '';

    if (isset($_SESSION['cart'][$idsp])) {
        $_SESSION['cart'][$idsp]['soluong'] += $soluong;
    } else {
        $_SESSION['cart'][$idsp] = [
            'tensp' => $tensp,
            'gia' => $gia,
            'hinhanh' => $hinhanh,
            'soluong' => $soluong,
            'idloai' => $idloai,
            'da' => $da,
            'duong' => $duong,
            'size' => $size,
            'topping' => $topping,
            'ghichu' => $ghichu
        ];
    }
}
?>

<main class="flex-1 py-10 container mx-auto px-4">
  <div style="padding-top: 40px;"><h1 class="text-3xl font-bold text-center text-orange-600 mb-6 drop-shadow">GIỎ HÀNG</h1></div>

  <?php if (!empty($_SESSION['cart'])): ?>
  <div id="cart-container" class="table-wrapper overflow-x-auto bg-white shadow-lg rounded-lg p-5 border border-gray-200">
    <table class="w-full border-collapse text-sm text-gray-700" id="cart-table">
      <thead>
        <tr class="bg-orange-100 text-gray-800 uppercase text-xs tracking-wide">
          <th class="p-3 border">Hình ảnh</th>
          <th class="p-3 border">Tên sản phẩm</th>
          <th class="p-3 border">Tùy chọn</th>
          <th class="p-3 border">Giá</th>
          <th class="p-3 border">Số lượng</th>
          <th class="p-3 border">Thành tiền</th>
          <th class="p-3 border">Xóa</th>
        </tr>
      </thead>
      <tbody>
        <?php $total = 0; ?>
        <?php foreach ($_SESSION['cart'] as $idsp => $item): 
          $subtotal = $item['gia'] * $item['soluong'];
          $total += $subtotal;
        ?>
        <tr class="hover:bg-orange-50 transition duration-200" data-id="<?php echo $idsp; ?>">
          <td class="p-3 border text-center">
            <img src="assets/images/<?php echo htmlspecialchars($item['hinhanh']); ?>" class="w-16 h-16 object-cover rounded-md mx-auto shadow-sm border border-gray-200">
          </td>
          <td class="p-3 border font-semibold text-gray-800 text-center"><?php echo htmlspecialchars($item['tensp']); ?></td>
          <td class="p-3 border text-left leading-snug">
            <?php
              $ghichuArr = [];
              if (!empty($item['da'])) $ghichuArr[] = "<b>Đá:</b> " . htmlspecialchars($item['da']);
              if (!empty($item['duong'])) $ghichuArr[] = "<b>Đường:</b> " . htmlspecialchars($item['duong']);
              if (!empty($item['size'])) $ghichuArr[] = "<b>Size:</b> " . htmlspecialchars($item['size']);
              if (!empty($item['topping'])) $ghichuArr[] = "<b>Topping:</b> " . htmlspecialchars($item['topping']);
              if (!empty($item['ghichu'])) $ghichuArr[] = "<b>Ghi chú:</b> " . htmlspecialchars($item['ghichu']);
              echo !empty($ghichuArr) ? implode("<br>", $ghichuArr) : "<span class='text-gray-400 italic'>Không có</span>";
            ?>
          </td>
          <td class="p-3 border text-center text-orange-600 font-bold"><?php echo number_format($item['gia']); ?>₫</td>
          <td class="p-3 border text-center">
            <input type="number" min="1" value="<?php echo $item['soluong']; ?>" 
                   class="w-16 text-center border border-gray-300 rounded-lg py-1 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 quantity-input"
                   data-price="<?php echo $item['gia']; ?>">
          </td>
          <td class="p-3 border text-center font-semibold text-gray-800 subtotal"><?php echo number_format($subtotal); ?>₫</td>
          <td class="p-3 border text-center">
            <button type="button" onclick="removeItem('<?php echo $idsp; ?>')" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-full text-xs shadow transition duration-200">✖</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="text-right text-lg font-semibold p-3 text-gray-800">
      <b>Tổng cộng:</b> <span id="total" class="text-orange-600"><b><?php echo number_format($total); ?> ₫</b></span>
    </div>

    <div class="text-right mr-3">
<?php if (isset($_SESSION['idkh'])): ?>
    <div class="text-right mt-4">
        <button style="border-radius: 3px;" id="checkoutBtn" class="bg-orange-500 text-white font-bold py-2 px-4 rounded">
            Thanh toán
        </button>
    </div>

    <div id="checkoutForm" class="hidden checkout-form">
        <h2>Thông tin giao hàng</h2>
        <form id="formCheckout" method="post" action="index.php?page=order-details">
            <div class="form-group">
                <label>Họ và tên:</label>
                <input type="text" name="tenkh" required placeholder="Nhập họ và tên">
            </div>
            <div class="form-group">
                <label>Địa chỉ:</label>
                <input type="text" name="diachi" required placeholder="Nhập địa chỉ giao hàng">
            </div>
            <div class="form-group">
                <label>Số điện thoại:</label>
                <input type="tel" name="sdt" required pattern="[0-9]{10}" placeholder="Nhập số điện thoại">
            </div>
            <button type="submit" name="confirm_order">Xác nhận đơn hàng</button>
        </form>
    </div>
<?php else: ?>
    <p class="text-center text-red-500 font-semibold mt-4">
        Vui lòng <a href="index.php?page=login" class="underline text-orange-500">đăng nhập</a> để thanh toán
    </p>
<?php endif; ?>
</div>

  </div>

  <?php else: ?>
    <p class="text-center text-gray-500 mt-8 text-lg italic">Giỏ hàng hiện tại trống 😢</p>
  <?php endif; ?>
</main>

<script>
  document.getElementById('checkoutBtn')?.addEventListener('click', () => {
    const form = document.getElementById('checkoutForm');
    form.classList.toggle('hidden');
    form.scrollIntoView({ behavior: 'smooth' });
});
document.querySelectorAll('.quantity-input').forEach(input => {
  input.addEventListener('input', () => {
    let value = input.value.replace(/\D/g, '');
    if (value === '' || parseInt(value) <= 0) value = 1;
    input.value = value;

    const price = parseFloat(input.dataset.price);
    const quantity = parseInt(value);
    const subtotalCell = input.closest('tr').querySelector('.subtotal');
    const subtotal = price * quantity;
    subtotalCell.textContent = subtotal.toLocaleString('vi-VN') + '₫';
    updateTotal();
  });
});

function updateTotal() {
  let total = 0;
  document.querySelectorAll('.subtotal').forEach(cell => {
    total += parseFloat(cell.textContent.replace(/[^\d]/g, '')) || 0;
  });
  document.getElementById('total').textContent = total.toLocaleString('vi-VN');
}

function removeItem(idsp) {
  if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

  const row = document.querySelector(`tr[data-id="${idsp}"]`);
  if (row) {
    row.remove();
    fetch('page/cart/remove_item.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'idsp=' + encodeURIComponent(idsp)
    });

    updateTotal();

    const toast = document.createElement('div');
    toast.textContent = "✅ Đã xóa sản phẩm khỏi giỏ hàng";
    toast.className = "fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded shadow-lg text-sm z-50 animate-fade";
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
  }
}
</script>

<style>
  .checkout-form {
    background-color: #fff;
    padding: 25px;
    margin-top: 20px;
    border-radius: 10px;
    border: 1px solid #ddd;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    transition: all 0.3s ease;
}

.checkout-form h2 {
    font-size: 20px;
    color: #ff6600;
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 2px solid #ff6600;
    padding-bottom: 8px;
}

.checkout-form .form-group {
    margin-bottom: 15px;
}

.checkout-form label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #333;
}

.checkout-form input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.checkout-form input:focus {
    border-color: #ff6600;
    box-shadow: 0 0 5px rgba(255,102,0,0.3);
    outline: none;
}

.checkout-form button {
    display: block;
    width: 100%;
    padding: 12px;
    background-color: #28a745;
    color: #fff;
    font-weight: bold;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.checkout-form button:hover {
    background-color: #218838;
}
@keyframes fade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade { animation: fade 0.3s ease-in-out; }
.table-wrapper {
  overflow-x: auto;
  max-width: 100%;
}
</style>

