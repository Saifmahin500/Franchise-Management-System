<!-- ========================== -->
<!-- Sidebar -->
<!-- ========================== -->
<?php
// Database connection
require_once __DIR__ . "/../../config/db.php"; // সঠিক path check করো

// Company Info Load
$settings = $pdo->query("SELECT * FROM company_settings LIMIT 1")->fetch();
$role = $_SESSION['user_role'] ?? '';
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
        <a href="dashboard.php"><i class="fa-solid fa-gauge"></i> <span class="menu-title">Dashboard</span></a>

        <?php if ($role === 'admin'): ?>
            <a href="branches.php"><i class="fa-solid fa-code-branch"></i> <span class="menu-title">Branches</span></a>
            <a href="settings.php"><i class="fa-solid fa-gear"></i> <span class="menu-title">Settings</span></a>
        <?php endif; ?>

        <?php if ($role === 'admin' || $role === 'manager'): ?>
            <a href="sales.php"><i class="fa-solid fa-cart-shopping"></i> <span class="menu-title">Sales</span></a>
            <a href="expenses.php"><i class="fa-solid fa-money-bill-trend-up"></i> <span class="menu-title">Expenses</span></a>
            <a href="stock.php"><i class="fa-solid fa-boxes-stacked"></i> <span class="menu-title">Stock</span></a>
            <a href="suppliers.php"><i class="fa-solid fa-truck-field"></i> <span class="menu-title">Suppliers</span></a>
            <a href="staff.php"><i class="fa-solid fa-users"></i> <span class="menu-title">Staff</span></a>
            <a href="reports.php"><i class="fa-solid fa-chart-line"></i> <span class="menu-title">Reports</span></a>
        <?php endif; ?>

        <?php if ($role === 'staff'): ?>
            <a href="attendance.php"><i class="fa-solid fa-calendar-check"></i> <span class="menu-title">My Attendance</span></a>
        <?php endif; ?>
    </div>
</div>