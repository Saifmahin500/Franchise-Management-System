<?php
require_once __DIR__ . "/../../config/db.php";

$type = $_GET['type'] ?? 'excel';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$branch_id = $_GET['branch_id'] ?? '';

// -------- Fetch data ----------
$query = "SELECT s.sale_date, b.name AS branch, s.amount 
          FROM sales s 
          JOIN branches b ON s.branch_id = b.id 
          WHERE 1=1";
$params = [];

if ($from && $to) {
    $query .= " AND s.sale_date BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;
}
if ($branch_id) {
    $query .= " AND s.branch_id = :branch";
    $params[':branch'] = $branch_id;
}
$query .= " ORDER BY s.sale_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------- Excel (CSV/TSV) export ----------
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=sales_report.xls");
    echo "Date\tBranch\tAmount\n";
    foreach ($sales as $s) {
        echo "{$s['sale_date']}\t{$s['branch']}\t{$s['amount']}\n";
    }
    exit;
}

// -------- PDF export ----------
if ($type === "pdf") {
    // dompdf 
    require_once __DIR__ . "/../../dompdf/autoload.inc.php";


    $html = "<h2 style='text-align:center'>Sales Report</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($sales as $row) {
        $amt = number_format((float)$row['amount'], 2, '.', ',');
        $html .= "<tr>
                                <td>{$row['sale_date']}</td>
                                <td>{$row['branch']}</td>   <!-- এখানে branch হবে -->
                                <td style='text-align:right;'>{$amt}</td>
                              </tr>";
    }

    $html .= "</tbody></table>";

    $dompdf = new \Dompdf\Dompdf();     // <-- Fully-qualified class name
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("sales_report.pdf", ["Attachment" => true]);
    exit;
}

echo "Invalid export type.";
