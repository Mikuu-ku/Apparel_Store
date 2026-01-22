<?php
session_start();
$_SESSION = array();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - Apparel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            margin: 0; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #fff; 
            font-family: 'Segoe UI', sans-serif; 
        }
        .logout-container { text-align: center; animation: fadeIn 0.8s ease; }
        .spinner { 
            width: 40px; height: 40px; border: 3px solid #f3f3f3; 
            border-top: 3px solid #000; border-radius: 50%; 
            margin: 0 auto 20px; animation: spin 1s linear infinite; 
        }
        h2 { font-size: 14px; text-transform: uppercase; letter-spacing: 2px; color: #111; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <meta http-equiv="refresh" content="1.5;url=../login.php">
</head>
<body>
    <div class="logout-container">
        <div class="spinner"></div>
        <h2>Logging you out securely</h2>
    </div>
</body>
</html>