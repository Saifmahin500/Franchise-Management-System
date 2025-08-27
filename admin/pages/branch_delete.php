<?php
require_once("../dbConfig.php");

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $DB_con->prepare("DELETE FROM branches WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: branches.php?deleted=1");
exit;
