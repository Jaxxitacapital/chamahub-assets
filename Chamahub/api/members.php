<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

$sql = "SELECT id, full_name, phone_number, email, role, joined_at FROM members";
$result = $conn->query($sql);

$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $members]);
?>
