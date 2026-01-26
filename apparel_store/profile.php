<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$v = "1.9";

if (isset($_POST['update_profile'])) {
    $new_fname = mysqli_real_escape_string($conn, $_POST['first_name']);
    $new_lname = mysqli_real_escape_string($conn, $_POST['last_name']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $new_pass  = $_POST['new_password'];

    $update_query = "UPDATE users SET first_name = '$new_fname', last_name = '$new_lname', contact_no = '$new_phone' WHERE id = $user_id";
    mysqli_query($conn, $update_query);

    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed_pass' WHERE id = $user_id");
    }

    $_SESSION['first_name'] = $new_fname;
    header("Location: profile.php?status=updated");
    exit();
}

$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

$first_name = $user['first_name'] ?? '';
$last_name = $user['last_name'] ?? '';
$full_name = htmlspecialchars(trim($first_name . ' ' . $last_name));

$count_res = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
$count_row = mysqli_fetch_assoc($count_res);
$cart_count = $count_row['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Apparel Clothing Line</title>
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo $v; ?>">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="profile-page">

<header class="header">
    <div class="header-container">
        <div class="logo">
            <a href="index.php">
                <img src="assets/images/new_logo.jpg" alt="Logo" class="header-logo">
            </a>
        </div>

        <div class="header-right">
            <div class="user-dropdown">
                <a href="javascript:void(0)" class="header-icon user-link">
                    <i class="fas fa-user-circle"></i> 
                    <span class="user-name-text"><?= strtoupper(htmlspecialchars($first_name)) ?></span>
                    <i class="fas fa-chevron-down arrow-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="orders.php"><i class="fas fa-shopping-bag"></i> My Orders</a>
                    <hr>
                    <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>

            <div class="cart-section-wrapper">
                <a href="cart.php" class="header-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-quantity"><?php echo $cart_count; ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <div class="profile-card">
        <?php if(isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="success-msg">Profile updated successfully!</div>
        <?php endif; ?>

        <h2 class="profile-title">Account Settings</h2>
        
        <div class="info-group">
            <label>Full Name</label>
            <p><?php echo $full_name; ?></p>
        </div>

        <div class="info-group">
            <label>Email Address</label>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="info-group" style="margin-bottom: 40px;">
            <label>Contact Number</label>
            <p>
                <?php 
                    $phone = $user['contact_no'];
                    echo ($phone == "2147483647" || empty($phone)) ? '<span class="error-text">Please update contact number</span>' : htmlspecialchars($phone);
                ?>
            </p>
        </div>

        <div class="profile-actions">
            <button onclick="openEditModal()" class="btn-save">Edit Profile</button>
            <a href="logout.php" class="btn-cancel">Logout</a>
        </div>
    </div>
</main>

<div id="editModal" class="profile-modal">
    <div class="profile-modal-content">
        <h3>Edit Personal Info</h3>
        <form method="POST">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact_no" value="<?php echo ($user['contact_no'] == "2147483647") ? '' : htmlspecialchars($user['contact_no']); ?>" placeholder="09xxxxxxxxx" required>
            </div>
            
            <div class="form-group password-group">
                <label>New Password</label>
                <div class="input-wrapper">
                     <input type="password" name="new_password" id="newPassword" placeholder="Leave blank to keep current">
                     <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
            <button type="button" onclick="closeEditModal()" class="btn-cancel" style="background:none; border:none; width:100%; cursor:pointer;">Cancel</button>
        </form>
    </div>
</div>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> APPAREL'S CLOTHING LINE. ALL RIGHTS RESERVED.</p>
    </div>
</footer>

<script>
    function openEditModal() { document.getElementById('editModal').style.display = "block"; }
    function closeEditModal() { document.getElementById('editModal').style.display = "none"; }
    window.onclick = function(e) { if (e.target == document.getElementById('editModal')) closeEditModal(); }

    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#newPassword');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
</script>

</body>
</html>