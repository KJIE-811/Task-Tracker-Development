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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        $error = "Security validation failed.";
    } else {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        // 🔥 NEW: Capture project due date from the form
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        $user_id = $_SESSION['user_id'];

        if (empty($name)) {
            $error = "Project name is required.";
        } else {
            // 🔥 UPDATED: Wrap project and owner membership in a transaction
            try {
                $conn->begin_transaction();
                
                $stmt = $conn->prepare("INSERT INTO projects (name, description, owner_id, due_date) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssis", $name, $description, $user_id, $due_date);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error creating project: " . $stmt->error);
                }
                
                $project_id = $stmt->insert_id;
                $stmt->close();
                
                // Establish creator automatically with 'owner' privileges 
                $member_stmt = $conn->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, 'owner')");
                $member_stmt->bind_param("ii", $project_id, $user_id);
                
                if (!$member_stmt->execute()) {
                    throw new Exception("Error adding owner to project members: " . $member_stmt->error);
                }
                
                $member_stmt->close();
                
                // Commit transaction
                $conn->commit();
                
                $_SESSION['success'] = "Project created successfully!";
                header("Location: project_view.php?project_id=" . $project_id);
                exit;
            } catch (Exception $e) {
                // Rollback on error
                $conn->rollback();
                $error = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/task-create.css">
</head>
<body>
<div class="container">
    <h1>Create New Project</h1>
    <?php if(!empty($error)): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
        <div class="form-group">
            <label>Project Name *</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>
        <div class="form-group">
            <label>Project Due Date</label>
            <input type="date" name="due_date">
        </div>
        <button type="submit">Create Project & Continue</button>
        <a href="index.php" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
    </form>
</div>
</body>
</html>