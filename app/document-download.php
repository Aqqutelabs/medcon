<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/document-storage.php';
require_login();

$documentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$documentId) { http_response_code(404); exit('Document not found.'); }
$user = current_user();
$stmt = $pdo->prepare('SELECT d.*,s.user_id FROM documents d JOIN students s ON s.id=d.student_id WHERE d.id=? LIMIT 1');
$stmt->execute([$documentId]);
$document = $stmt->fetch();
if (!$document || ($user['role'] === 'student' && (int)$document['user_id'] !== (int)$user['id']) || !in_array($user['role'], ['student','admin','super_admin'], true)) {
    http_response_code(404); exit('Document not found.');
}
$path = document_storage_path($document['file_path']);
if (!is_file($path)) { http_response_code(404); exit('Document file not found.'); }
$downloadName = preg_replace('/[^A-Za-z0-9._ -]/', '_', $document['file_name'] ?: 'document');
header('Content-Type: ' . ($document['file_type'] ?: 'application/octet-stream'));
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . $downloadName . '"');
header('X-Content-Type-Options: nosniff');
readfile($path);
