<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $error = 'Invalid form submission.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Invalid credentials.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'Invalid credentials.';
            } else if ($user['status'] === 'suspended') {
                $error = 'Your account is suspended.';
            } else {
                // success
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'role' => $user['role'],
                    'full_name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                ];
                $upd = $pdo->prepare('UPDATE users SET last_login_at = ? WHERE id = ?');
                $upd->execute([date('Y-m-d H:i:s'), $user['id']]);

                // redirect
                switch ($user['role']) {
                    case 'student': header('Location: /app/student/dashboard.php'); break;
                    case 'agent': header('Location: /app/agent/dashboard.php'); break;
                    case 'admin':
                    case 'super_admin': header('Location: /app/admin/dashboard.php'); break;
                    default: header('Location: /app/login.php');
                }
                exit;
            }
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sign in - Medcon Edu</title>
    <link rel="stylesheet" href="/app/assets/css/app.css">
</head>
<body class="mc-bg">
<main class="mc-container">
    <h1 class="mc-title">Sign in</h1>
    <?php if ($error): ?>
        <div class="mc-errors"><?php echo esc($error); ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
        <?php echo csrf_field(); ?>
        <label>Email <input type="email" name="email" required value="<?php echo esc($_POST['email'] ?? '')?>"></label>
        <label>Password <input type="password" name="password" required></label>
        <button class="mc-btn-primary" type="submit">Sign in</button>
    </form>
    <p><a href="/app/signup.php">Create account</a> · <a href="#">Forgot password</a></p>
</main>
<script src="/app/assets/js/app.js"></script>
</body>
</html>
