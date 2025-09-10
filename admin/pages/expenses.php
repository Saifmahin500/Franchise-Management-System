<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin','manager']);

// Default filter values
$from = $_GET['from'] ?? date('Y-m-01'); // first day of this month
$to   = $_GET['to'] ?? date('Y-m-t');   // last day of this month
$category = $_GET['category'] ?? '';

// Fetch categories from DB
$categories = $pdo->query("SELECT DISTINCT category FROM expenses ORDER BY category")
    ->fetchAll(PDO::FETCH_COLUMN);

// Build Query
$sql = "SELECT e.id, e.expense_date, b.name AS branch, e.category, e.amount
        FROM expenses e
        JOIN branches b ON e.branch_id = b.id
        WHERE e.expense_date BETWEEN :from AND :to";

$params = [
    ':from' => $from,
    ':to'   => $to
];

if ($category !== '') {
    $sql .= " AND e.category = :category";
    $params[':category'] = $category;
}

$sql .= " ORDER BY e.expense_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between mb-2">
        <h2><i class="fa-solid fa-money-bill-trend-up"></i> Expenses</h2>
        <!-- Add Expense Button -->
        <button class="btn btn_b" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="fas fa-plus"></i> Add Expense
        </button>
    </div>

    <!-- ========================== -->
    <!-- Step 1: Filter Form -->
    <!-- ========================== -->
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
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="expenses.php" class="btn btn-secondary">
                <i class="fas fa-sync"></i> Reset
            </a>
        </div>
    </form>

    <!-- ========================== -->
    <!-- Step 2: Expenses Table -->
    <!-- ========================== -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Date</th>
                    <th>Branch</th>
                    <th>Category</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($expenses): ?>
                    <?php foreach ($expenses as $exp): ?>
                        <tr>
                            <td><?= htmlspecialchars($exp['expense_date']) ?></td>
                            <td><?= htmlspecialchars($exp['branch']) ?></td>
                            <td><?= htmlspecialchars($exp['category']) ?></td>
                            <td><?= number_format($exp['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No expenses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ========================== -->
    <!-- Add Expense Button & Export -->
    <!-- ========================== -->


    <!-- Export Buttons -->
    <div class="d-flex justify-content-end mb-3">
        <a href="expenses_export.php?type=excel&from=<?= $from ?>&to=<?= $to ?>&category=<?= $category ?>" class="btn btn-outline-success me-2"><i class="fa-regular fa-file-excel"></i>
            Export to Excel
        </a>
        <a href="expenses_export.php?type=pdf&from=<?= $from ?>&to=<?= $to ?>&category=<?= $category ?>"
            class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>


    <!-- ========================== -->
    <!-- Add Expense Modal -->
    <!-- ========================== -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_expense.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                <?php
                                $branches = $pdo->query("SELECT id, name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($branches as $b) {
                                    echo "<option value='{$b['id']}'>{$b['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>