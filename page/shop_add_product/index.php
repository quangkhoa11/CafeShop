<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=login");
    exit;
}

$db = new database();
$idshop = $_SESSION['idshop'];

// Lấy danh sách loại sản phẩm
$loaisp = $db->xuatdulieu("SELECT * FROM loaisp");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them'])) {
    $tensp = trim($_POST['tensp']);
    $gia = trim($_POST['gia']);
    $mota = trim($_POST['mota']);
    $idloai = $_POST['idloai'];

    // Xử lý upload hình ảnh
    $hinhanh = "";
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === 0) {
        $target_dir = "assets/images/";
        $file_name = time() . "_" . basename($_FILES["hinhanh"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["hinhanh"]["tmp_name"], $target_file)) {
            $hinhanh = $file_name;
        }
    }

    // Thêm sản phẩm
    $sqlInsert = "
        INSERT INTO sanpham (tensp, gia, mota, idloai, hinhanh, idshop)
        VALUES ('$tensp', '$gia', '$mota', '$idloai', '$hinhanh', '$idshop')
    ";

    if ($db->themxoasua($sqlInsert)) {
        echo "<script>alert('✅ Thêm sản phẩm mới thành công!'); window.location='index.php?page=shop_products';</script>";
    } else {
        echo "<script>alert('❌ Có lỗi xảy ra khi thêm sản phẩm!');</script>";
    }
}
?>

<div class="add-container">
    <h2 class="add-title">➕ Thêm sản phẩm mới</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tên sản phẩm</label>
            <input type="text" name="tensp" placeholder="Nhập tên sản phẩm" required>
        </div>

        <div class="form-group">
            <label>Giá (VNĐ)</label>
            <input type="number" name="gia" placeholder="Nhập giá bán" required>
        </div>

        <div class="form-group">
            <label>Loại sản phẩm</label>
            <select name="idloai" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                <?php foreach ($loaisp as $loai): ?>
                    <option value="<?= $loai['idloai'] ?>"><?= htmlspecialchars($loai['tenloai']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="mota" rows="4" placeholder="Mô tả chi tiết sản phẩm..." required></textarea>
        </div>

        <div class="form-group">
            <label>Hình ảnh</label>
            <input type="file" name="hinhanh" accept="image/*" class="file-input" required>
        </div>

        <div class="text-center" style="margin-top: 28px;">
            <button type="submit" name="them" class="save-btn">💾 Thêm sản phẩm</button>
        </div>
    </form>
</div>
<style>

    .add-container {
        max-width: 640px;
        margin: 60px auto;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        padding: 32px 40px;
        transition: all 0.3s ease;
    }

    .add-container:hover {
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
    }

    .add-title {
        font-size: 26px;
        font-weight: 700;
        color: #f97316;
        text-align: center;
        margin-bottom: 28px;
    }

    label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        transition: all 0.25s ease;
        font-size: 15px;
    }

    input:focus,
    textarea:focus,
    select:focus {
        border-color: #fb923c;
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.25);
        outline: none;
    }

    textarea {
        resize: none;
    }

    .file-input {
        font-size: 14px;
        color: #374151;
        margin-top: 6px;
    }

    .save-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f97316, #fb923c);
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.3);
    }

    .save-btn:hover {
        background: linear-gradient(135deg, #ea580c, #f97316);
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(249, 115, 22, 0.4);
    }

    .form-group {
        margin-bottom: 18px;
    }

    .alert-error {
        text-align: center;
        color: #dc2626;
        margin-top: 20px;
        font-weight: 600;
    }
</style>