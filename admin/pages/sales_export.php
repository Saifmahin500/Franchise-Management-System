<?php
require_once __DIR__ . "/../../config/db.php";

$type = $_GET['type'] ?? '';

// -------- Date Filter (prepared) --------
$where  = "";
$params = [];
if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $where = "WHERE DATE(s.sale_date) BETWEEN :from AND :to";
    $params[':from'] = $_GET['from'];
    $params[':to']   = $_GET['to'];
}

// -------- Fetch sales --------
$sql = "
    SELECT s.id, s.sale_date, b.name AS branch_name, s.amount
    FROM sales s
    JOIN branches b ON s.branch_id = b.id
    $where
    ORDER BY s.sale_date DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================
// Export to Excel
// ===============================
if ($type === "excel") {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=sales_report.xls");
    echo "\xEF\xBB\xBF"; // UTF-8 BOM

    echo "Date\tBranch\tAmount\n";
    foreach ($sales as $row) {
        $amt = number_format((float)$row['amount'], 2, '.', '');
        echo "{$row['sale_date']}\t{$row['branch_name']}\t{$amt}\n";
    }
    exit;
}

// ===============================
// Export to PDF 
// ===============================
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
                    <td>{$row['branch_name']}</td>
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

echo "Invalid export type!";
