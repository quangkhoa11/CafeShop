<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=login");
    exit;
}

$idshop = $_SESSION['idshop'];
$db = new database();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPromo'])) {
    $ten = trim($_POST['ten']);
    $mota = trim($_POST['mota']);
    $giamgia = (int)$_POST['giamgia'];
    $ngay_bd = $_POST['ngay_bd'];
    $ngay_kt = $_POST['ngay_kt'];

    if ($ten === '') $errors[] = "Tên khuyến mãi không được để trống";
    if ($giamgia <= 0 || $giamgia > 100) $errors[] = "Giảm giá phải từ 1 đến 100%";
    if (strtotime($ngay_bd) > strtotime($ngay_kt)) $errors[] = "Ngày bắt đầu phải nhỏ hơn ngày kết thúc";

    if (empty($errors)) {
        $db->themxoasua("INSERT INTO khuyenmai(idshop, tenkm, mota, giamgia, ngay_bd, ngay_kt)
                         VALUES('$idshop', '$ten', '$mota', '$giamgia', '$ngay_bd', '$ngay_kt')");
        $success = "Thêm khuyến mãi thành công!";
    }
}

if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $db->themxoasua("DELETE FROM khuyenmai WHERE idkm = '$id' AND idshop = '$idshop'");
    header("Location: index.php?page=shop_promo");
    exit;
}

$km = $db->xuatdulieu("SELECT * FROM khuyenmai WHERE idshop='$idshop' ORDER BY idkm DESC");
?>

    <div class="w-full p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800 text-center">Quản lý Khuyến mãi</h1>

    <?php if (!empty($errors)): ?>
        <div class="p-4 mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            <?php foreach ($errors as $e) echo "<p class='mb-1'>• $e</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="p-4 mb-6 bg-green-50 border border-green-200 text-green-700 rounded-lg">✔ <?php echo $success; ?></div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-2xl shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Tạo Khuyến mãi Mới</h2>
        <form id="promoForm" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block mb-2 text-gray-600 font-medium">Tên khuyến mãi</label>
                <input name="ten" type="text" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-orange-300 focus:outline-none" placeholder="Nhập tên khuyến mãi" required>
            </div>

            <div>
                <label class="block mb-2 text-gray-600 font-medium">Giảm giá (%)</label>
                <input id="giamgia" name="giamgia" type="number" min="1" max="100"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-orange-300 focus:outline-none"
                    placeholder="Nhập số từ 1 đến 100" required
                    oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                >
            </div>
            <div>
                <label class="block mb-2 text-gray-600 font-medium">Ngày bắt đầu</label>
                <input id="ngay_bd" name="ngay_bd" type="date" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-orange-300 focus:outline-none" required>
            </div>

            <div>
                <label class="block mb-2 text-gray-600 font-medium">Ngày kết thúc</label>
                <input id="ngay_kt" name="ngay_kt" type="date" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-orange-300 focus:outline-none" required>
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-gray-600 font-medium">Mô tả</label>
                <textarea name="mota" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-orange-300 focus:outline-none" rows="3" placeholder="Mô tả chi tiết khuyến mãi"></textarea>
            </div>

            <div class="md:col-span-2 text-right">
                <button type="submit" name="addPromo" class="bg-gradient-to-r from-yellow-400 to-yellow-500 px-6 py-3 rounded-xl text-white font-semibold shadow hover:from-yellow-500 hover:to-yellow-600 transition">Thêm Khuyến mãi</button>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Danh sách Khuyến mãi</h2>

        <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-orange-100 text-gray-700 uppercase text-sm">
                    <th class="p-3 border-b">Tên</th>
                    <th class="p-3 border-b text-center">Giảm</th>
                    <th class="p-3 border-b">Ngày áp dụng</th>
                    <th class="p-3 border-b text-center">Hành động</th>
                </tr>
            </thead>
            <tbody class="text-gray-600">
                <?php if($km): ?>
                    <?php foreach ($km as $k): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-3"><?php echo htmlspecialchars($k['tenkm']); ?></td>
                            <td class="p-3 text-center font-semibold text-orange-500"><?php echo $k['giamgia']; ?>%</td>
                            <td class="p-3 text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($k['ngay_bd'])); ?> - 
                                <?php echo date('d/m/Y', strtotime($k['ngay_kt'])); ?>
                            </td>
                            <td class="p-3 text-center">
                                <a onclick="return confirm('Xóa khuyến mãi này?')" 
                                   href="?page=shop_promo&del=<?php echo $k['idkm']; ?>" 
                                   class="text-red-500 hover:text-red-600 font-medium transition">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-400">Chưa có khuyến mãi nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<script>
document.getElementById('promoForm').addEventListener('submit', function(e) {
    const start = document.getElementById('ngay_bd').value;
    const end = document.getElementById('ngay_kt').value;

    if (new Date(end) < new Date(start)) {
        alert("Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!");
        e.preventDefault();
        return false;
    }

    const discount = document.getElementById('giamgia').value;
    if (discount < 1 || discount > 100) {
        alert("Giảm giá phải từ 1 đến 100%");
        e.preventDefault();
        return false;
    }
});
</script>
