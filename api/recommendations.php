<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $recStmt = $pdo->prepare("SELECT * FROM `recommendations` WHERE `user_id` = ? AND `status` != 'dismissed' ORDER BY `match_pct` DESC");
    $recStmt->execute([$userId]);
    $recommendations = $recStmt->fetchAll();

    
    $stStmt = $pdo->prepare("SELECT `target_readiness_pct`, `mastered_competencies_count`, `total_competencies_count` FROM `progress_stats` WHERE `user_id` = ?");
    $stStmt->execute([$userId]);
    $stats = $stStmt->fetch() ?: ['target_readiness_pct' => 78, 'mastered_competencies_count' => 24, 'total_competencies_count' => 30];

    
    $assStmt = $pdo->query("SELECT `id`, `title`, `duration_mins`, `questions_count`, `difficulty` FROM `assessments` LIMIT 2");
    $recAssessments = $assStmt->fetchAll();

    jsonResponse([
        'status' => 'success',
        'data' => [
            'recommendations' => $recommendations,
            'gap_analysis' => [
                'target_role' => 'Lead AI Architect Benchmark',
                'readiness_pct' => (int)$stats['target_readiness_pct'],
                'mastered_competencies' => (int)$stats['mastered_competencies_count'],
                'total_competencies' => (int)$stats['total_competencies_count'],
                'recommendation_count' => count($recommendations)
            ],
            'recommended_assessments' => $recAssessments
        ]
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $action = $input['action'] ?? 'add_to_path';
    $recId = (int)($input['rec_id'] ?? 0);
    $recTitle = trim($input['rec_title'] ?? '');

    if (!$recId && $recTitle) {
        $rLookup = $pdo->prepare("SELECT `id` FROM `recommendations` WHERE `title` LIKE ? AND `user_id` = ? LIMIT 1");
        $rLookup->execute(["%$recTitle%", $userId]);
        $recId = (int)$rLookup->fetchColumn();
    }

    if (!$recId) {
        jsonError('Recommendation ID is required');
    }

    if ($action === 'dismiss') {
        $pdo->prepare("UPDATE `recommendations` SET `status` = 'dismissed' WHERE `id` = ? AND `user_id` = ?")
            ->execute([$recId, $userId]);

        jsonResponse([
            'status' => 'success',
            'message' => 'Recommendation dismissed'
        ]);
    } elseif ($action === 'add_to_path') {
        $rStmt = $pdo->prepare("SELECT * FROM `recommendations` WHERE `id` = ? AND `user_id` = ?");
        $rStmt->execute([$recId, $userId]);
        $rec = $rStmt->fetch();

        if ($rec) {
            $pdo->prepare("UPDATE `recommendations` SET `status` = 'enrolled' WHERE `id` = ?")->execute([$recId]);

            
            $cLookup = $pdo->prepare("SELECT `id` FROM `courses` WHERE `title` LIKE ? LIMIT 1");
            $cLookup->execute(["%{$rec['title']}%"]);
            $courseId = $cLookup->fetchColumn();

            if ($courseId) {
                $pdo->prepare("INSERT IGNORE INTO `enrollments` (`user_id`, `course_id`, `progress_pct`, `status`) VALUES (?, ?, 0, 'in_progress')")
                    ->execute([$userId, $courseId]);
            }

            
            $pdo->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `time_ago`) VALUES (?, 'Added to Learning Path', ?, 'info', 'Just now')")
                ->execute([$userId, "{$rec['title']} was added to your roadmap."]);

            
            $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`, `xp_gained`) VALUES (?, 'course', ?, 'Added from AI recommendations', 25)")
                ->execute([$userId, "Added to Path: {$rec['title']}"]);
        }

        jsonResponse([
            'status' => 'success',
            'message' => 'Added to Learning Path successfully'
        ]);
    } else {
        jsonError("Unknown action: $action");
    }
} else {
    jsonError('Method not allowed', 405);
}
