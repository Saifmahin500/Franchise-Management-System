<?php
require_once __DIR__ . "/../../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// ===== Filters =====
$from = $_GET['from'] ?? date('Y-m-01'); // default current month start
$to   = $_GET['to'] ?? date('Y-m-t');    // default current month end
$branch_id = $_GET['branch_id'] ?? '';

// normalize dates
$from_dt = date('Y-m-d', strtotime($from));
$to_dt   = date('Y-m-d', strtotime($to));

// ===== Branch list =====
$branches = $pdo->query("SELECT id, name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// ===== Total Sales =====
$sqlSales = "SELECT COALESCE(SUM(amount),0) FROM sales WHERE sale_date BETWEEN :from AND :to";
$params = [':from' => $from_dt, ':to' => $to_dt];
if ($branch_id) {
    $sqlSales .= " AND branch_id=:branch";
    $params[':branch'] = $branch_id;
}
$stmt = $pdo->prepare($sqlSales);
$stmt->execute($params);
$totalSales = (float)$stmt->fetchColumn();

// ===== Total Expenses =====
$sqlExp = "SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN :from AND :to";
$paramsExp = [":from" => $from_dt, ":to" => $to_dt];
if ($branch_id) {
    $sqlExp .= " AND branch_id=:branch";
    $paramsExp[':branch'] = $branch_id;
}
$stmt = $pdo->prepare($sqlExp);
$stmt->execute($paramsExp);
$totalExpenses = (float)$stmt->fetchColumn();

// ===== Salary (monthly sum only) =====
$sqlSal = "SELECT COALESCE(SUM(salary),0) FROM staff";
$paramsSal = [];
if ($branch_id) {
    $sqlSal .= " WHERE branch_id=:branch";
    $paramsSal[':branch'] = $branch_id;
}
$stmt = $pdo->prepare($sqlSal);
$stmt->execute($paramsSal);
$totalSalary = (float)$stmt->fetchColumn();

// ===== Net Profit/Loss =====
$net = $totalSales - ($totalExpenses + $totalSalary);
?>


<div class="container-fluid p-4">
    <h4 class="mb-3">📊 Profit / Loss Report (Monthly)</h4>

    <!-- Filter -->
    <form method="GET" class="row g-3 mb-4 align-items-end">
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="from" value="<?= htmlspecialchars($from_dt) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="to" value="<?= htmlspecialchars($to_dt) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-control">
                <option value="">All Branches</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $branch_id == $b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 text-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="report_profit.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm" id="dcard">
                <h6>Total Sales</h6>
                <h4><?= number_format($totalSales, 2) ?> </h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm" id="dcard">
                <h6>Total Expenses</h6>
                <h4><?= number_format($totalExpenses, 2) ?> </h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm" id="dcard">
                <h6>Total Salary</h6>
                <h4><?= number_format($totalSalary, 2) ?> </h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm" id="dcard">
                <h6>Net Profit / Loss</h6>
                <?php if ($net >= 0): ?>
                    <h4 class="text-success"><?= number_format($net, 2) ?> (Profit)</h4>
                <?php else: ?>
                    <h4 class="text-danger"><?= number_format(abs($net), 2) ?> (Loss)</h4>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <div class="card-body">
            <canvas id="monthlyChart" height="100"></canvas>
        </div>
    </div>

    <div class="text-end mt-3">
        <a href="report_profit_export.php?type=excel&from=<?= $from_dt ?>&to=<?= $to_dt ?>&branch_id=<?= $branch_id ?>" class="btn btn-outline-success me-2"> <i class="fa-regular fa-file-excel"></i>
            Export to Excel</a>
        <a href="report_profit_export.php?type=pdf&from=<?= $from_dt ?>&to=<?= $to_dt ?>&branch_id=<?= $branch_id ?>" class="btn btn-outline-danger "><i class="fas fa-file-pdf"></i>
            Export to PDF</a>
    </div>


    <h4 class="mb-3">📊 All Reports</h4>

    <div class="row">
        <!-- Sales Report -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-3" id="dcard">
                <div class="card-body text-center">
                    <h5>Sales Report</h5>
                    <p>View sales by date & branch</p>
                    <a href="report_sales.php" class="btn btn-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Expense Report -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-3" id="dcard">
                <div class="card-body text-center">
                    <h5>Expense Report</h5>
                    <p>Track expenses with categories</p>
                    <a href="report_expense.php" class="btn btn-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Stock Report -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-3" id="dcard">
                <div class="card-body text-center">
                    <h5>Stock Report</h5>
                    <p>Branch-wise stock availability</p>
                    <a href="stock_report.php" class="btn btn-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>

        <!-- Staff Salary -->
        <div class="col-md-3">
            <div class="card shadow-sm mb-3" id="dcard">
                <div class="card-body text-center">
                    <h5>Staff Salary Report</h5>
                    <p>Total staff salaries by branch</p>
                    <a href="staff_salary_report.php" class="btn btn-info btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Sales', 'Expenses', 'Salary', 'Net'],
            datasets: [{
                label: '৳ Amount',
                data: [<?= $totalSales ?>, <?= $totalExpenses ?>, <?= $totalSalary ?>, <?= $net ?>],
                backgroundColor: ['#173831', 'red', 'orange', '#FFCF70']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>



<?php include "../includes/footer.php"; ?>