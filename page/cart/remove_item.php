<?php
session_start();
if (isset($_POST['idsp'])) {
    $idsp = $_POST['idsp'];
    unset($_SESSION['cart'][$idsp]);
    echo "ok";
}
?>
