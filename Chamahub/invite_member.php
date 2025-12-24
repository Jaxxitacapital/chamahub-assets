<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$chama_id = $_GET['id'] ?? null;

if (!$chama_id) {
  echo "Invalid chama.";
  exit;
}

// Check if current user is an admin of the chama
$stmt = $pdo->prepare("SELECT role FROM chama_members WHERE chama_id = ? AND user_id = ?");
$stmt->execute([$chama_id, $_SESSION['user_id']]);
$role = $stmt->fetchColumn();

if ($role !== 'admin') {
  echo "Access denied.";
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];

  // Check if user exists
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user) {
    $user_id = $user['id'];

    // Check if already a member
    $stmt = $pdo->prepare("SELECT id FROM chama_members WHERE chama_id = ? AND user_id = ?");
    $stmt->execute([$chama_id, $user_id]);

    if ($stmt->fetch()) {
      $message = "User is already a member.";
    } else {
      $stmt = $pdo->prepare("INSERT INTO chama_members (chama_id, user_id, role) VALUES (?, ?, 'member')");
      $stmt->execute([$chama_id, $user_id]);
      $message = "User added successfully!";
    }
  } else {
    $message = "No user found with that email.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Invite Member</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
  <h3>Invite Member to Chama</h3>
  <?php if (isset($message)): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="mb-3">
      <label for="email" class="form-label">User Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Invite</button>
  </form>
  <a href="my_chamas.php" class="btn btn-secondary mt-3">← Back</a>
</body>
</html>
