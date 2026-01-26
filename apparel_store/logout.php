<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out | Apparel's Clothing Line</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/new_logo.jpg">
    <meta http-equiv="refresh" content="2;url=login.php?status=logged_out">
    <style>
        .logout-loader-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }

        .loader-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #111;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .logout-text {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #111;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="logout-loader-wrapper">
    <div class="loader-spinner"></div>
    <div class="logout-text">Logging you out securely</div>
</div>

</body>
</html>