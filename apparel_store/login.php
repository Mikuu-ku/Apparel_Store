<?php
session_start();
include "config/database.php";

$success = false;

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role']; 
        $_SESSION['name']    = $user['first_name']; 

        $success = true; 
        $redirect = ($user['role'] === 'admin') ? "admin/dashboard.php" : "index.php";
        header("Refresh: 1.5; url=$redirect"); 
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-form-container">
        <?php if($success): ?>
            <div class="login-success-overlay">
                <div class="checkmark-circle"><i class="fas fa-check"></i></div>
                <h3 style="letter-spacing: 1px;">Welcome back, <?= htmlspecialchars($_SESSION['name']) ?></h3>
                <p style="font-size: 12px; color: #888;">Authenticating credentials...</p>
            </div>
        <?php else: ?>
            <div class="auth-logo">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="Apparel's Logo">
                </a>
            </div>

            <h2>Welcome Back</h2>
            <p class="auth-subtext">Sign in to continue</p>

            <div id="js-error" class="error-msg" style="display:none;"></div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'logged_out'): ?>
                <div class="logout-msg">
                    <i class="fas fa-info-circle"></i> You have been logged out.
                </div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form" novalidate>
                <input type="email" name="email" placeholder="EMAIL ADDRESS" required>
                <input type="password" name="password" placeholder="PASSWORD" required>
                <button type="submit" name="login">Login</button>
            </form>

            <div class="auth-footer">
                New here? <a href="register.php">Create an account</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelector('.auth-form')?.addEventListener('submit', function(e) {
    const errorDiv = document.getElementById('js-error');
    const inputs = this.querySelectorAll('input[required]');
    let isValid = true;

    errorDiv.style.display = 'none';
    inputs.forEach(i => i.style.borderColor = '#ddd');

    for (let input of inputs) {
        if (!input.value.trim()) {
            e.preventDefault();
            isValid = false;
            input.style.borderColor = '#ff0000';
            errorDiv.innerText = "PLEASE FILL OUT ALL FIELDS";
            errorDiv.style.display = 'block';
            input.focus();
            break;
        }
    }
});
</script>
</body>
</html>