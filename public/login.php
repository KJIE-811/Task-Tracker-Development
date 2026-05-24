<?php
session_start();
include 'db.php';
include 'csrf.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        $message = "Security validation failed. Please try again.";
    } else {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($username) || empty($password)) {
            $message = "Username and password are required!";
        } else {
            // Select user query to check validation
            $select_sql = "SELECT id, name, username, password_hash FROM users WHERE username = ?";
            $stmt = $conn->prepare($select_sql);
            
            if ($stmt === false) {
                $message = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();

                    // Verify password
                    if (password_verify($password, $user['password_hash'])) {
                        // Store user info in session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['name'] = $user['name'];

                        $stmt->close();
                        header("Location: index.php");
                        exit;
                    } else {
                        error_log("Failed login attempt: Invalid password for username '" . htmlspecialchars($username) . "'");
                        $message = "Invalid username or password!";
                    }
                } else {
                    error_log("Failed login attempt: Username '" . htmlspecialchars($username) . "' not found");
                    $message = "Invalid username or password!";
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>
    <h1>Login</h1>

    <?php if ($message): ?>
        <div class="message <?php echo (strpos($message, 'successful') !== false) ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Login</button>
    </form>

    <div class="link">
        Don't have an account? <a href="register.php">Register here</a>
    </div>
</body>

</html>