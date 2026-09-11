<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/site-activity.php';
header('Cache-Control: no-store');
header('Content-Type: application/json');
function activity_reject(int $status): void { http_response_code($status); exit; }
$method = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($method, ['GET','POST'], true)) { header('Allow: GET, POST'); activity_reject(405); }
$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if ($agent === '' || preg_match('/bot|crawler|spider|headless|lighthouse|uptime|preview|curl|wget/i', $agent)) activity_reject(403);
if (isset($_SERVER['HTTP_SEC_FETCH_SITE']) && $_SERVER['HTTP_SEC_FETCH_SITE'] !== 'same-origin') activity_reject(403);
$referer = parse_url($_SERVER['HTTP_REFERER'] ?? '');
$host = parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
if (!is_array($referer) || strcasecmp($referer['host'] ?? '', $host ?? '') !== 0) activity_reject(403);
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$refPath = $referer['path'] ?? '';
if ($base !== '' && strpos($refPath, $base . '/') !== 0) activity_reject(403);
$page = site_activity_page(substr($refPath, strlen($base)));
if ($page === null) activity_reject(422);
try {
    $db = site_activity_db();
    $secret = $db->query("SELECT value FROM settings WHERE name='secret'")->fetchColumn();
    $cookie = $_COOKIE['medcon_visitor'] ?? '';
    $parts = explode('.', $cookie);
    $valid = count($parts) === 2 && preg_match('/^[a-f0-9]{64}$/D', $parts[0]) && hash_equals(hash_hmac('sha256', $parts[0], $secret), $parts[1]);
    if (!$valid && $method === 'POST') activity_reject(403);
    $visitor = $valid ? $parts[0] : bin2hex(random_bytes(32));
    $visitorHash = hash_hmac('sha256', 'visitor:' . $visitor, $secret);
    if ($method === 'GET') {
        if (!$valid) setcookie('medcon_visitor', $visitor . '.' . hash_hmac('sha256', $visitor, $secret), ['expires'=>time()+365*86400,'path'=>$base . '/','secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),'httponly'=>true,'samesite'=>'Lax']);
        $payload = base64_encode(json_encode(['visitor'=>$visitorHash,'page'=>$page,'expires'=>time()+3600,'view'=>bin2hex(random_bytes(16))]));
        echo json_encode(['token'=>$payload . '.' . hash_hmac('sha256', $payload, $secret)]); exit;
    }
    if (strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0])) !== 'application/json') activity_reject(415);
    $raw = file_get_contents('php://input', false, null, 0, 4097);
    if (strlen($raw)>4096) activity_reject(413);
    $input = json_decode($raw, true);
    if (!is_array($input) || !is_string($input['token'] ?? null)) activity_reject(422);
    $tokenParts = explode('.', $input['token']);
    if (count($tokenParts)!==2 || !hash_equals(hash_hmac('sha256', $tokenParts[0], $secret), $tokenParts[1])) activity_reject(403);
    $token = json_decode(base64_decode($tokenParts[0], true) ?: '', true);
    if (!is_array($token) || $token['expires']<time() || $token['visitor']!==$visitorHash || $token['page']!==$page) activity_reject(403);
    $kind = $input['kind'] ?? '';
    if (!in_array($kind, ['view','click'], true)) activity_reject(422);
    $id = $kind === 'view' ? $token['view'] : ($input['id'] ?? '');
    $button = $kind === 'click' ? ($input['button'] ?? '') : '';
    if (!is_string($id) || !preg_match('/^[a-zA-Z0-9-]{16,80}$/D', $id) || !is_string($button) || strlen($button)>160 || ($kind==='click' && !preg_match('/^[a-zA-Z0-9_.:# -]{1,160}$/D', $button))) activity_reject(422);
    site_activity_record($db, ['id'=>$id,'visitor'=>$visitorHash,'page'=>$page,'kind'=>$kind,'button'=>$button]);
    http_response_code(204);
} catch (Throwable $error) { error_log('Internal site analytics unavailable.'); activity_reject(503); }
