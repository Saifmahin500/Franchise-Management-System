<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['company_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Logo upload
    $logo = null;
    if (!empty($_FILES['logo']['name'])) {
        $targetDir = __DIR__ . "/../../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['logo']['name']);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetFile)) {
            $logo = "uploads/" . $fileName;
        }
    }

    // Check if already settings exists
    $check = $pdo->query("SELECT id FROM company_settings LIMIT 1")->fetch();
    if ($check) {
        $sql = "UPDATE company_settings SET company_name=?, address=?, phone=?, email=?";
        $params = [$name, $address, $phone, $email];

        if ($logo) {
            $sql .= ", logo=?";
            $params[] = $logo;
        }

        $sql .= " WHERE id=?";
        $params[] = $check['id'];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        $stmt = $pdo->prepare("INSERT INTO company_settings (company_name, logo, address, phone, email) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $logo, $address, $phone, $email]);
    }

    $success = "Settings updated successfully!";
}

// Fetch latest settings
$settings = $pdo->query("SELECT * FROM company_settings LIMIT 1")->fetch();
?>

<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>

<div class="container mt-4">
    <h4>Company Settings</h4>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="p-3 border rounded bg-light">
        <div class="form-group mb-3">
            <label>Company Name</label>
            <input type="text" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Logo</label>
            <input type="file" name="logo" class="form-control">
            <?php if (!empty($settings['logo'])): ?>
                <img src="../../<?= $settings['logo'] ?>" alt="Logo" width="100" class="mt-2">
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
