<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $cart_id = mysqli_real_escape_string($conn, $_GET['id']);
    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: cart.php?status=removed");
        exit();
    } else {
        header("Location: cart.php?status=error");
        exit();
    }
} else {
    header("Location: cart.php");
    exit();
}
?>