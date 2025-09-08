<?php
require_once __DIR__ . "/../../config/db.php";

$type = $_GET['type'] ?? 'excel';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$branch_id = $_GET['branch_id'] ?? '';
$category = $_GET['category'] ?? '';

// -------- Fetch data ----------
$query = "SELECT e.expense_date, b.name AS branch, e.category, e.amount 
          FROM expenses e 
          JOIN branches b ON e.branch_id = b.id 
          WHERE 1=1";
$params = [];

if ($from && $to) {
    $query .= " AND e.expense_date BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;
}
if ($branch_id) {
    $query .= " AND e.branch_id = :branch";
    $params[':branch'] = $branch_id;
}
if ($category) {
    $query .= " AND e.category = :cat";
    $params[':cat'] = $category;
}

$query .= " ORDER BY e.expense_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------- Excel (CSV/TSV) export ----------
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=expenses_report.xls");
    echo "Date\tBranch\tCategory\tAmount\n";
    foreach ($expenses as $e) {
        echo "{$e['expense_date']}\t{$e['branch']}\t{$e['category']}\t{$e['amount']}\n";
    }
    exit;
}

// -------- PDF export ----------
if ($type === "pdf") {
    // dompdf
    require_once __DIR__ . "/../../dompdf/autoload.inc.php";

    $html = "<h2 style='text-align:center'>Expenses Report</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($expenses as $row) {
        $amt = number_format((float)$row['amount'], 2, '.', ',');
        $html .= "<tr>
                    <td>{$row['expense_date']}</td>
                    <td>{$row['branch']}</td>
                    <td>{$row['category']}</td>
                    <td style='text-align:right;'>{$amt}</td>
                  </tr>";
    }

    $html .= "</tbody></table>";

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("expenses_report.pdf", ["Attachment" => true]);
    exit;
}

echo "Invalid export type.";
