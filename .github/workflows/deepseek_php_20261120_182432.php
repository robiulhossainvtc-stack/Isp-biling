<?php require 'includes/header.php'; ?>

<h2>Dashboard</h2>
<div class="stats">
  <?php
  $totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
  $activeCustomers = $pdo->query("SELECT COUNT(*) FROM customers WHERE status='active'")->fetchColumn();
  $unpaidAmount = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM invoices WHERE status!='paid'")->fetchColumn();
  $monthIncome = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE DATE_FORMAT(paid_at,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')")->fetchColumn();
  ?>
  <div class="stat"><span>Total Customers</span><b><?= $totalCustomers ?></b></div>
  <div class="stat"><span>Active</span><b><?= $activeCustomers ?></b></div>
  <div class="stat warn"><span>Outstanding</span><b><?= money($unpaidAmount) ?></b></div>
  <div class="stat ok"><span>This Month Income</span><b><?= money($monthIncome) ?></b></div>
</div>

<h3>সাম্প্রতিক বিলসমূহ</h3>
<table>
<tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Status</th><th>Due</th></tr>
<?php
$rows = $pdo->query("SELECT i.*, c.name AS cname FROM invoices i
                     JOIN customers c ON c.id=i.customer_id
                     ORDER BY i.id DESC LIMIT 10")->fetchAll();
foreach ($rows as $r): ?>
<tr>
  <td><?= e($r['invoice_no']) ?></td>
  <td><?= e($r['cname']) ?></td>
  <td><?= money($r['amount']) ?></td>
  <td><span class="badge <?= $r['status'] ?>"><?= e($r['status']) ?></span></td>
  <td><?= e($r['due_date']) ?></td>
</tr>
<?php endforeach; ?>
</table>

<?php require 'includes/footer.php'; ?>