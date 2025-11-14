<?php
$obj = new database();

$idshop = isset($_GET['idshop']) ? (int)$_GET['idshop'] : 0;
if (!$idshop) {
    echo "<p class='text-center text-red-500 mt-10'>Shop không tồn tại</p>";
    exit;
}

$shopList = $obj->xuatdulieu("SELECT * FROM shop WHERE idshop = '$idshop'");
if (!$shopList || count($shopList) == 0) {
    echo "<p class='text-center text-red-500 mt-10'>Shop không tồn tại</p>";
    exit;
}
$shop = $shopList[0];

$loaisp = $obj->xuatdulieu("SELECT DISTINCT l.idloai, l.tenloai, l.hinhanh
                             FROM loaisp l 
                             JOIN sanpham s ON l.idloai = s.idloai 
                             WHERE s.idshop = '$idshop' AND s.trangthai = 1");

$sanpham = $obj->xuatdulieu("SELECT * FROM sanpham WHERE idshop = '$idshop' AND trangthai = 1");

$rating_shop = $obj->xuatdulieu("
    SELECT AVG(diem) as avg_diem, COUNT(*) as total_reviews 
    FROM rating_sanpham 
    WHERE idsp IN (SELECT idsp FROM sanpham WHERE idshop = '$idshop')
");

$status = 'Open';
$openingHours = '07:00 - 21:00';

$cate = isset($_GET['cate']) ? $_GET['cate'] : '';
$products = $sanpham;
if ($cate) {
    $products = array_filter($sanpham, function($p) use($cate){
        return $p['idloai'] == $cate;
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart']) && isset($_POST['ajax'])) {
    ob_clean();
    $idsp = $_POST['idsp'];
    $tensp = $_POST['tensp'];
    $gia = $_POST['gia'];
    $hinhanh = $_POST['hinhanh'];
    $soluong = (int)$_POST['soluong'];
    $da = $_POST['da'] ?? '';
    $duong = $_POST['duong'] ?? '';
    $size = $_POST['size'] ?? '';
    $ghichu = trim($_POST['ghichu']);
    $idshopForm = $_POST['idshop'] ?? $idshop;

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $cart_key = md5($idsp.$da.$duong.$size.$ghichu.$idshopForm);

    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key]['soluong'] += $soluong;
    } else {
        $_SESSION['cart'][$cart_key] = [
            'idsp' => $idsp,
            'idshop' => $idshopForm,
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

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success'=>true, 'message'=>"Đã thêm {$soluong} x {$tensp} vào giỏ hàng!"]);
    exit;
}
?>

<title><?php echo htmlspecialchars($shop['tenshop']); ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
<link rel="stylesheet" href="assets/css/modal.css?v=1">

<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden mb-8">
        <div class="relative w-full h-60">
            <img src="assets/images/<?php echo htmlspecialchars($shop['anhbia']); ?>" 
                 alt="Ảnh cover shop" class="w-full h-full object-cover">
            <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                <div class="w-24 h-24 rounded-full border-4 border-white bg-white shadow-lg overflow-hidden flex items-center justify-center">
                    <img src="assets/images/<?php echo htmlspecialchars($shop['logo']); ?>" 
                         alt="Logo shop" class="object-contain w-20 h-20 rounded-full">
                </div>
            </div>
        </div>

        <div class="pt-16 pb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($shop['tenshop']); ?> </h1>
            <p class="text-gray-600 mb-2">📍 <?php echo htmlspecialchars($shop['diachi']); ?></p>
            <p class="text-gray-500 mb-2"><?php echo htmlspecialchars($shop['email']); ?></p>
            <div class="flex justify-center items-center space-x-4 mb-2">
                <?php
                    $avg_diem = round($rating_shop[0]['avg_diem'],1);
                    $total_reviews = $rating_shop[0]['total_reviews'];
                    $fullStars = floor($avg_diem);
                    $halfStar = ($avg_diem - $fullStars) >= 0.5 ? 1 : 0;
                ?>
                <a href="?page=rating_shop&idshop=<?php echo $idshop; ?>" 
                   class="flex items-center text-yellow-500 hover:text-yellow-600 transition duration-200"
                   title="Xem và đánh giá shop này">
                    <span class="mr-2">
                        <?php for($i=0;$i<$fullStars;$i++): ?>⭐<?php endfor; ?>
                        <?php if($halfStar): ?>✩<?php endif; ?>
                        (<?= $avg_diem ?>)
                    </span>
                    <span class="text-gray-500">(<?= $total_reviews ?> đánh giá)</span>
                </a>
            </div>
            <p class="text-gray-500">⏰ Giờ hoạt động: <?php echo $openingHours; ?> <span class="px-3 py-1 rounded-full <?php echo $status=='Open'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'; ?>">
                    <?php echo $status=='Open'?'Đang mở':'Đã đóng'; ?>
                </span></p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        <div id="category-menu" class="w-full lg:w-1/4 bg-white rounded-2xl shadow-md p-4 sticky top-4 h-fit">
    <h2 class="font-semibold text-gray-800 mb-4">Danh mục</h2>
    <ul class="space-y-2">

        <li>
            <a href="?page=shop_detail&idshop=<?php echo $idshop; ?>" 
               class="flex items-center gap-3 px-3 py-2 rounded hover:bg-orange-100 transition <?php echo !$cate?'bg-orange-100':''; ?>">
                <img src="assets/images/images.jpg" class="w-8 h-8 object-cover rounded-full border">
                <span>Tất cả</span>
            </a>
        </li>

        <?php foreach($loaisp as $loai): ?>
            <li>
                <a href="?page=shop_detail&idshop=<?php echo $idshop; ?>&cate=<?php echo $loai['idloai']; ?>" 
                   class="flex items-center gap-3 px-3 py-2 rounded hover:bg-orange-100 transition <?php echo ($cate==$loai['idloai'])?'bg-orange-100':''; ?>">

                    <img src="assets/images/<?php echo $loai['hinhanh']; ?>" 
                         class="w-8 h-8 object-cover rounded-full border" alt="">

                    <span><?php echo htmlspecialchars($loai['tenloai']); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


        <div class="w-full lg:w-3/4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if($products): ?>
                <?php foreach($products as $sp): ?>
                    <?php
                        $sold = $obj->xuatdulieu("
                            SELECT SUM(ctdb.soluong) as total_sold 
                            FROM chitietdonban ctdb 
                            JOIN donban db ON ctdb.iddonban = db.iddonban
                            WHERE ctdb.idsp = '{$sp['idsp']}' AND db.trangthai='Hoàn thành'
                        ");
                        $total_sold = $sold[0]['total_sold'] ?? 0;
                    ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-md p-3 flex flex-col justify-between">
                        <div>
                            <img src="assets/images/<?php echo $sp['hinhanh']; ?>" 
                                 alt="<?php echo htmlspecialchars($sp['tensp']); ?>" 
                                 class="w-60 h-40  object-cover rounded-lg mb-2">
                            <h3 class="font-semibold text-gray-800 mb-1 text-center"><?php echo htmlspecialchars($sp['tensp']); ?></h3>
                            <p class="text-center items-center text-gray-500 text-xs">🛒<?= $total_sold ?> đã bán</p>
                            <p class="text-orange-600 font-semibold mb-2 text-center"><?php echo number_format($sp['gia']); ?> VNĐ</p>
                        </div>
                        <button onclick="openModal('<?php echo $sp['idsp']; ?>', '<?php echo addslashes($sp['tensp']); ?>', '<?php echo $sp['gia']; ?>', '<?php echo $sp['hinhanh']; ?>', '<?php echo $sp['idloai']; ?>', '<?php echo $idshop; ?>')"
                                class="font-bold px-4 py-2 text-white rounded-lg" style="background-color: #ffb43cff;">
                            + Thêm vào giỏ
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500 col-span-full">Hiện tại chưa có sản phẩm nào</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="productModal" class="modal">
    <div class="modal-content">
        <button class="close" onclick="closeModal()">✖</button>

        <img id="modalImage" src="" alt="Sản phẩm" class="mb-3 rounded-lg w-40 h-40 object-cover">
        <h3 id="modalName" class="text-lg font-bold mb-1"></h3>
        <p id="modalPrice" class="text-orange-600 font-semibold mb-3"></p>

        <form class="modal-form" onsubmit="return addToCartModal(event)">
            <input type="hidden" id="modalId">
            <input type="hidden" id="modalShop">
            <input type="hidden" id="modalNameInput">
            <input type="hidden" id="modalPriceInput">
            <input type="hidden" id="modalImgInput">
            <input type="hidden" id="modalLoai">

            <div id="daDuongSection">
                <div class="option-group">
                    <p>Lượng đá:</p>
                    <label><input type="radio" name="da" value="Không đá" checked> Không đá</label>
                    <label><input type="radio" name="da" value="Đá riêng"> Đá riêng</label>
                    <label><input type="radio" name="da" value="Đá chung"> Đá chung</label>
                </div>

                <div class="option-group">
                    <p>Lượng đường:</p>
                    <label><input type="radio" name="duong" value="Không đường" > Không đường</label>
                    <label><input type="radio" name="duong" value="Ít đường"> Ít</label>
                    <label><input type="radio" name="duong" value="Vừa đường" checked> Vừa</label>
                    <label><input type="radio" name="duong" value="Nhiều đường"> Nhiều</label>
                </div>

                <div class="option-group">
                    <p>Size:</p>
                    <label><input type="radio" name="size" value="M" checked> Size M</label>
                    <label><input type="radio" name="size" value="L"> Size L</label>
                </div>
            </div>

            <div class="option-group">
                <p>Ghi chú:</p>
                <textarea name="ghichu" class="w-full border rounded-lg p-2 text-sm mb-2"></textarea>
            </div>

            <div class="option-group">
                <p>Số lượng:</p>
                <input id="modalQty" type="number" name="soluong" value="1" min="1" step="1" class="w-full text-center border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-orange-400 mb-2">
            </div>

            <button type="submit" class="bg-orange-500 text-white font-bold px-4 py-2 rounded-lg hover:bg-orange-600">Thêm vào giỏ hàng</button>
        </form>
    </div>
</div>
<div id="cartMessage" class="fixed bottom-5 right-5 hidden bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg font-medium z-50 transition-opacity duration-500">
    <span id="cartMessageText">Đã thêm vào giỏ hàng!!</span>
</div>

<script>
let basePrice = 0;

function openModal(idsp, name, price, img, idloai, idshop){
    const modal = document.getElementById('productModal');
    modal.style.display = 'flex';
    
    basePrice = Number(price);
    
    document.getElementById('modalImage').src = './assets/images/' + img;
    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('vi-VN').format(price) + ' VNĐ';

    document.getElementById('modalId').value = idsp;
    document.getElementById('modalShop').value = idshop; 
    document.getElementById('modalNameInput').value = name;
    document.getElementById('modalPriceInput').value = price;
    document.getElementById('modalImgInput').value = img;
    document.getElementById('modalLoai').value = idloai;
    document.getElementById('modalQty').value = 1;

    const daDuongSection = document.getElementById('daDuongSection');
    daDuongSection.style.display = (idloai == 3 || idloai == 6 || idloai == 7) ? 'block' : 'none';

    document.querySelectorAll('#productModal input[type=radio]').forEach(r => r.checked = r.defaultChecked);
    document.querySelector('#productModal textarea').value = '';

    updateModalPrice(); 
}

function closeModal(){
    document.getElementById('productModal').style.display = 'none';
}

function showCartMessage(text){
    const msg = document.getElementById('cartMessage');
    const textEl = document.getElementById('cartMessageText');
    textEl.innerText = text;

    msg.style.display = 'block';
    msg.style.opacity = '1';
    setTimeout(()=>{ msg.style.opacity = '0'; }, 2000);
    setTimeout(()=>{ msg.style.display = 'none'; }, 2600);
}

function updateModalPrice(){
    const size = document.querySelector('input[name="size"]:checked')?.value || 'M';
    const qty = Number(document.getElementById('modalQty').value) || 1;

    let currentPrice = basePrice;
    if (size === 'L') currentPrice += 5000;

    const total = currentPrice * qty;

    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
    document.getElementById('modalPriceInput').value = currentPrice;
}

function addToCartModal(event){
    event.preventDefault();

    const idsp = document.getElementById('modalId').value;
    const idshop = document.getElementById('modalShop').value; 
    const tensp = document.getElementById('modalNameInput').value;
    const gia = Number(document.getElementById('modalPriceInput').value); 
    const hinhanh = document.getElementById('modalImgInput').value;
    const soluong = Number(document.getElementById('modalQty').value);
    const idloai = document.getElementById('modalLoai').value;

    let da = '', duong = '', size = '';
    if (idloai == 3 || idloai == 6 || idloai == 7) {
        da = document.querySelector('input[name="da"]:checked').value;
        duong = document.querySelector('input[name="duong"]:checked').value;
        size = document.querySelector('input[name="size"]:checked').value;
    }

    const ghichu = document.querySelector('textarea[name="ghichu"]').value.trim();
    if (soluong < 1) { 
        alert('Số lượng phải >= 1'); 
        return false; 
    }

    const formData = new URLSearchParams({
        add_cart: 1,
        ajax: 1,
        idsp: idsp,
        idshop: idshop,
        tensp: tensp,
        gia: gia,
        hinhanh: hinhanh,
        soluong: soluong,
        da: da,
        duong: duong,
        size: size,
        ghichu: ghichu
    });

    fetch(window.location.href, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: formData.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeModal();             
            showCartMessage(data.message); 
        } else {
            alert('Có lỗi xảy ra, vui lòng thử lại!');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi kết nối đến server!');
    });
}

document.getElementById('modalQty').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value === '' || parseInt(this.value) < 1) this.value = 1;
    updateModalPrice();
});

document.querySelectorAll('input[name="size"]').forEach(radio => {
    radio.addEventListener('change', updateModalPrice);
});

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const cate = urlParams.get('cate');
    
    if (cate) {
        const menu = document.getElementById('category-menu');
        if(menu){
            menu.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
});

</script>
