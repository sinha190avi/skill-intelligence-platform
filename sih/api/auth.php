<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mail-config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'me';
$body   = getRequestBody();

if ($action === 'me') {
    if (empty($_SESSION['user_id'])) {
        jsonResponse(['status' => 'unauthenticated', 'authenticated' => false, 'message' => 'No active session'], 401);
    }

    $uid = (int)$_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT id, full_name, email, role_title, target_role, department, location, avatar_initials, karma_xp, rank_percentile FROM users WHERE id = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch();

    if (!$user) {
        unset($_SESSION['user_id']);
        jsonResponse(['status' => 'unauthenticated', 'authenticated' => false, 'message' => 'User not found in system'], 401);
    }

    jsonResponse(['status' => 'success', 'authenticated' => true, 'data' => $user]);
}

if ($action === 'send_otp') {
    $email = strtolower(trim($body['email'] ?? ''));
    $purpose = trim($body['purpose'] ?? 'login');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('Please provide a valid email address.');
    }

    
    try {
        $recentStmt = $pdo->prepare("SELECT id, created_at FROM otp_codes WHERE email = ? AND created_at > (NOW() - INTERVAL 45 SECOND) ORDER BY id DESC LIMIT 1");
        $recentStmt->execute([$email]);
        if ($recentStmt->fetch()) {
            jsonError('Please wait 45 seconds before requesting another code.', 429);
        }
    } catch (\Exception $e) {
        
    }

    
    try {
        $pdo->prepare("UPDATE otp_codes SET used = 1 WHERE email = ? AND used = 0")->execute([$email]);
    } catch (\Exception $e) {}

    
    $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? null;

    
    try {
        $insert = $pdo->prepare("INSERT INTO otp_codes (email, otp, purpose, expires_at, ip_address, created_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE), ?, NOW())");
        $insert->execute([$email, $otp, $purpose, $clientIp]);
    } catch (\Exception $e) {
        jsonError('Database error storing verification code: ' . $e->getMessage(), 500);
    }

    
    $userName = '';
    try {
        $userStmt = $pdo->prepare("SELECT full_name FROM users WHERE email = ?");
        $userStmt->execute([$email]);
        $row = $userStmt->fetch();
        if ($row && !empty($row['full_name'])) {
            $userName = $row['full_name'];
        }
    } catch (\Exception $e) {}

    if (empty($userName)) {
        $parts = explode('@', $email);
        $userName = ucwords(str_replace(['.', '_', '-'], ' ', $parts[0]));
    }

    
    $mailResult = sendOtpMail($email, $userName, $otp, $purpose);

    if ($mailResult['success']) {
        $resp = [
            'status'     => 'success',
            'message'    => 'Verification code sent to ' . htmlspecialchars($email),
            'expires_in' => 600,
            'configured' => $mailResult['configured'] ?? true
        ];
        
        if (!empty($mailResult['mock'])) {
            $resp['mock'] = true;
            $resp['dev_otp'] = $otp;
            $resp['message'] .= ' (Notice: SMTP not configured. Dev code: ' . $otp . ')';
        }
        jsonResponse($resp);
    } else {
        
        $resp = [
            'status'     => 'error',
            'message'    => 'Could not send verification email: ' . ($mailResult['error'] ?? $mailResult['message']),
            'configured' => $mailResult['configured'] ?? false
        ];
        if (isset($mailResult['dev_otp'])) {
            $resp['dev_otp'] = $mailResult['dev_otp'];
        }
        jsonResponse($resp, 500);
    }
}

if ($action === 'verify_otp') {
    $email = strtolower(trim($body['email'] ?? ''));
    $otp   = trim($body['otp'] ?? '');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('Please provide a valid email address.');
    }
    if (!$otp || strlen($otp) !== 6 || !ctype_digit($otp)) {
        jsonError('Please enter a valid 6-digit verification code.');
    }

    
    $stmt = $pdo->prepare("SELECT id, attempts, expires_at FROM otp_codes WHERE email = ? AND otp = ? AND used = 0 AND expires_at >= NOW() ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email, $otp]);
    $otpRecord = $stmt->fetch();

    if (!$otpRecord) {
        
        try {
            $pdo->prepare("UPDATE otp_codes SET attempts = attempts + 1 WHERE email = ? AND used = 0 ORDER BY id DESC LIMIT 1")->execute([$email]);
        } catch (\Exception $e) {}
        jsonError('Invalid or expired verification code. Please request a new one.', 401);
    }

    
    $pdo->prepare("UPDATE otp_codes SET used = 1 WHERE id = ?")->execute([$otpRecord['id']]);

    
    $userStmt = $pdo->prepare("SELECT id, full_name, email, role_title, target_role, avatar_initials, karma_xp, rank_percentile FROM users WHERE email = ?");
    $userStmt->execute([$email]);
    $user = $userStmt->fetch();

    if (!$user) {
        
        $parts = explode('@', $email);
        $nameFromEmail = ucwords(str_replace(['.', '_', '-'], ' ', $parts[0]));
        $words = explode(' ', $nameFromEmail);
        $initials = strtoupper(substr($words[0] ?? 'A', 0, 1) . substr($words[1] ?? 'E', 0, 1));

        $insertUser = $pdo->prepare("INSERT INTO users (full_name, email, role_title, target_role, avatar_initials, karma_xp, rank_percentile) VALUES (?, ?, 'ML Engineer', 'Lead AI Architect', ?, 150, 'Top 25%')");
        $insertUser->execute([$nameFromEmail, $email, $initials]);
        $newId = (int)$pdo->lastInsertId();

        
        $pdo->prepare("INSERT IGNORE INTO user_settings (user_id) VALUES (?)")->execute([$newId]);
        $pdo->prepare("INSERT IGNORE INTO progress_stats (user_id, total_hours_invested, active_streak_days, pass_rate_pct, target_readiness_pct, mastered_competencies_count, total_competencies_count, weekly_hours_json) VALUES (?, 4, 1, 85, 45, 3, 30, '[]')")->execute([$newId]);
        $pdo->prepare("INSERT INTO notifications (user_id, title, message, time_ago, type) VALUES (?, 'Welcome Engineer!', 'Your account has been verified and created via Email OTP.', 'Just now', 'success')")->execute([$newId]);
        $pdo->prepare("INSERT IGNORE INTO leaderboard_scores (user_id, xp_points, xp_weekly, xp_monthly, xp_quarterly, streak_days, readiness_pct, specialty, trend, change_label) VALUES (?, 150, 150, 150, 150, 1, 45, 'ML Engineering', 'new', '+150')")->execute([$newId]);

        $userStmt->execute([$email]);
        $user = $userStmt->fetch();
    }

    
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];

    
    try {
        $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'login', 'Verified via Email OTP', 'Authenticated session established', 10)")->execute([$user['id']]);
    } catch (\Exception $e) {}

    jsonResponse([
        'status'   => 'success',
        'message'  => 'Verification successful! Welcome back, ' . $user['full_name'],
        'redirect' => 'dashboard.html',
        'data'     => $user
    ]);
}

