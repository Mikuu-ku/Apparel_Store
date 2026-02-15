<?php
session_start();
include "config/database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $total_amount = mysqli_real_escape_string($conn, $_POST['total_amount']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $status = 'Pending'; 

    $order_sql = "INSERT INTO orders (user_id, full_name, address, total_amount, payment_method, status) 
                  VALUES ('$user_id', '$full_name', '$address', '$total_amount', '$payment_method', '$status')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn); 

        $cart_query = "SELECT product_id, quantity, size FROM cart WHERE user_id = '$user_id'";
        $cart_result = mysqli_query($conn, $cart_query);

        while ($row = mysqli_fetch_assoc($cart_result)) {
            $p_id = $row['product_id'];
            $qty = $row['quantity'];
            $size = strtolower($row['size']); 

            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity) 
                         VALUES ('$order_id', '$p_id', '$qty')";
            mysqli_query($conn, $item_sql);

            $update_stock_sql = "UPDATE products 
                                 SET stock_$size = stock_$size - $qty 
                                 WHERE id = '$p_id'";
            mysqli_query($conn, $update_stock_sql);
        }

        mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
        echo "success";
    } else {
        echo "error";
    }
}
?>