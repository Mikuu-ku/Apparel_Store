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
            $name = htmlspecialchars($row['name'], ENT_QUOTES);
            $desc = htmlspecialchars($row['description'], ENT_QUOTES);
            
            $productData = json_encode([
                "id"    => $row['id'],
                "name"  => $name,
                "price" => $row['price'],
                "image" => $row['image'],
                "desc"  => $desc,
                "s"     => $row['stock_s'],
                "m"     => $row['stock_m'],
                "l"     => $row['stock_l'],
                "xl"    => $row['stock_xl']
            ]);

            echo '<div class="search-item" 
                       onclick=\'handleQuickView(' . $productData . ')\' 
                       style="display: flex; align-items: center; padding: 12px; cursor: pointer; border-bottom: 1px solid #f2f2f2; font-family: \'Roboto\', sans-serif;">';
            
            echo '<img src="assets/images/' . $row['image'] . '" style="width: 40px; height: 40px; object-fit: cover; margin-right: 15px; border: 1px solid #eee;">';
            
            echo '<div style="display: flex; flex-direction: column;">';
            echo '<span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #111;">' . $name . '</span>';
            echo '<span style="font-size: 10px; color: #888; margin-top: 2px;">₱' . number_format($row['price'], 2) . '</span>';
            echo '</div>';
            
            echo '</div>';
        }
    } else {
        echo '<div style="padding: 20px; text-align: center; font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 2px; font-family: \'Roboto\', sans-serif;">No products found</div>';
    }
}
?>