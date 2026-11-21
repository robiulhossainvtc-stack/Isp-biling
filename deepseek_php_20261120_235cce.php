<?php
require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $pdo->prepare("INSERT INTO packages (name,speed,price,validity_days) VALUES (?,?,?,?)")
        ->execute([$_POST['name'], $_POST['speed'], $_POST['price'], $_POST['validity_days']]);
    header('Location: packages.php'); exit;
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM packages WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: packages.php'); exit;
}
$packages = $pdo->query("SELECT * FROM packages ORDER BY price")->fetchAll();
?>
<h2>Internet Packages</h2>
<form method="post" class="card form-grid">
  <input name="name" placeholder="প্যাকেজের নাম" required>
  <input name="speed" placeholder="স্পিড (যেমন 10Mbps)">
  <input name="price" type="number" step="0.01" placeholder="মূল্য" required>
  <input name="validity_days" type="number" value="30" placeholder="বৈধতা (দিন)">
  <button type="submit">যোগ করুন</button>
</form>
<table>
<tr><th>Name</th><th>Speed</th><th>Price</th><th>Validity</th><th></th></tr>
<?php foreach ($packages as $p): ?>
<tr>
  <td><?= e($p['name']) ?></td>
  <td><?= e($p['speed']) ?></td>
  <td><?= money($p['price']) ?></td>
  <td><?= $p['validity_days'] ?> days</td>
  <td><a class="btn-danger" href="?delete=<?= $p['id'] ?>" onclick="return confirm('মুছবেন?')">Delete</a></td>
</tr>
<?php endforeach; ?>
</table>
<?php require 'includes/footer.php'; ?>