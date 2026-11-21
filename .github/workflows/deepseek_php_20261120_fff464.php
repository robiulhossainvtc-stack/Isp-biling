<?php
require 'includes/header.php';

// স্বয়ংক্রিয় বিল তৈরি: নির্দিষ্ট মাসের জন্য সব active গ্রাহকের বিল
if (isset($_POST['generate'])) {
    $month = $_POST['month']; // 2026-11
    $customers = $pdo->query("SELECT c.id, p.price FROM customers c
                              JOIN packages p ON p.id=c.package_id
                              WHERE c.status='active'")->fetchAll();
    $ins = $pdo->prepare("INSERT IGNORE INTO invoices
        (customer_id,invoice_no,amount,month,status,due_date)
        VALUES (?,?,?,?, 'unpaid', ?)");
    $due = date('Y-m-d', strtotime($month . '-10'));
    foreach ($customers as $c) {
        $no = 'INV-' . $month . '-' . $c['id'];
        $ins->execute([$c['id'], $no, $c['price'], $month, $due]);
    }
    header('Location: invoices.php'); exit;
}

$invoices = $pdo->query("SELECT i.*, c.name AS cname FROM invoices i
                         JOIN customers c ON c.id=i.customer_id
                         ORDER BY i.id DESC")->fetchAll();
?>
<h2>Invoices</h2>
<form method="post" class="card inline-form">
  <input type="month" name="month" value="<?= date('Y-m') ?>" required>
  <button type="submit" name="generate">এই মাসের বিল তৈরি করুন</button>
</form>
<table>
<tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Month</th><th>Due</th><th>Status</th></tr>
<?php foreach ($invoices as $i): ?>
<tr>
  <td><?= e($i['invoice_no']) ?></td>
  <td><?= e($i['cname']) ?></td>
  <td><?= money($i['amount']) ?></td>
  <td><?= e($i['month']) ?></td>
  <td><?= e($i['due_date']) ?></td>
  <td><span class="badge <?= $i['status'] ?>"><?= $i['status'] ?></span></td>
</tr>
<?php endforeach; ?>
</table>
<?php require 'includes/footer.php'; ?>