<?php
// includes/login.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once(__DIR__ . '/db_chamahub.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $identifier = trim($_POST['email']);  // Can be email or username
    $password = trim($_POST['password']);

    if (empty($identifier) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both email/username and password.";
        header("Location: ../index.php");
        exit();
    }

    // Fetch user by email or username
    $stmt = $conn->prepare("SELECT id, username, email, password, name, role, chama_name FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Store user data in session
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
            if ($user['role'] === 'admin') {
                header("Location: ../dashboard.php");
            } else {
                header("Location: ../member_dashboard.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password.";
            header("Location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "User not found.";
        header("Location: ../index.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>
