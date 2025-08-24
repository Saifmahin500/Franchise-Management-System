<?php
require_once __DIR__ . '/dbConfig.php';
require_once __DIR__ . '/class/class_admin.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $auth = new Admin($DB_con);
    $res = $auth->login($email, $pass);

    if ($res['ok'] ?? false) {
        header('Location: pages/dashboard.php');
        exit;
    } else {
        $err = $res['err'] ?? 'Login failed';
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | Franchise Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-3 text-center">Franchise Management</h4>
                        <?php if ($err): ?>
                            <div class="alert alert-danger py-2"><?= htmlspecialchars($err) ?></div>
                        <?php endif; ?>
                        <form method="post" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input name="password" type="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted mt-3 small">© <?= date('Y') ?> Franchise Management</p>
            </div>
        </div>
    </div>
</body>

</html>