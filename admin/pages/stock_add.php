<?php
require_once __DIR__ . "/../../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product  = $_POST['product_name'] ?? '';
    $branch   = $_POST['branch_id'] ?? '';
    $category = $_POST['category'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $reorder  = $_POST['reorder_level'] ?? 10; // default 10
    
    if ($product && $branch && $category) {
        $stmt = $pdo->prepare("INSERT INTO stock 
            (product_name, branch_id, category, quantity, reorder_level, created_at) 
            VALUES (:product, :branch, :category, :qty, :reorder, NOW())");

        $stmt->execute([
            ':product' => $product,
            ':branch'  => $branch,
            ':category'=> $category,
            ':qty'     => $quantity,
            ':reorder' => $reorder
        ]);
    }
}

header("Location: stock.php");
exit;
