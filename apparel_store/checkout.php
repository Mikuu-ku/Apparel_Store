<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_query = "SELECT cart.*, products.name, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = $user_id";
$cart_items = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_items) == 0) {
    header("Location: index.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Apparel's</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="checkout-page">

<header class="header">
    <div class="header-container">
        <div class="header-left">
            <a href="cart.php" class="header-icon"><i class="fas fa-chevron-left"></i></a>
        </div>

        <div class="logo">
            <a href="index.php"><img src="assets/images/logo.png" alt="Logo" class="header-logo"></a>
        </div>

        <div class="header-right">
            <a href="javascript:void(0)" class="header-icon search-trigger"><i class="fas fa-search"></i></a>
            
            <div class="user-dropdown">
                <a href="#" class="header-icon user-link">
                    <i class="fas fa-user-circle"></i>
                    <span class="user-name-text"><?= strtoupper(htmlspecialchars($_SESSION['name'] ?? 'KASHMIR')) ?></span>
                    <i class="fas fa-chevron-down arrow-down"></i>
                </a>
            </div>

            <div class="cart-section-wrapper">
                <a href="cart.php" class="header-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-quantity">1</span>
                </a>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <div class="checkout-wrapper">
        <h1 class="checkout-title">Checkout</h1>

        <div class="checkout-grid">
            <div class="checkout-form-section">
                <form action="place_order.php" method="POST">
                    
                    <div class="form-block">
                        <h3 class="section-subtitle">Shipping Details</h3>
                        <div class="form-group">
                            <label class="minimal-label">Full Name</label>
                            <input type="text" name="full_name" class="minimal-input" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="minimal-label">Shipping Address</label>
                            <textarea name="address" class="minimal-input" rows="3" required placeholder="House No., Street, Barangay, City, Province"></textarea>
                        </div>
                    </div>

                    <div class="form-block">
                        <h3 class="section-subtitle">Payment Method</h3>
                        <div class="payment-methods">
                            <label class="method-option">
                                <input type="radio" name="payment_method" value="COD" checked>
                                <span class="method-box">Cash on Delivery</span>
                            </label>
                            <label class="method-option">
                                <input type="radio" name="payment_method" value="GCASH">
                                <span class="method-box">GCash Pay</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-add-cart">Complete Purchase</button>
                </form>
            </div>

            <div class="order-summary-section">
                <div class="receipt-card">
                    <h3 class="section-subtitle">Your Order</h3>
                    
                    <div class="receipt-items">
                        <?php 
                        $total = 0;
                        mysqli_data_seek($cart_items, 0); 
                        while($item = mysqli_fetch_assoc($cart_items)): 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <div class="summary-line">
                            <span class="item-details">
                                <span class="item-name"><?= $item['name'] ?></span>
                                <span class="item-qty">Quantity: <?= $item['quantity'] ?></span>
                            </span>
                            <span class="item-price">₱<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="receipt-footer">
                        <div class="summary-line">
                            <span>Shipping</span>
                            <span class="free-badge">FREE</span>
                        </div>
                        <div class="summary-line total-row">
                            <span>Total Due</span>
                            <span class="total-price">₱<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                    
                    <div class="receipt-note">
                        <p><i class="fas fa-shield-alt"></i> Secure checkout encrypted</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Apparel's Clothing Line.</p>
    </div>
</footer>

</body>
</html>