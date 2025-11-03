<?php
if (!isset($_SESSION['dangnhap']) || $_SESSION['role'] !== 'shop') {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];

$sql = "SELECT sp.idsp, sp.tensp, sp.gia, sp.mota, sp.hinhanh, l.tenloai
        FROM sanpham sp
        LEFT JOIN loaisp l ON sp.idloai = l.idloai
        WHERE sp.idshop = '$idshop'
        ORDER BY sp.idsp DESC";
$products = $db->xuatdulieu($sql);
?>

<title>Quản lý sản phẩm</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
<div class="container mx-auto py-10 px-4">
  <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
    <h1 class="text-3xl font-bold text-orange-600">🛍️ Quản lý sản phẩm</h1>
    <a href="index.php?page=shop_add_product" 
       class="bg-gradient-to-r from-red-400 to-red-500 hover:from-orange-500 hover:to-orange-600 text-white px-5 py-2 rounded-full font-semibold shadow-md transition-transform transform hover:scale-105">
       ➕ Thêm sản phẩm
    </a>
  </div>

  <?php if ($products && count($products) > 0): ?>
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg border border-orange-100">
      <table class="min-w-full text-sm text-center border-collapse">
        <thead class="bg-orange-100 text-gray-700 uppercase text-sm tracking-wide">
          <tr>
            <th class="py-3 px-4 border-b">Hình ảnh</th>
            <th class="py-3 px-4 border-b">Tên sản phẩm</th>
            <th class="py-3 px-4 border-b">Loại</th>
            <th class="py-3 px-4 border-b">Giá</th>
            <th class="py-3 px-4 border-b">Mô tả</th>
            <th class="py-3 px-4 border-b">Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr class="hover:bg-orange-50 transition-colors duration-200">
              <td class="py-3 px-4 border-b">
                <img src="assets/images/<?= htmlspecialchars($p['hinhanh']) ?>" 
                     alt="<?= htmlspecialchars($p['tensp']) ?>" 
                     class="w-14 h-14 object-cover rounded-xl shadow-sm mx-auto ring-1 ring-orange-200">
              </td>
              <td class="py-3 px-4 border-b font-semibold text-gray-800">
                <?= htmlspecialchars($p['tensp']) ?>
              </td>
              <td class="py-3 px-4 border-b text-gray-600">
                <?= htmlspecialchars($p['tenloai'] ?? '—') ?>
              </td>
              <td class="py-3 px-4 border-b text-orange-600 font-semibold">
                <?= number_format($p['gia'], 0, ',', '.') ?> ₫
              </td>
              <td class="py-3 px-4 border-b text-gray-500 truncate max-w-xs" title="<?= htmlspecialchars($p['mota']) ?>">
                <?= htmlspecialchars(strlen($p['mota']) > 50 ? substr($p['mota'], 0, 50) . '…' : $p['mota']) ?>
              </td>
              <td class="py-3 px-4 border-b">
                <div class="flex justify-center gap-3">
                  <a href="index.php?page=shop_edit_product&idsp=<?= $p['idsp'] ?>" 
                     class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-semibold transition">
                     ✏️ Sửa
                  </a>
                  <a href="index.php?page=shop_delete_product&id=<?= $p['idsp'] ?>" 
                     onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này không?')" 
                     class="px-3 py-1 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 font-semibold transition">
                     🗑️ Xóa
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text-center py-12 bg-white rounded-2xl shadow-md border border-orange-100">
      <p class="text-gray-500 text-lg mb-4">Hiện tại bạn chưa có sản phẩm nào.</p>
      <a href="index.php?page=shop_add_product" 
         class="inline-block bg-orange-500 text-white px-5 py-2 rounded-full font-semibold hover:bg-orange-600 shadow-md transition-transform transform hover:scale-105">
         ➕ Thêm sản phẩm mới
      </a>
    </div>
  <?php endif; ?>
</div>

<style>
table th, table td {
  border-bottom: 1px solid #f3f3f3;
}
</style>
