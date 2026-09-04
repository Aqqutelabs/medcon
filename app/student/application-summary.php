<?php
require_once __DIR__ . '/../includes/student.php';
require_once __DIR__ . '/../includes/document-storage.php';

$student = require_student($pdo);
$applicationStmt = $pdo->prepare('SELECT a.*,c.name college_name,p.name programme_name FROM applications a LEFT JOIN colleges c ON c.id=a.college_id LEFT JOIN programmes p ON p.id=a.programme_id WHERE a.student_id=? AND a.status<>"draft" ORDER BY a.created_at DESC LIMIT 1');
$applicationStmt->execute([$student['id']]);
$application = $applicationStmt->fetch();
if (!$application) { header('Location: ' . app_url('student/application.php')); exit; }
if (in_array($application['status'], ['submitted','documents_pending'], true)) { header('Location: ' . app_url('student/documents.php')); exit; }

$documentStmt = $pdo->prepare('SELECT id,document_type,file_name,file_size,status,admin_note,uploaded_at FROM documents WHERE student_id=? AND application_id=? ORDER BY created_at');
$documentStmt->execute([$student['id'],$application['id']]);
$documents = $documentStmt->fetchAll();
$documentsByType = [];
foreach ($documents as $document) $documentsByType[$document['document_type']][] = $document;
$documentTypes = document_type_options();
$complete = true;
foreach (required_document_types() as $key) {
    $hasCurrent=false; foreach($documentsByType[$key]??[] as $document) if(in_array($document['status'],['uploaded','approved'],true)){$hasCurrent=true;break;}
    if (!$hasCurrent) { $complete = false; break; }
}
if (!$complete) { header('Location: ' . app_url('student/documents.php')); exit; }

$success = flash_get('success');
$details = [
    'Preferred college' => $application['college_name'],
    'Preferred programme' => $application['programme_name'],
    'Intended intake' => $application['intended_intake'],
    'Education level' => $application['education_level'],
    'Science background' => $application['science_background'],
    'JAMB result' => $application['jamb_result'],
    'Passport status' => $application['passport_status'],
    'Budget range' => $application['budget_range'],
    'Accommodation preference' => $application['accommodation_preference'],
    'Medical study goal' => $application['study_goal'],
    'Additional message' => $application['message'],
];
student_portal_start('Application summary', 'application', $student);
?>
<section class="mc-portal-heading"><div><span class="mc-kicker">Process complete</span><h1>Application summary</h1><p>Your application and supporting documents have been sent to the admissions team.</p></div><span class="mc-status-pill"><?= esc(application_status_label($application['status'])) ?></span></section>
<?php if ($success): ?><div class="mc-success" role="status"><?= esc($success) ?></div><?php endif; ?>
<section class="mc-summary-banner"><div><span aria-hidden="true">✓</span></div><div><h2>Submission complete</h2><p>No further action is required right now. You can return here to review what you submitted, and status updates will appear on your dashboard.</p></div><a class="mc-btn-secondary mc-button-link" href="<?= esc(app_url('student/dashboard.php')) ?>">Return to dashboard</a></section>
<div class="mc-summary-grid"><section class="mc-panel"><h2>Application details</h2><dl class="mc-detail-list"><?php foreach ($details as $label => $value): ?><div><dt><?= esc($label) ?></dt><dd><?= nl2br(esc($value ?: 'Not supplied')) ?></dd></div><?php endforeach; ?></dl></section>
<section class="mc-panel"><h2>Documents submitted</h2><div class="mc-document-list"><?php foreach ($documentTypes as $key => $label): $items=$documentsByType[$key]??[]; if(!$items) continue; foreach($items as $document): ?><article class="mc-summary-document"><div><strong><?= esc($label) ?></strong><small><?= esc($document['file_name']) ?> · <?= esc(number_format(((int)$document['file_size'])/1048576,2)) ?>MB</small></div><div><span class="mc-status-pill"><?= esc(application_status_label($document['status'])) ?></span><a href="<?= esc(app_url('document-download.php?id='.$document['id'])) ?>" target="_blank" rel="noopener">View file</a></div></article><?php endforeach; endforeach; ?></div></section></div>
<?php student_portal_end(); ?>
