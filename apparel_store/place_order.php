<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['full_name'])) {
    header("Location: index.php"); exit;
}

$uid = $_SESSION['user_id'];
$name = mysqli_real_escape_string($conn, $_POST['full_name']);
$addr = mysqli_real_escape_string($conn, $_POST['address']);
$pm = $_POST['payment_method'];

$res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(p.price * c.quantity) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $uid"));
$grand_total = $res['total'];

if ($grand_total > 0) {
    $sql = "INSERT INTO orders (user_id, full_name, address, total_amount, payment_method, status) VALUES ($uid, '$name', '$addr', $grand_total, '$pm', 'Pending')";
    if (mysqli_query($conn, $sql)) {
        $oid = mysqli_insert_id($conn);
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $uid");
        header("Location: cart.php?order_success=true&id=" . $oid);
    }
} else {
    header("Location: cart.php");
}
?>