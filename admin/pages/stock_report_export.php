<?php
require_once __DIR__ . "/../../config/db.php";

$type = $_GET['type'] ?? 'excel';
$branch_id = $_GET['branch_id'] ?? '';

// -------- Fetch Data ----------
$query = "SELECT s.product_name, s.category, s.quantity, s.reorder_level, b.name AS branch
          FROM stock s
          JOIN branches b ON s.branch_id = b.id
          WHERE 1=1";
$params = [];

if ($branch_id) {
    $query .= " AND s.branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

$query .= " ORDER BY s.product_name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------- Excel Export ----------
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=stock_report.xls");
    echo "Product\tCategory\tBranch\tQuantity\tReorder Level\n";
    foreach ($stocks as $row) {
        echo "{$row['product_name']}\t{$row['category']}\t{$row['branch']}\t{$row['quantity']}\t{$row['reorder_level']}\n";
    }
    exit;
}

// -------- PDF Export ----------
if ($type === "pdf") {
    require_once __DIR__ . "/../../dompdf/autoload.inc.php";

    $html = "<h2 style='text-align:center'>Stock Report</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Branch</th>
                        <th>Quantity</th>
                        <th>Reorder Level</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($stocks as $row) {
        $html .= "<tr>
                    <td>{$row['product_name']}</td>
                    <td>{$row['category']}</td>
                    <td>{$row['branch']}</td>
                    <td style='text-align:center'>{$row['quantity']}</td>
                    <td style='text-align:center'>{$row['reorder_level']}</td>
                  </tr>";
    }
    $html .= "</tbody></table>";

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("stock_report.pdf", ["Attachment" => true]);
    exit;
}

echo "Invalid export type.";
