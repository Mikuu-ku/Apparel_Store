<?php
include "config/database.php";

if (!isset($_GET['id'])) {
    echo "Product not found.";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$product = mysqli_fetch_assoc($query);

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<div class="modal-product">

    <div class="modal-image">
        <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    </div>

    <div class="modal-info">
        <h2><?php echo $product['name']; ?></h2>

        <p class="modal-price">
            ₱<?php echo number_format($product['price'], 2); ?>
        </p>

        <p class="modal-description">
            <?php echo $product['description']; ?>
        </p>

        <p class="modal-stock">
            Stock: <?php echo $product['stock']; ?>
        </p>

        <?php if ($product['stock'] > 0) { ?>
            <form method="post" action="cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                <button type="submit" class="btn-primary">
                    Add to Cart
                </button>

                <a href="checkout.php?buy=<?php echo $product['id']; ?>" class="btn-secondary">
                    Buy Now
                </a>
            </form>
        <?php } else { ?>
            <p class="sold-out-text">This product is currently sold out.</p>
        <?php } ?>

    </div>

</div>
