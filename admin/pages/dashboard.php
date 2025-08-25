<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

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

    <!-- Sidebar -->
    <div class="sidebar">
        <h4><i class="fa-solid fa-building"></i> Franchise System</h4> <hr>
        
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

    <!-- Content -->
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

        <!-- Dashboard Content -->
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