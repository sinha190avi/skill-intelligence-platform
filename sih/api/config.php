<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400 * 30,  
        'path'     => '/',
        'secure'   => false,       
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-User-Id, X-User-Email');
header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (empty($_SESSION['user_id']) && !empty($_SERVER['HTTP_X_USER_ID'])) {
    $_SESSION['user_id'] = (int)$_SERVER['HTTP_X_USER_ID'];
}

$db_host = 'localhost';
$db_port = 3306;
$db_user = 'root';
$db_pass = '';
$db_name = 'skill_intelligence';

if (file_exists(__DIR__ . '/db-config.php')) {
    require_once __DIR__ . '/db-config.php';
}

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    
    if ($e->getCode() == 1049) {
        require_once __DIR__ . '/setup_db.php';
        if (file_exists(__DIR__ . '/setup_extended.php')) {
            require_once __DIR__ . '/setup_extended.php';
        }
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (Exception $err) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $err->getMessage()]);
            exit;
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function jsonError($message, $code = 400, $extra = []) {
    http_response_code($code);
    $res = array_merge(['status' => 'error', 'message' => $message], $extra);
    echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getRequestBody() {
    $raw = file_get_contents('php://input');
    if (!$raw) return $_POST;
    $json = json_decode($raw, true);
    return is_array($json) ? $json : $_POST;
}
