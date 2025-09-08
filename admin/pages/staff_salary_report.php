<?php
require_once __DIR__ . "/../../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Branch list for filter
$branches = $pdo->query("SELECT id, name FROM branches")->fetchAll(PDO::FETCH_ASSOC);

// Filter
$branch_id = $_GET['branch_id'] ?? '';
$where = "";
$params = [];

if ($branch_id) {
    $where = " WHERE st.branch_id = :branch_id";
    $params[':branch_id'] = $branch_id;
}

$query = "SELECT st.id, st.name, st.position, st.salary, st.joining_date, b.name as branch
          FROM staff st 
          JOIN branches b ON st.branch_id = b.id
          $where
          ORDER BY st.name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid p-4">
    <h4 class="mb-4"><i class="fa-solid fa-users"></i> Staff Salary Report</h4>

    <!-- Filter -->
    <form method="GET" class="row mb-4">
        <div class="col-md-3">
            <select name="branch_id" class="form-control">
                <option value="">All Branches</option>
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
     
    </form>
    <div class="col-md-4 text-right mb-2">
            <a href="staff_salary_report_export.php?type=excel&branch_id=<?= $branch_id ?>" class="btn btn-outline-success me-2"> <i class="fa-regular fa-file-excel"></i>
            Export to Excel</a>
            <a href="staff_salary_report_export.php?type=pdf&branch_id=<?= $branch_id ?>"  class="btn btn-outline-danger "><i class="fas fa-file-pdf"></i>
            Export to PDF</a>
        </div>

    <!-- Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Branch</th>
                <th>Salary</th>
                <th>Joining Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($staffs as $s):
                $total += $s['salary'];
            ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['position']) ?></td>
                    <td><?= htmlspecialchars($s['branch']) ?></td>
                    <td><?= number_format($s['salary'], 2) ?></td>
                    <td><?= htmlspecialchars($s['joining_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Salary</th>
                <th colspan="2"><?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<?php include "../includes/footer.php"; ?>