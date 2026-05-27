<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'csrf.php';

$error = '';
$success = '';

// Catch the active project ID from the URL context string
$project_id = (int)($_GET['project_id'] ?? 0);

if ($project_id === 0) {
    die("Error: No project workspace context provided.");
}

// Fetch the current project's name to display it as text
$user_id = $_SESSION['user_id'];
$proj_stmt = $conn->prepare("
    SELECT p.name 
    FROM projects p 
    JOIN project_members pm ON p.id = pm.project_id 
    WHERE p.id = ? AND pm.user_id = ?
");
$proj_stmt->bind_param("ii", $project_id, $user_id);
$proj_stmt->execute();
$project_data = $proj_stmt->get_result()->fetch_assoc();
$proj_stmt->close();

if (!$project_data) {
    die("Error: Project workspace not found or you do not have permission to access it.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = "Security validation failed. Please try again.";
    } else {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority = (int)($_POST['priority'] ?? 3); 
        $status = (int)($_POST['status'] ?? 1);     
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if (empty($title)) {
            $error = "Task title is required.";
        } elseif (!in_array($priority, [1, 2, 3, 4, 5])) { 
            $error = "Invalid priority selected.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO tasks 
                (user_id, project_id, title, description, status, priority, due_date, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->bind_param(
                "iiissis",
                $user_id,
                $project_id,
                $title,
                $description,
                $status,
                $priority,
                $due_date
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Task created successfully!";
                header("Location: Pview.php?project_id=" . $project_id);
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
    <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>!</div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

        <div class="form-group">
            <label>Project Workspace</label>
            <div style="background: #eef1f6; padding: 12px; border-radius: 6px; font-weight: bold; border: 1px solid #ccc; color: #333;">
                📁 <?php echo htmlspecialchars($project_data['name']); ?>
            </div>
        </div>

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
        <a href="Pview.php?project_id=<?php echo $project_id; ?>" style="margin-left:15px; color:#666; text-decoration:none;">Cancel</a>
    </form>
</div>
</body>
</html>