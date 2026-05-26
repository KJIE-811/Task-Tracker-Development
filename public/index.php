<?php
session_start();
include 'db.php';

http_response_code(200);
header('Content-Type: text/html; charset=utf-8');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$tasks = [];

if ($isLoggedIn) {
    // Fetch user's tasks using prepared statement
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
    
    if ($stmt === false) {
        error_log('Database prepare error: ' . $conn->error);
    } else {
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            error_log('Query execution error: ' . $stmt->error);
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Task Tracker</title>
  <link rel="stylesheet" href="assets/css/common.css">
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>📋 Task Tracker</h1>
      <div class="header-buttons">
        <?php if ($isLoggedIn): ?>
          <a href="task_create.php" class="btn btn-primary">+ Create Task</a>
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
        <h2>Your Tasks</h2>
        <?php if (count($tasks) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Created</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tasks as $task): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($task['title']); ?></strong></td>
                  <td>
                    <?php echo htmlspecialchars(substr($task['description'] ?? '', 0, 50)); ?>
                    <?php echo (strlen($task['description'] ?? '') > 50) ? '...' : ''; ?>
                  </td>
                  
                  <td class="priority-<?php echo (int)$task['priority']; ?>">
                    <?php 
                      $priorities = [
                          1 => 'Lowest', 
                          2 => 'Low', 
                          3 => 'Medium', 
                          4 => 'High', 
                          5 => 'Highest'
                      ];
                      $priorityValue = (int)$task['priority'];
                      echo htmlspecialchars($priorities[$priorityValue] ?? 'Unknown');
                    ?>
                  </td>
                  
                  <td>
                    <?php
                      $statuses = [
                          1 => ['label' => 'Todo', 'class' => 'todo'],
                          2 => ['label' => 'In Progress', 'class' => 'in-progress'],
                          3 => ['label' => 'Completed', 'class' => 'completed'],
                          4 => ['label' => 'On Hold', 'class' => 'on-hold']
                      ];
                      $statusValue = (int)($task['status'] ?? 1);
                      $statusInfo = $statuses[$statusValue] ?? ['label' => 'Unknown', 'class' => 'unknown'];
                    ?>
                    <span class="status-<?php echo htmlspecialchars($statusInfo['class']); ?>">
                      <?php echo htmlspecialchars($statusInfo['label']); ?>
                    </span>
                  </td>
                  
                  <td><?php echo $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '-'; ?></td>
                  <td><?php echo date('M d, Y', strtotime($task['created_at'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="no-tasks">
            <p>No tasks yet. <a href="task_create.php">Create your first task!</a></p>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <div class="login-prompt">
        <h2>Welcome to Task Tracker</h2>
        <p>Please login or register to view and manage your tasks.</p>
        <div style="display: flex; gap: 10px; justify-content: center;">
          <a href="login.php" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px;">Login</a>
          <a href="register.php" class="btn btn-secondary" style="padding: 12px 30px; font-size: 16px;">Register</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>