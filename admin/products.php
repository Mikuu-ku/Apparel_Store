<?php
session_start();
include "../config/database.php";

// Admin Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// --- HANDLE ADD PRODUCT ---
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $price = $_POST['price'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    $query = "INSERT INTO products (name, description, price, size, color, stock_s, stock_m, stock_l, stock_xl) 
              VALUES ('$name', '$desc', '$price', '$size', '$color', '$stock_s', '$stock_m', '$stock_l', '$stock_xl')";
    
    if(mysqli_query($conn, $query)) {
        header("Location: products.php?msg=Product Added Successfully");
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
        header("Location: products.php?msg=Product Updated Successfully");
        exit();
    }
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory | Apparel Admin</title>
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body { margin: 0; display: flex; background-color: #fcfcfc; font-family: 'Inter', sans-serif; color: #111; }
        
        /* Sidebar */
        .sidebar { width: 250px; height: 100vh; background: #000; color: #fff; padding: 30px 25px; position: fixed; left: 0; top: 0; box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar h2 { font-size: 14px; letter-spacing: 4px; margin-bottom: 50px; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #222; padding-bottom: 20px; }
        .sidebar a { display: block; color: #666; text-decoration: none; padding: 15px 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; transition: 0.3s; }
        .sidebar a i { margin-right: 12px; width: 20px; }
        .sidebar a:hover, .active-link { color: #fff !important; }
        .logout-link { margin-top: auto; color: #ff4444 !important; padding-bottom: 10px; }

        /* Main Workspace */
        .admin-container { 
            margin-left: 250px; 
            padding: 60px 80px; 
            width: calc(100% - 250px); 
            display: grid; 
            grid-template-columns: 380px 1fr; 
            gap: 50px; 
            box-sizing: border-box; 
        }
        
        .form-card, .inventory-card { background: #fff; padding: 40px; border: 1px solid #f0f0f0; }
        h3 { margin-top: 0; text-transform: uppercase; font-size: 13px; letter-spacing: 2px; margin-bottom: 30px; font-weight: 700; }
        
        /* Form Inputs */
        label { font-size: 9px; text-transform: uppercase; font-weight: 700; color: #aaa; letter-spacing: 1px; display: block; margin-bottom: 8px; }
        input, textarea { width: 100%; padding: 14px; margin-bottom: 20px; border: 1px solid #eee; box-sizing: border-box; font-family: inherit; font-size: 13px; outline: none; transition: 0.3s; }
        input:focus, textarea:focus { border-color: #000; }
        
        .stock-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 10px; }
        .stock-grid div { display: flex; flex-direction: column; }
        .stock-grid input { margin-bottom: 0; padding: 10px; }

        .btn-black { width: 100%; padding: 18px; background: #000; color: #fff; border: 1px solid #000; cursor: pointer; text-transform: uppercase; font-weight: 700; font-size: 11px; letter-spacing: 2px; transition: 0.3s; margin-top: 10px; }
        .btn-black:hover { background: #fff; color: #000; }

        /* Inventory Table */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 20px; background: #fafafa; border-bottom: 1px solid #eee; text-transform: uppercase; font-size: 10px; letter-spacing: 1.5px; color: #888; }
        td { padding: 20px; border-bottom: 1px solid #f9f9f9; font-size: 13px; vertical-align: middle; }

        /* Dropdown Actions */
        .dropdown { position: relative; display: inline-block; }
        .dropbtn { background: none; border: 1px solid #eee; padding: 10px 14px; cursor: pointer; font-size: 12px; transition: 0.2s; color: #888; }
        .dropbtn:hover { border-color: #000; color: #000; }
        .dropdown-content { display: none; position: absolute; right: 0; background-color: #fff; min-width: 150px; box-shadow: 0px 10px 30px rgba(0,0,0,0.08); z-index: 10; border: 1px solid #f0f0f0; }
        .dropdown-content a { color: #111; padding: 15px 20px; text-decoration: none; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; transition: 0.2s; }
        .dropdown-content a:hover { background-color: #000; color: #fff; }
        .dropdown:hover .dropdown-content { display: block; }
        .delete-opt { color: #ff4444 !important; }
        .delete-opt:hover { background-color: #ff4444 !important; color: #fff !important; }

        /* Modal Overlay */
        .modal { display: none; position: fixed; z-index: 2000; inset: 0; background: rgba(255,255,255,0.9); backdrop-filter: blur(5px); }
        .modal-content { background: #fff; margin: 4vh auto; padding: 50px; width: 450px; border: 1px solid #000; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .close-btn { position: absolute; right: 25px; top: 20px; font-size: 24px; cursor: pointer; color: #aaa; transition: 0.3s; }
        .close-btn:hover { color: #000; }

        /* Success Message Alert */
        .alert { position: fixed; top: 20px; right: 20px; background: #000; color: #fff; padding: 15px 30px; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; z-index: 3000; display: <?php echo isset($_GET['msg']) ? 'block' : 'none'; ?>; }
    </style>
</head>
<body>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert" id="alert-box"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <script>setTimeout(() => { document.getElementById('alert-box').style.display = 'none'; }, 3000);</script>
    <?php endif; ?>

    <div class="sidebar">
        <h2>APPAREL</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php" class="active-link"><i class="fas fa-tshirt"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-user"></i> Users</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a>
        <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="admin-container">
        <div class="form-card">
            <h3>Register Apparel</h3>
            <form method="POST">
                <label>Product Name</label>
                <input name="name" placeholder="e.g. Oversized Heavy Tee" required>
                
                <label>Description</label>
                <textarea name="desc" placeholder="Details about the garment..." rows="3"></textarea>
                
                <label>Unit Price</label>
                <input name="price" type="number" step="0.01" placeholder="₱ 0.00" required>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label>Sizes</label>
                        <input name="size" placeholder="S, M, L, XL">
                    </div>
                    <div>
                        <label>Color</label>
                        <input name="color" placeholder="Pitch Black">
                    </div>
                </div>
                
                <label>Stock Distribution</label>
                <div class="stock-grid">
                    <div><input name="stock_s" type="number" placeholder="S" value="0"></div>
                    <div><input name="stock_m" type="number" placeholder="M" value="0"></div>
                    <div><input name="stock_l" type="number" placeholder="L" value="0"></div>
                    <div><input name="stock_xl" type="number" placeholder="XL" value="0"></div>
                </div>
                <button type="submit" name="add" class="btn-black">Add to Inventory</button>
            </form>
        </div>

        <div class="inventory-card">
            <h3>Inventory Overview</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item Details</th>
                        <th>Price</th>
                        <th>Stock Levels</th>
                        <th style="text-align: right;">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td>
                            <span style="display: block; font-weight: 700; font-size: 14px; text-transform: uppercase;"><?php echo htmlspecialchars($row['name']); ?></span>
                            <span style="font-size: 10px; color: #aaa; text-transform: uppercase; letter-spacing: 1px;"><?php echo htmlspecialchars($row['color']); ?></span>
                        </td>
                        <td style="font-weight: 700;">₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <div style="font-family: monospace; font-size: 11px; color: #666;">
                                S:<?php echo $row['stock_s']; ?> | M:<?php echo $row['stock_m']; ?><br>
                                L:<?php echo $row['stock_l']; ?> | XL:<?php echo $row['stock_xl']; ?>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <div class="dropdown">
                                <button class="dropbtn"><i class="fas fa-ellipsis-h"></i></button>
                                <div class="dropdown-content">
                                    <a href="javascript:void(0)" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Edit Product</a>
                                    <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="delete-opt" onclick="return confirm('Remove this product from active inventory?')">Delete</a>
                                </div>
                            </div>
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
            <h3 style="margin-bottom: 30px;">Update Details</h3>
            <form method="POST">
                <input type="hidden" name="id" id="edit-id">
                <label>Product Name</label>
                <input name="name" id="edit-name" required>
                
                <label>Description</label>
                <textarea name="desc" id="edit-desc" rows="3"></textarea>
                
                <label>Price</label>
                <input name="price" id="edit-price" type="number" step="0.01" required>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div><label>Sizes</label><input name="size" id="edit-size"></div>
                    <div><label>Color</label><input name="color" id="edit-color"></div>
                </div>
                
                <label>Stock Correction</label>
                <div class="stock-grid">
                    <div><input name="stock_s" id="edit-stock-s" type="number"></div>
                    <div><input name="stock_m" id="edit-stock-m" type="number"></div>
                    <div><input name="stock_l" id="edit-stock-l" type="number"></div>
                    <div><input name="stock_xl" id="edit-stock-xl" type="number"></div>
                </div>
                <button type="submit" name="update" class="btn-black">Commit Changes</button>
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
        window.onclick = function(e) { if (e.target.className == 'modal') closeModal(); }
    </script>
</body>
</html>