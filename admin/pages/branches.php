<?php
include_once("../includes/header.php");
include_once("../includes/sidebar.php");
require_once("../dbConfig.php");

// Fetch branches
$stmt = $DB_con->query("SELECT * FROM branches ORDER BY id DESC");
$branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper p-4">
    <h2>Branches</h2>

    <!-- ✅ Success / Error Alerts -->
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            ✅ Branch added successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            ✅ Branch updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['deleted'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            🗑️ Branch deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <a href="branch_add.php" class="btn btn-primary mb-3">➕ Add New Branch</a>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Manager</th>
                <th>Contact</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($branches as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= htmlspecialchars($b['name']) ?></td>
                    <td><?= htmlspecialchars($b['location']) ?></td>
                    <td><?= htmlspecialchars($b['manager_name']) ?></td>
                    <td><?= htmlspecialchars($b['contact_number']) ?></td>
                    <td>
                        <a href="branch_edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                        <a href="branch_delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this branch?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once("../includes/footer.php"); ?>