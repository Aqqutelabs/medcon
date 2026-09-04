<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}require dirname(__DIR__).'/app/includes/db.php';$sql=file_get_contents(dirname(__DIR__).'/app/migrations/20260830_public_inquiries.sql');if($sql===false)throw new RuntimeException('Inquiry migration file is unavailable.');$pdo->exec($sql);echo "Public inquiries migration applied.\n";
