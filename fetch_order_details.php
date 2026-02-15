<?php
session_start();
include "config/database.php";

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    exit("<p style='padding:20px; text-align:center;'>Invalid Request</p>");
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user_id'];

$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_id' AND user_id = '$user_id'");
$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    exit("<p style='padding:20px; text-align:center;'>Order not found.</p>");
}

$items_query = mysqli_query($conn, "
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = '$order_id'
");
?>

<div style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
    <h3 style="text-transform: uppercase; letter-spacing: 2px; margin: 0; font-size: 18px;">
        Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
    </h3>
    <p style="font-size: 11px; color: #888; margin: 5px 0 0 0; text-transform: uppercase; letter-spacing: 1px;">
        Placed on <?php echo date("F d, Y", strtotime($order['created_at'])); ?>
    </p>
</div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="text-align: left; font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #eee;">
            <th style="padding: 10px 0;">Product</th>
            <th style="padding: 10px 0;">Size</th>
            <th style="padding: 10px 0; text-align: center;">Qty</th>
            <th style="padding: 10px 0; text-align: right;">Price</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if(mysqli_num_rows($items_query) > 0):
            while($item = mysqli_fetch_assoc($items_query)): 
        ?>
        <tr style="border-bottom: 1px solid #fafafa;">
            <td style="padding: 15px 0; display: flex; align-items: center; gap: 15px;">
                <img src="assets/images/<?php echo $item['image']; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #eee;">
                <span style="font-size: 13px; font-weight: 500; color: #111;"><?php echo htmlspecialchars($item['name']); ?></span>
            </td>
            <td style="font-size: 13px; color: #666;"><?php echo $item['size'] ?? 'N/A'; ?></td>
            <td style="font-size: 13px; color: #666; text-align: center;"><?php echo $item['quantity']; ?></td>
            <td style="font-size: 13px; font-weight: 700; color: #111; text-align: right;">
                ₱<?php echo number_format($item['price'], 2); ?>
            </td>
        </tr>
        <?php 
            endwhile; 
        else:
        ?>
            <tr>
                <td colspan="4" style="padding: 30px; text-align: center; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                    Order items could not be loaded.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="margin-top: 25px; border-top: 2px solid #111; padding-top: 15px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <span style="text-transform: uppercase; font-size: 11px; font-weight: 700; letter-spacing: 1px;">Total Amount Paid</span>
        <span style="font-size: 20px; font-weight: 700; color: #111;">
            ₱<?php echo number_format($order['total_amount'], 2); ?>
        </span>
    </div>

    <div style="background: #f9f9f9; padding: 15px; border: 1px solid #eee;">
        <h4 style="margin: 0 0 8px 0; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #888;">Delivery Information</h4>
        <div style="font-size: 12px; line-height: 1.6; color: #333;">
            <p style="margin: 0;"><strong>Recipient:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
            <p style="margin: 5px 0;"><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
            <p style="margin: 0;"><strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?></p>
        </div>
    </div>
</div>

<div style="margin-top: 20px; text-align: center;">
    <p style="font-size: 10px; color: #aaa; text-transform: uppercase; letter-spacing: 1px;">
        Order Status: <span style="color: #111; font-weight: 700;"><?php echo strtoupper($order['status']); ?></span>
    </p>
</div>