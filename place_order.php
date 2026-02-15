<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['full_name'])) {
    header("Location: index.php"); 
    exit;
}

$uid = $_SESSION['user_id'];
$name = mysqli_real_escape_string($conn, $_POST['full_name']);
$addr = mysqli_real_escape_string($conn, $_POST['address']);
$pm = mysqli_real_escape_string($conn, $_POST['payment_method']);

$cart_items_query = mysqli_query($conn, "SELECT c.*, p.price, p.name FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $uid");

$grand_total = 0;
$items_to_process = [];

while ($row = mysqli_fetch_assoc($cart_items_query)) {
    $grand_total += ($row['price'] * $row['quantity']);
    $items_to_process[] = $row;
}

if ($grand_total > 0) {
    $sql = "INSERT INTO orders (user_id, full_name, address, total_amount, payment_method, status) 
            VALUES ($uid, '$name', '$addr', $grand_total, '$pm', 'Pending')";
    
    if (mysqli_query($conn, $sql)) {
        $oid = mysqli_insert_id($conn);

        foreach ($items_to_process as $item) {
            $pid = $item['product_id'];
            $qty = $item['quantity'];
            $size_col = "stock_" . strtolower($item['size']); 
            
            mysqli_query($conn, "UPDATE products SET $size_col = $size_col - $qty WHERE id = $pid");
        }

        mysqli_query($conn, "DELETE FROM cart WHERE user_id = $uid");
        
        header("Location: cart.php?order_success=true&id=" . $oid);
        exit;
    }
} else {
    header("Location: cart.php");
    exit;
}
?>