<?php
include_once("../includes/header.php");
include_once("../includes/sidebar.php");
require_once("../dbConfig.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $manager = $_POST['manager'];
    $contact = $_POST['contact'];

    $stmt = $DB_con->prepare("INSERT INTO branches (name, location, manager_name, contact_number) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $location, $manager, $contact]);

    header("Location: branches.php?added=1");
    exit;
}
?>

<div class="content-wrapper p-4">
    <h2>Add Branch</h2>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Branch Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Manager Name</label>
            <input type="text" name="manager" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <a href="branches.php" class="btn btn-secondary">Back</a>
    </form>
</div>

<?php include_once("../includes/footer.php"); ?>