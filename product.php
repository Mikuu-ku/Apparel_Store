<?php
session_start(); // start the session
include "config/database.php";

// Get the product ID from URL safely
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid product.</p>";
    exit;
}

$id = $_GET['id'];

// Fetch product from database
$product_query = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$product = mysqli_fetch_assoc($product_query);

if (!$product) {
    echo "<p>Product not found</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Apparel's Clothing Line</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- HEADER -->
<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php" class="brand-link">APPAREL'S CLOTHING LINE</a>
        </div>
        <div class="header-right">
            🔍 &nbsp;
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php" class="header-link">Logout</a> | 
                <a href="cart.php" class="header-link">Cart</a>
            <?php else: ?>
                <a href="login.php" class="header-link">Login</a> | 
                <a href="cart.php" class="header-link">Cart</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="container product-detail-container">
    <div class="product-detail">
        <div class="product-images">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="product-info">
            <h1 class="detail-name"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="detail-price">₱<?php echo number_format($product['price'], 2); ?></p>

            <?php if ($product['stock'] > 0): ?>
                <p class="in-stock">In Stock</p>
            <?php else: ?>
                <p class="out-stock">Out of Stock</p>
            <?php endif; ?>

            <p class="detail-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <?php if ($product['stock'] > 0): ?>
                <form class="add-cart-form" method="post" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button type="submit" class="btn-add-cart">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>
