<?php
require_once __DIR__ . "/../../config/db.php";

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id=?");
    $stmt->execute([$id]);
    header("Location: suppliers.php?msg=Supplier deleted successfully");
    exit;
} else {
    header("Location: suppliers.php?msg=Invalid Supplier ID");
    exit;
}
