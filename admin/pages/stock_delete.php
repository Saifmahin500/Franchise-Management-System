<?php
require_once __DIR__ . "/../../config/db.php";

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM stock WHERE id=?");
    $stmt->execute([$id]);
}
header("Location: stock.php");
exit;
