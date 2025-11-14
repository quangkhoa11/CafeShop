<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=login");
    exit;
}

$idshop = $_SESSION['idshop'];
$db = new database();

$summary = $db->xuatdulieu("
    SELECT 
        COUNT(r.idrating) AS total_reviews,
        ROUND(AVG(r.diem),2) AS avg_rating,
        SUM(CASE WHEN r.diem=5 THEN 1 ELSE 0 END) AS star_5,
        SUM(CASE WHEN r.diem=4 THEN 1 ELSE 0 END) AS star_4,
        SUM(CASE WHEN r.diem=3 THEN 1 ELSE 0 END) AS star_3,
        SUM(CASE WHEN r.diem=2 THEN 1 ELSE 0 END) AS star_2,
        SUM(CASE WHEN r.diem=1 THEN 1 ELSE 0 END) AS star_1
    FROM rating_sanpham r
    JOIN sanpham s ON r.idsp = s.idsp
    WHERE s.idshop='$idshop'
")[0];

$limit = 10;
$page = isset($_GET['p']) ? max(1,(int)$_GET['p']) : 1;
$offset = ($page-1)*$limit;

$sql = "
SELECT r.idrating, r.diem, r.binhluan, r.ngaytao,
       s.tensp, s.hinhanh,
       k.tenkh
FROM rating_sanpham r
JOIN sanpham s ON r.idsp = s.idsp
JOIN khachhang k ON r.idkh = k.idkh
WHERE s.idshop = '$idshop'
ORDER BY r.ngaytao DESC
LIMIT $limit OFFSET $offset
";
$reviews = $db->xuatdulieu($sql);

$total = $db->xuatdulieu("
    SELECT COUNT(*) as cnt 
    FROM rating_sanpham r
    JOIN sanpham s ON r.idsp = s.idsp
    WHERE s.idshop='$idshop'
")[0]['cnt'];
$totalPages = ceil($total/$limit);
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="container mx-auto p-4">
    <h1 class="text-3xl text-center font-bold mb-6 text-gray-800">Quản lý đánh giá</h1>

    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-gray-500">Tổng đánh giá</div>
            <div class="text-2xl font-bold"><?= $summary['total_reviews'] ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-gray-500">Đánh giá trung bình</div>
            <div class="text-2xl font-bold text-yellow-500"><?= $summary['avg_rating'] ?? 0 ?> <span class="text-gray-400 text-base">/5</span></div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 mb-2">Phân bố rating</div>
            <?php for($i=5;$i>=1;$i--): 
                $count = $summary['star_'.$i] ?? 0;
                $percent = $summary['total_reviews']>0 ? round($count/$summary['total_reviews']*100) : 0;
            ?>
            <div class="flex items-center mb-1">
                <span class="w-8 text-sm text-yellow-500"><?= $i ?>★</span>
                <div class="flex-1 h-3 bg-gray-200 rounded mx-2">
                    <div class="h-3 bg-yellow-400 rounded" style="width:<?= $percent ?>%"></div>
                </div>
                <span class="w-10 text-sm text-gray-600"><?= $count ?></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <?php if(!$reviews): ?>
        <p class="text-gray-500">Chưa có đánh giá nào cho sản phẩm của bạn.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-4 text-left">Sản phẩm</th>
                        <th class="py-3 px-4 text-left">Khách hàng</th>
                        <th class="py-3 px-4 text-left">Rating</th>
                        <th class="py-3 px-4 text-left">Bình luận</th>
                        <th class="py-3 px-4 text-left">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($reviews as $r): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 flex items-center gap-3">
                                <img src="assets/images/<?= htmlspecialchars($r['hinhanh'] ?: 'default.png') ?>" 
                                     alt="<?= htmlspecialchars($r['tensp']) ?>" 
                                     class="w-12 h-12 object-cover rounded">
                                <span><?= htmlspecialchars($r['tensp']) ?></span>
                            </td>
                            <td class="py-3 px-4"><?= htmlspecialchars($r['tenkh']) ?></td>
                            <td class="py-3 px-4">
                                <?php for($i=1;$i<=5;$i++): ?>
                                    <span class="text-yellow-400"><?= $i <= $r['diem'] ? '★' : '☆' ?></span>
                                <?php endfor; ?>
                            </td>
                            <td class="py-3 px-4 max-w-xs">
                                <div class="text-gray-700 break-words"><?= nl2br(htmlspecialchars($r['binhluan'])) ?></div>
                            </td>
                            <td class="py-3 px-4 text-gray-500"><?= date('d/m/Y H:i', strtotime($r['ngaytao'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if($totalPages>1): ?>
            <div class="mt-4 flex justify-center gap-2">
                <?php for($i=1;$i<=$totalPages;$i++): ?>
                    <a href="?page=shop_reviews&p=<?= $i ?>" 
                       class="px-3 py-1 rounded border <?= $i==$page ? 'bg-blue-500 text-white' : 'bg-white text-blue-500' ?>">
                       <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
