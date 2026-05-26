<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'csrf.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = "Security validation failed. Please try again.";
    } else {

        // Get form data
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        // MATCHED TO YOUR LIVE DATABASE COLUMNS
        $priority = (int)($_POST['priority'] ?? 3); // Default to 3 (Medium)
        $status = (int)($_POST['status'] ?? 1);     // Dynamic status from the form dropdown (Default to 1: todo)

        // Normalize empty due date string to null
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $user_id = $_SESSION['user_id'];

        // Validation
        if (empty($title)) {
            $error = "Task title is required.";
        } elseif (strlen($title) > 255) {
            $error = "Task title cannot exceed 255 characters.";
        } elseif (strlen($description) > 1000) {
            $error = "Task description cannot exceed 1000 characters.";
        } elseif (!in_array($priority, [1, 2, 3, 4, 5])) { 
            $error = "Invalid priority selected.";
        } elseif (!in_array($status, [1, 2, 3, 4])) { // Validates against your 4 database statuses
            $error = "Invalid status selected.";
        } elseif ($due_date !== null && strtotime($due_date) === false) {
            $error = "Invalid due date.";
        } else {

            // SQL uses your live table's column names: 'status' and 'priority'
            $stmt = $conn->prepare("
                INSERT INTO tasks 
                (user_id, title, description, status, priority, due_date, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->bind_param(
                "issiis",
                $user_id,
                $title,
                $description,
                $status,
                $priority,
                $due_date
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Task created successfully!";
                header("Location: index.php");
                exit;
            } else {
                $error = "Error creating task: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/task-create.css">
</head>
<body>

<div class="container">

    <h1>Create New Task</h1>

    <div class="welcome">
        Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>!
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
               value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

        <div class="form-group">
            <label>Task Title *</label>
            <input type="text" name="title" maxlength="255" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" maxlength="1000"></textarea>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="1" selected>Todo</option>
                <option value="2">In Progress</option>
                <option value="3">Completed</option>
                <option value="4">On Hold</option>
            </select>
        </div>

        <div class="form-group">
            <label>Priority</label>
            <select name="priority">
                <option value="1">Lowest</option>
                <option value="2">Low</option>
                <option value="3" selected>Medium</option>
                <option value="4">High</option>
                <option value="5">Highest</option>
            </select>
        </div>

        <div class="form-group">
            <label>Due Date</label>
            <input type="date" name="due_date">
        </div>

        <button type="submit">Create Task</button>

    </form>

</div>

</body>
</html>