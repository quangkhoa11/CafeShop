<?php
$db = new database();

$sql = "
  SELECT 
    s.idshop, s.tenshop, s.diachi, s.anhbia, s.logo, s.lat_shop, s.lng_shop,
    ROUND(AVG(r.diem),1) AS avg_diem,
    COUNT(r.idrating) AS total_reviews
  FROM shop s
  LEFT JOIN sanpham sp ON s.idshop = sp.idshop
  LEFT JOIN rating_sanpham r ON sp.idsp = r.idsp
  GROUP BY s.idshop
";
$shops = $db->xuatdulieu($sql);
?>

<title>Cửa hàng</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
<div class="max-w-7xl mx-auto px-4 py-10">

  <h1 class="text-2xl font-semibold text-gray-800 mb-8 text-center">Danh sách cửa hàng</h1>

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8" id="shopGrid">
    <?php foreach ($shops as $shop): 
      $avg = $shop['avg_diem'] ?? 0;
      $total = $shop['total_reviews'] ?? 0;
      $fullStars = floor($avg);
      $halfStar = ($avg - $fullStars >= 0.5) ? 1 : 0;
    ?>
      <div class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col border border-gray-100 shop-card"
           data-lat="<?php echo $shop['lat_shop'] ?? 0; ?>" 
           data-lng="<?php echo $shop['lng_shop'] ?? 0; ?>" 
           data-shop-id="<?php echo $shop['idshop']; ?>">

        <div class="w-full h-40 overflow-hidden rounded-t-2xl">
          <img 
            src="assets/images/<?php echo htmlspecialchars($shop['anhbia']); ?>" 
            alt="Ảnh bìa shop" 
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-in-out">
        </div>

        <div class="flex justify-center -mt-10">
          <div class="w-20 h-20 rounded-full border-4 border-white bg-white shadow-md overflow-hidden flex items-center justify-center">
            <img 
              src="assets/images/<?php echo htmlspecialchars($shop['logo']); ?>" 
              alt="Logo shop" 
              class="w-full h-full object-cover rounded-full">
          </div>
        </div>

        <div class="pt-6 pb-5 px-4 text-center flex flex-col flex-1 justify-between">

          <div>
            <h2 class="text-lg font-semibold text-gray-800 mb-1 truncate group-hover:text-amber-600 transition-colors duration-200">
              <?php echo htmlspecialchars($shop['tenshop']); ?>
            </h2>

            <p class="text-sm text-gray-500 mb-2 break-words">
              📍 <?php echo htmlspecialchars($shop['diachi']); ?>
            </p>

            <div class="flex items-center justify-center text-yellow-500 mb-2">
              <?php 
              if($total > 0){
                  for($i=0; $i<$fullStars; $i++) echo "⭐";
                  if($halfStar) echo "✩";
                  for($i=0; $i<5-$fullStars-$halfStar; $i++) echo "☆";
              } else {
                  echo "☆☆☆☆☆";
              }
              ?>
              <span class="ml-1 text-gray-700 font-medium text-sm">(<?php echo $total; ?> đánh giá)</span>
            </div>

            <p class="text-sm text-gray-500 mb-auto distance-text" data-default="0.5">
              🚶 Khoảng cách: ...
            </p>
          </div>

          <div class="mt-4">
            <a href="?page=shop_detail&idshop=<?php echo $shop['idshop']; ?>"
               class="inline-block bg-yellow-300 hover:bg-yellow-600 text-sm font-medium py-2 px-5 rounded-full shadow-md transition-all duration-300">
              Xem chi tiết
            </a>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) ** 2;
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".shop-card");
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      const { latitude, longitude } = pos.coords;
      cards.forEach(card => {
        const shopLat = parseFloat(card.dataset.lat);
        const shopLng = parseFloat(card.dataset.lng);
        const distanceEl = card.querySelector(".distance-text");

        if (!isNaN(shopLat) && !isNaN(shopLng) && shopLat !== 0 && shopLng !== 0) {
  const dist = getDistanceFromLatLonInKm(latitude, longitude, shopLat, shopLng);
  distanceEl.innerText = `🚶 Khoảng cách: ${dist.toFixed(1)} km`;
} else {
  distanceEl.innerText = "🚶 Khoảng cách: chưa có dữ liệu định vị";
  console.warn("⚠️ Shop chưa có lat/lng hợp lệ:", card.dataset.shopId, card.querySelector("h2")?.innerText);
}

      });
    }, () => {
      document.querySelectorAll(".distance-text").forEach(el => {
        el.innerText = "🚶 Khoảng cách: không xác định được vị trí";
      });
    });
  } else {
    document.querySelectorAll(".distance-text").forEach(el => {
      el.innerText = "🚶 Khoảng cách: không hỗ trợ định vị";
    });
  }
});
</script>