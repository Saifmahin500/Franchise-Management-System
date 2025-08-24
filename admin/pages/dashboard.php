<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #343a40;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover {
            background: #495057;
        }

        .content {
            flex-grow: 1;
            background: #f8f9fa;
            padding: 20px;
        }

        .topbar {
            background: #fff;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="p-3">Admin Panel</h4>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="sales.php">💰 Sales</a>
        <a href="expenses.php">📉 Expenses</a>
        <a href="stock.php">📦 Stock</a>
        <a href="staff.php">👨‍💼 Staff</a>
        <a href="reports.php">📊 Reports</a>
        <a href="../public/logout.php">🚪 Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Topbar -->
        <div class="topbar">
            <h5>Welcome, Admin</h5>
            <span><?php echo date("d M Y"); ?></span>
        </div>

        <!-- Dashboard Cards -->
        <div class="container mt-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card text-bg-primary shadow-sm">
                        <div class="card-body">
                            <h5>Total Sales</h5>
                            <h3>$25,000</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-danger shadow-sm">
                        <div class="card-body">
                            <h5>Total Expenses</h5>
                            <h3>$10,000</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-success shadow-sm">
                        <div class="card-body">
                            <h5>Net Profit</h5>
                            <h3>$15,000</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-warning shadow-sm">
                        <div class="card-body">
                            <h5>Total Branches</h5>
                            <h3>5</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Placeholder for charts -->
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5>Branch Performance (Chart Here)</h5>
                    <p>Later we will add charts using Chart.js</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>