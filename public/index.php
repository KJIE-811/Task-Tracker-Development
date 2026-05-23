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
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f5f5f5;
      padding: 20px;
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .header {
      background: white;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .header h1 {
      color: #333;
      font-size: 2em;
    }
    
    .header-buttons {
      display: flex;
      gap: 10px;
    }
    
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      cursor: pointer;
      font-weight: 600;
      transition: all 0.3s;
      display: inline-block;
    }
    
    .btn-primary {
      background-color: #667eea;
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #5568d3;
    }
    
    .btn-danger {
      background-color: #dc3545;
      color: white;
    }
    
    .btn-danger:hover {
      background-color: #c82333;
    }
    
    .btn-secondary {
      background-color: #6c757d;
      color: white;
    }
    
    .btn-secondary:hover {
      background-color: #5a6268;
    }
    
    .welcome {
      background: white;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      color: #666;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .tasks-section {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .tasks-section h2 {
      color: #333;
      margin-bottom: 20px;
      font-size: 1.5em;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    table thead {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
    }
    
    table th {
      padding: 12px;
      text-align: left;
      font-weight: 600;
      color: #333;
    }
    
    table td {
      padding: 12px;
      border-bottom: 1px solid #dee2e6;
      color: #666;
    }
    
    table tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .priority-1 { color: #28a745; font-weight: 600; }
    .priority-2 { color: #20c997; font-weight: 600; }
    .priority-3 { color: #ffc107; font-weight: 600; }
    .priority-4 { color: #fd7e14; font-weight: 600; }
    .priority-5 { color: #dc3545; font-weight: 600; }
    
    .status-todo { background-color: #e7f3ff; color: #0066cc; padding: 4px 8px; border-radius: 3px; }
    .status-in-progress { background-color: #fff3e0; color: #e65100; padding: 4px 8px; border-radius: 3px; }
    .status-done { background-color: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 3px; }
    
    .no-tasks {
      text-align: center;
      color: #999;
      padding: 40px 20px;
      font-style: italic;
    }
    
    .login-prompt {
      background: white;
      padding: 40px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .login-prompt h2 {
      color: #333;
      margin-bottom: 20px;
    }
    
    .login-prompt p {
      color: #666;
      margin-bottom: 25px;
    }
    
    @media (max-width: 768px) {
      .header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
      
      table {
        font-size: 0.9em;
      }
      
      table th, table td {
        padding: 8px;
      }
    }
  </style>
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
        Welcome back, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>! 👋
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
                  <td><?php echo htmlspecialchars(substr($task['description'] ?? '', 0, 50)); ?><?php echo (strlen($task['description'] ?? '') > 50) ? '...' : ''; ?></td>
                  <td class="priority-<?php echo htmlspecialchars((int)$task['priority'], ENT_QUOTES); ?>">
                    <?php 
                      $priorities = [1 => 'Low', 2 => 'Medium-Low', 3 => 'Medium', 4 => 'High', 5 => 'Critical'];
                      $priorityValue = (int)$task['priority'];
                      echo isset($priorities[$priorityValue]) ? $priorities[$priorityValue] : 'Unknown';
                    ?>
                  </td>
                  <td>
                    <?php
                      $validStatuses = ['todo', 'in_progress', 'done'];
                      $status = $task['status'] ?? 'todo';
                      $statusClass = in_array($status, $validStatuses) ? str_replace('_', '-', $status) : 'todo';
                    ?>
                    <span class="status-<?php echo htmlspecialchars($statusClass, ENT_QUOTES); ?>">
                      <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $status))); ?>
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
