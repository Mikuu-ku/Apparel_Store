<?php
session_start();
include "config/database.php";

$v = "6.5"; // Version bumped for CSS refresh

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Add to Cart logic if coming from Quick View
if (isset($_POST['add_to_cart'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $quantity = (int)$_POST['quantity'];

    $check_cart = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'");

    if (mysqli_num_rows($check_cart) > 0) {
        $cart_row = mysqli_fetch_assoc($check_cart);
        $new_qty = $cart_row['quantity'] + $quantity;
        mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE id = " . $cart_row['id']);
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, size, quantity) VALUES ($user_id, $product_id, '$size', $quantity)");
    }
    
    header("Location: cart.php");
    exit();
}

$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total'] ?? 0;

$cart_query = "SELECT c.*, p.name, p.price, p.image, p.description, p.stock_s, p.stock_m, p.stock_l, p.stock_xl 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = $user_id";
$cart_items = mysqli_query($conn, $cart_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Bag | Apparel Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        window.history.forward();
        function noBack() { window.history.forward(); }
    </script>
</head>
<body onload="noBack();" onpageshow="if (event.persisted) noBack();">

<?php if (isset($_GET['order_success'])): ?>
    <div class="order-success-overlay" id="successOverlay">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <h2 class="success-title">Order Confirmed</h2>
        <p class="success-sub">Thank you for shopping with Apparel's.</p>
        <a href="index.php" class="btn-add-cart" style="width: auto; padding: 15px 40px; text-decoration: none;">Continue Shopping</a>
    </div>
<?php endif; ?>

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/new_logo.jpg" alt="Logo" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <div class="user-dropdown">
                <a href="javascript:void(0)" class="header-icon dropdown-trigger">
                    <i class="fas fa-user-circle"></i> 
                    <span class="user-name-text"><?= strtoupper($_SESSION['first_name'] ?? 'USER') ?></span>
                    <i class="fas fa-chevron-down" style="font-size: 8px; margin-left: 5px;"></i>
                </a>
                <div class="dropdown-content">
                    <a href="profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                    <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                    <hr>
                    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <span class="divider">|</span>

            <a href="cart.php" class="header-icon active-cart" title="Cart">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<main class="container">
    <?php if(isset($_GET['status']) && $_GET['status'] == 'removed'): ?>
        <div id="status-msg" class="error-msg" style="background: #f9f9f9; color: #000; text-align: center; border: 1px solid #eee;">
            ITEM SUCCESSFULLY REMOVED FROM BAG
        </div>
    <?php endif; ?>

    <div class="cart-header" style="margin-top: 40px; margin-bottom: 40px;">
        <h2 style="text-transform: uppercase; letter-spacing: 3px; font-weight: 700; font-size: 24px;">Your Shopping Bag</h2>
        <p style="font-size: 11px; color: #888; letter-spacing: 1px; text-transform: uppercase;">Review your items before checkout</p>
    </div>
    
    <?php if(mysqli_num_rows($cart_items) > 0): ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 50px;">
                <thead>
                    <tr style="text-align: left; text-transform: uppercase; font-size: 10px; letter-spacing: 2px; border-bottom: 2px solid #111; color: #888;">
                        <th style="padding: 20px 10px;">Item Details</th>
                        <th style="padding: 20px 10px;">Size</th>
                        <th style="padding: 20px 10px;">Price</th>
                        <th style="padding: 20px 10px;">Qty</th>
                        <th style="padding: 20px 10px;">Subtotal</th>
                        <th style="padding: 20px 10px; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_amount = 0;
                    while($item = mysqli_fetch_assoc($cart_items)): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_amount += $subtotal;
                    ?>
                    <tr style="border-bottom: 1px solid #f2f2f2;">
                        <td style="padding: 20px 10px; display: flex; align-items: center; gap: 20px;">
                            <img src="assets/images/<?php echo $item['image']; ?>" style="width: 80px; height: auto; border: 1px solid #f9f9f9;">
                            <div>
                                <span style="display: block; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span style="font-size: 10px; color: #999;">SKU: <?php echo str_pad($item['product_id'], 5, '0', STR_PAD_LEFT); ?></span>
                            </div>
                        </td>
                        <td style="padding: 20px 10px; font-size: 13px; font-weight: 500;"><?php echo $item['size']; ?></td>
                        <td style="padding: 20px 10px; font-size: 13px;">₱<?php echo number_format($item['price'], 2); ?></td>
                        <td style="padding: 20px 10px; font-size: 13px;"><?php echo $item['quantity']; ?></td>
                        <td style="padding: 20px 10px; font-size: 13px; font-weight: 700;">₱<?php echo number_format($subtotal, 2); ?></td>
                        <td style="padding: 20px 10px; text-align: right;">
                            <a href="javascript:void(0)" 
                               onclick="openRemoveModal('<?php echo $item['id']; ?>', '<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>')"
                               style="color: #ff0000; text-decoration: none; font-size: 10px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; border-bottom: 1px solid #ff0000; padding-bottom: 2px;">
                                Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 40px; margin-bottom: 100px;">
            <div style="width: 100%; max-width: 400px; background: #fdfdfd; padding: 30px; border: 1px solid #eee;">
                <h4 style="text-transform: uppercase; letter-spacing: 2px; font-size: 14px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Order Summary</h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px;">
                    <span>Subtotal</span>
                    <span>₱<?php echo number_format($total_amount, 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 13px; color: #888;">
                    <span>Shipping</span>
                    <span style="font-size: 10px;">Calculated at checkout</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 30px; font-size: 18px; font-weight: 700; border-top: 1px solid #eee; padding-top: 20px;">
                    <span>TOTAL</span>
                    <span>₱<?php echo number_format($total_amount, 2); ?></span>
                </div>
                <button class="btn-add-cart" onclick="location.href='checkout.php'">Proceed to Checkout</button>
                <a href="index.php" style="display: block; text-align: center; margin-top: 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; text-decoration: none;">Continue Shopping</a>
            </div>
        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 120px 0; border: 1px dashed #eee;">
            <i class="fas fa-shopping-bag" style="font-size: 40px; color: #eee; margin-bottom: 20px;"></i>
            <p style="color: #888; text-transform: uppercase; letter-spacing: 2px; font-size: 14px;">Your bag is currently empty.</p>
            <a href="index.php" class="btn-add-cart" style="display: inline-block; width: auto; padding: 15px 40px; text-decoration: none; margin-top: 30px;">Back to Shop</a>
        </div>
    <?php endif; ?>
</main>

<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <div class="modal-image"><img id="modalImg" src=""></div>
            <div class="modal-info">
                <h2 id="modalName"></h2>
                <p id="modalPrice" class="detail-price"></p>
                <p id="modalDesc" class="detail-description"></p>
                <form action="add_to_cart_check.php" method="POST">
                    <input type="hidden" name="product_id" id="modalId">
                    <input type="hidden" name="add_to_cart" value="1">
                    <div class="size-selection">
                        <label class="size-title">SELECT SIZE</label>
                        <div class="size-options">
                            <div class="size-item">
                                <input type="radio" name="size" value="S" id="s" required>
                                <label for="s" id="label_s">S</label>
                                <span class="stock-label" id="stock_s"></span>
                            </div>
                            <div class="size-item">
                                <input type="radio" name="size" value="M" id="m">
                                <label for="m" id="label_m">M</label>
                                <span class="stock-label" id="stock_m"></span>
                            </div>
                            <div class="size-item">
                                <input type="radio" name="size" value="L" id="l">
                                <label for="l" id="label_l">L</label>
                                <span class="stock-label" id="stock_l"></span>
                            </div>
                            <div class="size-item">
                                <input type="radio" name="size" value="XL" id="xl">
                                <label for="xl" id="label_xl">XL</label>
                                <span class="stock-label" id="stock_xl"></span>
                            </div>
                        </div>
                    </div>
                    <div class="qty-selection" style="margin-top: 20px;">
                        <label style="font-size: 11px; font-weight: 700;">QUANTITY</label>
                        <input type="number" name="quantity" value="1" min="1" id="modalQtyInput" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd;">
                    </div>
                    <button type="submit" class="btn-add-cart" style="width: 100%; margin-top: 20px; padding: 15px; background: #000; color: #fff; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer;">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="removeModal" class="custom-modal">
    <div class="modal-card">
        <h3 style="font-size: 14px;">Remove Item?</h3>
        <p id="removeText" style="font-size: 12px; margin-top: 10px;"></p>
        <div class="modal-btn-group" style="margin-top: 30px;">
            <button class="btn-modal-cancel" onclick="closeRemoveModal()">Keep Item</button>
            <a id="confirmRemoveBtn" href="#" class="btn-modal-confirm">Remove Now</a>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4 class="footer-title">Customer Service</h4>
            <ul class="footer-links">
                <li><a href="#">Shipping Policy</a></li>
                <li><a href="#">Returns & Exchanges</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<script>
    const searchBtn = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');
    const resultsPopup = document.getElementById('searchResultsPopup');

    if(searchBtn) {
        searchBtn.addEventListener('click', () => {
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) searchInput.focus();
        });
    }

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length > 1) {
                fetch(`fetch_search.php?query=${encodeURIComponent(query)}`)
                    .then(res => res.text())
                    .then(data => {
                        resultsPopup.innerHTML = data;
                        resultsPopup.style.display = 'block';
                    });
            } else {
                resultsPopup.style.display = 'none';
            }
        });
    }

    function handleQuickView(input) {
        const data = input;
        resultsPopup.style.display = 'none';

        document.getElementById('modalName').innerText = data.name;
        document.getElementById('modalPrice').innerText = '₱' + parseFloat(data.price).toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('modalDesc').innerText = data.desc;
        document.getElementById('modalImg').src = 'assets/images/' + data.image;
        document.getElementById('modalId').value = data.id;

        ['s', 'm', 'l', 'xl'].forEach(size => {
            const stock = parseInt(data[size] || 0);
            const inputRadio = document.getElementById(size);
            const label = document.getElementById('label_' + size);
            const stockLabel = document.getElementById('stock_' + size);
            stockLabel.innerText = stock + " LEFT";

            if (stock <= 0) {
                inputRadio.disabled = true;
                label.style.opacity = "0.3";
                label.style.textDecoration = "line-through";
            } else {
                inputRadio.disabled = false;
                label.style.opacity = "1";
                label.style.textDecoration = "none";
            }
        });

        document.getElementById('quickViewModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('quickViewModal').style.display = "none";
    }

    function openRemoveModal(id, name) {
        document.getElementById('removeText').innerHTML = "Are you sure you want to remove<br><strong>" + name + "</strong> from your bag?";
        document.getElementById('confirmRemoveBtn').href = "remove_cart.php?id=" + id;
        document.getElementById('removeModal').style.display = 'flex';
    }

    function closeRemoveModal() {
        document.getElementById('removeModal').style.display = 'none';
    }

    window.onclick = function(e) {
        if (e.target == document.getElementById('removeModal')) closeRemoveModal();
        if (e.target == document.getElementById('quickViewModal')) closeModal();
        if (!e.target.matches('.search-input') && !e.target.matches('.fa-search')) {
            if(resultsPopup) resultsPopup.style.display = 'none';
        }
    }
</script>

</body>
</html>