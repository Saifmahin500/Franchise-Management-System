<?php
require_once __DIR__ . "/../../config/db.php";

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id=?");
    $stmt->execute([$id]);
    header("Location: attendance.php?msg=deleted");
    exit;
}
header("Location: attendance.php?err=invalid");
