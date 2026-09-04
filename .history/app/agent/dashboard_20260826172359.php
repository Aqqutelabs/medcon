<?php
require_once __DIR__ . '/../includes/auth.php';
$agent = require_approved_agent($pdo);
$user = current_user();
$stmt=$pdo->prepare("SELECT s.id,u.first_name,u.last_name,u.email,u.phone,a.status,a.updated_at FROM students s JOIN users u ON u.id=s.user_id LEFT JOIN applications a ON a.student_id=s.id AND a.id=(SELECT MAX(a2.id) FROM applications a2 WHERE a2.student_id=s.id) WHERE s.agent_id=? ORDER BY s.created_at DESC"); $stmt->execute([(int)$agent['id']]); $students=$stmt->fetchAll();
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
<main class="mc-role-main"><div class="mc-portal-heading"><div><span class="mc-kicker">Agent workspace</span><h1>Your students</h1><p>Code: <strong><?= esc($agent['referral_code']) ?></strong> · Referral link: <strong><?= esc(site_url('a/'.$agent['referral_slug'])) ?></strong></p></div><a class="mc-btn-primary" href="<?= esc(app_url('agent/submit-student.php')) ?>">Register a student</a></div><section class="mc-panel"><h2>Referred students (<?= count($students) ?>)</h2><?php if($students): ?><div class="mc-table-wrap"><table><thead><tr><th>Student</th><th>Email</th><th>Application status</th><th>Last updated</th></tr></thead><tbody><?php foreach($students as $student): ?><tr><td><strong><?= esc($student['first_name'].' '.$student['last_name']) ?></strong></td><td><?= esc($student['email']) ?><br><?= esc($student['phone']?:'No phone') ?></td><td><span class="mc-status-pill"><?= esc(application_status_label($student['status'])) ?></span></td><td><?= $student['updated_at']?esc(date('j M Y',strtotime($student['updated_at']))):'Not started' ?></td></tr><?php endforeach; ?></tbody></table></div><?php else: ?><p>No students have used your code or link yet.</p><?php endif; ?></section>
</main>
<footer class="mc-portal-footer">Crafted by <a href="https://aqqute.com" target="_blank" rel="noopener noreferrer">Aqqute</a></footer>
</body>
</html>
