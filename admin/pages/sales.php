<?php
require_once __DIR__ . "/../../config/db.php";
session_start();

// Default query: সব sales দেখাবে
$query = "SELECT s.id, s.sale_date, s.amount, b.name 
          FROM sales s
          JOIN branches b ON s.branch_id = b.id 
          ORDER BY s.sale_date DESC";

$params = [];

// যদি filter apply হয়
if (isset($_GET['from_date']) && isset($_GET['to_date']) && $_GET['from_date'] && $_GET['to_date']) {
    $from = $_GET['from_date'];
    $to = $_GET['to_date'];

    $query = "SELECT s.id, s.sale_date, s.amount, b.name 
              FROM sales s
              JOIN branches b ON s.branch_id = b.id
              WHERE DATE(s.sale_date) BETWEEN :from AND :to
              ORDER BY s.sale_date DESC";

    $params = [':from' => $from, ':to' => $to];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="container-fluid">
    <h4 class="mb-4">Sales</h4>
    <!-- Add Sale Button -->
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addSaleModal">
        + Add Sale
    </button>



    <!-- Add Sale Modal -->
    <div class="modal fade" id="addSaleModal" tabindex="-1" aria-labelledby="addSaleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addSaleModalLabel">Add Sale Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <form method="POST" action="add_sale.php">
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">Select Branch</option>
                                <?php
                                $stmt = $pdo->query("SELECT id, name FROM branches ORDER BY name");
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <!-- Date Filter Form -->
    <form method="GET" class="form-inline mb-3">
        <label class="mr-2">From:</label>
        <input type="date" name="from_date" class="form-control mr-2"
            value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">

        <label class="mr-2">To:</label>
        <input type="date" name="to_date" class="form-control mr-2"
            value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="sales.php" class="btn btn-secondary ml-2">Reset</a>
    </form>

    <!-- Sales Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Branch</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sales): ?>
                <?php foreach ($sales as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['sale_date']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= number_format($row['amount'], 2) ?></td>
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

<?php include "../includes/footer.php"; ?>