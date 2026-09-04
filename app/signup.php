<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth-layout.php';
require_once __DIR__ . '/includes/profile-options.php';
require_once dirname(__DIR__) . '/scripts/mail/AccountNotifications.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verify_csrf($_POST['_csrf']??'')) $errors[]='Invalid form submission.';
    $first_name=trim($_POST['first_name']??''); $last_name=trim($_POST['last_name']??''); $email=strtolower(trim($_POST['email']??'')); $phone=trim($_POST['phone']??''); $country=trim($_POST['country']??'Nigeria'); $dob=trim($_POST['date_of_birth']??''); $gender=trim($_POST['gender']??''); $state=trim($_POST['state']??''); $lga=trim($_POST['lga']??''); $address=trim($_POST['address']??''); $heardAbout=trim($_POST['heard_about']??''); $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??''; $consent=isset($_POST['consent']); $referral=trim($_POST['referral_code']??($_SESSION['referral_code']??''));
    if ($first_name===''||$last_name===''||$email===''||$password===''||$confirm==='') $errors[]='Please fill in all required fields.';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Invalid email address.';
    if(!in_array($gender,['male','female'],true))$errors[]='Select your gender.';
    if($dob===''||!DateTime::createFromFormat('Y-m-d',$dob))$errors[]='Enter a valid date of birth.';
    if(!in_array($state,nigeria_states(),true)||$lga===''||$address==='')$errors[]='Select your state and LGA, and enter your address.';
    if(!in_array($heardAbout,acquisition_sources(),true))$errors[]='Tell us where you heard about this opportunity.';
    if (strlen($password)<8) $errors[]='Password must be at least 8 characters.';
    if ($password!==$confirm) $errors[]='Passwords do not match.';
    if (!$consent) $errors[]='You must accept terms to continue.';
    $stmt=$pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1'); $stmt->execute([$email]); if ($stmt->fetch()) $errors[]='An account with that email already exists.';
    if (!$errors) {
        $agentId=null;
        if ($referral !== '') { $agentStmt=$pdo->prepare("SELECT id FROM agents WHERE (referral_code=? OR referral_slug=?) AND approval_status='approved' LIMIT 1"); $agentStmt->execute([$referral,$referral]); $agentId=$agentStmt->fetchColumn() ?: null; if (!$agentId) $errors[]='That referral code is not valid.'; }
    }
    if (!$errors) {
        $pw_hash=password_hash($password,PASSWORD_DEFAULT); $now=date('Y-m-d H:i:s');
        $pdo->beginTransaction();
        $insert=$pdo->prepare('INSERT INTO users (role, first_name, last_name, email, phone, country, password_hash, status, email_verified, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute(['student',$first_name,$last_name,$email,$phone,$country,$pw_hash,'active',0,$now,$now]); $user_id=$pdo->lastInsertId();
        $ins2=$pdo->prepare('INSERT INTO students (user_id,agent_id,date_of_birth,gender,state,lga,address,heard_about,referral_code,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)'); $ins2->execute([$user_id,$agentId,$dob,$gender,$state,$lga,$address,$heardAbout,$referral?:null,$now,$now]);$studentId=(int)$pdo->lastInsertId();$pdo->commit();
        medcon_send_verification($pdo,(int)$user_id);if($agentId)medcon_notify_affiliate($pdo,(int)$agentId,$studentId);
        unset($_SESSION['referral_code']);
        $_SESSION['user']=['id'=>$user_id,'role'=>'student','full_name'=>$first_name.' '.$last_name,'email'=>$email]; header('Location: student/dashboard.php'); exit;
    }
}
auth_page_start('Create account','Start with clarity','Your route to medical school begins here.','Create your student account to organise applications, documents and admissions guidance.','mc-signup-page');
?>
<div class="mc-auth-top"><span>Already registered?</span><a class="mc-text-button" href="login.php">Sign in</a></div>
<div class="mc-form-heading"><span class="mc-kicker">Student registration</span><h1>Create your account</h1><p>Enter your details to open your Medcon student profile.</p></div>
<?php if ($errors): ?><div class="mc-errors"><?php foreach($errors as $item): ?><div><?= esc($item) ?></div><?php endforeach; ?></div><?php endif; ?>
<form method="post" id="signup-form" class="mc-auth-form mc-signup-form" novalidate>
    <?= csrf_field() ?>
    <div class="mc-field-grid">
        <label><span>First name</span><input name="first_name" autocomplete="given-name" required value="<?= esc($_POST['first_name']??'') ?>"></label>
        <label><span>Last name</span><input name="last_name" autocomplete="family-name" required value="<?= esc($_POST['last_name']??'') ?>"></label>
        <label><span>Email address</span><input type="email" name="email" autocomplete="email" required value="<?= esc($_POST['email']??'') ?>"></label>
        <label><span>Phone number</span><input name="phone" autocomplete="tel" value="<?= esc($_POST['phone']??'') ?>"></label>
        <label><span>Gender</span><select name="gender" required><option value="">Select gender</option><option value="male"<?= ($_POST['gender']??'')==='male'?' selected':'' ?>>Male</option><option value="female"<?= ($_POST['gender']??'')==='female'?' selected':'' ?>>Female</option></select></label>
        <label><span>Date of birth</span><input type="date" name="date_of_birth" required value="<?= esc($_POST['date_of_birth']??'') ?>"></label>
        <label><span>State</span><select name="state" required data-state-select><option value="">Select state</option><?= profile_select_options(nigeria_states(),$_POST['state']??'') ?></select></label>
        <label><span>City / LGA</span><select name="lga" required data-lga-select data-current="<?= esc($_POST['lga']??'') ?>"><option value="">Select state first</option></select></label>
        <label class="mc-field-wide"><span>Address</span><input name="address" autocomplete="street-address" required value="<?= esc($_POST['address']??'') ?>"></label>
        <label><span>Where did you hear about this opportunity?</span><select name="heard_about" required><option value="">Select one</option><?= profile_select_options(acquisition_sources(),$_POST['heard_about']??'') ?></select></label>
        <label><span>Country</span><input name="country" autocomplete="country-name" value="<?= esc($_POST['country']??'Nigeria') ?>"></label>
        <label class="mc-field-wide"><span>Referral code (optional)</span><input name="referral_code" value="<?= esc($_POST['referral_code']??($_SESSION['referral_code']??'')) ?>" placeholder="e.g. Medcon001"></label>
        <label><span>Password</span><div class="mc-password-wrapper"><input type="password" name="password" autocomplete="new-password" required><button type="button" class="mc-password-toggle" aria-label="Show password" data-show="false">👁</button></div><small>Minimum 8 characters</small></label>
        <label><span>Confirm password</span><div class="mc-password-wrapper"><input type="password" name="confirm_password" autocomplete="new-password" required><button type="button" class="mc-password-toggle" aria-label="Show password" data-show="false">👁</button></div></label>
    </div>
    <label class="mc-consent"><input type="checkbox" name="consent" <?= isset($_POST['consent'])?'checked':'' ?>><span>I agree to the <a href="../terms.php">Terms of Use</a> and <a href="../privacy-policy.php">Privacy Policy</a>.</span></label>
    <button class="mc-btn-primary" type="submit">Create account <span aria-hidden="true">→</span></button>
</form>
<script src="<?= esc(app_url('assets/js/profile-location.js?v=20260826')) ?>" data-locations-url="<?= esc(app_url('location-options.php')) ?>"></script>
<p class="mc-mobile-switch">Already have an account? <a href="login.php">Sign in</a></p>
<?php auth_page_end(); ?>
