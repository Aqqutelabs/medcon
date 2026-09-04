<?php
$cfg = require __DIR__ . '/config.php';

try {
    $dsn = "mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Display detailed error in development; in production remove the error details
    $isDev = strpos($_SERVER['SCRIPT_NAME'] ?? '', 'localhost') !== false || $_SERVER['HTTP_HOST'] === 'localhost';
    if ($isDev || isset($_GET['debug'])) {
        echo '<pre>Database connection failed.</pre>';
        echo '<pre>Error: ' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>DSN: ' . htmlspecialchars($dsn) . '</pre>';
    } else {
        echo 'Database connection failed.';
    }
    exit;
}
