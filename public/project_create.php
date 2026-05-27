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
        $user_id = $_SESSION['user_id'];

        if (empty($name)) {
            $error = "Project name is required.";
        } else {
            // Adjusted to use your explicit column name: owner_id
            $stmt = $conn->prepare("INSERT INTO projects (name, description, owner_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $description, $user_id);
            
            if ($stmt->execute()) {
                $project_id = $stmt->insert_id;
                
                // Adjusted to add creator with 'owner' role to project_members
                $member_stmt = $conn->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, 'owner')");
                $member_stmt->bind_param("ii", $project_id, $user_id);
                $member_stmt->execute();
                
                $_SESSION['success'] = "Project created successfully!";
                header("Location: index.php");
                exit;
            } else {
                $error = "Error creating project.";
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
        <button type="submit">Create Project & Continue</button>
        <a href="index.php" style="margin-left: 10px; color: #666;">Cancel</a>
    </form>
</div>
</body>
</html>