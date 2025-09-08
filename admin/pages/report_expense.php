<?php
require_once __DIR__ . "/../../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Filter values
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$branch_id = $_GET['branch_id'] ?? '';
$category = $_GET['category'] ?? '';

// Base query
$query = "SELECT e.expense_date, b.name AS branch, e.category, e.amount 
          FROM expenses e 
          JOIN branches b ON e.branch_id = b.id 
          WHERE 1=1";

$params = [];

if ($from && $to) {
    $query .= " AND e.expense_date BETWEEN :from AND :to";
    $params[':from'] = $from;
    $params[':to'] = $to;
}

if ($branch_id) {
    $query .= " AND e.branch_id = :branch";
    $params[':branch'] = $branch_id;
}

if ($category) {
    $query .= " AND e.category = :cat";
    $params[':cat'] = $category;
}

$query .= " ORDER BY e.expense_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all branches for dropdown
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Get unique categories for dropdown
$categories = $pdo->query("SELECT DISTINCT category FROM expenses")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid p-4">
    <h4 class="mb-3"><i class="fa-solid fa-money-bill-trend-up"></i> Expenses Report</h4>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-control">
                <option value="">All</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $branch_id == $b['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-control">
                <option value="">All</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['category'] ?>" <?= $category == $c['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <div class="col-md-12">
           
            <a href="report_expenses_export.php?type=excel&from=<?= $from ?>&to=<?= $to ?>&branch_id=<?= $branch_id ?>&category=<?= $category ?>" class="btn btn-outline-success me-2"> <i class="fa-regular fa-file-excel"></i>
            Export to Excel</a>
            <a href="report_expenses_export.php?type=pdf&from=<?= $from ?>&to=<?= $to ?>&branch_id=<?= $branch_id ?>&category=<?= $category ?>"  class="btn btn-outline-danger "><i class="fas fa-file-pdf"></i>
            Export to PDF</a>
        </div>
    </form>

    <!-- Expenses Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Category</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($expenses): ?>
                        <?php foreach ($expenses as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['expense_date']) ?></td>
                                <td><?= htmlspecialchars($e['branch']) ?></td>
                                <td><?= htmlspecialchars($e['category']) ?></td>
                                <td class="text-end"><?= number_format($e['amount'], 2) ?> ৳</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>