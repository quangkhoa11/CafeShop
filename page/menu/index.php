<title>Menu</title>
<?php
$obj = new database();

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

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $cart_key = md5($idsp . $da . $duong . $size .$ghichu);

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

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => "Đã thêm {$soluong} x {$tensp} vào giỏ hàng!"
    ]);
    exit;
}
?>

<main class="flex-1 py-10 container mx-auto px-4">
<?php
$loaisp = $obj->xuatdulieu("SELECT * FROM loaisp");
if ($loaisp) {
    echo '<ul class="category-list flex flex-wrap justify-center gap-3 mb-8 mt-6">';
    echo '<li><a href="index.php?page=menu" class="px-4 py-2 bg-white border border-orange-300 rounded-full text-orange-600 font-medium shadow-sm hover:bg-orange-100 transition">Tất cả</a></li>';
    foreach ($loaisp as $loai) {
        echo '<li><a href="index.php?page=menu&cate='.$loai['idloai'].'" class="px-4 py-2 bg-white border border-orange-300 rounded-full text-orange-600 font-medium shadow-sm hover:bg-orange-100 transition">'.$loai['tenloai'].'</a></li>';
    }
    echo '</ul>';
}

$cate = isset($_GET['cate']) ? $_GET['cate'] : '';
$where = [];
if ($cate) $where[] = "idloai='$cate'";
$sql = "SELECT * FROM sanpham";
if (!empty($where)) $sql .= " WHERE ".implode(" AND ", $where);

$sanpham = $obj->xuatdulieu($sql);

if ($sanpham) {
    echo '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-6">';
    foreach ($sanpham as $sp) {
        echo '<div class="bg-white rounded-xl shadow-md hover:shadow-lg p-5 cursor-pointer"
              onclick="openModal(\''.$sp["idsp"].'\', \''.addslashes($sp["tensp"]).'\', \''.$sp["gia"].'\', \''.$sp["hinhanh"].'\', \''.$sp["idloai"].'\')">
              <div class="h-56 flex items-center justify-center">
                <img class="h-full w-full object-cover rounded-lg" src="assets/images/'.$sp["hinhanh"].'" alt="'.$sp["tensp"].'">
              </div>
              <h3 class="mt-4 font-semibold text-lg text-gray-800 text-center">'.$sp["tensp"].'</h3>
              <p class="text-base text-orange-600 mt-2 font-bold text-center">'.number_format($sp["gia"]).' VNĐ</p>
              </div>';
    }
    echo '</div>';
} else {
    echo '<p class="text-center text-gray-500 mt-10">Hiện tại chưa có sản phẩm nào</p>';
}
?>
</main>

<div id="productModal" class="modal">
    <div class="modal-content">
        <button class="close" onclick="closeModal()">✖</button>

        <img id="modalImage" src="" alt="Sản phẩm" class="mb-3 rounded-lg w-40 h-40 object-cover">
        <h3 id="modalName" class="text-lg font-bold mb-1"></h3>
        <p id="modalPrice" class="text-orange-600 font-semibold mb-3"></p>

        <form class="modal-form" onsubmit="return addToCartModal(event)">
            <input type="hidden" id="modalId">
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
                    <label><input type="radio" name="duong" value="Không đường" checked> Không đường</label>
                    <label><input type="radio" name="duong" value="Ít đường" checked> Ít</label>
                    <label><input type="radio" name="duong" value="Vừa đường"> Vừa</label>
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
                <input id="modalQty" type="number" name="soluong" 
                value="1" min="1" step="1"
                class="w-full text-center border rounded-lg p-2 
              focus:outline-none focus:ring-2 focus:ring-orange-400 mb-2">
            </div>

            <button type="submit" class="bg-orange-500 text-white font-bold px-4 py-2 rounded-lg hover:bg-orange-600">Thêm vào giỏ hàng</button>
        </form>
    </div>
</div>

<div id="cartMessage" class="fixed bottom-5 right-5 hidden bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg font-medium z-50 transition-opacity duration-500">
    <span id="cartMessageText">Đã thêm vào giỏ hàng!!</span>
</div>

<link rel="stylesheet" href="assets/css/modal.css?v=2">

<script>
let basePrice = 0; 

function openModal(idsp, name, price, img, idloai){
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
    document.getElementById('modalLoai').value = idloai;
    document.getElementById('modalQty').value = 1;

    const daDuongSection = document.getElementById('daDuongSection');
    daDuongSection.style.display = (idloai == 3) ? 'block' : 'none';

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
    if (size === 'L') {
        currentPrice += 5000;
    }

    const total = currentPrice * qty;

    document.getElementById('modalPrice').innerText = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
    document.getElementById('modalPriceInput').value = currentPrice;
}

function addToCartModal(event){
    event.preventDefault();

    const idsp = document.getElementById('modalId').value;
    const tensp = document.getElementById('modalNameInput').value;
    const gia = Number(document.getElementById('modalPriceInput').value); 
    const hinhanh = document.getElementById('modalImgInput').value;
    const soluong = Number(document.getElementById('modalQty').value);
    const idloai = document.getElementById('modalLoai').value;

    let da = '', duong = '', size = '';
    if (idloai == 3) {
        da = document.querySelector('input[name="da"]:checked').value;
        duong = document.querySelector('input[name="duong"]:checked').value;
        size = document.querySelector('input[name="size"]:checked').value;
    }

    const ghichu = document.querySelector('textarea[name="ghichu"]').value.trim();
    if (soluong < 1) { 
        alert('Số lượng phải >= 1'); 
        return false; 
    }

    const thanhtien = gia * soluong;

    const formData = new URLSearchParams({
        add_cart: 1,
        ajax: 1,
        idsp: idsp,
        tensp: tensp,
        gia: gia,
        hinhanh: hinhanh,
        soluong: soluong,
        da: da,
        duong: duong,
        size: size,
        ghichu: ghichu,
        thanhtien: thanhtien
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
    if (this.value === '' || parseInt(this.value) < 1) {
        this.value = 1;
    }
    updateModalPrice();
});

document.querySelectorAll('input[name="size"]').forEach(radio => {
    radio.addEventListener('change', updateModalPrice);
});
</script>
