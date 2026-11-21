<?php
require 'includes/header.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $invId = (int)$_POST['invoice_id'];
    $amount = (float)$_POST['amount'];

    $pdo->beginTransaction();
    $pdo->prepare("INSERT INTO payments (invoice_id,amount,method,paid_at,received_by,note)
                   VALUES (?,?,?,?,?,?)")
        ->execute([$invId, $amount, $_POST['method'], $_POST['paid_at'],
                   $_SESSION['user_id'], $_POST['note']]);

    // ইনভয়েস স্ট্যাটাস আপডেট
    $inv = $pdo->prepare("SELECT amount FROM invoices WHERE id=?");
    $inv->execute([$invId]);
    $invAmount = $inv->fetchColumn();
    $paid = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id=?");
    $paid->execute([$invId]);
    $paidTotal = $paid->fetchColumn();
    $status = $paidTotal >= $invAmount ? 'paid' : ($paidTotal > 0 ? 'partial' : 'unpaid');
    $pdo->prepare("UPDATE invoices SET status=? WHERE id=?")->execute([$status, $invId]);
    $pdo->commit();
    header('Location: payments.php'); exit;
}

$unpaid = $pdo->query("SELECT i.id,i.invoice_no,c.name AS cname,i.amount
                       FROM invoices i JOIN customers c ON c.id=i.customer_id
                       WHERE i.status!='paid' ORDER BY i.id DESC")->fetchAll();

$payments = $pdo->query("SELECT p.*, i.invoice_no, c.name AS cname
                         FROM payments p
                         JOIN invoices i ON i.id=p.invoice_id
                         JOIN customers c ON c.id=i.customer_id
                         ORDER BY p.id DESC")->fetchAll();
?>
<h2>Payment Collection</h2>
<form method="post" class="card form-grid">
  <select name="invoice_id" required>
    <option value="">-- বিল নির্বাচন --</option>
    <?php foreach ($unpaid as $u): ?>
      <option value="<?= $u['id'] ?>">
        <?= e($u['invoice_no']) ?> — <?= e($u['cname']) ?> — <?= money($u['amount']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <input name="amount" type="number" step="0.01" placeholder="পরিমাণ" required>
  <select name="method">
    <option value="cash">Cash</option>
    <option value="bkash">bKash</option>
    <option value="bank">Bank</option>
    <option value="other">Other</option>
  </select>
  <input type="date" name="paid_at" value="<?= date('Y-m-d') ?>" required>
  <input name="note" placeholder="নোট (ঐচ্ছিক)">
  <button type="submit">সংগ্রহ করুন</button>
</form>

<h3>পেমেন্ট রেকর্ড</h3>
<table>
<tr><th>Invoice</th><th>Customer</th><th>Amount</th><th>Method</th><th>Date</th><th>Note</th></tr>
<?php foreach ($payments as $p): ?>
<tr>
  <td><?= e($p['invoice_no']) ?></td>
  <td><?= e($p['cname']) ?></td>
  <td><?= money($p['amount']) ?></td>
  <td><?= e($p['method']) ?></td>
  <td><?= e($p['paid_at']) ?></td>
  <td><?= e($p['note']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php require 'includes/footer.php'; ?>