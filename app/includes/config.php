<?php
// Database and app configuration - update with your values
return [
    'db_host' => '127.0.0.1',
    'db_name' => 'farmyksn_medcon',
    'db_user' => 'root',
    'db_pass' => '',
    'base_url' => '/app', // used for redirects
    // Local cPanel storage. Keep document records as storage keys so this can
    // later be replaced by a Supabase Storage adapter without changing pages.
    'document_storage' => [
        'driver' => 'local',
        'root' => dirname(__DIR__, 2) . '/docs',
        'url_prefix' => '/docs',
        'max_bytes' => 5 * 1024 * 1024,
    ],
];
