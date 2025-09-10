<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin', 'manager', 'staff']);
// Start session and check login

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// ==========================
// 1️⃣ Top Summary Cards Data
// ==========================

// Total Sales (this month)
$totalSales = $pdo->query("
    SELECT SUM(amount) 
    FROM sales 
    WHERE MONTH(sale_date) = MONTH(CURRENT_DATE())
    AND YEAR(sale_date) = YEAR(CURRENT_DATE())
")->fetchColumn();

// Total Expenses (this month)
$totalExpenses = $pdo->query("
    SELECT SUM(amount) 
    FROM expenses 
    WHERE MONTH(expense_date) = MONTH(CURRENT_DATE())
    AND YEAR(expense_date) = YEAR(CURRENT_DATE())
")->fetchColumn();

// Net Profit
$netProfit = $totalSales - $totalExpenses;

// Active Branches
$activeBranches = $pdo->query("
    SELECT COUNT(*) 
    FROM branches 
")->fetchColumn();

// =================================
// 2️⃣ Branch-wise Profit Chart Data
// =================================
$branchData = $pdo->query("
    SELECT b.name, 
           SUM(s.amount) - IFNULL(SUM(e.amount),0) AS profit
    FROM branches b
    LEFT JOIN sales s ON s.branch_id = b.id AND MONTH(s.sale_date) = MONTH(CURRENT_DATE()) AND YEAR(s.sale_date) = YEAR(CURRENT_DATE())
    LEFT JOIN expenses e ON e.branch_id = b.id AND MONTH(e.expense_date) = MONTH(CURRENT_DATE()) AND YEAR(e.expense_date) = YEAR(CURRENT_DATE())
    GROUP BY b.id
")->fetchAll(PDO::FETCH_ASSOC);

$branchNames = json_encode(array_column($branchData, 'name'));
$branchProfits = json_encode(array_column($branchData, 'profit'));

// ==========================================
// 3️⃣ Monthly Sales vs Expenses Chart Data
// ==========================================
$months = [];
$salesData = [];
$expenseData = [];

for ($i = 1; $i <= 12; $i++) {
    $months[] = date('M', mktime(0, 0, 0, $i, 1));
    $salesData[] = (float)$pdo->query("SELECT SUM(amount) FROM sales WHERE MONTH(sale_date)=$i")->fetchColumn() ?? 0;
    $expenseData[] = (float)$pdo->query("SELECT SUM(amount) FROM expenses WHERE MONTH(expense_date)=$i")->fetchColumn() ?? 0;
}

// ==========================
// 4️⃣ Recent Sales Table Data
// ==========================
$recentSales = $pdo->query("
    SELECT s.id, b.name AS branch, s.amount, s.sale_date 
    FROM sales s 
    JOIN branches b ON s.branch_id=b.id 
    ORDER BY s.sale_date DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// 5️⃣ Low Stock Alerts Table Data
// ==========================
$lowStock = $pdo->query("SELECT product_name, quantity FROM stock WHERE quantity<=5")->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>




<!-- ========================== -->
<!-- Main Content -->
<!-- ========================== -->
<div class="content">
<?php include "../includes/topbar.php"; ?>
    <!-- ========================== -->
    <!-- Dashboard Cards -->
    <!-- ========================== -->
    <div class="container mt-4">
        <div class="row g-3">
            <!-- Total Sales -->
            <div class="col-md-3">
                <div class="card shadow-sm" id="dcard">
                    <div class="card-body">

                        <h5><i class="fa-solid fa-cart-shopping"></i> Total Sales</h5>
                        <h3>$<?= number_format($totalSales, 2) ?></h3>
                    </div>
                </div>
            </div>
            <!-- Total Expenses -->
            <div class="col-md-3">
                <div class="card shadow-sm" id="dcard">
                    <div class="card-body">
                        <h5><i class="fa-solid fa-money-bill-trend-up"></i> Total Expenses</h5>
                        <h3>$<?= number_format($totalExpenses, 2) ?></h3>
                    </div>
                </div>
            </div>
            <!-- Net Profit -->
            <div class="col-md-3">
                <div class="card shadow-sm" id="dcard">
                    <div class="card-body">
                        <h5><i class="fa-solid fa-chart-line"></i> Net Profit</h5>
                        <h3>$<?= number_format($netProfit, 2) ?></h3>
                    </div>
                </div>
            </div>
            <!-- Active Branches -->
            <div class="col-md-3">
                <div class="card  shadow-sm" id="dcard">
                    <div class="card-body">
                        <h5><i class="fa-solid fa-code-branch"></i> Active Branches</h5>
                        <h3><?= $activeBranches ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================== -->
        <!-- Branch-wise Profit Chart -->
        <!-- ========================== -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5>Branch-wise Profit (This Month)</h5>
                <canvas id="branchProfitChart"></canvas>
            </div>
        </div>

        <!-- ========================== -->
        <!-- Monthly Sales vs Expenses Chart -->
        <!-- ========================== -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5>Monthly Sales vs Expenses</h5>
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- ========================== -->
        <!-- Recent Sales Table -->
        <!-- ========================== -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5>Recent Sales</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Branch</th>
                                <th>Amount ($)</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                                <tr>
                                    <td><?= $sale['id'] ?></td>
                                    <td><?= $sale['branch'] ?></td>
                                    <td><?= number_format($sale['amount'], 2) ?></td>
                                    <td><?= $sale['sale_date'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================== -->
        <!-- Low Stock Alerts Table -->
        <!-- ========================== -->
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h5>Low Stock Alerts</h5>
                <div class="table-responsive">
                    <table class="table table-danger table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lowStock as $item): ?>
                                <tr>
                                    <td><?= $item['product_name'] ?></td>
                                    <td><?= $item['stock_qty'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>