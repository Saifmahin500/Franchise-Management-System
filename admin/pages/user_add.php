<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin','manager']);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = trim($_POST['username']);
    $email = $_POST['email'] ?? '';
    $password = $_POST['password_hash'];
    $role = $_POST['role'];
    $branch_id = $_POST['branch_id'] ?: null;

    // validation (simple)
    if (!$username || !$password) { header("Location: users.php?err=1"); exit; }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password_hash, role, branch_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $full, $email, $hash, $role, $branch_id]);
}
header("Location: users.php?msg=created");

