<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth-layout.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!verify_csrf($_POST['_csrf']??'')) $errors[]='Invalid form submission.';
    $first_name=trim($_POST['first_name']??''); $last_name=trim($_POST['last_name']??''); $email=strtolower(trim($_POST['email']??'')); $phone=trim($_POST['phone']??''); $country=trim($_POST['country']??''); $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??''; $consent=isset($_POST['consent']); $referral=trim($_POST['referral_code']??($_SESSION['referral_code']??''));
    if ($first_name===''||$last_name===''||$email===''||$password===''||$confirm==='') $errors[]='Please fill in all required fields.';
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Invalid email address.';
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
        $insert=$pdo->prepare('INSERT INTO users (role, first_name, last_name, email, phone, country, password_hash, status, email_verified, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute(['student',$first_name,$last_name,$email,$phone,$country,$pw_hash,'active',0,$now,$now]); $user_id=$pdo->lastInsertId();
        $ins2=$pdo->prepare('INSERT INTO students (user_id, agent_id, created_at, updated_at) VALUES (?, ?, ?, ?)'); $ins2->execute([$user_id,$agentId,$now,$now]);
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
        <label class="mc-field-wide"><span>Country</span><input name="country" autocomplete="country-name" value="<?= esc($_POST['country']??'') ?>"></label>
        <label class="mc-field-wide"><span>Referral code (optional)</span><input name="referral_code" value="<?= esc($_POST['referral_code']??($_SESSION['referral_code']??'')) ?>" placeholder="e.g. Medcon001"></label>
        <label><span>Password</span><input type="password" name="password" autocomplete="new-password" required><small>Minimum 8 characters</small></label>
        <label><span>Confirm password</span><input type="password" name="confirm_password" autocomplete="new-password" required></label>
    </div>
    <label class="mc-consent"><input type="checkbox" name="consent" <?= isset($_POST['consent'])?'checked':'' ?>><span>I agree to the <a href="../terms.php">Terms of Use</a> and <a href="../privacy-policy.php">Privacy Policy</a>.</span></label>
    <button class="mc-btn-primary" type="submit">Create account <span aria-hidden="true">→</span></button>
</form>
<p class="mc-mobile-switch">Already have an account? <a href="login.php">Sign in</a></p>
<?php auth_page_end(); ?>
