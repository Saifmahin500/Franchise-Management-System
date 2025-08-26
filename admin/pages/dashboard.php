<?php
require_once __DIR__ . "/../../config/db.php";

// Start session and check login
session_start();
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background: #265166;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar h4 {
            padding: 20px;
            margin: 0;
            font-size: 1.2rem;
        }

        .sidebar a {
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background: #547C90;
            color: #fff;
        }

        .content {
            flex-grow: 1;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: #fff;
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-title {
            margin-left: 8px;
        }
    </style>
</head>

<body>

    <!-- ========================== -->
    <!-- Sidebar -->
    <!-- ========================== -->
    <div class="sidebar">
        <h4><i class="fa-solid fa-building"></i> Franchise System</h4>
        <hr>
        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> <span class="menu-title">Dashboard</span></a>
        <a href="branches.php"><i class="fa-solid fa-code-branch"></i> <span class="menu-title">Branches</span></a>
        <a href="sales.php"><i class="fa-solid fa-cart-shopping"></i> <span class="menu-title">Sales</span></a>
        <a href="expenses.php"><i class="fa-solid fa-money-bill-trend-up"></i> <span class="menu-title">Expenses</span></a>
        <a href="stock.php"><i class="fa-solid fa-boxes-stacked"></i> <span class="menu-title">Stock</span></a>
        <a href="suppliers.php"><i class="fa-solid fa-truck-field"></i> <span class="menu-title">Suppliers</span></a>
        <a href="staff.php"><i class="fa-solid fa-users"></i> <span class="menu-title">Staff</span></a>
        <a href="attendance.php"><i class="fa-solid fa-calendar-check"></i> <span class="menu-title">Attendance</span></a>
        <a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span class="menu-title">Reports</span></a>
        <a href="settings.php"><i class="fa-solid fa-gear"></i> <span class="menu-title">Settings</span></a>
    </div>

    <!-- ========================== -->
    <!-- Main Content -->
    <!-- ========================== -->
    <div class="content">
        <!-- Topbar -->
        <div class="topbar">
            <h5><i class="fa-solid fa-chart-pie text-primary"></i> Dashboard</h5>
            <div class="profile">
                <!-- Notification -->
                <a href="#" class="text-dark position-relative">
                    <i class="fa-solid fa-bell fa-lg"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </a>
                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/35" class="rounded-circle me-2" alt="profile">
                        <span>Admin</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ========================== -->
        <!-- Dashboard Cards -->
        <!-- ========================== -->
        <div class="container mt-4">
            <div class="row g-3">
                <!-- Total Sales -->
                <div class="col-md-3">
                    <div class="card text-bg-primary shadow-sm">
                        <div class="card-body">
                            <h5>Total Sales</h5>
                            <h3>$<?= number_format($totalSales, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Total Expenses -->
                <div class="col-md-3">
                    <div class="card text-bg-danger shadow-sm">
                        <div class="card-body">
                            <h5>Total Expenses</h5>
                            <h3>$<?= number_format($totalExpenses, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Net Profit -->
                <div class="col-md-3">
                    <div class="card text-bg-success shadow-sm">
                        <div class="card-body">
                            <h5>Net Profit</h5>
                            <h3>$<?= number_format($netProfit, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <!-- Active Branches -->
                <div class="col-md-3">
                    <div class="card text-bg-warning shadow-sm">
                        <div class="card-body">
                            <h5>Active Branches</h5>
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

    <!-- ========================== -->
    <!-- JS Scripts -->
    <!-- ========================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Branch-wise Profit Chart
        const ctx1 = document.getElementById('branchProfitChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= $branchNames ?>,
                datasets: [{
                    label: 'Profit',
                    data: <?= $branchProfits ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Monthly Sales vs Expenses Chart
        const months = <?= json_encode($months) ?>;
        const sales = <?= json_encode($salesData) ?>;
        const expenses = <?= json_encode($expenseData) ?>;

        const ctx2 = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                        label: 'Sales',
                        data: sales,
                        borderColor: 'green',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Expenses',
                        data: expenses,
                        borderColor: 'red',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>

</html>