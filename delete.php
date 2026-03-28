<?php
require 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    // Fetch photo path to delete file
    $stmt = $pdo->prepare("SELECT photo_path FROM matrimonial_biodata WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetchColumn();

    // Delete record
    $stmt = $pdo->prepare("DELETE FROM matrimonial_biodata WHERE id = ?");
    $stmt->execute([$id]);

    // Delete photo file if exists
    if ($photo && file_exists($photo)) {
        unlink($photo);
    }
}
header("Location: index.php");
exit;