<?php

require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$userId = (int)($_SESSION['user_id'] ?? ($_SERVER['HTTP_X_USER_ID'] ?? 0));
if ($userId <= 0) {
    jsonError('Unauthenticated. Please sign in.', 401);
}

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonError('User not found', 404);
    }

    $setStmt = $pdo->prepare("SELECT * FROM `user_settings` WHERE `user_id` = ?");
    $setStmt->execute([$userId]);
    $settings = $setStmt->fetch() ?: [];

    $statStmt = $pdo->prepare("SELECT * FROM `progress_stats` WHERE `user_id` = ?");
    $statStmt->execute([$userId]);
    $stats = $statStmt->fetch() ?: [];

    
    $certCount = $pdo->prepare("SELECT COUNT(*) FROM `assessment_attempts` WHERE `user_id` = ? AND `status` = 'Passed'");
    $certCount->execute([$userId]);
    $certs = $certCount->fetchColumn();

    $skillCount = $pdo->prepare("SELECT COUNT(*) FROM `skills` WHERE `user_id` = ? AND `proficiency_pct` >= 80");
    $skillCount->execute([$userId]);
    $mastered = $skillCount->fetchColumn();

    jsonResponse([
        'status' => 'success',
        'data' => [
            'user' => $user,
            'settings' => $settings,
            'stats' => $stats,
            'certifications_count' => (int)$certs,
            'mastered_skills_count' => (int)$mastered
        ]
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $action = $input['action'] ?? 'update_profile';

    if ($action === 'update_profile') {
        $fullName = trim($input['full_name'] ?? '');
        $roleTitle = trim($input['role_title'] ?? '');
        $targetRole = trim($input['target_role'] ?? '');
        $email = trim($input['email'] ?? '');
        $bio = trim($input['bio'] ?? '');

        if (!$fullName) {
            jsonError('Full name is required');
        }

        
        $words = preg_split("/\s+/", $fullName);
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) $initials .= strtoupper($w[0]);
        }
        $initials = substr($initials, 0, 2);

        $stmt = $pdo->prepare("UPDATE `users` SET 
            `full_name` = ?, 
            `role_title` = COALESCE(NULLIF(?, ''), `role_title`), 
            `target_role` = COALESCE(NULLIF(?, ''), `target_role`), 
            `email` = COALESCE(NULLIF(?, ''), `email`), 
            `bio` = COALESCE(NULLIF(?, ''), `bio`),
            `avatar_initials` = ?
            WHERE `id` = ?");
        $stmt->execute([$fullName, $roleTitle, $targetRole, $email, $bio, $initials, $userId]);

        
        $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`) VALUES (?, 'profile', 'Updated Professional Profile', ?)")
            ->execute([$userId, "Profile details updated: $fullName ($roleTitle)"]);

        
        $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ?");
        $stmt->execute([$userId]);
        $updatedUser = $stmt->fetch();

        jsonResponse([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $updatedUser
        ]);
    } elseif ($action === 'update_settings') {
        $themeMode = $input['theme_mode'] ?? null;
        $weeklyHours = isset($input['weekly_hours_target']) ? (int)$input['weekly_hours_target'] : null;
        $streakProtection = isset($input['daily_streak_protection']) ? (int)$input['daily_streak_protection'] : null;
        $recAlerts = isset($input['rec_alerts']) ? (int)$input['rec_alerts'] : null;
        $assessmentReports = isset($input['assessment_reports']) ? (int)$input['assessment_reports'] : null;

        $updates = [];
        $params = [];

        if ($themeMode !== null) { $updates[] = "`theme_mode` = ?"; $params[] = $themeMode; }
        if ($weeklyHours !== null) { $updates[] = "`weekly_hours_target` = ?"; $params[] = $weeklyHours; }
        if ($streakProtection !== null) { $updates[] = "`daily_streak_protection` = ?"; $params[] = $streakProtection; }
        if ($recAlerts !== null) { $updates[] = "`rec_alerts` = ?"; $params[] = $recAlerts; }
        if ($assessmentReports !== null) { $updates[] = "`assessment_reports` = ?"; $params[] = $assessmentReports; }

        if (!empty($updates)) {
            $params[] = $userId;
            $sql = "UPDATE `user_settings` SET " . implode(', ', $updates) . " WHERE `user_id` = ?";
            $pdo->prepare($sql)->execute($params);
        }

        $setStmt = $pdo->prepare("SELECT * FROM `user_settings` WHERE `user_id` = ?");
        $setStmt->execute([$userId]);

        jsonResponse([
            'status' => 'success',
            'message' => 'Settings saved successfully',
            'data' => $setStmt->fetch()
        ]);
    } else {
        jsonError("Unknown action: $action");
    }
} else {
    jsonError('Method not allowed', 405);
}
