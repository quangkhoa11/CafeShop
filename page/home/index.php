<?php
$db = new database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
    ob_clean();

    $idsp = $_POST['idsp'];
    $tensp = $_POST['tensp'];
    $gia = (int)$_POST['gia'];
    $hinhanh = $_POST['hinhanh'];
    $idshop = $_POST['idshop'] ?? 1;
    $soluong = (int)$_POST['soluong'];
    $da = $_POST['da'] ?? '';
    $duong = $_POST['duong'] ?? '';
    $size = $_POST['size'] ?? 'M';
    $ghichu = trim($_POST['ghichu'] ?? '');

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $cart_key = md5($idsp . $da . $duong . $size . $ghichu);
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['soluong'] += $soluong;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'idsp' => $idsp,
            'idshop' => $idshop,
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

    header('Content-Type: application/json');
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
$sql = "SELECT sp.*, lsp.tenloai, s.tenshop, s.logo 
        FROM sanpham sp
        JOIN loaisp lsp ON sp.idloai = lsp.idloai
        JOIN shop s ON sp.idshop = s.idshop
        WHERE sp.idloai = 3
        ORDER BY RAND()
        LIMIT 3";
$monbanchay = $db->xuatdulieu($sql);
?>

<section class="monbanchay">
    <h2 class="title-section">☕ Món Bán Chạy</h2>

    <div class="sanpham-grid">
        <?php if ($monbanchay && count($monbanchay) > 0): ?>
            <?php foreach ($monbanchay as $mon): ?>
                <div class="sanpham-item" 
                    onclick="openModal('<?php echo $mon['idsp']; ?>',
                   '<?php echo addslashes($mon['tensp']); ?>',
                   '<?php echo $mon['gia']; ?>',
                   '<?php echo $mon['hinhanh']; ?>',
                   '<?php echo $mon['idshop']; ?>')">

                    
                    <div class="sanpham-img-wrap">
                        <img src="./assets/images/<?php echo $mon['hinhanh']; ?>" 
                             alt="<?php echo $mon['tensp']; ?>" 
                             class="sanpham-img">

                        <div class="shop-info">
                            <img src="assets/images/<?php echo $mon['logo']; ?>" 
                                 alt="<?php echo $mon['tenshop']; ?>" 
                                 class="shop-logo">
                            <span class="shop-name"><?php echo $mon['tenshop']; ?></span>
                        </div>
                    </div>

                    <h3 class="sanpham-name"><?php echo $mon['tensp']; ?></h3>
                    <p class="sanpham-price"><?php echo number_format($mon['gia']); ?> VNĐ</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-products">Hiện chưa có sản phẩm nào trong danh sách bán chạy.</p>
        <?php endif; ?>
    </div>
</section>


    <div id="productModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closeModal()">✖</button>

            <img id="modalImage" src="" alt="Sản phẩm">
            <h3 id="modalName"></h3>
            <p id="modalPrice" class="text-orange-600 font-bold text-lg"></p>

            <form id="modalForm" method="POST" class="modal-form">
                <input type="hidden" name="idsp" id="modalId">
                <input type="hidden" name="tensp" id="modalNameInput">
                <input type="hidden" name="gia" id="modalPriceInput">
                <input type="hidden" name="hinhanh" id="modalImgInput">
                <input type="hidden" name="idshop" id="modalShopId">


                <div class="option-group">
                    <p>Lượng đá:</p>
                    <label><input type="radio" name="da" value="Không đá" checked> Không đá</label>
                    <label><input type="radio" name="da" value="Đá riêng"> Đá riêng</label>
                    <label><input type="radio" name="da" value="Đá chung"> Đá chung</label>
                </div>

                <div class="option-group">
                    <p>Lượng đường:</p>
                    <label><input type="radio" name="duong" value="Không đường" checked> Không đường</label>
                    <label><input type="radio" name="duong" value="Ít đường"> Ít</label>
                    <label><input type="radio" name="duong" value="Vừa đường"> Vừa</label>
                    <label><input type="radio" name="duong" value="Nhiều đường"> Nhiều</label>
                </div>

                <div class="option-group">
                    <p>Size:</p>
                    <label><input type="radio" name="size" value="M" checked> Size M</label>
                    <label><input type="radio" name="size" value="L"> Size L</label>
                </div>

                <div class="option-group">
                    <p>Ghi chú:</p>
                    <textarea name="ghichu" placeholder="Ví dụ: không đá, thêm sữa..." class="w-full border rounded-lg p-2 text-sm"></textarea>
                </div>

                <div class="option-group">
                    <p>Số lượng:</p>
                    <input id="modalQty" type="number" name="soluong" value="1" min="1" step="1"
                        class="w-full text-center border rounded-lg p-2 
                        focus:outline-none focus:ring-2 focus:ring-orange-400 mb-2">
                </div>

                <button type="submit" name="add_cart" class="submit-btn bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                    🛒 Thêm vào giỏ hàng
                </button>
            </form>
        </div>
    </div>

    <div id="cartMessage" class="fixed top-6 right-6 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg hidden transition-opacity duration-500">
        ✅ Thêm vào giỏ hàng thành công!
    </div>
</main>

<link rel="stylesheet" href="assets/css/modal.css?v=3">

<script>
let basePrice = 0;

function openModal(idsp, name, price, img, idshop){
    const modal = document.getElementById('productModal');
    modal.style.display = 'flex';

    basePrice = Number(price);
    document.getElementById('modalImage').src = './assets/images/' + img;
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';

    document.getElementById('modalId').value = idsp;
    document.getElementById('modalNameInput').value = name;
    document.getElementById('modalPriceInput').value = price;
    document.getElementById('modalImgInput').value = img;
    document.getElementById('modalShopId').value = idshop;
    document.getElementById('modalQty').value = 1;
    updatePrice();
}

function closeModal(){
    document.getElementById('productModal').style.display = 'none';
}

function updatePrice(){
    const qty = parseInt(document.getElementById('modalQty').value) || 1;
    const size = document.querySelector('input[name="size"]:checked')?.value || 'M';
    let price = basePrice;
    if(size === 'L') price += 5000;
    const total = price * qty;
    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
    document.getElementById('modalPriceInput').value = price;
}

function showCartMessage(message){
    const msg = document.getElementById('cartMessage');
    msg.innerText = message;
    msg.style.display = 'block';
    msg.style.opacity = '1';
    setTimeout(()=>{ msg.style.opacity = '0'; }, 2000);
    setTimeout(()=>{ msg.style.display = 'none'; }, 2600);
}

document.getElementById('modalQty').addEventListener('input', e=>{
    e.target.value = e.target.value.replace(/[^0-9]/g, '');
    if(e.target.value === '' || parseInt(e.target.value) < 1) e.target.value = 1;
    updatePrice();
});

document.querySelectorAll('input[name="size"]').forEach(radio=>{
    radio.addEventListener('change', updatePrice);
});

document.getElementById('modalForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('add_cart', '1');

    try {
        const res = await fetch(window.location.href, { method:'POST', body: formData });
        const data = await res.json();
        if(data.success){
            closeModal();
            showCartMessage('✅ Đã thêm sản phẩm vào giỏ hàng!');
        } else {
            showCartMessage('❌ Lỗi! Vui lòng thử lại.');
        }
    } catch(err){
        console.error(err);
        showCartMessage('⚠️ Lỗi kết nối!');
    }
});
</script>
<link rel="stylesheet" href="assets/css/menu.css">