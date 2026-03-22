<?php
session_start();
include "config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $order_id = (int)$_POST['order_id'];
    $user_id = $_SESSION['user_id'];

    // 1. Verify the order belongs to the user and is still Pending
    $query = "SELECT status FROM orders WHERE id = $order_id AND user_id = $user_id";
    $res = mysqli_query($conn, $query);
    $order = mysqli_fetch_assoc($res);

    if ($order && $order['status'] === 'Pending') {
        
        // 2. Restore the Stock to the products table
        $items_query = "SELECT product_id, quantity, size FROM order_items WHERE order_id = $order_id";
        $items_res = mysqli_query($conn, $items_query);
        
        while ($item = mysqli_fetch_assoc($items_res)) {
            $p_id = $item['product_id'];
            $qty = $item['quantity'];
            $size_col = "stock_" . strtolower($item['size']);
            
            // Add quantity back to the specific size column
            mysqli_query($conn, "UPDATE products SET $size_col = $size_col + $qty WHERE id = $p_id");
        }

        // 3. Update Order Status
        $update_sql = "UPDATE orders SET status = 'Cancelled' WHERE id = $order_id";
        
        if (mysqli_query($conn, $update_sql)) {
            echo "success";
        } else {
            echo "Error updating status: " . mysqli_error($conn);
        }
    } else {
        echo "Order cannot be cancelled. It might be already shipped or completed.";
    }
} else {
    echo "Unauthorized access.";
}
?>