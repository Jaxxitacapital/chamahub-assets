<?php
session_start();
require_once '../includes/db.php'; // Adjust path as needed

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $desc = $_POST['description'];
  $created_by = $_SESSION['user_id'];

  $stmt = $pdo->prepare("INSERT INTO chamas (name, description, created_by) VALUES (?, ?, ?)");
  $stmt->execute([$name, $desc, $created_by]);

  $chama_id = $pdo->lastInsertId();

  // Add creator as admin in chama_members
  $stmt = $pdo->prepare("INSERT INTO chama_members (chama_id, user_id, role) VALUES (?, ?, 'admin')");
  $stmt->execute([$chama_id, $created_by]);

  header("Location: my_chamas.php");
  exit;
}
?>

<!-- Form UI -->
<!DOCTYPE html>
<html>
<head>
  <title>Create Chama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h3>Create a New Chama</h3>
  <form method="POST">
    <div class="mb-3">
      <label for="name" class="form-label">Chama Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Create Chama</button>
  </form>
</body>
</html>
