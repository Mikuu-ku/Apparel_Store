<?php
include "../config/database.php";

if (!isset($_GET['id'])) {
    die("Order ID not specified.");
}

$order_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM orders WHERE id = '$order_id'";
$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Order not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $order['id']; ?></title>
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    <style>
        body { font-family: 'Courier New', Courier, monospace; color: #000; padding: 40px; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 40px; }
        .header h1 { text-transform: uppercase; letter-spacing: 5px; margin: 0; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 40px; font-size: 14px; }
        .section-title { text-transform: uppercase; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 10px; display: block; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { text-align: left; text-transform: uppercase; border-bottom: 1px solid #000; padding: 10px 0; font-size: 12px; }
        td { padding: 15px 0; border-bottom: 1px solid #eee; font-size: 13px; }
        .total-row { text-align: right; font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .footer { text-align: center; margin-top: 50px; font-size: 10px; text-transform: uppercase; color: #888; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body onload="window.print()">

    <div class="invoice-box">
        <div class="header">
            <h1>APPAREL'S</h1>
            <p>CLOTHING LINE • OFFICIAL INVOICE</p>
        </div>

        <div class="info-grid">
            <div>
                <span class="section-title">Billed To</span>
                <strong><?php echo $order['full_name']; ?></strong><br>
                <?php echo $order['address']; ?>
            </div>
            <div style="text-align: right;">
                <span class="section-title">Order Details</span>
                Invoice #: <?php echo $order['id']; ?><br>
                Date: <?php echo date('M d, Y', strtotime($order['created_at'])); ?><br>
                Payment: <?php echo $order['payment_method']; ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Order Purchase (#<?php echo $order['id']; ?>)</td>
                    <td style="text-align: right;">₱<?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-row">
            Total Amount: ₱<?php echo number_format($order['total_amount'], 2); ?>
        </div>

        <div class="footer">
            Thank you for shopping with Apparel's Clothing Line.<br>
            This is a computer-generated document.
        </div>
    </div>

</body>
</html>