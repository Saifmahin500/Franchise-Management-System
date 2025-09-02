<?php
require_once __DIR__ . "/../../config/db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO staff (name, position, salary, joining_date, branch_id) VALUES (?, ?, ?, ?, ?)");
    $ok = $stmt->execute([$_POST['name'], $_POST['position'], $_POST['salary'], $_POST['joining_date'], $_POST['branch_id']]);

    $_SESSION['success'] = $ok ? "Staff added successfully!" : "Failed to add staff.";
    header("Location: staff.php");
    exit;
}
