<?php
session_start();
include "config/database.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}

$v = "5.5"; // Version bumped for CSS refresh

$display_name = "USER";
if (isset($_SESSION['user_id'])) {
    if (!isset($_SESSION['first_name'])) {
        $uid = $_SESSION['user_id'];
        $user_query = mysqli_query($conn, "SELECT first_name FROM users WHERE id = $uid");
        if ($user_row = mysqli_fetch_assoc($user_query)) {
            $_SESSION['first_name'] = $user_row['first_name'];
        }
    }
    $display_name = $_SESSION['first_name'] ?? 'USER';
}

$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$query_str = "SELECT * FROM products";
if ($category != '') {
    $query_str .= " WHERE category = '$category'"; 
}
$query_str .= " ORDER BY id DESC";
$products = mysqli_query($conn, $query_str);

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

<header class="header">
    <div class="container header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/new_logo.jpg" alt="Apparel's Logo" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <div class="search-wrapper">
                <input type="text" id="searchInput" class="search-input" placeholder="SEARCH..." autocomplete="off">
                <a href="javascript:void(0)" class="header-icon" id="searchBtn">
                    <i class="fas fa-search"></i>
                </a>
                <div id="searchResultsPopup" class="search-results-popup"></div>
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown">
                    <a href="javascript:void(0)" class="header-icon">
                        <i class="fas fa-user-circle"></i> 
                        <span class="user-name-text"><?= strtoupper(htmlspecialchars($display_name)) ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 8px; margin-left: 5px;"></i>
                    </a>
                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fas fa-id-card"></i> My Profile</a>
                        <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                        <hr>
                        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="header-icon" title="Login"><i class="fas fa-user"></i></a>
            <?php endif; ?>

            <span class="divider">|</span>

            <a href="cart.php" class="header-icon" title="Bag">
                <i class="fas fa-shopping-bag"></i>
                <?php if($cart_count > 0): ?>
                    <span class="cart-count"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</header>

<div class="category-nav-wrapper">
    <div class="container">
        <nav class="category-bar">
            <a href="index.php" class="cat-link <?php echo $category == '' ? 'active' : ''; ?>">ALL</a>
            <a href="index.php?category=Tops" class="cat-link <?php echo $category == 'Tops' ? 'active' : ''; ?>">TOPS</a>
            <a href="index.php?category=Bottoms" class="cat-link <?php echo $category == 'Bottoms' ? 'active' : ''; ?>">BOTTOMS</a>
            <a href="index.php?category=Essentials" class="cat-link <?php echo $category == 'Essentials' ? 'active' : ''; ?>">HOODIES</a>
        </nav>
    </div>
</div>

<main class="container">
    <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($products)) { 
        $total_stock = ($row['stock_s'] ?? 0) + ($row['stock_m'] ?? 0) + ($row['stock_l'] ?? 0) + ($row['stock_xl'] ?? 0);
        $is_out_of_stock = ($total_stock <= 0);
    ?>
        <div class="product-card <?php echo $is_out_of_stock ? 'oos-card' : ''; ?>" 
             data-id="<?php echo $row['id']; ?>"
             data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
             data-price="<?php echo $row['price']; ?>"
             data-image="<?php echo $row['image']; ?>"
             data-desc="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
             data-s="<?php echo $row['stock_s'] ?? 0; ?>"
             data-m="<?php echo $row['stock_m'] ?? 0; ?>"
             data-l="<?php echo $row['stock_l'] ?? 0; ?>"
             data-xl="<?php echo $row['stock_xl'] ?? 0; ?>"
             onclick="<?php echo !$is_out_of_stock ? 'handleQuickView(this)' : ''; ?>">
            
            <?php if($is_out_of_stock): ?>
                <span class="badge">SOLD OUT</span>
            <?php endif; ?>

            <div class="product-link">
                <img src="assets/images/<?php echo $row['image']; ?>" alt="Product">
                <?php if(!$is_out_of_stock): ?>
                    <div class="hover-container"><span class="hover-text">Quick View</span></div>
                <?php endif; ?>
            </div>
            <h3 class="product-name"><?php echo $row['name']; ?></h3>
            <p class="price">₱<?php echo number_format($row['price'], 2); ?></p>
        </div>
    <?php } ?>
    </div>
</main> 

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4 class="footer-title">Customer Service</h4>
            <ul class="footer-links">
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Size Guide</a></li>
                <li><a href="#">FAQs</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4 class="footer-title">Follow Us</h4>
            <div class="social-links">
                <a href="https://www.facebook.com/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.instagram.com" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com" class="social-icon"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

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
                    
                    <button type="submit" class="btn-add-cart" style="width: 100%; margin-top: 20px; padding: 15px; background: #000; color: #fff; border: none; font-weight: 700; text-transform: uppercase; cursor: pointer;">
                        <?php echo isset($_SESSION['user_id']) ? 'Add to Cart' : 'Login to Add to Cart'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const searchBtn = document.getElementById('searchBtn');
const searchInput = document.getElementById('searchInput');
const resultsPopup = document.getElementById('searchResultsPopup');

searchBtn.addEventListener('click', () => {
    searchInput.classList.toggle('active');
    if (searchInput.classList.contains('active')) searchInput.focus();
});

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

/**
 * Universal function to handle Quick View.
 * Accepts either an HTML Element (from grid click) 
 * or a raw Data Object (from search result click).
 */
function handleQuickView(input) {
    // If input is an element (from dataset), otherwise it's direct data from search
    const data = input.dataset ? input.dataset : input;
    
    // Hide search popup if it was open
    resultsPopup.style.display = 'none';

    // Populate Modal
    document.getElementById('modalName').innerText = data.name;
    document.getElementById('modalPrice').innerText = '₱' + parseFloat(data.price).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('modalDesc').innerText = data.desc;
    document.getElementById('modalImg').src = 'assets/images/' + data.image;
    document.getElementById('modalId').value = data.id;

    // Reset and Update Sizes/Stock
    ['s', 'm', 'l', 'xl'].forEach(size => {
        const stock = parseInt(data[size]);
        const inputRadio = document.getElementById(size);
        const label = document.getElementById('label_' + size);
        const stockLabel = document.getElementById('stock_' + size);

        stockLabel.innerText = stock + " LEFT";

        if (stock <= 0) {
            inputRadio.disabled = true;
            label.style.opacity = "0.3";
            label.style.cursor = "not-allowed";
            label.style.textDecoration = "line-through";
            stockLabel.style.color = "#ff0000";
        } else {
            inputRadio.disabled = false;
            label.style.opacity = "1";
            label.style.cursor = "pointer";
            label.style.textDecoration = "none";
            stockLabel.style.color = "#888";
            inputRadio.onclick = function() {
                document.getElementById('modalQtyInput').max = stock;
            };
        }
    });

    document.getElementById('quickViewModal').style.display = "block";
}

function closeModal() {
    document.getElementById('quickViewModal').style.display = "none";
}

// Close search popup or modal on background click
window.onclick = function(event) {
    if (event.target == document.getElementById('quickViewModal')) {
        closeModal();
    }
    if (!event.target.matches('.search-input') && !event.target.matches('.fa-search')) {
        resultsPopup.style.display = 'none';
    }
}
</script>
</body>
</html>