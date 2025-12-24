<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_data'])) {
    header("Location: ../index.php");
    exit();
}
