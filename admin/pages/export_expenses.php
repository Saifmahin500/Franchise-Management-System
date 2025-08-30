<?php
require_once __DIR__ . "/../../config/db.php";

// Get filter values from query string
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-t');
$category = $_GET['category'] ?? '';

// Build Query
$sql = "SELECT e.expense_date, b.name AS branch, e.category, e.amount
        FROM expenses e
        JOIN branches b ON e.branch_id = b.id
        WHERE e.expense_date BETWEEN :from AND :to";

$params = [
    ':from' => $from,
    ':to'   => $to
];

if ($category !== '') {
    $sql .= " AND e.category = :category";
    $params[':category'] = $category;
}

$sql .= " ORDER BY e.expense_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CSV headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=expenses_' . date('Ymd_His') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Branch', 'Category', 'Amount']);

foreach ($expenses as $exp) {
    fputcsv($output, [$exp['expense_date'], $exp['branch'], $exp['category'], $exp['amount']]);
}

fclose($output);
exit;
