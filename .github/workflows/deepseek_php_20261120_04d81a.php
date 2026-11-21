<?php
require 'includes/header.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $code = 'C' . str_pad(rand(1,99999), 5, '0', STR_PAD_LEFT);
    $stmt = $pdo->prepare("INSERT INTO customers
        (code,name,phone,address,area_id,package_id,status,join_date)
        VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $code,
        trim($_POST['name']),
        trim($_POST['phone']),
        trim($_POST['address']),
        $_POST['area_id'] ?: null,
        $_POST['package_id'] ?: null,
        $_POST['status'],
        $_POST['join_date'] ?: date('Y-m-d'),
    ]);
    header('Location: customers.php'); exit;
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM customers WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: customers.php'); exit;
}

$areas = $pdo->query("SELECT * FROM areas ORDER BY name")->fetchAll();
$packages = $pdo->query("SELECT * FROM packages ORDER BY price")->fetchAll();
$customers = $pdo->query("SELECT c.*, a.name AS area, p.name AS package
                          FROM customers c
                          LEFT JOIN areas a ON a.id=c.area_id
                          LEFT JOIN packages p ON p.id=c.package_id
                          ORDER BY c.id DESC")->fetchAll();
?>
<h2>Customer Management</h2>

<form method="post" class="card form-grid">
  <input name="name" placeholder="নাম" required>
  <input name="phone" placeholder="মোবাইল">
  <input name="address" placeholder="ঠিকানা">
  <select name="area_id"><option value="">-- এরিয়া --</option>
    <?php foreach ($areas as $a): ?>
      <option value="<?= $a['id'] ?>"><?= e($a['name']) ?></option>
    <?php endforeach; ?></select>
  <select name="package_id"><option value="">-- প্যাকেজ --</option>
    <?php foreach ($packages as $p): ?>
      <option value="<?= $p['id'] ?>"><?= e($p['name']) ?> (<?= money($p['price']) ?>)</option>
    <?php endforeach; ?></select>
  <select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select>
  <input type="date" name="join_date">
  <button type="submit">যোগ করুন</button>
</form>

<table>
<tr><th>Code</th><th>Name</th><th>Phone</th><th>Area</th><th>Package</th><th>Status</th><th></th></tr>
<?php foreach ($customers as $c): ?>
<tr>
  <td><?= e($c['code']) ?></td>
  <td><?= e($c['name']) ?></td>
  <td><?= e($c['phone']) ?></td>
  <td><?= e($c['area']) ?></td>
  <td><?= e($c['package']) ?></td>
  <td><span class="badge <?= $c['status'] ?>"><?= $c['status'] ?></span></td>
  <td><a class="btn-danger" href="?delete=<?= $c['id'] ?>"
        onclick="return confirm('মুছবেন?')">Delete</a></td>
</tr>
<?php endforeach; ?>
</table>

<?php require 'includes/footer.php'; ?>