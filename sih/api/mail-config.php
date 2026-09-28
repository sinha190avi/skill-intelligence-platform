<?php

if (file_exists(__DIR__ . '/mail-local.php')) {
    require_once __DIR__ . '/mail-local.php';
}

if (!defined('SMTP_HOST'))       define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
if (!defined('SMTP_PORT'))       define('SMTP_PORT', (int)(getenv('SMTP_PORT') ?: 587));
if (!defined('SMTP_SECURE'))     define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls'); 
if (!defined('SMTP_USER'))       define('SMTP_USER', getenv('SMTP_USER') ?: '');
if (!defined('SMTP_PASS'))       define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: SMTP_USER);
if (!defined('SMTP_FROM_NAME'))  define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'Commit & Conquer');

function sendOtpMail($recipientEmail, $recipientName, $otpCode, $purpose = 'login') {
    $phpmailerPath = __DIR__ . '/../lib/PHPMailer/src/';
    if (!file_exists($phpmailerPath . 'PHPMailer.php')) {
        $altPaths = [
            __DIR__ . '/../../lib/PHPMailer/src/',
            dirname(__DIR__, 2) . '/lib/PHPMailer/src/',
            dirname(__DIR__) . '/lib/PHPMailer/src/'
        ];
        foreach ($altPaths as $p) {
            if (file_exists($p . 'PHPMailer.php')) {
                $phpmailerPath = $p;
                break;
            }
        }
    }

    if (!file_exists($phpmailerPath . 'PHPMailer.php')) {
        return [
            'success' => false,
            'configured' => false,
            'message' => 'PHPMailer library not found at ' . $phpmailerPath,
            'error'   => 'PHPMailer missing'
        ];
    }

    require_once $phpmailerPath . 'Exception.php';
    require_once $phpmailerPath . 'PHPMailer.php';
    require_once $phpmailerPath . 'SMTP.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        
        if (empty(SMTP_USER) || empty(SMTP_PASS)) {
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
            $logEntry = date('Y-m-d H:i:s') . " | To: $recipientEmail | OTP: $otpCode | Purpose: $purpose\n";
            @file_put_contents($logDir . '/otp_mock.log', $logEntry, FILE_APPEND);

            return [
                'success'    => true, 
                'mock'       => true,
                'configured' => false,
                'message'    => 'SMTP credentials not configured yet. Code generated: ' . $otpCode . '. Please configure api/mail-config.php for real delivery.',
                'dev_otp'    => $otpCode
            ];
        }

        
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        if (SMTP_SECURE === 'ssl' || SMTP_PORT == 465) {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = (int)SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 15;

        
        $fromEmail = !empty(SMTP_FROM_EMAIL) ? SMTP_FROM_EMAIL : SMTP_USER;
        $mail->setFrom($fromEmail, SMTP_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName ?: 'Engineer');
        $mail->addReplyTo($fromEmail, SMTP_FROM_NAME);

        
        $mail->isHTML(true);
        $mail->Subject = "Your Verification Code: $otpCode - Commit & Conquer";

        
        $digits = str_split($otpCode);
        $digitBoxes = '';
        foreach ($digits as $d) {
            $digitBoxes .= '<span style="display:inline-block;margin:0 4px;padding:12px 16px;background:rgba(99,102,241,0.18);border:1px solid #6366f1;border-radius:10px;font-size:28px;font-family:\'Courier New\',Courier,monospace;font-weight:800;color:#38bdf8;text-shadow:0 0 10px rgba(56,189,248,0.6);">' . htmlspecialchars($d) . '</span>';
        }

        $purposeText = ($purpose === 'register') ? 'complete your registration' : 'sign in to your account';

        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Verification Code</title>
</head>
<body style="margin:0;padding:0;background-color:#070414;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#e2e8f0;">
  <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#070414;padding:40px 10px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width:540px;background:#0f0920;border:1px solid rgba(99,102,241,0.3);border-radius:18px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.6);">
          <!-- Header -->
          <tr>
            <td style="padding:32px 36px;text-align:center;background:linear-gradient(135deg,rgba(79,70,229,0.25) 0%,rgba(139,92,246,0.15) 100%);border-bottom:1px solid rgba(99,102,241,0.2);">
              <h1 style="margin:0 0 6px 0;font-size:24px;font-weight:800;letter-spacing:1px;color:#818cf8;">COMMIT &amp; CONQUER</h1>
              <p style="margin:0;font-size:13px;color:#94a3b8;letter-spacing:0.5px;">Skill Intelligence Platform</p>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:36px 36px 24px 36px;text-align:center;">
              <h2 style="margin:0 0 14px 0;font-size:20px;font-weight:700;color:#f8fafc;">Verify Your Identity</h2>
              <p style="margin:0 0 28px 0;font-size:14px;line-height:1.6;color:#94a3b8;">
                Use the one-time verification code below to {$purposeText}. This code is valid for <strong style="color:#38bdf8;">10 minutes</strong>.
              </p>
              
              <!-- OTP Box -->
              <div style="margin:20px 0 30px 0;padding:22px 14px;background:#070414;border:1px dashed rgba(99,102,241,0.4);border-radius:14px;">
                {$digitBoxes}
              </div>

              <p style="margin:0 0 10px 0;font-size:12.5px;color:#64748b;">
                Never share this code with anyone. Platform admins will never ask for your OTP.
              </p>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="padding:20px 36px;background:#090618;border-top:1px solid rgba(99,102,241,0.15);text-align:center;">
              <p style="margin:0;font-size:11.5px;color:#475569;">
                If you did not request this email, please ignore it or secure your account.
              </p>
              <p style="margin:6px 0 0 0;font-size:11px;color:#334155;">
                &copy; Skill Intelligence Platform &bull; Enterprise AI Engineering
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        $mail->AltBody = "Your verification code is: $otpCode (valid for 10 minutes). Do not share this code with anyone.";

        $mail->send();
        return [
            'success'    => true,
            'configured' => true,
            'message'    => 'OTP sent successfully to ' . $recipientEmail
        ];
    } catch (\Exception $e) {
        return [
            'success'    => false,
            'configured' => true,
            'message'    => 'Failed to send email: ' . $mail->ErrorInfo,
            'error'      => $mail->ErrorInfo,
            'dev_otp'    => $otpCode
        ];
    }
}
