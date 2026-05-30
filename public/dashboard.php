<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p>You are successfully logged in.</p>
        <p>Username: <?php echo htmlspecialchars($_SESSION['username']); ?></p>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
        <!-- Task Dashboard Section -->

<div class="task-dashboard">

    <h2>My Tasks</h2>

    <div class="task-card">

        <div class="task-info">
            <h3>Software Engineering Report</h3>
            <p>Complete the reflective report documentation.</p>
        </div>

        <span class="status in-progress">
            In Progress
        </span>

    </div>

    <div class="task-card">

        <div class="task-info">
            <h3>Database Setup</h3>
            <p>Configure MySQL database tables.</p>
        </div>

        <span class="status done">
            Done
        </span>

    </div>

    <div class="task-card">

        <div class="task-info">
            <h3>UI Design</h3>
            <p>Improve dashboard appearance and layout.</p>
        </div>

        <span class="status pending">
            Pending
        </span>

    </div>

</div>
    </div>
</body>
</html>
