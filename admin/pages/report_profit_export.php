<?php
require_once __DIR__ . "/../../config/db.php";

$type = $_GET['type'] ?? 'excel';
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';
$branch_id = $_GET['branch_id'] ?? '';

if (!$from || !$to) {
    // defaults if called directly
    $to = date('Y-m-d');
    $from = date('Y-m-d', strtotime('-5 months', strtotime($to)));
}

// Normalize
$from_dt = date('Y-m-d', strtotime($from));
$to_dt   = date('Y-m-d', strtotime($to));

// monthly salary sum
$sqlSalary = "SELECT COALESCE(SUM(salary),0) FROM staff";
$paramsSal = [];
if ($branch_id) {
    $sqlSalary .= " WHERE branch_id = :branch";
    $paramsSal[':branch'] = $branch_id;
}
$stmt = $pdo->prepare($sqlSalary);
$stmt->execute($paramsSal);
$monthlySalarySum = (float)$stmt->fetchColumn();

// prepare months array
$start = new DateTime(date('Y-m-01', strtotime($from_dt)));
$end = new DateTime(date('Y-m-01', strtotime($to_dt)));
$end->modify('+1 month');
$period = new DatePeriod($start, new DateInterval('P1M'), $end);
$months = [];
foreach ($period as $dt) $months[] = $dt->format('Y-m');

// fetch grouped sales
$sqlMS = "SELECT DATE_FORMAT(sale_date,'%Y-%m') AS ym, COALESCE(SUM(amount),0) AS total
          FROM sales WHERE sale_date BETWEEN :from AND :to";
$paramsMS = [':from' => $from_dt, ':to' => $to_dt];
if ($branch_id) {
    $sqlMS .= " AND branch_id = :branch";
    $paramsMS[':branch'] = $branch_id;
}
$sqlMS .= " GROUP BY ym";
$stmt = $pdo->prepare($sqlMS);
$stmt->execute($paramsMS);
$tmpSales = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// fetch grouped expenses
$sqlME = "SELECT DATE_FORMAT(expense_date,'%Y-%m') AS ym, COALESCE(SUM(amount),0) AS total
          FROM expenses WHERE expense_date BETWEEN :from AND :to";
$paramsME = [':from' => $from_dt, ':to' => $to_dt];
if ($branch_id) {
    $sqlME .= " AND branch_id = :branch";
    $paramsME[':branch'] = $branch_id;
}
$sqlME .= " GROUP BY ym";
$stmt = $pdo->prepare($sqlME);
$stmt->execute($paramsME);
$tmpExp = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// build rows
$rows = [];
foreach ($months as $ym) {
    $dt = DateTime::createFromFormat('Y-m', $ym);
    $label = $dt->format('M Y');
    $s = (float)($tmpSales[$ym] ?? 0);
    $e = (float)($tmpExp[$ym] ?? 0);
    $sal = $monthlySalarySum;
    $net = $s - ($e + $sal);
    $rows[] = ['month' => $label, 'sales' => $s, 'expenses' => $e, 'salary' => $sal, 'net' => $net];
}

// -------- Excel export ----------
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=profit_loss_report.xls");
    echo "Month\tSales\tExpenses\tSalary\tNet\n";
    foreach ($rows as $r) {
        echo "{$r['month']}\t{$r['sales']}\t{$r['expenses']}\t{$r['salary']}\t{$r['net']}\n";
    }
    // totals
    $totS = array_sum(array_column($rows, 'sales'));
    $totE = array_sum(array_column($rows, 'expenses'));
    $totSal = array_sum(array_column($rows, 'salary'));
    $totNet = array_sum(array_column($rows, 'net'));
    echo "\nTotal\t{$totS}\t{$totE}\t{$totSal}\t{$totNet}\n";
    exit;
}

// -------- PDF export ----------
if ($type === 'pdf') {
    // dompdf include (no 'use' at top)
    $autoload = __DIR__ . "/../../dompdf/autoload.inc.php";
    if (!file_exists($autoload)) {
        http_response_code(500);
        echo "Dompdf not found. Run: composer require dompdf/dompdf or place dompdf in project.";
        exit;
    }
    require_once $autoload;

    $html = "<h3 style='text-align:center'>Profit / Loss Report</h3>";
    $html .= "<table border='1' width='100%' cellpadding='6' cellspacing='0' style='border-collapse:collapse;margin-top:10px;'>
                <thead>
                    <tr style='background:#eee;'>
                        <th>Month</th>
                        <th style='text-align:right;'>Sales</th>
                        <th style='text-align:right;'>Expenses</th>
                        <th style='text-align:right;'>Salary</th>
                        <th style='text-align:right;'>Net</th>
                    </tr>
                </thead>
                <tbody>";
    foreach ($rows as $r) {
        $html .= "<tr>
                    <td>{$r['month']}</td>
                    <td style='text-align:right;'>" . number_format($r['sales'], 2) . "</td>
                    <td style='text-align:right;'>" . number_format($r['expenses'], 2) . "</td>
                    <td style='text-align:right;'>" . number_format($r['salary'], 2) . "</td>
                    <td style='text-align:right;'>" . number_format($r['net'], 2) . "</td>
                  </tr>";
    }
    $totS = array_sum(array_column($rows, 'sales'));
    $totE = array_sum(array_column($rows, 'expenses'));
    $totSal = array_sum(array_column($rows, 'salary'));
    $totNet = array_sum(array_column($rows, 'net'));
    $html .= "<tfoot>
                <tr style='font-weight:bold;background:#f4f4f4;'>
                    <td style='text-align:right;'>Total</td>
                    <td style='text-align:right;'>" . number_format($totS, 2) . "</td>
                    <td style='text-align:right;'>" . number_format($totE, 2) . "</td>
                    <td style='text-align:right;'>" . number_format($totSal, 2) . "</td>
                    <td style='text-align:right;'>" . number_format($totNet, 2) . "</td>
                </tr>
              </tfoot>";
    $html .= "</tbody></table>";

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("profit_loss_report.pdf", ["Attachment" => true]);
    exit;
}

echo "Invalid export type.";
