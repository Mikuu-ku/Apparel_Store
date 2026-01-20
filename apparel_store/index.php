<?php
session_start();
include "config/database.php";

$v = "1.2";

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
    $count_row = mysqli_fetch_assoc($count_res);
    $cart_count = $count_row['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apparel Clothing Line</title>

    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/logo.png" alt="Apparel's Clothing Line" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <a href="#" class="header-icon search-spacer" title="Search">
                <i class="fas fa-search"></i>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="header-icon" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="header-icon" title="Login">
                    <i class="fas fa-user"></i>
                </a>
            <?php endif; ?>

            <span class="divider">|</span>

            <a href="cart.php" class="header-icon" title="Cart">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<main class="container">
    <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($products)) { 
        $is_out_of_stock = ($row['stock'] <= 0);
    ?>
        <div class="product-card <?php echo $is_out_of_stock ? 'oos-card' : ''; ?>" 
             data-id="<?php echo $row['id']; ?>"
             data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
             data-price="<?php echo $row['price']; ?>"
             data-stock="<?php echo $row['stock']; ?>"
             data-image="<?php echo $row['image']; ?>"
             data-desc="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
             onclick="<?php echo !$is_out_of_stock ? 'handleQuickView(this)' : ''; ?>">
            
            <?php if($is_out_of_stock): ?>
                <span class="badge">SOLD OUT</span>
            <?php endif; ?>

            <div class="product-link">
                <img src="assets/images/<?php echo $row['image']; ?>?v=<?php echo $v; ?>" alt="Product" style="pointer-events: none;">
                <?php if(!$is_out_of_stock): ?>
                    <div class="hover-container" style="pointer-events: none;">
                        <span class="hover-text">Quick View</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <h3 class="product-name" style="pointer-events: none;"><?php echo $row['name']; ?></h3>
            <p class="price" style="pointer-events: none;">₱<?php echo number_format($row['price'], 2); ?></p>
        </div>
    <?php } ?>
    </div>
</main> 

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <div class="modal-image">
                <img id="modalImg" src="" alt="">
            </div>
            <div class="modal-info">
                <h2 id="modalName"></h2>
                <p id="modalPrice" class="detail-price"></p>
                <p id="modalStock" class="in-stock"></p>
                <p id="modalDesc" class="detail-description"></p>

                <form action="cart.php" method="POST">
                    <input type="hidden" name="product_id" id="modalId">
                    <input type="hidden" name="add_to_cart" value="1">
                    
                    <div class="size-selection">
                        <label class="size-title">SELECT SIZE</label>
                        <div class="size-options">
                            <input type="radio" name="size" value="S" id="s" required><label for="s">S</label>
                            <input type="radio" name="size" value="M" id="m"><label for="m">M</label>
                            <input type="radio" name="size" value="L" id="l"><label for="l">L</label>
                            <input type="radio" name="size" value="XL" id="xl"><label for="xl">XL</label>
                        </div>
                    </div>

                    <div class="qty-selection">
                        <label>QUANTITY</label>
                        <input type="number" name="quantity" value="1" min="1" id="modalQtyInput">
                    </div>

                    <button type="submit" name="add_to_cart" class="btn-add-cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function handleQuickView(element) {
    if (element.classList.contains('oos-card')) return;

    const id = element.getAttribute('data-id');
    const name = element.getAttribute('data-name');
    const price = element.getAttribute('data-price');
    const stock = element.getAttribute('data-stock');
    const image = element.getAttribute('data-image');
    const desc = element.getAttribute('data-desc');

    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPrice').innerText = '₱' + parseFloat(price).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('modalDesc').innerText = desc;
    
    document.getElementById('modalImg').src = 'assets/images/' + image + '?v=<?php echo $v; ?>';
    
    document.getElementById('modalId').value = id;
    document.getElementById('modalStock').innerText = 'Available Stock: ' + stock;
    
    const qtyInput = document.getElementById('modalQtyInput');
    qtyInput.value = 1;
    qtyInput.max = stock;
    
    const modal = document.getElementById('quickViewModal');
    modal.style.display = "block";
    modal.style.zIndex = "10000"; 
}

function closeModal() {
    document.getElementById('quickViewModal').style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById('quickViewModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
</body>
</html>