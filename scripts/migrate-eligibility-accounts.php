<?php
declare(strict_types=1);if(PHP_SAPI!=='cli'){http_response_code(404);exit;}require dirname(__DIR__).'/app/includes/db.php';$sql=file_get_contents(dirname(__DIR__).'/app/migrations/20260830_eligibility_accounts.sql');if($sql===false)throw new RuntimeException('Eligibility account migration is unavailable.');$pdo->exec($sql);echo "Eligibility account migration applied.\n";
