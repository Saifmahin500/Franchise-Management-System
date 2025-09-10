<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/../includes/auth.php";
requireRole(['admin','manager']);

// fetch users
$stmt = $pdo->query("SELECT u.*, b.name AS branch_name FROM users u LEFT JOIN branches b ON u.id=b.id ORDER BY u.id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
$branches = $pdo->query("SELECT id,name FROM branches ORDER BY name")->fetchAll();
?>
<?php include "../includes/header.php"; ?>
<?php include "../includes/sidebar.php"; ?>


<div class="container">
  <h4>Users</h4>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">+ Add User</button>

  <table class="table mt-3">
    <thead><tr><th>Username</th><th>Name</th><th>Role</th><th>Branch</th><th>Action</th></tr></thead>
    <tbody>
      <?php foreach($users as $u): ?>
      <tr>
        <td><?=htmlspecialchars($u['username'])?></td>
        <td><?=htmlspecialchars($u['role'])?></td>
        <td><?=htmlspecialchars($u['branch_name'])?></td>
        <td>
          <a href="user_edit.php?id=<?=$u['id']?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="user_delete.php?id=<?=$u['id']?>" onclick="return confirm('Delete?')" class="btn btn-sm btn-danger">Delete</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="user_add.php" class="modal-content">
      <div class="modal-header"><h5>Add User</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label>Username</label><input name="username" class="form-control" required></div>
        <div class="mb-2"><label>Email</label><input name="email" type="email" class="form-control"></div>
        <div class="mb-2"><label>Password</label><input name="password" type="password" class="form-control" required></div>
        <div class="mb-2">
          <label>Role</label>
          <select name="role" class="form-control" required>
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="staff">Staff</option>
          </select>
        </div>
        <div class="mb-2">
          <label>Branch (for manager/staff)</label>
          <select name="branch_id" class="form-control">
            <option value="">-- None --</option>
            <?php foreach($branches as $b): ?>
              <option value="<?=$b['id']?>"><?=htmlspecialchars($b['name'])?></option>
            <?php endforeach;?>
          </select>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-success">Save</button></div>
    </form>
  </div>
</div>

<?php include "../includes/footer.php"; ?>
