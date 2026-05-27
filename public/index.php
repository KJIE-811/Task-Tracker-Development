<?php
session_start();
include 'db.php';

http_response_code(200);
header('Content-Type: text/html; charset=utf-8');

$isLoggedIn = isset($_SESSION['user_id']);
$projects = [];

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    
    $p_stmt = $conn->prepare("SELECT p.* FROM projects p JOIN project_members pm ON p.id = pm.project_id WHERE pm.user_id = ?");
    $p_stmt->bind_param("i", $user_id);
    $p_stmt->execute();
    $projects = $p_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $p_stmt->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project Directory</title>
  <link rel="stylesheet" href="assets/css/common.css">
  <link rel="stylesheet" href="assets/css/index.css">
  <style>
      .project-list { display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap;}
      .project-card { 
          background: #f4f6f9; 
          padding: 20px; 
          border-radius: 8px; 
          border: 1px solid #ddd; 
          width: 260px; 
          transition: transform 0.2s;
      }
      .project-card:hover { transform: translateY(-5px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
      .project-link { display: block; font-size: 18px; font-weight: bold; color: #007bff; text-decoration: none; margin-bottom: 10px; }
      .manage-link { font-size: 13px; color: #666; text-decoration: none; display: inline-block; margin-top: 10px;}
      .manage-link:hover { color: #333; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>📋 Project Workspaces</h1>
      <div class="header-buttons">
        <?php if ($isLoggedIn): ?>
          <a href="project_create.php" class="btn btn-secondary">+ New Project</a>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">Login</a>
          <a href="register.php" class="btn btn-secondary">Register</a>
        <?php endif; ?>
      </div>
    </div>
    
    <?php if ($isLoggedIn): ?>
      <div class="welcome">
        Welcome back, <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></strong>! 👋
      </div>

      <div class="tasks-section">
          <h2>Your Active Projects</h2>
          <p style="color: #777;">Click on any project title below to view its specific tasks table board.</p>
          
          <?php if(count($projects) > 0): ?>
              <div class="project-list">
                  <?php foreach($projects as $p): ?>
                      <div class="project-card">
                          <a href="project_view.php?project_id=<?php echo $p['id']; ?>" class="project-link">
                              📁 <?php echo htmlspecialchars($p['name']); ?>
                          </a>
                          <p style="font-size:13px; color:#555; margin:0; min-height:40px;">
                              <?php echo htmlspecialchars(substr($p['description'] ?? '', 0, 60)); ?>
                              <?php echo (strlen($p['description'] ?? '') > 60) ? '...' : ''; ?>
                          </p>
                          <hr style="border:0; border-top:1px solid #ddd; margin-top:10px;">
                          <a href="project_manage.php?project_id=<?php echo $p['id']; ?>" class="manage-link">👥 Invite / Team Members</a>
                      </div>
                  <?php endforeach; ?>
              </div>
          <?php else: ?>
              <div class="no-tasks">
                  <p>No project workspaces found. Get started by clicking <a href="project_create.php">Create a Project</a>!</p>
              </div>
          <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="login-prompt">
        <h2>Welcome to Task Tracker Workspace</h2>
        <p>Please login or register to view and manage your projects.</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>