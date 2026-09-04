<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['student']);
$user = current_user();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Student dashboard</title>
    <link rel="stylesheet" href="/app/assets/css/app.css">
</head>
<body class="mc-bg">
<main class="mc-container">
    <h1>Welcome, <?php echo esc($user['full_name']); ?></h1>
    <p>Role: <?php echo esc($user['role']); ?></p>
    <p><a href="/app/logout.php">Sign out</a></p>
    <p><a href="/">Back to site</a></p>
</main>
</body>
</html>
