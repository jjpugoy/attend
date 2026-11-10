<?php
$DB_HOST = 'localhost';
$DB_NAME = 'Attendance';
$DB_USER = 'root';   
$DB_PASS = '';       

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function page_start($title) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>" . h($title) . "</title>
    <style>
      body{font-family:Arial,sans-serif;margin:30px;}
      table{border-collapse:collapse;margin-top:12px;}
      th,td{border:1px solid #444;padding:6px 14px;text-align:left;}
      th{background:#ddd;}
      .links{margin:10px 0;}
      form.box{border:1px solid #888;padding:12px;display:inline-block;margin-top:10px;}
      label{display:block;margin:6px 0 2px;}
      .err{color:#b00;}
    </style></head><body><h2>" . h($title) . "</h2>";
}

function page_end() { echo "</body></html>"; }
