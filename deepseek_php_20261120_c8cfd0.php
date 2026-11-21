<?php
require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pdo->prepare("INSERT INTO areas (name) VALUES (?)")->execute([trim($_POST['name'])]);
    header('Location: areas.php'); exit;
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM areas WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: areas.php'); exit;
}
$areas = $pdo->query("SELECT a.*, (SELECT COUNT(*) FROM customers c WHERE c.area_id=a.id) AS total
                      FROM areas a ORDER BY a.name")->fetchAll();
?>
<h2>Area Management</h2>
<form method="post" class="card inline-form">
  <input name="name" placeholder="এরিয়ার নাম" required>
  <button type="submit">যোগ করুন</button>
</form>
<table>
<tr><th>Area</th><th>Customers</th><th></th></tr>
<?php foreach ($areas as $a): ?>
<tr>
  <td><?= e($a['name']) ?></td>
  <td><?= $a['total'] ?></td>
  <td><a class="btn-danger" href="?delete=<?= $a['id'] ?>" onclick="return confirm('মুছবেন?')">Delete</a></td>
</tr>
<?php endforeach; ?>
</table>
<?php require 'includes/footer.php'; ?>