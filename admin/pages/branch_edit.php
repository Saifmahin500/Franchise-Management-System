<?php
include_once("../includes/header.php");
include_once("../includes/sidebar.php");
require_once("../dbConfig.php");

$id = $_GET['id'] ?? 0;

// Fetch branch data
$stmt = $DB_con->prepare("SELECT * FROM branches WHERE id = ?");
$stmt->execute([$id]);
$branch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$branch) {
    echo "<div class='content-wrapper p-4'><h4>Branch not found!</h4></div>";
    include_once("../includes/footer.php");
    exit;
}

// Update branch
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $manager = $_POST['manager'];
    $contact = $_POST['contact'];

    $stmt = $DB_con->prepare("UPDATE branches SET name=?, location=?, manager_name=?, contact_number=? WHERE id=?");
    $stmt->execute([$name, $location, $manager, $contact, $id]);

    header("Location: branches.php?updated=1");
    exit;
}
?>

<div class="content-wrapper p-4">
    <h2>Edit Branch</h2>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Branch Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($branch['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($branch['location']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Manager Name</label>
            <input type="text" name="manager" value="<?= htmlspecialchars($branch['manager_name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" value="<?= htmlspecialchars($branch['contact_number']) ?>" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="branches.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include_once("../includes/footer.php"); ?>