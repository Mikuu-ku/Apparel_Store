<?php
session_start();
include "config/database.php";

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$success = false;
$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['role']       = $user['role']; 
        $_SESSION['name']       = $user['first_name']; 
        $_SESSION['first_name'] = $user['first_name']; 
        $_SESSION['last_name']  = $user['last_name'];

        $success = true; 

        if ($user['role'] === 'admin') {
            $redirect = "admin/dashboard.php";
        } 
        elseif (isset($_SESSION['pending_cart'])) {
            $redirect = "process_cart.php";
        } 
        else {
            $redirect = "index.php";
        }

        header("Refresh: 1.5; url=$redirect"); 
    } else {
        $error = "INVALID EMAIL OR PASSWORD";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle"><i class="fas fa-check"></i></div>
                <h3 style="letter-spacing: 1px; text-transform: uppercase;">Welcome back, <?= htmlspecialchars($_SESSION['first_name']) ?></h3>
                <p style="font-size: 11px; color: #888; margin-top: 10px; letter-spacing: 1px; text-transform: uppercase;">
                    <?php echo isset($_SESSION['pending_cart']) ? "Saving your items to bag..." : "Authenticating credentials..."; ?>
                </p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/new_logo.jpg" alt="Logo">
                </a>
            </div>

            <h2>Sign In</h2>
            <p class="auth-subtext">ACCESS YOUR ACCOUNT</p>

            <form method="POST" class="auth-form" id="loginForm" novalidate>
                <div class="input-group">
                    <input type="email" name="email" id="email" placeholder="EMAIL ADDRESS">
                    <div id="email-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT EMAIL</div>
                </div>
                
                <div class="input-group">
                    <div class="pass-field-wrapper">
                        <input type="password" name="password" id="password" placeholder="PASSWORD">
                        <i class="fas fa-eye toggle-icon" onclick="togglePass('password', this)"></i>
                    </div>
                    <div id="password-error" class="field-error"><i class="fas fa-exclamation-triangle"></i> PLEASE INPUT PASSWORD</div>
                </div>

                <?php if(!empty($error)): ?>
                    <div class="field-error" style="display: flex; justify-content: center; margin-bottom: 15px;">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <button type="submit" name="login">Login</button>
            </form>

            <div class="auth-footer">
                NO ACCOUNT? <a href="register.php">JOIN NOW</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function togglePass(inputId, icon) {
    const field = document.getElementById(inputId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    const emailInput = document.getElementById('email');
    const passInput = document.getElementById('password');
    const emailError = document.getElementById('email-error');
    const passError = document.getElementById('password-error');
    
    emailError.style.display = 'none';
    passError.style.display = 'none';
    emailInput.classList.remove('input-error');
    passInput.classList.remove('input-error');

    let isValid = true;

    if (!emailInput.value.trim()) {
        emailInput.classList.add('input-error');
        emailError.style.display = 'flex';
        isValid = false;
    } 
    
    if (!passInput.value.trim()) {
        passInput.classList.add('input-error');
        passError.style.display = 'flex';
        isValid = false;
    }

    if (!isValid) e.preventDefault();
});
</script>
</body>
</html>