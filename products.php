<?php
include "config/database.php";
$result = mysqli_query($conn, "SELECT * FROM products");
?>

<h2>Products</h2>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
    <div>
        <img src="assets/images/<?php echo $row['image']; ?>" width="150">
        <h3><?php echo $row['name']; ?></h3>
        <p>₱<?php echo $row['price']; ?></p>
        <a href="product.php?id=<?php echo $row['id']; ?>">View</a>
    </div>
<?php } ?>
