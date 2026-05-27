<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$project_id = (int)($_GET['project_id'] ?? 0);

$p_stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$p_stmt->bind_param("i", $project_id);
$p_stmt->execute();
$project = $p_stmt->get_result()->fetch_assoc();

if (!$project) {
    die("Workspace project channel not found.");
}

// Adjusted columns (status, priority, user_id) to seamlessly match your updated schema
$t_stmt = $conn->prepare("
    SELECT t.*, u.name AS creator_name 
    FROM tasks t 
    LEFT JOIN users u ON t.user_id = u.id
    WHERE t.project_id = ? 
    ORDER BY t.created_at DESC
");
$t_stmt->bind_param("i", $project_id);
$t_stmt->execute();
$tasks = $t_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$t_stmt->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($project['name']); ?> - Tasks Table</title>
  <link rel="stylesheet" href="assets/css/common.css">
  <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>📁 Project Board: <?php echo htmlspecialchars($project['name']); ?></h1>
      <div class="header-buttons">
          <a href="Pmanage.php?project_id=<?php echo $project_id; ?>" class="btn btn-secondary">👥 Team Management</a>
          <a href="Task_create.php?project_id=<?php echo $project_id; ?>" class="btn btn-primary">+ Add Task here</a>
          <a href="index.php" class="btn btn-danger">Back to Dashboard</a>
      </div>
    </div>

    <div class="welcome" style="background: #eef1f6; border-left: 4px solid #555;">
        <strong>Project Description:</strong> <?php echo htmlspecialchars($project['description'] ?: 'No description provided.'); ?>
    </div>
      
    <div class="tasks-section">
        <h2>Tasks Table Listing</h2>
        <?php if (count($tasks) > 0): ?>
          <table>
            <thead>
              <tr>
                <th>Task Title</th>
                <th>Description</th>
                <th>Assigned To</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tasks as $task): ?>
                <tr>
                  <td><strong><?php echo htmlspecialchars($task['title']); ?></strong></td>
                  <td>
                    <?php echo htmlspecialchars(substr($task['description'] ?? '', 0, 60)); ?>
                    <?php echo (strlen($task['description'] ?? '') > 60) ? '...' : ''; ?>
                  </td>
                  <td><span style="font-size:12px; color:#666;"><?php echo htmlspecialchars($task['creator_name'] ?? 'Unassigned'); ?></span></td>
                  
                  <td class="priority-<?php echo (int)$task['priority']; ?>">
                    <?php 
                      $priorities = [1 => 'High', 2 => 'Medium', 3 => 'Low'];
                      echo htmlspecialchars($priorities[(int)$task['priority']] ?? 'Unknown');
                    ?>
                  </td>
                  
                  <td>
                    <?php
                      $statuses = [
                          1 => ['label' => 'Completed', 'class' => 'completed'],
                          2 => ['label' => 'To Do', 'class' => 'todo'],
                          3 => ['label' => 'Pending', 'class' => 'pending']
                      ];
                      $statusInfo = $statuses[(int)($task['status'] ?? 2)] ?? ['label' => 'Unknown', 'class' => 'unknown'];
                    ?>
                    <span class="status-<?php echo htmlspecialchars($statusInfo['class']); ?>">
                      <?php echo htmlspecialchars($statusInfo['label']); ?>
                    </span>
                  </td>
                  
                  <td><?php echo $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '-'; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="no-tasks">
            <p style="text-align: center; color: #666; padding: 20px;">No tasks inside this project room yet. <a href="Task_create.php?project_id=<?php echo $project_id; ?>">Create the first one!</a></p>
          </div>
        <?php endif; ?>
    </div>
  </div>
</body>
</html>