<?php
require_once __DIR__ . '/../includes/student.php';
require_once __DIR__ . '/../includes/document-storage.php';
require_once __DIR__ . '/../includes/admin-layout.php';
require_once dirname(__DIR__,2) . '/scripts/mail/NotificationService.php';
require_once dirname(__DIR__,2) . '/scripts/mail/config.php';
require_role(['admin','super_admin']);
$user = current_user();
$applicationId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$applicationId) { http_response_code(404); exit('Application not found.'); }
$errors = [];
$applicationStatuses = ['submitted','received','under_review','documents_pending','approved','rejected','admission_issued','visa_processing','enrolled'];
$documentStatuses = ['approved','rejected','resubmission_required'];
$loadApplication = function () use ($pdo, $applicationId) {
    $stmt = $pdo->prepare("SELECT a.*,s.user_id,u.first_name,u.last_name,u.email,u.phone,u.country,c.name college_name,p.name programme_name FROM applications a JOIN students s ON s.id=a.student_id JOIN users u ON u.id=s.user_id LEFT JOIN colleges c ON c.id=a.college_id LEFT JOIN programmes p ON p.id=a.programme_id WHERE a.id=? AND a.status<>'draft' LIMIT 1");
    $stmt->execute([$applicationId]); return $stmt->fetch();
};
$application = $loadApplication();
if (!$application) { http_response_code(404); exit('Application not found.'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) $errors[] = 'Your session expired. Refresh the page and try again.';
    $action = $_POST['action'] ?? '';
    if (!$errors && $action === 'application_status') {
        $status = $_POST['status'] ?? ''; $note = trim($_POST['note'] ?? '');
        if (!in_array($status, $applicationStatuses, true)) $errors[] = 'Select a valid application status.';
        if (!$errors && $status !== $application['status']) {
            $previousStatus=$application['status'];
            $pdo->beginTransaction();
            try {
                $pdo->prepare('UPDATE applications SET status=?,updated_at=? WHERE id=?')->execute([$status,date('Y-m-d H:i:s'),$applicationId]);
                $body = 'Your application status is now ' . application_status_label($status) . '.' . ($note !== '' ? ' ' . $note : '');
                $pdo->prepare('INSERT INTO messages (sender_user_id,recipient_user_id,application_id,subject,body) VALUES (?,?,?,?,?)')->execute([$user['id'],$application['user_id'],$applicationId,'Application status updated',$body]);
                $pdo->commit();
                $event=$status==='admission_issued'?'application.admission_update':($status==='visa_processing'?'application.visa_update':'application.status_changed');$mailConfig=medcon_mail_config();medcon_notify($pdo,$event,(int)$application['user_id'],$event.':application:'.$applicationId.':'.$previousStatus.':'.$status,['status_label'=>application_status_label($status),'note'=>$note,'action_url'=>$mailConfig['base_url'].'/app/student/application-summary.php'],'application',(int)$applicationId);
                flash_set('success','Application status updated and the student was notified.');
                header('Location: ' . app_url('admin/application.php?id=' . $applicationId)); exit;
            } catch (Throwable $exception) { $pdo->rollBack(); $errors[] = 'The application status could not be updated.'; }
        }
    }
    if (!$errors && $action === 'document_review') {
        $documentId = filter_input(INPUT_POST, 'document_id', FILTER_VALIDATE_INT); $status = $_POST['status'] ?? ''; $note = trim($_POST['admin_note'] ?? '');
        if (!$documentId || !in_array($status, $documentStatuses, true)) $errors[] = 'Select a valid document decision.';
        if (in_array($status, ['rejected','resubmission_required'], true) && $note === '') $errors[] = 'Add a note explaining what the student needs to correct.';
        if (!$errors) {
            $documentStmt = $pdo->prepare('SELECT id,document_type FROM documents WHERE id=? AND application_id=? LIMIT 1');
            $documentStmt->execute([$documentId,$applicationId]); $document = $documentStmt->fetch();
            if (!$document) $errors[] = 'Document not found.';
            else {
                $pdo->beginTransaction();
                try {
                    $now=date('Y-m-d H:i:s');
                    $pdo->prepare('UPDATE documents SET status=?,admin_note=?,reviewed_at=?,reviewed_by=?,updated_at=? WHERE id=?')->execute([$status,$note?:null,$now,$user['id'],$now,$documentId]);
                    if ($status === 'resubmission_required') $pdo->prepare("UPDATE applications SET status='documents_pending',updated_at=? WHERE id=?")->execute([$now,$applicationId]);
                    $label = document_type_options()[$document['document_type']] ?? application_status_label($document['document_type']);
                    $body = $label . ' was marked ' . application_status_label($status) . '.' . ($note !== '' ? ' ' . $note : '');
                    $pdo->prepare('INSERT INTO messages (sender_user_id,recipient_user_id,application_id,subject,body) VALUES (?,?,?,?,?)')->execute([$user['id'],$application['user_id'],$applicationId,'Document review updated',$body]);
                    $pdo->commit();
                    if(in_array($status,['rejected','resubmission_required'],true)){$mailConfig=medcon_mail_config();medcon_notify($pdo,'application.document_action_required',(int)$application['user_id'],'application.document_action_required:document:'.$documentId.':'.$status.':'.strtotime($now),['document_label'=>$label,'note'=>$note,'action_url'=>$mailConfig['base_url'].'/app/student/documents.php'],'document',(int)$documentId);}
                    flash_set('success','Document review saved and the student was notified.');
                    header('Location: ' . app_url('admin/application.php?id=' . $applicationId)); exit;
                } catch (Throwable $exception) { $pdo->rollBack(); $errors[] = 'The document review could not be saved.'; }
            }
        }
    }
    if (!$errors && $action === 'admin_message') {
        $subject = trim($_POST['subject'] ?? ''); $body = trim($_POST['body'] ?? '');
        if ($subject === '') $errors[] = 'Add a subject for the notification.';
        if ($body === '') $errors[] = 'Write a message for the student.';
        if (strlen($subject) > 255) $errors[] = 'Keep the subject under 255 characters.';
        if (!$errors) {
            try {
                $pdo->prepare('INSERT INTO messages (sender_user_id,recipient_user_id,application_id,subject,body) VALUES (?,?,?,?,?)')->execute([$user['id'],$application['user_id'],$applicationId,$subject,$body]);
                flash_set('success','Notification sent to the student.');
                header('Location: ' . app_url('admin/application.php?id=' . $applicationId)); exit;
            } catch (Throwable $exception) { $errors[] = 'The notification could not be sent.'; }
        }
    }
}
$application = $loadApplication();
$docStmt=$pdo->prepare('SELECT * FROM documents WHERE application_id=? ORDER BY created_at'); $docStmt->execute([$applicationId]); $documents=$docStmt->fetchAll();
$success=flash_get('success');
$fields=['intended_intake'=>'Intended intake','education_level'=>'Education level','science_background'=>'Science background','jamb_result'=>'JAMB result','passport_status'=>'Passport status','budget_range'=>'Budget range','accommodation_preference'=>'Accommodation','study_goal'=>'Medical study goal','message'=>'Additional message'];
?>
<!doctype html><html lang="en"><head><?php require_once dirname(__DIR__, 2) . '/includes/analytics.php'; ?><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Review application | Medcon Admin</title><link rel="stylesheet" href="<?= esc(app_url('assets/css/app.css')) ?>"></head>
<body class="medcon-app mc-role-body"><?php admin_portal_header($user, 'applications'); ?>
<main class="mc-role-main"><div class="mc-portal-heading"><div><span class="mc-kicker">Application #<?= (int)$application['id'] ?></span><h1><?= esc($application['first_name'].' '.$application['last_name']) ?></h1><p><?= esc($application['email']) ?> · <?= esc($application['phone'] ?: 'No phone') ?> · <?= esc($application['country'] ?: 'No country') ?></p></div><span class="mc-status-pill"><?= esc(application_status_label($application['status'])) ?></span></div>
<?php if($errors): ?><div class="mc-errors" role="alert"><?php foreach($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?><?php if($success): ?><div class="mc-success" role="status"><?= esc($success) ?></div><?php endif; ?>
<div class="mc-admin-review-grid"><section class="mc-panel"><h2>Application details</h2><dl class="mc-detail-list"><div><dt>College</dt><dd><?= esc($application['college_name'] ?: 'Not selected') ?></dd></div><div><dt>Programme</dt><dd><?= esc($application['programme_name'] ?: 'Not selected') ?></dd></div><?php foreach($fields as $key=>$label): ?><div><dt><?= esc($label) ?></dt><dd><?= nl2br(esc($application[$key] ?: 'Not supplied')) ?></dd></div><?php endforeach; ?></dl></section>
<aside><section class="mc-panel"><h2>Update status</h2><form method="post" class="mc-review-form"><?= csrf_field() ?><input type="hidden" name="action" value="application_status"><label><span>Status</span><select name="status"><?php foreach($applicationStatuses as $status): ?><option value="<?= esc($status) ?>"<?= $application['status']===$status?' selected':'' ?>><?= esc(application_status_label($status)) ?></option><?php endforeach; ?></select></label><label><span>Message to student (optional)</span><textarea name="note" rows="4"></textarea></label><button class="mc-btn-primary" type="submit">Save status</button></form></section><section class="mc-panel mc-admin-message-panel"><span class="mc-kicker">One-way message</span><h2>Notify this student</h2><p>Send an important update without changing the application status.</p><form method="post" class="mc-review-form"><?= csrf_field() ?><input type="hidden" name="action" value="admin_message"><label><span>Subject</span><input name="subject" maxlength="255" required placeholder="e.g. Interview date confirmed"></label><label><span>Message</span><textarea name="body" rows="5" required placeholder="Write the update the student should see..."></textarea></label><button class="mc-btn-primary" type="submit">Send notification</button></form></section></aside></div>
<section class="mc-panel mc-admin-documents"><h2>Uploaded documents</h2><?php if(!$documents): ?><p>No documents uploaded yet.</p><?php else: ?><div class="mc-document-list"><?php foreach($documents as $document): ?><article class="mc-document-row"><div><strong><?= esc(document_type_options()[$document['document_type']] ?? application_status_label($document['document_type'])) ?></strong><small><?= esc($document['file_name']) ?> · <?= esc(number_format(((int)$document['file_size'])/1048576,2)) ?>MB</small><a href="<?= esc(app_url('document-download.php?id='.$document['id'])) ?>" target="_blank" rel="noopener">View file</a></div><span class="mc-status-pill"><?= esc(application_status_label($document['status'])) ?></span><form method="post" class="mc-review-form mc-document-review"><?= csrf_field() ?><input type="hidden" name="action" value="document_review"><input type="hidden" name="document_id" value="<?= (int)$document['id'] ?>"><label><span>Decision</span><select name="status"><?php foreach($documentStatuses as $status): ?><option value="<?= esc($status) ?>"<?= $document['status']===$status?' selected':'' ?>><?= esc(application_status_label($status)) ?></option><?php endforeach; ?></select></label><label><span>Review note</span><input name="admin_note" value="<?= esc($document['admin_note']??'') ?>"></label><button class="mc-btn-secondary" type="submit">Save review</button></form></article><?php endforeach; ?></div><?php endif; ?></section>
</main><?php admin_portal_footer(); ?></body></html>
