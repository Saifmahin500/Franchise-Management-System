<?php
require_once __DIR__ . "/../../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? '';

    if ($id && $status) {
        $stmt = $pdo->prepare("UPDATE attendance SET status=? WHERE id=?");
        $stmt->execute([$status, $id]);
        header("Location: attendance.php?msg=updated");
        exit;
    }
}
header("Location: attendance.php?err=invalid");
