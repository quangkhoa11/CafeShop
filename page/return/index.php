<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!empty($_GET)) {
    file_put_contents(__DIR__ . "/return_log.txt", date("Y-m-d H:i:s") . " | " . json_encode($_GET) . PHP_EOL, FILE_APPEND);
}

header("Location: index.php?page=listShop");
exit();
?>
