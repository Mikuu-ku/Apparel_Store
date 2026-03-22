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
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    // Ensure your DB table has 'category' column
    $query = "INSERT INTO products (name, description, category, price, color, stock_s, stock_m, stock_l, stock_xl) 
              VALUES ('$name', '$desc', '$category', '$price', '$color', '$stock_s', '$stock_m', '$stock_l', '$stock_xl')";
    
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
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    $query = "UPDATE products SET 
              name='$name', description='$desc', category='$category', price='$price', color='$color', 
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
    <title>Products | Apparel Admin</title>
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
            padding: 40px 50px; 
            width: calc(100% - 250px); 
            display: flex;
            flex-direction: column;
            gap: 30px; 
            box-sizing: border-box; 
        }
        
        .form-card, .inventory-card { background: #fff; padding: 30px; border: 1px solid #f0f0f0; }
        h3 { margin-top: 0; text-transform: uppercase; font-size: 12px; letter-spacing: 2px; margin-bottom: 20px; font-weight: 700; color: #000; }
        
        /* Compact Grid Layout */
        .compact-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 5px; }
        .input-group { margin-bottom: 15px; }
        
        label { font-size: 9px; text-transform: uppercase; font-weight: 700; color: #aaa; letter-spacing: 1px; display: block; margin-bottom: 5px; }
        input, textarea, select { width: 100%; padding: 12px; border: 1px solid #eee; box-sizing: border-box; font-family: inherit; font-size: 12px; outline: none; transition: 0.3s; background: #fafafa; }
        input:focus { border-color: #000; background: #fff; }
        
        /* Validation Styles */
        .input-error { border: 1px solid #ff0000 !important; background: #fff5f5 !important; }
        .field-error { color: #ff0000; font-size: 8px; font-weight: 700; text-transform: uppercase; margin-top: 4px; display: none; letter-spacing: 0.5px; }

        .stock-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .stock-row div { position: relative; }

        .btn-black { padding: 15px 30px; background: #000; color: #fff; border: 1px solid #000; cursor: pointer; text-transform: uppercase; font-weight: 700; font-size: 11px; letter-spacing: 2px; transition: 0.3s; }
        .btn-black:hover { background: #fff; color: #000; }

        /* Inventory Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #fafafa; border-bottom: 1px solid #eee; text-transform: uppercase; font-size: 9px; letter-spacing: 1.5px; color: #888; }
        td { padding: 15px; border-bottom: 1px solid #f9f9f9; font-size: 12px; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 2000; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(3px); }
        .modal-content { background: #fff; margin: 5vh auto; padding: 40px; width: 600px; position: relative; }
        .close-btn { position: absolute; right: 20px; top: 15px; font-size: 20px; cursor: pointer; color: #ccc; }

        .alert { position: fixed; top: 20px; right: 20px; background: #000; color: #fff; padding: 15px 30px; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; z-index: 3000; }
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
            <h3>Register New Apparel</h3>
            <form method="POST" id="addForm" novalidate>
                <div class="compact-row">
                    <div class="input-group">
                        <label>Product Name</label>
                        <input name="name" id="add_name" placeholder="Name">
                        <span class="field-error" id="err_add_name">Required</span>
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <select name="category" id="add_category">
                            <option value="">Select Category</option>
                            <option value="Tops">Tops</option>
                            <option value="Bottoms">Bottoms</option>
                            <option value="Essentials">Hoodies</option>
                        </select>
                        <span class="field-error" id="err_add_category">Required</span>
                    </div>
                    <div class="input-group">
                        <label>Unit Price</label>
                        <input name="price" id="add_price" type="number" step="0.01" placeholder="0.00">
                        <span class="field-error" id="err_add_price">Required</span>
                    </div>
                </div>

                <div class="compact-row">
                    <div class="input-group">
                        <label>Color</label>
                        <input name="color" id="add_color" placeholder="e.g. Black">
                        <span class="field-error" id="err_add_color">Required</span>
                    </div>
                    <div class="input-group" style="grid-column: span 2;">
                        <label>Stock (S | M | L | XL)</label>
                        <div class="stock-row">
                            <input name="stock_s" id="add_s" type="number" value="0">
                            <input name="stock_m" id="add_m" type="number" value="0">
                            <input name="stock_l" id="add_l" type="number" value="0">
                            <input name="stock_xl" id="add_xl" type="number" value="0">
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label>Description</label>
                    <textarea name="desc" id="add_desc" rows="2" placeholder="Product details..."></textarea>
                    <span class="field-error" id="err_add_desc">Required</span>
                </div>

                <div style="text-align: right;">
                    <button type="submit" name="add" class="btn-black">Add to Inventory</button>
                </div>
            </form>
        </div>

        <div class="inventory-card">
            <h3>Inventory Overview</h3>
            <table>
                <thead>
                    <tr>
                        <th>Details</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stocks</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td>
                            <b style="text-transform: uppercase;"><?php echo $row['name']; ?></b><br>
                            <small style="color:#888"><?php echo $row['color']; ?></small>
                        </td>
                        <td><?php echo $row['category']; ?></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td style="font-family: monospace; font-size: 11px;">
                            S:<?php echo $row['stock_s']; ?> M:<?php echo $row['stock_m']; ?> L:<?php echo $row['stock_l']; ?> XL:<?php echo $row['stock_xl']; ?>
                        </td>
                        <td style="text-align: right;">
                            <button class="btn-black" style="padding: 5px 12px; font-size: 9px;" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Edit</button>
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
            <form method="POST" id="editForm" novalidate>
                <input type="hidden" name="id" id="edit-id">
                <div class="compact-row">
                    <div class="input-group">
                        <label>Product Name</label>
                        <input name="name" id="edit-name">
                        <span class="field-error" id="err_edit_name">Required</span>
                    </div>
                    <div class="input-group">
                        <label>Category</label>
                        <select name="category" id="edit-category">
                            <option value="Tops">Tops</option>
                            <option value="Bottoms">Bottoms</option>
                            <option value="Essentials">Hoodies</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Price</label>
                        <input name="price" id="edit-price" type="number" step="0.01">
                        <span class="field-error" id="err_edit_price">Required</span>
                    </div>
                </div>
                <div class="input-group">
                    <label>Stocks (S | M | L | XL)</label>
                    <div class="stock-row">
                        <input name="stock_s" id="edit-s" type="number">
                        <input name="stock_m" id="edit-m" type="number">
                        <input name="stock_l" id="edit-l" type="number">
                        <input name="stock_xl" id="edit-xl" type="number">
                    </div>
                </div>
                <button type="submit" name="update" class="btn-black" style="width:100%">Commit Changes</button>
            </form>
        </div>
    </div>

    <script>
        function validate(formId, prefix) {
            const form = document.getElementById(formId);
            let isValid = true;
            const fields = ['name', 'category', 'price', 'color', 'desc'];
            
            fields.forEach(f => {
                const el = document.getElementById(prefix + f);
                const err = document.getElementById('err_' + prefix + f);
                if (el && !el.value.trim()) {
                    el.classList.add('input-error');
                    if (err) err.style.display = 'block';
                    isValid = false;
                } else if (el) {
                    el.classList.remove('input-error');
                    if (err) err.style.display = 'none';
                }
            });
            return isValid;
        }

        document.getElementById('addForm').onsubmit = function(e) {
            if (!validate('addForm', 'add_')) e.preventDefault();
        };

        function openEditModal(p) {
            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-name').value = p.name;
            document.getElementById('edit-category').value = p.category;
            document.getElementById('edit-price').value = p.price;
            document.getElementById('edit-s').value = p.stock_s;
            document.getElementById('edit-m').value = p.stock_m;
            document.getElementById('edit-l').value = p.stock_l;
            document.getElementById('edit-xl').value = p.stock_xl;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() { document.getElementById('editModal').style.display = 'none'; }
        window.onclick = e => { if(e.target.className == 'modal') closeModal(); }
    </script>
</body>
</html>