<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
  SELECT c.name, c.description, cm.role, c.id
  FROM chama_members cm
  JOIN chamas c ON cm.chama_id = c.id
  WHERE cm.user_id = ?
");
$stmt->execute([$user_id]);
$chamas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Chamas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h3>My Chamas</h3>
  <a href="create_chama.php" class="btn btn-success mb-3">+ Create New</a>
  <ul class="list-group">
    <?php foreach ($chamas as $chama): ?>
      <li class="list-group-item">
        <strong><?= htmlspecialchars($chama['name']) ?></strong><br>
        <small><?= htmlspecialchars($chama['description']) ?></small><br>
        <span class="badge bg-info text-dark"><?= $chama['role'] ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>
