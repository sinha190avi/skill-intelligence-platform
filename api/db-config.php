<?php

$isLocal = in_array($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']) || (php_sapi_name() === 'cli' && gethostbyname('sql107.infinityfree.com') === 'sql107.infinityfree.com');

if ($isLocal) {
    $db_host = 'localhost';
    $db_port = 3306;
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'skill_intelligence';
} else {
    $db_host = 'sql107.infinityfree.com';
    $db_port = 3306;
    $db_user = 'if0_43025695';
    $db_pass = 'GAJAGRAM123';
    $db_name = 'if0_43025695_sih';
}

