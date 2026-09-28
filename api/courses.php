<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT 
        c.*,
        e.id AS enrollment_id,
        e.progress_pct AS enrollment_progress,
        e.status AS enrollment_status,
        CASE WHEN e.id IS NOT NULL THEN 1 ELSE 0 END AS is_enrolled
        FROM `courses` c
        LEFT JOIN `enrollments` e ON c.id = e.course_id AND e.user_id = ?
        ORDER BY c.match_pct DESC");
    $stmt->execute([$userId]);
    $courses = $stmt->fetchAll();

    jsonResponse([
        'status' => 'success',
        'data' => $courses
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $action = $input['action'] ?? 'enroll';

    if ($action === 'enroll') {
        $courseId = (int)($input['course_id'] ?? 0);
        $courseTitle = trim($input['course_title'] ?? '');

        if (!$courseId && $courseTitle) {
            $cLookup = $pdo->prepare("SELECT `id` FROM `courses` WHERE `title` LIKE ? LIMIT 1");
            $cLookup->execute(["%$courseTitle%"]);
            $courseId = (int)$cLookup->fetchColumn();
        }

        if (!$courseId) {
            jsonError('Course not found');
        }

        $cStmt = $pdo->prepare("SELECT * FROM `courses` WHERE `id` = ?");
        $cStmt->execute([$courseId]);
        $course = $cStmt->fetch();

        if (!$course) {
            jsonError('Course not found', 404);
        }

        
        $chkStmt = $pdo->prepare("SELECT * FROM `enrollments` WHERE `user_id` = ? AND `course_id` = ?");
        $chkStmt->execute([$userId, $courseId]);
        $existing = $chkStmt->fetch();

        if ($existing) {
            jsonResponse([
                'status' => 'success',
                'message' => 'Already enrolled in this course',
                'data' => [
                    'course_id' => $courseId,
                    'course_title' => $course['title'],
                    'progress_pct' => (int)$existing['progress_pct'],
                    'status' => $existing['status']
                ]
            ]);
        }

        
        $insStmt = $pdo->prepare("INSERT INTO `enrollments` (`user_id`, `course_id`, `progress_pct`, `status`) VALUES (?, ?, 0, 'in_progress')");
        $insStmt->execute([$userId, $courseId]);
        $enrollmentId = $pdo->lastInsertId();

        
        $pdo->prepare("UPDATE `users` SET `karma_xp` = `karma_xp` + 50 WHERE `id` = ?")->execute([$userId]);

        
        $pdo->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `time_ago`) VALUES (?, 'Course Enrolled!', ?, 'course', 'Just now')")
            ->execute([$userId, "You enrolled in '{$course['title']}'. Added to active courses."]);

        
        $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`, `xp_gained`) VALUES (?, 'course', ?, ?, 50)")
            ->execute([$userId, "Enrolled in {$course['title']}", "{$course['labs_count']} hands-on labs • {$course['duration_hours']} hours", 50]);

        
        $pdo->prepare("UPDATE `recommendations` SET `status` = 'enrolled' WHERE `user_id` = ? AND `title` LIKE ?")
            ->execute([$userId, "%{$course['title']}%"]);

        jsonResponse([
            'status' => 'success',
            'message' => "Enrolled in {$course['title']}",
            'data' => [
                'enrollment_id' => $enrollmentId,
                'course_id' => $courseId,
                'course_title' => $course['title'],
                'progress_pct' => 0,
                'status' => 'in_progress'
            ]
        ]);
    } elseif ($action === 'update_progress') {
        $courseId = (int)($input['course_id'] ?? 0);
        $progressPct = min(100, max(0, (int)($input['progress_pct'] ?? 0)));

        $status = ($progressPct >= 100) ? 'completed' : 'in_progress';
        $pdo->prepare("UPDATE `enrollments` SET `progress_pct` = ?, `status` = ? WHERE `user_id` = ? AND `course_id` = ?")
            ->execute([$progressPct, $status, $userId, $courseId]);

        jsonResponse([
            'status' => 'success',
            'message' => 'Course progress updated',
            'data' => [
                'course_id' => $courseId,
                'progress_pct' => $progressPct,
                'status' => $status
            ]
        ]);
    } else {
        jsonError("Unknown action: $action");
    }
} else {
    jsonError('Method not allowed', 405);
}
