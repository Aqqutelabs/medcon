<?php
/**
 * Cloudflare Configuration & Protection
 * Handles protection rules, caching, and rate limiting
 */

// ============================================================================
// CLOUDFLARE CONFIGURATION
// ============================================================================

// Read a local, untracked environment file for local development. Production
// should provide these values through the web server environment instead.
if (getenv('CLOUDFLARE_ENABLED') === false) {
    $cloudflare_env_file = dirname(__DIR__, 2) . '/.env.cloudflare';
    if (is_readable($cloudflare_env_file)) {
        $cloudflare_values = parse_ini_file($cloudflare_env_file, false, INI_SCANNER_RAW) ?: [];
        foreach ($cloudflare_values as $name => $value) {
            if (is_string($value) && getenv($name) === false) {
                putenv($name . '=' . trim($value, " \t\n\r\0\x0B\"'"));
            }
        }
    }
}

$cloudflare_enabled = filter_var(getenv('CLOUDFLARE_ENABLED') ?: 'true', FILTER_VALIDATE_BOOLEAN);

define('CLOUDFLARE_CONFIG', [
    'enabled' => $cloudflare_enabled,
    'api_key' => getenv('CLOUDFLARE_API_KEY') ?: '',
    'zone_id' => getenv('CLOUDFLARE_ZONE_ID') ?: '',
    'email' => getenv('CLOUDFLARE_EMAIL') ?: '',
    
    // Pages that should have full Cloudflare protection
    'protected_pages' => [
        '/app/login',
        '/app/login.php',
        '/login.php',
        '/app/signup',
        '/app/signup.php',
        '/signup.php',
        '/app/student/application-start',
        '/app/student/submit-student',
        '/contact.php',
        '/for-agents.php',
        '/eligibility-checker.php',
        '/app/admin/agents',
        '/app/admin/agents.php',
        '/app/admin/leads',
        '/app/agent/dashboard',
        '/app/agent/dashboard.php',
        '/app/agent/submit-student',
        '/app/agent/submit-student.php',
    ],
    
    // Pages to EXCLUDE from Cloudflare protection
    'excluded_pages' => [
        '/app/logout',
        '/app/verify-email',
        '/app/resend-verification',
        '/app/forgot-password',
        '/app/reset-password',
    ],
    
    // Rate limiting rules (per page or pattern)
    'rate_limits' => [
        '/app/signup' => ['requests' => 5, 'window' => 3600], // 5 requests per hour
        '/app/signup.php' => ['requests' => 5, 'window' => 3600],
        '/app/login.php' => ['requests' => 10, 'window' => 900],
        '/contact.php' => ['requests' => 3, 'window' => 3600], // 3 requests per hour
        '/for-agents.php' => ['requests' => 3, 'window' => 3600],
        '/eligibility-checker.php' => ['requests' => 10, 'window' => 3600], // 10 requests per hour
        '/app/student/submit-student' => ['requests' => 2, 'window' => 3600], // 2 submissions per hour
        '/app/agent/submit-student.php' => ['requests' => 2, 'window' => 3600],
    ],
    
    // Caching rules (in seconds)
    'cache_ttl' => [
        '/eligibility-checker.php' => 300, // 5 minutes
        '/app/admin/agents' => 600, // 10 minutes
        '/app/agent/dashboard' => 300, // 5 minutes
    ],
    
    // Cache static assets longer
    'static_cache_ttl' => 86400, // 24 hours for CSS, JS, images
]);

// ============================================================================
// PROTECTION & CACHING FUNCTIONS
// ============================================================================

/**
 * Check if current page should have Cloudflare protection
 * @return bool
 */
