<?php
session_start();
include "config/database.php";

if (isset($_POST['product_id'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $quantity = (int)$_POST['quantity'];

    $size_column = "stock_" . strtolower($size);
    $stock_query = mysqli_query($conn, "SELECT name, $size_column FROM products WHERE id = '$product_id'");
    $product = mysqli_fetch_assoc($stock_query);

    if (!$product || $product[$size_column] < $quantity) {
        echo "<script>
                alert('Insufficient stock for " . $product['name'] . " in size " . strtoupper($size) . ". Only " . ($product[$size_column] ?? 0) . " left.');
                window.location.href = 'index.php';
              </script>";
        exit();
    }

    $_SESSION['pending_cart'] = [
        'product_id' => $product_id,
        'size' => $size,
        'quantity' => $quantity
    ];

    if (isset($_SESSION['user_id'])) {
        header("Location: process_cart.php");
    } else {
        header("Location: login.php?msg=please_login");
    }
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>