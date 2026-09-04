<?php
// Simple helper to create an admin user (development only)
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (php_sapi_name() === 'cli') {
    $email = $argv[1] ?? null;
    $password = $argv[2] ?? null;
} else {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
}

if (!$email || !$password) {
    echo "Usage: php create_admin.php admin@example.com secret\n";
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo '<form method="post"><input name="email" placeholder="email"><input name="password" placeholder="password"><button>Create</button></form>';
    }
    exit;
}

$pw = password_hash($password, PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');
$stmt = $pdo->prepare('INSERT INTO users (role, first_name, last_name, email, password_hash, status, email_verified, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute(['admin','Admin','User',$email,$pw,'active',1,$now,$now]);
echo "Created admin: $email\n";
