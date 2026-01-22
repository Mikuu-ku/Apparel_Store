<?php
session_start();

if (isset($_POST['product_id'])) {
    $_SESSION['pending_cart'] = [
        'product_id' => $_POST['product_id'],
        'size' => $_POST['size'],
        'quantity' => $_POST['quantity']
    ];

    if (isset($_SESSION['user_id'])) {
        header("Location: process_cart.php");
    } else {
        header("Location: login.php?msg=please_login");
    }
    exit();
}
?>