function should_apply_cloudflare_protection(): bool {
    $current_uri = $_SERVER['REQUEST_URI'] ?? '';
    
    // Remove query string for path checking
    $path = parse_url($current_uri, PHP_URL_PATH);
    
    // Check excluded pages first (highest priority)
    foreach (CLOUDFLARE_CONFIG['excluded_pages'] as $excluded) {
        if (stripos($path, $excluded) === 0) {
            return false;
        }
    }
    
    // Check protected pages
    foreach (CLOUDFLARE_CONFIG['protected_pages'] as $protected) {
        if ($path === $protected || stripos($path, rtrim($protected, '/') . '/') === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get cache TTL for current page
 * @return int|null TTL in seconds, or null for no caching
 */
function get_page_cache_ttl(): ?int {
    $current_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($current_uri, PHP_URL_PATH);
    
    // Check if path matches any cache rule
    foreach (CLOUDFLARE_CONFIG['cache_ttl'] as $pattern => $ttl) {
        if (stripos($path, $pattern) === 0) {
            return $ttl;
        }
    }
    
    return null;
}

/**
 * Get rate limit for current page
 * @return array|null ['requests' => int, 'window' => int] or null
 */
function get_page_rate_limit(): ?array {
    $current_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($current_uri, PHP_URL_PATH);
    
    foreach (CLOUDFLARE_CONFIG['rate_limits'] as $pattern => $limit) {
        if (stripos($path, $pattern) === 0) {
            return $limit;
        }
    }
    
    return null;
}

/**
 * Apply caching headers for the current page
 */
function apply_cache_headers(): void {
    $ttl = get_page_cache_ttl();
    
    if ($ttl !== null) {
        header('Cache-Control: public, max-age=' . $ttl);
        header('Expires: ' . gmdate('D, d M Y H:i:s T', time() + $ttl));
        header('Pragma: cache');
    } else {
        // Default: don't cache sensitive pages
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: ' . gmdate('D, d M Y H:i:s T', 0));
    }
}

/**
 * Apply Cloudflare-friendly caching headers
 */
function apply_cloudflare_cache_headers(): void {
    if (!CLOUDFLARE_CONFIG['enabled']) {
        return;
    }
    
    $ttl = get_page_cache_ttl();
    
    if ($ttl !== null) {
        // Tell Cloudflare to cache this page
        header('Cache-Control: public, max-age=' . $ttl . ', s-maxage=' . $ttl);
        header('CF-Cache-Tag: medcon-' . str_replace('/', '-', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')));
        header('Vary: Accept-Encoding');
    }
}

/**
 * Rate limiting - track and enforce requests per time window
 * Uses session to track requests (for local dev; Cloudflare handles in production)
 * @return bool False if rate limit exceeded
 */
function check_rate_limit(): bool {
    if (!CLOUDFLARE_CONFIG['enabled']) {
        return true;
    }
    
    $limit = get_page_rate_limit();
    if ($limit === null) {
        return true; // No rate limit configured for this page
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Get or create rate limit tracking in session
    if (!isset($_SESSION['_rate_limit'])) {
        $_SESSION['_rate_limit'] = [];
    }
    
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $key = 'limit_' . hash('sha256', $path);
    $now = time();
    
    // Initialize or check existing entries
    if (!isset($_SESSION['_rate_limit'][$key])) {
        $_SESSION['_rate_limit'][$key] = ['count' => 1, 'window_start' => $now];
        return true;
    }
    
    $entry = &$_SESSION['_rate_limit'][$key];
    
    // Check if we're still in the same window
    if ($now - $entry['window_start'] > $limit['window']) {
        // New window started
        $entry = ['count' => 1, 'window_start' => $now];
        return true;
    }
    
    // Still in same window - increment and check
    $entry['count']++;
    
    if ($entry['count'] > $limit['requests']) {
        // Rate limit exceeded
        header('HTTP/1.1 429 Too Many Requests');
        header('Retry-After: ' . ($limit['window'] - ($now - $entry['window_start'])));
        return false;
    }
    
    return true;
}

/**
 * Get Cloudflare client IP (respects Cloudflare headers)
 * @return string Client IP address
 */
function get_client_ip(): string {
    // Check Cloudflare header first
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    
    // Fallback to other headers
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ips = explode(',', $_SERVER[$key]);
            return trim($ips[0]);
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Verify Cloudflare token (for API requests)
 * @return bool
 */
function verify_cloudflare_token(): bool {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    
    if (!preg_match('/Bearer\s+(.+)/', $auth_header, $matches)) {
        return false;
    }
    
    $token = $matches[1];
    
    // In production, verify the token with Cloudflare or your auth system
    return !empty($token);
}

/**
 * Log rate limit violation
 * @param string $ip Client IP
 * @param string $path Request path
 */
function log_rate_limit_violation(string $ip, string $path): void {
    $log_file = dirname(__DIR__, 2) . '/data/rate-limit-violations.log';
    $log_entry = sprintf(
        "[%s] IP: %s | Path: %s | User-Agent: %s\n",
        date('Y-m-d H:i:s'),
        $ip,
        $path,
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    );
    
    error_log($log_entry, 3, $log_file);
}

/**
 * Add Cloudflare security headers
 */
function add_cloudflare_security_headers(): void {
    // Strict Transport Security
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    
    // X-Frame-Options
    header('X-Frame-Options: SAMEORIGIN');
    
    // X-Content-Type-Options
    header('X-Content-Type-Options: nosniff');
    
    // Content Security Policy (basic)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' challenges.cloudflare.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;");
    
    // Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Permissions Policy
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

// ============================================================================
// INITIALIZATION
// ============================================================================

/**
 * Initialize Cloudflare protection on page load
 */
function init_cloudflare_protection(): void {
    if (!CLOUDFLARE_CONFIG['enabled']) {
        return;
    }
    
    // Add security headers
    add_cloudflare_security_headers();
    
    // Apply caching headers
    apply_cloudflare_cache_headers();
    
    // Check rate limits for protected pages
    if (should_apply_cloudflare_protection()) {
        if (!check_rate_limit()) {
            $ip = get_client_ip();
            $path = $_SERVER['REQUEST_URI'] ?? '';
            log_rate_limit_violation($ip, $path);
            
            http_response_code(429);
            die('Too many requests. Please try again later.');
        }
    }
}

// Auto-initialize on include
if (PHP_SAPI !== 'cli') {
    init_cloudflare_protection();
}
