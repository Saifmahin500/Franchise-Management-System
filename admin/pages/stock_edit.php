<?php
require_once __DIR__ . "/../../config/db.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: stock.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM stock WHERE id=?");
$stmt->execute([$id]);
$stock = $stmt->fetch();

if (!$stock) {
    echo "<div class='alert alert-danger text-center mt-4'>❌ Stock not found!</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = $_POST['product_name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $reorder = $_POST['reorder_level'];

    $update = $pdo->prepare("UPDATE stock 
        SET product_name=?, category=?, quantity=?, reorder_level=? WHERE id=?");
    $update->execute([$product, $category, $quantity, $reorder, $id]);

    header("Location: stock.php");
    exit;
}
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center">
                    <h4>✏️ Edit Stock</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" class="needs-validation" novalidate>

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name"
                                value="<?= htmlspecialchars($stock['product_name']) ?>"
                                class="form-control" required>
                            <div class="invalid-feedback">Please enter product name.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category"
                                value="<?= htmlspecialchars($stock['category']) ?>"
                                class="form-control" required>
                            <div class="invalid-feedback">Please enter category.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity"
                                value="<?= $stock['quantity'] ?>"
                                class="form-control" required>
                            <div class="invalid-feedback">Please enter quantity.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reorder Level</label>
                            <input type="number" name="reorder_level"
                                value="<?= $stock['reorder_level'] ?>"
                                class="form-control" required>
                            <div class="invalid-feedback">Please enter reorder level.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="stock.php" class="btn btn-secondary">
                                ⬅ Back
                            </a>
                            <button type="submit" class="btn btn-success">
                                ✅ Update Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bootstrap form validation
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })();
</script>

<?php include __DIR__ . "/../includes/footer.php"; ?>