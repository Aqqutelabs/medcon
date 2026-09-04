<?php
// Common helpers
if (session_status() === PHP_SESSION_NONE) {
    // Secure session settings
    ini_set('session.use_strict_mode', 1);
    session_start();
}

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Flash messages
function flash_set($key, $msg) {
    $_SESSION['_flash'][$key] = $msg;
}

function flash_get($key) {
    if (!isset($_SESSION['_flash'][$key])) return null;
    $v = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $v;
}

// CSRF
function csrf_token() {
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field() {
    $t = csrf_token();
    return '<input type="hidden" name="_csrf" value="' . esc($t) . '">';
}

function verify_csrf($token) {
    return hash_equals($_SESSION['_csrf_token'] ?? '', (string)$token);
}
