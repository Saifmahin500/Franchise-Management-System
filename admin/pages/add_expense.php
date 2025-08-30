<?php
require_once __DIR__ . "/../../config/db.php";

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_date = $_POST['expense_date'] ?? '';
    $branch_id    = $_POST['branch_id'] ?? '';
    $category     = $_POST['category'] ?? '';
    $amount       = $_POST['amount'] ?? '';

    if ($expense_date && $branch_id && $category && $amount) {
        $stmt = $pdo->prepare("INSERT INTO expenses (expense_date, branch_id, category, amount) 
                               VALUES (:expense_date, :branch_id, :category, :amount)");
        $stmt->execute([
            ':expense_date' => $expense_date,
            ':branch_id'    => $branch_id,
            ':category'     => $category,
            ':amount'       => $amount
        ]);

        header("Location: expenses.php?success=1");
        exit;
    } else {
        header("Location: expenses.php?error=Please+fill+all+fields");
        exit;
    }
} else {
    header("Location: expenses.php");
    exit;
}
