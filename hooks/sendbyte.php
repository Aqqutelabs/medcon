<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/includes/email-config.php';
require_once __DIR__ . '/../app/includes/sendbyte-webhook.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$config = medcon_email_config();
$secret = (string) ($config['webhook_secret'] ?? '');

if ($secret === '') {
    http_response_code(503);
    echo json_encode(['error' => 'Webhook signing secret is not configured']);
    exit;
}

$contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
if ($contentLength > 1048576) {
    http_response_code(413);
    echo json_encode(['error' => 'Payload too large']);
    exit;
}

$rawBody = file_get_contents('php://input');
$signature = (string) ($_SERVER['HTTP_SENDBYTE_SIGNATURE'] ?? '');

if (!is_string($rawBody) || !medcon_verify_sendbyte_webhook($rawBody, $signature, $secret)) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid webhook signature']);
    exit;
}

$event = json_decode($rawBody, true);
if (!is_array($event) || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

// Signature-valid delivery, bounce, complaint, open, and click events are
// acknowledged here. Event-specific processing can be added when required.
http_response_code(200);
echo json_encode(['received' => true]);

