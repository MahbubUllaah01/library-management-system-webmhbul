<?php
// Database connection (edit user/password to match your local MySQL / XAMPP setup)
$pdo = new PDO('mysql:host=localhost;dbname=library_db;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
$msg = ''; $err = '';
