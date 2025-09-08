<?php
require_once __DIR__ . "/../../config/db.php";


$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: suppliers.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM suppliers WHERE id=?");
$stmt->execute([$id]);
$supplier = $stmt->fetch();

if (!$supplier) {
    echo "Supplier not found!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    $update = $pdo->prepare("UPDATE suppliers SET name=?, contact=?, address=? WHERE id=?");
    $update->execute([$name, $contact, $address, $id]);

    header("Location: suppliers.php?msg=Supplier updated successfully");
    exit;
}
?>
<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container mt-4">
    <h4>Edit Supplier</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($supplier['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($supplier['contact']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($supplier['address']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="suppliers.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
