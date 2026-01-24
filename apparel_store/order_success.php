<?php
session_start();
// Optional: Check if the user just came from a successful order
// if (!isset($_SESSION['last_order_id'])) { header("Location: index.php"); exit; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - Apparel's</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .success-wrapper {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        .success-content {
            max-width: 500px;
            width: 100%;
        }

        .success-icon {
            font-size: 50px;
            color: #111;
            margin-bottom: 30px;
            animation: fadeInDown 0.8s ease-out;
        }

        .success-title {
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .success-message {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
            margin-bottom: 40px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .order-number {
            display: inline-block;
            background: #f9f9f9;
            padding: 15px 30px;
            border: 1px solid #eee;
            font-weight: 700;
            letter-spacing: 2px;
            font-size: 12px;
            margin-bottom: 40px;
        }

        .success-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-outline {
            display: block;
            width: 100%;
            padding: 18px;
            border: 1px solid #111;
            background: transparent;
            color: #111;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .btn-outline:hover {
            background: #111;
            color: #fff;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo" style="margin: 0 auto;">
            <a href="index.php"><img src="assets/images/logo.png" alt="Logo" class="header-logo"></a>
        </div>
    </div>
</header>

<main class="container success-wrapper">
    <div class="success-content">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Order Received</h1>
        
        <p class="success-message">
            Thank you for your purchase. We are currently processing your order and will notify you as soon as it ships.
        </p>

        <?php if(isset($_SESSION['last_order_id'])): ?>
        <div class="order-number">
            ORDER #<?= $_SESSION['last_order_id'] ?>
        </div>
        <?php endif; ?>

        <div class="success-actions">
            <a href="orders.php" class="btn-outline">View Order History</a>
            <a href="index.php" class="btn-outline" style="border-color: #eee; color: #888;">Return to Shop</a>
        </div>
    </div>
</main>

<footer class="footer-bottom">
    <p>&copy; <?= date('Y') ?> Apparel's Clothing Line.</p>
</footer>

</body>
</html>