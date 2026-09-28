<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    
    $stmt = $pdo->query("SELECT `id`, `title`, `category`, `description`, `duration_mins`, `questions_count`, `difficulty`, `pass_score_pct`, `questions_json` FROM `assessments` ORDER BY `id` ASC");
    $assessments = $stmt->fetchAll();

    foreach ($assessments as &$a) {
        $questions = json_decode($a['questions_json'], true) ?: [];
        $a['questions_count'] = count($questions);
        $a['questions'] = $questions; 
    }

    
    $histStmt = $pdo->prepare("SELECT `id`, `assessment_id`, `assessment_title`, `score_pct`, `status`, `benchmark_percentile`, `completed_at` FROM `assessment_attempts` WHERE `user_id` = ? ORDER BY `id` DESC");
    $histStmt->execute([$userId]);
    $history = $histStmt->fetchAll();

    foreach ($history as &$h) {
        $diff = time() - strtotime($h['completed_at']);
        if ($diff < 60) $h['time_ago'] = 'Just now';
        elseif ($diff < 3600) $h['time_ago'] = floor($diff / 60) . ' mins ago';
        elseif ($diff < 86400) $h['time_ago'] = floor($diff / 3600) . ' hours ago';
        else $h['time_ago'] = date('M d, Y', strtotime($h['completed_at']));
    }

    jsonResponse([
        'status' => 'success',
        'data' => [
            'assessments' => $assessments,
            'history' => $history
        ]
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $assessmentId = (int)($input['assessment_id'] ?? 0);
    $submittedAnswers = $input['answers'] ?? [];

    if (!$assessmentId) {
        jsonError('Assessment ID is required');
    }

    $stmt = $pdo->prepare("SELECT * FROM `assessments` WHERE `id` = ?");
    $stmt->execute([$assessmentId]);
    $assessment = $stmt->fetch();

    if (!$assessment) {
        jsonError('Assessment not found', 404);
    }

    $questions = json_decode($assessment['questions_json'], true) ?: [];
    $totalQ = count($questions);

    if ($totalQ === 0) {
        jsonError('No questions configured for this assessment', 500);
    }

    
    $correctCount = 0;
    $detailedReview = [];

    foreach ($questions as $idx => $q) {
        $userChoice = isset($submittedAnswers[$idx]) ? (int)$submittedAnswers[$idx] : null;
        $isCorrect = ($userChoice !== null && $userChoice === (int)$q['correct']);
        if ($isCorrect) $correctCount++;

        $detailedReview[] = [
            'question' => $q['q'],
            'user_choice' => $userChoice,
            'correct_choice' => (int)$q['correct'],
            'is_correct' => $isCorrect,
            'explanation' => $q['explanation'] ?? ''
        ];
    }

    $scorePct = (int)round(($correctCount / $totalQ) * 100);
    $status = ($scorePct >= (int)$assessment['pass_score_pct']) ? 'Passed' : 'Failed';
    $percentile = 'Top ' . max(1, 100 - $scorePct + 2) . '%';
    $xpEarned = ($status === 'Passed') ? 150 : 50;

    
    $attStmt = $pdo->prepare("INSERT INTO `assessment_attempts` (`user_id`, `assessment_id`, `assessment_title`, `score_pct`, `status`, `benchmark_percentile`) VALUES (?, ?, ?, ?, ?, ?)");
    $attStmt->execute([$userId, $assessmentId, $assessment['title'], $scorePct, $status, $percentile]);
    $attemptId = $pdo->lastInsertId();

    
    $pdo->prepare("UPDATE `users` SET `karma_xp` = `karma_xp` + ? WHERE `id` = ?")->execute([$xpEarned, $userId]);
    $pdo->prepare("UPDATE `leaderboard_scores` SET `xp_points` = `xp_points` + ?, `xp_weekly` = `xp_weekly` + ? WHERE `user_id` = ?")->execute([$xpEarned, $xpEarned, $userId]);

    
    $skillUpdateStmt = $pdo->prepare("SELECT * FROM `skills` WHERE `user_id` = ? AND `category` = ? LIMIT 1");
    $skillUpdateStmt->execute([$userId, $assessment['category']]);
    $matchedSkill = $skillUpdateStmt->fetch();

    if ($matchedSkill) {
        
        $newProficiency = min(99, max((int)$matchedSkill['proficiency_pct'], (int)round(($matchedSkill['proficiency_pct'] + $scorePct) / 2)));
        $newLevel = ($newProficiency >= 90) ? 'Mastery' : (($newProficiency >= 80) ? 'Expert' : (($newProficiency >= 70) ? 'Proficient' : 'Intermediate'));
        $pdo->prepare("UPDATE `skills` SET `proficiency_pct` = ?, `level_label` = ? WHERE `id` = ?")
            ->execute([$newProficiency, $newLevel, $matchedSkill['id']]);
    }

    
    $statRecompute = $pdo->prepare("SELECT 
        COUNT(*) as total_skills,
        SUM(CASE WHEN proficiency_pct >= 80 THEN 1 ELSE 0 END) as mastered_skills,
        ROUND(AVG(proficiency_pct)) as avg_prof
        FROM `skills` WHERE `user_id` = ?");
    $statRecompute->execute([$userId]);
    $statsRow = $statRecompute->fetch();

    $newReadiness = min(99, max(78, (int)$statsRow['avg_prof']));
    $masteredCount = (int)$statsRow['mastered_skills'];

    $pdo->prepare("UPDATE `progress_stats` SET `target_readiness_pct` = ?, `mastered_competencies_count` = ? WHERE `user_id` = ?")
        ->execute([$newReadiness, $masteredCount, $userId]);

    
    $notifTitle = ($status === 'Passed') ? "🎉 Assessment Passed!" : "Assessment Submitted";
    $notifMsg = "You scored {$scorePct}% on {$assessment['title']}. (+{$xpEarned} Karma XP)";
    $pdo->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `type`, `time_ago`) VALUES (?, ?, ?, 'success', 'Just now')")
        ->execute([$userId, $notifTitle, $notifMsg]);

    
    $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`, `xp_gained`) VALUES (?, 'assessment', ?, ?, ?)")
        ->execute([$userId, "Completed Assessment: {$assessment['title']}", "Scored {$scorePct}% ({$status}) • Ranked in {$percentile}", $xpEarned]);

    jsonResponse([
        'status' => 'success',
        'message' => "Assessment submitted successfully",
        'data' => [
            'attempt_id' => $attemptId,
            'assessment_title' => $assessment['title'],
            'score_pct' => $scorePct,
            'status' => $status,
            'correct_count' => $correctCount,
            'total_questions' => $totalQ,
            'benchmark_percentile' => $percentile,
            'xp_earned' => $xpEarned,
            'new_readiness_pct' => $newReadiness,
            'mastered_competencies_count' => $masteredCount,
            'detailed_review' => $detailedReview
        ]
    ]);
} else {
    jsonError('Method not allowed', 405);
}
