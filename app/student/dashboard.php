<?php
require_once __DIR__ . '/../includes/student.php';
require_once __DIR__ . '/../includes/document-storage.php';
$student = require_student($pdo);
$appStmt = $pdo->prepare('SELECT a.*, c.name college_name, p.name programme_name FROM applications a LEFT JOIN colleges c ON c.id=a.college_id LEFT JOIN programmes p ON p.id=a.programme_id WHERE a.student_id=? ORDER BY a.created_at DESC LIMIT 1');
$appStmt->execute([$student['id']]);
$application = $appStmt->fetch() ?: null;
$docCounts = ['submitted' => 0, 'pending' => 0];
$docStmt = $pdo->prepare('SELECT status,document_type,COUNT(*) total FROM documents WHERE student_id=? GROUP BY status,document_type');
$docStmt->execute([$student['id']]);
$completedRequiredTypes=[];
foreach ($docStmt as $row) {
    if (in_array($row['status'], ['uploaded','approved'], true)) $docCounts['submitted'] += (int)$row['total'];
    if (in_array($row['status'], ['pending','rejected','resubmission_required'], true)) $docCounts['pending'] += (int)$row['total'];
    if (in_array($row['status'],['uploaded','approved'],true) && in_array($row['document_type'],required_document_types(),true)) $completedRequiredTypes[]=$row['document_type'];
}
$totalDocuments=$docCounts['submitted']+$docCounts['pending'];
$requiredDocumentsComplete=!array_diff(required_document_types(),array_unique($completedRequiredTypes));
$messageStmt = $pdo->prepare('SELECT subject, body, created_at FROM messages WHERE recipient_user_id=? ORDER BY created_at DESC LIMIT 1');
$messageStmt->execute([$student['user_id']]);
$latestMessage = $messageStmt->fetch() ?: null;
if (!$application) $nextStep = ['Start your application','Tell us your preferred college, programme and intake.',app_url('student/application-start.php')];
elseif ($application['status'] === 'draft') $nextStep = ['Complete your application','Review the remaining fields and submit when ready.',app_url('student/application.php')];
elseif ($totalDocuments === 0) $nextStep = ['Upload your documents','Your application is submitted. Continue with the supporting document checklist.',app_url('student/documents.php')];
elseif ($application['status'] === 'documents_pending' || $docCounts['pending'] > 0) $nextStep = ['Submit pending documents','Open your document checklist and complete the pending items.',app_url('student/documents.php')];
elseif ($requiredDocumentsComplete && $application['status'] === 'submitted') $nextStep = ['Finish your submission','All required documents are uploaded. Finish the process and review your summary.',app_url('student/documents.php')];
elseif ($requiredDocumentsComplete && $application['status'] !== 'submitted') $nextStep = ['View your summary','Your completed application and document summary is ready.',app_url('student/application-summary.php')];
else $nextStep = ['Track your application','Your application is active. Updates will appear here.',app_url('student/application.php')];
$journey=['Application','Documents','College review','Admission','Visa','Enrollment'];
$completedStageMap=['draft'=>0,'submitted'=>1,'documents_pending'=>1,'received'=>2,'under_review'=>2,'approved'=>3,'rejected'=>2,'admission_issued'=>4,'visa_processing'=>4,'enrolled'=>6];
$completedStages=$application ? ($completedStageMap[$application['status']]??0) : 0;
$currentStage=$completedStages<count($journey)?$completedStages:-1;
$progress=(int)round(($completedStages/count($journey))*100);
student_portal_start('Dashboard', 'dashboard', $student);
$success=flash_get('success');
?>
<?php if($success): ?><div class="mc-success" role="status"><?= esc($success) ?></div><?php endif; ?>
<section class="mc-portal-heading mc-dashboard-heading"><div><span class="mc-kicker">Student dashboard</span><h1>Good to see you, <?= esc($student['first_name']) ?>.</h1><p>Everything important about your medical school application, in one place.</p></div><div class="mc-heading-actions"><span class="mc-date-chip"><?= esc(date('l, j F')) ?></span><a class="mc-btn-primary" href="<?= esc($application ? app_url('student/application.php') : app_url('student/application-start.php')) ?>"><?= $application ? 'View application' : 'Start application' ?></a></div></section>

