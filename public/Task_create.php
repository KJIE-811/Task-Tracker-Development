<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = (int)($_POST['priority'] ?? 3);
    $due_date = $_POST['due_date'] ?? null;
    $status = 'todo';
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($title)) {
        $error = 'Task title is required.';
    } elseif (strlen($title) > 255) {
        $error = 'Task title cannot exceed 255 characters.';
    } elseif (strlen($description) > 1000) {
        $error = 'Task description cannot exceed 1000 characters.';
    } elseif ($priority < 1 || $priority > 5) {
        $error = 'Priority must be between 1 and 5.';
    } elseif (!empty($due_date) && strtotime($due_date) === false) {
        $error = 'Invalid due date format.';
    } else {
        // Clear any pending results
        while ($conn->next_result()) {
            if ($res = $conn->use_result()) {
                $res->free();
            }
        }
        
        // Verify user exists in database
        $user_check = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $user_check->bind_param("i", $user_id);
        $user_check->execute();
        $user_result = $user_check->get_result();
        
        if ($user_result->num_rows === 0) {
            $error = 'User not found in database. Please log in again.';
            $user_check->close();
        } else {
            $user_check->close();
            // Prepare and execute insert query
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, status, priority, due_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        
            if ($stmt === false) {
                $error = 'Database error: ' . $conn->error;
            } else {
                $stmt->bind_param(
                    "isssis",
                    $user_id,
                    $title,
                    $description,
                    $status,
                    $priority,
                    $due_date
                );

                if ($stmt->execute()) {
                    $success = 'Task created successfully!';
                    // Redirect to index after 2 seconds
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "index.php";
                        }, 2000);
                    </script>';
                    // Clear form fields
                    $title = '';
                    $description = '';
                    $priority = 3;
                    $due_date = '';
                } else {
                    $error = 'Error creating task: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task - Task Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .welcome {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }

        button,
        .btn-link {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        button[type="submit"] {
            background-color: #667eea;
            color: white;
            flex: 1;
        }

        button[type="submit"]:hover {
            background-color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        button[type="reset"] {
            background-color: #6c757d;
            color: white;
            flex: 1;
        }

        button[type="reset"]:hover {
            background-color: #5a6268;
        }

        .btn-link {
            background-color: transparent;
            color: #667eea;
            padding: 0;
            text-decoration: underline;
            width: auto;
        }

        .btn-link:hover {
            color: #5568d3;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .character-count {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }

            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Task</h1>
        <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Task Title *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required 
                    maxlength="255"
                    placeholder="Enter task title"
                    value="<?php echo htmlspecialchars($title ?? ''); ?>"
                >
                <div class="character-count"><span id="titleCount">0</span>/255</div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    placeholder="Enter task description (optional)"
                    maxlength="1000"
                ><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                <div class="character-count"><span id="descCount">0</span>/1000</div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority">
                        <option value="1" <?php echo (($priority ?? 3) == 1) ? 'selected' : ''; ?>>1 - Lowest</option>
                        <option value="2" <?php echo (($priority ?? 3) == 2) ? 'selected' : ''; ?>>2 - Low</option>
                        <option value="3" <?php echo (($priority ?? 3) == 3) ? 'selected' : ''; ?>>3 - Medium (Default)</option>
                        <option value="4" <?php echo (($priority ?? 3) == 4) ? 'selected' : ''; ?>>4 - High</option>
                        <option value="5" <?php echo (($priority ?? 3) == 5) ? 'selected' : ''; ?>>5 - Highest</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input 
                        type="date" 
                        id="due_date" 
                        name="due_date"
                        value="<?php echo htmlspecialchars($due_date ?? ''); ?>"
                    >
                </div>
            </div>

            <div class="button-group">
                <button type="submit">Create Task</button>
                <button type="reset">Clear</button>
            </div>
        </form>

        <div class="back-link">
            <a href="dashboard.php" class="btn-link">← Back to Dashboard</a>
        </div>
    </div>

    <script>
        // Character counter for title
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('titleCount').textContent = this.value.length;
        });

        // Character counter for description
        document.getElementById('description').addEventListener('input', function() {
            document.getElementById('descCount').textContent = this.value.length;
        });

        // Set minimum due date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('due_date').setAttribute('min', today);
    </script>
</body>
</html>