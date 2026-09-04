<?php
declare(strict_types=1);

function medcon_mail_config(): array
{
    static $resolved;
    if (is_array($resolved)) return $resolved;

    $appRoot = dirname(__DIR__, 2);
    $appConfig = require $appRoot . '/app/includes/config.php';
    $candidates = [];
    $environmentPath = trim((string) getenv('MEDCON_SENDBYTE_CONFIG'));
    if ($environmentPath !== '') $candidates[] = $environmentPath;
    if (!empty($appConfig['sendbyte_config'])) $candidates[] = (string) $appConfig['sendbyte_config'];
    $candidates[] = dirname($appRoot) . '/medcon_private/sendbyte_config.php';

    $private = [];
    foreach (array_unique($candidates) as $candidate) {
        if (is_file($candidate) && is_readable($candidate)) {
            $value = require $candidate;
            if (is_array($value)) { $private = $value; break; }
        }
    }

    $apiKey = trim((string) ($private['api_key'] ?? getenv('SENDBYTE_API_KEY') ?: ''));
    $mode = str_starts_with($apiKey, 'sk_test_') ? 'test' : (str_starts_with($apiKey, 'sk_live_') ? 'live' : 'unconfigured');
    $baseUrl = rtrim((string) ($appConfig['public_url'] ?? getenv('MEDCON_PUBLIC_URL') ?: ''), '/');
    if ($baseUrl === '') {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $host = preg_replace('/[^A-Za-z0-9.:-]/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $baseUrl = ($https ? 'https' : 'http') . '://' . $host . rtrim(dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/MedCon/index.php')), '/\\');
        $baseUrl = preg_replace('#/app(?:/.*)?$#', '', $baseUrl);
    }

    return $resolved = [
        'api_key' => $apiKey,
        'mode' => $mode,
        'endpoint' => 'https://api.sendbyte.africa/v1/emails',
        'from_email' => (string) ($private['from_email'] ?? 'notifications@medconedu.com'),
        'from_name' => (string) ($private['from_name'] ?? 'Medcon Edu'),
        'reply_to' => (string) ($private['reply_to'] ?? ''),
        'base_url' => $baseUrl,
    ];
}

