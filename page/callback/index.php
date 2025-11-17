<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$obj = new database();

$config = [
    "app_id" => 2554,
    "key1" => "sdngKKJmqEMzvh5QQcdD2A9XBSKUNaYn",
    "key2" => "trMrHtvjo6myautxDUiAcYsVtaeQ8nhf"
];

$json = file_get_contents("php://input");
$data = json_decode($json, true);

file_put_contents(__DIR__ . "/callback_log.txt", date("Y-m-d H:i:s") . " | " . $json . PHP_EOL, FILE_APPEND);

$result = [];

try {
    $reqmac = hash_hmac("sha256", $data["data"], $config["key2"]);

    if ($reqmac != $data["mac"]) {
        $result["return_code"] = -1;
        $result["return_message"] = "Invalid MAC";
    } else {
        $order = json_decode($data["data"], true);
        $embed = json_decode($order["embed_data"], true);

        $order_ids = $embed["orders"] ?? [];
        if (!empty($order_ids)) {
            foreach ($order_ids as $iddonban) {
                $sql = "UPDATE donban SET trangthai='Đã thanh toán' WHERE iddonban='$iddonban'";
                $obj->themxoasua($sql);
                file_put_contents(__DIR__ . "/callback_log.txt", "✅ Đã cập nhật trạng thái cho đơn: $iddonban" . PHP_EOL, FILE_APPEND);
            }

            if (isset($embed["parent_order_id"])) {
                $parent_id = $embed["parent_order_id"];

                $sql = "UPDATE donban_tong SET trangthai='Đã thanh toán' WHERE idtong='$parent_id'";
                $obj->themxoasua($sql);

                file_put_contents(__DIR__ . "/callback_log.txt", "🔥 Đã cập nhật đơn tổng: $parent_id" . PHP_EOL, FILE_APPEND);
            }

            $result["return_code"] = 1;
            $result["return_message"] = "success";
        } else {
            file_put_contents(__DIR__ . "/callback_log.txt", "❌ Không tìm thấy iddonban trong embed_data" . PHP_EOL, FILE_APPEND);
            $result["return_code"] = 0;
            $result["return_message"] = "Missing order_ids";
        }
    }
} catch (Exception $e) {
    $result["return_code"] = 0;
    $result["return_message"] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($result);
?>
