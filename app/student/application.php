<?php
require_once __DIR__ . '/../includes/student.php';
$student=require_student($pdo); $errors=[];
$stmt=$pdo->prepare('SELECT * FROM applications WHERE student_id=? ORDER BY created_at DESC LIMIT 1'); $stmt->execute([$student['id']]); $application=$stmt->fetch()?:[];
$colleges=$pdo->query('SELECT id,name FROM colleges WHERE is_active=1 ORDER BY name')->fetchAll();
$programmes=$pdo->query('SELECT id,college_id,name FROM programmes WHERE is_active=1 ORDER BY name')->fetchAll();
$fields=['college_id','programme_id','intended_intake','education_level','science_background','jamb_result','passport_status','budget_range','accommodation_preference','study_goal','message'];
$intakeOptions=['September 2026','September 2027'];
$educationOptions=['Secondary School (Pre-WASSCE)','Secondary School (WASSCE)','Bachelors','Post-Bachelors'];
$scienceOptions=['Yes','No'];
if(!$application){$assessmentStmt=$pdo->prepare('SELECT answers_json FROM eligibility_assessments WHERE user_id=? ORDER BY created_at DESC LIMIT 1');$assessmentStmt->execute([$student['user_id']]);$assessmentJson=$assessmentStmt->fetchColumn();if($assessmentJson){$eligibility=json_decode($assessmentJson,true)?:[];$educationMap=['WAEC/NECO/High School'=>'Secondary School (WASSCE)',"Bachelor’s Degree"=>'Bachelors',"Master’s Degree"=>'Post-Bachelors'];$passportMap=['Yes'=>'Valid passport','No'=>'Not started','Application in progress'=>'Application in progress'];$application=['education_level'=>$educationMap[$eligibility['education_level']??'']??'','science_background'=>($eligibility['biology']??'')==='Yes'?'Yes':(($eligibility['biology']??'')==='No'?'No':''),'passport_status'=>$passportMap[$eligibility['passport_status']??'']??''];}}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $values=[]; foreach($fields as $field) $values[$field]=trim($_POST[$field]??''); $action=$_POST['action']??'draft';
    if (!verify_csrf($_POST['_csrf']??'')) $errors[]='Your session expired. Refresh the page and try again.';
    if (isset($application['id']) && ($application['status']??'draft')!=='draft') $errors[]='This application has already been submitted and cannot be edited.';
    if ($action==='submit') {
        foreach(['college_id','programme_id','intended_intake','education_level','science_background','jamb_result','passport_status','budget_range','accommodation_preference','study_goal'] as $required) if ($values[$required]==='') { $errors[]='Complete every required application field before submitting.'; break; }
        if (!isset($_POST['consent'])) $errors[]='Confirm that the information is accurate and consent to application processing.';
    }
    $validCollege=array_filter($colleges,fn($c)=>(string)$c['id']===$values['college_id']);
    $validProgramme=array_filter($programmes,fn($p)=>(string)$p['id']===$values['programme_id'] && (string)$p['college_id']===$values['college_id']);
    if ($values['college_id']!=='' && !$validCollege) $errors[]='Select a valid college.';
    if ($values['programme_id']!=='' && !$validProgramme) $errors[]='Select a programme offered by your preferred college.';
    if ($values['intended_intake']!=='' && !in_array($values['intended_intake'],$intakeOptions,true)) $errors[]='Select a valid intended intake.';
    if ($values['education_level']!=='' && !in_array($values['education_level'],$educationOptions,true)) $errors[]='Select a valid education level.';
    if ($values['science_background']!=='' && !in_array($values['science_background'],$scienceOptions,true)) $errors[]='Select Yes or No for science background.';
    if ($values['jamb_result']!=='' && (!ctype_digit($values['jamb_result']) || (int)$values['jamb_result']<0 || (int)$values['jamb_result']>400)) $errors[]='Enter a valid JAMB score between 0 and 400.';
    if (!$errors) {
        $status=$action==='submit'?'submitted':'draft'; $now=date('Y-m-d H:i:s');
        $params=[$values['college_id']?:null,$values['programme_id']?:null,$values['intended_intake']?:null,$values['education_level']?:null,$values['science_background']?:null,$values['jamb_result']?:null,$values['passport_status']?:null,$values['budget_range']?:null,$values['accommodation_preference']?:null,$values['study_goal']?:null,$values['message']?:null,$status,$action==='submit'?$now:null,$now];
        if (!empty($application['id'])) { $params[]=$application['id']; $params[]=$student['id']; $pdo->prepare('UPDATE applications SET college_id=?,programme_id=?,intended_intake=?,education_level=?,science_background=?,jamb_result=?,passport_status=?,budget_range=?,accommodation_preference=?,study_goal=?,message=?,status=?,submitted_at=?,updated_at=? WHERE id=? AND student_id=? AND status="draft"')->execute($params); $applicationId=$application['id']; }
        else { array_unshift($params,$student['agent_id'],$student['id']); $pdo->prepare('INSERT INTO applications (agent_id,student_id,college_id,programme_id,intended_intake,education_level,science_background,jamb_result,passport_status,budget_range,accommodation_preference,study_goal,message,status,submitted_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute($params); $applicationId=$pdo->lastInsertId(); }
        if ($action==='submit') {
            $pdo->prepare('INSERT INTO messages (recipient_user_id,application_id,subject,body) VALUES (?,?,?,?)')->execute([$student['user_id'],$applicationId,'Application received','Your application has been submitted. The admissions team will review it and share the next step here.']);
            flash_set('success','Your application has been submitted. Continue by uploading your supporting documents.');
        } else flash_set('success','Your application draft has been saved.');
        header('Location: '.app_url($action==='submit'?'student/documents.php':'student/application.php')); exit;
    }
    $application=array_replace($application,$values);
}
$success=flash_get('success'); $hasSavedApplication=isset($application['id']); $locked=$hasSavedApplication && ($application['status']??'draft')!=='draft'; student_portal_start('Application','application',$student);
?>
<section class="mc-portal-heading"><div><span class="mc-kicker">Application enquiry</span><h1><?= $locked?'Your application':'Build your application' ?></h1><p><?= $locked?'Your submitted information and current status are shown below.':'Save a draft at any time, then submit when every required field is complete.' ?></p></div><?php if($hasSavedApplication): ?><span class="mc-status-pill"><?= esc(application_status_label($application['status']??'draft')) ?></span><?php endif; ?></section>
<?php if($errors): ?><div class="mc-errors" role="alert"><?php foreach($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?><?php if($success): ?><div class="mc-success" role="status"><?= esc($success) ?></div><?php endif; ?>
<form method="post" class="mc-portal-form"><?= csrf_field() ?><fieldset<?= $locked?' disabled':'' ?>><legend>Study preferences</legend><div class="mc-field-grid">
<label><span>Preferred college *</span><select name="college_id" required><option value="">Select a college</option><?php foreach($colleges as $college): ?><option value="<?= $college['id'] ?>"<?= (string)($application['college_id']??'')===(string)$college['id']?' selected':'' ?>><?= esc($college['name']) ?></option><?php endforeach; ?></select></label>
<label><span>Preferred programme *</span><input type="hidden" name="programme_id" value="<?= esc($application['programme_id']??'') ?>"><input type="text" value="Doctor of Medicine" disabled aria-describedby="programme-note"><small id="programme-note">Programme is fixed for all partner colleges.</small></label>
<label><span>Intended intake *</span><select name="intended_intake" required><option value="">Select intake</option><?php foreach($intakeOptions as $option): ?><option value="<?= esc($option) ?>"<?= ($application['intended_intake']??'')===$option?' selected':'' ?>><?= esc($option) ?></option><?php endforeach; ?></select></label>
<label><span>Current education level *</span><select name="education_level" required><option value="">Select education level</option><?php foreach($educationOptions as $option): ?><option value="<?= esc($option) ?>"<?= ($application['education_level']??$student['education_level']??'')===$option?' selected':'' ?>><?= esc($option) ?></option><?php endforeach; ?></select></label>
<label><span>Science background *</span><select name="science_background" required><option value="">Select an answer</option><?php foreach($scienceOptions as $option): ?><option value="<?= esc($option) ?>"<?= ($application['science_background']??'')===$option?' selected':'' ?>><?= esc($option) ?></option><?php endforeach; ?></select></label>
<label><span>JAMB result *</span><input type="number" name="jamb_result" min="0" max="400" inputmode="numeric" placeholder="Enter score (0–400)" required value="<?= esc($application['jamb_result']??'') ?>"></label>
<label><span>Passport status *</span><select name="passport_status" required><?php foreach([''=>'Select status','Valid passport'=>'Valid passport','Application in progress'=>'Application in progress','Not started'=>'Not started'] as $v=>$l): ?><option value="<?= esc($v) ?>"<?= ($application['passport_status']??'')===$v?' selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></label>
<label><span>Budget range *</span><select name="budget_range" required><?php foreach([''=>'Select range','Under $10,000 per year'=>'Under $10,000 per year','$10,000–$15,000 per year'=>'$10,000–$15,000 per year','$15,000–$20,000 per year'=>'$15,000–$20,000 per year','Above $20,000 per year'=>'Above $20,000 per year'] as $v=>$l): ?><option value="<?= esc($v) ?>"<?= ($application['budget_range']??'')===$v?' selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></label>
<label><span>Accommodation preference *</span><select name="accommodation_preference" required><?php foreach([''=>'Select preference','School hostel'=>'School hostel','Private accommodation'=>'Private accommodation','Need guidance'=>'Need guidance'] as $v=>$l): ?><option value="<?= esc($v) ?>"<?= ($application['accommodation_preference']??'')===$v?' selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></label>
<label class="mc-field-wide"><span>Medical study goal *</span><textarea name="study_goal" required><?= esc($application['study_goal']??'') ?></textarea></label><label class="mc-field-wide"><span>Additional message</span><textarea name="message"><?= esc($application['message']??'') ?></textarea></label>
<?php if(!$locked): ?><label class="mc-consent mc-field-wide"><input type="checkbox" name="consent"><span>I confirm that this information is accurate and consent to Medcon processing it for admission support.</span></label><?php endif; ?>
</div></fieldset><?php if(!$locked): ?><div class="mc-form-actions"><button class="mc-btn-secondary" name="action" value="draft" type="submit">Save draft</button><button class="mc-btn-primary" name="action" value="submit" type="submit">Submit application</button></div><?php else: ?><div class="mc-form-actions"><a class="mc-btn-primary mc-button-link" href="<?= esc(app_url('student/documents.php')) ?>">Continue to documents →</a></div><?php endif; ?></form>
<script>
(function () {
    var college = document.querySelector('select[name="college_id"]');
    var programme = document.querySelector('input[name="programme_id"]');
    if (!college || !programme) return;
    var programmeByCollege = <?= json_encode(array_column($programmes, 'id', 'college_id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    function syncProgramme() {
        programme.value = programmeByCollege[college.value] || '';
    }
    college.addEventListener('change', syncProgramme);
    syncProgramme();
})();
</script>
<?php student_portal_end(); ?>
