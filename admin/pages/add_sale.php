<?php
require_once __DIR__ . "/../../config/db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sale_date = $_POST['date'] ?? null;
    $branch_id = $_POST['branch_id'] ?? null;
    $amount = $_POST['amount'] ?? null;

    if ($sale_date && $branch_id && $amount) {
        $stmt = $pdo->prepare("INSERT INTO sales (sale_date, branch_id, amount) VALUES (?, ?, ?)");
        if ($stmt->execute([$sale_date, $branch_id, $amount])) {
            $_SESSION['success'] = "Sale added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add sale!";
        }
    } else {
        $_SESSION['error'] = "All fields are required!";
    }

    header("Location: sales.php");
    exit;
}
