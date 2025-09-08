<?php
require_once __DIR__ . "/../../config/db.php";

// Branches list for dropdown
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);

$branch_id = $_GET['branch_id'] ?? '';
$params = [];

// Query stock with branch join
$query = "SELECT s.id, s.product_name, s.category, s.quantity, s.reorder_level, b.name AS branch
          FROM stock s
          JOIN branches b ON s.branch_id = b.id
          WHERE 1=1";

if ($branch_id) {
    $query .= " AND s.branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

$query .= " ORDER BY s.product_name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container-fluid px-4">
    <h4 class="mt-4 mb-4">📦 Stock Report</h4>

    <!-- Filter Form -->
    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <select name="branch_id" class="form-control">
                <option value="">-- All Branches --</option>
                <?php foreach ($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($branch_id == $b['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        <div class="col-md-7 text-end">
            <a href="stock_report_export.php?type=excel&branch_id=<?= $branch_id ?>" class="btn btn-outline-success me-2"> <i class="fa-regular fa-file-excel"></i>
            Export to Excel
            </a>
            <a href="stock_report_export.php?type=pdf&branch_id=<?= $branch_id ?>"  class="btn btn-outline-danger "><i class="fas fa-file-pdf"></i>
            Export to PDF
            </a>
        </div>
    </form>

    <!-- Stock Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Branch</th>
                        <th>Quantity</th>
                        <th>Reorder Level</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stocks): ?>
                        <?php foreach ($stocks as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['branch']) ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td><?= $row['reorder_level'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No stock data found!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
