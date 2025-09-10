<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";

// Company Info Load
$settings = $pdo->query("SELECT * FROM company_settings LIMIT 1")->fetch();


?>


<div class="sidebar p-3" style="min-height:100vh;">
    <div class="text-center mb-4">
        <?php if (!empty($settings['logo'])): ?>
            <img src="../../<?= $settings['logo'] ?>" alt="Logo" width="80" class="rounded-circle mb-2">
        <?php else: ?>
            <img src="../../assets/default-logo.png" alt="Logo" width="80" class="rounded-circle mb-2">
        <?php endif; ?>
        <h5><?= htmlspecialchars($settings['company_name'] ?? "My Company") ?></h5>
    </div>

    <div>
        <hr>

        <?php if (isAdmin()): ?>
            <!-- Admin সব দেখতে পাবে -->
            <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="branches.php"><i class="fa-solid fa-code-branch"></i> Branches</a>
            <a href="sales.php"><i class="fa-solid fa-cart-shopping"></i> Sales</a>
            <a href="expenses.php"><i class="fa-solid fa-money-bill-trend-up"></i> Expenses</a>
            <a href="stock.php"><i class="fa-solid fa-boxes-stacked"></i> Stock</a>
            <a href="suppliers.php"><i class="fa-solid fa-truck-field"></i> Suppliers</a>
            <a href="staff.php"><i class="fa-solid fa-users"></i> Staff</a>
            <a href="attendance.php"><i class="fa-solid fa-calendar-check"></i> Attendance</a>
            <a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
            <!-- <a href="users.php"><i class="fa-solid fa-gear"></i> Add Users</a> -->

        <?php elseif (isManager()): ?>
            <!-- Manager → কিছু restricted -->
            <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="sales.php"><i class="fa-solid fa-cart-shopping"></i> Sales</a>
            <a href="expenses.php"><i class="fa-solid fa-money-bill-trend-up"></i> Expenses</a>
            <a href="stock.php"><i class="fa-solid fa-boxes-stacked"></i> Stock</a>
            <a href="staff.php"><i class="fa-solid fa-users"></i> Staff</a>
            <a href="reports.php"><i class="fa-solid fa-chart-line"></i> Reports</a>

        <?php elseif (isStaff()): ?>
            <!-- Staff → শুধু Attendance -->
            <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
            <a href="attendance.php"><i class="fa-solid fa-calendar-check"></i> Attendance</a>
        <?php endif; ?>
        <a class="dropdown-item" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>