<?php
/**
 * Cloudflare Setup Script
 * Run: php scripts/setup-cloudflare.php
 * 
 * Creates all Cloudflare firewall rules, rate limits, and caching rules
 */

// Load environment and config
require_once __DIR__ . '/../app/includes/config.php';
require_once __DIR__ . '/../app/includes/cloudflare.php';
require_once __DIR__ . '/../app/includes/cloudflare-api.php';

// Parse arguments
$action = $argv[1] ?? 'help';
$force = in_array('--force', $argv);

// Color output for CLI
class CliColor {
    const RED = "\033[31m";
    const GREEN = "\033[32m";
    const YELLOW = "\033[33m";
    const BLUE = "\033[34m";
    const RESET = "\033[0m";
    
    public static function success($msg) { echo self::GREEN . "✓ $msg" . self::RESET . "\n"; }
    public static function error($msg) { echo self::RED . "✗ $msg" . self::RESET . "\n"; }
    public static function info($msg) { echo self::BLUE . "ℹ $msg" . self::RESET . "\n"; }
    public static function warn($msg) { echo self::YELLOW . "⚠ $msg" . self::RESET . "\n"; }
}

function print_help() {
    echo <<<'HELP'
Cloudflare Setup Script

Usage:
  php scripts/setup-cloudflare.php <action> [options]

Actions:
  setup              Create all Cloudflare rules and settings
  test               Test Cloudflare API connection
  purge-cache        Purge Cloudflare cache
  show-rules         Display configured rules
  help               Show this help message

Options:
  --force            Skip confirmation prompts

Examples:
  php scripts/setup-cloudflare.php setup
  php scripts/setup-cloudflare.php test
  php scripts/setup-cloudflare.php purge-cache --force

HELP;
}

function test_cloudflare_connection() {
    CliColor::info("Testing Cloudflare API connection...");
    
    $cf = get_cloudflare_api();
    if (!$cf) {
        CliColor::error("Cloudflare credentials not found in environment.");
        CliColor::info("Make sure CLOUDFLARE_API_KEY, CLOUDFLARE_ZONE_ID, and CLOUDFLARE_EMAIL are set.");
        return false;
    }
    
    $result = $cf->get_zone_info();
    
    if (isset($result['success']) && $result['success']) {
        CliColor::success("Connected to Cloudflare!");
        CliColor::info("Zone: " . ($result['result']['name'] ?? 'Unknown'));
        CliColor::info("Status: " . ($result['result']['status'] ?? 'Unknown'));
        return true;
    } else {
        CliColor::error("Failed to connect to Cloudflare.");
        if (isset($result['errors'])) {
            foreach ($result['errors'] as $error) {
                CliColor::error("  - " . ($error['message'] ?? 'Unknown error'));
            }
        }
        return false;
    }
}

function setup_cloudflare_rules($force = false) {
    CliColor::info("Setting up Cloudflare protection...");
    
    if (!$force) {
        echo "This will create firewall rules, rate limits, and cache settings. Continue? (y/n): ";
        $response = trim(fgets(STDIN));
        if ($response !== 'y') {
            CliColor::warn("Setup cancelled.");
            return;
        }
    }
    
    $cf = get_cloudflare_api();
    if (!$cf) {
        CliColor::error("Cloudflare API not initialized. Check your .env.cloudflare file.");
        return;
    }
    
    // Test connection first
    if (!test_cloudflare_connection()) {
        return;
    }
    
    echo "\n";
    
    // 1. Create bypass rule for login (highest priority)
    CliColor::info("Creating bypass rule for login pages...");
    $result = $cf->create_bypass_rule('/app/login');
    if (isset($result['success']) && $result['success']) {
        CliColor::success("Login bypass rule created");
    } else {
        CliColor::warn("Could not create login bypass rule: " . json_encode($result));
    }
    
    // 2. Create rate limiting rules for protected pages
    $rate_limits = [
        '/app/signup' => ['requests' => 5, 'window' => 3600],
        '/contact.php' => ['requests' => 3, 'window' => 3600],
        '/eligibility-checker.php' => ['requests' => 10, 'window' => 3600],
        '/app/student/submit-student' => ['requests' => 2, 'window' => 3600],
    ];
    
    echo "\n";
    CliColor::info("Creating rate limiting rules...");
    foreach ($rate_limits as $path => $limit) {
        CliColor::info("  Setting: $path ({$limit['requests']} requests per {$limit['window']}s)");
        $result = $cf->create_rate_limit_rule($path, $limit['requests'], $limit['window']);
        if (isset($result['success']) && $result['success']) {
            CliColor::success("Rate limit rule created for $path");
        } else {
            CliColor::warn("Could not create rate limit for $path");
        }
    }
    
    // 3. Create caching rules
    $cache_rules = [
        '/eligibility-checker.php' => 300,
        '/app/admin/agents' => 600,
        '/app/agent/dashboard' => 300,
    ];
    
    echo "\n";
    CliColor::info("Creating cache rules...");
    foreach ($cache_rules as $path => $ttl) {
        CliColor::info("  Setting: $path (TTL: {$ttl}s)");
        $result = $cf->create_cache_rule($path, $ttl);
        if (isset($result['success']) && $result['success']) {
            CliColor::success("Cache rule created for $path");
        } else {
            CliColor::warn("Could not create cache rule for $path");
        }
    }
    
    // 4. Enable security features
    echo "\n";
    CliColor::info("Enabling security features...");
    
    $result = $cf->enable_ddos_protection();
    if (isset($result['success']) && $result['success']) {
        CliColor::success("DDoS protection enabled");
    } else {
        CliColor::warn("Could not enable DDoS protection");
    }
    
    $result = $cf->enable_waf();
    if (isset($result['success']) && $result['success']) {
        CliColor::success("Web Application Firewall (WAF) enabled");
    } else {
        CliColor::warn("Could not enable WAF");
    }
    
    echo "\n";
    CliColor::success("Cloudflare setup complete!");
    CliColor::info("Please verify settings in the Cloudflare Dashboard:");
    CliColor::info("  https://dash.cloudflare.com/");
}

