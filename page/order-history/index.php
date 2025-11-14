<?php
$db = new database();

if (!isset($_SESSION['idkh'])) {
    header("Location: index.php?page=login");
    exit;
}

$idkh = $_SESSION['idkh'];

$donhang = $db->xuatdulieu("
    SELECT d.*, s.tenshop
    FROM donban d
    JOIN shop s ON d.idshop = s.idshop
    WHERE d.idkh = '$idkh'
    ORDER BY d.ngayban DESC
");
?>

<title>Lịch sử đơn hàng</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="max-w-5xl mx-auto mt-10 bg-white p-6 rounded-2xl shadow-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">🧾 Lịch sử đơn hàng của bạn</h1>

    <?php if (!$donhang): ?>
        <p class="text-center text-gray-500">Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-orange-600 text-white">
                    <tr>
                        <th class="px-4 py-2">Mã đơn</th>
                        <th class="px-4 py-2">Ngày mua</th>
                        <th class="px-4 py-2">Cửa hàng</th>
                        <th class="px-4 py-2">Tổng tiền</th>
                        <th class="px-4 py-2">Trạng thái</th>
                        <th class="px-4 py-2 text-center">Đánh giá</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($donhang as $d): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-semibold text-gray-700">#<?= $d['iddonban'] ?></td>
                            <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($d['ngayban'])) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($d['tenshop']) ?></td>
                            <td class="px-4 py-2 text-orange-600 font-semibold">
                                <?= number_format($d['tongtien'], 0, ',', '.') ?>₫
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-white text-xs 
                                    <?= $d['trangthai'] === 'Hoàn thành' ? 'bg-green-500' : 
                                        ($d['trangthai'] === 'Đang giao' ? 'bg-blue-500' : 'bg-gray-400') ?>">
                                    <?= htmlspecialchars($d['trangthai']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <?php if ($d['trangthai'] === 'Hoàn thành'): ?>
                                    <?php
                                        // Kiểm tra xem khách đã đánh giá đơn này chưa
                                        $danhgia = $db->xuatdulieu("
                                            SELECT COUNT(*) AS total 
                                            FROM rating_sanpham 
                                            WHERE idkh = '$idkh' 
                                            AND idsp IN (SELECT idsp FROM chitietdonban WHERE iddonban = '{$d['iddonban']}')
                                        ");
                                        $daDanhGia = $danhgia && $danhgia[0]['total'] > 0;
                                    ?>

                                    <?php if ($daDanhGia): ?>
                                        <button class="px-3 py-1 rounded bg-gray-300 text-gray-600 cursor-not-allowed font-semibold">
                                            Đã đánh giá
                                        </button>
                                    <?php else: ?>
                                        <a href="index.php?page=rating_shop&idshop=<?= $d['idshop'] ?>&iddonban=<?= $d['iddonban'] ?>"
                                           class="px-3 py-1 rounded bg-green-500 text-white font-semibold hover:bg-green-600 transition">
                                            Đánh giá
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">Chưa thể đánh giá</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
