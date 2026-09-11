<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/calculator-activity.php';
header('Cache-Control: no-store');
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST'); http_response_code(405); exit;
}
// JSON requests require a same-origin fetch; cross-origin preflights are not allowed.
if (strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0])) !== 'application/json'
    || ($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '') === 'cross-site') {
    http_response_code(403); exit;
}
$raw = file_get_contents('php://input', false, null, 0, 2049);
if (strlen($raw) > 2048) { http_response_code(413); exit; }
$input = json_decode($raw, true);
$allowed = [
    'event'=>array_keys(calculator_event_labels()),
    'college'=>['tmtcs','pltci'],
    'background'=>['secondary','other-bachelor','biology-bachelor'],
    'package'=>['basic','premium'],
];
$event = [];
foreach ($allowed as $key=>$values) {
    if (!is_array($input) || !in_array($input[$key] ?? null, $values, true)) { http_response_code(422); exit; }
    $event[$key] = $input[$key];
}
foreach (['id','visit'] as $key) {
    if (!is_string($input[$key] ?? null) || !preg_match('/^[a-zA-Z0-9-]{16,80}$/D', $input[$key])) { http_response_code(422); exit; }
    $event[$key] = $input[$key];
}
try {
    calculator_activity($event);
    http_response_code(204);
} catch (Throwable $error) {
    error_log('Calculator activity could not be saved.');
    http_response_code(503);
}
