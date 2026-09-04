<?php
require_once __DIR__ . '/../includes/student.php';
$student=require_student($pdo);
$stmt=$pdo->prepare('SELECT id FROM applications WHERE student_id=? ORDER BY created_at DESC LIMIT 1'); $stmt->execute([$student['id']]);
if ($stmt->fetch()) { header('Location: '.app_url('student/application.php')); exit; }
student_portal_start('Start application','application',$student);
?>
<section class="mc-start-card"><span class="mc-kicker">Application enquiry</span><h1>Take the first step toward medical school</h1><p>Your application helps Medcon understand your preferred college, intended intake, education, budget and support needs. You can save a draft and return before submitting.</p><div class="mc-start-points"><span>About 5 minutes</span><span>Save and continue later</span><span>Your information stays private</span></div><a class="mc-btn-primary" href="<?= esc(app_url('student/application.php')) ?>">Begin application</a></section>
<?php student_portal_end(); ?>
