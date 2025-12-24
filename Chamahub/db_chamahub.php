<?php
$host = "sql303.infinityfree.com";     // InfinityFree MySQL host
$user = "if0_39405308";                // InfinityFree MySQL username
$pass = "Astramekins75";               // InfinityFree MySQL password
$dbname = "if0_39405308_chamahub_db";  // ✅ Your actual database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Connected successfully to InfinityFree database!";
}
?>
