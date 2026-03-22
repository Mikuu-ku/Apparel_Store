<?php
session_start();
include "config/database.php";

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password   = $_POST['password'];
    $confirm_p  = $_POST['confirm_password'];

    // Server-side check if email exists
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error = "Email is already registered.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (first_name, last_name, email, password, role) 
                  VALUES ('$first_name', '$last_name', '$email', '$hashed_password', 'user')";
        
        if (mysqli_query($conn, $query)) {
            $success = true;
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Ensuring our error styles from your CSS are forced if needed */
        .field-error {
            color: #ff0000;
            font-size: 9px;
            margin-top: 5px;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-weight: bold;
            display: none; /* Hidden by default */
        }
        input.input-error {
            border: 1px solid #ff0000 !important;
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    <?php if ($success): ?>
        <div class="login-success-overlay">
            <div class="checkmark-circle"><i class="fas fa-check"></i></div>
            <h2 class="success-title">ACCOUNT CREATED</h2>
            <p class="success-text">Registration successful! You can now sign in.</p>
            <a href="login.php" class="btn-continue">GO TO LOGIN</a>
        </div>
    <?php else: ?>

        <div class="auth-form-container">
            <div class="auth-logo">
                <a href="index.php"><img src="assets/images/new_logo.jpg" alt="Logo"></a>
            </div>

            <form action="register.php" method="POST" id="regForm" class="auth-form" novalidate>
                <h2 style="font-size: 14px; letter-spacing: 2px; margin-bottom: 30px; color: #888;">CREATE ACCOUNT</h2>

                <?php if ($error): ?>
                    <p style="color: #ff0000; font-size: 10px; text-transform: uppercase; margin-bottom: 15px; text-align:center;">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <div class="input-group">
                    <label class="minimal-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="minimal-input" placeholder="First Name">
                    <span class="field-error" id="error_first_name">First name is required</span>
                </div>

                <div class="input-group">
                    <label class="minimal-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="minimal-input" placeholder="Last Name">
                    <span class="field-error" id="error_last_name">Last name is required</span>
                </div>

                <div class="input-group">
                    <label class="minimal-label">Email Address</label>
                    <input type="email" name="email" id="email" class="minimal-input" placeholder="Email Address">
                    <span class="field-error" id="error_email">Please enter a valid email</span>
                </div>

                <div class="input-group">
                    <label class="minimal-label">Password</label>
                    <div class="pass-field-wrapper">
                        <input type="password" name="password" id="regPass" class="minimal-input" placeholder="••••••••">
                        <i class="fas fa-eye toggle-icon" onclick="togglePass('regPass', this)"></i>
                    </div>
                    <span class="field-error" id="error_password">Password must be at least 6 characters</span>
                </div>

                <div class="input-group">
                    <label class="minimal-label">Confirm Password</label>
                    <div class="pass-field-wrapper">
                        <input type="password" name="confirm_password" id="confirmPass" class="minimal-input" placeholder="••••••••">
                        <i class="fas fa-eye toggle-icon" onclick="togglePass('confirmPass', this)"></i>
                    </div>
                    <span class="field-error" id="error_confirm">Passwords do not match</span>
                </div>

                <button type="submit" class="register-btn">REGISTER</button>

                <div class="auth-footer">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    const form = document.getElementById('regForm');

    form.addEventListener('submit', function(e) {
        let hasError = false;

        // Helper to show error
        const showError = (id, show) => {
            const input = document.getElementById(id);
            const errorMsg = document.getElementById('error_' + id === 'error_confirmPass' ? 'error_confirm' : 'error_' + id);
            
            // Special handling for the confirm ID mapping
            const errorElement = id === 'confirmPass' ? document.getElementById('error_confirm') : 
                                 id === 'regPass' ? document.getElementById('error_password') : 
                                 document.getElementById('error_' + id);

            if (show) {
                input.classList.add('input-error');
                errorElement.style.display = 'flex';
                hasError = true;
            } else {
                input.classList.remove('input-error');
                errorElement.style.display = 'none';
            }
        };

        // 1. Validate First Name
        showError('first_name', document.getElementById('first_name').value.trim() === "");

        // 2. Validate Last Name
        showError('last_name', document.getElementById('last_name').value.trim() === "");

        // 3. Validate Email
        const email = document.getElementById('email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        showError('email', !emailRegex.test(email));

        // 4. Validate Password Length
        const pass = document.getElementById('regPass').value;
        showError('regPass', pass.length < 6);

        // 5. Validate Password Match
        const confirm = document.getElementById('confirmPass').value;
        showError('confirmPass', confirm !== pass || confirm === "");

        if (hasError) {
            e.preventDefault(); // Stop form submission
        }
    });

    function togglePass(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>

</body>
</html>