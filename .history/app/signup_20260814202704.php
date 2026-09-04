<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? '')) {
        $errors[] = 'Invalid form submission.';
    }

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $consent = isset($_POST['consent']);

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = 'Please fill in all required fields.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (!$consent) {
        $errors[] = 'You must accept terms to continue.';
    }

    // Check unique email
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'An account with that email already exists.';
    }

    if (empty($errors)) {
        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        $now = date('Y-m-d H:i:s');

        $insert = $pdo->prepare('INSERT INTO users (role, first_name, last_name, email, phone, country, password_hash, status, email_verified, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute(['student', $first_name, $last_name, $email, $phone, $country, $pw_hash, 'active', 0, $now, $now]);
        $user_id = $pdo->lastInsertId();

        $ins2 = $pdo->prepare('INSERT INTO students (user_id, created_at, updated_at) VALUES (?, ?, ?)');
        $ins2->execute([$user_id, $now, $now]);

        // Log in user
        $_SESSION['user'] = [
            'id' => $user_id,
            'role' => 'student',
            'full_name' => $first_name . ' ' . $last_name,
            'email' => $email,
        ];

        header('Location: /app/student/dashboard.php');
        exit;
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sign up - Medcon Edu</title>
    <link rel="stylesheet" href="/app/assets/css/app.css">
</head>
<body class="mc-bg">
<main class="mc-container">
    <h1 class="mc-title">Create your student account</h1>

    <?php if (!empty($errors)): ?>
        <div class="mc-errors">
            <?php foreach ($errors as $e): ?>
                <div><?php echo esc($e); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" id="signup-form" novalidate>
        <?php echo csrf_field(); ?>
        <label>First name <input name="first_name" required value="<?php echo esc($_POST['first_name'] ?? '')?>"></label>
        <label>Last name <input name="last_name" required value="<?php echo esc($_POST['last_name'] ?? '')?>"></label>
        <label>Email <input type="email" name="email" required value="<?php echo esc($_POST['email'] ?? '')?>"></label>
        <label>Phone <input name="phone" value="<?php echo esc($_POST['phone'] ?? '')?>"></label>
        <label>Country <input name="country" value="<?php echo esc($_POST['country'] ?? '')?>"></label>
        <label>Password <input type="password" name="password" required></label>
        <label>Confirm password <input type="password" name="confirm_password" required></label>
        <label class="mc-consent"><input type="checkbox" name="consent" <?php echo isset($_POST['consent']) ? 'checked' : ''?>> I agree to terms</label>
        <button class="mc-btn-primary" type="submit">Create account</button>
    </form>

    <p>Already have an account? <a href="/app/login.php">Sign in</a></p>
</main>
<script src="/app/assets/js/app.js"></script>
</body>
</html>
