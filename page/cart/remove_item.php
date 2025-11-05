<?php
session_start();

if (isset($_POST['cart_key'])) {
    $cart_key = $_POST['cart_key'];

    if (isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
        echo "ok";
    } else {
        echo "not_found";
    }
} else {
    echo "no_key";
}
?>
