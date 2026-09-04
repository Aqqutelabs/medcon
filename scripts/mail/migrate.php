<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__,2).'/app/includes/db.php';
$sql=file_get_contents(dirname(__DIR__,2).'/app/migrations/20260830_phase1_email_notifications.sql');
if($sql===false)throw new RuntimeException('Migration file is unavailable.');
$pdo->exec($sql);echo "Phase 1 email migration applied.\n";
