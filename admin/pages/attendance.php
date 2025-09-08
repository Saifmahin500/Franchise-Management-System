<?php
require_once __DIR__ . "/../../config/db.php";


// Branch list
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Staff list
$staff = $pdo->query("SELECT id, name FROM staff")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['staff_id'];
    $branch_id = $_POST['branch_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO attendance (staff_id, branch_id, date, status) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$staff_id, $branch_id, $date, $status])) {
        $success = "Attendance saved successfully!";
    } else {
        $error = "Failed to save attendance!";
    }
}

// Filter values
$filterBranch = $_GET['branch_id'] ?? '';
$filterDate = $_GET['date'] ?? '';

// Query build
$sql = "SELECT a.id, s.name AS staff_name, b.name AS branch_name, a.date, a.status 
        FROM attendance a
        JOIN staff s ON a.staff_id = s.id
        JOIN branches b ON a.branch_id = b.id
        WHERE 1 ";

$params = [];

if ($filterBranch) {
    $sql .= " AND a.branch_id = ? ";
    $params[] = $filterBranch;
}

if ($filterDate) {
    $sql .= " AND a.date = ? ";
    $params[] = $filterDate;
}

$sql .= " ORDER BY a.date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$attendanceList = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container mt-4">
    <h2 class="mb-3"><i class="fa-solid fa-calendar-check"></i> Add Attendance</h2>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?= ($_GET['msg'] == 'updated') ? "Attendance updated successfully!" : "Attendance deleted successfully!" ?>
        </div>
    <?php elseif (isset($_GET['err'])): ?>
        <div class="alert alert-danger">Something went wrong!</div>
    <?php endif; ?>


    <form method="POST" class="card p-3 shadow-sm">
        <div class="row">
            <div class="col-md-4">
                <label>Staff</label>
                <select name="staff_id" class="form-control" required>
                    <option value="">Select Staff</option>
                    <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Branch</label>
                <select name="branch_id" class="form-control" required>
                    <option value="">Select Branch</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Leave">Leave</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-3">Save Attendance</button>
    </form>

    <div class="container mt-5">
        <h4 class="mb-3">Attendance Records</h4>

        <!-- Filter Form -->
        <form method="GET" class="row mb-5">
            <div class="col-md-4">
                <select name="branch_id" class="form-control">
                    <option value="">All Branches</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= ($filterBranch == $b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>" class="form-control">
            </div>

            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="attendance.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <!-- Attendance Table -->
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Staff</th>
                    <th>Branch</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
                <?php if ($attendanceList): ?>
                    <?php foreach ($attendanceList as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['staff_name']) ?></td>
                            <td><?= htmlspecialchars($row['branch_name']) ?></td>
                            <td><?= $row['date'] ?></td>
                            <td>
                                <?php if ($row['status'] == 'Present'): ?>
                                    <span class="badge bg-success">Present</span>
                                <?php elseif ($row['status'] == 'Absent'): ?>
                                    <span class="badge bg-danger">Absent</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Leave</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $row['id'] ?>"><i class="fa fa-edit"></i></button>

                                <!-- Delete Button -->
                                <a href="attendance_delete.php?id=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure to delete this record?');">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="attendance_edit.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Attendance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                            <div class="mb-3">
                                                <label>Status</label>
                                                <select name="status" class="form-control" required>
                                                    <option value="Present" <?= $row['status'] == "Present" ? "selected" : "" ?>>Present</option>
                                                    <option value="Absent" <?= $row['status'] == "Absent" ? "selected" : "" ?>>Absent</option>
                                                    <option value="Leave" <?= $row['status'] == "Leave" ? "selected" : "" ?>>Leave</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Update</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<?php include "../includes/footer.php"; ?>