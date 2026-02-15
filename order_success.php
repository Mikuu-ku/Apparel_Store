<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$first_name = $_SESSION['first_name'] ?? 'USER';

// Fetch cart count
$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Processing | Apparel Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Loading Overlay Styling */
        #loader-container {
            position: fixed;
            inset: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .loader-text {
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 10px;
            font-weight: 700;
            color: #111;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Hide content initially */
        #success-content {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s ease;
            display: none;
        }

        #success-content.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>

<div id="loader-container">
    <div class="spinner"></div>
    <div class="loader-text">Securing Your Order...</div>
</div>

<div id="success-content">
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php"><img src="assets/images/new_logo.jpg" alt="Logo" class="header-logo"></a>
            </div>
            <div class="header-right">
                <div class="user-dropdown">
                    <a href="javascript:void(0)" class="header-icon user-link">
                        <i class="fas fa-user-circle"></i> 
                        <span class="user-name-text"><?= strtoupper(htmlspecialchars($first_name)) ?></span>
                        <i class="fas fa-chevron-down arrow-down"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                        <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                        <hr>
                        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                <div class="cart-section-wrapper">
                    <a href="cart.php" class="header-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="cart-quantity"><?= $cart_count ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="profile-card" style="text-align: center; margin-top: 80px;">
            <div class="success-icon" style="margin-bottom: 20px;">
                <i class="fas fa-check-circle" style="font-size: 60px; color: #111;"></i>
            </div>
            <h2 class="profile-title">Order Successful</h2>
            <div class="info-group">
                <p style="text-align: center; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 1px;">
                    Your payment has been processed. We are preparing your items for shipment.
                </p>
            </div>
            <?php if(isset($_SESSION['last_order_id'])): ?>
            <div class="info-group">
                <label>Order Reference</label>
                <p>#<?= $_SESSION['last_order_id'] ?></p>
            </div>
            <?php endif; ?>
            <div class="profile-actions" style="margin-top: 40px;">
                <a href="orders.php" class="btn-save" style="text-decoration: none; margin-bottom: 10px;">Track My Order</a>
                <a href="index.php" class="btn-cancel">Continue Shopping</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
        </div>
    </footer>
</div>

<script>
    // Logic to handle the Loading -> Success transition
    window.addEventListener('load', function() {
        setTimeout(function() {
            const loader = document.getElementById('loader-container');
            const content = document.getElementById('success-content');
            
            // Fade out loader
            loader.style.opacity = '0';
            
            setTimeout(function() {
                loader.style.display = 'none';
                // Show success card with pop-up effect
                content.style.display = 'block';
                setTimeout(() => content.classList.add('show'), 50);
            }, 500);
            
        }, 2000); // 2 second fake loading time for professional feel
    });
</script>

</body>
</html>