<?php
// ✅ Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Clear session data
$_SESSION = [];
session_unset();
session_destroy();

// ✅ Clear remember_token
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, "/smartsurplus", "", false, true);
}

// ✅ Optional: Clear PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ✅ Redirect to login page
header("Location: /smartsurplus/Chamahub/includes/login.php?loggedout=1");
exit();
?>
