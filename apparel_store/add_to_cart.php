<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    $product_query = mysqli_query($conn, "SELECT stock FROM products WHERE id = $product_id");
    $product = mysqli_fetch_assoc($product_query);

    if ($product && $product['stock'] >= $quantity) {
        $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");
        if (mysqli_num_rows($cart_query) > 0) {
            mysqli_query($conn, "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id");
        } else {
            mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
        }

        header("Location: cart.php");
        exit;
    } else {
        echo "<p>Product not available or insufficient stock.</p>";
        exit;
    }
} else {
    echo "<p>Invalid request.</p>";
    exit;
}
