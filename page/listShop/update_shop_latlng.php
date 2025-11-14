<?php
require_once 'class/classdb.php';
$db = new database();

echo "<pre style='font-family:monospace; background:#f9f9f9; padding:10px; border-radius:8px;'>";

$shops = $db->xuatdulieu("SELECT idshop, diachi FROM shop WHERE lat_shop IS NULL OR lng_shop IS NULL");

function getLatLngFromAddressOSM($address) {
    if (stripos($address, 'Việt Nam') === false && stripos($address, 'Vietnam') === false) {
        $address .= ', Việt Nam';
    }

    $address = urlencode($address);
    $url = "https://nominatim.openstreetmap.org/search?format=json&limit=1&q=$address";
    $opts = ["http" => ["header" => "User-Agent: ShopUpdater/1.0\r\n"]];
    $context = stream_context_create($opts);
    $resp = @file_get_contents($url, false, $context);
    if (!$resp) {
        echo "❌ Không kết nối được đến Nominatim\n";
        return null;
    }
    $data = json_decode($resp, true);
    if (!$data || !isset($data[0])) {
        echo "⚠️ Không tìm thấy tọa độ cho: $address\n";
        return null;
    }
    return ['lat' => $data[0]['lat'], 'lng' => $data[0]['lon']];
}


$updated = 0;
foreach ($shops as $shop) {
    $address = trim($shop['diachi']);
    $loc = getLatLngFromAddressOSM($address);

    if ($loc) {
        $lat = $loc['lat'];
        $lng = $loc['lng'];
        $id = (int)$shop['idshop'];
        $db->thucthi("UPDATE shop SET lat_shop='$lat', lng_shop='$lng' WHERE idshop='$id'");
        $updated++;
        echo "✅ Đã cập nhật: {$address} → ($lat, $lng)\n";
        flush();
        sleep(1);
    } else {
        echo "⚠️ Không tìm thấy tọa độ cho: {$address}\n";
    }
}

echo "\nHoàn tất! Đã cập nhật $updated shop.";
echo "</pre>";
?>
