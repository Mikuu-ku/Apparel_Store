<?php
session_start();
include "config/database.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$cart_query = "
    SELECT cart.*, products.name, products.price 
    FROM cart 
    JOIN products ON cart.product_id = products.id
    WHERE cart.user_id = $user_id
";
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
    <title>Checkout - Apparel's Clothing Line</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .checkout-grid { display: grid; grid-template-columns: 1fr 400px; gap: 50px; margin-top: 40px; }
        .checkout-section h3 { 
            text-transform: uppercase; letter-spacing: 2px; font-size: 14px; 
            border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 25px; 
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 8px; }
        .form-control { 
            width: 100%; padding: 12px; border: 1px solid #eee; 
            font-family: inherit; box-sizing: border-box; 
        }
        .order-summary-card { background: #f9f9f9; padding: 30px; border: 1px solid #eee; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px; }
        .summary-total { 
            display: flex; justify-content: space-between; margin-top: 20px; 
            padding-top: 20px; border-top: 1px solid #ddd; font-weight: 700; font-size: 18px; 
        }
        .payment-methods { margin-top: 20px; }
        .method-option { 
            display: flex; align-items: center; gap: 10px; padding: 15px; 
            border: 1px solid #eee; background: #fff; margin-bottom: 10px; cursor: pointer;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo"><a href="index.php"><img src="assets/images/logo.png" alt="Logo" class="header-logo"></a></div>
        <div class="header-right">
            <a href="cart.php" class="header-icon"><i class="fas fa-arrow-left"></i> Back to Cart</a>
        </div>
    </div>
</header>

<main class="container">
    <h1 style="text-transform: uppercase; letter-spacing: 2px;">Checkout</h1>

    <div class="checkout-grid">
        <div class="checkout-section">
            <form action="place_order.php" method="POST">
                <h3>Shipping Information</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($_SESSION['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Complete Address</label>
                    <textarea name="address" class="form-control" rows="3" required placeholder="House No., Street, Brgy, City"></textarea>
                </div>

                <h3 style="margin-top: 40px;">Payment Method</h3>
                <div class="payment-methods">
                    <label class="method-option">
                        <input type="radio" name="payment_method" value="COD" checked>
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="method-option">
                        <input type="radio" name="payment_method" value="GCASH">
                        <span>GCash</span>
                    </label>
                </div>

                <button type="submit" class="btn-add-cart" style="width: 100%; margin-top: 30px;">Place Order Now</button>
            </form>
        </div>

        <div class="order-summary">
            <div class="order-summary-card">
                <h3>Order Summary</h3>
                <?php 
                $total = 0;
                while($item = mysqli_fetch_assoc($cart_items)): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                <div class="summary-item">
                    <span><?= $item['name'] ?> (x<?= $item['quantity'] ?>)</span>
                    <span>₱<?= number_format($subtotal, 2) ?></span>
                </div>
                <?php endwhile; ?>

                <div class="summary-item" style="color: #888;">
                    <span>Shipping Fee</span>
                    <span>₱0.00</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>₱<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>