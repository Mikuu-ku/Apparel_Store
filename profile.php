<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$v = "2.4"; 

// --- BACKEND LOGIC: Update Profile & Upload Image ---
if (isset($_POST['update_profile'])) {
    $new_fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $new_lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $new_pass  = $_POST['new_password'];

    // 1. Handle Profile Picture Upload
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "assets/images/profiles/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        // Use a clean filename to avoid caching issues
        $new_filename = "user_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            mysqli_query($conn, "UPDATE users SET profile_pic = '$new_filename' WHERE id = $user_id");
        }
    }

    // 2. Update Basic Information
    $update_query = "UPDATE users SET first_name = '$new_fname', last_name = '$new_lname', contact_no = '$new_phone' WHERE id = $user_id";
    mysqli_query($conn, $update_query);

    // 3. Update Password if provided
    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed_pass' WHERE id = $user_id");
    }

    $_SESSION['first_name'] = $new_fname;
    header("Location: profile.php?status=updated");
    exit();
}

// Fetch Current User Data
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

$first_name = $user['first_name'] ?? '';
$profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'default_avatar.jpg';

// Cart Count for Header
$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$cart_count = mysqli_fetch_assoc($count_res)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Apparel Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f5f5f5; font-family: 'Roboto', sans-serif; margin: 0; }
        
        /* Shopee-style Layout Container */
        .shopee-layout {
            max-width: 1200px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 27px;
            padding: 0 15px;
            min-height: 600px;
        }

        /* Sidebar Styling */
        .shopee-sidebar-header {
            display: flex;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #efefef;
            margin-bottom: 15px;
        }
        .shopee-avatar-small {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 1px solid #ddd;
        }
        .shopee-user-name { font-weight: 700; font-size: 13px; display: block; overflow: hidden; text-overflow: ellipsis; }
        .shopee-edit-label { font-size: 12px; color: #888; text-decoration: none; display: flex; align-items: center; gap: 5px; margin-top: 2px; }
        .shopee-nav-link { display: block; padding: 10px 0; color: #333; text-decoration: none; font-size: 14px; transition: color 0.2s; }
        .shopee-nav-link i { width: 25px; color: #ee4d2d; font-size: 16px; }
        .shopee-nav-link.active { color: #black; font-weight: 500; }
        .shopee-nav-link:hover { color: #ee4d2d; }

        /* Main Card Styling */
        .shopee-card {
            background: #fff;
            padding: 30px;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .shopee-card-header {
            border-bottom: 1px solid #efefef;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .shopee-card-header h2 { font-size: 18px; margin: 0; font-weight: 500; text-transform: capitalize; }
        .shopee-card-header p { font-size: 14px; color: #555; margin-top: 5px; }

        .shopee-flex-form { display: flex; flex-wrap: wrap; }
        .shopee-inputs { flex: 2; border-right: 1px solid #efefef; padding-right: 30px; min-width: 300px; }
        .shopee-upload { flex: 1; text-align: center; padding-left: 30px; min-width: 200px; }

        .shopee-row { display: flex; align-items: center; margin-bottom: 25px; }
        .shopee-row label { width: 30%; text-align: right; padding-right: 20px; font-size: 14px; color: #555; }
        .shopee-row .field-val { width: 70%; font-size: 14px; }
        .shopee-row input { width: 100%; padding: 10px; border: 1px solid #dbdbdb; border-radius: 2px; outline: none; font-size: 14px; }
        .shopee-row input:focus { border-color: #999; }

        .shopee-save-btn {
            background-color: #ee4d2d;
            color: #fff;
            border: none;
            padding: 12px 35px;
            border-radius: 2px;
            cursor: pointer;
            margin-left: 30%;
            font-size: 14px;
            transition: background 0.2s;
        }
        .shopee-save-btn:hover { background-color: #d73211; }

        /* Upload UI */
        .shopee-avatar-big { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; border: 1px solid #eee; background: #fafafa; }
        .btn-upload { background: #fff; border: 1px solid #dbdbdb; padding: 8px 15px; font-size: 14px; cursor: pointer; display: inline-block; transition: background 0.2s; }
        .btn-upload:hover { background: #f8f8f8; }
        input[type="file"] { display: none; }
        
        .success-msg { background: #fafff2; color: #2ecc71; padding: 12px; margin-bottom: 20px; font-size: 14px; border: 1px solid #e2f9e1; border-radius: 2px; }
    </style>
</head>
<body>

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php"><img src="assets/images/new_logo.jpg" alt="Logo" class="header-logo"></a>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <a href="javascript:void(0)" class="header-icon user-link">
                    <i class="fas fa-user-circle"></i> 
                    <span class="user-name-text"><?= strtoupper(htmlspecialchars($first_name)) ?></span>
                </a>
            </div>
            <a href="cart.php" class="header-icon">
                <i class="fas fa-shopping-bag"></i>
                <span class="cart-quantity"><?= $cart_count ?></span>
            </a>
        </div>
    </div>
</header>

<main class="shopee-layout">
    <!-- Left Sidebar -->
    <aside>
        <div class="shopee-sidebar-header">
            <!-- Dynamic profile picture in sidebar -->
            <img src="assets/images/profiles/<?= $profile_pic ?>" class="shopee-avatar-small" onerror="this.src='assets/images/profiles/default_avatar.jpg'">
            <div class="shopee-user-info">
                <span class="shopee-user-name"><?= htmlspecialchars($first_name) ?></span>
                <a href="profile.php" class="shopee-edit-label"><i class="fas fa-pen"></i> Edit Profile</a>
            </div>
        </div>
        <nav>
            <a href="profile.php" class="shopee-nav-link active"><i class="fas fa-user"></i> My Account</a>
            <a href="orders.php" class="shopee-nav-link"><i class="fas fa-list-alt"></i> My Purchase</a>
            <a href="logout.php" class="shopee-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Right Profile Card -->
    <section class="shopee-card">
        <div class="shopee-card-header">
            <h2>My Profile</h2>
            <p>Manage and protect your account information</p>
        </div>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="success-msg"><i class="fas fa-check-circle"></i> Profile updated successfully!</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="shopee-flex-form">
            <!-- Left Form Side -->
            <div class="shopee-inputs">
                <div class="shopee-row">
                    <label>Email</label>
                    <div class="field-val"><span><?php echo htmlspecialchars($user['email']); ?></span></div>
                </div>
                <div class="shopee-row">
                    <label>First Name</label>
                    <div class="field-val"><input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required></div>
                </div>
                <div class="shopee-row">
                    <label>Last Name</label>
                    <div class="field-val"><input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required></div>
                </div>
                <div class="shopee-row">
                    <label>Phone Number</label>
                    <div class="field-val"><input type="text" name="contact_no" value="<?= ($user['contact_no'] == "2147483647" || empty($user['contact_no'])) ? '' : htmlspecialchars($user['contact_no']) ?>" placeholder="09xxxxxxxxx"></div>
                </div>
                <div class="shopee-row">
                    <label>New Password</label>
                    <div class="field-val"><input type="password" name="new_password" placeholder="Leave blank to keep current"></div>
                </div>
                <button type="submit" name="update_profile" class="shopee-save-btn">Save</button>
            </div>

            <!-- Right Upload Side -->
            <div class="shopee-upload">
                <img src="assets/images/profiles/<?= $profile_pic ?>" id="preview" class="shopee-avatar-big" onerror="this.src='assets/images/profiles/default_avatar.jpg'">
                <br>
                <label class="btn-upload" for="file-input">
                    Select Image
                </label>
                <input type="file" id="file-input" name="profile_image" accept="image/*" onchange="previewImage(this)">
                <p style="font-size: 12px; color: #999; margin-top: 15px; line-height: 1.5;">
                    File size: maximum 1 MB<br>
                    File extension: .JPEG, .PNG
                </p>
            </div>
        </form>
    </section>
</main>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<script>
    // Live Image Preview Function
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>