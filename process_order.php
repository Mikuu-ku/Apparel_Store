<?php
session_start();
include "config/database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $status = 'Pending'; 

    // 1. Calculate the Total Amount on the backend to prevent ₱0.00
    $shipping_fee = 50.00; // Adjust this to your actual shipping fee
    $cart_total_query = "SELECT SUM(p.price * c.quantity) as subtotal 
                         FROM cart c 
                         JOIN products p ON c.product_id = p.id 
                         WHERE c.user_id = '$user_id'";
    $total_res = mysqli_query($conn, $cart_total_query);
    $total_row = mysqli_fetch_assoc($total_res);
    
    $subtotal = $total_row['subtotal'] ?? 0;
    $total_amount = $subtotal + $shipping_fee;

    // 2. Insert into orders table with the correct total
    $order_sql = "INSERT INTO orders (user_id, full_name, address, total_amount, payment_method, status) 
                  VALUES ('$user_id', '$full_name', '$address', '$total_amount', '$payment_method', '$status')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn); 

        // 3. Fetch items and prices to save in order_items
        $cart_query = "SELECT c.product_id, c.quantity, c.size, p.price 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = '$user_id'";
        $cart_result = mysqli_query($conn, $cart_query);

        while ($row = mysqli_fetch_assoc($cart_result)) {
            $p_id = $row['product_id'];
            $qty = $row['quantity'];
            $size = $row['size'];
            $price = $row['price'];
            $size_lower = strtolower($size); 

            // Save size and price in order_items
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, size, price) 
                         VALUES ('$order_id', '$p_id', '$qty', '$size', '$price')";
            mysqli_query($conn, $item_sql);

            // Update product stock
            $update_stock_sql = "UPDATE products 
                                 SET stock_$size_lower = stock_$size_lower - $qty 
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