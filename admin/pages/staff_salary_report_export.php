<?php
require_once __DIR__ . "/../../config/db.php";

$type = $_GET['type'] ?? 'excel';
$branch_id = $_GET['branch_id'] ?? '';

$where = "";
$params = [];
if ($branch_id) {
    $where = " WHERE st.branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

$query = "SELECT st.name, st.position, st.salary, st.joining_date, b.name AS branch
          FROM staff st
          JOIN branches b ON st.branch_id = b.id
          $where
          ORDER BY st.name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------- Excel Export ----------
if ($type === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=staff_salary_report.xls");
    echo "Name\tPosition\tBranch\tSalary\tJoining Date\n";
    foreach ($staffs as $s) {
        echo "{$s['name']}\t{$s['position']}\t{$s['branch']}\t{$s['salary']}\t{$s['joining_date']}\n";
    }
    exit;
}

// -------- PDF Export ----------
if ($type === "pdf") {
    require_once __DIR__ . "/../../dompdf/autoload.inc.php";

    $html = "<h2 style='text-align:center'>Staff Salary Report</h2>";
    $html .= "<table border='1' cellspacing='0' cellpadding='6' width='100%'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Branch</th>
                        <th>Salary</th>
                        <th>Joining Date</th>
                    </tr>
                </thead>
                <tbody>";

    $total = 0;
    foreach ($staffs as $row) {
        $amt = number_format((float)$row['salary'], 2, '.', ',');
        $total += $row['salary'];
        $html .= "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['position']}</td>
                    <td>{$row['branch']}</td>
                    <td style='text-align:right;'>{$amt}</td>
                    <td>{$row['joining_date']}</td>
                  </tr>";
    }

    $html .= "<tr>
                <td colspan='3' style='text-align:right'><b>Total Salary</b></td>
                <td colspan='2' style='text-align:right'><b>" . number_format($total, 2) . "</b></td>
              </tr>";

    $html .= "</tbody></table>";

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("staff_salary_report.pdf", ["Attachment" => true]);
    exit;
}

echo "Invalid export type.";
