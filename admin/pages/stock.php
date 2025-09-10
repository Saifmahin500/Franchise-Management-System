<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin','manager']);


// Fetch categories for filter dropdown
$categories = $pdo->query("SELECT DISTINCT category FROM stock")->fetchAll(PDO::FETCH_ASSOC);

// Handle selected filter
$selectedCategory = $_GET['category'] ?? '';

// Build query with filter
if ($selectedCategory) {
    $stmt = $pdo->prepare("SELECT * FROM stock WHERE category = :cat ORDER BY created_at DESC");
    $stmt->execute([':cat' => $selectedCategory]);
} else {
    $stmt = $pdo->query("SELECT * FROM stock ORDER BY created_at DESC");
}
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container-fluid mt-4">
    <!-- Top Actions -->
    <div class="d-flex justify-content-between mb-3">
        <h2 class=""><i class="fa-solid fa-boxes-stacked"></i> Stock</h2>
        <button class="btn btn_b" data-bs-toggle="modal" data-bs-target="#addStockModal">
            + Add Stock Entry
        </button>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body py-3">
                    <form method="GET" action="">
                        <label class="form-label fw-semibold mb-2">Filter by Category</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white fw-semibold">Category</span>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option
                                        value="<?= htmlspecialchars($cat['category']) ?>"
                                        <?= ($selectedCategory == $cat['category']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['category']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Stock Table -->
    <div class="card">
        <div class="card-header text-white" style="background-color: #173831; ">
            Stock List
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-success">
                    <tr>
                        <th>Product</th>
                        <th>Branch</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stocks): ?>
                        <?php foreach ($stocks as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']) ?></td>
                                <td><?= htmlspecialchars($row['branch_id']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['quantity']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td>
                                    <a href="stock_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    <a href="stock_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?');"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No stock found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="stock_add.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Stock Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-control" required>
                                <option value="">-- Select Branch --</option>
                                <?php
                                require_once __DIR__ . "/../../config/db.php";
                                $branches = $pdo->query("SELECT id, name FROM branches")->fetchAll();
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
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>

                        <input type="hidden" name="reorder_level" value="10">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . "/../includes/footer.php"; ?>