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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script>
    <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-FZ8QPG3B9T');</script>
    <title>Agent dashboard</title>
    <link rel="stylesheet" href="<?= esc(app_url('assets/css/app.css')) ?>">
</head>
<body class="medcon-app mc-role-body">
<header class="mc-role-topbar"><a href="<?= esc(site_url()) ?>" class="mc-portal-logo"><img src="<?= esc(site_url('img/logo.svg')) ?>" alt="Medcon"><span>Agent portal</span></a><div class="mc-portal-user"><span><?php echo esc($user['full_name']); ?></span><a href="<?= esc(app_url('logout.php')) ?>">Sign out</a></div></header>
<main class="mc-role-main"><div class="mc-portal-heading"><div><span class="mc-kicker">Agent workspace</span><h1>Your students</h1><p>Referred students, applications and outstanding actions will appear here.</p></div></div><section class="mc-panel mc-empty-state"><h2>No student records assigned</h2><p>Assigned student activity will appear in this workspace.</p></section>
</main>
</body>
</html>
