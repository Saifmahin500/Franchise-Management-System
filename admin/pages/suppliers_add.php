<?php
require_once __DIR__ . "/../../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $address = $_POST['address'] ?? '';

    if ($name && $contact) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, contact, address) VALUES (?, ?, ?)");
        $stmt->execute([$name, $contact, $address]);
        header("Location: suppliers.php?msg=Supplier added successfully");
        exit;
    } else {
        header("Location: suppliers.php?msg=All fields are required");
        exit;
    }
}
