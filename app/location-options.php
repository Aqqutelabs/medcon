<?php
require_once __DIR__.'/includes/functions.php';
require_once __DIR__.'/includes/profile-options.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=86400');
echo json_encode(nigeria_locations(),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
