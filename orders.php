<?php
session_start();
include "config/database.php";

// --- AUTH CHECK ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$v = "2.1";

// Fetch User Info for Header (Syncing with first_name column)
$user_query = mysqli_query($conn, "SELECT first_name FROM users WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($user_query);
$display_name = $user_data['first_name'] ?? 'USER';

// Fetch Cart Count for Header
$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total'] ?? 0;

/**
 * Fetch Orders
 */
$orders_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Apparel Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/new_logo.jpg" alt="Logo" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <div class="user-dropdown">
                <a href="javascript:void(0)" class="header-icon">
                    <i class="fas fa-user-circle"></i> 
                    <span class="user-name-text"><?= strtoupper(htmlspecialchars($display_name)) ?></span>
                    <i class="fas fa-chevron-down" style="font-size: 8px; margin-left: 5px;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                    <hr>
                    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <span class="divider">|</span>

            <a href="cart.php" class="header-icon">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<main class="container" style="min-height: 70vh;">
    <div class="cart-header" style="margin-top: 50px; margin-bottom: 40px;">
        <h2 class="order-history-title">Order History</h2>
        <p style="font-size: 11px; color: #888; letter-spacing: 1px; text-transform: uppercase; margin-top: 5px;">Track your recent purchases and status</p>
    </div>

    <?php if(mysqli_num_rows($orders_result) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($orders_result)): 
                        $total = $order['total_price'] ?? ($order['amount'] ?? 0);
                        $status = $order['status'] ?? 'Pending';
                    ?>
                    <tr>
                        <td class="order-id-cell">
                            #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td>
                            <?php echo date("M d, Y", strtotime($order['created_at'])); ?>
                        </td>
                        <td class="order-total-cell">
                            ₱<?php echo number_format($total, 2); ?>
                        </td>
                        <td>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <button onclick="viewOrderDetails(<?php echo $order['id']; ?>)" class="btn-view-details">
                                View Details
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <i class="fas fa-shopping-bag" style="font-size: 40px; color: #ccc; margin-bottom: 20px;"></i>
            <p style="color: #888; text-transform: uppercase; letter-spacing: 2px; font-size: 14px;">You haven't placed any orders yet.</p>
            <a href="index.php" class="btn-add-cart" style="display: inline-block; width: auto; padding: 15px 50px; text-decoration: none; margin-top: 30px; border: none; background: #111; color: #fff; font-size: 11px; letter-spacing: 2px; text-transform: uppercase;">Start Shopping</a>
        </div>
    <?php endif; ?>
</main>

<div id="orderModal" class="order-modal">
    <div class="order-modal-content">
        <span class="close-order-modal" onclick="closeOrderModal()">&times;</span>
        <div id="orderDetailsBody">
            </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<script>
function viewOrderDetails(orderId) {
    const modal = document.getElementById('orderModal');
    const body = document.getElementById('orderDetailsBody');
    
    modal.style.display = "block";
    
    body.innerHTML = '<div class="loader"><i class="fas fa-spinner fa-spin"></i> LOADING DETAILS...</div>';

    fetch('fetch_order_details.php?id=' + orderId)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(data => {
            body.innerHTML = data;
        })
        .catch(err => {
            body.innerHTML = '<p style="color:red; text-align:center; padding: 20px;">Error loading order details. Please try again.</p>';
            console.error('Fetch error:', err);
        });
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target == modal) {
        closeOrderModal();
    }
}
</script>

</body>
</html>