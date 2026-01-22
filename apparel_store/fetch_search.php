<?php
include "config/database.php";

$query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

if ($query !== '') {
    $sql = "SELECT * FROM products 
            WHERE name LIKE '%$query%' 
            AND (stock_s + stock_m + stock_l + stock_xl) > 0 
            LIMIT 5";
            
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="search-item" onclick="location.href=\'index.php?id=' . $row['id'] . '\'">';
            echo '<img src="assets/images/' . $row['image'] . '" width="30">';
            echo '<span>' . htmlspecialchars($row['name']) . '</span>';
            echo '</div>';
        }
    } else {
        echo '<div class="search-item">No products found</div>';
    }
}
?>