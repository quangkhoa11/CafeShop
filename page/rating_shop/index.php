<?php
session_start();
$obj = new database();

$idshop = isset($_GET['idshop']) ? (int)$_GET['idshop'] : 0;
$iddonban = isset($_GET['iddonban']) ? (int)$_GET['iddonban'] : 0;

if (!$idshop) {
    echo "<p class='text-center text-red-500 mt-10'>Không tìm thấy shop!</p>";
    exit;
}

$shop = $obj->xuatdulieu("SELECT * FROM shop WHERE idshop='$idshop'");
if (!$shop) {
    echo "<p class='text-center text-red-500 mt-10'>Shop không tồn tại!</p>";
    exit;
}
$shop = $shop[0];

$idkh = $_SESSION['idkh'] ?? 0;

if ($iddonban > 0) {
    $sanpham = $obj->xuatdulieu("
        SELECT DISTINCT sp.idsp, sp.tensp 
        FROM sanpham sp
        JOIN chitietdonban ctdb ON sp.idsp = ctdb.idsp
        WHERE ctdb.iddonban = '$iddonban'
    ");
} else {
    $sanpham = $obj->xuatdulieu("
        SELECT DISTINCT sp.idsp, sp.tensp 
        FROM sanpham sp
        JOIN chitietdonban ctdb ON sp.idsp = ctdb.idsp
        JOIN donban db ON ctdb.iddonban = db.iddonban
        WHERE db.idkh = '$idkh'
          AND db.idshop = '$idshop'
          AND db.trangthai = 'Hoàn thành'
    ");
}

$sanphamRated = [];
if ($idkh) {
    $tmp = $obj->xuatdulieu("
        SELECT idsp FROM rating_sanpham 
        WHERE idkh='$idkh' 
        AND idsp IN (SELECT idsp FROM sanpham WHERE idshop='$idshop')
    ");
    if ($tmp) {
        $sanphamRated = array_column($tmp, 'idsp');
    }
}

$sanphamToRate = [];
foreach ($sanpham as $sp) {
    if (!in_array($sp['idsp'], $sanphamRated)) {
        $sanphamToRate[] = $sp;
    }
}

if (isset($_POST['submitRating']) && $idkh) {
    $idsp = (int)$_POST['idsp'];
    $diem = (int)$_POST['diem'];
    $binhluan = trim($_POST['binhluan']);

    if (in_array($idsp, $sanphamRated)) {
        echo "<p class='text-center text-red-500 mt-4'>Bạn đã đánh giá sản phẩm này rồi!</p>";
        exit;
    }

    if ($diem >= 1 && $diem <= 5) {
        $check = $obj->xuatdulieu("
            SELECT 1 
            FROM chitietdonban c
            JOIN donban d ON c.iddonban = d.iddonban
            WHERE d.idkh = '$idkh'
              AND d.idshop = '$idshop'
              AND d.trangthai = 'Hoàn thành'
              AND c.idsp = '$idsp'
            LIMIT 1
        ");

        if ($check) {
            $obj->themxoasua("
                INSERT INTO rating_sanpham (idsp, idkh, diem, binhluan, ngaytao)
                VALUES ('$idsp', '$idkh', '$diem', '$binhluan', NOW())
            ");
            echo "<script>alert('Cảm ơn bạn đã đánh giá!'); window.location='index.php?page=rating_shop&idshop=$idshop';</script>";
            exit;
        } else {
            echo "<p class='text-center text-red-500 mt-4'>Bạn chỉ có thể đánh giá sản phẩm đã mua và hoàn thành!</p>";
        }
    } else {
        echo "<p class='text-center text-red-500 mt-4'>Vui lòng chọn số sao hợp lệ (1-5).</p>";
    }
}

$ratings = $obj->xuatdulieu("
    SELECT r.*, sp.tensp, kh.tenkh , sp.hinhanh
    FROM rating_sanpham r
    JOIN sanpham sp ON r.idsp = sp.idsp
    JOIN khachhang kh ON r.idkh = kh.idkh
    WHERE sp.idshop = '$idshop'
    ORDER BY r.ngaytao DESC
");

$diemtb = $obj->xuatdulieu("
    SELECT ROUND(AVG(r.diem),1) AS tb 
    FROM rating_sanpham r
    JOIN sanpham sp ON r.idsp = sp.idsp
    WHERE sp.idshop = '$idshop'
");
$diemtb = $diemtb && $diemtb[0]['tb'] ? $diemtb[0]['tb'] : 0;
?>

<title>Đánh giá <?= htmlspecialchars($shop['tenshop']); ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

<div class="w-full px-4 md:px-12 lg:px-24 xl:px-40 py-12">
    <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl">

        <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-8 mb-8 border-b pb-6">
            <img src="assets/images/<?= htmlspecialchars($shop['logo']); ?>" class="w-24 h-24 md:w-28 md:h-28 rounded-full border-2 border-orange-400 shadow-sm" alt="">
            <div>
                <h1 class="text-2xl md:text-4xl font-bold text-gray-800"><?= htmlspecialchars($shop['tenshop']); ?></h1>
                <p class="text-sm md:text-base text-gray-500 mt-1 md:mt-2"><?= htmlspecialchars($shop['diachi']); ?></p>
                <p class="text-yellow-500 mt-2 md:mt-3 text-lg md:text-xl font-semibold">
                    ⭐ <?= $diemtb ? $diemtb . "/5.0" : "Chưa có đánh giá"; ?>
                </p>
            </div>
        </div>

        <h2 class="text-2xl md:text-3xl font-semibold text-orange-600 mb-6">Đánh giá từ khách hàng</h2>
        <?php if ($ratings): ?>
            <div class="space-y-4 md:space-y-6 mb-8">
                <?php foreach($ratings as $r): ?>
                    <?php
                        $imgSp = !empty($r['hinhanh']) ? "assets/images/{$r['hinhanh']}" : "assets/images/no-image.png";
                    ?>
                    <div class="p-4 md:p-5 border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row md:items-start space-y-3 md:space-y-0 md:space-x-4">
                        <img src="<?= $imgSp ?>" alt="<?= htmlspecialchars($r['tensp']) ?>" class="w-full md:w-24 lg:w-28 h-24 md:h-28 object-cover rounded-xl border border-gray-300 shadow-sm">
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-2 md:mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-orange-100 rounded-full w-10 h-10 flex items-center justify-center font-bold text-orange-600 text-base md:text-lg">
                                        <?= strtoupper(substr($r['tenkh'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm md:text-base"><?= htmlspecialchars($r['tenkh']) ?></p>
                                        <p class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($r['ngaytao'])) ?></p>
                                    </div>
                                </div>
                                <p class="text-yellow-500 text-lg md:text-xl">
                                    <?= str_repeat('⭐', $r['diem']) ?>
                                </p>
                            </div>
                            <p class="text-sm text-gray-600 italic mb-1">Sản phẩm: <?= htmlspecialchars($r['tensp']) ?></p>
                            <p class="text-gray-800 leading-relaxed text-sm md:text-base"><?= nl2br(htmlspecialchars($r['binhluan'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 mb-6 text-center italic text-base md:text-lg">Chưa có đánh giá nào cho shop này.</p>
        <?php endif; ?>

        <?php if ($idkh && $sanphamToRate): ?>
            <div class="border-t pt-6 md:pt-8">
                <h3 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-6">Gửi đánh giá của bạn</h3>
                <form method="POST" class="space-y-5 md:space-y-6 bg-gray-50 p-5 md:p-8 rounded-2xl shadow-lg">
                    <?php if (count($sanphamToRate) === 1): ?>
                        <input type="hidden" name="idsp" value="<?= $sanphamToRate[0]['idsp'] ?>">
                        <p class="text-gray-800 font-medium text-sm md:text-base mb-2 md:mb-3">Sản phẩm: <?= htmlspecialchars($sanphamToRate[0]['tensp']) ?></p>
                    <?php else: ?>
                        <div>
                            <label class="block font-medium mb-1 md:mb-2 text-gray-700 text-sm md:text-base">Chọn sản phẩm:</label>
                            <select name="idsp" required class="w-full border border-gray-300 rounded-xl px-3 md:px-5 py-2 md:py-3 focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm md:text-base">
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach($sanphamToRate as $sp): ?>
                                    <option value="<?= $sp['idsp'] ?>"><?= htmlspecialchars($sp['tensp']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block font-medium mb-1 md:mb-2 text-gray-700 text-sm md:text-base">Chọn số sao:</label>
                        <select name="diem" required class="w-full border border-gray-300 rounded-xl px-3 md:px-5 py-2 md:py-3 focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm md:text-base">
                            <option value="">-- Chọn số sao --</option>
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium mb-1 md:mb-2 text-gray-700 text-sm md:text-base">Bình luận:</label>
                        <textarea name="binhluan" rows="4" class="w-full border border-gray-300 rounded-xl px-3 md:px-5 py-2 md:py-3 focus:ring-2 focus:ring-orange-500 focus:outline-none text-sm md:text-base resize-none" placeholder="Hãy chia sẻ cảm nhận của bạn..."></textarea>
                    </div>

                    <button type="submit" name="submitRating" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 md:py-4 rounded-2xl font-semibold shadow-md text-sm md:text-base transition-all">
                        Gửi đánh giá
                    </button>
                </form>
            </div>
        <?php elseif ($idkh && !$sanphamToRate): ?>
            <p class="text-center text-gray-500 mt-4 md:mt-6 italic text-sm md:text-base">Bạn đã đánh giá tất cả sản phẩm đã mua trong shop này.</p>
        <?php else: ?>
            <p class="text-center text-gray-500 mt-3 md:mt-4 text-sm md:text-base">
                <a href="index.php?page=login" class="text-orange-600 font-semibold underline">Đăng nhập</a> để gửi đánh giá.
            </p>
        <?php endif; ?>
    </div>
</div>
