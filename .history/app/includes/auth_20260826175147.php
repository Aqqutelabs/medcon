<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function crafted_by_aqqute(): void {
    echo '<footer class="mc-portal-footer">Crafted by <a href="https://aqqute.com" target="_blank" rel="noopener noreferrer">Aqqute</a></footer>';
}

function current_user() {
    if (!empty($_SESSION['user'])) return $_SESSION['user'];
    return null;
}

function require_login() {
    if (empty($_SESSION['user'])) {
        $_SESSION['_next'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . app_url('login.php'));
        exit;
    }
}

function require_role(array $roles) {
    require_login();
    $user = current_user();
    if (!in_array($user['role'], $roles)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

function require_approved_agent(PDO $pdo): array {
    require_role(['agent']);
    $user = current_user();
    $stmt = $pdo->prepare('SELECT a.*, u.first_name, u.last_name, u.email, u.phone, u.country FROM agents a JOIN users u ON u.id=a.user_id WHERE a.user_id=? LIMIT 1');
    $stmt->execute([(int)$user['id']]);
    $agent = $stmt->fetch();
    if (!$agent || $agent['approval_status'] !== 'approved') {
        http_response_code(403);
        exit('Your agent account is awaiting approval.');
    }
    return $agent;
}

function redirect_by_role($role) {
    switch ($role) {
        case 'student':
            header('Location: ' . app_url('student/dashboard.php'));
            break;
        case 'agent':
            header('Location: ' . app_url('agent/dashboard.php'));
            break;
        case 'admin':
        case 'super_admin':
            header('Location: ' . app_url('admin/dashboard.php'));
            break;
        default:
            header('Location: ' . app_url('login.php'));
    }
    exit;
}

function logout_user() {
    // clear and destroy session
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
