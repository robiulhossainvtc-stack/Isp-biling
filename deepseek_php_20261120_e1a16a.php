<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'isp_billing');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function money($n) { return '৳ ' . number_format($n, 2); }
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }