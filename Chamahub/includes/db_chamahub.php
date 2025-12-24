<?php
$host = 'sql303.infinityfree.com'; // from your InfinityFree panel
$db   = 'if0_39405308_chamahub_db'; // your actual database name
$user = 'if0_39405308'; // your MySQL username
$pass = 'Astramekins75'; // your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
