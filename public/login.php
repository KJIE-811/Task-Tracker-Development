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
            // Clear any pending results
            while ($conn->next_result()) {
                if ($res = $conn->use_result()) {
                    $res->free();
                }
            }
            
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
                        $message = "Invalid username or password!";
                    }
                } else {
                    $message = "User not found!";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        h1 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
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