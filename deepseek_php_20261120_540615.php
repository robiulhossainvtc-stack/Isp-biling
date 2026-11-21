<?php
require 'includes/auth.php';
require_admin();
require 'includes/header.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")
        ->execute([
            trim($_POST['name']),
            trim($_POST['email']),
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['role']
        ]);
    header('Location: staff.php'); exit;
}
$users = $pdo->query("SELECT id,name,email,role FROM users ORDER BY id")->fetchAll();
?>
<h2>Staff Management</h2>
<form method="post" class="card form-grid">
  <input name="name" placeholder="নাম" required>
  <input name="email" type="email" placeholder="Email" required>
  <input name="password" type="password" placeholder="Password" required>
  <select name="role"><option value="staff">Staff</option><option value="admin">Admin</option></select>
  <button type="submit">যোগ করুন</button>
</form>
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
<?php foreach ($users as $u): ?>
<tr><td><?= $u['id'] ?></td><td><?= e($u['name']) ?></td>
    <td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td></tr>
<?php endforeach; ?>
</table>
<?php require 'includes/footer.php'; ?>