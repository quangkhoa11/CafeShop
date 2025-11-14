<title>Hồ sơ cửa hàng</title>
<?php
if (!isset($_SESSION['idshop'])) {
    header("Location: index.php?page=shop_login");
    exit;
}

$idshop = $_SESSION['idshop'];
$db = new database();

$shop = $db->xuatdulieu("SELECT * FROM shop WHERE idshop = '$idshop'")[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $tenshop = trim($_POST['tenshop']);
    $email   = trim($_POST['email']);
    $sdt     = trim($_POST['sdt']);
    $diachi  = trim($_POST['diachi']);
    $lat_shop = trim($_POST['lat_shop']);
    $lng_shop = trim($_POST['lng_shop']);

    $logo = $shop['logo'];

    if (!empty($_FILES['logo']['name'])) {
        $upload_dir = 'assets/images/';
        $filename = time() . '_' . basename($_FILES["logo"]["name"]);
        $target_file = $upload_dir . $filename;

        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if ($logo && file_exists($upload_dir . $logo)) unlink($upload_dir . $logo);

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
            $logo = $filename;
        }
    }

    $cover = $shop['anhbia']; 

if (!empty($_FILES['cover']['name'])) {
    $upload_dir = 'assets/images/';
    $filename = time() . '_cover_' . basename($_FILES["cover"]["name"]);
    $target_file = $upload_dir . $filename;

    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    if ($cover && file_exists($upload_dir . $cover)) unlink($upload_dir . $cover);

    if (move_uploaded_file($_FILES["cover"]["tmp_name"], $target_file)) {
        $cover = $filename;
    }
}


    $sql = "UPDATE shop SET 
            tenshop = '$tenshop', 
            email = '$email', 
            sdt = '$sdt', 
            diachi = '$diachi', 
            logo = '$logo',
            anhbia = '$cover',
            lat_shop = '$lat_shop',
            lng_shop = '$lng_shop'
        WHERE idshop = '$idshop'";
$db->themxoasua($sql);


    echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location='index.php?page=shop_profile';</script>";
    exit;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="profile-container">
    <h2>Hồ sơ cửa hàng</h2>

    <form method="POST" enctype="multipart/form-data" class="profile-form">
        <div class="profile-header">
    <img src="assets/images/<?= htmlspecialchars($shop['anhbia'] ?? '') ?>" 
         class="profile-cover" 
         alt="Ảnh bìa shop"
         onerror="this.src='assets/images/default_cover.png'">

    <label class="upload-btn">
        <input type="file" name="cover" accept="image/*" onchange="previewCover(this)">
        Thay đổi ảnh bìa
    </label>
    <div id="previewCover" style="margin-top:10px;"></div>

    <img src="assets/images/<?= htmlspecialchars($shop['logo']) ?>" 
         class="profile-logo" 
         alt="Logo Shop" 
         onerror="this.src='assets/images/default_shop.png'">

    <label class="upload-btn">
        <input type="file" name="logo" accept="image/*" onchange="previewLogo(this)">
        Thay đổi logo
    </label>
    <div id="preview" style="margin-top:10px;"></div>
</div>

        <div class="form-group">
            <label>Tên cửa hàng:</label>
            <input type="text" name="tenshop" value="<?= htmlspecialchars($shop['tenshop']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($shop['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại:</label>
            <input type="text" name="sdt" value="<?= htmlspecialchars($shop['sdt']) ?>" required>
        </div>

        <div class="form-group">
            <label>Địa chỉ:</label>
            <div style="display:flex; gap:5px; margin-bottom:5px;">
                <input type="text" id="diachi" name="diachi" 
                       value="<?= htmlspecialchars($shop['diachi']) ?>" 
                       placeholder="Nhập hoặc tìm địa chỉ trên bản đồ" required style="flex:1; padding:8px;">
                <button type="button" id="searchAddress">🔍 Tìm</button>
            </div>
            <input type="hidden" id="lat_shop" name="lat_shop" value="<?= htmlspecialchars($shop['lat_shop']) ?>">
            <input type="hidden" id="lng_shop" name="lng_shop" value="<?= htmlspecialchars($shop['lng_shop']) ?>">
            <div id="map" style="height:350px; border:1px solid #ccc; border-radius:8px;"></div>
        </div>

        <div class="form-actions">
            <button type="submit" name="update" class="btn-save">💾 Lưu thay đổi</button>
            <a href="index.php?page=shop_dashboard" class="btn-cancel">← Quay lại</a>
        </div>
    </form>