function purge_cloudflare_cache($force = false) {
    CliColor::info("Preparing to purge Cloudflare cache...");
    
    $paths = [
        'https://medcon.com/eligibility-checker.php',
        'https://medcon.com/app/signup',
        'https://medcon.com/contact.php',
        'https://medcon.com/app/admin/agents',
        'https://medcon.com/app/agent/dashboard',
    ];
    
    if (!$force) {
        echo "This will purge cache for " . count($paths) . " paths. Continue? (y/n): ";
        $response = trim(fgets(STDIN));
        if ($response !== 'y') {
            CliColor::warn("Purge cancelled.");
            return;
        }
    }
    
    $cf = get_cloudflare_api();
    if (!$cf) {
        CliColor::error("Cloudflare API not initialized.");
        return;
    }
    
    CliColor::info("Purging cache...");
    $result = $cf->purge_cache($paths);
    
    if (isset($result['success']) && $result['success']) {
        CliColor::success("Cache purged successfully!");
        CliColor::info("Purged " . count($paths) . " paths");
    } else {
        CliColor::error("Failed to purge cache");
        if (isset($result['errors'])) {
            foreach ($result['errors'] as $error) {
                CliColor::error("  - " . ($error['message'] ?? 'Unknown error'));
            }
        }
    }
}

function show_configured_rules() {
    echo <<<'RULES'
═══════════════════════════════════════════════════════════════
  Configured Cloudflare Rules for MedCon
═══════════════════════════════════════════════════════════════

PROTECTED PAGES (with challenges & caching):
  ✓ /app/signup                  - 5 requests/hour
  ✓ /app/student/*               - Application forms
  ✓ /contact.php                 - 3 requests/hour
  ✓ /eligibility-checker.php     - 10 requests/hour, cache 5m
  ✓ /app/admin/agents            - Cache 10 minutes
  ✓ /app/agent/dashboard         - Cache 5 minutes

EXCLUDED PAGES (no Cloudflare challenges):
  ✗ /app/login                   - No rate limiting
  ✗ /app/logout                  - No rate limiting
  ✗ /app/forgot-password         - No rate limiting
  ✗ /app/reset-password          - No rate limiting
  ✗ /app/verify-email            - No rate limiting
  ✗ /app/resend-verification     - No rate limiting

SECURITY HEADERS (applied globally):
  • Strict-Transport-Security: max-age=31536000
  • X-Frame-Options: SAMEORIGIN
  • X-Content-Type-Options: nosniff
  • Content-Security-Policy: strict
  • Referrer-Policy: strict-origin-when-cross-origin

═══════════════════════════════════════════════════════════════

RULES;
}

// Main execution
match($action) {
    'setup' => setup_cloudflare_rules($force),
    'test' => test_cloudflare_connection(),
    'purge-cache' => purge_cloudflare_cache($force),
    'show-rules' => show_configured_rules(),
    'help' => print_help(),
    default => print_help(),
};
