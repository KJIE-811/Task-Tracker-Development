<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection and CSRF protection
include 'db.php';
include 'csrf.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        $error = 'Security validation failed. Please try again.';
    } else {
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
                        $_SESSION['success'] = 'Task created successfully!';
                     header("Location: index.php");
                     exit;
                    } else {
                        $error = 'Error creating task: ' . $stmt->error;
                    }
                    $stmt->close();
                }
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
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/task-create.css">
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
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
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