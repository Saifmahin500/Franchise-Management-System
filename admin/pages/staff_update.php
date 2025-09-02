<?php
require_once __DIR__ . "/../../config/db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE staff SET name=?, position=?, salary=?, joining_date=?, branch_id=? WHERE id=?");
    $ok = $stmt->execute([$_POST['name'], $_POST['position'], $_POST['salary'], $_POST['joining_date'], $_POST['branch_id'], $_POST['id']]);

    $_SESSION['success'] = $ok ? "Staff updated successfully!" : "Failed to update staff.";
    header("Location: staff.php");
    exit;
}
