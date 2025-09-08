<?php
require_once __DIR__ . "/../../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Filters
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$branch_id = $_GET['branch_id'] ?? '';

// Base Query
$query = "SELECT s.sale_date, b.name AS branch, s.amount 
          FROM sales s 
          JOIN branches b ON s.branch_id = b.id 
          WHERE 1=1";

$params = [];

if ($from && $to) {
    $query .= " AND s.sale_date BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;
}

if ($branch_id) {
    $query .= " AND s.branch_id = :branch";
    $params[':branch'] = $branch_id;
}

$query .= " ORDER BY s.sale_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for filter
$branches = $pdo->query("SELECT * FROM branches")->fetchAll();
?>

<div class="container-fluid mt-4">
    <h4 class="mb-3">📊 Sales Report</h4>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Branch</label>
            <select name="branch_id" class="form-control">
                <option value="">All Branches</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($branch_id == $b['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="report_sales.php" class="btn btn-secondary ms-2">Reset</a>
        </div>
    </form>

    <!-- Export Buttons -->
    <div class="mb-3">
        <a href="report_sales_export.php?type=excel&from=<?= $from ?>&to=<?= $to ?>&branch_id=<?= $branch_id ?>" class="btn btn-outline-success me-2"> <i class="fa-regular fa-file-excel"></i>
        Export to Excel</a>
        <a href="report_sales_export.php?type=pdf&from=<?= $from ?>&to=<?= $to ?>&branch_id=<?= $branch_id ?>"  class="btn btn-outline-danger "><i class="fas fa-file-pdf"></i>
        Export to PDF</a>
    </div>

    <!-- Sales Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sales): ?>
                        <?php foreach ($sales as $s): ?>
                            <tr>
                                <td><?= $s['sale_date'] ?></td>
                                <td><?= htmlspecialchars($s['branch']) ?></td>
                                <td><?= number_format($s['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No sales found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>