<?php
require_once __DIR__ . '/../includes/student.php';
require_role(['admin','super_admin']);
$user = current_user();
$applications = $pdo->query("SELECT a.id,a.status,a.intended_intake,a.submitted_at,u.first_name,u.last_name,u.email,c.name college_name,p.name programme_name FROM applications a JOIN students s ON s.id=a.student_id JOIN users u ON u.id=s.user_id LEFT JOIN colleges c ON c.id=a.college_id LEFT JOIN programmes p ON p.id=a.programme_id WHERE a.status<>'draft' ORDER BY a.submitted_at DESC, a.created_at DESC LIMIT 100")->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FZ8QPG3B9T"></script>
    <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'G-FZ8QPG3B9T');</script>
    <title>Admin dashboard</title>
    <link rel="stylesheet" href="<?= esc(app_url('assets/css/app.css')) ?>">
</head>
<body class="medcon-app mc-role-body">
<header class="mc-role-topbar"><a href="<?= esc(site_url()) ?>" class="mc-portal-logo"><img src="<?= esc(site_url('img/logo.svg')) ?>" alt="Medcon"><span>Admin portal</span></a><nav class="mc-admin-nav" aria-label="Admin portal"><a href="<?= esc(app_url('admin/dashboard.php')) ?>" aria-current="page">Applications</a><a href="<?= esc(app_url('admin/leads.php')) ?>">Eligibility leads</a></nav><div class="mc-portal-user"><span><?php echo esc($user['full_name']); ?></span><a href="<?= esc(app_url('logout.php')) ?>">Sign out</a></div></header>
<main class="mc-role-main">
    <div class="mc-portal-heading"><div><span class="mc-kicker">Admissions operations</span><h1>Applications</h1><p>Review submitted student applications and their current position.</p></div></div>
    <section class="mc-panel"><h2>Student applications</h2>
    <?php if ($applications): ?><div class="mc-table-wrap"><table><thead><tr><th>Student</th><th>College and programme</th><th>Intake</th><th>Status</th><th>Submitted</th><th></th></tr></thead><tbody><?php foreach ($applications as $application): ?><tr><td><strong><?= esc($application['first_name'].' '.$application['last_name']) ?></strong><br><small><?= esc($application['email']) ?></small></td><td><?= esc($application['college_name'] ?: 'Not selected') ?><br><small><?= esc($application['programme_name'] ?: 'Not selected') ?></small></td><td><?= esc($application['intended_intake'] ?: 'Not supplied') ?></td><td><span class="mc-status-pill"><?= esc(application_status_label($application['status'])) ?></span></td><td><?= $application['submitted_at'] ? esc(date('j M Y', strtotime($application['submitted_at']))) : '—' ?></td><td><a class="mc-arrow-link" href="<?= esc(app_url('admin/application.php?id=' . $application['id'])) ?>">Review →</a></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p>No submitted applications yet.</p><?php endif; ?>
    </section>
</main>
</body>
</html>
