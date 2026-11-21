<?php
require_once __DIR__ . '/auth.php';
require_login();
$u = current_user();
?>
<!doctype html><html><head><meta charset="utf-8">
<title>ISP Billing</title>
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="topbar">
  <strong>ISP Billing</strong>
  <div class="menu">
    <a href="index.php">Dashboard</a>
    <a href="customers.php">Customers</a>
    <a href="packages.php">Packages</a>
    <a href="areas.php">Areas</a>
    <a href="invoices.php">Invoices</a>
    <a href="payments.php">Payments</a>
    <a href="reports.php">Reports</a>
    <?php if ($u['role']==='admin'): ?><a href="staff.php">Staff</a><?php endif; ?>
    <span class="user"><?= e($u['name']) ?> (<?= e($u['role']) ?>)</span>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>
<main class="container">