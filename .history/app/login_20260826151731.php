<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth-layout.php';
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) $error = 'Invalid form submission.';
    else {
        $email = strtolower(trim($_POST['email'] ?? '')); $password = $_POST['password'] ?? '';
        if ($email === '' || $password === '') $error = 'Invalid credentials.';
        else {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1'); $stmt->execute([$email]); $user = $stmt->fetch();
            if (!$user || !password_verify($password, $user['password_hash'])) $error = 'Invalid credentials.';
            elseif ($user['status'] === 'suspended') $error = 'Your account is suspended.';
            elseif ($user['role'] === 'agent' && !in_array((string)$user['approval_status'], ['approved'], true)) $error = 'Your agent account is awaiting approval.';
            else {
                session_regenerate_id(true);
                $_SESSION['user'] = ['id'=>$user['id'],'role'=>$user['role'],'full_name'=>$user['first_name'].' '.$user['last_name'],'email'=>$user['email']];
                $upd=$pdo->prepare('UPDATE users SET last_login_at = ? WHERE id = ?'); $upd->execute([date('Y-m-d H:i:s'),$user['id']]);
                switch ($user['role']) { case 'student': header('Location: student/dashboard.php'); break; case 'agent': header('Location: agent/dashboard.php'); break; case 'admin': case 'super_admin': header('Location: admin/dashboard.php'); break; default: header('Location: login.php'); }
                exit;
            }
        }
    }
}
auth_page_start('Sign in','Your medical journey','Learn with purpose. Practise with confidence.','Access your applications, admission documents and next steps in one secure place.','mc-login-page');
?>
<div class="mc-auth-top"><span>New to Medcon?</span><a class="mc-text-button" href="signup.php">Create account</a></div>
<div class="mc-form-heading"><span class="mc-kicker">Student portal</span><h1>Welcome back</h1><p>Sign in to continue your medical education journey.</p></div>
<?php if ($error): ?><div class="mc-errors"><?= esc($error) ?></div><?php endif; ?>
<form method="post" class="mc-auth-form" novalidate>
    <?= csrf_field() ?>
    <label><span>Email address</span><input type="email" name="email" autocomplete="email" required value="<?= esc($_POST['email'] ?? '') ?>"></label>
    <label><span>Password</span><input type="password" name="password" autocomplete="current-password" required></label>
    <div class="mc-form-assist"><a href="forgot-password.php">Forgot password?</a></div>
    <button class="mc-btn-primary" type="submit">Sign in <span aria-hidden="true">→</span></button>
</form>
<p class="mc-mobile-switch">No account? <a href="signup.php">Create your student account</a></p>
<?php auth_page_end(); ?>
