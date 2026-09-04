<?php
// Common helpers
require_once dirname(__DIR__, 2) . '/includes/links.php';

if (session_status() === PHP_SESSION_NONE) {
    // Secure session settings
    ini_set('session.use_strict_mode', 1);
    $sessionPath = dirname(__DIR__, 2) . '/data/sessions';
    if (is_dir($sessionPath) && is_writable($sessionPath)) {
        session_save_path($sessionPath);
    }
    session_start();
}

// Load Cloudflare protection after the secure session is ready for rate limiting.
require_once dirname(__FILE__) . '/cloudflare.php';

function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function medcon_base_path(): string {
    static $base = null;
    if ($base !== null) return $base;
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $position = strpos($script, '/app/');
    $base = $position === false ? '' : rtrim(substr($script, 0, $position), '/');
    return $base;
}

function app_url(string $path = ''): string {
    return medcon_base_path() . '/app' . ($path === '' ? '' : '/' . ltrim($path, '/'));
}

if (!function_exists('site_url')) {
    function site_url(string $path = ''): string {
        return medcon_base_path() . '/' . ltrim($path, '/');
    }
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

function application_status_label(?string $status): string {
    return $status ? ucwords(str_replace('_', ' ', $status)) : 'Not started';
}
