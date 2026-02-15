<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$data = $_SESSION['pending_cart'] ?? $_POST;

if (isset($data['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = mysqli_real_escape_string($conn, $data['product_id']);
    $size = mysqli_real_escape_string($conn, $data['size']);
    $quantity = (int)$data['quantity']; 

    if (empty($size)) {
        header("Location: index.php");
        exit();
    }

    $check_query = "SELECT id FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id' AND size = '$size'";
    $check = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check) > 0) {
        $query = "UPDATE cart SET quantity = quantity + $quantity 
                  WHERE user_id = '$user_id' AND product_id = '$product_id' AND size = '$size'";
    } else {
        $query = "INSERT INTO cart (user_id, product_id, quantity, size) 
                  VALUES ('$user_id', '$product_id', '$quantity', '$size')";
    }

    if (mysqli_query($conn, $query)) {
        unset($_SESSION['pending_cart']);
        header("Location: cart.php?action=added");
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>