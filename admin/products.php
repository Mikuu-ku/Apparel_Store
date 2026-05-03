<?php
session_start();
include "../config/database.php";

/** 
 * ADMIN AUTHENTICATION 
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// --- PHP LOGIC: DELETE PRODUCT ---
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $res = mysqli_query($conn, "SELECT image FROM products WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    if (!empty($row['image'])) {
        $path = '../assets/uploads/' . $row['image'];
        if (file_exists($path)) unlink($path); 
    }
    $query = "DELETE FROM products WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Product Deleted Permanently";
        header("Location: products.php");
        exit();
    }
}

// --- PHP LOGIC: ADD PRODUCT ---
if (isset($_POST['add'])) {
    $name = !empty($_POST['new_name']) ? $_POST['new_name'] : $_POST['name'];
    $name = mysqli_real_escape_string($conn, $name);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = $_POST['price'];
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    $image_filename = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $img_name = $_FILES['product_image']['name'];
        $new_img_name = uniqid("IMG-", true) . '.' . strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        // Note: Image is saved in assets/uploads/
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], '../assets/uploads/' . $new_img_name)) {
            $image_filename = $new_img_name;
        }
    }

    $query = "INSERT INTO products (name, description, category, price, color, stock_s, stock_m, stock_l, stock_xl, image) 
              VALUES ('$name', '$desc', '$category', '$price', '$color', '$stock_s', '$stock_m', '$stock_l', '$stock_xl', '$image_filename')";
    
    if(mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Product Registered Successfully";
        header("Location: products.php");
        exit();
    }
}

// --- PHP LOGIC: UPDATE PRODUCT ---
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $stock_s = (int)$_POST['stock_s'];
    $stock_m = (int)$_POST['stock_m'];
    $stock_l = (int)$_POST['stock_l'];
    $stock_xl = (int)$_POST['stock_xl'];

    if (isset($_FILES['update_image']) && $_FILES['update_image']['error'] === 0) {
        $img_name = $_FILES['update_image']['name'];
        $new_img_name = uniqid("IMG-", true) . '.' . strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        if (move_uploaded_file($_FILES['update_image']['tmp_name'], '../assets/uploads/' . $new_img_name)) {
            $old_res = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
            $old_row = mysqli_fetch_assoc($old_res);
            if(!empty($old_row['image'])) unlink('../assets/uploads/'.$old_row['image']);
            $query = "UPDATE products SET name='$name', price='$price', stock_s='$stock_s', stock_m='$stock_m', stock_l='$stock_l', stock_xl='$stock_xl', image='$new_img_name' WHERE id=$id";
        }
    } else {
        $query = "UPDATE products SET name='$name', price='$price', stock_s='$stock_s', stock_m='$stock_m', stock_l='$stock_l', stock_xl='$stock_xl' WHERE id=$id";
    }
    
    if(mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Product Updated Successfully";
        header("Location: products.php");
        exit();
    }
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$existing_names = mysqli_query($conn, "SELECT DISTINCT name FROM products ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Apparel Admin</title>
    <link rel="icon" type="image/jpeg" href="../assets/images/new_logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        
        body { 
            margin: 0; display: flex; background-color: #fcfcfc; 
            font-family: 'Inter', sans-serif; color: #111; overflow-x: hidden;
        }

        .sidebar { 
            width: 250px; height: 100vh; background: #000; color: #fff; 
            padding: 30px 25px; position: fixed; left: 0; top: 0; 
            box-sizing: border-box; display: flex; flex-direction: column; z-index: 1000; 
        }

        .sidebar h2 { 
            font-size: 14px; letter-spacing: 4px; margin-bottom: 50px; 
            text-transform: uppercase; border-bottom: 1px solid #222; padding-bottom: 20px; 
        }

        .sidebar a { 
            display: block; color: #666; text-decoration: none; padding: 15px 0; 
            font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; transition: 0.3s; 
        }

        .sidebar a:hover, .active-link { color: #fff !important; }

        .admin-container { 
            margin-left: 250px; padding: 40px 50px; 
            width: calc(100% - 250px); box-sizing: border-box; min-height: 100vh;
        }

        .form-card, .inventory-card { 
            background: #fff; padding: 35px; border: 1px solid #f0f0f0; 
            margin-bottom: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }

        h3 { margin: 0; text-transform: uppercase; font-size: 13px; letter-spacing: 2px; font-weight: 700; }

        .compact-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 10px; }

        .input-group { margin-bottom: 20px; }

        label { 
            font-size: 10px; text-transform: uppercase; font-weight: 700; 
            color: #aaa; letter-spacing: 1.2px; display: block; margin-bottom: 8px; 
        }

        input, textarea, select { 
            width: 100%; padding: 14px; border: 1px solid #eee; 
            font-size: 13px; background: #fafafa; outline: none; 
            box-sizing: border-box; transition: 0.3s;
        }

        input:focus, select:focus, textarea:focus { border-color: #000; background: #fff; }

        /* VALIDATION ERROR STYLE */
        .error-border { border: 1.5px solid #ff4444 !important; background: #fff5f5 !important; }

        .stock-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }

        .btn-black { 
            padding: 15px 25px; background: #000; color: #fff; 
            border: 1px solid #000; cursor: pointer; text-transform: uppercase; 
            font-weight: 700; font-size: 11px; letter-spacing: 2px; transition: 0.3s; 
        }

        .btn-black:hover { background: #fff; color: #000; }

        .btn-toggle { 
            font-size: 10px; color: #007bff; cursor: pointer; 
            text-decoration: underline; font-weight: 700; text-transform: uppercase;
        }

        /* CUSTOM FILE INPUT DESIGN */
        .file-wrapper { position: relative; width: 100%; overflow: hidden; }
        .file-wrapper input[type="file"] { opacity: 0; position: absolute; inset: 0; cursor: pointer; z-index: 2; }
        .file-design { 
            display: block; padding: 12px; background: #fafafa; border: 1px solid #eee; 
            font-size: 11px; text-align: center; color: #888; font-weight: 700; 
            text-transform: uppercase; transition: 0.3s;
        }
        .file-wrapper:hover .file-design { border-color: #000; color: #000; }

        .modal { display: none; position: fixed; z-index: 2000; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); }
        .modal-content { background: #fff; margin: 5vh auto; padding: 50px; width: 650px; position: relative; box-sizing: border-box; }
        .close-btn { position: absolute; right: 25px; top: 20px; font-size: 24px; cursor: pointer; color: #ccc; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 20px; background: #fafafa; border-bottom: 1px solid #eee; text-transform: uppercase; font-size: 10px; color: #888; letter-spacing: 1px; }
        td { padding: 20px; border-bottom: 1px solid #f9f9f9; font-size: 13px; vertical-align: middle; }
        .product-thumb { width: 55px; height: 55px; object-fit: cover; border: 1px solid #eee; margin-right: 15px; }
        .status-badge { font-size: 9px; padding: 4px 8px; background: #eee; text-transform: uppercase; font-weight: 700; }
    </style>
</head>
<body>

    <?php if(isset($_SESSION['success_message'])): ?>
        <script>
            Swal.fire({ icon: 'success', title: 'SUCCESS', text: '<?php echo $_SESSION['success_message']; ?>', confirmButtonColor: '#000', timer: 3000 });
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="sidebar">
        <h2>APPAREL</h2>
        <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
        <a href="products.php" class="active-link"><i class="fas fa-tshirt"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-users"></i> User Management</a>
        <a href="../index.php"><i class="fas fa-eye"></i> View Site</a> <!-- Removed target="_blank" -->
        <a href="logout.php" style="margin-top:auto; color:#ff4444;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="admin-container">
        
        <div class="form-card">
            <div class="header-flex">
                <h3>Register Apparel</h3>
                <span id="toggleBtn" class="btn-toggle" onclick="toggleProductInput()">+ REGISTER NEW PRODUCT NAME</span>
            </div>
            
            <form method="POST" id="addForm" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="compact-row">
                    <div class="input-group" id="dropdownGroup">
                        <label>Existing Product Name</label>
                        <select name="name" id="add_name" class="val-field">
                            <option value="">-- Select Existing Product --</option>
                            <?php mysqli_data_seek($existing_names, 0); while($n = mysqli_fetch_assoc($existing_names)): ?>
                                <option value="<?php echo htmlspecialchars($n['name']); ?>"><?php echo htmlspecialchars($n['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="input-group" id="inputGroup" style="display:none;">
                        <label>New Product Name</label>
                        <input type="text" name="new_name" id="add_new_name" class="val-field" placeholder="e.g. Oversized Street Hoodie">
                    </div>

                    <div class="input-group">
                        <label>Category</label>
                        <select name="category" id="add_category" class="val-field">
                            <option value="">Select Category</option>
                            <option value="TSHIRTS">TSHIRTS</option>
                            <option value="Bottoms">Bottoms</option>
                            <option value="Hoodies">Hoodies</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Price (PHP)</label>
                        <input name="price" id="add_price" class="val-field" type="number" step="0.01" placeholder="0.00">
                    </div>
                </div>

                <div class="compact-row">
                    <div class="input-group">
                        <label>Color Variant</label>
                        <input name="color" id="add_color" placeholder="e.g. Acid Wash Grey">
                    </div>
                    <div class="input-group" style="grid-column: span 2;">
                        <label>Stock Levels (S | M | L | XL)</label>
                        <div class="stock-row">
                            <input name="stock_s" type="number" value="0">
                            <input name="stock_m" type="number" value="0">
                            <input name="stock_l" type="number" value="0">
                            <input name="stock_xl" type="number" value="0">
                        </div>
                    </div>
                </div>

                <div class="compact-row" style="grid-template-columns: 1fr 2fr;">
                    <div class="input-group">
                        <label>Product Image</label>
                        <div class="file-wrapper">
                            <div class="file-design" id="fileNameDisplay">CHOOSE PRODUCT IMAGE</div>
                            <input type="file" name="product_image" id="add_image" onchange="displayFileName(this)">
                        </div>
                    </div>
                    <div class="input-group">
                        <label>Product Description</label>
                        <textarea name="desc" id="add_desc" rows="2" placeholder="Fabric details..."></textarea>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 10px;">
                    <button type="submit" name="add" class="btn-black">Register Product</button>
                </div>
            </form>
        </div>

        <div class="inventory-card">
            <h3>Inventory Overview</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Base Price</th>
                        <th>Available Stocks</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($products, 0); while($row = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td style="display:flex; align-items:center;">
                            <img src="../assets/uploads/<?php echo $row['image']; ?>" class="product-thumb" onerror="this.src='../assets/images/no-image.jpg';">
                            <div>
                                <b><?php echo htmlspecialchars($row['name']); ?></b><br>
                                <small style="color: #888;"><?php echo htmlspecialchars($row['color']); ?></small>
                            </div>
                        </td>
                        <td><span class="status-badge"><?php echo $row['category']; ?></span></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td><small>S:<?php echo $row['stock_s']; ?> | M:<?php echo $row['stock_m']; ?> | L:<?php echo $row['stock_l']; ?> | XL:<?php echo $row['stock_xl']; ?></small></td>
                        <td style="text-align: right;">
                            <button class="btn-black" style="padding: 8px 15px; font-size: 9px;" onclick='openEditModal(<?php echo json_encode($row); ?>)'>Edit</button>
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
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit-id">
                <div class="input-group"><label>Name</label><input type="text" name="name" id="edit-name"></div>
                <div class="input-group"><label>Price</label><input type="number" name="price" id="edit-price" step="0.01"></div>
                <div class="stock-row" style="margin-bottom: 20px;">
                    <div><label>S</label><input type="number" name="stock_s" id="edit-s"></div>
                    <div><label>M</label><input type="number" name="stock_m" id="edit-m"></div>
                    <div><label>L</label><input type="number" name="stock_l" id="edit-l"></div>
                    <div><label>XL</label><input type="number" name="stock_xl" id="edit-xl"></div>
                </div>
                <div class="input-group">
                    <label>Change Image</label>
                    <div class="file-wrapper"><div class="file-design">UPLOAD NEW</div><input type="file" name="update_image"></div>
                </div>
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" name="update" class="btn-black" style="flex: 2;">Save All Changes</button>
                    <button type="button" class="btn-black" style="flex: 1; background: #ff4444; border-color: #ff4444;" onclick="confirmDelete()">Delete</button>
                </div>
            </form>
            <form id="deleteForm" method="POST" style="display:none;"><input type="hidden" name="delete_id" id="delete-id"></form>
        </div>
    </div>

    <script>
        let isNewProduct = false;

        function toggleProductInput() {
            const dropdown = document.getElementById('dropdownGroup');
            const input = document.getElementById('inputGroup');
            const btn = document.getElementById('toggleBtn');
            // Reset borders when toggling
            document.getElementById('add_name').classList.remove('error-border');
            document.getElementById('add_new_name').classList.remove('error-border');
            
            if (!isNewProduct) {
                dropdown.style.display = 'none'; input.style.display = 'block';
                btn.innerText = "← USE EXISTING PRODUCT NAME"; isNewProduct = true;
            } else {
                dropdown.style.display = 'block'; input.style.display = 'none';
                btn.innerText = "+ REGISTER NEW PRODUCT NAME"; isNewProduct = false;
            }
        }

        /**
         * AUTO-REMOVE RED BORDERS
         */
        document.querySelectorAll('.val-field').forEach(field => {
            field.addEventListener('input', function() { this.classList.remove('error-border'); });
        });

        function displayFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files[0]) {
                display.innerText = input.files[0].name;
                display.classList.remove('error-border');
            } else {
                display.innerText = "CHOOSE PRODUCT IMAGE";
            }
        }

        function validateForm() {
            let isValid = true;
            const nameField = isNewProduct ? document.getElementById('add_new_name') : document.getElementById('add_name');
            const price = document.getElementById('add_price');
            const category = document.getElementById('add_category');
            const fileDisplay = document.getElementById('fileNameDisplay');
            const fileInput = document.getElementById('add_image');

            // Reset borders
            [nameField, price, category, fileDisplay].forEach(el => el.classList.remove('error-border'));

            if (nameField.value === "") { nameField.classList.add('error-border'); isValid = false; }
            if (price.value === "" || price.value <= 0) { price.classList.add('error-border'); isValid = false; }
            if (category.value === "") { category.classList.add('error-border'); isValid = false; }
            if (fileInput.files.length === 0) { fileDisplay.classList.add('error-border'); isValid = false; }
            
            if (!isValid) {
                Swal.fire({ icon: 'error', title: 'MISSING DATA', text: 'Please complete all highlighted fields.', confirmButtonColor: '#000' });
            }
            return isValid;
        }

        function openEditModal(p) {
            document.getElementById('edit-id').value = p.id;
            document.getElementById('edit-name').value = p.name;
            document.getElementById('edit-price').value = p.price;
            document.getElementById('edit-s').value = p.stock_s;
            document.getElementById('edit-m').value = p.stock_m;
            document.getElementById('edit-l').value = p.stock_l;
            document.getElementById('edit-xl').value = p.stock_xl;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeModal() { document.getElementById('editModal').style.display = 'none'; }

        function confirmDelete() {
            const id = document.getElementById('edit-id').value;
            closeModal(); 
            Swal.fire({
                title: 'DELETE PRODUCT?', text: "This action is permanent.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ff4444', confirmButtonText: 'YES, DELETE'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-id').value = id;
                    document.getElementById('deleteForm').submit();
                }
            });
        }
    </script>
</body>
</html>