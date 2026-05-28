<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';
include 'csrf.php';

$project_id = (int)($_GET['project_id'] ?? 0);

if ($project_id === 0) {
    die("Project workspace selection required.");
}

$p_stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$p_stmt->bind_param("i", $project_id);
$p_stmt->execute();
$project = $p_stmt->get_result()->fetch_assoc();
$p_stmt->close();

if (!$project) {
    die("Project not found.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_member'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        $error = "Security validation failed.";
    } else {
        $new_user_id = (int)$_POST['user_id'];
        
        $check = $conn->prepare("SELECT project_id FROM project_members WHERE project_id = ? AND user_id = ?");
        $check->bind_param("ii", $project_id, $new_user_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "User is already a member of this project workspace.";
        } else {
            $ins = $conn->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, 'member')");
            $ins->bind_param("ii", $project_id, $new_user_id);
            if ($ins->execute()) {
                $success = "Team member added successfully!";
            } else {
                $error = "Failed to add member to workspace: " . $ins->error;
            }
            $ins->close();
        }
        $check->close();
    }
}

$mem_query = $conn->prepare("SELECT users.id, users.name, users.email, project_members.role FROM project_members JOIN users ON project_members.user_id = users.id WHERE project_members.project_id = ?");
$mem_query->bind_param("i", $project_id);
$mem_query->execute();
$members = $mem_query->get_result()->fetch_all(MYSQLI_ASSOC);
$mem_query->close();

$all_users = $conn->query("SELECT id, name FROM users")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Project Team</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .team-box { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;}
        .member-card { background: white; padding: 10px; margin: 5px 0; border-radius: 4px; border-left: 4px solid #007bff; display:flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>👥 Manage Team: <?php echo htmlspecialchars($project['name']); ?></h1>
        <a href="project_view.php?project_id=<?php echo $project_id; ?>" class="btn btn-secondary">Back to Project Workspace</a>
    </div>

    <?php if(!empty($error)): ?><div class="alert alert-error" style="color:red; margin-bottom: 15px;"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if(!empty($success)): ?><div class="alert alert-success" style="color:green; margin-bottom: 15px;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <div class="team-box">
        <h3>Invite a Team Member</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">
            <select name="user_id" style="padding: 10px; width: 250px; border-radius: 4px; border: 1px solid #ccc;" required>
                <option value="">-- Select User --</option>
                <?php foreach ($all_users as $u): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_member" class="btn btn-primary" style="padding:10px 15px; margin-left: 5px;">Add to Project</button>
        </form>
    </div>

    <h2>Current Team Members</h2>
    <div>
        <?php foreach ($members as $m): ?>
            <div class="member-card">
                <strong><?php echo htmlspecialchars($m['name']); ?> (<?php echo htmlspecialchars(ucfirst($m['role'] ?? 'member')); ?>)</strong>
                <span style="color:#666; font-size:13px;"><?php echo htmlspecialchars($m['email']); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>