<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function current_user() {
    if (!empty($_SESSION['user'])) return $_SESSION['user'];
    return null;
}

function require_login() {
    if (empty($_SESSION['user'])) {
        $_SESSION['_next'] = $_SERVER['REQUEST_URI'];
        header('Location: /app/login.php');
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

function redirect_by_role($role) {
    switch ($role) {
        case 'student':
            header('Location: /app/student/dashboard.php');
            break;
        case 'agent':
            header('Location: /app/agent/dashboard.php');
            break;
        case 'admin':
        case 'super_admin':
            header('Location: /app/admin/dashboard.php');
            break;
        default:
            header('Location: /app/login.php');
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
