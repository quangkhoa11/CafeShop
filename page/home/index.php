<?php
$db = new database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
    $idsp = $_POST['idsp'];
    $tensp = $_POST['tensp'];
    $gia = $_POST['gia'];
    $hinhanh = $_POST['hinhanh'];
    $soluong = (int)$_POST['soluong'];
    $da = $_POST['da'];
    $duong = $_POST['duong'];
    $size = $_POST['size'];
    $ghichu = trim($_POST['ghichu']);

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $cart_key = md5($idsp . $da . $duong . $size . $ghichu);
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['soluong'] += $soluong;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'idsp' => $idsp,
            'tensp' => $tensp,
            'gia' => $gia,
            'hinhanh' => $hinhanh,
            'soluong' => $soluong,
            'da' => $da,
            'duong' => $duong,
            'size' => $size,
            'ghichu' => $ghichu
        ];
    }

    echo json_encode(['success' => true]);
    exit;
}
?>

<title>Trang chủ</title>
<main class="flex-1 container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
        <div>
            <h1 class="text-4xl font-extrabold leading-tight">
                Chào mừng đến với <span class="text-orange-600">The Dream</span>
            </h1>
            <p class="mt-4 text-gray-600">
                Cà phê là thức uống phổ biến toàn cầu, được chế biến từ hạt rang của cây cà phê. 
                Cà phê không chỉ là thức uống mà còn là phong cách sống mang lại nhiều cảm hứng và năng lượng mỗi ngày.
            </p>
            <div class="mt-6 flex gap-3">
                <a href="index.php?page=menu" class="inline-block px-5 py-3 rounded-lg bg-indigo-600 text-white font-medium">Xem Menu</a>
                <a href="index.php?page=contact" class="inline-block px-5 py-3 rounded-lg border hover:bg-orange-500 hover:text-white">Liên hệ</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-center">
            <img src="./assets/images/cafepro.jpg" alt="" class="rounded-lg max-w-full h-auto" />
        </div>
    </div>

    <?php
    $sql = "SELECT * FROM sanpham sp 
            JOIN loaisp lsp ON sp.idloai = lsp.idloai 
            WHERE sp.idloai = 3 
            ORDER BY RAND() 
            LIMIT 3";
    $monbanchay = $db->xuatdulieu($sql);
    ?>

    <section class="mt-12 container mx-auto px-4">
        <h2 class="text-2xl font-semibold mb-6 text-center text-amber-700">☕ Món Bán Chạy</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($monbanchay && count($monbanchay) > 0): ?>
                <?php foreach ($monbanchay as $mon): ?>
                    <div 
                        class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-transform hover:scale-105 p-4 cursor-pointer"
                        onclick="openModal('<?php echo $mon['idsp']; ?>','<?php echo addslashes($mon['tensp']); ?>','<?php echo $mon['gia']; ?>','<?php echo $mon['hinhanh']; ?>')"
                    >
                        <div class="h-60 flex items-center justify-center">
                            <img class="h-full w-full object-cover rounded-lg" 
                                 src="./assets/images/<?php echo $mon['hinhanh']; ?>" 
                                 alt="<?php echo $mon['tensp']; ?>">
                        </div>
                        <h3 class="mt-3 font-medium text-center text-gray-800"><?php echo $mon['tensp']; ?></h3>
                        <p class="text-center text-amber-600 font-semibold mt-1"><?php echo number_format($mon['gia']); ?> VNĐ</p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-span-3 text-center text-gray-500">Hiện chưa có sản phẩm nào trong danh sách bán chạy.</p>
            <?php endif; ?>
        </div>
    </section>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closeModal()">✖</button>

            <img id="modalImage" src="" alt="Sản phẩm">
            <h3 id="modalName"></h3>
            <p id="modalPrice"></p>

            <form id="modalForm" method="POST" class="modal-form">
                <input type="hidden" name="idsp" id="modalId">
                <input type="hidden" name="tensp" id="modalNameInput">
                <input type="hidden" name="gia" id="modalPriceInput">
                <input type="hidden" name="hinhanh" id="modalImgInput">

                <div class="option-group">
                    <p>Lượng đá:</p>
                    <label><input type="radio" name="da" value="Không đá" checked> Không đá</label>
                    <label><input type="radio" name="da" value="Đá riêng"> Đá riêng</label>
                    <label><input type="radio" name="da" value="Đá chung"> Đá chung</label>
                </div>

                <div class="option-group">
                    <p>Lượng đường:</p>
                    <label><input type="radio" name="duong" value="Ít đường" checked> Ít</label>
                    <label><input type="radio" name="duong" value="Vừa đường"> Vừa</label>
                    <label><input type="radio" name="duong" value="Nhiều đường"> Nhiều</label>
                </div>

                <div class="option-group">
                    <p>Size</p>
                    <label><input type="radio" name="size" value="M" checked> Size M</label>
                    <label><input type="radio" name="size" value="L"> Size L</label>
                </div>



                <div class="option-group">
                    <p>Ghi chú:</p>
                    <textarea name="ghichu" placeholder="Ví dụ: không đá, thêm sữa..." class="w-full border rounded-lg p-2 text-sm"></textarea>
                </div>

                <div class="option-group">
                    <p>Số lượng:</p>
                    <input id="modalQty" type="number" name="soluong" value="1" min="1"
                           class="w-full text-center border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <button type="submit" name="add_cart" class="submit-btn">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>

    <div id="toast" class="fixed top-6 right-6 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg hidden transition-opacity duration-500">
        ✅ Thêm vào giỏ hàng thành công!
    </div>

    <link rel="stylesheet" href="assets/css/modal.css">

<style>
#cartMessage.show {
  opacity: 1;
  transform: translateY(0);
}
</style>

<div id="cartMessage"></div>

<script>
function openModal(idsp, name, price, img) {
  const modal = document.getElementById('productModal');
  modal.style.display = 'flex';
  document.getElementById('modalImage').src = './assets/images/' + img;
  document.getElementById('modalName').innerText = name;
  document.getElementById('modalPrice').innerText = new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';
  
  document.getElementById('modalId').value = idsp;
  document.getElementById('modalNameInput').value = name;
  document.getElementById('modalPriceInput').value = price;
  document.getElementById('modalImgInput').value = img;
  document.getElementById('modalQty').value = 1;
}

function closeModal() {
  document.getElementById('productModal').style.display = 'none';
}

function showCartMessage(message) {
  const msg = document.getElementById('cartMessage');
  msg.innerText = message;
  msg.classList.add('show');
  msg.style.display = 'block';
  setTimeout(() => {
    msg.classList.remove('show');
    setTimeout(() => msg.style.display = 'none', 300);
  }, 2000);
}

document.getElementById('modalForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const formData = new FormData(this);
  formData.append('add_cart', '1');

  try {
    const response = await fetch(window.location.href, {
      method: 'POST',
      body: formData
    });
    const text = await response.text();

    closeModal();
    showCartMessage('✅ Đã thêm sản phẩm vào giỏ hàng!');
  } catch (error) {
    showCartMessage('❌ Lỗi! Không thể thêm sản phẩm.');
  }
});
</script>

</main>

