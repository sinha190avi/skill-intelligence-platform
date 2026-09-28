<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail-config.php';

header('Content-Type: application/json; charset=UTF-8');

$to = $_GET['to'] ?? $_POST['to'] ?? SMTP_USER;

if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please provide a valid recipient email via ?to=your_email@domain.com',
        'current_smtp_config' => [
            'host' => SMTP_HOST,
            'port' => SMTP_PORT,
            'secure' => SMTP_SECURE,
            'user' => !empty(SMTP_USER) ? substr(SMTP_USER, 0, 3) . '***' : '(NOT SET)',
            'pass' => !empty(SMTP_PASS) ? '****** (CONFIGURED)' : '(NOT SET)',
            'from_name' => SMTP_FROM_NAME
        ]
    ], JSON_PRETTY_PRINT);
    exit;
}

$testOtp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
$result = sendOtpMail($to, 'Test Recipient', $testOtp, 'login');

echo json_encode([
    'diagnostic_result' => $result,
    'recipient' => $to,
    'test_otp' => $testOtp,
    'smtp_host' => SMTP_HOST,
    'smtp_port' => SMTP_PORT,
    'timestamp' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);
