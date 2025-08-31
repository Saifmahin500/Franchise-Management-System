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



<div class="container-fluid p-4">
    <div class="d-flex justify-content-between mb-2">
        <h5><i class="fa-solid fa-cart-shopping"></i> Sales</h5>
        <!-- Add Sale Button -->
        <button type="button" class="btn btn_b mb-3" data-bs-toggle="modal" data-bs-target="#addSaleModal">
            + Add Sale
        </button>
    </div>


    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
    <?php endif; ?>





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
    <h5>Date filter</h5>
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="mr-2">From:</label>
            <input type="date" name="from_date" class="form-control mr-2"
                value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <label class="mr-2">To:</label>
            <input type="date" name="to_date" class="form-control mr-2"
                value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn btn-primary mt-3 me-2"><i class="fas fa-filter"></i> Filter</button>
            <a href="sales.php" class="btn btn-secondary ml-2 mt-3"><i class="fas fa-sync"></i> Reset</a>

        </div>

    </form>

    <!-- Sales Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-success">
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

   



        <!-- Export to Excel/PDF option -->

        <div class="d-flex justify-content-end mb-3">
            <a href="sales_export.php?type=excel&from=<?= urlencode($_GET['from_date'] ?? '') ?>&to=<?= urlencode($_GET['to_date'] ?? '') ?>"
                class="btn btn-outline-success me-2"> <i class="fa-regular fa-file-excel"></i>
                Export to Excel
            </a>
            <a href="sales_export.php?type=pdf&from=<?= urlencode($_GET['from_date'] ?? '') ?>&to=<?= urlencode($_GET['to_date'] ?? '') ?>"
                class="btn btn-outline-danger "><i class="fas fa-file-pdf"></i>
                Export to PDF
            </a>
        </div>







    <?php include "../includes/footer.php"; ?>