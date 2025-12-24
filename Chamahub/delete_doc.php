<?php
require_once 'db_chamahub.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT file_path FROM documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($path);
    $stmt->fetch();
    $stmt->close();

    if (file_exists($path)) {
        unlink($path);
    }

    $stmt = $conn->prepare("DELETE FROM documents WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: documents.php");
    exit;
}
?>
