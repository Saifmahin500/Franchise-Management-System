<?php
require_once __DIR__ . "/../../config/db.php";
session_start();

// ==========================
// 🔔 Alert Messages (Session)
// ==========================
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// ==========================
// 🔴 Handle Delete Request
// ==========================
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $del = $pdo->prepare("DELETE FROM staff WHERE id=?");
    if ($del->execute([$id])) {
        $_SESSION['success'] = "Staff deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete staff.";
    }
    header("Location: staff.php");
    exit;
}

// ==========================
// 🟢 Fetch Staffs
// ==========================
$stmt = $pdo->query("
    SELECT s.*, b.name AS branch_name 
    FROM staff s 
    JOIN branches b ON s.branch_id = b.id 
    ORDER BY s.joining_date DESC
");
$staffs = $stmt->fetchAll();

// ✅ Branch Filter
$branchFilter = $_GET['branch_id'] ?? '';

// Base Query
$query = "
    SELECT s.id, s.name, s.position, s.salary,s.branch_id, s.joining_date, b.name as branch_name 
    FROM staff s
    JOIN branches b ON s.branch_id = b.id
";
$params = [];

// যদি branch select করা থাকে
if ($branchFilter) {
    $query .= " WHERE s.branch_id = ? ";
    $params[] = $branchFilter;
}

$query .= " ORDER BY s.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$staffs = $stmt->fetchAll();

// ✅ Salary Total
$totalSalary = 0;
foreach ($staffs as $st) {
    $totalSalary += $st['salary'];
}

// Branch List (filter dropdown এর জন্য)
$branches = $pdo->query("SELECT * FROM branches")->fetchAll();
?>
<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-4">
        <h2 class="mb-3">👨‍💼 Staff Management</h2>
        <!-- Add Staff Button -->
        <button class="btn btn_b mb-3" data-bs-toggle="modal" data-bs-target="#addStaffModal">➕ Add Staff</button>

    </div>

    <!-- 🔔 Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ✅ Branch Filter Form -->
    <label class="form-label fw-semibold mb-2">Filter by Branch</label>
    <form method="GET" class="mb-3 d-flex" style="max-width:400px;"> 
        <select name="branch_id" class="form-select me-2">
            <option value="">-- All Branches --</option>
            <?php foreach ($branches as $b): ?>
                <option value="<?= $b['id'] ?>" <?= ($branchFilter == $b['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($b['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>



    <!-- Staff Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Salary</th>
                <th>Joining Date</th>
                <th>Branch</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffs as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['position']) ?></td>
                    <td><?= number_format($s['salary']) ?></td>
                    <td><?= date("d M, Y", strtotime($s['joining_date'])) ?></td>
                    <td><?= htmlspecialchars($s['branch_name']) ?></td>
                    <td>
                        <!-- Edit Button -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStaffModal<?= $s['id'] ?>">✏️ Edit</button>

                        <!-- Delete Button -->
                        <a href="staff.php?delete=<?= $s['id'] ?>" onclick="return confirm('Are you sure to delete this staff?')" class="btn btn-sm btn-danger">🗑 Delete</a>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editStaffModal<?= $s['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="staff_update.php">
                                <div class="modal-header">
                                    <h5 class="modal-title">✏️ Edit Staff</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" value="<?= htmlspecialchars($s['name']) ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Position</label>
                                        <input type="text" name="position" value="<?= htmlspecialchars($s['position']) ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Salary</label>
                                        <input type="number" name="salary" value="<?= $s['salary'] ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Joining Date</label>
                                        <input type="date" name="joining_date" value="<?= $s['joining_date'] ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Branch</label>
                                        <select name="branch_id" class="form-control" required>
                                            <?php foreach ($branches as $b): ?>
                                                <option value="<?= $b['id'] ?>" <?= ($s['branch_id'] == $b['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($b['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">💾 Update</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </tbody>
        <!-- ✅ Salary Total Row -->
        <?php if ($staffs): ?>
            <tfoot>
                <tr class="table-secondary">
                    <th colspan="2" class="text-end">Total Salary:</th>
                    <th colspan="4"><?= number_format($totalSalary, 2) ?></th>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="staff_add.php">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Salary</label>
                        <input type="number" name="salary" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-control" required>
                            <option value="">Select Branch</option>
                            <?php
                            $branches = $pdo->query("SELECT * FROM branches ORDER BY name ASC")->fetchAll();
                            foreach ($branches as $b): ?>
                                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">💾 Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>