<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .profile-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 40px auto;
        }
        .user-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .user-meta p { margin: 8px 0; font-size: 15px; }
        .nav-actions { display: flex; gap: 15px; margin-top: 25px; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .btn-home { background: #007bff; color: #fff; flex: 2; }
        .btn-home:hover { background: #0056b3; }
        .btn-logout { background: #dc3545; color: #fff; flex: 1; }
        .btn-logout:hover { background: #bd2130; }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-card">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
            <p style="color: #666;">You are successfully logged in.</p>
            
            <div class="user-meta">
                <p>👤 <strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                <p>🏷️ <strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            </div>

            <div class="nav-actions">
                <a href="index.php" class="btn btn-home">🏠 Back to Homepage Hub</a>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>