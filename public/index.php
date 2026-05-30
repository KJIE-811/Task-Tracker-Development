<?php
session_start();
include 'db.php';

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
  <title>Homepage - Project Hub</title>
  <link rel="stylesheet" href="assets/css/common.css">
  <link rel="stylesheet" href="assets/css/index.css">
  <style>
      .header-buttons { display: flex; gap: 10px; align-items: center; }
      .btn { padding: 8px 16px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block; }
      .btn-primary { background: #007bff; color: white; }
      .btn-secondary { background: #6c757d; color: white; }
      .btn-danger { background: #dc3545; color: white; }
      .project-list { display: flex; gap: 20px; margin-top: 20px; flex-wrap: wrap; }
      .project-card { background: #f4f6f9; padding: 20px; border-radius: 8px; border: 1px solid #ddd; width: 260px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
      <h1>📋 Project Workspaces</h1>
      <div class="header-buttons">
        <?php if ($isLoggedIn): ?>
          <a href="dashboard.php" class="btn btn-secondary">👤 My Profile Dashboard</a>
          <a href="project_create.php" class="btn btn-primary">+ New Project</a>
          <a href="logout.php" class="btn btn-danger">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">Login</a>
          <a href="register.php" class="btn btn-secondary">Register</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($isLoggedIn): ?>
      <div class="tasks-section">
          <h2>Your Active Workspaces</h2>
          <?php if(count($projects) > 0): ?>
              <div class="project-list">
                  <?php foreach($projects as $p): ?>
                      <div class="project-card">
                          <a href="project_view.php?project_id=<?php echo $p['id']; ?>" style="font-size: 18px; font-weight: bold; color: #007bff; text-decoration: none;">
                              📁 <?php echo htmlspecialchars($p['name']); ?>
                          </a>
                          <p style="font-size:13px; color:#555; margin-top:5px;"><?php echo htmlspecialchars($p['description']); ?></p>
                      </div>
                  <?php endforeach; ?>
              </div>
          <?php else: ?>
              <p>No projects found. Create one to get started!</p>
          <?php endif; ?>
      </div>
    <?php else: ?>
      <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
          <h2>Welcome to Task Tracker</h2>
          <p>Please log in to manage your project workspaces.</p>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>