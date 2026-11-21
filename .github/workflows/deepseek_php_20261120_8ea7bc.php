<?php
require 'includes/header.php';

$month = $_GET['month'] ?? date('Y-m');
$income = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE DATE_FORMAT(paid_at,'%Y-%m')=?");
$income->execute([$month]);
$income = $income->fetchColumn();

$billed = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM invoices WHERE month=?");
$billed->execute([$month]);
$billed = $billed->fetchColumn();

$paidCount = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE month=? AND status='paid'");
$paidCount->execute([$month]);
$paidCount = $paidCount->fetchColumn();

$unpaidCount = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE month=? AND status!='paid'");
$unpaidCount->execute([$month]);
$unpaidCount = $unpaidCount->fetchColumn();
?>
<h2>Reports & Analytics</h2>
<form method="get" class="card inline-form">
  <input type="month" name="month" value="<?= e($month) ?>">
  <button type="submit">দেখুন</button>
</form>

<div class="stats">
  <div class="stat"><span>Billed (<?= e($month) ?>)</span><b><?= money($billed) ?></b></div>
  <div class="stat ok"><span>Collected</span><b><?= money($income) ?></b></div>
  <div class="stat warn"><span>Outstanding</span><b><?= money($billed - $income) ?></b></div>
  <div class="stat"><span>Paid / Unpaid</span><b><?= $paidCount ?> / <?= $unpaidCount ?></b></div>
</div>

<h3>এরিয়া অনুযায়ী গ্রাহক সংখ্যা</h3>
<table>
<tr><th>Area</th><th>Customers</th></tr>
<?php
$rows = $pdo->query("SELECT a.name, COUNT(c.id) AS total
                     FROM areas a LEFT JOIN customers c ON c.area_id=a.id
                     GROUP BY a.id ORDER BY total DESC")->fetchAll();
foreach ($rows as $r): ?>
<tr><td><?= e($r['name']) ?></td><td><?= $r['total'] ?></td></tr>
<?php endforeach; ?>
</table>

<?php require 'includes/footer.php'; ?>