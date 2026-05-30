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

// Fetch the current project's name and its due date to validate against
$user_id = $_SESSION['user_id'];
// 🔥 UPDATED: Selecting p.due_date alongside the name
$proj_stmt = $conn->prepare("
    SELECT p.name, p.due_date 
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

// Fetch valid statuses and priorities from database
$status_list = [];
$priority_list = [];

$status_stmt = $conn->prepare("SELECT id, name FROM status ORDER BY id");
$status_stmt->execute();
$status_result = $status_stmt->get_result();
while ($row = $status_result->fetch_assoc()) {
    $status_list[$row['id']] = $row['name'];
}
$status_stmt->close();

$priority_stmt = $conn->prepare("SELECT id, name FROM priority ORDER BY id");
$priority_stmt->execute();
$priority_result = $priority_stmt->get_result();
while ($row = $priority_result->fetch_assoc()) {
    $priority_list[$row['id']] = $row['name'];
}
$priority_stmt->close();

if (empty($status_list) || empty($priority_list)) {
    die("Error: Status or Priority table is empty. Please contact administrator.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = "Security validation failed. Please try again.";
    } else {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority_id = (int)($_POST['priority_id'] ?? 0); 
        $status_id = (int)($_POST['status_id'] ?? 0);     
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if (empty($title)) {
            $error = "Task title is required.";
        } elseif (!isset($status_list[$status_id])) { 
            $error = "Invalid status selected.";
        } elseif (!isset($priority_list[$priority_id])) {
            $error = "Invalid priority selected.";
        } 
        // 🔥 NEW: Backend Validation Rule - Compare Task due date against Project deadline
        elseif ($due_date !== null && $project_data['due_date'] !== null && strtotime($due_date) > strtotime($project_data['due_date'])) {
            $error = "Task due date cannot be later than the project deadline (" . date('M d, Y', strtotime($project_data['due_date'])) . ").";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO tasks 
                (created_by, project_id, title, description, status_id, priority_id, due_date, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->bind_param(
                "iissiis",
                $user_id,
                $project_id,
                $title,
                $description,
                $status_id,
                $priority_id,
                $due_date
            );

            if ($stmt->execute()) {
                $_SESSION['success'] = "Task created successfully!";
                header("Location: project_view.php?project_id=" . $project_id);
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
                <?php if ($project_data['due_date']): ?>
                    <span style="font-weight: normal; color: #666; font-size: 13px; margin-left: 10px;">
                        (Deadline: <?php echo date('M d, Y', strtotime($project_data['due_date'])); ?>)
                    </span>
                <?php endif; ?>
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
            <select name="status_id">
                <?php foreach ($status_list as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php echo ($id === 1) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Priority</label>
            <select name="priority_id">
                <?php foreach ($priority_list as $id => $name): ?>
                    <option value="<?php echo $id; ?>" <?php echo ($id === 3) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Due Date</label>
            <input type="date" name="due_date" 
                   <?php if ($project_data['due_date']): ?> max="<?php echo $project_data['due_date']; ?>" <?php endif; ?>>
        </div>

        <button type="submit">Create Task</button>
        <a href="project_view.php?project_id=<?php echo $project_id; ?>" style="margin-left:15px; color:#666; text-decoration:none;">Cancel</a>
    </form>
</div>
</body>
</html>