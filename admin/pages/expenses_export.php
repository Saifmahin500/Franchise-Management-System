<?php
require_once __DIR__ . "/../../config/db.php";

// Export type: "excel" বা "pdf"
$type = $_GET['type'] ?? '';
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-t');
$category = $_GET['category'] ?? '';

// Filtered query
$where = "WHERE e.expense_date BETWEEN :from AND :to";
$params = [':from' => $from, ':to' => $to];
if ($category !== '') {
    $where .= " AND e.category = :category";
    $params[':category'] = $category;
}

$sql = "SELECT e.expense_date, b.name AS branch, e.category, e.amount
        FROM expenses e
        JOIN branches b ON e.branch_id = b.id
        $where
        ORDER BY e.expense_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// Excel Export
// ===============================
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=expenses_" . date('Ymd_His') . ".xls");
    echo "\xEF\xBB\xBF"; // BOM for UTF-8

    echo "Date\tBranch\tCategory\tAmount\n";
    foreach ($expenses as $exp) {
        $amt = number_format($exp['amount'], 2, '.', '');
        echo "{$exp['expense_date']}\t{$exp['branch']}\t{$exp['category']}\t{$amt}\n";
    }
    exit;
}

// ===============================
// PDF Export using Dompdf
// ===============================
if ($type === 'pdf') {
    require_once __DIR__ . "/../../dompdf/autoload.inc.php";

    $html = "<h3 style='text-align:center'>Expenses Report</h3>";
    $html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
                <thead>
                    <tr style='background:#f2f2f2;'>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($expenses as $exp) {
        $amt = number_format($exp['amount'], 2, '.', ',');
        $html .= "<tr>
                    <td>{$exp['expense_date']}</td>
                    <td>{$exp['branch']}</td>
                    <td>{$exp['category']}</td>
                    <td style='text-align:right'>{$amt}</td>
                  </tr>";
    }
    $html .= "</tbody></table>";

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("expenses_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);
    exit;
}

echo "Invalid export type!";
