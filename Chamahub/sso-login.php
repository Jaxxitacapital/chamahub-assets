<?php
// Start session
session_start();

// Show all errors (for debugging — remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
require_once(__DIR__ . '/includes/db_chamahub.php');

// Check if token is present in URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("❌ Token missing.");
}

$token = $_GET['token'];

// Prepare statement to fetch user by token
$stmt = $conn->prepare("SELECT * FROM users WHERE ss_sso_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

// If token matches a user
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Set session data
    $_SESSION['user'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'role' => $user['role']
    ];

    // Clear the token after successful login (optional but recommended)
    $clearToken = $conn->prepare("UPDATE users SET ss_sso_token = NULL WHERE id = ?");
    $clearToken->bind_param("i", $user['id']);
    $clearToken->execute();

    // Redirect to ChamaHub dashboard
    header("Location: dashboard.php");
    exit;
} else {
    echo "❌ Invalid or expired token.";
}
?>
