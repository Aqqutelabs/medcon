<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/admin-layout.php';
require_role(['admin','super_admin']);
$user = current_user();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) $errors[] = 'Your session expired. Refresh and try again.';
    $agentId = filter_input(INPUT_POST, 'agent_id', FILTER_VALIDATE_INT);
    $status = $_POST['approval_status'] ?? '';
    if (!$agentId || !in_array($status, ['pending','approved','rejected','suspended'], true)) $errors[] = 'Choose a valid agent status.';
    if (!$errors) {
        $now = date('Y-m-d H:i:s');
        $pdo->prepare('UPDATE agents SET approval_status=?,approved_by=?,approved_at=?,updated_at=? WHERE id=?')->execute([$status,$status === 'approved' ? $user['id'] : null,$status === 'approved' ? $now : null,$now,$agentId]);
    }
}
$agents = $pdo->query('SELECT a.*,u.first_name,u.last_name,u.email,u.phone FROM agents a JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC')->fetchAll();
?>
<!doctype html><html lang="en"><head><?php require_once dirname(__DIR__, 2) . '/includes/analytics.php'; ?><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Agents | Medcon Admin</title><link rel="stylesheet" href="<?= esc(app_url('assets/css/app.css')) ?>"></head>
<body class="medcon-app mc-role-body"><?php admin_portal_header($user, 'agents'); ?>
<main class="mc-role-main"><div class="mc-portal-heading"><div><span class="mc-kicker">Partnership operations</span><h1>Agent applications</h1><p>Approve agents before they can enter the portal or receive referrals.</p></div></div>
<?php if($errors): ?><div class="mc-errors" role="alert"><?php foreach($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?>
<section class="mc-panel"><div class="mc-table-wrap"><table><thead><tr><th>Agent</th><th>Organization</th><th>Code / link</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach($agents as $agent): ?><tr><td><strong><?= esc($agent['first_name'].' '.$agent['last_name']) ?></strong><br><small><?= esc($agent['email']) ?></small></td><td><?= esc($agent['organization_name']?:'Not supplied') ?><br><small><?= esc($agent['city']?:'') ?></small></td><td><?= esc($agent['referral_code']) ?><br><small>/a/<?= esc($agent['referral_slug']) ?></small></td><td><span class="mc-status-pill"><?= esc(ucfirst($agent['approval_status'])) ?></span></td><td><a class="mc-arrow-link" href="<?= esc(app_url('admin/agent.php?id='.(int)$agent['id'])) ?>">Details</a><form method="post"><?= csrf_field() ?><input type="hidden" name="agent_id" value="<?= (int)$agent['id'] ?>"><select name="approval_status"><option value="pending"<?= $agent['approval_status']==='pending'?' selected':'' ?>>Pending</option><option value="approved"<?= $agent['approval_status']==='approved'?' selected':'' ?>>Approved</option><option value="rejected"<?= $agent['approval_status']==='rejected'?' selected':'' ?>>Rejected</option><option value="suspended"<?= $agent['approval_status']==='suspended'?' selected':'' ?>>Suspended</option></select><button class="mc-btn-secondary" type="submit">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div></section></main>
<?php admin_portal_footer(); ?></body></html>
