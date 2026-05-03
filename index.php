<?php
session_start();
include "config/database.php";

$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$v = time(); 

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
    <title>Apparel's Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* Admin Bar Styles */
        .admin-top-bar {
            background: #000;
            color: #fff;
            padding: 10px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Roboto', sans-serif;
            font-size: 11px;
            letter-spacing: 1px;
            position: sticky;
            top: 0;
            z-index: 10000;
            border-bottom: 1px solid #333;
        }
        .admin-top-bar a { color: #fff; text-decoration: none; margin-left: 20px; text-transform: uppercase; font-weight: 700; }
        .admin-tag { background: #fff; color: #000; padding: 2px 8px; font-weight: 900; margin-right: 10px; }

        /* Carousel Layout - FIXED ZOOM/SCALING */
        .hero-carousel {
    position: relative;
    width: 100%;
    max-width: 1200px;
    margin: 20px auto;
    height: 500px;
    overflow: hidden;
    background: #000; /* Black base */
}
        .carousel-container {
            display: flex;
            height: 100%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .carousel-slide {
    min-width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row-reverse; /* Put image on left, text on right */
    align-items: center;
    background: #000;
}
        .carousel-slide img {
    flex: 1.2; /* Makes the image area slightly wider than the text area */
    width: 50%;
    height: 100%;
    object-fit: cover; /* Back to cover since it's a side-panel now */
    background: #1a1a1a;
}
        .carousel-caption {
    position: relative;
    flex: 1;
    padding: 60px;
    background: #000; /* Pure black background for the text */
    color: #fff; /* White text for visibility */
    z-index: 5;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
        .carousel-caption h2 { 
    font-size: 42px; 
    font-weight: 700;
    letter-spacing: 2px;
    color: #fff;
    margin-bottom: 10px;
}
        .carousel-caption p {
    font-size: 16px;
    color: #ccc; /* Slightly grey for the price to keep hierarchy */
    margin-bottom: 30px;
}
        
        .btn-shop-now {
    display: inline-block;
    background: #fff;
    color: #000;
    width: fit-content;
    padding: 15px 35px;
    text-decoration: none;
    font-weight: 700;
    text-transform: uppercase;
    transition: 0.3s;
    border: 1px solid #fff;
}
        .btn-shop-now:hover {
    background: transparent;
    color: #fff;
}
        .carousel-prev, .carousel-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.3);
            color: #fff;
            border: none;
            padding: 20px 15px;
            cursor: pointer;
            z-index: 10;
        }
        .carousel-next { right: 0; }
        .carousel-prev { left: 0; }
        .carousel-dots { position: absolute; bottom: 20px; width: 100%; text-align: center; z-index: 10; }
        .dot { height: 8px; width: 8px; margin: 0 5px; background: rgba(255,255,255,0.4); border-radius: 50%; display: inline-block; cursor: pointer; transition: 0.3s; }
        .dot.active { background: #fff; width: 25px; border-radius: 10px; }

        /* Sold Out Styling */
        .oos-card { opacity: 0.6; cursor: not-allowed !important; }
        .badge { background: #ff4444; color: #fff; padding: 5px 10px; position: absolute; top: 10px; left: 10px; z-index: 5; font-weight: bold; font-size: 10px; }
    </style>
</head>
<body>

<?php if ($is_admin): ?>
<div class="admin-top-bar">
    <div>
        <span class="admin-tag">ADMIN MODE</span>
        <span>Logged in as Administrator</span>
    </div>
    <div>
        <a href="admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="admin/products.php"><i class="fas fa-tshirt"></i> Inventory</a>
        <a href="admin/orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="logout.php" style="color: #ff4444;">Logout</a>
    </div>
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
            <a href="index.php?category=Tshirts" class="cat-link <?php echo $category == 'Tshirts' ? 'active' : ''; ?>">TSHIRTS</a>
            <a href="index.php?category=Bottoms" class="cat-link <?php echo $category == 'Bottoms' ? 'active' : ''; ?>">BOTTOMS</a>
            <a href="index.php?category=Essentials" class="cat-link <?php echo $category == 'Essentials' ? 'active' : ''; ?>">HOODIES</a>
        </nav>
    </div>
</div>

<div class="hero-carousel">
    <div class="carousel-container" id="carouselContainer">
        <?php
        $carousel_query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 8");
        $slide_count = 0;
        while($c_row = mysqli_fetch_assoc($carousel_query)): 
        ?>
            <div class="carousel-slide">
                <img src="assets/uploads/<?php echo $c_row['image']; ?>" alt="Featured product">
                <div class="carousel-caption">
                    <h2><?php echo strtoupper($c_row['name']); ?></h2>
                    <p>New Arrival - ₱<?php echo number_format($c_row['price'], 2); ?></p>
                    <a href="index.php" class="btn-shop-now">View Collection</a>
                </div>
            </div>
        <?php 
            $slide_count++;
        endwhile; 
        ?>
    </div>
    
    <button class="carousel-prev" onclick="moveSlide(-1)">&#10094;</button>
    <button class="carousel-next" onclick="moveSlide(1)">&#10095;</button>

    <div class="carousel-dots">
        <?php for($i=0; $i < $slide_count; $i++): ?>
            <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="currentSlide(<?php echo $i; ?>)"></span>
        <?php endfor; ?>
    </div>
</div>

<main class="container">
    <div class="product-grid">
    <?php while($row = mysqli_fetch_assoc($products)) { 
        $stock_s = (int)($row['stock_s'] ?? 0);
        $stock_m = (int)($row['stock_m'] ?? 0);
        $stock_l = (int)($row['stock_l'] ?? 0);
        $stock_xl = (int)($row['stock_xl'] ?? 0);
        $total_stock = $stock_s + $stock_m + $stock_l + $stock_xl;
        $is_out_of_stock = ($total_stock <= 0);
    ?>
        <div class="product-card <?php echo $is_out_of_stock ? 'oos-card' : ''; ?>" 
             data-id="<?php echo $row['id']; ?>"
             data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
             data-price="<?php echo $row['price']; ?>"
             data-image="<?php echo $row['image']; ?>"
             data-desc="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
             data-s="<?php echo $stock_s; ?>"
             data-m="<?php echo $stock_m; ?>"
             data-l="<?php echo $stock_l; ?>"
             data-xl="<?php echo $stock_xl; ?>"
             onclick="<?php echo !$is_out_of_stock ? 'handleQuickView(this)' : ''; ?>">
            
            <?php if($is_out_of_stock): ?>
                <span class="badge">SOLD OUT</span>
            <?php endif; ?>

            <div class="product-link">
                <img src="assets/uploads/<?php echo $row['image']; ?>" alt="Product">
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
                <li><a href="customer_service.php?page=contact">Contact Us</a></li>
                <li><a href="customer_service.php?page=size-guide">Size Guide</a></li>
                <li><a href="customer_service.php?page=faqs">FAQs</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4 class="footer-title">Follow Us</h4>
            <div class="social-links">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<!-- QUICK VIEW MODAL -->
<div id="quickViewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-body">
            <div class="modal-image" id="zoomContainer">
                <img id="modalImg" src="" alt="Product Image">
            </div>
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
                            <div class="size-item"><input type="radio" name="size" value="S" id="s" required><label for="s" id="label_s">S</label><span class="stock-label" id="stock_s"></span></div>
                            <div class="size-item"><input type="radio" name="size" value="M" id="m"><label for="m" id="label_m">M</label><span class="stock-label" id="stock_m"></span></div>
                            <div class="size-item"><input type="radio" name="size" value="L" id="l"><label for="l" id="label_l">L</label><span class="stock-label" id="stock_l"></span></div>
                            <div class="size-item"><input type="radio" name="size" value="XL" id="xl"><label for="xl" id="label_xl">XL</label><span class="stock-label" id="stock_xl"></span></div>
                        </div>
                    </div>
                    <div class="qty-selection">
                        <label>QUANTITY</label>
                        <div class="qty-input-wrapper"><input type="number" name="quantity" id="modalQtyInput" value="1" min="1"></div>
                    </div>
                    <button type="submit" class="btn-add-cart"><?php echo isset($_SESSION['user_id']) ? 'Add to Cart' : 'Login to Add to Cart'; ?></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let slideIndex = 0;
const slides = document.querySelectorAll('.carousel-slide');
const dots = document.querySelectorAll('.dot');

function showSlides() {
    if (slideIndex >= slides.length) slideIndex = 0;
    if (slideIndex < 0) slideIndex = slides.length - 1;
    const container = document.getElementById('carouselContainer');
    if(container) container.style.transform = `translateX(-${slideIndex * 100}%)`;
    dots.forEach(dot => dot.classList.remove('active'));
    if(dots[slideIndex]) dots[slideIndex].classList.add('active');
}

function moveSlide(n) { slideIndex += n; showSlides(); }
function currentSlide(n) { slideIndex = n; showSlides(); }
setInterval(() => { slideIndex++; showSlides(); }, 5000);

const searchInput = document.getElementById('searchInput');
const resultsPopup = document.getElementById('searchResultsPopup');
const quickViewModal = document.getElementById('quickViewModal');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length > 1) {
            fetch(`fetch_search.php?query=${encodeURIComponent(query)}`)
                .then(res => res.text())
                .then(data => {
                    resultsPopup.innerHTML = data;
                    resultsPopup.style.display = 'block';
                });
        } else { resultsPopup.style.display = 'none'; }
    });
}

function handleQuickView(input) {
    const data = input.dataset;
    document.getElementById('modalName').innerText = data.name;
    document.getElementById('modalPrice').innerText = '₱' + parseFloat(data.price).toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('modalDesc').innerText = data.desc;
    document.getElementById('modalImg').src = 'assets/uploads/' + data.image;
    document.getElementById('modalId').value = data.id;
    
    ['s', 'm', 'l', 'xl'].forEach(size => {
        const stock = parseInt(data[size]);
        const rb = document.getElementById(size);
        const lbl = document.getElementById('label_' + size);
        const slbl = document.getElementById('stock_' + size);
        slbl.innerText = stock + " LEFT";
        if (stock <= 0) {
            rb.disabled = true;
            lbl.style.opacity = "0.2";
            lbl.style.textDecoration = "line-through";
        } else {
            rb.disabled = false;
            lbl.style.opacity = "1";
            lbl.style.textDecoration = "none";
        }
    });
    quickViewModal.style.display = "block";
    document.body.style.overflow = "hidden";
}

function closeModal() {
    quickViewModal.style.display = "none";
    document.body.style.overflow = "auto";
}

window.onclick = (e) => { if (e.target == quickViewModal) closeModal(); }
</script>
</body>
</html>