if ($action === 'login') {
    $email    = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';

    if (!$email || !$password) {
        jsonError('Email and password are required.');
    }

    $stmt = $pdo->prepare("SELECT id, full_name, email, role_title, target_role, avatar_initials, karma_xp, rank_percentile, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $valid = false;
    if ($user) {
        if (empty($user['password_hash'])) {
            
            $valid = true;
        } else {
            $valid = password_verify($password, $user['password_hash']);
        }
    }

    if (!$valid) {
        jsonError('Invalid email or password.', 401);
    }

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];

    
    try {
        $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'login', 'Signed in with password', 'Authenticated session started', 0)")->execute([$user['id']]);
    } catch (\Exception $e) {}

    unset($user['password_hash']);
    jsonResponse(['status' => 'success', 'message' => 'Login successful', 'data' => $user, 'redirect' => 'dashboard.html']);
}

if ($action === 'register') {
    $full_name  = trim($body['full_name'] ?? '');
    $email      = strtolower(trim($body['email'] ?? ''));
    $password   = $body['password'] ?? '';
    $role_title = trim($body['role_title'] ?? 'ML Engineer');

    if (!$full_name || !$email || !$password) {
        jsonError('Full name, email, and password are required.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('Invalid email address.');
    }
    if (strlen($password) < 8) {
        jsonError('Password must be at least 8 characters.');
    }

    
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
        jsonError('An account with this email already exists.', 409);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $full_name), 0, 2))));

    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, role_title, target_role, avatar_initials, karma_xp, rank_percentile, password_hash) VALUES (?, ?, ?, 'Lead AI Architect', ?, 100, 'Unranked', ?)");
    $stmt->execute([$full_name, $email, $role_title, $initials, $hash]);
    $newId = (int)$pdo->lastInsertId();

    $pdo->prepare("INSERT IGNORE INTO user_settings (user_id) VALUES (?)")->execute([$newId]);
    $pdo->prepare("INSERT IGNORE INTO progress_stats (user_id, total_hours_invested, active_streak_days, pass_rate_pct, target_readiness_pct, mastered_competencies_count, total_competencies_count, weekly_hours_json) VALUES (?, 0, 0, 0, 0, 0, 30, '[]')")->execute([$newId]);
    $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'register', 'Account Created', 'Welcome to Skill Intelligence Platform!', 100)")->execute([$newId]);
    $pdo->prepare("INSERT INTO notifications (user_id, title, message, time_ago, type) VALUES (?, 'Welcome!', 'Your Skill Intelligence account is ready. Start your first assessment!', 'Just now', 'success')")->execute([$newId]);
    $pdo->prepare("INSERT IGNORE INTO leaderboard_scores (user_id, xp_points, xp_weekly, xp_monthly, xp_quarterly, streak_days, readiness_pct, specialty, trend, change_label) VALUES (?, 100, 100, 100, 100, 1, 10, 'General AI', 'new', '+100')")->execute([$newId]);

    $_SESSION['user_id']    = $newId;
    $_SESSION['user_name']  = $full_name;
    $_SESSION['user_email'] = $email;

    jsonResponse([
        'status'   => 'success',
        'message'  => 'Account created successfully',
        'redirect' => 'dashboard.html',
        'data'     => ['id' => $newId, 'full_name' => $full_name, 'email' => $email, 'karma_xp' => 100]
    ], 201);
}

if ($action === 'logout') {
    $_SESSION = [];
    if (session_id()) {
        session_destroy();
    }
    jsonResponse(['status' => 'success', 'message' => 'Logged out successfully']);
}

jsonError('Unknown action: ' . $action);
