<title>Thực đơn</title>
<?php
$obj = new database();
$loaisp = $obj->xuatdulieu("SELECT * FROM loaisp");

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$idloai = isset($_GET['idloai']) ? (int)$_GET['idloai'] : 0;

$sqlShop = "
    SELECT DISTINCT s.* 
    FROM shop s
    JOIN sanpham sp ON s.idshop = sp.idshop
    WHERE 1
";

if ($idloai > 0) {
    $sqlShop .= " AND sp.idloai = '$idloai'";
}
if ($keyword !== '') {
    $kw = addslashes($keyword);
    $sqlShop .= " AND (sp.tensp LIKE '%$kw%' OR s.tenshop LIKE '%$kw%')";
}

$sqlShop .= " ORDER BY RAND()";
$shops = $obj->xuatdulieu($sqlShop);
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<style>
.shop-card { transition: all 0.3s ease; }
.shop-card:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(255,166,0,0.2); }

.product-card {
    background: #fffaf5;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    transition: all 0.3s;
    min-height: 260px;
}
.product-img { width: 100%; aspect-ratio: 1/1; object-fit: cover; }
.product-info { padding: 8px; text-align: center; }
.product-info h4 { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.shop-header { min-height: 80px; display: flex; align-items: center; gap:10px; }

.category-item:hover { background: #ffe7c4; }
.active-category { background: #ffb84d; color: white; }

.category-link {
    display: flex;
    align-items: center;
    gap: 8px;
}
.category-link img {
    width: 30px;
    height: 30px;
    object-fit: cover;
    border-radius: 6px;
}
</style>

<div class="min-h-screen max-w-6xl mx-auto px-3 md:px-6 py-10 font-sans flex flex-col md:flex-row gap-6">
    <aside class="md:w-1/4 bg-orange-50 rounded-2xl shadow p-4 h-full">
        <h3 class="text-xl font-bold text-orange-600 mb-4 flex items-center gap-1">🍔 Danh mục</h3>
        <ul class="space-y-2">
            <li>
                <a href="index.php?page=menu_new" 
                   class="block px-4 py-2 rounded-lg text-gray-700 font-medium category-link <?= $idloai == 0 ? 'active-category' : '' ?>">
                   <img src="assets/images/images.jpg" alt="Tất cả">
                   <span>Tất cả</span>
                </a>
            </li>
            <?php foreach ($loaisp as $l): ?>
            <li>
                <a href="index.php?page=menu_new&idloai=<?= $l['idloai'] ?>" 
                   class="block px-4 py-2 rounded-lg text-gray-700 font-medium category-link <?= $idloai == $l['idloai'] ? 'active-category' : '' ?>">
                   <img src="assets/images/<?= htmlspecialchars($l['hinhanh']) ?>" alt="<?= htmlspecialchars($l['tenloai']) ?>">
                   <span><?= htmlspecialchars($l['tenloai']) ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <main class="md:w-3/4 w-full flex flex-col gap-6">
        <form method="get" class="flex flex-col sm:flex-row gap-3 mb-3 bg-orange-50 p-4 rounded-xl shadow">
            <input type="hidden" name="page" value="menu_new">
            <?php if ($idloai > 0): ?>
                <input type="hidden" name="idloai" value="<?= $idloai ?>">
            <?php endif; ?>

            <input type="text" name="keyword" placeholder="🔍 Tìm shop, món ăn..."
                value="<?= htmlspecialchars($keyword) ?>"
                class="flex-1 border border-orange-300 bg-white rounded-xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-orange-400 outline-none placeholder-gray-400" />

            <button type="submit"
                class="bg-yellow-300 hover:bg-yellow-500 text-white font-semibold px-6 py-3 rounded-xl transition">
                Tìm kiếm
            </button>
        </form>

        <?php if ($shops && count($shops) > 0): ?>
            <div class="space-y-10">
                <?php foreach ($shops as $shop): 
                    $idshop = $shop['idshop'];
                    $sp = $obj->xuatdulieu("
                        SELECT * FROM sanpham 
                        WHERE idshop = '$idshop' AND trangthai = 1
                        ORDER BY RAND() LIMIT 3
                    ");
                ?>
                    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition overflow-hidden shop-card">
                        <div class="shop-header bg-gradient-to-r from-orange-100 to-orange-50 px-6 py-4 border-b border-orange-200">
                            <img src="assets/images/<?= htmlspecialchars($shop['logo']) ?>" 
                                 alt="<?= htmlspecialchars($shop['tenshop']) ?>" 
                                 class="w-14 h-14 rounded-full object-cover border border-orange-300 shadow-sm">

                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($shop['tenshop']) ?></h3>
                                <p class="text-sm text-gray-600 flex items-center gap-1">
                                    📍 <?= htmlspecialchars($shop['diachi']) ?>
                                    <span class="shop-distance" 
                                          data-lat="<?= $shop['lat_shop'] ?>" 
                                          data-lng="<?= $shop['lng_shop'] ?>">...</span>
                                </p>
                            </div>
                            <a href="index.php?page=shop_detail&idshop=<?= $shop['idshop'] ?>" 
                               class="text-orange-600 font-semibold hover:underline">Xem shop →</a>
                        </div>

                        <?php if ($sp): ?>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4">
                                <?php foreach ($sp as $s): ?>
                                    <div class="product-card shadow-sm hover:shadow-lg transition">
                                        <img src="assets/images/<?= htmlspecialchars($s['hinhanh']) ?>" 
                                             alt="<?= htmlspecialchars($s['tensp']) ?>" 
                                             class="product-img">
                                        <div class="product-info">
                                            <h4 class="font-semibold text-gray-700"><?= htmlspecialchars($s['tensp']) ?></h4>
                                            <p class="text-orange-600 font-bold text-sm"><?= number_format($s['gia'], 0, ',', '.') ?>₫</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic p-4 text-center">Shop này hiện chưa có sản phẩm nào.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4" style="padding-left: 250px;">
                <div class="flex flex-col items-center justify-center text-gray-400">
                    <img src="assets/images/crying.png" 
                         alt="Không tìm thấy" 
                         class="object-contain w-full h-40 mb-2">
                    <p class="text-center text-sm">Không tìm thấy cửa hàng hoặc thức uống cần tìm!!!</p>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2){
    const R = 6371;
    const dLat = (lat2-lat1)*Math.PI/180;
    const dLon = (lon2-lon1)*Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2)**2;
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R*c;
}

if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(position){
        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;

        document.querySelectorAll('.shop-distance').forEach(function(el){
            const shopLat = parseFloat(el.dataset.lat);
            const shopLng = parseFloat(el.dataset.lng);
            const distance = getDistanceFromLatLonInKm(userLat, userLng, shopLat, shopLng);
            el.textContent = ' - Khoảng cách: ' + distance.toFixed(1) + ' km';
        });
    }, function(err){
        console.log("Không lấy được vị trí người dùng: ", err);
    });
} else {
    console.log("Trình duyệt không hỗ trợ Geolocation.");
}
</script>
