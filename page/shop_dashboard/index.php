<?php
if (!isset($_SESSION['dangnhap']) || $_SESSION['role'] !== 'shop') {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];

$sqlSP = "SELECT COUNT(*) AS tongsp FROM sanpham WHERE idshop = '$idshop'";
$sp = $db->xuatdulieu($sqlSP);
$tongsp = $sp[0]['tongsp'] ?? 0;

$sqlDH = "SELECT COUNT(*) AS tongdh FROM donban WHERE idshop = '$idshop'";
$dh = $db->xuatdulieu($sqlDH);
$tongdh = $dh[0]['tongdh'] ?? 0;

$sqlDT = "SELECT SUM(tongtien) AS tongdt FROM donban WHERE idshop = '$idshop' AND trangthai='Hoàn thành'";
$dt = $db->xuatdulieu($sqlDT);
$tongdt = number_format($dt[0]['tongdt'] ?? 0, 0, ',', '.');

$sqlNew = "SELECT iddonban, ngayban, tongtien, trangthai FROM donban WHERE idshop = '$idshop' ORDER BY ngayban DESC LIMIT 5";
$donhangMoi = $db->xuatdulieu($sqlNew);
?>

<title>Trang quản lý Shop</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

<div class="container mx-auto py-10 px-4">
  <h1 class="text-3xl font-bold mb-8 text-center text-orange-600">📊 Tổng quan hoạt động Shop</h1>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
    <div class="stat-card border-orange-400">
      <div class="icon bg-orange-100 text-orange-500">📦</div>
      <h3 class="label">Sản phẩm</h3>
      <p class="value text-orange-600"><?= $tongsp ?></p>
    </div>
    <div class="stat-card border-blue-400">
      <div class="icon bg-blue-100 text-blue-500">🧾</div>
      <h3 class="label">Đơn hàng</h3>
      <p class="value text-blue-600"><?= $tongdh ?></p>
    </div>
    <div class="stat-card border-green-400">
      <div class="icon bg-green-100 text-green-500">💰</div>
      <h3 class="label">Doanh thu</h3>
      <p class="value text-green-600"><?= $tongdt ?> ₫</p>
    </div>
  </div>

  <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-100">
    <h2 class="text-lg font-semibold mb-4 text-gray-700">🕓 Đơn hàng mới nhất</h2>
    <?php if ($donhangMoi && count($donhangMoi) > 0): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-center border-collapse">
          <thead>
            <tr class="bg-orange-100 text-gray-700 uppercase text-xs">
              <th class="py-3 px-4 border-b">Mã đơn</th>
              <th class="py-3 px-4 border-b">Ngày tạo</th>
              <th class="py-3 px-4 border-b">Tổng tiền</th>
              <th class="py-3 px-4 border-b">Trạng thái</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($donhangMoi as $dh): ?>
              <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4 border-b"><?= htmlspecialchars($dh['iddonban']) ?></td>
                <td class="py-3 px-4 border-b"><?= htmlspecialchars($dh['ngayban']) ?></td>
                <td class="py-3 px-4 border-b font-medium text-green-600"><?= number_format($dh['tongtien'], 0, ',', '.') ?> ₫</td>
                <td class="py-3 px-4 border-b">
                  <span class="status-badge <?= strtolower($dh['trangthai']) ?>"><?= htmlspecialchars($dh['trangthai']) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-gray-500 text-center py-4">Chưa có đơn hàng nào.</p>
    <?php endif; ?>
  </div>

  <h2 class="text-lg font-semibold mt-12 mb-4 text-gray-700">🚀 Lối tắt nhanh</h2>
  <div class="grid sm:grid-cols-3 gap-6">
    <a href="index.php?page=shop_products" class="shortcut-card bg-orange-100 hover:bg-orange-200">📦 Quản lý sản phẩm</a>
    <a href="index.php?page=shop_orders" class="shortcut-card bg-blue-100 hover:bg-blue-200">🧾 Quản lý đơn hàng</a>
    <a href="index.php?page=shop_profile" class="shortcut-card bg-green-100 hover:bg-green-200">👤 Hồ sơ shop</a>
  </div>
</div>

<style>
.stat-card {
  background: #fff;
  padding: 1.75rem;
  border-radius: 1.25rem;
  text-align: center;
  border-top-width: 5px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}
.stat-card .icon {
  width: 3rem;
  height: 3rem;
  margin: 0 auto 0.5rem;
  border-radius: 9999px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}
.stat-card .label {
  color: #6b7280;
  margin-bottom: 0.25rem;
  font-size: 0.95rem;
}
.stat-card .value {
  font-size: 1.75rem;
  font-weight: 700;
}

.shortcut-card {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  border-radius: 1.25rem;
  font-weight: 600;
  color: #374151;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 3px 8px rgba(0,0,0,0.05);
}
.shortcut-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: capitalize;
}
.status-badge.hoàn {
  background: #dcfce7;
  color: #166534;
}
.status-badge.đang {
  background: #fef9c3;
  color: #854d0e;
}
.status-badge.hủy {
  background: #fee2e2;
  color: #991b1b;
}
</style>
