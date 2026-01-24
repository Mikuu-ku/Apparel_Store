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
<body>

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
                <a href="#" class="header-icon"><i class="fas fa-user"></i></a>
            </div>
            <a href="cart.php" class="header-icon"><i class="fas fa-shopping-bag"></i></a>
        </div>
    </div>
</header>

<main class="container">
    <h1 class="checkout-title">Checkout</h1>

    <div class="checkout-grid">
        <div class="checkout-section">
            <form action="place_order.php" method="POST">
                <h3>Shipping Details</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_SESSION['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="address" class="form-control" rows="4" required placeholder="House No., Street, Barangay, City, Province"></textarea>
                </div>

                <h3>Payment Method</h3>
                <div class="payment-methods">
                    <label class="method-option">
                        <input type="radio" name="payment_method" value="COD" checked>
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="method-option">
                        <input type="radio" name="payment_method" value="GCASH">
                        <span>GCash Pay</span>
                    </label>
                </div>
                <button type="submit" class="btn-add-cart">Complete Purchase</button>
            </form>
        </div>

        <div class="order-summary">
            <div class="order-summary-card">
                <h3>Summary</h3>
                <?php 
                $total = 0;
                // Re-fetch items to reset the pointer for the loop
                mysqli_data_seek($cart_items, 0); 
                while($item = mysqli_fetch_assoc($cart_items)): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <div class="summary-item">
                    <span><?= $item['name'] ?> <span class="item-qty">x<?= $item['quantity'] ?></span></span>
                    <span>₱<?= number_format($subtotal, 2) ?></span>
                </div>
                <?php endwhile; ?>

                <div class="summary-item" style="color: #888; font-weight: 400; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
                    <span>Shipping</span>
                    <span>FREE</span>
                </div>
                <div class="summary-total">
                    <span>Total Due</span>
                    <span>₱<?= number_format($total, 2) ?></span>
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