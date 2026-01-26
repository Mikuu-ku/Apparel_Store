<?php
include "../config/database.php";

// --- HANDLE ADD PRODUCT ---
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $price = $_POST['price'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    
    // Captured individual stocks for each size
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    $query = "INSERT INTO products (name, description, price, size, color, stock_s, stock_m, stock_l, stock_xl) 
              VALUES ('$name', '$desc', '$price', '$size', '$color', '$stock_s', '$stock_m', '$stock_l', '$stock_xl')";
    
    if(mysqli_query($conn, $query)) {
        header("Location: products.php?msg=added");
        exit();
    }
}

// --- HANDLE UPDATE PRODUCT ---
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $price = $_POST['price'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    $query = "UPDATE products SET 
              name='$name', description='$desc', price='$price', size='$size', color='$color', 
              stock_s='$stock_s', stock_m='$stock_m', stock_l='$stock_l', stock_xl='$stock_xl' 
              WHERE id=$id";
    
    if(mysqli_query($conn, $query)) {
        header("Location: products.php?msg=updated");
        exit();
    }
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Apparel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        body { margin: 0; display: flex; background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; color: #111; }
        .sidebar { width: 250px; height: 100vh; background: #111; color: #fff; padding: 25px; position: fixed; left: 0; top: 0; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 18px; letter-spacing: 2px; margin-bottom: 40px; text-transform: uppercase; border-bottom: 1px solid #333; padding-bottom: 15px; }
        .sidebar a { display: block; color: #888; text-decoration: none; padding: 15px 0; transition: 0.3s; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .sidebar a i { margin-right: 10px; width: 20px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .active-link { font-weight: bold; }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 20px; }

        .admin-container { margin-left: 250px; padding: 50px; width: calc(100% - 250px); display: grid; grid-template-columns: 350px 1fr; gap: 40px; box-sizing: border-box; }
        .form-card, .inventory-card { background: #fff; padding: 30px; border: 1px solid #eee; }
        h3 { margin-top: 0; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; margin-bottom: 25px; }
        
        input, textarea { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; box-sizing: border-box; font-family: inherit; }
        .stock-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .stock-grid input { margin-bottom: 0; }
        
        .btn-black { width: 100%; padding: 15px; background: #000; color: #fff; border: none; cursor: pointer; text-transform: uppercase; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 12px; border-bottom: 2px solid #000; text-transform: uppercase; font-size: 11px; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 13px; vertical-align: top; }

        .modal { display: none; position: fixed; z-index: 2000; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(5px); }
        .modal-content { background: #fff; margin: 2% auto; padding: 30px; width: 450px; position: relative; max-height: 90vh; overflow-y: auto; }
        .close-btn { position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; color: #888; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>APPAREL ADMIN</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php" class="active-link"><i class="fas fa-tshirt"></i> Manage Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-user"></i> View Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="admin-container">
        <div class="form-card">
            <h3>Add New Apparel</h3>
            <form method="POST">
                <input name="name" placeholder="Product Name" required>
                <textarea name="desc" placeholder="Description" rows="3"></textarea>
                <input name="price" type="number" step="0.01" placeholder="Price (₱)" required>
                <input name="size" placeholder="Sizes (e.g. S, M, L, XL)">
                <input name="color" placeholder="Color">
                
                <p style="font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px;">Stock by Size</p>
                <div class="stock-grid">
                    <input name="stock_s" type="number" placeholder="S" value="0">
                    <input name="stock_m" type="number" placeholder="M" value="0">
                    <input name="stock_l" type="number" placeholder="L" value="0">
                    <input name="stock_xl" type="number" placeholder="XL" value="0">
                </div>
                <button type="submit" name="add" class="btn-black">Add to Inventory</button>
            </form>
        </div>

        <div class="inventory-card">
            <h3>Current Inventory</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock (S, M, L, XL)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            S: <?php echo $row['stock_s']; ?> | M: <?php echo $row['stock_m']; ?><br>
                            L: <?php echo $row['stock_l']; ?> | XL: <?php echo $row['stock_xl']; ?>
                        </td>
                        <td>
                            <a href="javascript:void(0)" onclick='openEditModal(<?php echo json_encode($row); ?>)' style="color: #2196F3; text-decoration: none; font-weight: bold; font-size: 11px;">EDIT</a><br><br>
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete?')" style="color: #ff4444; text-decoration: none; font-weight: bold; font-size: 11px;">DELETE</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3>Update Product</h3>
            <form method="POST">
                <input type="hidden" name="id" id="edit-id">
                <input name="name" id="edit-name" required>
                <textarea name="desc" id="edit-desc" rows="3"></textarea>
                <input name="price" id="edit-price" type="number" step="0.01" required>
                <input name="size" id="edit-size">
                <input name="color" id="edit-color">
                
                <p style="font-size: 11px; font-weight: bold; text-transform: uppercase;">Update Stocks</p>
                <div class="stock-grid">
                    <input name="stock_s" id="edit-stock-s" type="number" placeholder="S">
                    <input name="stock_m" id="edit-stock-m" type="number" placeholder="M">
                    <input name="stock_l" id="edit-stock-l" type="number" placeholder="L">
                    <input name="stock_xl" id="edit-stock-xl" type="number" placeholder="XL">
                </div>
                <button type="submit" name="update" class="btn-black">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(product) {
            document.getElementById('edit-id').value = product.id;
            document.getElementById('edit-name').value = product.name;
            document.getElementById('edit-desc').value = product.description;
            document.getElementById('edit-price').value = product.price;
            document.getElementById('edit-size').value = product.size;
            document.getElementById('edit-color').value = product.color;
            document.getElementById('edit-stock-s').value = product.stock_s;
            document.getElementById('edit-stock-m').value = product.stock_m;
            document.getElementById('edit-stock-l').value = product.stock_l;
            document.getElementById('edit-stock-xl').value = product.stock_xl;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeModal() { document.getElementById('editModal').style.display = 'none'; }
    </script>
</body>
</html>