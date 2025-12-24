<?php
// includes/login.php
session_start();
require_once(__DIR__ . '/../db_chamahub.php'); // adjust path if db file is outside /includes

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $identifier = trim($_POST['email']);  // Can be email or username
    $password = trim($_POST['password']);

    if (empty($identifier) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both email/username and password.";
        header("Location: ../index.php");
        exit();
    }

    // Fetch user
    $stmt = $conn->prepare("SELECT id, username, email, password, name, role, chama_name FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_data'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role'],
                'chama_name' => $user['chama_name'] ?? null
            ];
            $_SESSION['logged_in'] = true;

            // Redirect based on role
            header("Location: " . ($user['role'] === 'admin' ? "../dashboard.php" : "../member_dashboard.php"));
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password.";
        }
    } else {
        $_SESSION['login_error'] = "User not found.";
    }

    header("Location: ../index.php");
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>
