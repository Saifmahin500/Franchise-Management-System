<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin','manager']);


// Fetch suppliers
$stmt = $pdo->query("SELECT * FROM suppliers ORDER BY created_at DESC");
$suppliers = $stmt->fetchAll();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between mb-2">
        <h2><i class="fa-solid fa-truck-field"></i> Suppliers</h2>
        <!-- Add Supplier Button -->
        <button class="btn btn_b mb-3" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="fa fa-plus"></i> Add Supplier
        </button>
    </div>





    <!-- Alert Messages -->
    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <!-- Suppliers Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suppliers as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['contact']) ?></td>
                    <td><?= htmlspecialchars($s['address']) ?></td>
                    <td><?= $s['created_at'] ?></td>
                    <td>
                        <a href="suppliers_edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="suppliers_delete.php?id=<?= $s['id'] ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="suppliers_add.php">
                <div class="modal-header">
                    <h5 class="modal-title">Add Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contact</label>
                        <input type="text" name="contact" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>