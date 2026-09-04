<?php
require_once __DIR__ . '/../includes/student.php';
require_once __DIR__ . '/../includes/profile-options.php';
require_once dirname(__DIR__,2) . '/scripts/mail/AccountNotifications.php';
$student = require_student($pdo); $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values=[]; foreach (['first_name','last_name','phone','country','date_of_birth','gender','nationality','education_level','state','lga','address','heard_about','referral_code','parent_name','parent_phone','parent_email'] as $field) $values[$field]=trim($_POST[$field]??'');
    if (!verify_csrf($_POST['_csrf']??'')) $errors[]='Your session expired. Refresh the page and try again.';
    if ($values['first_name']===''||$values['last_name']===''||$values['phone']===''||$values['country']==='') $errors[]='First name, last name, phone and country are required.';
    if ($values['date_of_birth']!=='' && !DateTime::createFromFormat('Y-m-d',$values['date_of_birth'])) $errors[]='Enter a valid date of birth.';
    if($values['gender']!==''&&!in_array($values['gender'],['male','female'],true))$errors[]='Select a valid gender.';
    if($values['state']!==''&&!in_array($values['state'],nigeria_states(),true))$errors[]='Select a valid state.';
    if($values['heard_about']!==''&&!in_array($values['heard_about'],acquisition_sources(),true))$errors[]='Select where you heard about this opportunity.';
    if ($values['parent_email']!=='' && !filter_var($values['parent_email'],FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid parent or guardian email.';
    if (!$errors) {
        $pdo->beginTransaction();
        try {
            $pdo->prepare('UPDATE users SET first_name=?,last_name=?,phone=?,country=? WHERE id=?')->execute([$values['first_name'],$values['last_name'],$values['phone'],$values['country'],$student['user_id']]);
            $previousAgentId=$student['agent_id']??null;$agentId=$previousAgentId;if($values['referral_code']!==''){$agentStmt=$pdo->prepare("SELECT id FROM agents WHERE (referral_code=? OR referral_slug=?) AND approval_status='approved' LIMIT 1");$agentStmt->execute([$values['referral_code'],$values['referral_code']]);$agentId=$agentStmt->fetchColumn()?:$agentId;}
            $pdo->prepare('UPDATE students SET date_of_birth=?,gender=?,nationality=?,education_level=?,state=?,lga=?,address=?,heard_about=?,referral_code=?,agent_id=?,parent_name=?,parent_phone=?,parent_email=? WHERE id=? AND user_id=?')->execute([$values['date_of_birth']?:null,$values['gender']?:null,$values['nationality']?:null,$values['education_level']?:null,$values['state']?:null,$values['lga']?:null,$values['address']?:null,$values['heard_about']?:null,$values['referral_code']?:null,$agentId,$values['parent_name']?:null,$values['parent_phone']?:null,$values['parent_email']?:null,$student['id'],$student['user_id']]);
            $pdo->commit();if(!$previousAgentId&&$agentId)medcon_notify_affiliate($pdo,(int)$agentId,(int)$student['id']);$_SESSION['user']['full_name']=$values['first_name'].' '.$values['last_name']; flash_set('success','Your profile has been updated.'); header('Location: '.app_url('student/profile.php')); exit;
        } catch (Throwable $e) { $pdo->rollBack(); $errors[]='Your profile could not be saved. Try again.'; }
    }
    $student=array_replace($student,$values);
}
$success=flash_get('success'); student_portal_start('Profile','profile',$student);
?>
<section class="mc-portal-heading"><div><span class="mc-kicker">Personal details</span><h1>Your profile</h1><p>Keep these details complete so the admissions team can assess and support your application.</p></div></section>
<?php if($errors): ?><div class="mc-errors" role="alert"><?php foreach($errors as $error): ?><div><?= esc($error) ?></div><?php endforeach; ?></div><?php endif; ?>
<?php if($success): ?><div class="mc-success" role="status"><?= esc($success) ?></div><?php endif; ?>
<form method="post" class="mc-portal-form"><?= csrf_field() ?>
<fieldset><legend>Student information</legend><div class="mc-field-grid"><label><span>First name *</span><input name="first_name" required value="<?= esc($student['first_name']) ?>"></label><label><span>Last name *</span><input name="last_name" required value="<?= esc($student['last_name']) ?>"></label><label><span>Phone *</span><input type="tel" name="phone" required value="<?= esc($student['phone']) ?>"></label><label><span>Country *</span><input name="country" required value="<?= esc($student['country']?:'Nigeria') ?>"></label><label><span>Date of birth</span><input type="date" name="date_of_birth" value="<?= esc($student['date_of_birth']??'') ?>"></label><label><span>Gender</span><select name="gender"><option value="">Select gender</option><option value="male"<?= ($student['gender']??'')==='male'?' selected':'' ?>>Male</option><option value="female"<?= ($student['gender']??'')==='female'?' selected':'' ?>>Female</option></select></label><label><span>State</span><select name="state" data-state-select><option value="">Select state</option><?= profile_select_options(nigeria_states(),$student['state']??'') ?></select></label><label><span>City / LGA</span><select name="lga" data-lga-select data-current="<?= esc($student['lga']??'') ?>"><option value="">Select state first</option></select></label><label class="mc-field-wide"><span>Address</span><input name="address" value="<?= esc($student['address']??'') ?>"></label><label><span>Where did you hear about this opportunity?</span><select name="heard_about"><option value="">Select one</option><?= profile_select_options(acquisition_sources(),$student['heard_about']??'') ?></select></label><label><span>Referral code</span><input name="referral_code" value="<?= esc($student['referral_code']??'') ?>" placeholder="e.g. Medcon001"></label><label><span>Nationality</span><input name="nationality" value="<?= esc($student['nationality']??'') ?>"></label><label><span>Education level</span><input name="education_level" value="<?= esc($student['education_level']??'') ?>"></label></div></fieldset>
<fieldset><legend>Parent or guardian</legend><div class="mc-field-grid"><label><span>Name</span><input name="parent_name" value="<?= esc($student['parent_name']??'') ?>"></label><label><span>Phone</span><input type="tel" name="parent_phone" value="<?= esc($student['parent_phone']??'') ?>"></label><label class="mc-field-wide"><span>Email</span><input type="email" name="parent_email" value="<?= esc($student['parent_email']??'') ?>"></label></div></fieldset>
<button class="mc-btn-primary" type="submit">Save profile</button></form>
<script src="<?= esc(app_url('assets/js/profile-location.js?v=20260826')) ?>" data-locations-url="<?= esc(app_url('location-options.php')) ?>"></script>
<?php student_portal_end(); ?>