</div>

<script>
var diachiInput = document.getElementById('diachi');
var latInput = document.getElementById('lat_shop');
var lngInput = document.getElementById('lng_shop');

var initialLat = parseFloat(latInput.value) || 10.8231;
var initialLng = parseFloat(lngInput.value) || 106.6297;

var map = L.map('map').setView([initialLat, initialLng], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'© OpenStreetMap contributors'
}).addTo(map);

var marker = L.marker([initialLat, initialLng], {draggable:true}).addTo(map);

marker.on('dragend', updateLatLng);

map.on('click', function(e){
    marker.setLatLng(e.latlng);
    updateLatLng();
});

document.getElementById('searchAddress').onclick = searchAddress;

function updateLatLng(){
    var latlng = marker.getLatLng();
    latInput.value = latlng.lat;
    lngInput.value = latlng.lng;

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`, {
        headers: {'Accept':'application/json','User-Agent':'ShopProfileApp/1.0'}
    })
    .then(res=>res.json())
    .then(data=>{
        if(data && data.display_name) diachiInput.value = data.display_name;
    })
    .catch(err => console.log(err));
}

function searchAddress(){
    var query = diachiInput.value.trim();
    if(!query){ alert("Vui lòng nhập địa chỉ"); return; }

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`, {
        headers: {'Accept':'application/json','User-Agent':'ShopProfileApp/1.0'}
    })
    .then(res=>res.json())
    .then(data=>{
        if(data && data.length>0){
            var lat = parseFloat(data[0].lat);
            var lon = parseFloat(data[0].lon);
            map.setView([lat, lon], 16);
            marker.setLatLng([lat, lon]);
            latInput.value = lat;
            lngInput.value = lon;
            diachiInput.value = data[0].display_name;
        } else {
            alert("Không tìm thấy địa chỉ.");
        }
    })
    .catch(err => console.log(err));
}

function previewLogo(input){
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            preview.innerHTML = `<img src="${e.target.result}" style="width:120px;height:120px;border-radius:50%;margin-top:10px;border:3px solid #ff9933;">`;
            document.querySelector('.profile-logo').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.profile-container { width:700px; margin:50px auto; background:#fffaf2; border-radius:16px; padding:30px 40px; box-shadow:0 6px 20px rgba(0,0,0,0.1); font-family:'Segoe UI', sans-serif; }
.profile-container h2 { text-align:center; color:#ff6600; font-weight:700; border-bottom:3px solid #ff6600; padding-bottom:10px; margin-bottom:25px; }
.profile-header { text-align:center; margin-bottom:25px; }
.profile-logo { width:120px; height:120px; border-radius:50%; object-fit:cover; border:4px solid #ffcc80; box-shadow:0 3px 10px rgba(0,0,0,0.15); margin-bottom:10px; }
.upload-btn input { display:none; }
.upload-btn { display:inline-block; background:#ff9933; color:white; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:bold; transition:0.3s; }
.upload-btn:hover { background:#e67e00; }
.form-group { margin-bottom:20px; }
label { font-weight:600; color:#333; display:block; margin-bottom:8px; }
input[type="text"], input[type="email"], textarea { width:100%; padding:10px 12px; border-radius:8px; border:1px solid #ddd; font-size:15px; background:#fff; }
input:focus, textarea:focus { border-color:#ff9933; box-shadow:0 0 5px rgba(255,153,51,0.5); outline:none; }
textarea { resize:none; }
.form-actions { text-align:center; margin-top:25px; }
.btn-save { background:#ff6600; color:white; border:none; padding:10px 20px; border-radius:8px; font-weight:bold; cursor:pointer; transition:0.3s; margin-right:10px; }
.btn-save:hover { background:#e05500; }
.btn-cancel { text-decoration:none; background:#999; color:white; padding:10px 20px; border-radius:8px; font-weight:bold; transition:0.3s; }
.btn-cancel:hover { background:#777; }
</style>
