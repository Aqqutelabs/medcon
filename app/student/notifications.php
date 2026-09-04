<?php
require_once __DIR__ . '/../includes/student.php';
$student = require_student($pdo);
$messageStmt = $pdo->prepare('SELECT id, subject, body, read_at, created_at FROM messages WHERE recipient_user_id=? ORDER BY created_at DESC, id DESC');
$messageStmt->execute([$student['user_id']]);
$messages = $messageStmt->fetchAll();
if ($messages) {
    $pdo->prepare('UPDATE messages SET read_at=COALESCE(read_at, ?) WHERE recipient_user_id=? AND read_at IS NULL')->execute([date('Y-m-d H:i:s'), $student['user_id']]);
}
student_portal_start('Notifications', 'notifications', $student);
?>
<section class="mc-portal-heading mc-notification-heading"><div><span class="mc-kicker">Updates & messages</span><h1>Notifications</h1><p>Important application updates and direct messages from the Medcon admissions team.</p></div><span class="mc-one-way-pill"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11V8a5 5 0 0 1 10 0v3M5 11h14v10H5z"/></svg>Admin messages only</span></section>
<section class="mc-notification-shell" aria-label="Notification history">
<div class="mc-notification-top"><div><span class="mc-team-avatar" aria-hidden="true">M</span><div><strong>Medcon Admissions</strong><small>Official student updates</small></div></div><span class="mc-online-label"><i></i> Updates enabled</span></div>
<div class="mc-notification-feed">
<?php if(!$messages): ?><div class="mc-notification-empty"><span aria-hidden="true">✓</span><h2>You’re all caught up</h2><p>Major application updates and messages from the admissions team will appear here.</p></div><?php else: ?>
<?php $lastDate=''; foreach($messages as $message): $dateKey=date('Y-m-d',strtotime($message['created_at'])); if($dateKey!==$lastDate): $lastDate=$dateKey; ?><div class="mc-date-divider"><span><?= $dateKey===date('Y-m-d')?'Today':($dateKey===date('Y-m-d',strtotime('-1 day'))?'Yesterday':esc(date('l, j F Y',strtotime($dateKey)))) ?></span></div><?php endif; ?>
<article class="mc-notification-message<?= $message['read_at']===null?' is-unread':'' ?>"><span class="mc-team-avatar" aria-hidden="true">M</span><div class="mc-message-content"><div class="mc-message-meta"><strong>Medcon Admissions</strong><time datetime="<?= esc(date('c',strtotime($message['created_at']))) ?>"><?= esc(date('g:i A',strtotime($message['created_at']))) ?></time></div><div class="mc-message-bubble"><h2><?= esc($message['subject']) ?></h2><p><?= nl2br(esc($message['body'])) ?></p></div></div></article>
<?php endforeach; endif; ?>
</div>
<footer class="mc-notification-note"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 11V8a5 5 0 0 1 10 0v3M5 11h14v10H5z"/></svg><span>This is a one-way notification channel. Students cannot reply here. For help, use the support links in the sidebar.</span></footer>
</section>
<?php student_portal_end(); ?>
