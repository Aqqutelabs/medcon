<?php
$_SERVER['SCRIPT_NAME'] = preg_replace('#/accreditation/index\.php$#', '/accreditation.php', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/accreditation/index.php'));
require dirname(__DIR__) . '/accreditation.php';