<section class="mc-dashboard-hero" aria-label="Application overview">
<article class="mc-application-feature"><div class="mc-feature-top"><div><span class="mc-feature-label">Your application</span><span class="mc-feature-status"><?= esc(application_status_label($application['status']??null)) ?></span></div><strong><?= $progress ?>%</strong></div><h2><?= $application ? esc($application['college_name']?:'College selection pending') : 'Begin your medical school application' ?></h2><p><?= $application ? esc($application['programme_name']?:'Programme selection pending') : 'Create your application and save your progress as you go.' ?></p><div class="mc-feature-progress" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"><span style="width:<?= $progress ?>%"></span></div><div class="mc-feature-footer"><span><?= $application && $application['intended_intake'] ? 'Intake: '.esc($application['intended_intake']) : 'Intake not selected' ?></span><a href="<?= esc($application?app_url('student/application.php'):app_url('student/application-start.php')) ?>">Open application <span aria-hidden="true">→</span></a></div></article>
<article class="mc-next-action-card"><div class="mc-action-icon" aria-hidden="true">01</div><span class="mc-kicker">Priority action</span><h2><?= esc($nextStep[0]) ?></h2><p><?= esc($nextStep[1]) ?></p><a class="mc-btn-secondary" href="<?= esc($nextStep[2]) ?>">Continue now <span aria-hidden="true">→</span></a></article>
</section>

<section class="mc-compact-metrics" aria-label="Student summary">
<a class="mc-compact-metric" href="<?= esc(app_url('student/documents.php')) ?>"><span class="mc-metric-icon mc-icon-teal" aria-hidden="true">✓</span><span><small>Documents ready</small><strong><?= $docCounts['submitted'] ?><?= $totalDocuments ? ' of '.$totalDocuments : '' ?></strong></span><b aria-hidden="true">→</b></a>
<a class="mc-compact-metric" href="<?= esc(app_url('student/documents.php')) ?>"><span class="mc-metric-icon mc-icon-orange" aria-hidden="true">!</span><span><small>Documents pending</small><strong><?= $docCounts['pending'] ?></strong></span><b aria-hidden="true">→</b></a>
<div class="mc-compact-metric"><span class="mc-metric-icon" aria-hidden="true">✉</span><span><small>Latest update</small><strong><?= $latestMessage ? esc(date('j M',strtotime($latestMessage['created_at']))) : 'No messages' ?></strong></span></div>
</section>

<section class="mc-panel mc-journey-card"><div class="mc-section-head"><div><span class="mc-kicker">Application progress</span><h2>Your admission journey</h2><p>Each stage updates as your application moves forward.</p></div><strong><?= $progress ?>%</strong></div><ol class="mc-progress-steps"><?php foreach($journey as $index=>$stage): $state=$index<$completedStages?'is-complete':($index===$currentStage?'is-current':''); ?><li class="<?= $state ?>"><span aria-hidden="true"><?= $index<$completedStages?'✓':($index===$currentStage?'●':$index+1) ?></span><strong><?= esc($stage) ?></strong></li><?php endforeach; ?></ol></section>

<section class="mc-dashboard-lower"><article class="mc-panel mc-message-card"><div class="mc-section-head"><div><span class="mc-kicker">Latest message</span><h2><?= $latestMessage?esc($latestMessage['subject']):'No messages yet' ?></h2></div><?php if($latestMessage): ?><time datetime="<?= esc(date('Y-m-d',strtotime($latestMessage['created_at']))) ?>"><?= esc(date('j M Y',strtotime($latestMessage['created_at']))) ?></time><?php endif; ?></div><p><?= $latestMessage?esc($latestMessage['body']):'Your admissions updates and important notices will appear here.' ?></p></article><aside class="mc-support-card"><span class="mc-kicker">Personal guidance</span><h2>Need help with your next step?</h2><p>Speak with a Medcon admissions adviser about eligibility, documents or college selection.</p><a class="mc-btn-light" href="<?= esc(MEDCON_CONSULTATION_URL) ?>" target="_blank" rel="noopener noreferrer">Book consultation</a></aside></section>
<?php student_portal_end(); ?>
