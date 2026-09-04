<?php
require_once __DIR__ . '/../includes/auth.php';
require_role(['agent']);
$user = current_user();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Agent dashboard</title>
    <link rel="stylesheet" href="/app/assets/css/app.css">
</head>
<body class="mc-bg">
<main class="mc-container">
    <h1>Agent area</h1>
    <p>Signed in as <?php echo esc($user['full_name']); ?></p>
    <p><a href="/app/logout.php">Sign out</a></p>
</main>
</body>
</html>
